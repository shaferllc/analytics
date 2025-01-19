<?php

namespace Shaferllc\Analytics\Concerns;

use DOMElement;
use DOMDocument;

class Semantic
{
    /**
     * Analyzes the semantic structure of the HTML document
     *
     * @param DOMDocument $document The HTML document to analyze
     * @return array Analysis results containing semantic structure information
     */
    public function analyzeSemanticStructure(DOMDocument $document): array
    {
        $analysis = [
            'headings' => [],
            'landmarks' => [],
            'sections' => [],
            'contentStructure' => []
        ];

        // Analyze heading hierarchy
        $headings = $document->getElementsByTagName('h1');
        foreach ($headings as $heading) {
            $analysis['headings'][] = [
                'level' => 1,
                'text' => $heading->textContent,
                'position' => $heading->getLineNo()
            ];
        }

        // Analyze landmark regions
        $landmarks = $document->getElementsByTagName('main');
        foreach ($landmarks as $landmark) {
            $analysis['landmarks'][] = [
                'role' => $landmark->getAttribute('role') ?: 'main',
                'label' => $landmark->getAttribute('aria-label'),
                'position' => $landmark->getLineNo()
            ];
        }

        // Analyze document sections
        $sections = $document->getElementsByTagName('section');
        foreach ($sections as $section) {
            $analysis['sections'][] = [
                'id' => $section->getAttribute('id'),
                'role' => $section->getAttribute('role'),
                'position' => $section->getLineNo()
            ];
        }

        // Analyze content structure
        $article = $document->getElementsByTagName('article');
        foreach ($article as $content) {
            $analysis['contentStructure'][] = [
                'type' => 'article',
                'id' => $content->getAttribute('id'),
                'position' => $content->getLineNo()
            ];
        }

        return $analysis;
    }


    public function analyzeHTML5Elements(DOMDocument $domDocument): array
    {
        $html5Elements = [
            'header', 'footer', 'nav', 'main', 'article', 'section', 'aside',
            'figure', 'figcaption', 'time', 'mark', 'details', 'summary'
        ];

        $results = [];
        foreach ($html5Elements as $element) {
            $elements = $domDocument->getElementsByTagName($element);
            if ($elements->length > 0) {
                $results[$element] = [
                    'count' => $elements->length,
                    'usage' => []
                ];
                foreach ($elements as $node) {
                    $results[$element]['usage'][] = [
                        'id' => $node->getAttribute('id'),
                        'class' => $node->getAttribute('class'),
                        'role' => $node->getAttribute('role')
                    ];
                }
            }
        }

        return $results;
    }


    public function analyzeLandmarkRoles(DOMDocument $domDocument): array
    {
        $landmarkRoles = [
            'banner', 'complementary', 'contentinfo', 'form', 'main', 
            'navigation', 'region', 'search'
        ];

        $results = [];
        $elements = $domDocument->getElementsByTagName('*');

        foreach ($elements as $element) {
            if ($element->hasAttribute('role')) {
                $role = $element->getAttribute('role');
                if (in_array($role, $landmarkRoles)) {
                    if (!isset($results[$role])) {
                        $results[$role] = [
                            'count' => 0,
                            'elements' => []
                        ];
                    }
                    
                    $results[$role]['count']++;
                    $results[$role]['elements'][] = [
                        'tag' => $element->tagName,
                        'id' => $element->getAttribute('id'),
                        'class' => $element->getAttribute('class'),
                        'aria_label' => $element->getAttribute('aria-label'),
                        'aria_labelledby' => $element->getAttribute('aria-labelledby')
                    ];
                }
            }
        }

        return [
            'landmark_roles_found' => count($results),
            'roles' => $results
        ];
    }


