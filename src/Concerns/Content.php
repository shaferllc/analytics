<?php

namespace Shaferllc\Analytics\Concerns;

use DOMDocument;

class Content
{
    private const MIN_WORD_COUNT = 300;
    private const MAX_WORD_COUNT = 2500;
    private const OPTIMAL_KEYWORD_DENSITY = 2.0;
    private const MAX_HEADING_LENGTH = 60;
    private const IDEAL_PARAGRAPH_LENGTH = 150;
    private const READABILITY_THRESHOLD = 60;
    
    public function analyzeContentQuality(DOMDocument $domDocument): array
    {
        $issues = [];
        $score = 100;
        $recommendations = [];
        $body = $domDocument->getElementsByTagName('body')->item(0);
        
        if (!$body) {
            return [
                'word_count' => 0,
                'score' => 0,
                'issues' => ['No body tag found'],
                'recommendations' => ['Add a body tag to the document']
            ];
        }

        $text = $body->textContent;
        $wordCount = str_word_count(strip_tags($text));

        // Word count scoring
        if ($wordCount < self::MIN_WORD_COUNT) {
            $score -= 20;
            $issues[] = 'Content length is less than recommended minimum of 300 words';
            $recommendations[] = 'Add more content to reach at least 300 words';
        } elseif ($wordCount > self::MAX_WORD_COUNT) {
            $score -= 10;
            $recommendations[] = 'Consider breaking content into multiple pages';
        }

        // Heading structure scoring
        $h1Tags = $domDocument->getElementsByTagName('h1');
        if ($h1Tags->length === 0) {
            $score -= 15;
            $issues[] = 'No H1 heading found';
            $recommendations[] = 'Add a primary H1 heading';
        } elseif ($h1Tags->length > 1) {
            $score -= 10;
            $issues[] = 'Multiple H1 headings found';
            $recommendations[] = 'Use only one H1 heading per page';
        }

        // Content structure analysis
        $paragraphs = $domDocument->getElementsByTagName('p');
        if ($paragraphs->length === 0) {
            $score -= 10;
            $issues[] = 'No paragraph tags found';
            $recommendations[] = 'Structure content using proper paragraph tags';
        }

        // Image analysis
        $images = $domDocument->getElementsByTagName('img');
        if ($images->length === 0 && $wordCount > self::MIN_WORD_COUNT) {
            $score -= 5;
            $recommendations[] = 'Add relevant images to enhance content';
        }

        return [
            'word_count' => $wordCount,
            'score' => max(0, $score),
            'issues' => $issues,
            'recommendations' => $recommendations,
            'details' => [
                'heading_count' => [
                    'h1' => $h1Tags->length,
                    'paragraphs' => $paragraphs->length,
                    'images' => $images->length
                ]
            ]
        ];
    }

    public function analyzeTextContent(DOMDocument $domDocument): array
    {
        $bodyText = $this->extractBodyKeywords($domDocument);
        $wordCount = count($bodyText);
        $score = 100;
        
        // Score based on content length
        if ($wordCount < self::MIN_WORD_COUNT) {
            $score -= 20;
        } elseif ($wordCount > self::MAX_WORD_COUNT) {
            $score -= 10;
        }

        $recommendations = $this->getContentRecommendations($wordCount);
        
        return [
            'word_count' => $wordCount,
            'score' => max(0, $score),
            'has_sufficient_content' => $wordCount >= self::MIN_WORD_COUNT,
            'recommendations' => $recommendations
        ];
    }

