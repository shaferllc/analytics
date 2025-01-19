<?php

namespace Shaferllc\Analytics\Concerns;

use DOMXPath;
use DOMElement;
use DOMDocument;

class Accessibility
{
    /**
     * Analyzes ARIA usage and accessibility roles in the document
     *
     * @param DOMDocument $domDocument The DOM document to analyze
     * @return array Analysis of ARIA attributes, roles and potential issues
     */
    public function analyzeAriaUsage(DOMDocument $domDocument): array
    {
        $ariaUsage = [
            'roles' => [],
            'attributes' => [],
            'landmarks' => [],
            'issues' => []
        ];

        $elements = $domDocument->getElementsByTagName('*');
        foreach ($elements as $element) {
            if (!$element instanceof DOMElement) {
                continue;
            }

            $this->checkRoles($element, $ariaUsage);
            $this->checkAriaAttributes($element, $ariaUsage);
            $this->checkCommonAriaIssues($element, $ariaUsage);
        }

        $this->addSummaryStatistics($ariaUsage);
        $this->addRecommendations($ariaUsage);

        return $ariaUsage;
    }

    /**
     * Analyzes alt text usage and quality in images
     *
     * @param DOMDocument $domDocument The DOM document to analyze
     * @return array Analysis of alt text usage including issues and recommendations
     */
    public function analyzeAltTexts(DOMDocument $domDocument): array
    {
        $images = $domDocument->getElementsByTagName('img');
        $altTexts = [];
        $issues = [];

        foreach ($images as $img) {
            $imageInfo = $this->getImageInfo($img);
            $altTexts[] = $imageInfo;

            $this->checkAltTextIssues($img, $imageInfo, $issues);
        }

        return [
            'images' => $altTexts,
            'total_images' => count($altTexts),
            'images_with_alt' => count(array_filter($altTexts, fn($img) => $img['has_alt'])),
            'issues' => $issues,
            'total_issues' => count($issues),
            'recommendations' => $this->getAltTextRecommendations()
        ];
    }

    /**
     * Checks if an image appears to be decorative
     */
    private function isDecorativeImage(DOMElement $img): bool
    {
        $class = $img->getAttribute('class');
        $role = $img->getAttribute('role');
        
        return (
            str_contains($class, 'decorative') ||
            str_contains($class, 'background') ||
            $role === 'presentation' ||
            $role === 'none' ||
            $img->getAttribute('aria-hidden') === 'true'
        );
    }

    /**
     * Analyzes color contrast ratios in the document
     *
     * @param DOMDocument $domDocument The DOM document to analyze
     * @return array Color contrast analysis results
     */
    public function analyzeColorContrast(DOMDocument $domDocument): array
    {
        $xpath = new \DOMXPath($domDocument);
        $elements = $xpath->query('//*[text()]');
        
        $contrastIssues = [];
        $analyzedElements = [];
        
        foreach ($elements as $element) {
            if (!$element instanceof DOMElement) {
                continue;
            }
            
            $elementInfo = $this->getElementColorInfo($element);
            if (!$elementInfo['foreground'] && !$elementInfo['background']) {
                continue;
            }
            
            if ($this->hasLowContrast($elementInfo['foreground'], $elementInfo['background'])) {
                $contrastIssues[] = [
                    'type' => 'low_contrast',
                    'element' => $elementInfo,
                    'recommendation' => 'Increase color contrast to meet WCAG 2.1 Level AA standards'
                ];
            }
            
            $analyzedElements[] = $elementInfo;
        }

        return [
            'elements_analyzed' => count($analyzedElements),
            'contrast_issues' => $contrastIssues,
            'total_issues' => count($contrastIssues),
            'analyzed_elements' => $analyzedElements,
            'recommendations' => $this->getContrastRecommendations($elementInfo)
        ];
    }

    /**
     * Analyzes keyboard navigation accessibility of the document
     *
     * @param DOMDocument $domDocument The DOM document to analyze
     * @return array Analysis of keyboard navigation including issues and recommendations
     */
    public function analyzeKeyboardNavigation(DOMDocument $domDocument): array
    {
        $issues = [];
        $interactiveElements = [];
        $xpath = new \DOMXPath($domDocument);

        $elements = $this->getInteractiveElements($xpath);

        foreach ($elements as $element) {
            if (!$element instanceof DOMElement) {
                continue;
            }

            $elementInfo = $this->getElementNavigationInfo($element);
            $this->checkNavigationIssues($element, $elementInfo, $issues);
            $interactiveElements[] = $elementInfo;
        }

        return [
            'interactive_elements' => $interactiveElements,
            'total_interactive' => count($interactiveElements),
            'issues' => $issues,
            'total_issues' => count($issues),
            'recommendations' => $this->getKeyboardNavigationRecommendations()
        ];
    }

