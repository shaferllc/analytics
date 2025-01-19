<?php

namespace Shaferllc\Analytics\Concerns;

use DOMDocument;

class Structure
{
   /**
     * Analyzes the heading hierarchy and structure of the document
     *
     * @param DOMDocument $domDocument The DOM document to analyze
     * @return array Analysis of heading structure including hierarchy and potential issues
     */
    public function analyzeHeadingHierarchy(DOMDocument $domDocument): array
    {
        $headings = [];
        $issues = [];
        $previousLevel = 0;
        $hasH1 = false;
        $h1Count = 0;

        // Analyze all heading tags h1-h6
        for ($i = 1; $i <= 6; $i++) {
            $headingElements = $domDocument->getElementsByTagName('h' . $i);
            foreach ($headingElements as $heading) {
                $level = (int)substr($heading->nodeName, 1);
                $headingInfo = [
                    'level' => $level,
                    'text' => trim($heading->textContent),
                    'path' => $heading->getNodePath()
                ];
                $headings[] = $headingInfo;

                // Check for h1 presence and count
                if ($level === 1) {
                    $hasH1 = true;
                    $h1Count++;
                    if ($h1Count > 1) {
                        $issues[] = [
                            'type' => 'multiple_h1',
                            'description' => 'Multiple H1 headings found',
                            'path' => $heading->getNodePath(),
                            'impact' => 'high'
                        ];
                    }
                }

                // Check for skipped heading levels
                if ($level - $previousLevel > 1 && $previousLevel !== 0) {
                    $issues[] = [
                        'type' => 'skipped_level',
                        'description' => "Heading level skipped from H{$previousLevel} to H{$level}",
                        'path' => $heading->getNodePath(),
                        'impact' => 'medium'
                    ];
                }

                $previousLevel = $level;
            }
        }

        // Check if H1 is missing
        if (!$hasH1) {
            $issues[] = [
                'type' => 'missing_h1',
                'description' => 'No H1 heading found in document',
                'impact' => 'high'
            ];
        }

        return [
            'headings' => $headings,
            'total_headings' => count($headings),
            'has_h1' => $hasH1,
            'h1_count' => $h1Count,
            'issues' => $issues,
            'recommendations' => [
                'Use a single H1 heading per page',
                'Maintain proper heading hierarchy without skipping levels',
                'Keep heading text concise and descriptive',
                'Use headings to create a clear document outline'
            ]
        ];
    }

    public function analyzeLinkStructure(DOMDocument $domDocument): array
    {
        $links = [
            'internal' => [],
            'external' => [],
            'broken' => [],
            'nofollow' => []
        ];
        
        $anchors = $domDocument->getElementsByTagName('a');
        foreach ($anchors as $anchor) {
            $href = $anchor->attributes?->getNamedItem('href')?->nodeValue;
            $rel = $anchor->attributes?->getNamedItem('rel')?->nodeValue;
            $text = trim($anchor->textContent);
            
            $linkData = [
                'url' => $href,
                'text' => $text,
                'rel' => $rel,
                'title' => $anchor->attributes?->getNamedItem('title')?->nodeValue,
                'path' => $anchor->getNodePath()
            ];
            
            if (empty($href) || $href === '#') {
                $links['broken'][] = $linkData;
            } elseif (strpos($rel, 'nofollow') !== false) {
                $links['nofollow'][] = $linkData;
            } elseif (parse_url($href, PHP_URL_HOST) !== null) {
                $links['external'][] = $linkData;
            } else {
                $links['internal'][] = $linkData;
            }
        }

        return [
            'link_counts' => [
                'total' => count($anchors),
                'internal' => count($links['internal']),
                'external' => count($links['external']),
                'broken' => count($links['broken']),
                'nofollow' => count($links['nofollow'])
            ],
            'links' => $links,
            'recommendations' => [
                'Fix broken links',
                'Use descriptive anchor text',
                'Balance internal and external linking',
                'Implement proper nofollow attributes',
                'Add title attributes to important links',
                'Ensure external links open in new tabs',
                'Use consistent link styling'
            ]
        ];
    }

