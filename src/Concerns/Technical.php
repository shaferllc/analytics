<?php

namespace Shaferllc\Analytics\Concerns;

use DOMDocument;
use DOMXPath;
use DOMElement;

class Technical
{
    /**
     * Analyze canonical links on the page
     *
     * @param DOMDocument $domDocument The DOM document to analyze
     * @return array Analysis results for canonical links
     */
    public function analyzeCanonicalLinks(DOMDocument $domDocument): array
    {
        $results = [];
        $xpath = new DOMXPath($domDocument);
        
        // Get all canonical link elements
        $canonicalLinks = $xpath->query('//link[@rel="canonical"]');
        
        // Check if canonical link exists
        $results['has_canonical'] = $canonicalLinks->length > 0;
        
        if ($results['has_canonical']) {
            // Get the href value of the first canonical link
            $firstLink = $canonicalLinks->item(0);
            if ($firstLink instanceof DOMElement) {
                $canonicalUrl = $firstLink->getAttribute('href');
                $results['canonical_url'] = $canonicalUrl;
                
                // Check if multiple canonical links exist (which is invalid)
                $results['multiple_canonicals'] = $canonicalLinks->length > 1;
                
                // Verify canonical URL is valid
                $results['is_valid_url'] = filter_var($canonicalUrl, FILTER_VALIDATE_URL) !== false;
            }
        }
        
        return $results;
    }

    /**
     * Analyze robots meta directives on the page
     *
     * @param DOMDocument $domDocument The DOM document to analyze
     * @return array Analysis results for robots directives
     */
    public function analyzeRobotsDirectives(DOMDocument $domDocument): array
    {
        $xpath = new DOMXPath($domDocument);
        $results = [
            'has_robots_meta' => false,
            'directives' => [],
            'issues' => []
        ];

        // Get all robots meta tags
        $robotsMeta = $xpath->query('//meta[@name="robots" or @name="googlebot"]');
        
        if ($robotsMeta->length > 0) {
            $results['has_robots_meta'] = true;
            
            foreach ($robotsMeta as $meta) {
                if (!$meta instanceof DOMElement) {
                    continue;
                }

                $name = $meta->getAttribute('name');
                $content = $meta->getAttribute('content');
                $directives = array_map('trim', explode(',', strtolower($content)));
                
                $results['directives'][$name] = $directives;

                // Check for conflicting directives
                if (in_array('index', $directives) && in_array('noindex', $directives)) {
                    $results['issues'][] = [
                        'type' => 'conflicting_directives',
                        'message' => "Conflicting index/noindex directives found in {$name} meta tag"
                    ];
                }

                if (in_array('follow', $directives) && in_array('nofollow', $directives)) {
                    $results['issues'][] = [
                        'type' => 'conflicting_directives',
                        'message' => "Conflicting follow/nofollow directives found in {$name} meta tag"
                    ];
                }

                // Check for potentially problematic directives
                if (in_array('none', $directives) || in_array('noindex', $directives)) {
                    $results['issues'][] = [
                        'type' => 'blocking_directive',
                        'message' => "Search engine blocking directive found: {$content}"
                    ];
                }
            }
        }

        return $results;
    }