    /**
     * Helper method to check if colors have potentially low contrast
     */
    private function hasLowContrast(?string $color, ?string $backgroundColor): bool
    {
        if (!$color || !$backgroundColor) {
            return false;
        }
        
        $lowContrastPairs = [
            ['#fff', '#ddd'],
            ['#000', '#333'],
            ['#777', '#999']
        ];
        
        foreach ($lowContrastPairs as $pair) {
            if (stripos($color, $pair[0]) !== false && 
                stripos($backgroundColor, $pair[1]) !== false) {
                return true;
            }
        }
        
        return false;
    }
    /**
     * Analyzes focus management in interactive elements
     * 
     * @param DOMDocument $dom The DOM document to analyze
     * @return array Analysis results including issues and recommendations
     */
    public function analyzeFocusManagement(DOMDocument $dom): array
    {
        $issues = [];
        $focusableElements = $this->getFocusableElements($dom);

        foreach ($focusableElements as $element) {
            $this->checkFocusIssues($element, $issues);
        }

        return [
            'focusable_elements' => count($focusableElements),
            'issues' => $issues,
            'total_issues' => count($issues),
            'recommendations' => $this->getFocusManagementRecommendations()
        ];
    }

    /**
     * Analyzes form labels and their associations with form controls
     */
    public function analyzeFormLabels(DOMDocument $dom): array
    {
        $formControls = [];
        $issues = [];

        $this->analyzeFormElements($dom, $formControls, $issues);

        return [
            'form_controls' => $formControls,
            'total_controls' => count($formControls),
            'labeled_controls' => count(array_filter($formControls, fn($control) => $control['has_label'])),
            'issues' => $issues,
            'total_issues' => count($issues),
            'recommendations' => $this->getFormLabelRecommendations()
        ];
    }

    /**
     * Checks for ARIA roles and adds them to the analysis
     *
     * @param DOMElement $element The element to check
     * @param array $ariaUsage The array tracking ARIA usage
     */
    private function checkRoles(DOMElement $element, array &$ariaUsage): void
    {
        if ($element->hasAttribute('role')) {
            $role = $element->getAttribute('role');
            
            if (!isset($ariaUsage['roles'][$role])) {
                $ariaUsage['roles'][$role] = 0;
            }
            $ariaUsage['roles'][$role]++;

            // Check for landmark roles
            $landmarkRoles = ['banner', 'navigation', 'main', 'complementary', 'contentinfo', 'search', 'form'];
            if (in_array($role, $landmarkRoles)) {
                if (!isset($ariaUsage['landmarks'][$role])) {
                    $ariaUsage['landmarks'][$role] = 0;
                }
                $ariaUsage['landmarks'][$role]++;
            }

            // Check for invalid/deprecated roles
            $deprecatedRoles = ['presentation', 'directory'];
            if (in_array($role, $deprecatedRoles)) {
                $ariaUsage['issues'][] = [
                    'type' => 'deprecated_role',
                    'element' => $element->getNodePath(),
                    'role' => $role,
                    'message' => "The role '$role' is deprecated or not recommended"
                ];
            }

            // Check for mismatched semantics
            if ($element->tagName === 'button' && $role !== 'button') {
                $ariaUsage['issues'][] = [
                    'type' => 'semantic_mismatch',
                    'element' => $element->getNodePath(),
                    'role' => $role,
                    'message' => "Button element has mismatched role '$role'"
                ];
            }
        }
    }

    /**
     * Checks for ARIA attributes and adds them to the analysis
     *
     * @param DOMElement $element The element to check
     * @param array $ariaUsage The array tracking ARIA usage
     */
    private function checkAriaAttributes(DOMElement $element, array &$ariaUsage): void
    {
        foreach ($element->attributes as $attribute) {
            if (strpos($attribute->name, 'aria-') === 0) {
                $ariaAttr = $attribute->name;
                
                if (!isset($ariaUsage['attributes'][$ariaAttr])) {
                    $ariaUsage['attributes'][$ariaAttr] = 0;
                }
                $ariaUsage['attributes'][$ariaAttr]++;

                // Check for invalid values
                $value = $attribute->value;
                if ($ariaAttr === 'aria-hidden' && !in_array($value, ['true', 'false'])) {
                    $ariaUsage['issues'][] = [
                        'type' => 'invalid_value',
                        'element' => $element->getNodePath(),
                        'attribute' => $ariaAttr,
                        'value' => $value,
                        'message' => "Invalid value for aria-hidden (must be 'true' or 'false')"
                    ];
                }

                // Check for redundant attributes
                if ($ariaAttr === 'aria-label' && $element->hasAttribute('alt')) {
                    $ariaUsage['issues'][] = [
                        'type' => 'redundant_attributes',
                        'element' => $element->getNodePath(),
                        'message' => "Element has both aria-label and alt attributes"
                    ];
                }

                // Check for empty values
                if (empty($value) && !in_array($ariaAttr, ['aria-expanded'])) {
                    $ariaUsage['issues'][] = [
                        'type' => 'empty_value',
                        'element' => $element->getNodePath(),
                        'attribute' => $ariaAttr,
                        'message' => "ARIA attribute has empty value"
                    ];
                }
            }
        }
    }

