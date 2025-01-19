<?php

namespace Shaferllc\Analytics\Concerns;

use DOMDocument;
use DOMElement;

class MetaTags
{
    private const SCORE_WEIGHTS = [
        'title' => 10,
        'description' => 8,
        'keywords' => 5,
        'viewport' => 7,
        'robots' => 6,
        'charset' => 7,
        'og' => 8,
        'twitter' => 6,
        'canonical' => 7,
        'author' => 4,
        'language' => 5,
        'favicon' => 4,
        'geo' => 3,
        'application' => 4,
        'security' => 6,
        'verification' => 3,
        'refresh' => 2,
        'theme-color' => 2,
        'apple-mobile' => 4,
        'alternate' => 3
    ];

    public function analyzeMetaTags(DOMDocument $domDocument): array
    {
        $metaTags = [
            'title' => $this->extractTitle($domDocument),
            'description' => $this->extractMetaDescription($domDocument),
            'keywords' => $this->extractMetaKeywords($domDocument),
            'viewport' => $this->extractViewport($domDocument),
            'robots' => $this->extractRobots($domDocument),
            'charset' => $this->extractCharset($domDocument),
            'og' => $this->extractOpenGraph($domDocument),
            'twitter' => $this->extractTwitter($domDocument),
            'canonical' => $this->extractCanonical($domDocument),
            'author' => $this->extractAuthor($domDocument),
            'language' => $this->extractLanguage($domDocument),
            'favicon' => $this->extractFavicon($domDocument),
            'geo' => $this->extractGeoTags($domDocument),
            'application' => $this->extractApplicationTags($domDocument),
            'security' => $this->extractSecurityTags($domDocument),
            'verification' => $this->extractVerificationTags($domDocument),
            'refresh' => $this->extractRefreshTag($domDocument),
            'theme-color' => $this->extractThemeColor($domDocument),
            'apple-mobile' => $this->extractAppleMobileTags($domDocument),
            'alternate' => $this->extractAlternateTags($domDocument)
        ];

        $score = $this->calculateMetaTagScore($metaTags);

        return [
            'tags' => $metaTags,
            'score' => $score,
            'issues' => $this->analyzeMetaTagIssues($metaTags),
            'recommendations' => $this->generateMetaTagRecommendations($metaTags)
        ];
    }

    private function calculateMetaTagScore(array $metaTags): array
    {
        $totalScore = 0;
        $maxScore = 0;
        $scores = [];

        foreach (self::SCORE_WEIGHTS as $tag => $weight) {
            $maxScore += $weight;
            $tagScore = 0;

            if ($tag === 'og' || $tag === 'twitter' || $tag === 'apple-mobile') {
                $tagScore = !empty($metaTags[$tag]) ? $weight : 0;
            } else {
                $tagScore = !empty($metaTags[$tag]) ? $weight : 0;
            }

            $scores[$tag] = [
                'score' => $tagScore,
                'max' => $weight,
                'percentage' => ($weight > 0) ? round(($tagScore / $weight) * 100) : 0
            ];

            $totalScore += $tagScore;
        }

        return [
            'total' => round(($totalScore / $maxScore) * 100),
            'breakdown' => $scores,
            'raw_score' => $totalScore,
            'max_possible' => $maxScore
        ];
    }

    public function extractTitle(DOMDocument $domDocument): ?string
    {
        return $this->extractNodeContent($domDocument, 'title');
    }

    private function extractNodeContent(DOMDocument $doc, string $tagName): ?string
    {
        $node = $doc->getElementsByTagName($tagName)->item(0);
        return $node ? trim($node->textContent) : null;
    }

    public function extractMetaDescription(DOMDocument $domDocument): ?string
    {
        $metaTags = $domDocument->getElementsByTagName('meta');
        foreach ($metaTags as $metaTag) {
            if ($metaTag instanceof DOMElement && strtolower($metaTag->getAttribute('name')) === 'description') {
                return $metaTag->getAttribute('content');
            }
        }
        return null;
    }

