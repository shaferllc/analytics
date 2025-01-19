<?php

namespace Shaferllc\Analytics\Concerns;

use DOMXPath;
use DOMDocument;
use DOMElement;

class Search {

    private function checkSearchInput(DOMElement $input): bool {
        return $input->getAttribute('type') === 'search';
    }

    private function hasLabel(DOMXPath $xpath, DOMElement $input): bool {
        $id = $input->getAttribute('id');
        return $id && $xpath->query("//label[@for='{$id}']")->length > 0;
    }

    private function hasAriaLabel(DOMElement $input): bool {
        return $input->getAttribute('aria-label') || $input->getAttribute('aria-labelledby');
    }

    public function analyzeSearchForms(DOMDocument $domDocument): array
    {
        $xpath = new DOMXPath($domDocument);
        $issues = [];
        $details = [];
        $searchForms = $xpath->query('//form[contains(@role, "search")] | //form[.//input[@type="search"]]');

        if ($searchForms->length === 0) {
            $issues[] = [
                'type' => 'no_search_form',
                'recommendation' => 'Consider adding a search form to help users find content',
                'severity' => 'high'
            ];
            $details[] = 'No search functionality found';
            return $this->buildResponse($issues, $details);
        }

        foreach ($searchForms as $form) {
            $this->validateSearchForm($xpath, $form, $issues, $details);
        }

        $details = array_merge($details, [
            'Ensure search form has proper role="search"',
            'Include clear submit button',
            'Add placeholder text for search input',
            'Consider adding search suggestions/autocomplete',
            'Ensure search is keyboard accessible',
            'Add clear visual focus indicators',
            'Include search results count',
            'Provide feedback for empty results'
        ]);

        return $this->buildResponse($issues, $details);
    }

    private function validateSearchForm(DOMXPath $xpath, DOMElement $form, array &$issues, array &$details): void 
    {
        // Check for search input
        $searchInput = $xpath->query('.//input[@type="search"]', $form)->length > 0;
        if (!$searchInput) {
            $issues[] = [
                'type' => 'invalid_search_input',
                'recommendation' => 'Use input type="search" for search fields',
                'severity' => 'medium'
            ];
            $details[] = 'Search form missing proper search input type';
        }

        // Check for submit button
        $submitButton = $xpath->query('.//button[@type="submit"] | .//input[@type="submit"]', $form)->length > 0;
        if (!$submitButton) {
            $issues[] = [
                'type' => 'missing_submit',
                'recommendation' => 'Add a submit button for search form',
                'severity' => 'medium'
            ];
            $details[] = 'Search form missing submit button';
        }

        // Check for labels/aria-labels
        $inputs = $xpath->query('.//input', $form);
        foreach ($inputs as $input) {
            if (!$this->hasLabel($xpath, $input) && !$this->hasAriaLabel($input)) {
                $issues[] = [
                    'type' => 'missing_label',
                    'recommendation' => 'Add proper labels for search inputs',
                    'severity' => 'high'
                ];
                $details[] = 'Search input missing label or aria-label';
            }
        }
    }

    private function buildResponse(array $issues, array $details): array
    {
        return [
            'passed' => count($issues) === 0,
            'importance' => 'high',
            'issues' => $issues,
            'details' => array_unique($details)
        ];
    }

    public function findSearchInputs(DOMDocument $domDocument): array
    {
        $xpath = new DOMXPath($domDocument);
        $searchInputs = [];
        $issues = [];
        $details = [];

        // Find explicit search inputs
        $inputs = $xpath->query('//input[@type="search"]');
        foreach ($inputs as $input) {
            $searchInputs[] = [
                'type' => 'explicit',
                'element' => $input->getNodePath(),
                'has_label' => $this->hasLabel($xpath, $input)
            ];
        }

        // Find implicit search inputs
        $textInputs = $xpath->query('//input[@type="text"]');
        foreach ($textInputs as $input) {
            $attrs = [
                'id' => strtolower($input->getAttribute('id')),
                'name' => strtolower($input->getAttribute('name')),
                'placeholder' => strtolower($input->getAttribute('placeholder'))
            ];
            
            if (preg_match('/(search|find|query|q\b)/', implode(' ', $attrs))) {
                $searchInputs[] = [
                    'type' => 'implicit',
                    'element' => $input->getNodePath(),
                    'has_label' => $this->hasLabel($xpath, $input)
                ];
            }
        }

        if (empty($searchInputs)) {
            $issues[] = [
                'type' => 'no_search_inputs',
                'recommendation' => 'Add search functionality to improve user experience',
                'severity' => 'high'
            ];
            $details[] = 'No search inputs found on the page';
        }

        $details = array_merge($details, [
            'Use semantic type="search" for search inputs',
            'Add descriptive placeholder text',
            'Include clear button to reset search',
            'Consider adding search suggestions',
            'Ensure proper keyboard navigation',
            'Add autocomplete functionality'
        ]);

        return [
            'passed' => count($searchInputs) > 0,
            'importance' => 'medium',
            'search_inputs' => $searchInputs,
            'issues' => $issues,
            'details' => array_unique($details)
        ];
    }

