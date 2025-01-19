<?php

namespace Shaferllc\Analytics\Concerns;

use DOMDocument;
use DOMElement;

class Assets
{
    // Image Analysis
    
    /**
     * Extracts information about image formats used in the document
     *
     * @param DOMDocument $domDocument The DOM document to analyze
     * @return array Information about images including formats, dimensions and next-gen status
     */
    public function extractImageFormats(DOMDocument $domDocument): array
    {
        $imageFormats = [];
        $nextGenFormats = ['webp', 'avif']; // Define next-gen formats
        
        foreach ($domDocument->getElementsByTagName('img') as $image) {
            /** @var DOMElement $image */
            $src = $image->getAttribute('src');
            if (empty($src)) continue;
            
            $url = $this->url($src);
            $extension = strtolower(pathinfo($url, PATHINFO_EXTENSION));
            
            $imageFormats[] = [
                'url' => $url,
                'format' => $extension ?: 'unknown',
                'alt' => $image->getAttribute('alt'),
                'is_next_gen' => in_array($extension, $nextGenFormats),
                'is_svg' => $extension === 'svg',
                'dimensions' => [
                    'width' => $image->getAttribute('width'),
                    'height' => $image->getAttribute('height')
                ]
            ];
        }
        
        return [
            'images' => $imageFormats,
            'total' => count($imageFormats),
            'next_gen_count' => count(array_filter($imageFormats, fn($img) => $img['is_next_gen'])),
            'svg_count' => count(array_filter($imageFormats, fn($img) => $img['is_svg'])),
            'missing_alt' => count(array_filter($imageFormats, fn($img) => empty($img['alt']))),
            'missing_dimensions' => count(array_filter($imageFormats, fn($img) => 
                empty($img['dimensions']['width']) || empty($img['dimensions']['height'])
            ))
        ];
    }

    // Script Analysis

    /**
     * Extracts information about CSS and JS assets
     * 
     * @param DOMDocument $domDocument The DOM document to analyze
     * @return array Information about stylesheets and scripts
     */
    public function extractAssetInfo(DOMDocument $domDocument): array 
    {
        $assets = [
            'stylesheets' => [],
            'scripts' => []
        ];

        // Extract stylesheets
        foreach ($domDocument->getElementsByTagName('link') as $link) {
            /** @var DOMElement $link */
            if ($link->getAttribute('rel') === 'stylesheet') {
                $assets['stylesheets'][] = [
                    'href' => $link->getAttribute('href'),
                    'media' => $link->getAttribute('media'),
                    'is_async' => $link->hasAttribute('onload'),
                    'path' => $link->getNodePath()
                ];
            }
        }

        // Extract scripts
        foreach ($domDocument->getElementsByTagName('script') as $script) {
            /** @var DOMElement $script */
            $assets['scripts'][] = [
                'src' => $script->getAttribute('src'),
                'type' => $script->getAttribute('type') ?: 'text/javascript',
                'is_async' => $script->hasAttribute('async'),
                'is_defer' => $script->hasAttribute('defer'),
                'is_module' => $script->getAttribute('type') === 'module',
                'path' => $script->getNodePath()
            ];
        }

        return [
            'assets' => $assets,
            'stylesheet_count' => count($assets['stylesheets']),
            'script_count' => count($assets['scripts']),
            'async_scripts' => count(array_filter($assets['scripts'], fn($s) => $s['is_async'])),
            'defer_scripts' => count(array_filter($assets['scripts'], fn($s) => $s['is_defer'])),
            'module_scripts' => count(array_filter($assets['scripts'], fn($s) => $s['is_module']))
        ];
    }

    public function findInlineScripts(DOMDocument $domDocument): array
    {
        $inlineScripts = [];
        
        foreach ($domDocument->getElementsByTagName('script') as $script) {
            if ($script instanceof DOMElement && 
                !$script->getAttribute('src') && 
                $script->textContent) {
                $inlineScripts[] = [
                    'length' => strlen($script->textContent),
                    'location' => $script->getNodePath(),
                    'type' => $script->getAttribute('type') ?: 'text/javascript'
                ];
            }
        }
        
        return $inlineScripts;
    }