    public function analyzeReadability(DOMDocument $domDocument): array
    {
        $textBlocks = [];
        $readabilityScores = [];
        $totalScore = 100;
        
        // Analyze text content in paragraphs
        $paragraphs = $domDocument->getElementsByTagName('p');
        $totalSentences = 0;
        $totalWords = 0;
        $totalSyllables = 0;
        
        foreach ($paragraphs as $p) {
            $text = trim($p->textContent);
            if (strlen($text) > 0) {
                $wordCount = str_word_count($text);
                $sentenceCount = preg_match_all('/[.!?]+/', $text, $matches);
                
                // Penalize very long paragraphs
                if ($wordCount > self::IDEAL_PARAGRAPH_LENGTH) {
                    $totalScore -= 5;
                }
                
                $textBlocks[] = [
                    'text' => $text,
                    'word_count' => $wordCount,
                    'sentence_count' => $sentenceCount,
                    'words_per_sentence' => $sentenceCount > 0 ? round($wordCount / $sentenceCount, 1) : 0,
                    'path' => $p->getNodePath()
                ];
                
                $totalSentences += $sentenceCount;
                $totalWords += $wordCount;
                
                // Estimate syllables based on vowel groups
                $syllables = preg_match_all('/[aeiou]+/i', $text);
                $totalSyllables += $syllables;
            }
        }

        // Calculate average words per sentence
        $avgWordsPerSentence = $totalSentences > 0 ? round($totalWords / $totalSentences, 1) : 0;
        if ($avgWordsPerSentence > 25) {
            $totalScore -= 15;
        }

        // Add Flesch Reading Ease Score calculation
        $fleschScore = $this->calculateFleschScore($totalSyllables, $totalWords, $totalSentences);
        if ($fleschScore < self::READABILITY_THRESHOLD) {
            $totalScore -= 10;
            $recommendations[] = 'Simplify language to improve readability score';
        }

        $recommendations = [
            'Keep paragraphs concise and focused',
            'Use clear, simple language',
            'Break up long text blocks',
            'Include subheadings for better scanning'
        ];

        if ($avgWordsPerSentence > 25) {
            $recommendations[] = 'Shorten sentences for better readability';
        }

        return [
            'score' => max(0, $totalScore),
            'text_blocks' => $textBlocks,
            'metrics' => [
                'average_words_per_sentence' => $avgWordsPerSentence,
                'total_paragraphs' => count($textBlocks),
                'total_sentences' => $totalSentences,
                'total_words' => $totalWords,
                'flesch_score' => round($fleschScore, 1)
            ],
            'recommendations' => $recommendations
        ];
    }

    public function analyzeKeywordDensity(DOMDocument $domDocument): array
    {
        $text = $domDocument->textContent;
        $words = str_word_count(strtolower($text), 1);
        $totalWords = count($words);
        $score = 100;
        
        $wordFrequency = array_count_values($words);
        arsort($wordFrequency);
        
        // Calculate keyword density
        $density = [];
        $keywordIssues = [];
        
        foreach ($wordFrequency as $word => $count) {
            if (strlen($word) > 3) {
                $keywordDensity = round(($count / $totalWords) * 100, 2);
                
                // Penalize keyword stuffing
                if ($keywordDensity > self::OPTIMAL_KEYWORD_DENSITY + 2) {
                    $score -= 5;
                    $keywordIssues[] = "Keyword '$word' appears too frequently";
                }
                
                $density[$word] = [
                    'count' => $count,
                    'density' => $keywordDensity,
                    'locations' => $this->findKeywordLocations($domDocument, $word)
                ];
            }
        }

        return [
            'score' => max(0, $score),
            'total_words' => $totalWords,
            'unique_words' => count($wordFrequency),
            'keyword_density' => array_slice($density, 0, 20),
            'issues' => $keywordIssues,
            'recommendations' => [
                'Maintain keyword density between 1-3%',
                'Use variations of key terms naturally',
                'Avoid keyword stuffing',
                'Focus on topic relevance over keyword repetition'
            ]
        ];
    }

    private function extractBodyKeywords(DOMDocument $domDocument): array
    {
        $body = $domDocument->getElementsByTagName('body')->item(0);
        return $body ? $this->tokenizeText($this->cleanText($body->textContent)) : [];
    }

    private function getContentRecommendations(int $wordCount): array
    {
        $recommendations = [];
        
        if ($wordCount < self::MIN_WORD_COUNT) {
            $recommendations[] = 'Add more content to improve SEO (minimum 300 words recommended)';
        }
        
        if ($wordCount > self::MAX_WORD_COUNT) {
            $recommendations[] = 'Consider breaking long content into multiple pages';
        }
        
        return $recommendations;
    }