    /**
     * Checks for common ARIA-related accessibility issues
     *
     * @param DOMElement $element The element to check
     * @param array $ariaUsage The array tracking ARIA usage and issues
     */
    private function checkCommonAriaIssues(DOMElement $element, array &$ariaUsage): void
    {
        // Check for invalid ARIA roles
        $role = $element->getAttribute('role');
        if ($role && !in_array($role, [
            'alert', 'alertdialog', 'application', 'article', 'banner', 'button', 
            'cell', 'checkbox', 'columnheader', 'combobox', 'complementary', 
            'contentinfo', 'definition', 'dialog', 'directory', 'document',
            'feed', 'figure', 'form', 'grid', 'gridcell', 'group', 'heading',
            'img', 'link', 'list', 'listbox', 'listitem', 'log', 'main',
            'marquee', 'math', 'menu', 'menubar', 'menuitem', 'menuitemcheckbox',
            'menuitemradio', 'navigation', 'none', 'note', 'option', 'presentation',
            'progressbar', 'radio', 'radiogroup', 'region', 'row', 'rowgroup',
            'rowheader', 'scrollbar', 'search', 'searchbox', 'separator',
            'slider', 'spinbutton', 'status', 'switch', 'tab', 'table',
            'tablist', 'tabpanel', 'term', 'textbox', 'timer', 'toolbar',
            'tooltip', 'tree', 'treegrid', 'treeitem'
        ])) {
            $ariaUsage['issues'][] = [
                'type' => 'invalid_role',
                'element' => $element->getNodePath(),
                'role' => $role,
                'message' => "Invalid ARIA role: $role"
            ];
        }

        // Check for required ARIA attributes based on role
        if ($role === 'checkbox' || $role === 'switch') {
            if (!$element->hasAttribute('aria-checked')) {
                $ariaUsage['issues'][] = [
                    'type' => 'missing_required_attr',
                    'element' => $element->getNodePath(),
                    'role' => $role,
                    'message' => "Elements with role='$role' must have aria-checked attribute"
                ];
            }
        }

        // Check for conflicting ARIA states
        if ($element->hasAttribute('aria-hidden') && $element->hasAttribute('aria-live')) {
            $ariaUsage['issues'][] = [
                'type' => 'conflicting_states',
                'element' => $element->getNodePath(),
                'message' => "Element cannot have both aria-hidden and aria-live attributes"
            ];
        }

        // Check for improper nesting of ARIA landmarks
        if (in_array($role, ['banner', 'complementary', 'contentinfo', 'main', 'navigation', 'search'])) {
            $parent = $element->parentNode;
            if ($parent instanceof DOMElement && $parent->hasAttribute('role')) {
                $parentRole = $parent->getAttribute('role');
                if (in_array($parentRole, ['banner', 'complementary', 'contentinfo', 'main', 'navigation', 'search'])) {
                    $ariaUsage['issues'][] = [
                        'type' => 'nested_landmarks',
                        'element' => $element->getNodePath(),
                        'message' => "Landmark role '$role' should not be nested within another landmark"
                    ];
                }
            }
        }

        // Check for proper heading structure when using aria-level
        if ($element->hasAttribute('aria-level')) {
            $level = (int)$element->getAttribute('aria-level');
            if ($level < 1 || $level > 6) {
                $ariaUsage['issues'][] = [
                    'type' => 'invalid_heading_level',
                    'element' => $element->getNodePath(),
                    'level' => $level,
                    'message' => "aria-level must be between 1 and 6"
                ];
            }
        }
    }
    /**
     * Adds summary statistics to the ARIA usage analysis
     * 
     * @param array $ariaUsage The ARIA usage data to add statistics to
     */
    private function addSummaryStatistics(array &$ariaUsage): void
    {
        $ariaUsage['statistics'] = [
            'total_roles' => count($ariaUsage['roles']),
            'total_attributes' => count($ariaUsage['attributes']),
            'total_landmarks' => count($ariaUsage['landmarks']),
            'total_issues' => count($ariaUsage['issues']),
            'most_used_roles' => array_slice(array_count_values($ariaUsage['roles']), 0, 5),
            'most_used_attributes' => array_slice(array_count_values($ariaUsage['attributes']), 0, 5)
        ];

        // Calculate percentage of elements with ARIA attributes
        if (!empty($ariaUsage['roles']) || !empty($ariaUsage['attributes'])) {
            $totalElements = count($ariaUsage['roles']) + count($ariaUsage['attributes']);
            $elementsWithIssues = count($ariaUsage['issues']);
            $ariaUsage['statistics']['issue_rate'] = round(($elementsWithIssues / $totalElements) * 100, 2);
        } else {
            $ariaUsage['statistics']['issue_rate'] = 0;
        }
    }

