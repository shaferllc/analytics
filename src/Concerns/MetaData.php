<?php

namespace Shaferllc\Analytics\Concerns;

use DOMDocument;
use DOMElement;

class MetaData
{
    /**
     * Extract Open Graph meta tags from the document
     */
    public function extractOpenGraphTags(DOMDocument $domDocument): array
    {
        $ogTags = [];
        $metas = $domDocument->getElementsByTagName('meta');

        /** @var DOMElement $meta */
        foreach ($metas as $meta) {
            $property = $meta->getAttribute('property');
            if (str_starts_with($property, 'og:')) {
                $content = $meta->getAttribute('content');
                $key = substr($property, 3); // Remove 'og:' prefix

                $ogTags[$key] = [
                    'content' => $content,
                    'path' => $meta->getNodePath()
                ];
            }
        }

        return [
            'count' => count($ogTags),
            'tags' => $ogTags
        ];
    }

    /**
     * Extract Twitter Card meta tags from the document
     */
    public function extractTwitterCards(DOMDocument $domDocument): array
    {
        $twitterTags = [];
        $metas = $domDocument->getElementsByTagName('meta');

        /** @var DOMElement $meta */
        foreach ($metas as $meta) {
            $name = $meta->getAttribute('name');
            if (str_starts_with($name, 'twitter:')) {
                $content = $meta->getAttribute('content');
                $key = substr($name, 8); // Remove 'twitter:' prefix

                $twitterTags[$key] = [
                    'content' => $content,
                    'path' => $meta->getNodePath()
                ];
            }
        }

        return [
            'count' => count($twitterTags),
            'tags' => $twitterTags
        ];
    }

    /**
     * Extract Schema.org markup from the document
     */
    public function extractSchemaMarkup(DOMDocument $domDocument): array
    {
        $schemaData = [];
        $elements = $domDocument->getElementsByTagName('*');

        /** @var DOMElement $element */
        foreach ($elements as $element) {
            if ($element->hasAttribute('itemtype')) {
                $type = $element->getAttribute('itemtype');
                $properties = [];

                // Get all descendant elements with itemprop
                $itemProps = $element->getElementsByTagName('*');
                /** @var DOMElement $prop */
                foreach ($itemProps as $prop) {
                    if ($prop->hasAttribute('itemprop')) {
                        $propName = $prop->getAttribute('itemprop');
                        $propValue = $this->extractItemPropValue($prop);

                        if ($propValue) {
                            $properties[$propName] = [
                                'content' => $propValue,
                                'path' => $prop->getNodePath()
                            ];
                        }
                    }
                }

                if (!empty($properties)) {
                    $schemaData[] = [
                        'type' => $type,
                        'properties' => $properties,
                        'path' => $element->getNodePath()
                    ];
                }
            }
        }

        return [
            'count' => count($schemaData),
            'items' => $schemaData
        ];
    }

    /**
     * Extract value from an itemprop element based on its type
     */
    private function extractItemPropValue(DOMElement $element): ?string
    {
        if ($element->hasAttribute('content')) {
            return $element->getAttribute('content');
        }

        if ($element->tagName === 'meta') {
            return null;
        }

        if ($element->tagName === 'img') {
            return $element->getAttribute('src');
        }

        if ($element->tagName === 'a') {
            return $element->getAttribute('href');
        }

        if ($element->tagName === 'time') {
            return $element->getAttribute('datetime') ?: $element->textContent;
        }

        return trim($element->textContent);
    }

    /**
     * Detect social media widgets and integration elements in the document
     */
    public function detectSocialWidgets(DOMDocument $domDocument): array
    {
        $widgets = [];
        $patterns = $this->getSocialWidgetPatterns();

        $elements = $domDocument->getElementsByTagName('*');

        /** @var DOMElement $element */
        foreach ($elements as $element) {
            foreach ($patterns as $platform => $selectors) {
                if ($this->checkSocialWidgetElement($element, $platform, $selectors, $widgets)) {
                    continue;
                }
            }
        }

        return [
            'count' => count($widgets),
            'widgets' => $widgets,
            'platforms' => array_count_values(array_column($widgets, 'platform'))
        ];
    }