    public function analyzeUrlStructure(DOMDocument $domDocument): array
    {
        $xpath = new \DOMXPath($domDocument);
        $links = $xpath->query('//a[@href]');
        $issues = [];
        $urlData = [];

        foreach ($links as $link) {
            $href = $link->attributes?->getNamedItem('href')?->nodeValue;
            $url = parse_url($href);
            
            // Skip empty, javascript: and mailto: links
            if (empty($href) || str_starts_with($href, 'javascript:') || str_starts_with($href, 'mailto:')) {
                continue;
            }

            $urlData[] = [
                'url' => $href,
                'text' => $link->textContent,
                'path' => $link->getNodePath()
            ];

            // Check for URL issues
            if (strlen($href) > 100) {
                $issues[] = [
                    'type' => 'long_url',
                    'url' => $href,
                    'recommendation' => 'Keep URLs under 100 characters'
                ];
            }

            if (preg_match('/[A-Z]/', $href)) {
                $issues[] = [
                    'type' => 'uppercase_chars',
                    'url' => $href, 
                    'recommendation' => 'Use lowercase characters in URLs'
                ];
            }

            if (str_contains($href, ' ')) {
                $issues[] = [
                    'type' => 'spaces_in_url',
                    'url' => $href,
                    'recommendation' => 'Replace spaces with hyphens'
                ];
            }

            if (isset($url['query'])) {
                $issues[] = [
                    'type' => 'query_parameters',
                    'url' => $href,
                    'recommendation' => 'Consider using clean URLs without query parameters'
                ];
            }
        }

        return [
            'passed' => count($issues) === 0,
            'importance' => 'high',
            'total_urls' => count($urlData),
            'urls' => $urlData,
            'issues' => $issues,
            'details' => [
                'Use descriptive keywords in URLs',
                'Keep URLs short and readable',
                'Use hyphens to separate words',
                'Avoid query parameters when possible',
                'Implement proper URL structure for SEO',
                'Ensure URLs are mobile-friendly',
                'Use canonical URLs when needed'
            ]
        ];
    }

    public function analyzeNavigationStructure(DOMDocument $domDocument): array 
    {
        $xpath = new \DOMXPath($domDocument);
        $navElements = $xpath->query('//nav|//ul[contains(@class, "menu")]|//ul[contains(@class, "nav")]');
        $navigation = [];
        $issues = [];

        foreach ($navElements as $nav) {
            $menuItems = $xpath->query('.//li/a', $nav);
            $navData = [
                'items' => [],
                'depth' => 0,
                'path' => $nav->getNodePath()
            ];

            foreach ($menuItems as $item) {
                $navData['items'][] = [
                    'text' => trim($item->textContent),
                    'url' => $item->attributes?->getNamedItem('href')?->nodeValue,
                    'active' => $item->attributes?->getNamedItem('class')?->nodeValue === 'active'
                ];
            }

            $navigation[] = $navData;
        }

        return [
            'total_menus' => count($navigation),
            'menus' => $navigation,
            'issues' => $issues,
            'recommendations' => [
                'Use semantic nav elements',
                'Implement clear menu hierarchy',
                'Add aria labels for accessibility',
                'Include mobile-friendly navigation',
                'Highlight active menu items'
            ]
        ];
    }

    public function analyzeFooterStructure(DOMDocument $domDocument): array
    {
        $xpath = new \DOMXPath($domDocument);
        $footer = $xpath->query('//footer')->item(0);
        $footerData = [];
        $issues = [];

        if ($footer) {
            $sections = $xpath->query('.//div[contains(@class, "footer")]', $footer);
            foreach ($sections as $section) {
                $footerData[] = [
                    'content' => trim($section->textContent),
                    'links' => $this->extractLinks($section),
                    'path' => $section->getNodePath()
                ];
            }
        } else {
            $issues[] = [
                'type' => 'missing_footer',
                'description' => 'No semantic footer element found',
                'impact' => 'medium'
            ];
        }

        return [
            'has_footer' => (bool)$footer,
            'sections' => $footerData,
            'issues' => $issues,
            'recommendations' => [
                'Use semantic footer element',
                'Include copyright information',
                'Add social media links',
                'Provide contact information',
                'Include sitemap links'
            ]
        ];
    }

    private function extractLinks(\DOMNode $element): array
    {
        $links = [];
        $xpath = new \DOMXPath($element->ownerDocument);
        $anchors = $xpath->query('.//a', $element);
        
        foreach ($anchors as $anchor) {
            $links[] = [
                'text' => trim($anchor->textContent),
                'url' => $anchor->attributes?->getNamedItem('href')?->nodeValue,
                'title' => $anchor->attributes?->getNamedItem('title')?->nodeValue
            ];
        }
        
        return $links;
    }
}