    /**
     * Adds accessibility recommendations based on the analysis
     * 
     * @param array $ariaUsage The ARIA usage data to add recommendations to
     */
    private function addRecommendations(array &$ariaUsage): void
    {
        $ariaUsage['recommendations'] = [
            'Use ARIA landmarks to define page regions',
            'Ensure proper nesting of landmark roles',
            'Provide accessible names for interactive elements',
            'Use appropriate ARIA roles and attributes',
            'Validate heading structure and levels',
            'Test with screen readers and accessibility tools'
        ];

        // Add context-specific recommendations based on issues found
        if (!empty($ariaUsage['issues'])) {
            $issueTypes = array_column($ariaUsage['issues'], 'type');
            
            if (in_array('nested_landmarks', $issueTypes)) {
                $ariaUsage['recommendations'][] = 'Avoid nesting landmark roles unnecessarily';
            }
            
            if (in_array('invalid_heading_level', $issueTypes)) {
                $ariaUsage['recommendations'][] = 'Ensure aria-level attributes are between 1 and 6';
            }

            if (count($ariaUsage['landmarks']) === 0) {
                $ariaUsage['recommendations'][] = 'Add landmark roles to improve page structure';
            }
        }
    }

    /**
     * Gets information about an image element
     *
     * @param DOMElement $img The image element to analyze
     * @return array Information about the image including alt text and attributes
     */
    private function getImageInfo(DOMElement $img): array
    {
        $alt = $img->getAttribute('alt');
        $src = $img->getAttribute('src');
        $title = $img->getAttribute('title');
        $isDecorative = $this->isDecorativeImage($img);

        return [
            'src' => $src,
            'alt' => $alt,
            'title' => $title,
            'has_alt' => $alt !== '',
            'alt_length' => strlen($alt),
            'is_decorative' => $isDecorative,
            'node_path' => $img->getNodePath(),
            'attributes' => [
                'role' => $img->getAttribute('role'),
                'aria-label' => $img->getAttribute('aria-label'),
                'aria-hidden' => $img->getAttribute('aria-hidden'),
                'class' => $img->getAttribute('class')
            ]
        ];
    }

    /**
     * Checks for issues with alt text usage on an image
     *
     * @param DOMElement $img The image element to check
     * @param array $imageInfo Information about the image
     * @param array &$issues Array to store found issues
     */
    private function checkAltTextIssues(DOMElement $img, array $imageInfo, array &$issues): void
    {
        // Skip decorative images as they don't need alt text
        if ($imageInfo['is_decorative']) {
            return;
        }

        // Check if alt text is missing
        if (!$imageInfo['has_alt']) {
            $issues[] = [
                'type' => 'missing_alt',
                'message' => 'Image is missing alt text',
                'element' => $imageInfo['node_path'],
                'src' => $imageInfo['src']
            ];
            return;
        }

        // Check if alt text is too short (less than 5 chars)
        if ($imageInfo['alt_length'] < 5 && !$imageInfo['is_decorative']) {
            $issues[] = [
                'type' => 'short_alt',
                'message' => 'Alt text may be too short to be descriptive',
                'element' => $imageInfo['node_path'],
                'alt' => $imageInfo['alt']
            ];
        }

        // Check if alt text is too long (more than 125 chars)
        if ($imageInfo['alt_length'] > 125) {
            $issues[] = [
                'type' => 'long_alt',
                'message' => 'Alt text may be too long - consider using aria-describedby for longer descriptions',
                'element' => $imageInfo['node_path'],
                'alt' => $imageInfo['alt']
            ];
        }

        // Check for redundant alt text and title
        if ($imageInfo['alt'] === $imageInfo['title'] && $imageInfo['title'] !== '') {
            $issues[] = [
                'type' => 'redundant_text',
                'message' => 'Alt text and title attribute are identical',
                'element' => $imageInfo['node_path'],
                'text' => $imageInfo['alt']
            ];
        }

        // Check for likely placeholder alt text
        $placeholders = ['image', 'picture', 'photo', 'graphic'];
        if (in_array(strtolower($imageInfo['alt']), $placeholders)) {
            $issues[] = [
                'type' => 'placeholder_alt',
                'message' => 'Alt text appears to be a placeholder',
                'element' => $imageInfo['node_path'],
                'alt' => $imageInfo['alt']
            ];
        }
    }