    /**
     * Analyzes internationalization and language-related elements in the document
     *
     * @param DOMDocument $domDocument The DOM document to analyze
     * @return array Analysis of internationalization implementation
     */
    public function analyzeInternationalization(DOMDocument $domDocument): array
    {
        $xpath = new DOMXPath($domDocument);
        $results = [
            'html_lang' => '',
            'alternate_langs' => [],
            'hreflang_tags' => [],
            'issues' => []
        ];

        // Check HTML lang attribute
        $html = $xpath->query('//html')[0];
        if ($html instanceof DOMElement) {
            $results['html_lang'] = $html->getAttribute('lang');
            
            if (empty($results['html_lang'])) {
                $results['issues'][] = [
                    'type' => 'missing_html_lang',
                    'message' => 'HTML lang attribute is missing',
                    'recommendation' => 'Add a lang attribute to the HTML element'
                ];
            }
        }

        // Check alternate language links
        $alternateLangs = $xpath->query('//link[@rel="alternate"][@hreflang]');
        foreach ($alternateLangs as $link) {
            if (!$link instanceof DOMElement) {
                continue;
            }

            $hreflang = $link->getAttribute('hreflang');
            $href = $link->getAttribute('href');

            $results['hreflang_tags'][] = [
                'lang' => $hreflang,
                'url' => $href
            ];

            // Validate hreflang format
            if (!preg_match('/^[a-z]{2}(-[A-Z]{2})?$/', $hreflang) && $hreflang !== 'x-default') {
                $results['issues'][] = [
                    'type' => 'invalid_hreflang',
                    'value' => $hreflang,
                    'message' => 'Invalid hreflang format',
                    'recommendation' => 'Use ISO 639-1 language codes with optional ISO 3166-1 country codes'
                ];
            }
        }

        // Check for x-default hreflang
        $hasXDefault = false;
        foreach ($results['hreflang_tags'] as $tag) {
            if ($tag['lang'] === 'x-default') {
                $hasXDefault = true;
                break;
            }
        }

        if (!empty($results['hreflang_tags']) && !$hasXDefault) {
            $results['issues'][] = [
                'type' => 'missing_x_default',
                'message' => 'No x-default hreflang tag found',
                'recommendation' => 'Add an x-default hreflang tag for language/region selector pages'
            ];
        }

        return $results;
    }

    /**
     * Analyzes SSL/HTTPS implementation and security headers
     * 
     * @param DOMDocument $domDocument The DOM document to analyze
     * @return array Analysis of security implementation
     */
    public function analyzeSecurityHeaders(DOMDocument $domDocument): array 
    {
        $results = [
            'has_https' => false,
            'mixed_content' => [],
            'issues' => []
        ];

        $xpath = new DOMXPath($domDocument);

        // Check for mixed content
        $resources = [
            'img' => 'src',
            'script' => 'src', 
            'link' => 'href',
            'iframe' => 'src',
            'form' => 'action',
            'object' => 'data'
        ];

        foreach ($resources as $tag => $attr) {
            $elements = $xpath->query("//{$tag}[@{$attr}]");
            foreach ($elements as $element) {
                if (!$element instanceof DOMElement) {
                    continue;
                }

                $url = $element->getAttribute($attr);
                if (str_starts_with($url, 'http:')) {
                    $results['mixed_content'][] = [
                        'element' => $tag,
                        'url' => $url
                    ];
                    $results['issues'][] = [
                        'type' => 'mixed_content',
                        'message' => "Mixed content found: {$url}",
                        'recommendation' => 'Update resource to use HTTPS'
                    ];
                }
            }
        }

        // Check for secure cookie attributes
        $scripts = $xpath->query('//script');
        foreach ($scripts as $script) {
            $content = $script->textContent;
            if (strpos($content, 'document.cookie') !== false && 
                (strpos($content, 'secure=false') !== false || 
                 strpos($content, 'httpOnly=false') !== false)) {
                $results['issues'][] = [
                    'type' => 'insecure_cookie',
                    'message' => 'Insecure cookie settings detected',
                    'recommendation' => 'Set secure and httpOnly flags for cookies'
                ];
            }
        }

        return $results;
    }