    private function extractMetaKeywords(DOMDocument $domDocument): array
    {
        $keywords = [];
        $metaTags = $domDocument->getElementsByTagName('meta');

        foreach ($metaTags as $meta) {
            if ($meta instanceof DOMElement && strtolower($meta->getAttribute('name')) === 'keywords') {
                $content = $meta->getAttribute('content');
                $keywords = array_merge(
                    $keywords,
                    array_map('trim', explode(',', $content))
                );
            }
        }

        return array_values(array_unique($keywords));
    }

    private function extractViewport(DOMDocument $domDocument): bool
    {
        $metaTags = $domDocument->getElementsByTagName('meta');
        foreach ($metaTags as $meta) {
            if ($meta instanceof DOMElement && strtolower($meta->getAttribute('name')) === 'viewport') {
                return true;
            }
        }
        return false;
    }

    private function extractRobots(DOMDocument $domDocument): ?string
    {
        $metaTags = $domDocument->getElementsByTagName('meta');
        foreach ($metaTags as $meta) {
            if ($meta instanceof DOMElement && strtolower($meta->getAttribute('name')) === 'robots') {
                return $meta->getAttribute('content');
            }
        }
        return null;
    }

    private function extractCharset(DOMDocument $domDocument): ?string
    {
        $metaTags = $domDocument->getElementsByTagName('meta');
        foreach ($metaTags as $meta) {
            if ($meta instanceof DOMElement && $meta->hasAttribute('charset')) {
                return $meta->getAttribute('charset');
            }
        }
        return null;
    }

    private function extractOpenGraph(DOMDocument $domDocument): array
    {
        $ogTags = [];
        $metaTags = $domDocument->getElementsByTagName('meta');
        foreach ($metaTags as $meta) {
            if ($meta instanceof DOMElement) {
                $property = strtolower($meta->getAttribute('property'));
                if (strpos($property, 'og:') === 0) {
                    $ogProperty = substr($property, 3);
                    $ogTags[$ogProperty] = $meta->getAttribute('content');
                }
            }
        }
        return $ogTags;
    }

    private function extractTwitter(DOMDocument $domDocument): array
    {
        $twitterTags = [];
        $metaTags = $domDocument->getElementsByTagName('meta');
        foreach ($metaTags as $meta) {
            if ($meta instanceof DOMElement) {
                $name = strtolower($meta->getAttribute('name'));
                if (strpos($name, 'twitter:') === 0) {
                    $twitterProperty = substr($name, 8);
                    $twitterTags[$twitterProperty] = $meta->getAttribute('content');
                }
            }
        }
        return $twitterTags;
    }

    private function extractCanonical(DOMDocument $domDocument): ?string
    {
        $links = $domDocument->getElementsByTagName('link');
        foreach ($links as $link) {
            if ($link instanceof DOMElement && $link->getAttribute('rel') === 'canonical') {
                return $link->getAttribute('href');
            }
        }
        return null;
    }

    private function extractAuthor(DOMDocument $domDocument): ?string
    {
        $metaTags = $domDocument->getElementsByTagName('meta');
        foreach ($metaTags as $meta) {
            if ($meta instanceof DOMElement && strtolower($meta->getAttribute('name')) === 'author') {
                return $meta->getAttribute('content');
            }
        }
        return null;
    }

    private function extractLanguage(DOMDocument $domDocument): ?string
    {
        $metaTags = $domDocument->getElementsByTagName('meta');
        foreach ($metaTags as $meta) {
            if ($meta instanceof DOMElement && strtolower($meta->getAttribute('name')) === 'language') {
                return $meta->getAttribute('content');
            }
        }
        return null;
    }

    private function extractFavicon(DOMDocument $domDocument): ?string
    {
        $links = $domDocument->getElementsByTagName('link');
        foreach ($links as $link) {
            if ($link instanceof DOMElement &&
                ($link->getAttribute('rel') === 'icon' || $link->getAttribute('rel') === 'shortcut icon')) {
                return $link->getAttribute('href');
            }
        }
        return null;
    }

