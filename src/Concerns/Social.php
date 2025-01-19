<?php

namespace Shaferllc\Analytics\Concerns;

use DOMXPath;
use DOMDocument;
use DOMElement;

class Social
{
    private function isSocialLink(string $host, array $socialDomains): bool
    {
        return in_array($host, $socialDomains);
    }

    public function analyzeSocialSignals(DOMDocument $domDocument): array
    {
        $xpath = new DOMXPath($domDocument);
        $issues = [];
        $details = [];

        // Common social media domains
        $socialDomains = [
            'facebook.com',
            'twitter.com', 
            'instagram.com',
            'linkedin.com',
            'youtube.com',
            'pinterest.com'
        ];

        // Check for social media links
        $links = $xpath->query('//a[@href]');
        $socialLinksFound = false;

        foreach ($links as $link) {
            if (!$link instanceof DOMElement) {
                continue;
            }

            $href = $link->getAttribute('href');
            $parsedUrl = parse_url($href);
            
            if (!empty($parsedUrl['host']) && $this->isSocialLink($parsedUrl['host'], $socialDomains)) {
                $socialLinksFound = true;
                
                // Check for proper rel attributes
                $rel = $link->getAttribute('rel');
                if (!str_contains($rel, 'noopener') || !str_contains($rel, 'noreferrer')) {
                    $issues[] = [
                        'type' => 'social_link_security',
                        'element' => $link->nodeValue,
                        'recommendation' => 'Add rel="noopener noreferrer" to social media links'
                    ];
                    $details[] = "Social media link missing security attributes: {$link->nodeValue}";
                }
            }
        }

        // Check for social meta tags
        $metaTags = [
            'og:title' => false,
            'og:description' => false,
            'og:image' => false,
            'twitter:card' => false,
            'twitter:title' => false,
            'twitter:description' => false
        ];

        $metas = $xpath->query('//meta');
        foreach ($metas as $meta) {
            if (!$meta instanceof DOMElement) {
                continue;
            }

            $property = $meta->getAttribute('property');
            $name = $meta->getAttribute('name');
            
            if (array_key_exists($property, $metaTags)) {
                $metaTags[$property] = true;
            }
            if (array_key_exists($name, $metaTags)) {
                $metaTags[$name] = true;
            }
        }

        foreach ($metaTags as $tag => $exists) {
            if (!$exists) {
                $issues[] = [
                    'type' => 'missing_social_meta',
                    'recommendation' => "Add {$tag} meta tag for better social sharing"
                ];
                $details[] = "Missing {$tag} meta tag";
            }
        }

        if (!$socialLinksFound) {
            $issues[] = [
                'type' => 'no_social_presence',
                'recommendation' => 'Add social media links to improve visibility'
            ];
            $details[] = 'No social media links found';
        }

        $details = array_merge($details, [
            'Include links to social media profiles',
            'Add Open Graph meta tags for Facebook sharing',
            'Add Twitter Card meta tags for Twitter sharing',
            'Ensure social links have proper security attributes',
            'Consider adding social sharing buttons'
        ]);

        return [
            'passed' => count($issues) === 0,
            'importance' => 'medium',
            'issues' => $issues,
            'details' => $details
        ];
    }

    public function analyzeSocialMetadata(DOMDocument $domDocument): array
    {
        $xpath = new DOMXPath($domDocument);
        $metadata = [
            'open_graph' => [],
            'twitter_cards' => [],
            'other' => []
        ];
        $issues = [];

        // Extract all social meta tags
        $metas = $xpath->query('//meta');
        foreach ($metas as $meta) {
            if (!$meta instanceof DOMElement) {
                continue;
            }

            $property = $meta->getAttribute('property');
            $name = $meta->getAttribute('name');
            $content = $meta->getAttribute('content');

            if (str_starts_with($property, 'og:')) {
                $metadata['open_graph'][$property] = $content;
            } elseif (str_starts_with($name, 'twitter:')) {
                $metadata['twitter_cards'][$name] = $content;
            } elseif (in_array($name, ['description', 'keywords', 'author'])) {
                $metadata['other'][$name] = $content;
            }
        }

        // Check for required meta tags
        $requiredOG = ['og:title', 'og:type', 'og:image', 'og:url'];
        foreach ($requiredOG as $tag) {
            if (!isset($metadata['open_graph'][$tag])) {
                $issues[] = [
                    'type' => 'missing_og_tag',
                    'tag' => $tag,
                    'recommendation' => "Add {$tag} meta tag for better social sharing"
                ];
            }
        }

        $requiredTwitter = ['twitter:card', 'twitter:title', 'twitter:description'];
        foreach ($requiredTwitter as $tag) {
            if (!isset($metadata['twitter_cards'][$tag])) {
                $issues[] = [
                    'type' => 'missing_twitter_tag',
                    'tag' => $tag,
                    'recommendation' => "Add {$tag} meta tag for better Twitter sharing"
                ];
            }
        }

        return [
            'metadata' => $metadata,
            'issues' => $issues,
            'recommendations' => [
                'Ensure all required Open Graph tags are present',
                'Include Twitter Card meta tags for rich Twitter previews',
                'Add descriptive content to social meta tags',
                'Use high-quality images for social sharing',
                'Keep meta descriptions within recommended length limits'
            ]
        ];
    }

