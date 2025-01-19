<?php

namespace Shaferllc\Analytics\Concerns;

use DOMNode;
use DOMXPath;
use DOMElement;
use DOMDocument;

class Security
{
    /**
     * Find potential security issues in the document
     */
    public function findSecurityIssues(DOMDocument $domDocument): array
    {
        $issues = [];
        $xpath = new DOMXPath($domDocument);

        // Check for inline scripts which could pose XSS risks
        $scripts = $xpath->query('//script[not(@src)]');
        foreach ($scripts as $script) {
            if (!$script instanceof DOMElement) continue;
            
            $issues[] = [
                'type' => 'inline_script',
                'severity' => 'warning', 
                'message' => 'Inline script detected - consider moving to external file'
            ];
        }

        // Check for forms without CSRF protection
        $forms = $xpath->query('//form');
        foreach ($forms as $form) {
            if (!$form instanceof DOMElement) continue;
            
            $csrfFound = $xpath->query('.//input[@name="_token" or @name="csrf_token"]', $form)->length > 0;
            
            if (!$csrfFound) {
                $issues[] = [
                    'type' => 'csrf',
                    'severity' => 'high',
                    'message' => 'Form without CSRF protection detected'
                ];
            }
        }

        // Check for target="_blank" without rel="noopener"
        $links = $xpath->query('//a[@target="_blank"][not(contains(@rel, "noopener"))]');
        foreach ($links as $link) {
            if (!$link instanceof DOMElement) continue;
            
            $issues[] = [
                'type' => 'target_blank',
                'severity' => 'medium',
                'message' => 'Link with target="_blank" missing rel="noopener"'
            ];
        }

        // Check for password inputs without autocomplete="off"
        $passwordInputs = $xpath->query('//input[@type="password"][not(@autocomplete="off")]');
        foreach ($passwordInputs as $input) {
            if (!$input instanceof DOMElement) continue;
            
            $issues[] = [
                'type' => 'password_autocomplete',
                'severity' => 'medium',
                'message' => 'Password input without autocomplete="off"'
            ];
        }

        return $issues;
    }

    /**
     * Find mixed content (HTTP resources on HTTPS pages)
     */
    public function findMixedContent(DOMDocument $domDocument): array
    {
        $mixedContent = [];
        $xpath = new DOMXPath($domDocument);

        // Check elements with src/href attributes
        $elements = $xpath->query('//*[@src or @href]');
        foreach ($elements as $element) {
            if (!$element instanceof DOMElement) continue;
            
            $url = $element->getAttribute('src') ?: $element->getAttribute('href');
            if ($url && str_starts_with($url, 'http://')) {
                $mixedContent[] = [
                    'type' => $element->nodeName,
                    'url' => $url,
                    'element' => $element->getNodePath()
                ];
            }

            // Check source elements for media tags
            if (in_array($element->nodeName, ['video', 'audio'])) {
                $sources = $xpath->query('.//source[@src]', $element);
                foreach ($sources as $source) {
                    if (!$source instanceof DOMElement) continue;
                    
                    $sourceUrl = $source->getAttribute('src');
                    if ($sourceUrl && str_starts_with($sourceUrl, 'http://')) {
                        $mixedContent[] = [
                            'type' => "{$element->nodeName}_source",
                            'url' => $sourceUrl,
                            'element' => $source->getNodePath()
                        ];
                    }
                }
            }
        }

        return $mixedContent;
    }

    /**
     * Analyzes content for potential security issues
     */
    public function analyzeContentSecurity(DOMDocument $dom): array
    {
        $issues = [];
        $details = [];
        $xpath = new DOMXPath($dom);

        // Check for potentially unsafe elements
        $unsafeElements = $xpath->query('//script[not(@src)]');
        foreach ($unsafeElements as $element) {
            if (!$element instanceof DOMElement) continue;
            
            $issues[] = [
                'type' => 'unsafe_script',
                'element' => $element->getNodePath(),
                'recommendation' => 'Remove or sanitize inline JavaScript'
            ];
        }

        // Check for inline event handlers
        $inlineEvents = $xpath->query('//*[@onclick or @onload or @onmouseover]');
        foreach ($inlineEvents as $element) {
            if (!$element instanceof DOMElement) continue;
            
            $issues[] = [
                'type' => 'inline_event_handler',
                'element' => $element->getNodePath(),
                'recommendation' => 'Remove inline event handlers and use external JavaScript'
            ];
        }

        // Check for external resources
        $externalResources = $xpath->query('//img[@src] | //iframe[@src] | //link[@href]');
        foreach ($externalResources as $resource) {
            if (!$resource instanceof DOMElement) continue;
            
            $src = $resource->getAttribute('src') ?: $resource->getAttribute('href');
            if (preg_match('/^https?:\/\//', $src)) {
                $details[] = "External resource found: {$src}";
            }
        }

        $details = array_merge($details, [
            'Avoid inline JavaScript',
            'Use Content Security Policy headers',
            'Sanitize user-generated content',
            'Validate external resources',
            'Implement proper XSS protection'
        ]);

        return [
            'passed' => empty($issues),
            'importance' => 'critical',
            'issues' => $issues,
            'details' => array_unique($details)
        ];
    }

