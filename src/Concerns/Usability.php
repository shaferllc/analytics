<?php

namespace Shaferllc\Analytics\Concerns;

use DOMDocument;


class Usability
{
    
    public function analyzeNavigation(DOMDocument $domDocument): array
    {
        $navigation = [];
        $issues = [];

        // Analyze main navigation
        $navElements = $domDocument->getElementsByTagName('nav');
        foreach ($navElements as $nav) {
            $links = $nav->getElementsByTagName('a');
            $navigation[] = [
                'type' => $nav->getAttribute('aria-label') ?: 'unnamed',
                'link_count' => $links->length,
                'has_aria_label' => $nav->hasAttribute('aria-label'),
                'path' => $nav->getNodePath()
            ];

            // Check for navigation accessibility
            if (!$nav->hasAttribute('aria-label')) {
                $issues[] = [
                    'type' => 'missing_nav_label',
                    'element' => $nav->getNodePath(),
                    'impact' => 'medium'
                ];
            }
        }

        return [
            'navigation_elements' => $navigation,
            'issues' => $issues,
            'recommendations' => [
                'Ensure all navigation elements have aria-labels',
                'Keep navigation consistent across pages',
                'Provide clear visual hierarchy',
                'Include skip navigation links for accessibility'
            ]
        ];
    }



    public function analyzeFormUsability(DOMDocument $domDocument): array
    {
        $forms = [];
        $issues = [];

        $formElements = $domDocument->getElementsByTagName('form');
        foreach ($formElements as $form) {
            $formAnalysis = [
                'inputs' => 0,
                'required_fields' => 0,
                'has_labels' => true,
                'has_submit' => false,
                'has_validation' => false,
                'path' => $form->getNodePath()
            ];

            // Analyze form elements
            $inputs = $form->getElementsByTagName('input');
            foreach ($inputs as $input) {
                $formAnalysis['inputs']++;
                if ($input->hasAttribute('required')) {
                    $formAnalysis['required_fields']++;
                }
                if ($input->getAttribute('type') === 'submit') {
                    $formAnalysis['has_submit'] = true;
                }
            }

            $forms[] = $formAnalysis;
        }

        return [
            'forms' => $forms,
            'issues' => $issues,
            'recommendations' => [
                'Use clear labels for all form fields',
                'Provide validation feedback',
                'Mark required fields consistently',
                'Include submit buttons with clear actions'
            ]
        ];
    }

     /**
     * Analyzes the overall page layout and structure
     *
     * @param DOMDocument $domDocument The DOM document to analyze
     * @return array Analysis of page layout including structure and potential issues
     */
    public function analyzePageLayout(DOMDocument $domDocument): array
    {
        $layout = [];
        $issues = [];

        // Check for semantic structure elements
        $semanticElements = ['header', 'main', 'footer', 'nav', 'article', 'section', 'aside'];
        foreach ($semanticElements as $element) {
            $count = $domDocument->getElementsByTagName($element)->length;
            $layout['semantic_elements'][$element] = $count;
            
            if ($element === 'main' && $count === 0) {
                $issues[] = [
                    'type' => 'missing_main',
                    'description' => 'No main content area defined',
                    'impact' => 'medium'
                ];
            }
        }

        // Analyze content width and potential overflow
        $body = $domDocument->getElementsByTagName('body')->item(0);
        if ($body) {
            $elements = $body->getElementsByTagName('*');
            foreach ($elements as $element) {
                if ($element->hasAttribute('style')) {
                    $style = $element->getAttribute('style');
                    if (strpos($style, 'width: 100vw') !== false || strpos($style, 'margin-left: -') !== false) {
                        $issues[] = [
                            'type' => 'potential_overflow',
                            'description' => 'Element may cause horizontal scrolling',
                            'path' => $element->getNodePath(),
                            'impact' => 'medium'
                        ];
                    }
                }
            }
        }

        // Check for landmark regions
        $landmarks = $domDocument->getElementsByTagName('*');
        $hasLandmarks = false;
        foreach ($landmarks as $element) {
            if ($element->hasAttribute('role')) {
                $hasLandmarks = true;
                break;
            }
        }

        if (!$hasLandmarks) {
            $issues[] = [
                'type' => 'missing_landmarks',
                'description' => 'No ARIA landmark roles found',
                'impact' => 'medium'
            ];
        }

        return [
            'layout_structure' => $layout,
            'issues' => $issues,
            'recommendations' => [
                'Use semantic HTML elements for page structure',
                'Implement ARIA landmarks for better accessibility',
                'Ensure content fits viewport width',
                'Maintain consistent layout across different screen sizes',
                'Use appropriate spacing between elements',
                'Consider mobile-first responsive design'
            ]
        ];
    }