    /**
     * Analyzes XML sitemap implementation
     *
     * @param DOMDocument $domDocument The DOM document to analyze
     * @return array Analysis of sitemap implementation
     */
    public function analyzeSitemapImplementation(DOMDocument $domDocument): array
    {
        $results = [
            'has_sitemap_link' => false,
            'sitemap_urls' => [],
            'issues' => []
        ];

        $xpath = new DOMXPath($domDocument);
        
        // Check for sitemap references in robots.txt link elements
        $sitemapLinks = $xpath->query('//link[@rel="sitemap"]');
        foreach ($sitemapLinks as $link) {
            if (!$link instanceof DOMElement) {
                continue;
            }

            $results['has_sitemap_link'] = true;
            $url = $link->getAttribute('href');
            $results['sitemap_urls'][] = $url;

            // Validate sitemap URL
            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                $results['issues'][] = [
                    'type' => 'invalid_sitemap_url',
                    'url' => $url,
                    'message' => 'Invalid sitemap URL format',
                    'recommendation' => 'Ensure sitemap URL is absolute and properly formatted'
                ];
            }

            // Check sitemap file extension
            if (!preg_match('/\.(xml|xml\.gz)$/i', $url)) {
                $results['issues'][] = [
                    'type' => 'invalid_sitemap_format',
                    'url' => $url,
                    'message' => 'Sitemap should be XML format',
                    'recommendation' => 'Use .xml or .xml.gz extension for sitemap files'
                ];
            }
        }