    /**
     * Gets recommendations for improving alt text usage
     *
     * @return array List of recommendations for alt text improvements
     */
    private function getAltTextRecommendations(): array
    {
        return [
            'Use descriptive alt text that conveys the meaning and function of images',
            'Keep alt text concise (5-125 characters)',
            'Use empty alt="" for decorative images',
            'Avoid redundant information in alt text and title attributes',
            'Don\'t use generic terms like "image" or "photo" as alt text',
            'For complex images, use aria-describedby to provide longer descriptions',
            'Ensure alt text provides equivalent information to sighted users',
            'Include text from infographics and charts in the alt text',
            'For linked images, describe the destination in the alt text',
            'Test alt text by reading it without seeing the image'
        ];
    }

    /**
     * Gets color information for an element
     *
     * @param DOMElement $element The element to analyze
     * @return array Color information including foreground and background colors
     */
    private function getElementColorInfo(DOMElement $element): array
    {
        $styles = [
            'color' => null,
            'background-color' => null
        ];

        // Get computed styles if available
        if ($element->hasAttribute('style')) {
            $styleAttr = $element->getAttribute('style');
            foreach ($styles as $property => $value) {
                if (preg_match("/$property:\s*([^;]+)/i", $styleAttr, $matches)) {
                    $styles[$property] = trim($matches[1]);
                }
            }
        }

        // Get colors from class-based styles
        $class = $element->getAttribute('class');
        if ($class) {
            // Extract potential color classes
            if (preg_match('/(?:color|text)-([a-zA-Z0-9]+)/', $class, $matches)) {
                $styles['color'] = $matches[1];
            }
            if (preg_match('/(?:bg|background)-([a-zA-Z0-9]+)/', $class, $matches)) {
                $styles['background-color'] = $matches[1];
            }
        }

        return [
            'element_type' => $element->tagName,
            'node_path' => $this->getNodePath($element),
            'foreground' => $styles['color'],
            'background' => $styles['background-color'],
            'text_content' => $element->textContent,
            'class' => $class
        ];
    }
    /**
     * Gets the XPath-like node path for an element
     *
     * @param DOMElement $element The element to get the path for
     * @return string The node path
     */
    private function getNodePath(DOMElement $element): string
    {
        $path = '';
        $current = $element;

        while ($current !== null) {
            if ($current instanceof DOMElement) {
                $index = 1;
                $sibling = $current->previousSibling;
                
                while ($sibling !== null) {
                    if ($sibling instanceof DOMElement && $sibling->tagName === $current->tagName) {
                        $index++;
                    }
                    $sibling = $sibling->previousSibling;
                }

                $path = '/' . $current->tagName . '[' . $index . ']' . $path;
            }
            $current = $current->parentNode;
        }

        return $path ?: '/';
    }
    /**
     * Gets contrast recommendations based on color information
     *
     * @param array $colorInfo Color information from getElementColorInfo
     * @return array Recommendations for improving contrast if needed
     */
    private function getContrastRecommendations(array $colorInfo): array
    {
        $recommendations = [];

        // Check if we have both foreground and background colors
        if (!$colorInfo['foreground'] || !$colorInfo['background']) {
            $recommendations[] = 'Ensure explicit foreground and background colors are defined for this element';
            return $recommendations;
        }

        // Basic color combinations known to have poor contrast
        $poorContrastPairs = [
            ['white', 'yellow'],
            ['white', 'lime'],
            ['black', 'navy'],
            ['black', 'darkblue'],
            ['gray', 'white'],
            ['yellow', 'white']
        ];

        $fg = strtolower($colorInfo['foreground']);
        $bg = strtolower($colorInfo['background']);

        foreach ($poorContrastPairs as $pair) {
            if (($fg === $pair[0] && $bg === $pair[1]) || 
                ($fg === $pair[1] && $bg === $pair[0])) {
                $recommendations[] = "Consider changing the color combination of {$fg} on {$bg} to improve contrast";
            }
        }

        // Additional recommendations based on element type
        if ($colorInfo['element_type'] === 'a' && empty($recommendations)) {
            $recommendations[] = 'Ensure link colors have sufficient contrast with surrounding text';
        }

        return $recommendations;
    }

