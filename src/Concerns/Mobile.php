<?php

namespace Shaferllc\Analytics\Concerns;

use DOMNode;
use DOMXPath;
use DOMElement;
use DOMDocument;

class Mobile
{
    public function analyzeMobileSEO(DOMDocument $domDocument): array
    {
        $xpath = new DOMXPath($domDocument);
        $issues = [];
        $details = [];

        // Check viewport meta tag
        $viewport = $xpath->query('//meta[@name="viewport"]')->item(0);
        if (!$viewport) {
            $issues[] = [
                'type' => 'missing_viewport',
                'recommendation' => 'Add viewport meta tag for mobile responsiveness'
            ];
            $details[] = 'Missing viewport meta tag';
        } else {
            $content = $viewport instanceof DOMElement ? $viewport->getAttribute('content') : '';
            if (!str_contains($content, 'width=device-width')) {
                $issues[] = [
                    'type' => 'invalid_viewport',
                    'recommendation' => 'Viewport should include width=device-width'
                ];
                $details[] = 'Viewport meta tag should include width=device-width';
            }
        }

        // Check for mobile-friendly elements
        $touchTargets = $xpath->query('//a|//button|//input[@type="submit"]|//input[@type="button"]');
        foreach ($touchTargets as $target) {
            if (!$target instanceof DOMElement) {
                continue;
            }
            
            $style = $target->getAttribute('style');
            if (preg_match('/width:\s*(\d+)px/', $style, $matches)) {
                if ((int)$matches[1] < 44) {
                    $issues[] = [
                        'type' => 'small_touch_target',
                        'element' => $target->nodeName,
                        'recommendation' => 'Ensure touch targets are at least 44px wide'
                    ];
                    $details[] = 'Touch target found with width less than 44px';
                }
            }
        }

        // Check for mobile-unfriendly elements
        $unfriendlyElements = $xpath->query('//frame|//frameset|//object|//embed|//applet');
        if ($unfriendlyElements->length > 0) {
            $issues[] = [
                'type' => 'mobile_unfriendly_elements',
                'recommendation' => 'Remove elements not supported on mobile devices'
            ];
            $details[] = 'Found elements that are not mobile-friendly';
        }

        // Check text size
        $textElements = $xpath->query('//*[text()]');
        foreach ($textElements as $element) {
            if (!$element instanceof DOMElement) {
                continue;
            }
            
            $style = $element->getAttribute('style');
            if (preg_match('/font-size:\s*(\d+)px/', $style, $matches)) {
                if ((int)$matches[1] < 16) {
                    $issues[] = [
                        'type' => 'small_text',
                        'recommendation' => 'Use font size of at least 16px for readability'
                    ];
                    $details[] = 'Text elements found with font size smaller than 16px';
                    break;
                }
            }
        }

        $details = array_merge($details, [
            'Ensure viewport meta tag is properly configured',
            'Make touch targets at least 44px wide',
            'Avoid mobile-unfriendly elements',
            'Use readable font sizes (16px minimum)',
            'Test site on various mobile devices'
        ]);

        return [
            'passed' => count($issues) === 0,
            'importance' => 'high',
            'issues' => $issues,
            'details' => $details
        ];
    }

    public function analyzeMobileFriendly(DOMDocument $domDocument): array
    {
        $issues = [];
        $score = 100;

        // Check viewport meta tag
        $hasViewport = false;
        $metas = $domDocument->getElementsByTagName('meta');
        foreach ($metas as $meta) {
            if (!$meta instanceof DOMElement) {
                continue;
            }
            
            if ($meta->getAttribute('name') === 'viewport') {
                $hasViewport = true;
                $content = $meta->getAttribute('content');
                if (!str_contains($content, 'width=device-width')) {
                    $score -= 20;
                    $issues[] = [
                        'type' => 'viewport_not_responsive',
                        'severity' => 'high',
                        'message' => 'Viewport meta tag should include width=device-width'
                    ];
                }
                break;
            }
        }

        if (!$hasViewport) {
            $score -= 30;
            $issues[] = [
                'type' => 'missing_viewport',
                'severity' => 'high',
                'message' => 'Missing viewport meta tag'
            ];
        }

        // Check for fixed-width elements
        $elements = $domDocument->getElementsByTagName('*');
        foreach ($elements as $element) {
            if (!$element instanceof DOMElement) {
                continue;
            }
            
            $style = $element->getAttribute('style');
            if (preg_match('/width:\s*\d+px/i', $style)) {
                $score -= 10;
                $issues[] = [
                    'type' => 'fixed_width',
                    'severity' => 'medium',
                    'message' => 'Found fixed-width element that may break mobile layout',
                    'element' => $element->getNodePath()
                ];
            }
        }

        // Check images for responsive attributes
        $images = $domDocument->getElementsByTagName('img');
        foreach ($images as $img) {
            if (!$img instanceof DOMElement) {
                continue;
            }
            
            if (!$img->hasAttribute('srcset') && !$img->hasAttribute('sizes')) {
                $score -= 5;
                $issues[] = [
                    'type' => 'non_responsive_image',
                    'severity' => 'low',
                    'message' => 'Image lacks responsive attributes (srcset/sizes)',
                    'element' => $img->getNodePath()
                ];
            }
        }

        return [
            'is_mobile_friendly' => $score >= 70,
            'score' => $score,
            'issues' => $issues,
            'recommendations' => [
                'Add viewport meta tag with width=device-width',
                'Use responsive images with srcset and sizes attributes',
                'Avoid fixed-width elements',
                'Ensure touch targets are at least 44x44px',
                'Use relative units (rem, em, %) instead of fixed pixels'
            ]
        ];
    }