        return $results;
    }

    /**
     * Analyzes the technology stack used in the web page
     * 
     * @param DOMDocument $domDocument The DOM document to analyze
     * @return array Analysis results including frameworks, libraries and technologies detected
     */
    public function analyzeTechnologyStack(DOMDocument $domDocument): array
    {
        $technologies = [];
        
        // Check for common JavaScript frameworks
        $scripts = $domDocument->getElementsByTagName('script');
        foreach ($scripts as $script) {
            $src = $script->getAttribute('src');
            if (strpos($src, 'react') !== false) {
                $technologies[] = ['name' => 'React', 'type' => 'framework'];
            }
            if (strpos($src, 'angular') !== false) {
                $technologies[] = ['name' => 'Angular', 'type' => 'framework'];
            }
            if (strpos($src, 'vue') !== false) {
                $technologies[] = ['name' => 'Vue.js', 'type' => 'framework'];
            }
            if (strpos($src, 'jquery') !== false) {
                $technologies[] = ['name' => 'jQuery', 'type' => 'library'];
            }
            if (strpos($src, 'next') !== false) {
                $technologies[] = ['name' => 'Next.js', 'type' => 'framework'];
            }
            if (strpos($src, 'nuxt') !== false) {
                $technologies[] = ['name' => 'Nuxt.js', 'type' => 'framework'];
            }
        }

        // Check for CSS frameworks
        $links = $domDocument->getElementsByTagName('link');
        foreach ($links as $link) {
            $href = $link->getAttribute('href');
            if (strpos($href, 'bootstrap') !== false) {
                $technologies[] = ['name' => 'Bootstrap', 'type' => 'css_framework'];
            }
            if (strpos($href, 'tailwind') !== false) {
                $technologies[] = ['name' => 'Tailwind CSS', 'type' => 'css_framework'];
            }
            if (strpos($href, 'bulma') !== false) {
                $technologies[] = ['name' => 'Bulma', 'type' => 'css_framework'];
            }
            if (strpos($href, 'foundation') !== false) {
                $technologies[] = ['name' => 'Foundation', 'type' => 'css_framework'];
            }
        }

        // Check meta tags for common platforms
        $metas = $domDocument->getElementsByTagName('meta');
        foreach ($metas as $meta) {
            $content = $meta->getAttribute('content');
            if (strpos($content, 'WordPress') !== false) {
                $technologies[] = ['name' => 'WordPress', 'type' => 'cms'];
            }
            if (strpos($content, 'Drupal') !== false) {
                $technologies[] = ['name' => 'Drupal', 'type' => 'cms'];
            }
            if (strpos($content, 'Joomla') !== false) {
                $technologies[] = ['name' => 'Joomla', 'type' => 'cms'];
            }
            if (strpos($content, 'Shopify') !== false) {
                $technologies[] = ['name' => 'Shopify', 'type' => 'ecommerce'];
            }
            if (strpos($content, 'Magento') !== false) {
                $technologies[] = ['name' => 'Magento', 'type' => 'ecommerce'];
            }
        }

        return [
            'technologies' => $technologies,
            'total_detected' => count($technologies),
            'summary' => [
                'frameworks' => array_filter($technologies, fn($tech) => $tech['type'] === 'framework'),
                'libraries' => array_filter($technologies, fn($tech) => $tech['type'] === 'library'),
                'css_frameworks' => array_filter($technologies, fn($tech) => $tech['type'] === 'css_framework'),
                'cms' => array_filter($technologies, fn($tech) => $tech['type'] === 'cms'),
                'ecommerce' => array_filter($technologies, fn($tech) => $tech['type'] === 'ecommerce')
            ]
        ];
    }

    /**
     * Analyzes browser compatibility issues in the document
     *
     * @param DOMDocument $domDocument The DOM document to analyze
     * @return array Analysis of browser compatibility including issues and recommendations
     */
    public function analyzeBrowserCompatibility(DOMDocument $domDocument): array
    {
        $issues = [];
        $features = [];

        // Check for modern JavaScript features
        $scripts = $domDocument->getElementsByTagName('script');
        foreach ($scripts as $script) {
            $content = $script->textContent;
            if (strpos($content, 'async/await') !== false || 
                strpos($content, '=>') !== false ||
                strpos($content, '...') !== false ||
                strpos($content, 'const') !== false ||
                strpos($content, 'let') !== false) {
                $features[] = [
                    'type' => 'modern_js',
                    'path' => $script->getNodePath(),
                    'compatibility' => 'ES6+ features may need transpilation for older browsers'
                ];
            }
        }

        // Check for modern CSS features
        $styles = $domDocument->getElementsByTagName('style');
        foreach ($styles as $style) {
            $content = $style->textContent;
            if (strpos($content, 'grid') !== false || 
                strpos($content, 'flex') !== false ||
                strpos($content, 'var(--') !== false ||
                strpos($content, '@supports') !== false ||
                strpos($content, 'calc') !== false) {
                $features[] = [
                    'type' => 'modern_css',
                    'path' => $style->getNodePath(),
                    'compatibility' => 'Modern CSS features may need fallbacks'
                ];
            }
        }

        // Check for HTML5 elements
        $html5Elements = [
            'article', 'aside', 'dialog', 'figcaption', 'figure', 
            'footer', 'header', 'main', 'nav', 'section', 'time',
            'mark', 'meter', 'progress', 'details', 'summary'
        ];
        
        foreach ($html5Elements as $element) {
            if ($domDocument->getElementsByTagName($element)->length > 0) {
                $features[] = [
                    'type' => 'html5_element',
                    'element' => $element,
                    'compatibility' => 'HTML5 elements may need polyfills for legacy browsers'
                ];
            }
        }

        // Check for potential compatibility issues
        foreach ($features as $feature) {
            if ($feature['type'] === 'modern_js') {
                $issues[] = [
                    'type' => 'js_compatibility',
                    'feature' => $feature,
                    'recommendation' => 'Consider using Babel or similar transpiler for broader browser support'
                ];
            }
            if ($feature['type'] === 'modern_css') {
                $issues[] = [
                    'type' => 'css_compatibility',
                    'feature' => $feature,
                    'recommendation' => 'Add CSS fallbacks or use PostCSS for better compatibility'
                ];
            }
            if ($feature['type'] === 'html5_element') {
                $issues[] = [
                    'type' => 'html_compatibility',
                    'feature' => $feature,
                    'recommendation' => 'Include HTML5 shiv or similar polyfill for legacy browsers'
                ];
            }
        }

        return [
            'features' => $features,
            'total_features' => count($features),
            'issues' => $issues,
            'total_issues' => count($issues),
            'recommendations' => [
                'Use feature detection instead of browser detection',
                'Implement polyfills for HTML5 elements in legacy browsers',
                'Add CSS fallbacks for modern properties',
                'Consider transpiling modern JavaScript',
                'Test in multiple browsers and versions',
                'Provide graceful degradation for unsupported features',
                'Use vendor prefixes where necessary',
                'Consider using modernizr for feature detection',
                'Implement progressive enhancement',
                'Use babel-preset-env for targeted transpilation'
            ]
        ];
    }

    /**
     * Validates HTML structure and syntax
     *
     * @param DOMDocument $domDocument The DOM document to validate
     * @return array Validation results including issues and recommendations
     */
    public function validateHtml(DOMDocument $domDocument): array
    {
        $issues = [];
        $elements = [];

        // Check for valid doctype declaration
        if (!$domDocument->doctype) {
            $issues[] = [
                'type' => 'missing_doctype',
                'recommendation' => 'Add a valid DOCTYPE declaration'
            ];
        }

        // Check for valid HTML structure
        $html = $domDocument->getElementsByTagName('html')->item(0);
        if (!$html) {
            $issues[] = [
                'type' => 'missing_html_tag',
                'recommendation' => 'Add root <html> element'
            ];
        } else {
            if (!$html->hasAttribute('lang')) {
                $issues[] = [
                    'type' => 'missing_lang',
                    'element' => 'html',
                    'recommendation' => 'Add lang attribute to html element'
                ];
            }
        }

        // Check for head and body sections
        $head = $domDocument->getElementsByTagName('head')->item(0);
        $body = $domDocument->getElementsByTagName('body')->item(0);

        if (!$head) {
            $issues[] = [
                'type' => 'missing_head',
                'recommendation' => 'Add <head> section'
            ];
        }

        if (!$body) {
            $issues[] = [
                'type' => 'missing_body', 
                'recommendation' => 'Add <body> section'
            ];
        }

        // Check for required meta tags
        if ($head) {
            $metaTags = $head->getElementsByTagName('meta');
            $hasCharset = false;
            $hasViewport = false;
            $hasDescription = false;

            foreach ($metaTags as $meta) {
                if ($meta->hasAttribute('charset')) {
                    $hasCharset = true;
                }
                if ($meta->getAttribute('name') === 'viewport') {
                    $hasViewport = true;
                }
                if ($meta->getAttribute('name') === 'description') {
                    $hasDescription = true;
                }
            }

            if (!$hasCharset) {
                $issues[] = [
                    'type' => 'missing_charset',
                    'recommendation' => 'Add charset meta tag'
                ];
            }

            if (!$hasViewport) {
                $issues[] = [
                    'type' => 'missing_viewport',
                    'recommendation' => 'Add viewport meta tag for responsive design'
                ];
            }

            if (!$hasDescription) {
                $issues[] = [
                    'type' => 'missing_description',
                    'recommendation' => 'Add meta description for SEO'
                ];
            }
        }

        // Check for semantic structure
        $mainContent = $domDocument->getElementsByTagName('main');
        if ($mainContent->length === 0) {
            $issues[] = [
                'type' => 'missing_main',
                'recommendation' => 'Add <main> element for primary content'
            ];
        }

        return [
            'valid_structure' => count($issues) === 0,
            'issues' => $issues,
            'total_issues' => count($issues),
            'recommendations' => [
                'Ensure valid DOCTYPE declaration',
                'Include required HTML structural elements',
                'Add necessary meta tags',
                'Specify document language',
                'Follow HTML5 standards',
                'Use semantic HTML elements',
                'Include ARIA landmarks where appropriate',
                'Validate HTML using W3C validator',
                'Ensure proper nesting of elements',
                'Use meaningful heading hierarchy'
            ]
        ];
    }

    /**
     * Detects potentially unused CSS selectors by analyzing the DOM structure
     */
    public function detectUnusedSelectors(DOMDocument $domDocument): array
    {
        $unusedSelectors = [];
        $elements = $domDocument->getElementsByTagName('style');
        
        foreach ($elements as $style) {
            $cssContent = $style->textContent;
            
            // Basic CSS parser to extract selectors
            preg_match_all('/([^{]+){[^}]*}/', $cssContent, $matches);
            
            if (!empty($matches[1])) {
                foreach ($matches[1] as $selector) {
                    $selector = trim($selector);
                    
                    // Skip media queries and keyframes
                    if (strpos($selector, '@') === 0) {
                        continue;
                    }
                    
                    try {
                        // Try to find elements matching the selector
                        $xpath = $this->cssToXPath($selector);
                        $query = new \DOMXPath($domDocument);
                        $result = @$query->query($xpath);
                        
                        if ($result === false || $result->length === 0) {
                            $unusedSelectors[] = [
                                'selector' => $selector,
                                'location' => $style->getNodePath(),
                                'recommendation' => 'Remove or update unused CSS selector'
                            ];
                        }
                    } catch (\Exception $e) {
                        continue;
                    }
                }
            }
        }
        
        return $unusedSelectors;
    }

    /**
     * Converts a CSS selector to an XPath query
     */
    private function cssToXPath(string $selector): string
    {
        $patterns = [
            '/\s*>\s*/' => '/',
            '/\s*,\s*/' => '|',
            '/\s+/' => '//',
            '/#([^\s]+)/' => "[@id='$1']",
            '/\.([^\s]+)/' => "[contains(concat(' ',normalize-space(@class),' '),' $1 ')]",
            '/\[([^\]]+)\]/' => '[$1]',
            '/:first-child/' => '[1]',
            '/:last-child/' => '[last()]',
            '/:nth-child\((\d+)\)/' => '[$1]'
        ];
        
        $xpath = preg_replace(array_keys($patterns), array_values($patterns), $selector);
        
        // Handle element selectors
        if (!preg_match('/^[\.#\[]/', $xpath)) {
            $xpath = '//' . $xpath;
        }
        
        return $xpath;
    }
    
     /**
     * Analyzes JavaScript usage and potential issues in the document
     *
     * @param DOMDocument $domDocument The DOM document to analyze
     * @return array Analysis of JavaScript usage including issues and recommendations
     */
    public function analyzeJavascriptUsage(DOMDocument $domDocument): array
    {
        $scripts = $domDocument->getElementsByTagName('script');
        $scriptElements = [];
        $issues = [];
        $inlineScriptCount = 0;
        $externalScriptCount = 0;

        foreach ($scripts as $script) {
            $scriptInfo = [
                'type' => $script->getAttribute('type'),
                'src' => $script->getAttribute('src'),
                'async' => $script->hasAttribute('async'),
                'defer' => $script->hasAttribute('defer'),
                'path' => $script->getNodePath()
            ];

            if ($scriptInfo['src']) {
                $externalScriptCount++;
                
                // Check for protocol-relative URLs
                if (strpos($scriptInfo['src'], '//') === 0) {
                    $issues[] = [
                        'type' => 'protocol_relative_url',
                        'element' => $scriptInfo,
                        'recommendation' => 'Use HTTPS instead of protocol-relative URLs'
                    ];
                }

                // Check for unoptimized loading
                if (!$scriptInfo['async'] && !$scriptInfo['defer']) {
                    $issues[] = [
                        'type' => 'blocking_script',
                        'element' => $scriptInfo,
                        'recommendation' => 'Consider adding async or defer attributes to non-critical scripts'
                    ];
                }
            } else {
                $inlineScriptCount++;
                
                // Check for inline script size
                if (strlen($script->textContent) > 1000) {
                    $issues[] = [
                        'type' => 'large_inline_script',
                        'element' => $scriptInfo,
                        'recommendation' => 'Consider moving large inline scripts to external files'
                    ];
                }
            }

            $scriptElements[] = $scriptInfo;
        }

        // Check for jQuery version if present
        $jquery = $this->detectJQueryVersion($domDocument);
        if ($jquery && version_compare($jquery, '3.0.0', '<')) {
            $issues[] = [
                'type' => 'outdated_jquery',
                'version' => $jquery,
                'recommendation' => 'Update to jQuery 3.x for better security and performance'
            ];
        }

        return [
            'total_scripts' => count($scriptElements),
            'inline_scripts' => $inlineScriptCount,
            'external_scripts' => $externalScriptCount,
            'scripts' => $scriptElements,
            'jquery_version' => $jquery,
            'issues' => $issues,
            'total_issues' => count($issues),
            'recommendations' => [
                'Use async or defer for non-critical scripts',
                'Minimize use of inline JavaScript',
                'Ensure all external scripts use HTTPS',
                'Keep JavaScript libraries updated',
                'Consider using modern JavaScript features',
                'Implement error handling and logging'
            ]
        ];
    }

    /**
     * Analyzes CSS usage and identifies potential issues in the HTML document
     *
     * @param \DOMDocument $domDocument
     * @return array
     */
    public function analyzeCssUsage(DOMDocument $domDocument): array
    {
        $issues = [];
        $cssElements = [];
        $inlineCssCount = 0;
        $externalCssCount = 0;

        // Analyze <link> elements for external stylesheets
        $linkElements = $domDocument->getElementsByTagName('link');
        foreach ($linkElements as $link) {
            if ($link->getAttribute('rel') === 'stylesheet') {
                $externalCssCount++;
                
                $cssInfo = [
                    'type' => 'external',
                    'href' => $link->getAttribute('href'),
                    'media' => $link->getAttribute('media') ?: 'all'
                ];

                // Check for non-HTTPS external stylesheets
                if (strpos($link->getAttribute('href'), 'http://') === 0) {
                    $issues[] = [
                        'type' => 'insecure_css',
                        'element' => $cssInfo,
                        'recommendation' => 'Use HTTPS for external stylesheets'
                    ];
                }

                $cssElements[] = $cssInfo;
            }
        }

        // Analyze <style> elements for inline CSS
        $styleElements = $domDocument->getElementsByTagName('style');
        foreach ($styleElements as $style) {
            $inlineCssCount++;
            
            $cssInfo = [
                'type' => 'inline',
                'content_length' => strlen($style->textContent)
            ];

            // Check for large inline styles
            if (strlen($style->textContent) > 2000) {
                $issues[] = [
                    'type' => 'large_inline_css',
                    'element' => $cssInfo,
                    'recommendation' => 'Consider moving large inline styles to external files'
                ];
            }

            $cssElements[] = $cssInfo;
        }

        // Check for inline styles in elements
        $elementsWithStyle = $domDocument->getElementsByTagName('*');
        foreach ($elementsWithStyle as $element) {
            if ($element->hasAttribute('style')) {
                $inlineCssCount++;
                $cssElements[] = [
                    'type' => 'inline_attribute',
                    'element_tag' => $element->tagName,
                    'style_content' => $element->getAttribute('style')
                ];
            }
        }

        return [
            'total_css' => count($cssElements),
            'inline_css' => $inlineCssCount,
            'external_css' => $externalCssCount,
            'stylesheets' => $cssElements,
            'issues' => $issues,
            'total_issues' => count($issues),
            'recommendations' => [
                'Use external stylesheets instead of inline styles',
                'Ensure all external stylesheets use HTTPS',
                'Minimize use of inline styles',
                'Consider using CSS preprocessors',
                'Implement media queries for responsive design',
                'Optimize CSS delivery'
            ]
        ];
    }

      /**
     * Attempts to detect jQuery version if present
     */
    private function detectJQueryVersion(DOMDocument $domDocument): ?string
    {
        $scripts = $domDocument->getElementsByTagName('script');
        foreach ($scripts as $script) {
            $src = $script->getAttribute('src');
            if ($src && preg_match('/jquery[.-](\d+\.\d+\.\d+)/', $src, $matches)) {
                return $matches[1];
            }
        }
        return null;
    }

}