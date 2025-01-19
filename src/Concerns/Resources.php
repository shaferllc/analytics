<?php

namespace Shaferllc\Analytics\Concerns;

use DOMDocument;

class Resources
{
    
    /**
     * Detects resource hints in the document
     *
     * @param DOMDocument $dom The DOM document to analyze
     * @return array Analysis of resource hints including preload, prefetch, preconnect
     */
    public function detectResourceHints(DOMDocument $dom): array
    {
        $hints = [
            'preload' => [],
            'prefetch' => [], 
            'preconnect' => [],
            'dns-prefetch' => []
        ];

        $xpath = new \DOMXPath($dom);
        $links = $xpath->query('//link[@rel]');

        foreach ($links as $link) {
            $rel = $link->getAttribute('rel');
            $href = $link->getAttribute('href');
            
            if (array_key_exists($rel, $hints)) {
                $hints[$rel][] = [
                    'href' => $href,
                    'as' => $link->getAttribute('as'),
                    'type' => $link->getAttribute('type'),
                    'crossorigin' => $link->getAttribute('crossorigin'),
                    'media' => $link->getAttribute('media')
                ];
            }
        }

        $total = array_sum(array_map('count', $hints));
        $details = [];

        if ($total === 0) {
            $details[] = 'No resource hints detected';
        } else {
            foreach ($hints as $type => $resources) {
                if (!empty($resources)) {
                    $details[] = sprintf('Found %d %s hint(s)', count($resources), $type);
                }
            }
        }

        return [
            'hints' => $hints,
            'total_hints' => $total,
            'details' => $details,
            'recommendations' => [
                'Use preload for critical resources',
                'Add preconnect for third-party domains',
                'Consider prefetch for resources needed later',
                'Implement dns-prefetch for external domains'
            ]
        ];
    }


    public function extractHttpRequests(DOMDocument $domDocument): array
    {
        $httpRequests = [];

        // JavaScript files
        foreach ($domDocument->getElementsByTagName('script') as $node) {
            if ($node->getAttribute('src')) {
                $httpRequests['JavaScripts'][] = $this->url($node->getAttribute('src'));
            }
        }

        // CSS files
        foreach ($domDocument->getElementsByTagName('link') as $node) {
            if (preg_match('/\bstylesheet\b/', $node->getAttribute('rel'))) {
                $httpRequests['CSS'][] = $this->url($node->getAttribute('href'));
            }
        }

        // Images (excluding lazy loaded)
        foreach ($domDocument->getElementsByTagName('img') as $node) {
            if (!empty($node->getAttribute('src')) && 
                !preg_match('/\blazy\b/', $node->getAttribute('loading'))) {
                $httpRequests['Images'][] = $this->url($node->getAttribute('src'));
            }
        }

        // Audio files (excluding preload="none")
        foreach ($domDocument->getElementsByTagName('audio') as $audioNode) {
            if ($audioNode->getAttribute('preload') != 'none') {
                foreach ($audioNode->getElementsByTagName('source') as $node) {
                    if (!empty($node->getAttribute('src')) && 
                        str_starts_with($node->getAttribute('type'), 'audio/')) {
                        $httpRequests['Audios'][] = $this->url($node->getAttribute('src'));
                    }
                }
            }
        }

        // Video files (excluding preload="none")
        foreach ($domDocument->getElementsByTagName('video') as $videoNode) {
            if ($videoNode->getAttribute('preload') != 'none') {
                foreach ($videoNode->getElementsByTagName('source') as $node) {
                    if (!empty($node->getAttribute('src')) && 
                        str_starts_with($node->getAttribute('type'), 'video/')) {
                        $httpRequests['Videos'][] = $this->url($node->getAttribute('src'));
                    }
                }
            }
        }

        // Iframes (excluding lazy loaded)
        foreach ($domDocument->getElementsByTagName('iframe') as $node) {
            if (!empty($node->getAttribute('src')) && 
                !preg_match('/\blazy\b/', $node->getAttribute('loading'))) {
                $httpRequests['Iframes'][] = $this->url($node->getAttribute('src'));
            }
        }

        return $httpRequests;
    }

