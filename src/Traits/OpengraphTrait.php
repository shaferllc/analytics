<?php

namespace Shaferllc\Analytics\Traits;

use DOMXPath;
use DOMElement;
use DOMDocument;
use App\Services\OpengraphService;

trait OpengraphTrait
{
    private const HOST_DIRECTIVE_TYPES = [
        'host',
        'mirror',
        'preferred-host'
    ];

    private const CRAWL_RATE_THRESHOLDS = [
        'optimal' => 1,
        'aggressive' => 0.5,
        'conservative' => 2
    ];
    /**
     * Get detailed metadata information
     * 
     * @param string $key
     * @param array $data
     * @return array
     */
    private function getMetadataDetails(string $key, array $data): array
    {
        return match($key) {
            'title' => ['length' => strlen($data[$key] ?? ''), 'recommended' => '50-60 characters'],
            'description' => ['length' => strlen($data[$key] ?? ''), 'recommended' => '150-160 characters'],
            'image' => ['url' => $data[$key] ?? null],
            default => []
        };
    }

       /**
     * Extract RDFa properties
     * 
     * @param DOMElement $element
     * @return array
     */
    private function extractRdfaProperties(DOMElement $element): array
    {
        $properties = [];
        $xpath = new DOMXPath($element->ownerDocument);
        
        $props = $xpath->query('.//*[@property]', $element);
        foreach ($props as $prop) {
            if ($prop instanceof DOMElement) {
                $name = $prop->getAttribute('property');
                $value = $prop->getAttribute('content') ?: $prop->nodeValue;
                if ($value !== null) {
                    $properties[$name] = $value;
                }
            }
        }
        
        return $properties;
    }

    /**
     * Get Microdata value
     * 
     * @param DOMElement $element
     * @return string|null
     */
    private function getMicrodataValue(DOMElement $element): ?string
    {
        if ($element->hasAttribute('content')) {
            return $element->getAttribute('content');
        }
        
        if ($element->tagName === 'meta') {
            return $element->getAttribute('content');
        }
        
        if ($element->tagName === 'img') {
            return $element->getAttribute('src');
        }
        
        if ($element->tagName === 'a') {
            return $element->getAttribute('href');
        }
        
        if ($element->tagName === 'time') {
            return $element->getAttribute('datetime') ?: $element->nodeValue;
        }
        
        return $element->nodeValue ?: null;
    }


    /**
     * Detect page language
     * 
     * @param string $html
     * @return string|null
     */
    private function detectLanguage(string $html): ?string
    {
        $doc = new DOMDocument();
        $doc->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'), LIBXML_NOERROR);
        
        $htmlTag = $doc->getElementsByTagName('html')->item(0);
        if ($htmlTag instanceof DOMElement) {
            return $htmlTag->getAttribute('lang') ?: null;
        }
        
        return null;
    }

    /**
     * Extract structured data
     * 
     * @param string $html
     * @return array
     */
    private function extractStructuredData(string $html): array
    {
        $structuredData = [];
        
        $doc = new DOMDocument();
        $doc->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'), LIBXML_NOERROR);
        
        $xpath = new DOMXPath($doc);
        
        // Extract Microdata
        $itemscopes = $xpath->query('//*[@itemscope]');
        foreach ($itemscopes as $scope) {
            if ($scope instanceof DOMElement) {
                $type = $scope->getAttribute('itemtype');
                $structuredData['microdata'][] = [
                    'type' => $type,
                    'properties' => $this->extractMicrodataProperties($scope)
                ];
            }
        }
        
        // Extract RDFa
        $rdfas = $xpath->query('//*[@typeof]');
        foreach ($rdfas as $rdfa) {
            if ($rdfa instanceof DOMElement) {
                $type = $rdfa->getAttribute('typeof');
                $structuredData['rdfa'][] = [
                    'type' => $type,
                    'properties' => $this->extractRdfaProperties($rdfa)
                ];
            }
        }
        
        return $structuredData;
    }

    /**
     * Extract Microdata properties
     * 
     * @param DOMElement $scope
     * @return array
     */
    private function extractMicrodataProperties(DOMElement $scope): array
    {
        $properties = [];
        $xpath = new DOMXPath($scope->ownerDocument);
        
        $props = $xpath->query('.//*[@itemprop]', $scope);
        foreach ($props as $prop) {
            if ($prop instanceof DOMElement) {
                $name = $prop->getAttribute('itemprop');
                $value = $this->getMicrodataValue($prop);
                if ($value !== null) {
                    $properties[$name] = $value;
                }
            }
        }
        
        return $properties;
    }

        /**
     * Get canonical URL
     * 
     * @param string $html
     * @return string|null
     */
    private function getCanonicalUrl(string $html): ?string
    {
        $doc = new DOMDocument();
        $doc->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'), LIBXML_NOERROR);
        
        $xpath = new DOMXPath($doc);
        $links = $xpath->query('//link[@rel="canonical"]');

        if ($links && $links->length > 0) {
            $node = $links->item(0);
            if ($node instanceof DOMElement) {
                return $node->getAttribute('href');
            }
        }

        return null;
    }


    /**
     * Get favicon URL
     * 
     * @param string $html
     * @param string $baseUrl
     * @return string|null
     */
    private function getFavicon(string $html, string $baseUrl): ?string
    {
        $doc = new DOMDocument();
        $doc->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'), LIBXML_NOERROR);
        
        $xpath = new DOMXPath($doc);
        $links = $xpath->query('//link[@rel="icon" or @rel="shortcut icon" or @rel="apple-touch-icon"]');

        if ($links && $links->length > 0) {
            $node = $links->item(0);
            if ($node instanceof DOMElement) {
                $href = $node->getAttribute('href');
                if (str_starts_with($href, '/')) {
                    return rtrim($baseUrl, '/') . $href;
                }
                return $href;
            }
        }

        // Try default favicon location
        return rtrim($baseUrl, '/') . '/favicon.ico';
    }

    private function extractHostDirectives(string $robotsTxt): array
    {
        $directives = [];
        $lines = explode("\n", $robotsTxt);

        foreach ($lines as $line) {
            $line = trim($line);
            foreach (self::HOST_DIRECTIVE_TYPES as $type) {
                if (preg_match("/^{$type}:\s*(.+)$/i", $line, $matches)) {
                    $directives[] = $matches[1];
                }
            }
        }

        return array_unique($directives);
    }


    private function analyzeCrawlRates(array $userAgents): array
    {
        $analysis = [];
        
        foreach ($userAgents as $agent => $rules) {
            $crawlDelay = $rules['crawl-delay'] ?? null;
            $requestRate = $rules['request-rate'] ?? null;
            $visitTime = $rules['visit-time'] ?? null;

            $status = 'optimal';
            if ($crawlDelay && $crawlDelay > self::CRAWL_RATE_THRESHOLDS['conservative']) {
                $status = 'conservative';
            } elseif ($crawlDelay && $crawlDelay < self::CRAWL_RATE_THRESHOLDS['aggressive']) {
                $status = 'aggressive';
            }

            $analysis[$agent] = [
                'crawl_delay' => $crawlDelay,
                'request_rate' => $requestRate,
                'visit_time' => $visitTime,
                'status' => $status
            ];
        }

        return $analysis;
    }
    
 
}