    /**
     * Gets interactive elements from the document using XPath
     *
     * @param \DOMXPath $xpath The XPath object to query with
     * @return \DOMNodeList List of interactive elements
     */
    private function getInteractiveElements(\DOMXPath $xpath): \DOMNodeList
    {
        // Query for common interactive elements
        $interactiveSelectors = [
            'a[href]',                    // Links with href
            'button',                     // Buttons
            'input:not([type="hidden"])', // Input fields (except hidden)
            'select',                     // Select dropdowns
            'textarea',                   // Text areas
            '*[onclick]',                 // Elements with onclick handlers
            '*[role="button"]',           // ARIA button roles
            '*[role="link"]',             // ARIA link roles
            '*[role="menuitem"]',         // Menu items
            '*[role="tab"]',              // Tabs
            '*[contenteditable="true"]',  // Editable elements
            '*[tabindex]'                 // Elements with tabindex
        ];

        // Combine all selectors with OR operator
        $xpathQuery = '//' . implode('|//', $interactiveSelectors);
        
        return $xpath->query($xpathQuery);
    }

    /**
     * Gets navigation information for an interactive element
     *
     * @param DOMElement $element The element to analyze
     * @return array Navigation information including tabindex, role, and event handlers
     */
    private function getElementNavigationInfo(DOMElement $element): array
    {
        $info = [
            'element_type' => $element->tagName,
            'tabindex' => $element->getAttribute('tabindex'),
            'role' => $element->getAttribute('role'),
            'has_onclick' => $element->hasAttribute('onclick'),
            'has_keydown' => $element->hasAttribute('onkeydown'),
            'has_keyup' => $element->hasAttribute('onkeyup'),
            'has_keypress' => $element->hasAttribute('onkeypress'),
            'is_disabled' => $element->hasAttribute('disabled'),
            'is_hidden' => $element->hasAttribute('hidden') || 
                          $element->getAttribute('aria-hidden') === 'true',
            'is_focusable' => $this->isFocusable($element)
        ];

        // Add specific info for links
        if ($element->tagName === 'a') {
            $info['has_href'] = $element->hasAttribute('href');
            $info['href_value'] = $element->getAttribute('href');
        }

        // Add specific info for form controls
        if (in_array($element->tagName, ['input', 'select', 'textarea'])) {
            $info['type'] = $element->getAttribute('type');
            $info['has_label'] = $this->hasAssociatedLabel($element);
        }

        return $info;
    }

    /**
     * Checks if an element is focusable
     */
    private function isFocusable(DOMElement $element): bool
    {
        $focusableElements = ['a', 'button', 'input', 'select', 'textarea'];
        
        return in_array($element->tagName, $focusableElements) ||
               $element->hasAttribute('tabindex') ||
               $element->getAttribute('contenteditable') === 'true';
    }

    /**
     * Checks if a form control has an associated label
     */
    private function hasAssociatedLabel(DOMElement $element): bool
    {
        $id = $element->getAttribute('id');
        if (!$id) {
            return false;
        }

        $xpath = new \DOMXPath($element->ownerDocument);
        $labels = $xpath->query("//label[@for='$id']");
        
        return $labels->length > 0;
    }

    /**
     * Checks for common navigation accessibility issues
     */
    private function checkNavigationIssues(DOMElement $element): array
    {
        $issues = [];

        // Check if navigation has a label
        if ($element->tagName === 'nav' && !$element->hasAttribute('aria-label') && !$element->hasAttribute('aria-labelledby')) {
            $issues[] = 'Navigation landmark should have a label via aria-label or aria-labelledby';
        }

        // Check for skip links at the start of navigation
        if ($element->tagName === 'nav') {
            $firstLink = $element->getElementsByTagName('a')->item(0);
            if (!$firstLink || strpos(strtolower($firstLink->textContent), 'skip') === false) {
                $issues[] = 'Navigation should include a skip link as the first item';
            }
        }

        // Check for proper heading structure in navigation
        if ($element->tagName === 'nav') {
            $headings = $element->getElementsByTagName('h1');
            if ($headings->length === 0) {
                $issues[] = 'Navigation should contain a heading for better structure';
            }
        }

        // Check for keyboard navigation support
        if ($element->tagName === 'nav') {
            $links = $element->getElementsByTagName('a');
            foreach ($links as $link) {
                if (!$this->isFocusable($link)) {
                    $issues[] = 'All navigation links should be keyboard accessible';
                    break;
                }
            }
        }

        return $issues;
    }