    public function findExternalScripts(DOMDocument $domDocument): array
    {
        $externalScripts = [];
        
        foreach ($domDocument->getElementsByTagName('script') as $script) {
            if ($script instanceof DOMElement && $script->getAttribute('src')) {
                $externalScripts[] = [
                    'src' => $this->url($script->getAttribute('src')),
                    'type' => $script->getAttribute('type') ?: 'text/javascript',
                    'is_async' => $script->hasAttribute('async'),
                    'is_defer' => $script->hasAttribute('defer'),
                    'is_module' => $script->getAttribute('type') === 'module',
                    'path' => $script->getNodePath()
                ];
            }
        }
        
        return $externalScripts;
    }

    public function analyzeAsyncDefer(DOMDocument $domDocument): array
    {
        $scripts = $domDocument->getElementsByTagName('script');
        $analysis = [
            'total_scripts' => 0,
            'async_scripts' => 0,
            'defer_scripts' => 0,
            'blocking_scripts' => 0,
            'issues' => []
        ];
        
        foreach ($scripts as $script) {
            if (!$script instanceof DOMElement || !$script->getAttribute('src')) {
                continue;
            }

            $analysis['total_scripts']++;
            
            if ($script->hasAttribute('async')) {
                $analysis['async_scripts']++;
            } elseif ($script->hasAttribute('defer')) {
                $analysis['defer_scripts']++;
            } else {
                $analysis['blocking_scripts']++;
                $analysis['issues'][] = [
                    'type' => 'blocking_script',
                    'src' => $this->url($script->getAttribute('src')),
                    'path' => $script->getNodePath(),
                    'suggestion' => 'Consider adding async or defer attribute'
                ];
            }
        }

        return [
            'stats' => $analysis,
            'recommendations' => [
                'Use async for scripts that can load in any order',
                'Use defer for scripts that rely on DOM or other scripts',
                'Place blocking scripts at the bottom of the body',
                'Consider using module type for modern JavaScript'
            ]
        ];
    }

    // Framework Detection

    public function detectJQueryUsage(DOMDocument $domDocument): array
    {
        $scripts = $domDocument->getElementsByTagName('script');
        $jqueryInfo = [
            'jquery_detected' => false,
            'version' => null,
            'instances' => [],
            'inline_usage' => false
        ];

        foreach ($scripts as $script) {
            if (!$script instanceof DOMElement) {
                continue;
            }

            $src = $script->getAttribute('src');
            if ($src && preg_match('/jquery[.-](\d+\.\d+\.\d+)?.*\.js/i', $src, $matches)) {
                $jqueryInfo['jquery_detected'] = true;
                $jqueryInfo['instances'][] = [
                    'type' => 'external',
                    'src' => $this->url($src),
                    'version' => $matches[1] ?? null,
                    'path' => $script->getNodePath()
                ];
                if (isset($matches[1])) {
                    $jqueryInfo['version'] = $matches[1];
                }
            }

            if ($script->textContent && preg_match('/(?:\$|jQuery)(?:\s*)\(/i', $script->textContent)) {
                $jqueryInfo['jquery_detected'] = true;
                $jqueryInfo['inline_usage'] = true;
                $jqueryInfo['instances'][] = [
                    'type' => 'inline',
                    'path' => $script->getNodePath(),
                    'usage' => substr($script->textContent, 0, 100) . '...'
                ];
            }
        }

        return [
            'detected' => $jqueryInfo['jquery_detected'],
            'version' => $jqueryInfo['version'],
            'usage' => [
                'external_scripts' => count(array_filter($jqueryInfo['instances'], fn($i) => $i['type'] === 'external')),
                'inline_usage' => $jqueryInfo['inline_usage']
            ],
            'instances' => $jqueryInfo['instances'],
            'recommendations' => [
                'Consider using vanilla JavaScript for modern browsers',
                'If jQuery is required, use the latest version',
                'Load jQuery from a reliable CDN with local fallback',
                'Consider using jQuery slim version if advanced features aren\'t needed'
            ]
        ];
    }