    /**
     * Analyzes external resources in the document for security and performance implications
     */
    public function analyzeExternalResources(DOMDocument $domDocument): array
    {
        $resources = [
            'scripts' => [],
            'stylesheets' => [],
            'images' => [],
            'iframes' => [],
            'fonts' => [],
            'media' => []
        ];

        $issues = [];
        $domains = [];
        $xpath = new DOMXPath($domDocument);

        // Analyze scripts
        $scripts = $xpath->query('//script[@src]');
        foreach ($scripts as $script) {
            if (!$script instanceof DOMElement) continue;
            
            $src = $script->getAttribute('src');
            $domain = parse_url($src, PHP_URL_HOST);
            $domains[$domain] = ($domains[$domain] ?? 0) + 1;

            $resources['scripts'][] = [
                'url' => $src,
                'domain' => $domain,
                'async' => $script->hasAttribute('async'),
                'defer' => $script->hasAttribute('defer'),
                'integrity' => $script->getAttribute('integrity')
            ];

            if (!$script->hasAttribute('integrity')) {
                $issues[] = [
                    'type' => 'missing_sri',
                    'resource' => $src,
                    'recommendation' => 'Add Subresource Integrity (SRI) hash'
                ];
            }
        }

        // Analyze stylesheets
        $styles = $xpath->query('//link[@rel="stylesheet"]');
        foreach ($styles as $style) {
            if (!$style instanceof DOMElement) continue;
            
            $href = $style->getAttribute('href');
            $domain = parse_url($href, PHP_URL_HOST);
            $domains[$domain] = ($domains[$domain] ?? 0) + 1;

            $resources['stylesheets'][] = [
                'url' => $href,
                'domain' => $domain,
                'media' => $style->getAttribute('media'),
                'integrity' => $style->getAttribute('integrity')
            ];
        }

        // Analyze images
        $images = $xpath->query('//img[@src]');
        foreach ($images as $img) {
            if (!$img instanceof DOMElement) continue;
            
            $src = $img->getAttribute('src');
            $domain = parse_url($src, PHP_URL_HOST);
            $domains[$domain] = ($domains[$domain] ?? 0) + 1;

            $resources['images'][] = [
                'url' => $src,
                'domain' => $domain,
                'loading' => $img->getAttribute('loading'),
                'size' => [
                    'width' => $img->getAttribute('width'),
                    'height' => $img->getAttribute('height')
                ]
            ];
        }

        // Analyze iframes
        $iframes = $xpath->query('//iframe[@src]');
        foreach ($iframes as $iframe) {
            if (!$iframe instanceof DOMElement) continue;
            
            $src = $iframe->getAttribute('src');
            $domain = parse_url($src, PHP_URL_HOST);
            $domains[$domain] = ($domains[$domain] ?? 0) + 1;

            $resources['iframes'][] = [
                'url' => $src,
                'domain' => $domain,
                'sandbox' => $iframe->getAttribute('sandbox')
            ];

            if (!$iframe->hasAttribute('sandbox')) {
                $issues[] = [
                    'type' => 'unsandboxed_iframe',
                    'resource' => $src,
                    'recommendation' => 'Add sandbox attribute to iframe'
                ];
            }
        }

        return [
            'resources' => $resources,
            'domains' => $domains,
            'total_external_domains' => count($domains),
            'total_resources' => array_sum(array_map('count', $resources)),
            'issues' => $issues,
            'recommendations' => [
                'Use Subresource Integrity (SRI) for external scripts and stylesheets',
                'Implement proper Content Security Policy (CSP)',
                'Sandbox third-party iframes',
                'Minimize requests to external domains',
                'Consider self-hosting critical resources'
            ]
        ];
    }

    /**
     * Analyze form security features and potential vulnerabilities
     */
    public function analyzeFormSecurity(DOMDocument $domDocument): array
    {
        $issues = [];
        $xpath = new DOMXPath($domDocument);
        $forms = $xpath->query('//form');
        
        foreach ($forms as $form) {
            if (!$form instanceof DOMElement) continue;
            
            $method = strtolower($form->getAttribute('method') ?: 'get');
            $action = $form->getAttribute('action');
            
            // Check for CSRF protection
            $hasCSRFToken = $xpath->query('.//input[contains(translate(@name, "CSRF", "csrf"), "csrf") or contains(@name, "token")]', $form)->length > 0;

            if ($method === 'post' && !$hasCSRFToken) {
                $issues[] = [
                    'type' => 'missing_csrf_protection',
                    'element' => 'form',
                    'action' => $action,
                    'recommendation' => 'Add CSRF token to protect against cross-site request forgery'
                ];
            }

            // Check for autocomplete on sensitive fields
            $sensitiveInputs = $xpath->query('.//input[@type="password" or @type="credit-card" or contains(@name, "credit") or contains(@name, "card")]', $form);
            foreach ($sensitiveInputs as $input) {
                if (!$input instanceof DOMElement) continue;
                
                if (!$input->hasAttribute('autocomplete') || $input->getAttribute('autocomplete') !== 'off') {
                    $issues[] = [
                        'type' => 'autocomplete_not_disabled',
                        'element' => 'input',
                        'name' => $input->getAttribute('name'),
                        'recommendation' => 'Disable autocomplete for sensitive form fields'
                    ];
                }
            }
        }

        return [
            'form_analysis' => [
                'total_forms' => $forms->length,
                'issues' => $issues,
                'recommendations' => [
                    'Implement CSRF protection for all POST forms',
                    'Disable autocomplete for sensitive form fields',
                    'Use HTTPS for form submissions',
                    'Implement proper input validation',
                    'Consider implementing rate limiting for form submissions'
                ]
            ]
        ];
    }