    public function analyzeContentStructure(DOMDocument $domDocument): array
    {
        $structure = [];

        // Analyze heading hierarchy
        $headings = [];
        for ($i = 1; $i <= 6; $i++) {
            $elements = $domDocument->getElementsByTagName('h' . $i);
            if ($elements->length > 0) {
                $headings['h' . $i] = [
                    'count' => $elements->length,
                    'elements' => []
                ];
                foreach ($elements as $element) {
                    $headings['h' . $i]['elements'][] = [
                        'text' => trim($element->nodeValue),
                        'id' => $element->getAttribute('id'),
                        'class' => $element->getAttribute('class')
                    ];
                }
            }
        }
        $structure['headings'] = $headings;

        // Analyze list elements
        $lists = [
            'ul' => ['count' => 0, 'items' => 0],
            'ol' => ['count' => 0, 'items' => 0]
        ];
        foreach (['ul', 'ol'] as $listType) {
            $elements = $domDocument->getElementsByTagName($listType);
            $lists[$listType]['count'] = $elements->length;
            foreach ($elements as $list) {
                $items = $list->getElementsByTagName('li');
                $lists[$listType]['items'] += $items->length;
            }
        }
        $structure['lists'] = $lists;

        // Analyze paragraphs
        $paragraphs = $domDocument->getElementsByTagName('p');
        $structure['paragraphs'] = [
            'count' => $paragraphs->length,
            'with_classes' => 0,
            'empty' => 0
        ];
        foreach ($paragraphs as $p) {
            if ($p->hasAttribute('class')) {
                $structure['paragraphs']['with_classes']++;
            }
            if (trim($p->nodeValue) === '') {
                $structure['paragraphs']['empty']++;
            }
        }

        // Analyze table structure
        $tables = $domDocument->getElementsByTagName('table');
        $structure['tables'] = [
            'count' => $tables->length,
            'with_headers' => 0,
            'with_caption' => 0
        ];
        foreach ($tables as $table) {
            $headers = $table->getElementsByTagName('th');
            if ($headers->length > 0) {
                $structure['tables']['with_headers']++;
            }
            $captions = $table->getElementsByTagName('caption');
            if ($captions->length > 0) {
                $structure['tables']['with_caption']++;
            }
        }

        return $structure;
    }



     /**
     * Analyzes list elements in the document for proper structure and usage
     *
     * @param DOMDocument $domDocument The DOM document to analyze
     * @return array Analysis of list elements including structure and potential issues
     */
    public function analyzeListElements(DOMDocument $domDocument): array
    {
        $lists = [];
        $issues = [];

        // Get all list elements
        $orderedLists = $domDocument->getElementsByTagName('ol');
        $unorderedLists = $domDocument->getElementsByTagName('ul');
        $definitionLists = $domDocument->getElementsByTagName('dl');

        // Analyze ordered lists
        foreach ($orderedLists as $list) {
            $listItems = $list->getElementsByTagName('li');
            $lists[] = [
                'type' => 'ordered',
                'path' => $list->getNodePath(),
                'items' => $listItems->length,
                'nested' => $this->hasNestedLists($list)
            ];

            // Check for empty lists
            if ($listItems->length === 0) {
                $issues[] = [
                    'type' => 'empty_list',
                    'description' => 'Ordered list has no list items',
                    'path' => $list->getNodePath(),
                    'impact' => 'medium'
                ];
            }
        }

        // Analyze unordered lists
        foreach ($unorderedLists as $list) {
            $listItems = $list->getElementsByTagName('li');
            $lists[] = [
                'type' => 'unordered',
                'path' => $list->getNodePath(),
                'items' => $listItems->length,
                'nested' => $this->hasNestedLists($list)
            ];

            // Check for empty lists
            if ($listItems->length === 0) {
                $issues[] = [
                    'type' => 'empty_list',
                    'description' => 'Unordered list has no list items',
                    'path' => $list->getNodePath(),
                    'impact' => 'medium'
                ];
            }
        }

        // Analyze definition lists
        foreach ($definitionLists as $list) {
            $terms = $list->getElementsByTagName('dt');
            $descriptions = $list->getElementsByTagName('dd');
            $lists[] = [
                'type' => 'definition',
                'path' => $list->getNodePath(),
                'terms' => $terms->length,
                'descriptions' => $descriptions->length
            ];

            // Check for missing terms or descriptions
            if ($terms->length === 0 || $descriptions->length === 0) {
                $issues[] = [
                    'type' => 'incomplete_definition_list',
                    'description' => 'Definition list missing terms or descriptions',
                    'path' => $list->getNodePath(),
                    'impact' => 'medium'
                ];
            }
        }

        return [
            'lists' => $lists,
            'total_lists' => count($lists),
            'by_type' => [
                'ordered' => $orderedLists->length,
                'unordered' => $unorderedLists->length,
                'definition' => $definitionLists->length
            ],
            'issues' => $issues,
            'recommendations' => [
                'Use appropriate list type for the content (ordered vs unordered)',
                'Ensure lists have at least one list item',
                'Use definition lists for term-description pairs',
                'Maintain consistent list structure throughout the document'
            ]
        ];
    }