    /**
     * Detects JavaScript frameworks and libraries used in the document
     */
    public function detectFrameworks(DOMDocument $domDocument): array
    {
        $frameworks = [];
        
        foreach ($domDocument->getElementsByTagName('script') as $script) {
            if (!$script instanceof DOMElement) {
                continue;
            }

            $src = $script->getAttribute('src');
            $content = $script->textContent;

            // Framework detection patterns
            $this->detectReact($frameworks, $src, $content);
            $this->detectVue($frameworks, $src, $content);
            $this->detectAngular($frameworks, $src, $content);
            $this->detectOtherFrameworks($frameworks, $src);
        }

        return [
            'detected_frameworks' => array_values($frameworks),
            'total_frameworks' => count($frameworks),
            'recommendations' => [
                'Keep frameworks updated to latest stable versions',
                'Consider using modern lightweight alternatives',
                'Implement proper lazy loading for framework resources',
                'Ensure framework bundles are properly optimized'
            ]
        ];
    }

    private function detectReact(&$frameworks, $src, $content): void 
    {
        if ($src && preg_match('/react[.-](\d+\.\d+\.\d+)?.*\.js/i', $src, $matches)) {
            $frameworks['react'] = [
                'name' => 'React',
                'version' => $matches[1] ?? null,
                'source' => $this->url($src),
                'type' => 'external'
            ];
        } elseif ($content && preg_match('/React\.createElement|ReactDOM/i', $content)) {
            $frameworks['react'] = [
                'name' => 'React',
                'type' => 'inline',
                'detected_in' => 'inline script'
            ];
        }
    }

    private function detectVue(&$frameworks, $src, $content): void
    {
        if ($src && preg_match('/vue[.-](\d+\.\d+\.\d+)?.*\.js/i', $src, $matches)) {
            $frameworks['vue'] = [
                'name' => 'Vue.js',
                'version' => $matches[1] ?? null,
                'source' => $this->url($src),
                'type' => 'external'
            ];
        } elseif ($content && preg_match('/Vue\.createApp|new Vue/i', $content)) {
            $frameworks['vue'] = [
                'name' => 'Vue.js',
                'type' => 'inline',
                'detected_in' => 'inline script'
            ];
        }
    }

    private function detectAngular(&$frameworks, $src, $content): void
    {
        if ($src && preg_match('/angular[.-](\d+\.\d+\.\d+)?.*\.js/i', $src, $matches)) {
            $frameworks['angular'] = [
                'name' => 'Angular',
                'version' => $matches[1] ?? null,
                'source' => $this->url($src),
                'type' => 'external'
            ];
        } elseif ($content && preg_match('/angular\.module|ng-app/i', $content)) {
            $frameworks['angular'] = [
                'name' => 'Angular',
                'type' => 'inline',
                'detected_in' => 'inline script'
            ];
        }
    }

    private function detectOtherFrameworks(&$frameworks, $src): void
    {
        $frameworkPatterns = [
            'svelte' => [
                'pattern' => '/svelte[.-](\d+\.\d+\.\d+)?.*\.js/i',
                'name' => 'Svelte'
            ],
            'backbone' => [
                'pattern' => '/backbone[.-](\d+\.\d+\.\d+)?.*\.js/i',
                'name' => 'Backbone.js'
            ],
            'ember' => [
                'pattern' => '/ember[.-](\d+\.\d+\.\d+)?.*\.js/i',
                'name' => 'Ember.js'
            ]
        ];

        foreach ($frameworkPatterns as $key => $framework) {
            if ($src && preg_match($framework['pattern'], $src, $matches)) {
                $frameworks[$key] = [
                    'name' => $framework['name'],
                    'version' => $matches[1] ?? null,
                    'source' => $this->url($src),
                    'type' => 'external'
                ];
            }
        }
    }

    // Third-Party Script Analysis