    public function analyzeMobileOptimization(DOMDocument $domDocument): array
    {
        $issues = [];
        $score = 100;
        $xpath = new DOMXPath($domDocument);

        // Check for touch target sizes
        $clickableElements = $xpath->query('//a|//button|//input[@type="submit"]|//input[@type="button"]|//select');
        foreach ($clickableElements as $element) {
            if (!$element instanceof DOMElement) {
                continue;
            }
            
            $style = $element->getAttribute('style');
            if (preg_match('/(?:width|height):\s*(\d+)px/i', $style, $matches)) {
                if ((int)$matches[1] < 44) {
                    $score -= 5;
                    $issues[] = [
                        'type' => 'small_touch_target',
                        'severity' => 'medium',
                        'message' => 'Touch target is smaller than recommended 44x44px',
                        'element' => $element->getNodePath()
                    ];
                }
            }
        }

        // Check for font size readability
        $textElements = $xpath->query('//*[text()]');
        foreach ($textElements as $element) {
            if (!$element instanceof DOMElement) {
                continue;
            }
            
            $style = $element->getAttribute('style');
            if (preg_match('/font-size:\s*(\d+)px/i', $style, $matches)) {
                if ((int)$matches[1] < 16) {
                    $score -= 5;
                    $issues[] = [
                        'type' => 'small_font_size',
                        'severity' => 'medium',
                        'message' => 'Font size smaller than recommended 16px',
                        'element' => $element->getNodePath()
                    ];
                }
            }
        }

        // Check for horizontal scrolling issues
        $elements = $domDocument->getElementsByTagName('*');
        foreach ($elements as $element) {
            if (!$element instanceof DOMElement) {
                continue;
            }
            
            $style = $element->getAttribute('style');
            if (strpos($style, 'overflow-x: auto') !== false || strpos($style, 'overflow-x: scroll') !== false) {
                $score -= 10;
                $issues[] = [
                    'type' => 'horizontal_scroll',
                    'severity' => 'high',
                    'message' => 'Element may cause horizontal scrolling on mobile',
                    'element' => $element->getNodePath()
                ];
            }
        }

        // Check for mobile-specific meta tags
        $metaTags = $xpath->query('//meta[@name="format-detection"] | //meta[@name="theme-color"] | //meta[@name="apple-mobile-web-app-capable"]');
        if ($metaTags->length === 0) {
            $score -= 5;
            $issues[] = [
                'type' => 'missing_mobile_meta',
                'severity' => 'low',
                'message' => 'Missing mobile-specific meta tags'
            ];
        }

        return [
            'optimization_score' => $score,
            'is_well_optimized' => $score >= 80,
            'issues' => $issues,
            'recommendations' => [
                'Ensure touch targets are at least 44x44px',
                'Use minimum 16px font size for readability',
                'Avoid horizontal scrolling',
                'Add mobile-specific meta tags',
                'Test on various mobile devices and screen sizes',
                'Consider implementing AMP or mobile-first design'
            ]
        ];
    }