    private function extractGeoTags(DOMDocument $domDocument): array
    {
        $geoTags = [];
        $metaTags = $domDocument->getElementsByTagName('meta');
        foreach ($metaTags as $meta) {
            if ($meta instanceof DOMElement) {
                $name = strtolower($meta->getAttribute('name'));
                if (strpos($name, 'geo.') === 0) {
                    $geoProperty = substr($name, 4);
                    $geoTags[$geoProperty] = $meta->getAttribute('content');
                }
            }
        }
        return $geoTags;
    }

    private function extractApplicationTags(DOMDocument $domDocument): array
    {
        $appTags = [];
        $metaTags = $domDocument->getElementsByTagName('meta');
        foreach ($metaTags as $meta) {
            if ($meta instanceof DOMElement) {
                $name = strtolower($meta->getAttribute('name'));
                if (in_array($name, ['application-name', 'msapplication-TileColor', 'msapplication-TileImage'])) {
                    $appTags[$name] = $meta->getAttribute('content');
                }
            }
        }
        return $appTags;
    }

    private function extractSecurityTags(DOMDocument $domDocument): array
    {
        $securityTags = [];
        $metaTags = $domDocument->getElementsByTagName('meta');
        foreach ($metaTags as $meta) {
            if ($meta instanceof DOMElement) {
                $httpEquiv = strtolower($meta->getAttribute('http-equiv'));
                if (in_array($httpEquiv, ['content-security-policy', 'referrer-policy', 'x-frame-options'])) {
                    $securityTags[$httpEquiv] = $meta->getAttribute('content');
                }
            }
        }
        return $securityTags;
    }

    private function extractVerificationTags(DOMDocument $domDocument): array
    {
        $verificationTags = [];
        $metaTags = $domDocument->getElementsByTagName('meta');
        foreach ($metaTags as $meta) {
            if ($meta instanceof DOMElement) {
                $name = strtolower($meta->getAttribute('name'));
                if (strpos($name, 'google-site-verification') === 0 ||
                    strpos($name, 'msvalidate.01') === 0 ||
                    strpos($name, 'yandex-verification') === 0) {
                    $verificationTags[$name] = $meta->getAttribute('content');
                }
            }
        }
        return $verificationTags;
    }

    private function extractRefreshTag(DOMDocument $domDocument): ?string
    {
        $metaTags = $domDocument->getElementsByTagName('meta');
        foreach ($metaTags as $meta) {
            if ($meta instanceof DOMElement && strtolower($meta->getAttribute('http-equiv')) === 'refresh') {
                return $meta->getAttribute('content');
            }
        }
        return null;
    }

    private function extractThemeColor(DOMDocument $domDocument): ?string
    {
        $metaTags = $domDocument->getElementsByTagName('meta');
        foreach ($metaTags as $meta) {
            if ($meta instanceof DOMElement && strtolower($meta->getAttribute('name')) === 'theme-color') {
                return $meta->getAttribute('content');
            }
        }
        return null;
    }

    private function extractAppleMobileTags(DOMDocument $domDocument): array
    {
        $appleTags = [];
        $metaTags = $domDocument->getElementsByTagName('meta');
        foreach ($metaTags as $meta) {
            if ($meta instanceof DOMElement) {
                $name = strtolower($meta->getAttribute('name'));
                if (strpos($name, 'apple-mobile-web-app') === 0) {
                    $property = substr($name, 19);
                    $appleTags[$property] = $meta->getAttribute('content');
                }
            }
        }
        return $appleTags;
    }