    /**
     * Analyzes third-party scripts and their impact
     *
     * @param DOMDocument $domDocument The DOM document to analyze
     * @return array Analysis of third-party scripts including sources and recommendations
     */
    public function analyzeThirdPartyScripts(DOMDocument $domDocument): array
    {
        $thirdPartyScripts = [];
        $hostDomain = parse_url($domDocument->documentURI ?? '', PHP_URL_HOST);

        foreach ($domDocument->getElementsByTagName('script') as $script) {
            if (!$script instanceof DOMElement) {
                continue;
            }

            $src = $script->getAttribute('src');
            if (!$src) {
                continue;
            }

            $scriptDomain = parse_url($src, PHP_URL_HOST);
            if ($scriptDomain && $scriptDomain !== $hostDomain) {
                $category = $this->categorizeThirdPartyScript($src, $scriptDomain);
                
                $thirdPartyScripts[] = [
                    'source' => $this->url($src),
                    'domain' => $scriptDomain,
                    'category' => $category,
                    'async' => $script->hasAttribute('async'),
                    'defer' => $script->hasAttribute('defer'),
                    'path' => $script->getNodePath()
                ];
            }
        }

        $categories = array_count_values(array_column($thirdPartyScripts, 'category'));

        return [
            'scripts' => $thirdPartyScripts,
            'total_scripts' => count($thirdPartyScripts),
            'categories' => $categories,
            'async_count' => count(array_filter($thirdPartyScripts, fn($s) => $s['async'])),
            'defer_count' => count(array_filter($thirdPartyScripts, fn($s) => $s['defer'])),
            'recommendations' => [
                'Use async/defer attributes for non-critical third-party scripts',
                'Implement resource hints (dns-prefetch, preconnect) for third-party domains',
                'Monitor and audit third-party script performance impact',
                'Consider self-hosting critical third-party resources',
                'Implement subresource integrity (SRI) for third-party scripts'
            ]
        ];
    }

    private function categorizeThirdPartyScript(string $src, string $domain): string
    {
        $patterns = [
            'analytics' => [
                'google-analytics', 'analytics', 'gtag', 'stats',
                'segment.com', 'mixpanel', 'hotjar'
            ],
            'advertising' => [
                'doubleclick', 'adsense', 'adwords', 'advertising',
                'ad.', 'ads.'
            ],
            'social' => [
                'facebook', 'twitter', 'linkedin', 'instagram',
                'pinterest', 'social'
            ],
            'cdn' => [
                'cloudflare', 'jsdelivr', 'unpkg', 'cdnjs',
                'googleapis'
            ],
            'utility' => [
                'jquery', 'bootstrap', 'fontawesome', 'polyfill'
            ]
        ];

        foreach ($patterns as $category => $keywords) {
            foreach ($keywords as $keyword) {
                if (stripos($domain, $keyword) !== false || stripos($src, $keyword) !== false) {
                    return $category;
                }
            }
        }

        return 'other';
    }

    // Style Analysis

    public function findInlineStyles(DOMDocument $domDocument): array
    {
        $inlineStyles = [];
        
        foreach ($domDocument->getElementsByTagName('*') as $element) {
            if ($element instanceof DOMElement && $element->hasAttribute('style')) {
                $inlineStyles[] = [
                    'element' => $element->nodeName,
                    'style' => $element->getAttribute('style'),
                    'line' => $element->getLineNo()
                ];
            }
        }

        return $inlineStyles;
    }

    public function findExternalStylesheets(DOMDocument $domDocument): array
    {
        $stylesheets = [];
        
        foreach ($domDocument->getElementsByTagName('link') as $link) {
            if ($link instanceof DOMElement && 
                $link->getAttribute('rel') === 'stylesheet' && 
                $link->hasAttribute('href')) {
                $stylesheets[] = [
                    'href' => $link->getAttribute('href'),
                    'media' => $link->getAttribute('media') ?: 'all',
                    'line' => $link->getLineNo()
                ];
            }
        }

        return $stylesheets;
    }