    /**
     * Analyzes Open Graph metadata from the HTML document
     *
     * @param \DOMXPath $xpath The XPath object for the HTML document
     * @return array Open Graph metadata analysis results
     */
    public function analyzeOpenGraph(DOMDocument|DOMXPath $xpath): array
    {
        // Convert DOMDocument to DOMXPath if needed
        if ($xpath instanceof DOMDocument) {
            $xpath = new DOMXPath($xpath);
        }

        $ogTags = $xpath->query('//meta[starts-with(@property, "og:")]');
        $metadata = [];
        $issues = [];

        foreach ($ogTags as $tag) {
            if (!$tag instanceof DOMElement) {
                continue;
            }

            $property = $tag->getAttribute('property');
            $content = $tag->getAttribute('content');
            $metadata[str_replace('og:', '', $property)] = $content;
        }

        // Check for essential OG tags
        $essentialTags = ['title', 'description', 'image', 'url'];
        foreach ($essentialTags as $tag) {
            if (!isset($metadata[$tag])) {
                $issues[] = [
                    'type' => 'missing_og_tag',
                    'tag' => "og:$tag",
                    'recommendation' => "Add og:$tag meta tag for better social sharing"
                ];
            }
        }

        // Validate image URL if present
        if (isset($metadata['image'])) {
            if (!filter_var($metadata['image'], FILTER_VALIDATE_URL)) {
                $issues[] = [
                    'type' => 'invalid_og_image',
                    'url' => $metadata['image'],
                    'recommendation' => 'Provide absolute URL for og:image'
                ];
            }
        }

        return [
            'metadata' => $metadata,
            'issues' => $issues,
            'has_basic_tags' => count(array_intersect_key(array_flip($essentialTags), $metadata)) === count($essentialTags)
        ];
    }

    /**
     * Analyzes Twitter Card metadata in the document
     *
     * @param \DOMDocument|\DOMXPath $xpath The XPath object for the HTML document
     * @return array Twitter Card metadata analysis results
     */
    public function analyzeTwitterCards(DOMDocument|DOMXPath $xpath): array
    {
        // Convert DOMDocument to DOMXPath if needed
        if ($xpath instanceof DOMDocument) {
            $xpath = new DOMXPath($xpath);
        }

        $twitterTags = $xpath->query('//meta[starts-with(@name, "twitter:")]');
        $metadata = [];
        $issues = [];

        foreach ($twitterTags as $tag) {
            if (!$tag instanceof DOMElement) {
                continue;
            }

            $property = $tag->getAttribute('name');
            $content = $tag->getAttribute('content');
            $metadata[str_replace('twitter:', '', $property)] = $content;
        }

        // Check for essential Twitter Card tags
        $essentialTags = ['card', 'title', 'description', 'image'];
        foreach ($essentialTags as $tag) {
            if (!isset($metadata[$tag])) {
                $issues[] = [
                    'type' => 'missing_twitter_tag',
                    'tag' => "twitter:$tag",
                    'recommendation' => "Add twitter:$tag meta tag for better Twitter sharing"
                ];
            }
        }

        // Validate card type if present
        if (isset($metadata['card'])) {
            $validCardTypes = ['summary', 'summary_large_image', 'app', 'player'];
            if (!in_array($metadata['card'], $validCardTypes)) {
                $issues[] = [
                    'type' => 'invalid_card_type',
                    'value' => $metadata['card'],
                    'recommendation' => 'Use a valid Twitter Card type: ' . implode(', ', $validCardTypes)
                ];
            }
        }

        // Validate image URL if present
        if (isset($metadata['image'])) {
            if (!filter_var($metadata['image'], FILTER_VALIDATE_URL)) {
                $issues[] = [
                    'type' => 'invalid_twitter_image',
                    'url' => $metadata['image'],
                    'recommendation' => 'Provide absolute URL for twitter:image'
                ];
            }
        }

        return [
            'metadata' => $metadata,
            'issues' => $issues,
            'has_basic_tags' => count(array_intersect_key(array_flip($essentialTags), $metadata)) === count($essentialTags)
        ];
    }
}