    /**
     * Analyzes viewport meta tag settings in the HTML document
     */
    public function analyzeViewportSettings(DOMDocument $domDocument): array
    {
        $viewportMeta = $domDocument->getElementsByTagName('meta');
        $viewportSettings = [];
        
        foreach ($viewportMeta as $meta) {
            if (strtolower($meta->getAttribute('name')) === 'viewport') {
                $content = $meta->getAttribute('content');
                $properties = explode(',', $content);
                
                foreach ($properties as $property) {
                    $parts = explode('=', trim($property));
                    if (count($parts) === 2) {
                        $viewportSettings[trim($parts[0])] = trim($parts[1]);
                    }
                }
                
                return [
                    'has_viewport_meta' => true,
                    'settings' => $viewportSettings,
                    'is_responsive' => isset($viewportSettings['width']) && $viewportSettings['width'] === 'device-width'
                ];
            }
        }
        
        return [
            'has_viewport_meta' => false,
            'settings' => [],
            'is_responsive' => false
        ];
    }

    /**
     * Analyzes touch-friendly elements and their spacing in the HTML document
     */
    public function analyzeTouchElements(DOMDocument $domDocument): array
    {
        $touchElements = [];
        $issues = [];

        // Elements that are commonly interacted with via touch
        $interactiveElements = [
            'a', 'button', 'input', 'select', 'textarea'
        ];

        foreach ($interactiveElements as $tagName) {
            $elements = $domDocument->getElementsByTagName($tagName);
            
            foreach ($elements as $element) {
                $width = $element->getAttribute('width');
                $height = $element->getAttribute('height');
                $style = $element->getAttribute('style');
                
                // Extract dimensions from inline styles if present
                if (preg_match('/width:\s*(\d+)px/', $style, $widthMatch)) {
                    $width = $widthMatch[1];
                }
                if (preg_match('/height:\s*(\d+)px/', $style, $heightMatch)) {
                    $height = $heightMatch[1];
                }

                // Check for minimum touch target size (44x44px recommended)
                if (($width && $width < 44) || ($height && $height < 44)) {
                    $issues[] = [
                        'element' => $tagName,
                        'path' => $element->getNodePath(),
                        'issue' => 'Element size below recommended 44x44px minimum',
                        'dimensions' => [
                            'width' => $width ?: 'unknown',
                            'height' => $height ?: 'unknown'
                        ]
                    ];
                }

                $touchElements[] = [
                    'type' => $tagName,
                    'path' => $element->getNodePath(),
                    'dimensions' => [
                        'width' => $width ?: 'unknown',
                        'height' => $height ?: 'unknown'
                    ],
                    'has_hover_state' => (bool)preg_match('/:hover/', $style)
                ];
            }
        }

        return [
            'elements' => $touchElements,
            'issues' => $issues,
            'total_interactive_elements' => count($touchElements),
            'elements_with_issues' => count($issues),
            'recommendations' => [
                'Ensure touch targets are at least 44x44px',
                'Maintain adequate spacing between interactive elements',
                'Avoid hover-dependent interactions'
            ]
        ];
    }

    public function analyzeTapTargets(DOMDocument $domDocument): array
    {
        $tapTargets = [];
        $issues = [];
        $minSize = 48; // Minimum recommended tap target size in pixels

        // Get all clickable elements
        $xpath = new DOMXPath($domDocument);
        $clickableElements = $xpath->query('//a|//button|//input[@type="submit"]|//input[@type="button"]|//select|//label');

        foreach ($clickableElements as $element) {
            // Get element dimensions from width/height attributes or style
            $width = $element->getAttribute('width');
            $height = $element->getAttribute('height');
            
            // Check inline styles
            $style = $element->getAttribute('style');
            if (preg_match('/width:\s*([\d.]+)px/', $style, $wMatches)) {
                $width = $wMatches[1];
            }
            if (preg_match('/height:\s*([\d.]+)px/', $style, $hMatches)) {
                $height = $hMatches[1];
            }

            // Convert to numeric values
            $width = is_numeric($width) ? floatval($width) : 0;
            $height = is_numeric($height) ? floatval($height) : 0;

            $tapTarget = [
                'element' => $element->tagName,
                'path' => $element->getNodePath(),
                'text' => substr(trim($element->textContent), 0, 50),
                'width' => $width,
                'height' => $height
            ];

            $tapTargets[] = $tapTarget;

            // Check if target size is too small
            if (($width > 0 && $width < $minSize) || ($height > 0 && $height < $minSize)) {
                $issues[] = array_merge($tapTarget, [
                    'issue' => 'Target size too small',
                    'recommended_size' => "{$minSize}px"
                ]);
            }
        }

        return [
            'tap_targets' => $tapTargets,
            'issues' => $issues,
            'total_clickable_elements' => count($clickableElements),
            'elements_with_issues' => count($issues),
            'recommendations' => [
                'Ensure tap targets are at least 48x48 pixels',
                'Add padding to increase tap target size if needed',
                'Maintain adequate spacing between clickable elements',
                'Consider touch-friendly design patterns'
            ]
        ];
    }