    public function analyzeCriticalCss(DOMDocument $domDocument): array
    {
        $criticalCss = [];
        $styles = $domDocument->getElementsByTagName('style');
        $inlineStyles = [];
        
        // Analyze inline styles in head
        $head = $domDocument->getElementsByTagName('head')->item(0);
        if ($head) {
            foreach ($styles as $style) {
                if ($style->parentNode === $head) {
                    $inlineStyles[] = [
                        'content' => $style->textContent,
                        'size' => strlen($style->textContent),
                        'location' => 'head'
                    ];
                }
            }
        }

        // Check for critical CSS loading patterns
        $criticalLinks = [];
        foreach ($domDocument->getElementsByTagName('link') as $link) {
            if ($link->getAttribute('rel') === 'preload' && 
                $link->getAttribute('as') === 'style') {
                $criticalLinks[] = [
                    'href' => $link->getAttribute('href'),
                    'media' => $link->getAttribute('media') ?: 'all'
                ];
            }
        }

        return [
            'inline_styles' => $inlineStyles,
            'critical_links' => $criticalLinks,
            'total_inline_size' => array_sum(array_column($inlineStyles, 'size')),
            'has_critical_css' => !empty($inlineStyles) || !empty($criticalLinks)
        ];
    }

    public function analyzeMediaQueries(DOMDocument $domDocument): array
    {
        $mediaQueries = [];
        
        // Analyze inline styles for media queries
        foreach ($domDocument->getElementsByTagName('style') as $style) {
            $content = $style->textContent;
            preg_match_all('/@media[^{]+\{(?:[^{}]*\{[^{}]*\})*[^{}]*\}/i', $content, $matches);
            
            if (!empty($matches[0])) {
                foreach ($matches[0] as $query) {
                    preg_match('/@media\s+([^{]+)/i', $query, $mediaMatch);
                    $condition = trim($mediaMatch[1]);
                    
                    $mediaQueries[] = [
                        'condition' => $condition,
                        'content_length' => strlen($query),
                        'location' => $style->getNodePath(),
                        'type' => $this->categorizeMediaQuery($condition)
                    ];
                }
            }
        }

        // Analyze linked stylesheets
        foreach ($domDocument->getElementsByTagName('link') as $link) {
            if ($link->getAttribute('rel') === 'stylesheet' && $link->hasAttribute('media')) {
                $mediaQueries[] = [
                    'condition' => $link->getAttribute('media'),
                    'content_length' => 0, // External file, length unknown
                    'location' => $link->getNodePath(),
                    'type' => $this->categorizeMediaQuery($link->getAttribute('media')),
                    'is_external' => true
                ];
            }
        }

        return [
            'total_queries' => count($mediaQueries),
            'queries' => $mediaQueries,
            'types' => array_count_values(array_column($mediaQueries, 'type'))
        ];
    }

    private function categorizeMediaQuery(string $condition): string
    {
        $condition = strtolower($condition);
        
        if (strpos($condition, 'min-width') !== false || strpos($condition, 'max-width') !== false) {
            return 'responsive';
        }
        if (strpos($condition, 'print') !== false) {
            return 'print';
        }
        if (strpos($condition, 'screen') !== false) {
            return 'screen';
        }
        if (strpos($condition, 'orientation') !== false) {
            return 'orientation';
        }
        if (strpos($condition, 'prefers-color-scheme') !== false) {
            return 'theme';
        }
        if (strpos($condition, 'prefers-reduced-motion') !== false) {
            return 'accessibility';
        }
        
        return 'other';
    }

    // Helper Methods

    /**
     * Helper method to normalize URLs
     */
    private function url(string $src): string
    {
        // Remove query strings and fragments
        return preg_replace('/[?#].*$/', '', $src);
    }