    public function analyzeVisualHierarchy(DOMDocument $domDocument): array
    {
        $xpath = new \DOMXPath($domDocument);
        $hierarchyElements = [];
        $issues = [];
        $details = [];

        // Check heading structure
        $headings = $xpath->query('//h1|//h2|//h3|//h4|//h5|//h6');
        $lastLevel = 0;
        foreach ($headings as $heading) {
            $currentLevel = (int)substr($heading->nodeName, 1);
            
            if ($lastLevel > 0 && $currentLevel - $lastLevel > 1) {
                $issues[] = [
                    'type' => 'skipped_heading_level',
                    'element' => $heading->getNodePath(),
                    'recommendation' => "Don't skip heading levels (h{$lastLevel} to h{$currentLevel})"
                ];
            }
            
            $hierarchyElements[] = [
                'type' => 'heading',
                'level' => $currentLevel,
                'text' => trim($heading->textContent),
                'path' => $heading->getNodePath()
            ];
            
            $lastLevel = $currentLevel;
        }

        // Check for visual sections and containers
        $sections = $xpath->query('//section|//article|//main|//aside|//div[contains(@class, "section")]');
        foreach ($sections as $section) {
            $hierarchyElements[] = [
                'type' => 'section',
                'element' => $section->nodeName,
                'path' => $section->getNodePath()
            ];
        }

        // Check for lists and their nesting
        $lists = $xpath->query('//ul|//ol');
        foreach ($lists as $list) {
            $nestingLevel = 0;
            $parent = $list->parentNode;
            while ($parent) {
                if ($parent->nodeName === 'ul' || $parent->nodeName === 'ol') {
                    $nestingLevel++;
                }
                $parent = $parent->parentNode;
            }

            if ($nestingLevel > 3) {
                $issues[] = [
                    'type' => 'deep_list_nesting',
                    'element' => $list->getNodePath(),
                    'recommendation' => 'Consider simplifying deeply nested lists'
                ];
            }

            $hierarchyElements[] = [
                'type' => 'list',
                'element' => $list->nodeName,
                'nesting_level' => $nestingLevel,
                'path' => $list->getNodePath()
            ];
        }

        if (empty($hierarchyElements)) {
            $issues[] = [
                'type' => 'no_visual_structure',
                'recommendation' => 'Add clear visual hierarchy with headings and sections'
            ];
            $details[] = 'No clear visual hierarchy elements detected';
        }

        $details = array_merge($details, [
            'Use proper heading hierarchy (h1-h6)',
            'Structure content with semantic sections',
            'Maintain consistent visual patterns',
            'Use whitespace effectively for content separation',
            'Ensure logical nesting of elements'
        ]);

        return [
            'passed' => empty($issues),
            'importance' => 'high',
            'hierarchy_elements' => $hierarchyElements,
            'issues' => $issues,
            'details' => $details
        ];
    }


    public function analyzeInteractionElements(DOMDocument $domDocument): array
    {
        $interactiveElements = [];
        $issues = [];

        // Analyze buttons
        $buttons = $domDocument->getElementsByTagName('button');
        foreach ($buttons as $button) {
            $buttonAnalysis = [
                'type' => 'button',
                'text' => trim($button->textContent),
                'has_aria_label' => $button->hasAttribute('aria-label'),
                'path' => $button->getNodePath()
            ];
            $interactiveElements[] = $buttonAnalysis;

            if (!$buttonAnalysis['text'] && !$buttonAnalysis['has_aria_label']) {
                $issues[] = [
                    'type' => 'button_accessibility',
                    'message' => 'Button lacks text content and aria-label',
                    'path' => $buttonAnalysis['path']
                ];
            }
        }

        // Analyze links
        $links = $domDocument->getElementsByTagName('a');
        foreach ($links as $link) {
            $linkAnalysis = [
                'type' => 'link',
                'text' => trim($link->textContent),
                'href' => $link->getAttribute('href'),
                'has_aria_label' => $link->hasAttribute('aria-label'),
                'path' => $link->getNodePath()
            ];
            $interactiveElements[] = $linkAnalysis;

            if (!$linkAnalysis['text'] && !$linkAnalysis['has_aria_label']) {
                $issues[] = [
                    'type' => 'link_accessibility',
                    'message' => 'Link lacks text content and aria-label',
                    'path' => $linkAnalysis['path']
                ];
            }
        }

        return [
            'interactive_elements' => $interactiveElements,
            'issues' => $issues,
            'recommendations' => [
                'Ensure all buttons have descriptive text or aria-labels',
                'Make link text meaningful and descriptive',
                'Add hover and focus states for interactive elements',
                'Maintain sufficient touch targets for mobile users'
            ]
        ];
    }
 
}