    public function analyzeFontSizes(DOMDocument $domDocument): array
    {
        $fontSizes = [];
        $issues = [];
        $minFontSize = 16; // Minimum recommended font size in pixels

        // Get all text-containing elements
        $xpath = new DOMXPath($domDocument);
        $textElements = $xpath->query('//*[not(self::script)][not(self::style)][text()[normalize-space()]]');

        foreach ($textElements as $element) {
            $computedStyle = $element->getAttribute('style');
            $fontSize = null;

            // Extract font size from inline styles
            if (preg_match('/font-size:\s*([\d.]+)(px|rem|em|pt)/', $computedStyle, $matches)) {
                $size = $matches[1];
                $unit = $matches[2];

                // Convert common units to pixels (approximate)
                switch ($unit) {
                    case 'rem':
                    case 'em':
                        $fontSize = $size * 16;
                        break;
                    case 'pt':
                        $fontSize = $size * 1.333333;
                        break;
                    default:
                        $fontSize = $size;
                }

                if ($fontSize < $minFontSize) {
                    $issues[] = [
                        'element' => $element->tagName,
                        'path' => $element->getNodePath(),
                        'text' => substr(trim($element->textContent), 0, 50),
                        'font_size' => "{$size}{$unit}",
                        'computed_px' => $fontSize
                    ];
                }

                $fontSizes[] = [
                    'element' => $element->tagName,
                    'path' => $element->getNodePath(),
                    'size' => "{$size}{$unit}",
                    'computed_px' => $fontSize
                ];
            }
        }

        return [
            'font_sizes' => $fontSizes,
            'issues' => $issues,
            'total_text_elements' => count($textElements),
            'elements_with_small_text' => count($issues),
            'recommendations' => [
                'Use minimum 16px font size for body text',
                'Ensure sufficient contrast between text and background',
                'Consider using relative units (rem/em) for better scaling',
                'Implement proper text hierarchy with different sizes'
            ]
        ];
    }

    public function checkMobileFriendly(DOMDocument $domDocument): array
    {
        $issues = [];
        $recommendations = [];

        // Check viewport meta tag
        $viewportMeta = null;
        $metas = $domDocument->getElementsByTagName('meta');
        foreach ($metas as $meta) {
            if (strtolower($meta->getAttribute('name')) === 'viewport') {
                $viewportMeta = $meta;
                break;
            }
        }

        if (!$viewportMeta) {
            $issues[] = [
                'type' => 'missing_viewport',
                'description' => 'No viewport meta tag found',
                'impact' => 'high'
            ];
            $recommendations[] = 'Add viewport meta tag with content="width=device-width, initial-scale=1"';
        } else {
            $content = $viewportMeta->getAttribute('content');
            if (!str_contains($content, 'width=device-width')) {
                $issues[] = [
                    'type' => 'incorrect_viewport',
                    'description' => 'Viewport meta tag missing width=device-width',
                    'impact' => 'high'
                ];
            }
            if (!str_contains($content, 'initial-scale=1')) {
                $issues[] = [
                    'type' => 'missing_scale',
                    'description' => 'Viewport meta tag missing initial-scale',
                    'impact' => 'medium'
                ];
            }
        }

        // Check for fixed width elements
        $elements = $domDocument->getElementsByTagName('*');
        foreach ($elements as $element) {
            $style = $element->getAttribute('style');
            $width = $element->getAttribute('width');
            
            if (preg_match('/width:\s*(\d+)px/', $style, $matches)) {
                if ((int)$matches[1] > 480) {
                    $issues[] = [
                        'type' => 'fixed_width',
                        'element' => $element->getNodePath(),
                        'width' => $matches[1],
                        'description' => 'Element has fixed width that may cause horizontal scrolling',
                        'impact' => 'medium'
                    ];
                }
            }
            
            if (is_numeric($width) && (int)$width > 480) {
                $issues[] = [
                    'type' => 'fixed_width_attribute',
                    'element' => $element->getNodePath(),
                    'width' => $width,
                    'description' => 'Element has fixed width attribute that may cause horizontal scrolling',
                    'impact' => 'medium'
                ];
            }
        }

        return [
            'is_mobile_friendly' => count($issues) === 0,
            'issues' => $issues,
            'recommendations' => array_merge($recommendations, [
                'Use relative units (%, em, rem) instead of fixed pixel widths',
                'Ensure content scales properly on different screen sizes',
                'Test on various mobile devices and screen sizes'
            ])
        ];
    }
}