     /**
     * Analyzes paragraphs in the document for structure and readability
     *
     * @param DOMDocument $domDocument The DOM document to analyze
     * @return array Analysis of paragraphs including length, structure and potential issues
     */
    public function analyzeParagraphs(DOMDocument $domDocument): array
    {
        $paragraphs = [];
        $issues = [];
        $pElements = $domDocument->getElementsByTagName('p');
        
        $totalLength = 0;
        $longParagraphs = 0;
        $shortParagraphs = 0;
        
        foreach ($pElements as $p) {
            $text = trim($p->textContent);
            $wordCount = str_word_count($text);
            $charCount = strlen($text);
            
            $paragraphInfo = [
                'text' => substr($text, 0, 100) . (strlen($text) > 100 ? '...' : ''),
                'word_count' => $wordCount,
                'char_count' => $charCount,
                'path' => $p->getNodePath()
            ];
            
            $paragraphs[] = $paragraphInfo;
            $totalLength += $wordCount;
            
            // Check for overly long paragraphs (more than 150 words)
            if ($wordCount > 150) {
                $longParagraphs++;
                $issues[] = [
                    'type' => 'long_paragraph',
                    'description' => 'Paragraph is too long for comfortable reading',
                    'word_count' => $wordCount,
                    'path' => $p->getNodePath(),
                    'impact' => 'medium'
                ];
            }
            
            // Check for very short paragraphs (less than 20 words)
            if ($wordCount < 20 && $wordCount > 0) {
                $shortParagraphs++;
            }
            
            // Check for empty paragraphs
            if ($wordCount === 0) {
                $issues[] = [
                    'type' => 'empty_paragraph',
                    'description' => 'Empty paragraph found',
                    'path' => $p->getNodePath(),
                    'impact' => 'low'
                ];
            }
        }
        
        $avgLength = $pElements->length > 0 ? $totalLength / $pElements->length : 0;
        
        return [
            'total_paragraphs' => $pElements->length,
            'average_length' => round($avgLength, 2),
            'long_paragraphs' => $longParagraphs,
            'short_paragraphs' => $shortParagraphs,
            'paragraphs' => $paragraphs,
            'issues' => $issues,
            'recommendations' => [
                'Keep paragraphs under 150 words for better readability',
                'Vary paragraph length to maintain reader interest',
                'Remove empty paragraphs',
                'Use appropriate spacing between paragraphs',
                'Break up long paragraphs into smaller chunks'
            ]
        ];
    }
       /**
     * Checks if a list element contains nested lists
     *
     * @param DOMElement $list The list element to check
     * @return bool Whether the list contains nested lists
     */
    private function hasNestedLists(DOMElement $list): bool
    {
        return $list->getElementsByTagName('ul')->length > 0 || 
               $list->getElementsByTagName('ol')->length > 0;
    }



 /**
     * Analyzes table structure and accessibility features
     *
     * @param DOMDocument $domDocument The DOM document to analyze
     * @return array Analysis of table structure including headers, captions and potential issues
     */
    public function analyzeTableStructure(DOMDocument $domDocument): array
    {
        $tables = $domDocument->getElementsByTagName('table');
        $tableAnalysis = [];
        $issues = [];

        foreach ($tables as $table) {
            $tableInfo = [
                'path' => $table->getNodePath(),
                'has_caption' => false,
                'has_headers' => false,
                'has_scope' => false,
                'has_thead' => false,
                'has_tbody' => false,
                'has_tfoot' => false,
                'rows' => 0,
                'columns' => 0
            ];

            // Check for caption
            if ($table->getElementsByTagName('caption')->length > 0) {
                $tableInfo['has_caption'] = true;
            } else {
                $issues[] = [
                    'type' => 'missing_caption',
                    'description' => 'Table missing caption element',
                    'path' => $table->getNodePath(),
                    'impact' => 'medium'
                ];
            }

            // Check table structure
            $tableInfo['has_thead'] = $table->getElementsByTagName('thead')->length > 0;
            $tableInfo['has_tbody'] = $table->getElementsByTagName('tbody')->length > 0;
            $tableInfo['has_tfoot'] = $table->getElementsByTagName('tfoot')->length > 0;

            // Analyze headers
            $headers = $table->getElementsByTagName('th');
            if ($headers->length > 0) {
                $tableInfo['has_headers'] = true;
                foreach ($headers as $header) {
                    if ($header->hasAttribute('scope')) {
                        $tableInfo['has_scope'] = true;
                    }
                }
            } else {
                $issues[] = [
                    'type' => 'missing_headers',
                    'description' => 'Table missing header cells (th elements)',
                    'path' => $table->getNodePath(),
                    'impact' => 'high'
                ];
            }

            // Count rows and columns
            $rows = $table->getElementsByTagName('tr');
            $tableInfo['rows'] = $rows->length;
            if ($rows->length > 0) {
                $firstRow = $rows->item(0);
                $tableInfo['columns'] = $firstRow->getElementsByTagName('td')->length + 
                                      $firstRow->getElementsByTagName('th')->length;
            }

            $tableAnalysis[] = $tableInfo;
        }

        return [
            'tables_found' => count($tableAnalysis),
            'table_analysis' => $tableAnalysis,
            'issues' => $issues,
            'recommendations' => [
                'Use caption element to provide table title/summary',
                'Include proper table headers with th elements',
                'Use scope attribute on header cells',
                'Implement thead, tbody, and tfoot where appropriate',
                'Ensure consistent number of columns across rows',
                'Add appropriate ARIA labels if needed'
            ]
        ];
    }
 
     
}