    public function analyzeSearchResults(DOMDocument $domDocument): array
    {
        $xpath = new DOMXPath($domDocument);
        $searchResults = [];
        $issues = [];
        $details = [];

        // Check for common search result containers
        $resultContainers = $xpath->query('//*[contains(@class, "search-results") or contains(@class, "results") or @role="search"]');
        
        foreach ($resultContainers as $container) {
            $searchResults[] = [
                'type' => 'container',
                'element' => $container->getNodePath(),
                'has_aria' => $container->getAttribute('role') === 'search'
            ];
        }

        // Look for ARIA landmarks and roles
        $ariaResults = $xpath->query('//*[@aria-label="Search results" or @role="region"][@aria-label="Search results"]');
        foreach ($ariaResults as $result) {
            $searchResults[] = [
                'type' => 'aria',
                'element' => $result->getNodePath(),
                'label' => $result->getAttribute('aria-label')
            ];
        }

        if (empty($searchResults)) {
            $issues[] = [
                'type' => 'no_results_container',
                'recommendation' => 'Add semantic markup for search results container',
                'severity' => 'medium'
            ];
            $details[] = 'No clearly marked search results container found';
        }

        $details = array_merge($details, [
            'Use ARIA landmarks to mark search results region',
            'Include result count and pagination info',
            'Ensure results are keyboard navigable',
            'Provide clear "no results found" message when applicable',
            'Add sorting options for results',
            'Include filtering capabilities'
        ]);

        return [
            'passed' => count($searchResults) > 0,
            'importance' => 'medium',
            'result_containers' => $searchResults,
            'issues' => $issues,
            'details' => array_unique($details)
        ];
    }

    public function analyzeSearchTracking(DOMDocument $domDocument): array
    {
        $xpath = new DOMXPath($domDocument);
        $trackingElements = [];
        $issues = [];
        $details = [];

        // Check for search analytics scripts
        $scripts = $xpath->query('//script');
        foreach ($scripts as $script) {
            $content = $script->textContent;
            $src = $script->getAttribute('src');

            if ($this->hasSearchTracking($content, $src)) {
                $trackingElements[] = [
                    'type' => 'script',
                    'source' => $src ?: 'inline',
                    'element' => $script->getNodePath()
                ];
            }
        }

        // Check for search input tracking attributes
        $searchInputs = $xpath->query('//input[@type="search" or contains(@name, "search") or contains(@id, "search")]');
        foreach ($searchInputs as $input) {
            if ($input->hasAttribute('data-analytics') || $input->hasAttribute('data-tracking')) {
                $trackingElements[] = [
                    'type' => 'input',
                    'element' => $input->getNodePath(),
                    'tracking_attribute' => $input->hasAttribute('data-analytics') ? 'data-analytics' : 'data-tracking'
                ];
            }
        }

        if (empty($trackingElements)) {
            $issues[] = [
                'type' => 'no_search_tracking',
                'recommendation' => 'Implement search analytics tracking',
                'severity' => 'medium'
            ];
            $details[] = 'No search tracking mechanisms detected';
        }

        $details = array_merge($details, [
            'Track search queries and results count',
            'Monitor zero-result searches',
            'Implement search refinement tracking',
            'Track search result interactions',
            'Analyze search patterns',
            'Monitor search abandonment'
        ]);

        return [
            'passed' => count($trackingElements) > 0,
            'importance' => 'medium',
            'tracking_elements' => $trackingElements,
            'issues' => $issues,
            'details' => array_unique($details)
        ];
    }

    private function hasSearchTracking(string $content, ?string $src): bool
    {
        return preg_match('/(searchTracking|trackSearch|search_query|q=|search_term)/i', $content) ||
               preg_match('/(analytics.*search|search.*analytics)/i', $src ?? '');
    }