    /**
     * Get recommendations for improving keyboard navigation accessibility
     *
     * @return array List of keyboard navigation recommendations
     */
    private function getKeyboardNavigationRecommendations(): array
    {
        return [
            'Ensure all interactive elements are focusable and have visible focus indicators',
            'Add skip links at the beginning of navigation sections to bypass repetitive content',
            'Implement a logical tab order that follows the visual layout of the page',
            'Provide keyboard shortcuts for frequently used actions where appropriate',
            'Ensure dropdown menus and complex widgets are operable with keyboard only',
            'Add focus management for modal dialogs and dynamic content',
            'Include visible focus states that meet WCAG 2.1 contrast requirements',
            'Ensure custom interactive elements have appropriate ARIA roles and keyboard support'
        ];
    }

    /**
     * Get all focusable elements within a given element
     *
     * @param \DOMElement $element The element to check for focusable elements
     * @return array Array of focusable elements
     */
    private function getFocusableElements(\DOMElement $element): array
    {
        $focusableElements = [];
        
        // Common focusable elements
        $selectors = ['a', 'button', 'input', 'select', 'textarea', 'details'];
        
        foreach ($selectors as $selector) {
            $elements = $element->getElementsByTagName($selector);
            foreach ($elements as $el) {
                if ($this->isFocusable($el)) {
                    $focusableElements[] = $el;
                }
            }
        }

        // Get elements with tabindex
        $xpath = new \DOMXPath($element->ownerDocument);
        $elementsWithTabindex = $xpath->query('.//*[@tabindex]', $element);
        
        foreach ($elementsWithTabindex as $el) {
            if ($this->isFocusable($el)) {
                $focusableElements[] = $el;
            }
        }

        return $focusableElements;
    }
    /**
     * Check for potential focus management issues
     *
     * @param \DOMElement $element The element to check for focus issues
     * @return array List of focus-related issues found
     */
    private function checkFocusIssues(DOMElement $element): array
    {
        $issues = [];
        $focusableElements = $this->getFocusableElements($element);
        
        // Check for positive tabindex values
        foreach ($focusableElements as $el) {
            $tabindex = $el->getAttribute('tabindex');
            if ($tabindex !== '' && (int)$tabindex > 0) {
                $issues[] = "Positive tabindex value found which may disrupt natural tab order";
            }
        }

        // Check for potentially problematic focus traps
        $modals = $element->getElementsByTagName('dialog');
        foreach ($modals as $modal) {
            if (!$modal->hasAttribute('aria-modal')) {
                $issues[] = "Modal dialog missing aria-modal attribute for proper focus management";
            }
        }

        // Check for hidden focusable elements
        foreach ($focusableElements as $el) {
            if ($el->hasAttribute('aria-hidden') && $el->getAttribute('aria-hidden') === 'true') {
                if ($el->hasAttribute('tabindex') || in_array($el->tagName, ['a', 'button', 'input'])) {
                    $issues[] = "Focusable element found within aria-hidden content";
                }
            }
        }

        // Check for proper focus management in dynamic content
        $liveRegions = $xpath->query('.//*[@aria-live]', $element);
        if ($liveRegions->length > 0 && count($focusableElements) === 0) {
            $issues[] = "Live region detected without focusable elements for keyboard navigation";
        }

        return $issues;
    }

    /**
     * Get recommendations for improving focus management
     *
     * @return array List of recommendations for better focus management
     */
    private function getFocusManagementRecommendations(): array
    {
        return [
            'Ensure a logical tab order by avoiding positive tabindex values',
            'Use aria-modal="true" on dialog elements to properly manage focus',
            'Remove focusable elements from aria-hidden content',
            'Provide keyboard focus management for dynamic content and live regions',
            'Maintain visible focus indicators for all interactive elements',
            'Implement focus traps for modal dialogs and other overlay content',
            'Restore focus to trigger elements when closing modals or popups',
            'Use skip links to bypass repetitive content blocks',
            'Ensure custom widgets are keyboard accessible with appropriate ARIA attributes'
        ];
    }


    /**
     * Analyzes form elements for accessibility issues
     *
     * @param DOMDocument $dom The DOM document to analyze
     * @param array $formControls Array to store form control information
     * @param array $issues Array to store identified issues
     */
   
