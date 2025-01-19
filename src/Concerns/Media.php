<?php

namespace Shaferllc\Analytics\Concerns;

use DOMDocument;

class Media
{
    public function analyzeImageOptimization(DOMDocument $domDocument): array
    {
        $images = [];
        $issues = [];
        
        $imgs = $domDocument->getElementsByTagName('img');
        foreach ($imgs as $img) {
            $src = $img->getAttribute('src');
            $alt = $img->getAttribute('alt');
            $width = $img->getAttribute('width');
            $height = $img->getAttribute('height');
            $loading = $img->getAttribute('loading');
            $srcset = $img->getAttribute('srcset');
            $sizes = $img->getAttribute('sizes');
            $decoding = $img->getAttribute('decoding');
            
            $imageData = [
                'src' => $src,
                'alt' => $alt,
                'dimensions' => [
                    'width' => $width,
                    'height' => $height
                ],
                'loading' => $loading,
                'srcset' => $srcset,
                'sizes' => $sizes,
                'decoding' => $decoding,
                'path' => $img->getNodePath()
            ];
            
            $images[] = $imageData;
            
            // Check for optimization issues
            if (empty($alt)) {
                $issues[] = [
                    'type' => 'missing_alt',
                    'src' => $src,
                    'recommendation' => 'Add descriptive alt text'
                ];
            }
            
            if (empty($loading)) {
                $issues[] = [
                    'type' => 'no_lazy_loading',
                    'src' => $src,
                    'recommendation' => 'Add loading="lazy" attribute'
                ];
            }
            
            if (empty($width) || empty($height)) {
                $issues[] = [
                    'type' => 'missing_dimensions',
                    'src' => $src,
                    'recommendation' => 'Specify width and height attributes'
                ];
            }

            if (empty($srcset)) {
                $issues[] = [
                    'type' => 'missing_srcset',
                    'src' => $src, 
                    'recommendation' => 'Add srcset for responsive images'
                ];
            }

            if (empty($decoding)) {
                $issues[] = [
                    'type' => 'missing_decoding',
                    'src' => $src,
                    'recommendation' => 'Add decoding="async" attribute'
                ];
            }

            // Check file extension
            $extension = strtolower(pathinfo($src, PATHINFO_EXTENSION));
            if (!in_array($extension, ['webp', 'avif'])) {
                $issues[] = [
                    'type' => 'not_next_gen_format',
                    'src' => $src,
                    'recommendation' => 'Use WebP or AVIF format for better compression'
                ];
            }
        }

        return [
            'total_images' => count($images),
            'images' => $images,
            'issues' => $issues,
            'recommendations' => [
                'Optimize image file sizes',
                'Use WebP/AVIF formats with fallbacks',
                'Implement lazy loading',
                'Provide descriptive alt text',
                'Add srcset and sizes for responsive images',
                'Use async decoding where appropriate',
                'Specify explicit width/height to prevent layout shifts',
                'Consider using CSS background images for decorative elements'
            ]
        ];
    }

}