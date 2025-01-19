<?php

namespace Shaferllc\Analytics\Concerns;

use DOMDocument;
use DOMXPath;

class StructuredData
{

    /**
     * Analyze Schema.org markup in the document
     *
     * @param DOMDocument $document
     * @return array
     */
    public function analyzeSchemaMarkup(DOMDocument $document): array
    {
        $xpath = new DOMXPath($document);
        $schemaElements = [];

        // Find elements with itemscope attribute
        $itemscopes = $xpath->query('//*[@itemscope]');
        foreach ($itemscopes as $element) {
            $type = $element->getAttribute('itemtype');
            if (!empty($type)) {
                $schemaElements[] = [
                    'type' => $type,
                    'properties' => $this->extractItemProps($xpath, $element)
                ];
            }
        }

        // Find elements with schema.org vocabulary in itemtype
        $schemaTypes = $xpath->query('//*[contains(@itemtype, "schema.org")]');
        foreach ($schemaTypes as $element) {
            if (!$element->hasAttribute('itemscope')) {
                $schemaElements[] = [
                    'type' => $element->getAttribute('itemtype'),
                    'properties' => $this->extractItemProps($xpath, $element)
                ];
            }
        }

        return [
            'count' => count($schemaElements),
            'types' => array_unique(array_column($schemaElements, 'type')),
            'elements' => $schemaElements,
            'issues' => $this->validateSchemaMarkup($schemaElements)
        ];
    }

    /**
     * Extract itemprop values from an element
     *
     * @param DOMXPath $xpath
     * @param \DOMElement $element
     * @return array
     */
    private function extractItemProps(DOMXPath $xpath, \DOMElement $element): array
    {
        $properties = [];
        $itemprops = $xpath->query('.//*[@itemprop]', $element);

        foreach ($itemprops as $prop) {
            $name = $prop->getAttribute('itemprop');
            $value = $prop->getAttribute('content') ?: $prop->textContent;
            $properties[$name] = trim($value);
        }

        return $properties;
    }

    /**
     * Validate schema markup for common issues
     *
     * @param array $schemas
     * @return array
     */
    private function validateSchemaMarkup(array $schemas): array
    {
        $issues = [];

        foreach ($schemas as $schema) {
            if (empty($schema['properties'])) {
                $issues[] = "Schema type '{$schema['type']}' has no properties defined";
            }

            if (!str_contains($schema['type'], 'schema.org')) {
                $issues[] = "Schema type '{$schema['type']}' does not use schema.org vocabulary";
            }
        }

        return $issues;
    }


    public function analyzeMicrodata(DOMDocument $domDocument): array
    {
        $microdata = [
            'itemscope' => [],
            'itemtype' => [],
            'itemprop' => []
        ];

        $elements = $domDocument->getElementsByTagName('*');
        foreach ($elements as $element) {
            // Check for itemscope
            if ($element->hasAttribute('itemscope')) {
                $type = $element->getAttribute('itemtype') ?: 'unspecified';
                $microdata['itemscope'][] = [
                    'type' => $type,
                    'path' => $element->getNodePath()
                ];
            }

            // Track itemtype usage
            if ($element->hasAttribute('itemtype')) {
                $type = $element->getAttribute('itemtype');
                if (!isset($microdata['itemtype'][$type])) {
                    $microdata['itemtype'][$type] = 0;
                }
                $microdata['itemtype'][$type]++;
            }

            // Track itemprop usage
            if ($element->hasAttribute('itemprop')) {
                $prop = $element->getAttribute('itemprop');
                if (!isset($microdata['itemprop'][$prop])) {
                    $microdata['itemprop'][$prop] = 0;
                }
                $microdata['itemprop'][$prop]++;
            }
        }

        return $microdata;
    }


    public function extractJsonLD(DOMDocument $domDocument): array
    {
        $jsonLdData = [];
        $scripts = $domDocument->getElementsByTagName('script');

        foreach ($scripts as $script) {
            if ($script->getAttribute('type') === 'application/ld+json') {
                try {
                    $content = trim($script->textContent);
                    $decoded = json_decode($content, true);
                    
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $jsonLdData[] = [
                            'content' => $decoded,
                            'path' => $script->getNodePath()
                        ];
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }
        }

        return [
            'count' => count($jsonLdData),
            'items' => $jsonLdData
        ];
    }



}