    private function analyzeFormElements(DOMDocument $dom, array &$formControls, array &$issues): void
    {
        $xpath = new \DOMXPath($dom);
        $controls = $xpath->query('//input|//select|//textarea');

        foreach ($controls as $control) {
            $controlInfo = [
                'id' => $control->getAttribute('id'),
                'action' => $control->getAttribute('action'),
                'method' => strtoupper($control->getAttribute('method') ?: 'GET'),
                'has_csrf' => $this->checkCsrfProtection($control, $xpath),
                'has_https' => $this->checkHttpsAction($control),
                'has_validation' => $this->checkFormValidation($control, $xpath),
                'issues' => $this->collectFormIssues($control, $xpath),
                'type' => $control->tagName === 'input' ? $control->getAttribute('type') : $control->tagName,
                'id' => $control->getAttribute('id'),
                'name' => $control->getAttribute('name'),
                'has_label' => false,
                'has_aria_label' => false
            ];

            // Check for explicit label
            if ($controlInfo['id']) {
                $label = $xpath->query("//label[@for='" . $controlInfo['id'] . "']")->item(0);
                if ($label) {
                    $controlInfo['has_label'] = true;
                }
            }

            // Check for implicit label
            $parentLabel = $xpath->query("ancestor::label", $control)->item(0);
            if ($parentLabel) {
                $controlInfo['has_label'] = true;
            }

            // Check for ARIA labeling
            if ($control->hasAttribute('aria-label') || $control->hasAttribute('aria-labelledby')) {
                $controlInfo['has_aria_label'] = true;
            }

            // Record issues if no labeling is found
            if (!$controlInfo['has_label'] && !$controlInfo['has_aria_label']) {
                $issues[] = [
                    'type' => 'missing_label',
                    'element' => $control->getNodePath(),
                    'message' => "Form control missing accessible label"
                ];
            }

            // Check for placeholder-only labeling
            if (!$controlInfo['has_label'] && !$controlInfo['has_aria_label'] && 
                $control->hasAttribute('placeholder')) {
                $issues[] = [
                    'type' => 'placeholder_label',
                    'element' => $control->getNodePath(),
                    'message' => "Form control using placeholder as only label"
                ];
            }

            $formControls[] = $controlInfo;
        }
    }

    /**
     * Get recommendations for form labeling
     *
     * @return array List of recommendations for form accessibility
     */
    private function getFormLabelRecommendations(): array
    {
        return [
            'Use explicit labels with for attributes matching input IDs',
            'Avoid using placeholders as the only form of labeling',
            'Ensure all form controls have associated labels or ARIA labels',
            'Group related form controls using fieldset and legend elements',
            'Provide clear error messages and validation feedback',
            'Use aria-required for required fields instead of just the required attribute',
            'Ensure error messages are associated with inputs using aria-describedby',
            'Consider using aria-invalid for fields with validation errors'
        ];
    }

    private function checkCsrfProtection(DOMElement $form, DOMXPath $xpath): bool
    {
        $csrfTokens = $xpath->query('.//input[@name="_token" or contains(@name, "csrf")]', $form);
        return $csrfTokens->length > 0;
    }


    private function checkHttpsAction(DOMElement $form): bool
    {
        $action = $form->getAttribute('action');
        return !$action || strpos(strtolower($action), 'https://') === 0;
    }

        private function checkFormValidation(DOMElement $form, DOMXPath $xpath): bool
    {
        $requiredInputs = $xpath->query('.//input[@required]|.//select[@required]|.//textarea[@required]', $form);
        return $requiredInputs->length > 0;
    }

      private function collectFormIssues(DOMElement $form, DOMXPath $xpath): array
    {
        $issues = [];

        // Check CSRF
        if (!$this->checkCsrfProtection($form, $xpath)) {
            $issues[] = [
                'type' => 'missing_csrf',
                'severity' => 'high',
                'recommendation' => 'Add CSRF token protection to the form'
            ];
        }

        // Check HTTPS
        if (!$this->checkHttpsAction($form)) {
            $issues[] = [
                'type' => 'insecure_form_action',
                'severity' => 'high',
                'recommendation' => 'Use HTTPS for form submission'
            ];
        }

        // Check sensitive data handling
        $sensitiveInputs = $xpath->query('.//input[@type="password" or @type="email" or @type="tel"]', $form);
        if ($sensitiveInputs->length > 0 && strtoupper($form->getAttribute('method')) !== 'POST') {
            $issues[] = [
                'type' => 'sensitive_data_get',
                'severity' => 'high',
                'recommendation' => 'Use POST method for forms with sensitive data'
            ];
        }

        return $issues;
    }
}