    public function detectSearchFilters(DOMDocument $domDocument): array
    {
        $xpath = new DOMXPath($domDocument);
        $filters = [];
        $issues = [];
        $details = [];

        // Look for common filter containers and forms
        $filterContainers = $xpath->query('//*[contains(@class, "filter") or contains(@class, "facet") or contains(@class, "refinement")]');
        foreach ($filterContainers as $container) {
            $filters[] = [
                'type' => 'container',
                'element' => $container->getNodePath()
            ];
        }

        // Check for filter-related form controls
        $formControls = $xpath->query('//select|//input[@type="checkbox"]|//input[@type="radio"]');
        foreach ($formControls as $control) {
            $parent = $control->parentNode;
            if ($parent && $this->isFilterControl($parent)) {
                $filters[] = [
                    'type' => 'form_control',
                    'element' => $control->getNodePath(),
                    'control_type' => $control->nodeName
                ];
            }
        }

        // Look for ARIA filter widgets
        $ariaFilters = $xpath->query('//*[@role="listbox" or @role="combobox" or @role="group"][@aria-label]');
        foreach ($ariaFilters as $filter) {
            $label = $filter->getAttribute('aria-label');
            if (preg_match('/(filter|facet|refine|sort|category)/i', $label)) {
                $filters[] = [
                    'type' => 'aria_widget',
                    'element' => $filter->getNodePath(),
                    'label' => $label
                ];
            }
        }

        if (empty($filters)) {
            $issues[] = [
                'type' => 'no_filters_found',
                'recommendation' => 'Add semantic filter controls for search refinement',
                'severity' => 'medium'
            ];
            $details[] = 'No search filter controls detected';
        }

        $details = array_merge($details, [
            'Use ARIA roles and labels for filter controls',
            'Ensure filters are keyboard accessible',
            'Provide clear visual feedback for active filters',
            'Consider adding filter count indicators',
            'Add clear all filters option',
            'Implement mobile-friendly filter UI'
        ]);

        return [
            'passed' => count($filters) > 0,
            'importance' => 'medium',
            'filters' => $filters,
            'issues' => $issues,
            'details' => array_unique($details)
        ];
    }

    private function isFilterControl(DOMElement $element): bool
    {
        $classAndId = $element->getAttribute('class') . ' ' . $element->getAttribute('id');
        return (bool)preg_match('/(filter|facet|refine|sort|category)/i', $classAndId);
    }

    public function analyzeSiteSearchMarkup(DOMDocument $domDocument): array
    {
        $xpath = new DOMXPath($domDocument);
        $searchElements = [];
        $issues = [];
        $details = [];

        $this->checkStructuredData($domDocument, $searchElements);
        $this->checkOpenSearch($xpath, $searchElements);
        $this->checkSemanticMarkup($xpath, $searchElements);

        if (empty($searchElements)) {
            $issues[] = [
                'type' => 'missing_search_markup',
                'recommendation' => 'Implement structured search markup',
                'severity' => 'medium'
            ];
            $details[] = 'No search-specific markup detected';
        }

        $details = array_merge($details, [
            'Add Sitelinks searchbox markup',
            'Implement OpenSearch description',
            'Use semantic ARIA roles for search components',
            'Ensure search markup is properly structured',
            'Add search action schema markup',
            'Include search target URL template'
        ]);

        return [
            'passed' => count($searchElements) > 0,
            'importance' => 'medium',
            'search_elements' => $searchElements,
            'issues' => $issues,
            'details' => array_unique($details)
        ];
    }

    private function checkStructuredData(DOMDocument $domDocument, array &$searchElements): void
    {
        $scripts = $domDocument->getElementsByTagName('script');
        foreach ($scripts as $script) {
            if ($script->getAttribute('type') === 'application/ld+json') {
                $json = json_decode($script->textContent, true);
                if ($json && isset($json['@type']) && $json['@type'] === 'WebSite' && isset($json['potentialAction'])) {
                    $searchElements[] = [
                        'type' => 'structured_data',
                        'format' => 'json-ld',
                        'element' => $script->getNodePath()
                    ];
                }
            }
        }
    }

    private function checkOpenSearch(DOMXPath $xpath, array &$searchElements): void
    {
        $links = $xpath->query('//link[@rel="search"]');
        foreach ($links as $link) {
            if ($link->getAttribute('type') === 'application/opensearchdescription+xml') {
                $searchElements[] = [
                    'type' => 'opensearch',
                    'href' => $link->getAttribute('href'),
                    'element' => $link->getNodePath()
                ];
            }
        }
    }

    private function checkSemanticMarkup(DOMXPath $xpath, array &$searchElements): void
    {
        $searchForms = $xpath->query('//form[contains(@role, "search")]|//div[contains(@role, "search")]');
        foreach ($searchForms as $form) {
            $searchElements[] = [
                'type' => 'semantic_markup',
                'element' => $form->getNodePath(),
                'role' => $form->getAttribute('role')
            ];
        }
    }
}