    protected function findKeywordLocations(DOMDocument $domDocument, string $keyword): array
    {
        $locations = [];
        $elements = ['title', 'h1', 'h2', 'h3', 'p', 'a'];
        
        foreach ($elements as $tag) {
            $nodes = $domDocument->getElementsByTagName($tag);
            foreach ($nodes as $node) {
                if (stripos($node->textContent, $keyword) !== false) {
                    $locations[] = [
                        'element' => $tag,
                        'path' => $node->getNodePath(),
                        'context' => trim(substr($node->textContent, 0, 100))
                    ];
                }
            }
        }
        
        return $locations;
    }

    private function tokenizeText(string $text): array
    {
        $words = array_filter(explode(' ', $this->cleanText($text)));
        return array_values(array_unique($words));
    }

    private function cleanText(string $text): string
    {
        return preg_replace('/[^\p{L}\p{N}\s]/u', ' ', mb_strtolower(trim($text)));
    }

    private function calculateFleschScore(int $syllables, int $words, int $sentences): float 
    {
        if ($words === 0 || $sentences === 0) {
            return 0;
        }
        return 206.835 - (1.015 * ($words / $sentences)) - (84.6 * ($syllables / $words));
    }

    public function analyzeHeadingStructure(DOMDocument $domDocument): array
    {
        $headings = [];
        $issues = [];
        $score = 100;

        // Analyze all heading levels
        for ($i = 1; $i <= 6; $i++) {
            $tags = $domDocument->getElementsByTagName("h$i");
            $headings["h$i"] = [];
            
            foreach ($tags as $tag) {
                $text = trim($tag->textContent);
                $length = strlen($text);
                
                if ($length > self::MAX_HEADING_LENGTH) {
                    $score -= 5;
                    $issues[] = "H$i heading exceeds recommended length: '$text'";
                }
                
                $headings["h$i"][] = [
                    'text' => $text,
                    'length' => $length,
                    'path' => $tag->getNodePath()
                ];
            }
        }

        // Check heading hierarchy
        if (!empty($headings['h2']) && empty($headings['h1'])) {
            $score -= 10;
            $issues[] = 'H2 headings present without H1 heading';
        }

        return [
            'score' => max(0, $score),
            'headings' => $headings,
            'issues' => $issues,
            'recommendations' => $this->getHeadingRecommendations($headings, $issues)
        ];
    }

    private function getHeadingRecommendations(array $headings, array $issues): array
    {
        $recommendations = [];
        
        if (empty($headings['h1'])) {
            $recommendations[] = 'Add a primary H1 heading';
        }
        
        if (count($headings['h1']) > 1) {
            $recommendations[] = 'Use only one H1 heading per page';
        }
        
        if (!empty($issues)) {
            $recommendations[] = 'Review and adjust heading lengths to be more concise';
            $recommendations[] = 'Ensure proper heading hierarchy (H1 → H2 → H3)';
        }
        
        return $recommendations;
    }

    public function analyzeDuplicateContent(DOMDocument $domDocument): array
    {
        $paragraphs = $domDocument->getElementsByTagName('p');
        $duplicates = [];
        $score = 100;
        
        $seen = [];
        foreach ($paragraphs as $p) {
            $text = trim($p->textContent);
            if (strlen($text) > 50) {  // Only check substantial paragraphs
                $hash = md5($text);
                if (isset($seen[$hash])) {
                    $duplicates[] = [
                        'text' => $text,
                        'path' => $p->getNodePath()
                    ];
                    $score -= 5;
                }
                $seen[$hash] = true;
            }
        }
        
        return [
            'score' => max(0, $score),
            'duplicates' => $duplicates,
            'recommendations' => $duplicates ? ['Remove or rephrase duplicate content'] : []
        ];
    }

    public function analyzeContentToHtmlRatio(DOMDocument $domDocument): array
    {
        $htmlSize = strlen($domDocument->saveHTML());
        $textContent = trim($domDocument->textContent);
        $textSize = strlen($textContent);
        
        $ratio = ($textSize / $htmlSize) * 100;
        $score = 100;
        
        if ($ratio < 10) {
            $score -= 20;
        }
        
        return [
            'score' => $score,
            'ratio' => round($ratio, 2),
            'text_size' => $textSize,
            'html_size' => $htmlSize
        ];
    }
}