    /**
     * Get patterns for detecting social media widgets
     */
    private function getSocialWidgetPatterns(): array
    {
        return [
            'facebook' => [
                'class' => ['fb-', 'facebook-'],
                'src' => ['facebook.com', 'connect.facebook.net'],
                'data' => ['data-href="facebook.com']
            ],
            'twitter' => [
                'class' => ['twitter-', 'tweet-'],
                'src' => ['platform.twitter.com'],
                'data' => ['data-twitter']
            ],
            'instagram' => [
                'class' => ['instagram-', 'insta-'],
                'src' => ['instagram.com/embed'],
                'data' => ['data-instgrm']
            ],
            'linkedin' => [
                'class' => ['linkedin-'],
                'src' => ['platform.linkedin.com'],
                'data' => ['data-linkedin']
            ],
            'pinterest' => [
                'class' => ['pinterest-', 'pin-it-'],
                'src' => ['assets.pinterest.com'],
                'data' => ['data-pin']
            ]
        ];
    }

    /**
     * Check if element matches social widget patterns
     */
    private function checkSocialWidgetElement(DOMElement $element, string $platform, array $selectors, array &$widgets): bool
    {
        // Check classes
        $class = $element->getAttribute('class');
        foreach ($selectors['class'] as $prefix) {
            if (strpos($class, $prefix) !== false) {
                $widgets[] = [
                    'platform' => $platform,
                    'type' => 'class',
                    'element' => $element->tagName,
                    'path' => $element->getNodePath()
                ];
                return true;
            }
        }

        // Check src attributes
        $src = $element->getAttribute('src');
        foreach ($selectors['src'] as $domain) {
            if (strpos($src, $domain) !== false) {
                $widgets[] = [
                    'platform' => $platform,
                    'type' => 'embed',
                    'element' => $element->tagName,
                    'path' => $element->getNodePath()
                ];
                return true;
            }
        }

        // Check data attributes
        foreach ($selectors['data'] as $data) {
            foreach ($element->attributes as $attr) {
                if (
                    strpos($attr->nodeName, 'data-') === 0 &&
                    strpos($attr->nodeValue, $data) !== false
                ) {
                    $widgets[] = [
                        'platform' => $platform,
                        'type' => 'data-attribute',
                        'element' => $element->tagName,
                        'path' => $element->getNodePath()
                    ];
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Detects cookie usage in the document by analyzing scripts and consent elements
     */
    public function detectCookieUsage(DOMDocument $domDocument): array
    {
        $cookieElements = [];
        $scripts = $domDocument->getElementsByTagName('script');

        // Check for common cookie-related JavaScript
        foreach ($scripts as $script) {
            $content = $script->textContent;
            if (preg_match('/(document\.cookie|setCookie|getCookie)/i', $content)) {
                $cookieElements[] = [
                    'type' => 'script',
                    'content' => substr($content, 0, 100) . '...',
                    'path' => $script->getNodePath()
                ];
            }
        }

        // Check for cookie consent elements
        $elements = $domDocument->getElementsByTagName('*');
        $consentKeywords = ['cookie', 'consent', 'gdpr', 'ccpa'];

        foreach ($elements as $element) {
            if (!$element instanceof DOMElement) {
                continue;
            }

            $text = strtolower($element->textContent);
            $id = strtolower($element->getAttribute('id'));
            $class = strtolower($element->getAttribute('class'));

            foreach ($consentKeywords as $keyword) {
                if (
                    strpos($text, $keyword) !== false ||
                    strpos($id, $keyword) !== false ||
                    strpos($class, $keyword) !== false
                ) {
                    $cookieElements[] = [
                        'type' => 'consent_element',
                        'element' => $element->nodeName,
                        'identifier' => $id ?: $class,
                        'path' => $element->getNodePath()
                    ];
                    break;
                }
            }
        }

        return [
            'has_cookie_usage' => !empty($cookieElements),
            'elements' => $cookieElements,
            'count' => count($cookieElements)
        ];
    }

    /**
     * Detects tracking scripts by analyzing script sources and content
     */
    public function detectTrackingScripts(DOMDocument $domDocument): array
    {
        $trackingScripts = [];
        $scripts = $domDocument->getElementsByTagName('script');

        // Common tracking script patterns by category
        $trackingPatterns = [
            'analytics' => [
                'google-analytics.com',
                'analytics.js',
                'gtag',
                'ga\(',
                '_gaq'
            ],
            'advertising' => [
                'doubleclick.net',
                'googlesyndication.com',
                'adnxs.com',
                'facebook.com/tr',
                'ads.js'
            ],
            'social' => [
                'platform.twitter.com',
                'connect.facebook.net',
                'linkedin.com/analytics',
                'pinterest.com/pinit'
            ],
            'metrics' => [
                'hotjar.com',
                'mouseflow.com',
                'clarity.ms',
                'mixpanel'
            ]
        ];

        foreach ($scripts as $script) {
            /** @var DOMElement $script */
            $src = $script->getAttribute('src');
            $content = $script->textContent;

            foreach ($trackingPatterns as $category => $patterns) {
                foreach ($patterns as $pattern) {
                    if (
                        (!empty($src) && stripos($src, $pattern) !== false) ||
                        (!empty($content) && preg_match('/' . preg_quote($pattern, '/') . '/i', $content))
                    ) {
                        $trackingScripts[] = [
                            'type' => $category,
                            'source' => $src ?: 'inline',
                            'pattern_matched' => $pattern,
                            'path' => $script->getNodePath()
                        ];
                        break 2;
                    }
                }
            }
        }

        return [
            'has_tracking' => !empty($trackingScripts),
            'scripts' => $trackingScripts,
            'count' => count($trackingScripts),
            'types' => array_count_values(array_column($trackingScripts, 'type'))
        ];
    }

    /**
     * Finds GDPR-related elements in the document
     */
    public function findGDPRElements(DOMDocument $domDocument): array
    {
        $gdprElements = [];
        $elements = $domDocument->getElementsByTagName('*');

        $keywords = [
            'gdpr',
            'privacy',
            'cookie',
            'consent',
            'data protection',
            'personal data',
            'opt-out',
            'opt-in',
            'cookie policy',
            'privacy policy',
            'data processing'
        ];

        foreach ($elements as $element) {
            if (!$element instanceof DOMElement) {
                continue;
            }

            $text = strtolower($element->textContent);
            $id = strtolower($element->getAttribute('id'));
            $class = strtolower($element->getAttribute('class'));

            foreach ($keywords as $keyword) {
                if (
                    strpos($text, $keyword) !== false ||
                    strpos($id, $keyword) !== false ||
                    strpos($class, $keyword) !== false
                ) {

                    $gdprElements[] = [
                        'element' => $element->nodeName,
                        'type' => $this->determineGDPRElementType($element, $keyword),
                        'keyword_matched' => $keyword,
                        'text' => substr($text, 0, 100) . (strlen($text) > 100 ? '...' : ''),
                        'path' => $element->getNodePath()
                    ];
                    break;
                }
            }
        }

        return [
            'elements' => $gdprElements,
            'count' => count($gdprElements),
            'types' => array_count_values(array_column($gdprElements, 'type'))
        ];
    }

    /**
     * Determines the type of a GDPR-related element based on its content and tag
     */
    private function determineGDPRElementType(DOMElement $element, string $keyword): string
    {
        $text = strtolower($element->textContent);
        $tagName = strtolower($element->nodeName);

        if ($tagName === 'button' || $tagName === 'a') {
            return 'control';
        }

        if ($tagName === 'form') {
            return 'form';
        }

        if (strpos($text, 'accept') !== false || strpos($text, 'agree') !== false) {
            return 'consent';
        }

        if (strpos($text, 'policy') !== false || strpos($text, 'notice') !== false) {
            return 'policy';
        }

        if ($keyword === 'cookie' || strpos($text, 'cookie') !== false) {
            return 'cookie_notice';
        }

        return 'informational';
    }

    /**
     * Finds privacy policy links in multiple languages
     */
    public function findPrivacyPolicyLinks(DOMDocument $domDocument): array
    {
        $policyLinks = [];
        $elements = $domDocument->getElementsByTagName('a');

        $keywords = [
            'privacy policy',
            'privacy notice',
            'privacy statement',
            'data policy',
            'data protection',
            'privacy',
            'datenschutz', // German
            'confidentialité', // French 
            'privacidad' // Spanish
        ];

        foreach ($elements as $element) {
            if (!$element instanceof DOMElement) {
                continue;
            }

            $text = strtolower($element->textContent);
            $href = $element->getAttribute('href');

            foreach ($keywords as $keyword) {
                if (
                    strpos($text, $keyword) !== false ||
                    strpos(strtolower($href), str_replace(' ', '', $keyword)) !== false
                ) {

                    $policyLinks[] = [
                        'text' => trim($element->textContent),
                        'href' => $href,
                        'keyword_matched' => $keyword,
                        'path' => $element->getNodePath(),
                        'location' => $this->determineLinkLocation($element)
                    ];
                    break;
                }
            }
        }

        return [
            'has_policy_links' => !empty($policyLinks),
            'links' => $policyLinks,
            'count' => count($policyLinks),
            'locations' => array_count_values(array_column($policyLinks, 'location'))
        ];
    }

    /**
     * Determines the location of a link in the document structure
     */
    private function determineLinkLocation(DOMElement $element): string
    {
        $parent = $element->parentNode;
        while ($parent && $parent->nodeType === XML_ELEMENT_NODE) {
            $tagName = strtolower($parent->nodeName);

            if ($tagName === 'header') {
                return 'header';
            }
            if ($tagName === 'footer') {
                return 'footer';
            }
            if ($tagName === 'nav') {
                return 'navigation';
            }

            $parent = $parent->parentNode;
        }

        return 'content';
    }

    /**
     * Analyzes forms that collect user data for security and privacy compliance
     */
    public function analyzeDataCollectionForms(DOMDocument $domDocument): array
    {
        $forms = $domDocument->getElementsByTagName('form');
        $formAnalysis = [];

        foreach ($forms as $form) {
            if (!$form instanceof DOMElement) {
                continue;
            }

            $inputs = $form->getElementsByTagName('input');
            $sensitiveFields = [];
            $securityFeatures = [];

            // Check form security attributes
            if ($form->hasAttribute('action')) {
                $action = $form->getAttribute('action');
                if (strpos($action, 'https://') === 0) {
                    $securityFeatures[] = 'secure_endpoint';
                }
            }

            if (strtolower($form->getAttribute('method')) === 'post') {
                $securityFeatures[] = 'post_method';
            }

            // Analyze input fields
            foreach ($inputs as $input) {
                if (!$input instanceof DOMElement) {
                    continue;
                }

                $type = $input->getAttribute('type');
                $name = strtolower($input->getAttribute('name'));

                // Check for sensitive data fields
                $sensitivePatterns = [
                    'credit_card' => '/(credit|card|cc|cardnum)/i',
                    'password' => '/(password|passwd|pwd)/i',
                    'ssn' => '/(ssn|social)/i',
                    'email' => '/(email|e-mail)/i',
                    'phone' => '/(phone|tel|mobile)/i',
                    'personal' => '/(firstname|lastname|name|address|dob|birthday)/i'
                ];

                foreach ($sensitivePatterns as $category => $pattern) {
                    if (preg_match($pattern, $name)) {
                        $sensitiveFields[] = [
                            'field' => $name,
                            'type' => $type,
                            'category' => $category
                        ];
                    }
                }

                // Check security features on inputs
                if ($type === 'password' && $input->hasAttribute('autocomplete')) {
                    $securityFeatures[] = 'password_autocomplete';
                }
            }

            $formAnalysis[] = [
                'location' => $form->getNodePath(),
                'method' => $form->getAttribute('method') ?: 'get',
                'action' => $form->getAttribute('action'),
                'sensitive_fields' => $sensitiveFields,
                'security_features' => array_unique($securityFeatures),
                'has_csrf_protection' => $this->checkForCSRFToken($form),
                'encryption_level' => $this->determineFormEncryption($form)
            ];
        }

        return [
            'form_count' => count($formAnalysis),
            'forms' => $formAnalysis,
            'total_sensitive_fields' => array_sum(array_map(function ($form) {
                return count($form['sensitive_fields']);
            }, $formAnalysis))
        ];
    }

    /**
     * Checks if a form has CSRF token protection
     */
    private function checkForCSRFToken(DOMElement $form): bool
    {
        $inputs = $form->getElementsByTagName('input');
        foreach ($inputs as $input) {
            if (!$input instanceof DOMElement) {
                continue;
            }
            $name = strtolower($input->getAttribute('name'));
            if (strpos($name, 'csrf') !== false || strpos($name, 'token') !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Determines the encryption type used by a form
     */
    private function determineFormEncryption(DOMElement $form): string
    {
        $enctype = $form->getAttribute('enctype');
        if ($enctype === 'multipart/form-data') {
            return 'multipart';
        }
        if ($enctype === 'application/x-www-form-urlencoded') {
            return 'urlencoded';
        }
        return 'none';
    }


    /**
     * Analyzes schema.org types used in the document
     *
     * @param DOMDocument $domDocument The DOM document to analyze
     * @return array Analysis of schema.org type usage including itemtype and JSON-LD schemas
     */
    public function analyzeSchemaTypes(DOMDocument $domDocument): array
    {
        $schemas = [
            'itemtype' => [],
            'jsonld' => [],
            'total_schemas' => 0
        ];

        // Analyze itemtype schemas
        $elements = $domDocument->getElementsByTagName('*');
        foreach ($elements as $element) {
            if ($element->hasAttribute('itemtype')) {
                $type = $element->getAttribute('itemtype');
                if (strpos($type, 'schema.org') !== false) {
                    $schemaType = str_replace('http://schema.org/', '', $type);
                    $schemaType = str_replace('https://schema.org/', '', $schemaType);
                    
                    if (!isset($schemas['itemtype'][$schemaType])) {
                        $schemas['itemtype'][$schemaType] = 0;
                    }
                    $schemas['itemtype'][$schemaType]++;
                    $schemas['total_schemas']++;
                }
            }
        }

        // Analyze JSON-LD schemas
        $scripts = $domDocument->getElementsByTagName('script');
        foreach ($scripts as $script) {
            if ($script->getAttribute('type') === 'application/ld+json') {
                $json = json_decode($script->textContent, true);
                if ($json && isset($json['@type'])) {
                    $type = is_array($json['@type']) ? $json['@type'] : [$json['@type']];
                    foreach ($type as $schemaType) {
                        if (!isset($schemas['jsonld'][$schemaType])) {
                            $schemas['jsonld'][$schemaType] = 0;
                        }
                        $schemas['jsonld'][$schemaType]++;
                        $schemas['total_schemas']++;
                    }
                }
            }
        }

        return [
            'types' => $schemas,
            'recommendations' => [
                'Use JSON-LD format for structured data when possible',
                'Ensure schema types are valid and follow schema.org specifications',
                'Include all required properties for each schema type',
                'Test structured data using Schema.org testing tools'
            ]
        ];
    }
}