    private function extractAlternateTags(DOMDocument $domDocument): array
    {
        $alternateTags = [];
        $links = $domDocument->getElementsByTagName('link');
        foreach ($links as $link) {
            if ($link instanceof DOMElement && $link->getAttribute('rel') === 'alternate') {
                $alternateTags[] = [
                    'hreflang' => $link->getAttribute('hreflang'),
                    'href' => $link->getAttribute('href'),
                    'type' => $link->getAttribute('type')
                ];
            }
        }
        return $alternateTags;
    }

    private function analyzeMetaTagIssues(array $metaTags): array
    {
        $issues = [];

        if (empty($metaTags['title'])) {
            $issues[] = [
                'type' => 'missing_title',
                'severity' => 'high',
                'recommendation' => 'Add a descriptive title tag'
            ];
        } elseif (strlen($metaTags['title']) > 60) {
            $issues[] = [
                'type' => 'long_title',
                'severity' => 'medium',
                'recommendation' => 'Title tag should be 50-60 characters long'
            ];
        }

        if (empty($metaTags['description'])) {
            $issues[] = [
                'type' => 'missing_description',
                'severity' => 'high',
                'recommendation' => 'Add a meta description'
            ];
        } elseif (strlen($metaTags['description']) > 160) {
            $issues[] = [
                'type' => 'long_description',
                'severity' => 'medium',
                'recommendation' => 'Meta description should be 150-160 characters long'
            ];
        }

        if (!$metaTags['viewport']) {
            $issues[] = [
                'type' => 'missing_viewport',
                'severity' => 'high',
                'recommendation' => 'Add a viewport meta tag for mobile optimization'
            ];
        }

        if (empty($metaTags['og'])) {
            $issues[] = [
                'type' => 'missing_og_tags',
                'severity' => 'medium',
                'recommendation' => 'Add Open Graph meta tags for better social media sharing'
            ];
        }

        if (empty($metaTags['canonical'])) {
            $issues[] = [
                'type' => 'missing_canonical',
                'severity' => 'medium',
                'recommendation' => 'Add a canonical URL tag to prevent duplicate content issues'
            ];
        }

        if (empty($metaTags['language'])) {
            $issues[] = [
                'type' => 'missing_language',
                'severity' => 'low',
                'recommendation' => 'Specify the content language using a meta tag'
            ];
        }

        return $issues;
    }

    private function generateMetaTagRecommendations(array $metaTags): array
    {
        $recommendations = [];

        if (empty($metaTags['og']) || count($metaTags['og']) < 4) {
            $recommendations[] = 'Implement basic Open Graph tags (og:title, og:description, og:image, og:url)';
        }

        if (empty($metaTags['twitter'])) {
            $recommendations[] = 'Add Twitter Card meta tags for better Twitter sharing';
        }

        if (!isset($metaTags['charset'])) {
            $recommendations[] = 'Specify character encoding using meta charset tag';
        }

        if (empty($metaTags['favicon'])) {
            $recommendations[] = 'Add a favicon for better brand recognition';
        }

        $recommendations = array_merge($recommendations, [
            'Keep title tags between 50-60 characters',
            'Keep meta descriptions between 150-160 characters',
            'Use unique meta descriptions for each page',
            'Include relevant keywords naturally in meta tags',
            'Implement schema.org markup for rich snippets',
            'Add meta robots tag to control search engine crawling behavior',
            'Include alternate language versions using hreflang tags',
            'Add meta author tag for content attribution',
            'Implement mobile-specific meta tags for better mobile experience',
            'Use meta refresh tags appropriately for timed redirects',
            'Include meta viewport tag with appropriate settings',
            'Add meta theme-color for mobile browser UI customization',
            'Implement rel="prev" and rel="next" for paginated content',
            'Use meta geo tags for location-specific content',
            'Add appropriate security-related meta tags (CSP, referrer policy)',
            'Include meta application-name for PWA support',
            'Optimize meta tags for local SEO with geo meta tags',
            'Implement appropriate social media meta tags for each platform',
            'Use meta tags to specify content type and character encoding',
            'Add meta copyright tag for content protection'
        ]);

        return $recommendations;
    }
}