       /**
     * Analyzes resource loading patterns and optimization opportunities
     *
     * @param DOMDocument $domDocument The DOM document to analyze
     * @return array Analysis of resource loading including issues and recommendations
     */
    public function analyzeResourceLoading(DOMDocument $domDocument): array
    {
        $resources = [];
        $issues = [];

        // Analyze scripts
        $scripts = $domDocument->getElementsByTagName('script');
        foreach ($scripts as $script) {
            $src = $script->getAttribute('src');
            if ($src) {
                $resources[] = [
                    'type' => 'script',
                    'src' => $src,
                    'async' => $script->hasAttribute('async'),
                    'defer' => $script->hasAttribute('defer'),
                    'path' => $script->getNodePath()
                ];

                if (!$script->hasAttribute('async') && !$script->hasAttribute('defer')) {
                    $issues[] = [
                        'type' => 'blocking_script',
                        'src' => $src,
                        'recommendation' => 'Add async or defer attribute to non-critical scripts'
                    ];
                }
            }
        }

        // Analyze stylesheets
        $styles = $domDocument->getElementsByTagName('link');
        foreach ($styles as $style) {
            if ($style->getAttribute('rel') === 'stylesheet') {
                $href = $style->getAttribute('href');
                $resources[] = [
                    'type' => 'stylesheet',
                    'src' => $href,
                    'media' => $style->getAttribute('media'),
                    'path' => $style->getNodePath()
                ];

                if (!$style->hasAttribute('media')) {
                    $issues[] = [
                        'type' => 'unoptimized_css',
                        'src' => $href,
                        'recommendation' => 'Consider using media queries for non-critical CSS'
                    ];
                }
            }
        }

        // Analyze images
        $images = $domDocument->getElementsByTagName('img');
        foreach ($images as $img) {
            $src = $img->getAttribute('src');
            $resources[] = [
                'type' => 'image',
                'src' => $src,
                'loading' => $img->getAttribute('loading'),
                'path' => $img->getNodePath()
            ];

            if (!$img->hasAttribute('loading')) {
                $issues[] = [
                    'type' => 'no_lazy_loading',
                    'src' => $src,
                    'recommendation' => 'Add loading="lazy" for images below the fold'
                ];
            }
        }

        // Check for resource hints
        $hints = $domDocument->getElementsByTagName('link');
        $hasPreload = false;
        $hasPreconnect = false;

        foreach ($hints as $hint) {
            $rel = $hint->getAttribute('rel');
            if ($rel === 'preload') $hasPreload = true;
            if ($rel === 'preconnect') $hasPreconnect = true;
        }

        if (!$hasPreload) {
            $issues[] = [
                'type' => 'missing_preload',
                'recommendation' => 'Consider using preload for critical resources'
            ];
        }

        if (!$hasPreconnect) {
            $issues[] = [
                'type' => 'missing_preconnect',
                'recommendation' => 'Use preconnect for third-party domains'
            ];
        }

        return [
            'resources' => $resources,
            'total_resources' => count($resources),
            'issues' => $issues,
            'total_issues' => count($issues),
            'recommendations' => [
                'Implement lazy loading for below-fold images',
                'Use async/defer for non-critical scripts',
                'Optimize CSS delivery with media queries',
                'Utilize resource hints (preload, preconnect)',
                'Minimize render-blocking resources',
                'Consider using HTTP/2 for parallel loading',
                'Implement critical CSS inlining'
            ]
        ];
    }

    public function detectMissingResourceHints(DOMDocument $domDocument): array
    {
        $xpath = new \DOMXPath($domDocument);
        $issues = [];
        $details = [];
        $resourceHints = [];

        // Check for existing resource hints
        $hints = $xpath->query('//link[@rel="preload" or @rel="prefetch" or @rel="preconnect" or @rel="dns-prefetch"]');
        
        foreach ($hints as $hint) {
            $resourceHints[] = [
                'type' => $hint->getAttribute('rel'),
                'href' => $hint->getAttribute('href'),
                'as' => $hint->getAttribute('as')
            ];
        }

        // Check for potential preload candidates
        $criticalResources = $xpath->query('//script[@src]|//link[@rel="stylesheet"]|//img[contains(@class, "hero") or contains(@class, "banner")]');
        foreach ($criticalResources as $resource) {
            $src = $resource->getAttribute('src') ?: $resource->getAttribute('href');
            if (!$this->hasMatchingHint($src, $resourceHints)) {
                $issues[] = [
                    'type' => 'missing_preload',
                    'resource' => $src,
                    'element' => $resource->getNodePath(),
                    'recommendation' => 'Consider preloading critical resource'
                ];
            }
        }

        // Check for potential preconnect candidates
        $externalDomains = [];
        $externalResources = $xpath->query('//script[@src]|//link[@href]|//img[@src]');
        foreach ($externalResources as $resource) {
            $url = $resource->getAttribute('src') ?: $resource->getAttribute('href');
            if ($url) {
                $domain = parse_url($url, PHP_URL_HOST);
                if ($domain && !in_array($domain, $externalDomains)) {
                    $externalDomains[] = $domain;
                    if (!$this->hasMatchingHint($domain, $resourceHints)) {
                        $issues[] = [
                            'type' => 'missing_preconnect',
                            'domain' => $domain,
                            'recommendation' => 'Consider adding preconnect for external domain'
                        ];
                    }
                }
            }
        }

        $details = [
            'Use preload for critical resources',
            'Add preconnect for external domains',
            'Consider dns-prefetch as fallback',
            'Avoid excessive resource hints',
            'Prioritize above-the-fold resources'
        ];

        return [
            'has_resource_hints' => !empty($resourceHints),
            'existing_hints' => $resourceHints,
            'issues' => $issues,
            'details' => $details
        ];
    }

    private function hasMatchingHint(string $resource, array $hints): bool
    {
        foreach ($hints as $hint) {
            if (strpos($resource, $hint['href']) !== false) {
                return true;
            }
        }
        return false;
    }


    /**
     * Normalize and clean a URL
     *
     * @param string $url The URL to normalize
     * @return string The normalized URL
     */
    private function url(string $url): string
    {
        // Remove whitespace
        $url = trim($url);

        // Handle protocol-relative URLs
        if (str_starts_with($url, '//')) {
            $url = 'https:' . $url;
        }

        // Handle root-relative URLs
        if (str_starts_with($url, '/')) {
            // Use domain if available, otherwise return as-is
            return isset($this->domain) ? 'https://' . $this->domain . $url : $url;
        }

        // Return original URL if it's already absolute or can't be normalized
        return $url;
    }

}