    /**
     * Calculates sizes of various resources in the document
     *
     * @param DOMDocument $domDocument The DOM document to analyze
     * @return array Information about resource sizes including scripts, styles, images
     */
    public function calculateResourceSizes(DOMDocument $domDocument): array
    {
        $resources = [
            'scripts' => [],
            'styles' => [], 
            'images' => [],
            'total_size' => 0
        ];

        // Calculate script sizes
        foreach ($domDocument->getElementsByTagName('script') as $script) {
            if ($script->hasAttribute('src')) {
                $src = $script->getAttribute('src');
                $size = $this->getResourceSize($src);
                if ($size) {
                    $resources['scripts'][] = [
                        'url' => $src,
                        'size' => $size
                    ];
                    $resources['total_size'] += $size;
                }
            }
        }

        // Calculate stylesheet sizes
        foreach ($domDocument->getElementsByTagName('link') as $link) {
            if ($link->getAttribute('rel') === 'stylesheet') {
                $href = $link->getAttribute('href');
                $size = $this->getResourceSize($href);
                if ($size) {
                    $resources['styles'][] = [
                        'url' => $href,
                        'size' => $size
                    ];
                    $resources['total_size'] += $size;
                }
            }
        }

        // Calculate image sizes
        foreach ($domDocument->getElementsByTagName('img') as $img) {
            if ($img->hasAttribute('src')) {
                $src = $img->getAttribute('src');
                $size = $this->getResourceSize($src);
                if ($size) {
                    $resources['images'][] = [
                        'url' => $src,
                        'size' => $size
                    ];
                    $resources['total_size'] += $size;
                }
            }
        }

        return [
            'resources' => $resources,
            'total_size_kb' => round($resources['total_size'] / 1024, 2),
            'resource_counts' => [
                'scripts' => count($resources['scripts']),
                'styles' => count($resources['styles']),
                'images' => count($resources['images'])
            ]
        ];
    }

    /**
     * Gets the size of a resource in bytes
     */
    private function getResourceSize(string $url): ?int
    {
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            return null;
        }

        $headers = @get_headers($url, 1);
        if ($headers === false) {
            return null;
        }

        if (isset($headers['Content-Length'])) {
            return (int) $headers['Content-Length'];
        }

