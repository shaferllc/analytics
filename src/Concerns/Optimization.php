<?php

namespace Shaferllc\Analytics\Concerns;

use DOMDocument;

class Optimization
{
    
    public function detectOptimizationOpportunities(DOMDocument $domDocument): array
    {
        $xpath = new \DOMXPath($domDocument);
        $opportunities = [];
        $issues = [];
        $details = [];

        // Check for unoptimized images
        $images = $xpath->query('//img');
        foreach ($images as $image) {
            $src = $image->getAttribute('src');
            if ($src && !preg_match('/\.(webp|avif)$/i', $src)) {
                $opportunities[] = [
                    'type' => 'image_format',
                    'element' => $image->getNodePath(),
                    'recommendation' => 'Consider using WebP or AVIF image formats'
                ];
            }

            // Check for missing width/height attributes
            if (!$image->hasAttribute('width') || !$image->hasAttribute('height')) {
                $opportunities[] = [
                    'type' => 'image_dimensions',
                    'element' => $image->getNodePath(),
                    'recommendation' => 'Add width and height attributes to prevent layout shifts'
                ];
            }
        }

        // Check for render-blocking resources
        $blockingResources = $xpath->query('//link[@rel="stylesheet"]|//script[not(@async) and not(@defer)]');
        foreach ($blockingResources as $resource) {
            $opportunities[] = [
                'type' => 'render_blocking',
                'element' => $resource->getNodePath(),
                'recommendation' => $resource->nodeName === 'link' ? 
                    'Consider using media queries or loading CSS asynchronously' :
                    'Add async or defer attributes to non-critical scripts'
            ];
        }

        // Check for unminified resources
        $resources = $xpath->query('//script[@src]|//link[@rel="stylesheet"][@href]');
        foreach ($resources as $resource) {
            $url = $resource->getAttribute('src') ?: $resource->getAttribute('href');
            if ($url && !preg_match('/\.(min|bundle)\.(js|css)$/i', $url)) {
                $opportunities[] = [
                    'type' => 'unminified_resource',
                    'element' => $resource->getNodePath(),
                    'recommendation' => 'Minify and bundle resources for production'
                ];
            }
        }

        if (empty($opportunities)) {
            $details[] = 'No immediate optimization opportunities detected';
        } else {
            $issues = [
                'type' => 'optimization_needed',
                'count' => count($opportunities),
                'recommendation' => 'Multiple optimization opportunities identified'
            ];
        }

        $details = array_merge($details, [
            'Optimize and compress images',
            'Minimize render-blocking resources',
            'Enable text compression',
            'Implement resource minification',
            'Consider lazy loading for below-fold content'
        ]);

        return [
            'has_opportunities' => !empty($opportunities),
            'opportunities' => $opportunities,
            'issues' => $issues,
            'details' => $details
        ];
    }

    /**
     * Detects performance bottlenecks in the document
     *
     * @param DOMDocument $domDocument The DOM document to analyze
     * @return array Analysis of performance bottlenecks including issues and recommendations
     */
    public function detectPerformanceBottlenecks(DOMDocument $domDocument): array
    {
        $bottlenecks = [];
        $issues = [];
        $details = [];
        
        $xpath = new \DOMXPath($domDocument);

        // Check for large images without dimensions
        $images = $xpath->query('//img[not(@width) or not(@height)]');
        foreach ($images as $image) {
            $src = $image->getAttribute('src');
            if ($src) {
                $bottlenecks[] = [
                    'type' => 'layout_shift_risk',
                    'element' => $image->getNodePath(),
                    'recommendation' => 'Add width and height attributes to prevent layout shifts'
                ];
            }
        }

        // Check for excessive DOM size
        $elements = $xpath->query('//*');
        $domSize = $elements->length;
        if ($domSize > 1500) {
            $issues[] = [
                'type' => 'large_dom',
                'count' => $domSize,
                'recommendation' => 'Large DOM size can impact performance. Consider reducing DOM elements.'
            ];
        }

        // Check for nested tables
        $nestedTables = $xpath->query('//table//table');
        if ($nestedTables->length > 0) {
            $bottlenecks[] = [
                'type' => 'nested_tables',
                'count' => $nestedTables->length,
                'recommendation' => 'Nested tables can slow down page rendering. Consider using CSS Grid or Flexbox.'
            ];
        }

        // Check for excessive CSS/JS files
        $styleSheets = $xpath->query('//link[@rel="stylesheet"]')->length;
        $scripts = $xpath->query('//script[@src]')->length;
        
        if ($styleSheets > 8) {
            $bottlenecks[] = [
                'type' => 'excessive_css',
                'count' => $styleSheets,
                'recommendation' => 'Too many CSS files. Consider bundling stylesheets.'
            ];
        }

        if ($scripts > 10) {
            $bottlenecks[] = [
                'type' => 'excessive_scripts',
                'count' => $scripts,
                'recommendation' => 'Too many script files. Consider bundling JavaScript files.'
            ];
        }

        // Check for inline styles
        $inlineStyles = $xpath->query('//*[@style]');
        if ($inlineStyles->length > 20) {
            $bottlenecks[] = [
                'type' => 'inline_styles',
                'count' => $inlineStyles->length,
                'recommendation' => 'Excessive inline styles detected. Move styles to external stylesheet.'
            ];
        }

        if (empty($bottlenecks)) {
            $details[] = 'No major performance bottlenecks detected';
        } else {
            $issues[] = [
                'type' => 'performance_issues',
                'count' => count($bottlenecks),
                'recommendation' => 'Multiple performance bottlenecks identified'
            ];
        }

        $details = array_merge($details, [
            'Optimize DOM size and structure',
            'Bundle and minimize HTTP requests',
            'Avoid layout shifts',
            'Reduce JavaScript execution time',
            'Optimize critical rendering path'
        ]);

        return [
            'has_bottlenecks' => !empty($bottlenecks),
            'bottlenecks' => $bottlenecks,
            'issues' => $issues,
            'details' => $details,
            'metrics' => [
                'dom_size' => $domSize,
                'stylesheet_count' => $styleSheets,
                'script_count' => $scripts
            ]
        ];
    }