    public function checkUnsafeCrossOriginLinks(DOMDocument $domDocument): array
    {
        $unsafeLinks = [];
        $links = $domDocument->getElementsByTagName('a');
        $currentHost = parse_url($this->domain ?: config('app.url'), PHP_URL_HOST);

        foreach ($links as $link) {
            $this->checkLinkSecurity($link, $currentHost, $unsafeLinks);
        }

        return $unsafeLinks;
    }

    public function checkLinkSecurity(DOMElement $link, string $currentHost, array &$unsafeLinks): void
    {
        $href = $link->getAttribute('href');
        $target = $link->getAttribute('target');
        $rel = $link->getAttribute('rel');

        if (empty($href) || $href[0] === '#') return;

        $parsedUrl = parse_url($href);
        $linkHost = $parsedUrl['host'] ?? null;

        if ($linkHost && $linkHost !== $currentHost) {
            if ($target === '_blank' && (!$rel || !preg_match('/(noopener.*noreferrer|noreferrer.*noopener)/', $rel))) {
                $unsafeLinks[] = [
                    'type' => 'unsafe_link',
                    'url' => $href,
                    'text' => $link->textContent,
                    'missing' => $this->getMissingRelAttributes($rel),
                    'element' => $link->getNodePath()
                ];
            }
        }
    }

    private function getMissingRelAttributes(string $rel): string
    {
        $missing = [];
        if (strpos($rel, 'noopener') === false) $missing[] = 'noopener';
        if (strpos($rel, 'noreferrer') === false) $missing[] = 'noreferrer';
        return implode(' ', $missing);
    }



    public function findPlaintextEmails(DOMDocument $domDocument): array
    {
        $emails = [];
        $textNodes = $this->getTextNodes($domDocument);
        $pattern = '/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/';

        foreach ($textNodes as $node) {
            $this->extractEmailsFromNode($node, $pattern, $emails);
        }

        return $emails;
    }

    private function getTextNodes(DOMNode $node): array
    {
        $textNodes = [];
        
        if ($node->nodeType === XML_TEXT_NODE) {
            $textNodes[] = $node;
        }
        
        if ($node->hasChildNodes()) {
            foreach ($node->childNodes as $child) {
                $textNodes = array_merge($textNodes, $this->getTextNodes($child));
            }
        }
        
        return $textNodes;
    }

    private function extractEmailsFromNode(DOMNode $node, string $pattern, array &$emails): void
    {
        $content = $node->nodeValue;
        if (preg_match_all($pattern, $content, $matches)) {
            foreach ($matches[0] as $email) {
                $emails[] = [
                    'type' => 'plaintext_email',
                    'email' => $email,
                    'context' => trim(substr($content, 0, 100)),
                    'element' => $node->getNodePath()
                ];
            }
        }
    }

    public function checkFormSecurity(DOMDocument $domDocument): array
    {
        $issues = [];
        $forms = $domDocument->getElementsByTagName('form');

        foreach ($forms as $form) {
            // Check for CSRF token
            $csrfTokens = $form->getElementsByTagName('input');
            $hasCSRFToken = false;

            foreach ($csrfTokens as $input) {
                if (
                    ($input->getAttribute('name') === '_token' || 
                    $input->getAttribute('name') === 'csrf_token') &&
                    $input->getAttribute('type') === 'hidden'
                ) {
                    $hasCSRFToken = true;
                    break;
                }
            }

            if (!$hasCSRFToken) {
                $issues[] = [
                    'type' => 'missing_csrf_token',
                    'element' => $form->getNodePath(),
                    'context' => $form->getAttribute('action') ?: 'No action specified'
                ];
            }

            // Check for secure method
            $method = strtoupper($form->getAttribute('method'));
            if ($method === 'GET' || empty($method)) {
                $issues[] = [
                    'type' => 'insecure_form_method',
                    'element' => $form->getNodePath(),
                    'context' => "Form using {$method} method"
                ];
            }

            // Check for secure action URL
            $action = $form->getAttribute('action');
            if (!empty($action) && strpos(strtolower($action), 'http:') === 0) {
                $issues[] = [
                    'type' => 'insecure_form_action',
                    'element' => $form->getNodePath(),
                    'context' => "Form action using non-HTTPS URL: {$action}"
                ];
            }
        }

        return $issues;
    }

}