        // Attempt to get file size directly if accessible
        $size = @filesize($url);
        return $size !== false ? $size : null;
    }

    /**
     * Identifies render-blocking resources in the HTML
     * 
     * @return array Array of render-blocking resources and their details
     */
    public function findRenderBlockingResources(DOMDocument $domDocument): array
    {
        $renderBlocking = [
            'styles' => [],
            'scripts' => []
        ];

        // Find render-blocking stylesheets (those in <head>)
        $head = $domDocument->getElementsByTagName('head')->item(0);
        if ($head) {
            foreach ($head->getElementsByTagName('link') as $link) {
                if ($link->getAttribute('rel') === 'stylesheet' && !$link->hasAttribute('media')) {
                    $href = $link->getAttribute('href');
                    $renderBlocking['styles'][] = [
                        'url' => $href,
                        'size' => $this->getResourceSize($href)
                    ];
                }
            }
        }

        // Find render-blocking scripts (synchronous scripts in <head>)
        if ($head) {
            foreach ($head->getElementsByTagName('script') as $script) {
                if (!$script->hasAttribute('async') && !$script->hasAttribute('defer')) {
                    $src = $script->getAttribute('src');
                    if ($src) {
                        $renderBlocking['scripts'][] = [
                            'url' => $src,
                            'size' => $this->getResourceSize($src)
                        ];
                    }
                }
            }
        }

        return $renderBlocking;
    }

    /**
     * Analyzes lazy loading implementation for images and iframes
     *
     * @param DOMDocument $domDocument The DOM document to analyze
     * @return array Analysis of lazy loading implementation
     */
    public function analyzeLazyLoading(DOMDocument $domDocument): array
    {
        $analysis = [
            'images' => [
                'total' => 0,
                'lazy_native' => 0,
                'lazy_custom' => 0,
                'no_lazy' => 0,
                'items' => []
            ],
            'iframes' => [
                'total' => 0, 
                'lazy_native' => 0,
                'lazy_custom' => 0,
                'no_lazy' => 0,
                'items' => []
            ]
        ];

        // Analyze images
        foreach ($domDocument->getElementsByTagName('img') as $img) {
            $analysis['images']['total']++;
            
            $item = [
                'src' => $img->getAttribute('src'),
                'loading' => $img->getAttribute('loading'),
                'data_attributes' => []
            ];

            // Check for native lazy loading
            if ($img->getAttribute('loading') === 'lazy') {
                $analysis['images']['lazy_native']++;
            }
            // Check for custom lazy loading implementations
            elseif ($this->hasCustomLazyLoading($img)) {
                $analysis['images']['lazy_custom']++;
                $item['data_attributes'] = $this->getCustomLazyAttributes($img);
            }
            else {
                $analysis['images']['no_lazy']++;
            }

            $analysis['images']['items'][] = $item;
        }

        // Analyze iframes
        foreach ($domDocument->getElementsByTagName('iframe') as $iframe) {
            $analysis['iframes']['total']++;
            
            $item = [
                'src' => $iframe->getAttribute('src'),
                'loading' => $iframe->getAttribute('loading'),
                'data_attributes' => []
            ];

            // Check for native lazy loading
            if ($iframe->getAttribute('loading') === 'lazy') {
                $analysis['iframes']['lazy_native']++;
            }
            // Check for custom lazy loading implementations
            elseif ($this->hasCustomLazyLoading($iframe)) {
                $analysis['iframes']['lazy_custom']++;
                $item['data_attributes'] = $this->getCustomLazyAttributes($iframe);
            }
            else {
                $analysis['iframes']['no_lazy']++;
            }

            $analysis['iframes']['items'][] = $item;
        }

        return $analysis;
    }

    /**
     * Checks if element has custom lazy loading implementation
     */
    private function hasCustomLazyLoading(DOMElement $element): bool
    {
        $lazyAttributes = ['data-src', 'data-lazy', 'data-lazy-src'];
        $lazyClasses = ['lazy', 'lazyload'];

        foreach ($lazyAttributes as $attr) {
            if ($element->hasAttribute($attr)) {
                return true;
            }
        }

        $classes = explode(' ', $element->getAttribute('class'));
        foreach ($lazyClasses as $class) {
            if (in_array($class, $classes)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Gets custom lazy loading related attributes
     */
    private function getCustomLazyAttributes(DOMElement $element): array
    {
        $attributes = [];
        $lazyPrefixes = ['data-', 'lazy'];

        foreach ($element->attributes as $attr) {
            foreach ($lazyPrefixes as $prefix) {
                if (strpos($attr->name, $prefix) === 0) {
                    $attributes[$attr->name] = $attr->value;
                }
            }
        }

        return $attributes;
    }

    /**
     * Analyzes resource hints in the document
     *
     * @param DOMDocument $domDocument The DOM document to analyze
     * @return array Information about resource hints including preload, prefetch, preconnect
     */
    public function analyzeResourceHints(DOMDocument $domDocument): array
    {
        $hints = [
            'preload' => [],
            'prefetch' => [],
            'preconnect' => [],
            'dns-prefetch' => []
        ];

        foreach ($domDocument->getElementsByTagName('link') as $link) {
            if ($link instanceof DOMElement) {
                $rel = $link->getAttribute('rel');
                if (array_key_exists($rel, $hints)) {
                    $hint = [
                        'href' => $link->getAttribute('href'),
                        'as' => $link->getAttribute('as'),
                        'type' => $link->getAttribute('type'),
                        'crossorigin' => $link->getAttribute('crossorigin'),
                        'media' => $link->getAttribute('media')
                    ];

                    // Filter out empty attributes
                    $hints[$rel][] = array_filter($hint);
                }
            }
        }

        return [
            'hints' => $hints,
            'total_hints' => array_sum(array_map('count', $hints)),
            'by_type' => [
                'preload' => count($hints['preload']),
                'prefetch' => count($hints['prefetch']), 
                'preconnect' => count($hints['preconnect']),
                'dns_prefetch' => count($hints['dns-prefetch'])
            ],
            'recommendations' => [
                'Use preload for critical resources',
                'Implement dns-prefetch for external domain lookups',
                'Add preconnect for important third-party domains',
                'Consider prefetch for resources needed in next navigation',
                'Avoid excessive resource hints that may impact performance'
            ]
        ];
    }
}