    public function detectPerformanceIssues(DOMDocument $domDocument): array
    {
        $issues = [];
        
        // Check for render-blocking resources
        $scripts = $domDocument->getElementsByTagName('script');
        foreach ($scripts as $script) {
            if (!$script->hasAttribute('async') && !$script->hasAttribute('defer')) {
                $issues[] = [
                    'type' => 'render_blocking_script',
                    'path' => $script->getNodePath(),
                    'src' => $script->getAttribute('src'),
                    'impact' => 'high'
                ];
            }
        }

        // Check for unoptimized images
        $images = $domDocument->getElementsByTagName('img');
        foreach ($images as $img) {
            if (!$img->hasAttribute('loading')) {
                $issues[] = [
                    'type' => 'missing_lazy_loading',
                    'path' => $img->getNodePath(),
                    'src' => $img->getAttribute('src'),
                    'impact' => 'medium'
                ];
            }
        }

        return [
            'issues' => $issues,
            'recommendations' => [
                'Use async/defer for non-critical scripts',
                'Implement lazy loading for images',
                'Minimize render-blocking resources',
                'Optimize critical rendering path'
            ]
        ];
    }


    public function detectRenderBlocking(DOMDocument $domDocument): array
    {
        $blocking = [];
        
        // Check for render-blocking CSS
        $styles = $domDocument->getElementsByTagName('link');
        foreach ($styles as $style) {
            if ($style->getAttribute('rel') === 'stylesheet' && 
                !$style->getAttribute('media') && 
                !$style->getAttribute('onload')) {
                $blocking['css'][] = [
                    'href' => $style->getAttribute('href'),
                    'path' => $style->getNodePath(),
                    'recommendation' => 'Add media attribute or load asynchronously'
                ];
            }
        }
        
        // Check for render-blocking JavaScript
        $scripts = $domDocument->getElementsByTagName('script');
        foreach ($scripts as $script) {
            if (!$script->getAttribute('async') && 
                !$script->getAttribute('defer') && 
                !$script->getAttribute('type') === 'module') {
                $blocking['js'][] = [
                    'src' => $script->getAttribute('src'),
                    'path' => $script->getNodePath(),
                    'recommendation' => 'Add async or defer attribute'
                ];
            }
        }
        
        return $blocking;
    }



    public function detectHeavyElements(DOMDocument $domDocument): array
    {
        $heavy = [];
        
        // Check for large images
        $images = $domDocument->getElementsByTagName('img');
        foreach ($images as $img) {
            $width = $img->getAttribute('width');
            $height = $img->getAttribute('height');
            
            if ($width > 1000 || $height > 1000) {
                $heavy['images'][] = [
                    'src' => $img->getAttribute('src'),
                    'dimensions' => "{$width}x{$height}",
                    'path' => $img->getNodePath(),
                    'recommendation' => 'Consider using responsive images with srcset'
                ];
            }
        }
        
        // Check for large inline scripts
        $scripts = $domDocument->getElementsByTagName('script');
        foreach ($scripts as $script) {
            if (!$script->getAttribute('src') && strlen($script->textContent) > 1000) {
                $heavy['inline_scripts'][] = [
                    'size' => strlen($script->textContent),
                    'path' => $script->getNodePath(),
                    'recommendation' => 'Move large scripts to external files'
                ];
            }
        }
        
        return $heavy;
    }

   
}