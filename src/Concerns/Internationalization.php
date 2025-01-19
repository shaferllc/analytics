<?php

namespace Shaferllc\Analytics\Concerns;

use DOMNode;
use DOMXPath;
use DOMElement;
use DOMDocument;

class Internationalization
{
    /**
     * Finds language-related tags and attributes in the HTML document
     */
    public function findLanguageTags(DOMDocument $domDocument): array
    {
        $languageInfo = [
            'html_lang' => null,
            'hreflang_links' => [],
            'has_language_switcher' => false,
            'detected_languages' => [],
            'w3c_references' => [
                'html_lang' => 'https://www.w3.org/International/questions/qa-html-language-declarations',
                'hreflang' => 'https://www.w3.org/International/questions/qa-hreflang',
                'language_switcher' => 'https://www.w3.org/International/articles/language-switchers/'
            ],
            'examples' => [
                'html_lang' => '<html lang="en">',
                'hreflang' => '<link rel="alternate" hreflang="fr" href="https://example.com/fr/">',
                'language_switcher' => '<nav class="language-switcher"><a href="/en">English</a></nav>'
            ]
        ];

        $this->extractHtmlLang($domDocument, $languageInfo);
        $this->extractHreflangLinks($domDocument, $languageInfo);
        $this->detectLanguageSwitcher($domDocument, $languageInfo);

        $languageInfo['detected_languages'] = array_unique($languageInfo['detected_languages']);

        return [
            'has_language_tags' => !empty($languageInfo['html_lang']) || !empty($languageInfo['hreflang_links']),
            'primary_language' => $languageInfo['html_lang'],
            'alternate_languages' => $languageInfo['hreflang_links'],
            'has_language_switcher' => $languageInfo['has_language_switcher'],
            'detected_languages' => $languageInfo['detected_languages'],
            'total_languages' => count($languageInfo['detected_languages']),
            'w3c_references' => $languageInfo['w3c_references'],
            'examples' => $languageInfo['examples']
        ];
    }

    private function extractHtmlLang(DOMDocument $domDocument, array &$languageInfo): void 
    {
        $html = $domDocument->getElementsByTagName('html')->item(0);
        if ($html instanceof DOMElement && $html->hasAttribute('lang')) {
            $lang = $html->getAttribute('lang');
            $languageInfo['html_lang'] = $lang;
            $languageInfo['detected_languages'][] = $lang;
        }
    }

    private function extractHreflangLinks(DOMDocument $domDocument, array &$languageInfo): void
    {
        foreach ($domDocument->getElementsByTagName('link') as $link) {
            if (!$link instanceof DOMElement) {
                continue;
            }

            if ($link->getAttribute('rel') === 'alternate' && $link->hasAttribute('hreflang')) {
                $hreflang = $link->getAttribute('hreflang');
                $languageInfo['hreflang_links'][] = [
                    'href' => $link->getAttribute('href'),
                    'hreflang' => $hreflang
                ];
                $languageInfo['detected_languages'][] = $hreflang;
            }
        }
    }

    private function detectLanguageSwitcher(DOMDocument $domDocument, array &$languageInfo): void
    {
        foreach ($domDocument->getElementsByTagName('*') as $element) {
            if (!$element instanceof DOMElement) {
                continue;
            }
            
            $identifier = $element->getAttribute('class') . ' ' . $element->getAttribute('id');
            if (preg_match('/(language|lang)[-_]?(switcher|selector|menu)/i', $identifier)) {
                $languageInfo['has_language_switcher'] = true;
                break;
            }
        }
    }

    /**
     * Analyzes hreflang tags implementation
     */
    public function analyzeHreflangTags(DOMDocument $domDocument): array
    {
        $hreflangTags = [];
        $issues = [];
        $hasXDefault = false;
        
        foreach ($domDocument->getElementsByTagName('link') as $link) {
            if (!$this->isValidHreflangLink($link)) {
                continue;
            }

            $href = $link->getAttribute('href');
            $hreflang = $link->getAttribute('hreflang');
            $path = $link->getNodePath();
            
            $hreflangTags[] = compact('href', 'hreflang', 'path');
            
            if ($hreflang === 'x-default') {
                $hasXDefault = true;
            }

            $this->validateHreflangTag($href, $hreflang, $path, $issues);
        }
        
        if (!$hasXDefault && $hreflangTags) {
            $issues[] = [
                'type' => 'missing_x_default',
                'description' => 'No x-default hreflang tag found'
            ];
        }
        
        $recommendations = [
            [
                'text' => 'Include x-default hreflang tag for language/region selector page',
                'example' => '<link rel="alternate" href="https://example.com/" hreflang="x-default">',
                'w3c_reference' => 'https://www.w3.org/International/questions/qa-hreflang-default'
            ],
            [
                'text' => 'Use properly formatted language and region codes',
                'example' => '<link rel="alternate" href="https://example.com/fr" hreflang="fr">',
                'w3c_reference' => 'https://www.w3.org/International/articles/language-tags/'
            ],
            [
                'text' => 'Ensure all hreflang tags have valid href attributes',
                'example' => '<link rel="alternate" href="https://example.com/de" hreflang="de">',
                'w3c_reference' => 'https://www.w3.org/International/questions/qa-hreflang-validation'
            ],
            [
                'text' => 'Implement bidirectional linking between language versions',
                'example' => '<!-- On English page -->\n<link rel="alternate" href="/fr" hreflang="fr">\n<!-- On French page -->\n<link rel="alternate" href="/" hreflang="en">',
                'w3c_reference' => 'https://www.w3.org/International/questions/qa-hreflang-bi-directional'
            ]
        ];

        return [
            'tags' => $hreflangTags,
            'issues' => $issues,
            'total_tags' => count($hreflangTags),
            'has_x_default' => $hasXDefault,
            'languages' => array_unique(array_column($hreflangTags, 'hreflang')),
            'recommendations' => $recommendations
        ];
    }

    private function isValidHreflangLink(DOMElement $link): bool
    {
        return $link instanceof DOMElement 
            && $link->getAttribute('rel') === 'alternate' 
            && $link->hasAttribute('hreflang');
    }

    /**
     * Analyzes text direction and RTL support
     */
    public function analyzeTextDirection(DOMDocument $domDocument): array
    {
        $directionInfo = [
            'html_dir' => null,
            'rtl_elements' => [],
            'mixed_direction' => false,
            'has_rtl_support' => false,
            'rtl_styles' => [],
            'bidi_elements' => [],
            'direction_attributes' => []
        ];

        // Example: <html dir="rtl">
        $html = $domDocument->getElementsByTagName('html')->item(0);
        if ($html instanceof DOMElement && $html->hasAttribute('dir')) {
            $directionInfo['html_dir'] = $html->getAttribute('dir');
            $directionInfo['has_rtl_support'] = $directionInfo['html_dir'] === 'rtl';
        }

        // Example: <link rel="stylesheet" href="styles-rtl.css">
        foreach ($domDocument->getElementsByTagName('link') as $link) {
            if ($link instanceof DOMElement && 
                $link->getAttribute('rel') === 'stylesheet' &&
                preg_match('/(rtl|right-to-left)/i', $link->getAttribute('href'))) {
                $directionInfo['rtl_styles'][] = $link->getAttribute('href');
                $directionInfo['has_rtl_support'] = true;
            }
        }

        foreach ($domDocument->getElementsByTagName('*') as $element) {
            if (!$element instanceof DOMElement) {
                continue;
            }

            $this->checkElementDirection($element, $directionInfo);
            
            if (!$directionInfo['has_rtl_support']) {
                $directionInfo['has_rtl_support'] = $this->hasRtlClasses($element);
            }

            // Example: <p dir="rtl">Arabic text</p>
            if ($element->hasAttribute('dir')) {
                $directionInfo['direction_attributes'][] = [
                    'element' => $element->tagName,
                    'dir' => $element->getAttribute('dir'),
                    'path' => $element->getNodePath()
                ];
            }

            // Example: <p>Mixed العربية and English text</p>
            if (preg_match('/[\x{0590}-\x{05FF}\x{0600}-\x{06FF}]/u', $element->textContent)) {
                $directionInfo['bidi_elements'][] = [
                    'element' => $element->tagName,
                    'text' => substr($element->textContent, 0, 100),
                    'path' => $element->getNodePath()
                ];
            }
        }

        $recommendations = [
            [
                'text' => 'Set document-level text direction using HTML dir attribute',
                'reference' => 'https://www.w3.org/International/questions/qa-html-dir',
                'example' => '<html dir="rtl">'
            ],
            [
                'text' => 'Use CSS logical properties for RTL support',
                'reference' => 'https://www.w3.org/International/articles/inline-bidi-markup/',
                'example' => 'margin-inline-start: 1em; /* Instead of margin-left */'
            ],
            [
                'text' => 'Test layout with RTL languages',
                'reference' => 'https://www.w3.org/International/techniques/authoring-html#direction',
                'example' => '<div class="container" dir="rtl">محتوى عربي</div>'
            ],
            [
                'text' => 'Ensure proper handling of bidirectional text',
                'reference' => 'https://www.w3.org/International/articles/inline-bidi-markup/',
                'example' => '<span dir="rtl">العربية</span> mixed with English'
            ],
            [
                'text' => 'Include language-specific stylesheets for RTL support',
                'reference' => 'https://www.w3.org/International/questions/qa-scripts',
                'example' => '<link rel="stylesheet" href="styles-rtl.css">'
            ],
            [
                'text' => 'Use Unicode bidirectional algorithm markers when needed',
                'reference' => 'https://www.w3.org/International/articles/unicode-bidirectional/',
                'example' => '&#x202B;RTL text&#x202C;'
            ],
            [
                'text' => 'Test with mixed LTR/RTL content',
                'reference' => 'https://www.w3.org/International/articles/bidi-markup/',
                'example' => '<p dir="ltr">English <span dir="rtl">العربية</span> mixed</p>'
            ]
        ];
        
        if (!$directionInfo['has_rtl_support']) {
            array_unshift($recommendations, [
                'text' => 'Enable RTL support using HTML dir attribute',
                'reference' => 'https://developer.mozilla.org/en-US/docs/Web/HTML/Global_attributes/dir',
                'example' => '<html dir="rtl" lang="ar">'
            ]);
        }

        if (count($directionInfo['bidi_elements']) > 0) {
            $recommendations[] = [
                'text' => 'Review bidirectional text handling in identified elements',
                'reference' => 'https://www.w3.org/International/tutorials/bidi-xhtml/',
                'example' => '<p dir="auto">Mixed العربية and English</p>'
            ];
        }

        return [
            'document_direction' => $directionInfo['html_dir'] ?? 'ltr',
            'rtl_elements' => $directionInfo['rtl_elements'],
            'has_mixed_directions' => $directionInfo['mixed_direction'],
            'has_rtl_support' => $directionInfo['has_rtl_support'],
            'total_rtl_elements' => count($directionInfo['rtl_elements']),
            'rtl_stylesheets' => $directionInfo['rtl_styles'],
            'bidirectional_elements' => $directionInfo['bidi_elements'],
            'direction_attributes' => $directionInfo['direction_attributes'],
            'total_bidi_elements' => count($directionInfo['bidi_elements']),
            'recommendations' => $recommendations
        ];
    }


    /**
     * Analyzes character encoding settings
     */
    public function analyzeCharacterEncoding(DOMDocument $domDocument): array
    {
        $encodingInfo = [
            'meta_charset' => null,
            'content_type_charset' => null,
            'xml_encoding' => null,
            'bom_detected' => false,
            'issues' => [],
            'warnings' => [],
        ];

        // Check XML encoding declaration
        $encodingInfo['xml_encoding'] = $domDocument->xmlEncoding;

        // Check for BOM
        $this->detectBOM($domDocument, $encodingInfo);

        // Check meta tags
        foreach ($domDocument->getElementsByTagName('meta') as $meta) {
            if (!$meta instanceof DOMElement) {
                continue;
            }

            $lineNumber = $this->getLineNumber($meta);

            if ($meta->hasAttribute('charset')) {
                $encodingInfo['meta_charset'] = $meta->getAttribute('charset');
                
                // Validate charset value
                if (!$this->isValidCharset($encodingInfo['meta_charset'])) {
                    $encodingInfo['warnings'][] = [
                        'type' => 'invalid_charset_value',
                        'charset' => $encodingInfo['meta_charset'],
                        'description' => 'Invalid or unsupported character encoding specified',
                        'line' => $lineNumber,
                        'example' => '<meta charset="UTF-8">',
                        'reference' => 'https://www.w3.org/International/questions/qa-choosing-encodings'
                    ];
                }
                continue;
            }
            
            if (strtolower($meta->getAttribute('http-equiv')) === 'content-type') {
                if (preg_match('/charset=([^;\s]+)/i', $meta->getAttribute('content'), $matches)) {
                    $encodingInfo['content_type_charset'] = trim($matches[1]);
                }
            }

            // Check for pragma directive
            if (strtolower($meta->getAttribute('http-equiv')) === 'pragma' && 
                stripos($meta->getAttribute('content'), 'charset') !== false) {
                $encodingInfo['warnings'][] = [
                    'type' => 'deprecated_pragma',
                    'description' => 'Deprecated charset declaration using pragma directive',
                    'line' => $lineNumber,
                    'example' => '<meta charset="UTF-8"> <!-- Use this instead -->',
                    'reference' => 'https://www.w3.org/International/tutorials/tutorial-char-enc/'
                ];
            }
        }

        // Check for missing charset declaration
        $html = $domDocument->getElementsByTagName('html')->item(0);
        $htmlLineNumber = $html ? $this->getLineNumber($html) : 0;
        
        if (!$encodingInfo['meta_charset'] && !$encodingInfo['content_type_charset']) {
            $encodingInfo['issues'][] = [
                'type' => 'missing_charset',
                'description' => 'No character encoding declaration found',
                'impact' => 'high',
                'suggestion' => 'Add <meta charset="UTF-8"> in the document head',
                'example' => '<!DOCTYPE html>\n<html>\n<head>\n  <meta charset="UTF-8">\n  <title>Page Title</title>\n</head>',
                'reference' => 'https://www.w3.org/International/questions/qa-html-encoding-declarations',
                'line' => $htmlLineNumber
            ];
        }

        // Check for charset position in document
        if ($encodingInfo['meta_charset']) {
            $this->validateCharsetPosition($domDocument, $encodingInfo);
        }

        // Check for conflicting declarations
        $this->checkConflictingDeclarations($encodingInfo);

        $result = [
            'charset_meta' => $encodingInfo['meta_charset'],
            'content_type_charset' => $encodingInfo['content_type_charset'],
            'xml_encoding' => $encodingInfo['xml_encoding'],
            'declared_encoding' => $domDocument->encoding,
            'bom_detected' => $encodingInfo['bom_detected'],
            'has_charset_declaration' => !empty($encodingInfo['meta_charset']) || !empty($encodingInfo['content_type_charset']),
            'issues' => $encodingInfo['issues'],
            'warnings' => $encodingInfo['warnings']
        ];

        if (!empty($encodingInfo['issues']) || !empty($encodingInfo['warnings'])) {
            $result['recommendations'] = [
                [
                    'text' => 'Always declare character encoding using <meta charset="UTF-8"> tag',
                    'reference' => 'https://www.w3.org/International/questions/qa-html-encoding-declarations'
                ],
                [
                    'text' => 'Place charset declaration within the first 1024 bytes of the document',
                    'reference' => 'https://www.w3.org/International/tutorials/tutorial-char-enc/#placement'
                ],
                [
                    'text' => 'Use UTF-8 encoding for best international compatibility',
                    'reference' => 'https://www.w3.org/International/questions/qa-choosing-encodings'
                ],
                [
                    'text' => 'Ensure consistent encoding across all declarations',
                    'reference' => 'https://www.w3.org/International/getting-started/characters'
                ],
                [
                    'text' => 'Avoid using deprecated charset declaration methods',
                    'reference' => 'https://www.w3.org/International/questions/qa-meta-charset'
                ],
                [
                    'text' => 'Consider adding appropriate byte order mark (BOM) for UTF encodings',
                    'reference' => 'https://www.w3.org/International/questions/qa-byte-order-mark'
                ]
            ];
        }

        return $result;
    }

    private function getLineNumber(DOMNode $node): int 
    {
        return $node->getLineNo();
    }

    private function isValidCharset(string $charset): bool
    {
        $validCharsets = [
            'utf-8', 'utf8', 'iso-8859-1', 'iso-8859-15', 
            'windows-1252', 'ascii', 'us-ascii'
        ];
        return in_array(strtolower($charset), $validCharsets);
    }

    private function detectBOM(DOMDocument $domDocument, array &$encodingInfo): void
    {
        if ($domDocument->textContent) {
            $firstBytes = substr($domDocument->textContent, 0, 4);
            $encodingInfo['bom_detected'] = (
                strpos($firstBytes, "\xEF\xBB\xBF") === 0 || // UTF-8
                strpos($firstBytes, "\xFF\xFE") === 0 || // UTF-16LE
                strpos($firstBytes, "\xFE\xFF") === 0 // UTF-16BE
            );
        }
    }

    private function validateCharsetPosition(DOMDocument $domDocument, array &$encodingInfo): void
    {
        $head = $domDocument->getElementsByTagName('head')->item(0);
        if ($head && $head->firstChild) {
            $firstMetaCharset = false;
            foreach ($head->childNodes as $index => $node) {
                if ($node instanceof DOMElement && 
                    $node->tagName === 'meta' && 
                    $node->hasAttribute('charset')) {
                    $firstMetaCharset = $index <= 1;
                    $lineNumber = $this->getLineNumber($node);
                    break;
                }
            }
            
            if (!$firstMetaCharset) {
                $encodingInfo['warnings'][] = [
                    'type' => 'charset_position',
                    'description' => 'Character encoding declaration should appear as early as possible in the document head',
                    'line' => $lineNumber ?? 0
                ];
            }
        }
    }

    private function checkConflictingDeclarations(array &$encodingInfo): void
    {
        $declarations = array_filter([
            $encodingInfo['meta_charset'],
            $encodingInfo['content_type_charset'],
            $encodingInfo['xml_encoding']
        ]);
        
        $uniqueDeclarations = array_unique(array_map('strtolower', $declarations));
        
        if (count($declarations) > count($uniqueDeclarations)) {
            $encodingInfo['issues'][] = [
                'type' => 'conflicting_charset',
                'description' => 'Conflicting character encoding declarations found',
                'declarations' => $declarations,
                'impact' => 'high'
            ];
        }
    }

    private function validateHreflangTag(string $href, string $hreflang, string $path, array &$issues): void
    {
        if (empty($href)) {
            $issues[] = [
                'type' => 'missing_href',
                'hreflang' => $hreflang,
                'path' => $path,
                'description' => 'Hreflang tag missing href attribute'
            ];
        }
        
        if (empty($hreflang)) {
            $issues[] = [
                'type' => 'invalid_hreflang',
                'href' => $href,
                'path' => $path,
                'description' => 'Empty or invalid hreflang value'
            ];
        } elseif (!preg_match('/^[a-z]{2}(-[A-Z]{2})?$/', $hreflang)) {
            $issues[] = [
                'type' => 'malformed_language_code',
                'hreflang' => $hreflang,
                'path' => $path,
                'description' => 'Malformed language code in hreflang attribute'
            ];
        }
    }

    private function checkElementDirection(DOMElement $element, array &$directionInfo): void
    {
        if (!$element->hasAttribute('dir')) {
            return;
        }

        $dir = $element->getAttribute('dir');
        if ($dir === 'rtl') {
            $directionInfo['rtl_elements'][] = [
                'element' => $element->tagName,
                'path' => $element->getNodePath(),
                'text' => $element->textContent
            ];
        }
        
        if ($directionInfo['html_dir'] && $dir !== $directionInfo['html_dir']) {
            $directionInfo['mixed_direction'] = true;
        }
    }

    private function hasRtlClasses(DOMElement $element): bool
    {
        if (!$element->hasAttribute('class')) {
            return false;
        }

        return (bool) preg_match('/(^|\s)(rtl|text-right|direction-rtl)($|\s)/i', $element->getAttribute('class'));
    }


    /**
     * Finds localized content elements in the document
     *
     * @param DOMDocument $domDocument The DOM document to analyze
     * @return array Analysis of localized content including translated elements and potential issues
     */
    public function findLocalizedContent(DOMDocument $domDocument): array
    {
        $localizedContent = [];
        $issues = [];

        $this->findTranslateElements($domDocument, $localizedContent);
        $this->findTranslationPatterns($domDocument, $localizedContent);
        $this->checkUntranslatedContent($domDocument, $issues);

        return [
            'localized_elements' => $localizedContent,
            'total_localized' => count($localizedContent),
            'potential_issues' => $issues,
            'recommendations' => [
                'Use translate attribute for content that should/should not be translated',
                'Implement consistent translation markers (classes or data attributes)',
                'Ensure all user-facing text is properly marked for translation',
                'Consider using a translation management system'
            ]
        ];
    }

    private function findTranslateElements(DOMDocument $domDocument, array &$localizedContent): void
    {
        foreach ($domDocument->getElementsByTagName('*') as $element) {
            if ($element instanceof DOMElement && $element->hasAttribute('translate')) {
                $localizedContent[] = [
                    'element' => $element->nodeName,
                    'path' => $element->getNodePath(),
                    'translate' => $element->getAttribute('translate'),
                    'text' => substr(trim($element->textContent), 0, 100),
                    'example' => '<div translate="yes">Translate this content</div>',
                    'w3c_reference' => 'https://www.w3.org/International/questions/qa-translate-flag',
                    'w3c_examples' => [
                        '<p translate="yes">This text should be translated</p>',
                        '<span translate="no">Keep this text as-is</span>',
                        '<div translate>Default translatable content</div>'
                    ]
                ];
            }
        }
    }

    private function findTranslationPatterns(DOMDocument $domDocument, array &$localizedContent): void
    {
        foreach ($domDocument->getElementsByTagName('*') as $element) {
            if (!$element instanceof DOMElement) {
                continue;
            }
            
            $class = $element->getAttribute('class');
            $dataAttrs = $this->extractDataAttributes($element);

            if ($this->hasTranslationMarkers($class, $dataAttrs)) {
                $localizedContent[] = [
                    'element' => $element->nodeName,
                    'path' => $element->getNodePath(),
                    'class' => $class,
                    'data_attributes' => $dataAttrs,
                    'text' => substr(trim($element->textContent), 0, 100),
                    'example' => '<div class="i18n">Translated text</div>',
                    'w3c_reference' => 'https://www.w3.org/International/questions/qa-translate-flag',
                    'w3c_examples' => [
                        '<div class="translate">Text to translate</div>',
                        '<span data-i18n="key">Localized content</span>',
                        '<p data-translate>Translatable paragraph</p>'
                    ]
                ];
            }
        }
    }

    private function extractDataAttributes(DOMElement $element): array
    {
        $dataAttrs = [];
        foreach ($element->attributes as $attr) {
            if (strpos($attr->nodeName, 'data-') === 0) {
                $dataAttrs[$attr->nodeName] = $attr->nodeValue;
            }
        }
        return $dataAttrs;
    }

    private function hasTranslationMarkers(string $class, array $dataAttrs): bool
    {
        return preg_match('/(i18n|translate|localize)/i', $class) 
            || array_key_exists('data-i18n', $dataAttrs)
            || array_key_exists('data-translate', $dataAttrs);
    }

    private function checkUntranslatedContent(DOMDocument $domDocument, array &$issues): void
    {
        $textNodes = $this->getTextNodes($domDocument->documentElement);
        foreach ($textNodes as $node) {
            $text = trim($node->nodeValue);
            if (strlen($text) > 20 && !$this->hasTranslationParent($node)) {
                $issues[] = [
                    'type' => 'potential_untranslated',
                    'description' => 'Found potentially untranslated content that should be marked for translation',
                    'text' => substr($text, 0, 100),
                    'path' => $this->getNodePath($node),
                    'length' => strlen($text),
                    'example' => '<p translate="yes">This text needs translation</p>',
                    'w3c_reference' => 'https://www.w3.org/International/questions/qa-translate-flag'
                ];
            }
        }
    }

    /**
     * Checks if a node has a parent element with translation markers
     */
    private function hasTranslationParent(DOMNode $node): bool
    {
        $parent = $node->parentNode;
        while ($parent && $parent instanceof DOMElement) {
            if ($parent->hasAttribute('translate') ||
                preg_match('/(i18n|translate|localize)/i', $parent->getAttribute('class')) ||
                $parent->hasAttribute('data-i18n') ||
                $parent->hasAttribute('data-translate')) {
                return true;
            }
            $parent = $parent->parentNode;
        }
        return false;
    }

    /**
     * Gets the node path for a text node
     */
    private function getNodePath(DOMNode $node): string
    {
        if ($node->parentNode) {
            return $node->parentNode->getNodePath() . '/text()';
        }
        return '/text()';
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

    public function analyzeLanguageDeclaration(DOMDocument $domDocument): array
    {
        $issues = [];
        $details = [];
        $langElements = [];

        // Check HTML lang attribute
        // Example: <html lang="en">
        $html = $domDocument->getElementsByTagName('html')->item(0);
        $htmlLang = $html ? $html->getAttribute('lang') : null;

        if (!$htmlLang) {
            $issues[] = [
                'type' => 'missing_html_lang',
                'recommendation' => 'Add lang attribute to HTML element',
                'reference' => 'https://www.w3.org/International/questions/qa-html-language-declarations',
                'example' => '<html lang="en">'
            ];
            $details[] = 'Missing language declaration on HTML element';
        } else {
            $langElements[] = [
                'element' => 'html',
                'lang' => $htmlLang
            ];
        }

        // Check for elements with different languages
        // Example: <p lang="es">¡Hola Mundo!</p>
        $xpath = new DOMXPath($domDocument);
        $elementsWithLang = $xpath->query('//*[@lang]');

        foreach ($elementsWithLang as $element) {
            if (!$element instanceof DOMElement) {
                continue;
            }
            
            $lang = $element->getAttribute('lang');
            if ($lang !== $htmlLang) {
                $langElements[] = [
                    'element' => $element->nodeName,
                    'lang' => $lang,
                    'path' => $element->getNodePath()
                ];
            }
        }

        // Check meta content language
        // Example: <meta http-equiv="content-language" content="en">
        $metaLang = $xpath->query('//meta[@http-equiv="content-language"]');
        if ($metaLang->length > 0) {
            $contentLang = $metaLang->item(0)->getAttribute('content');
            $langElements[] = [
                'element' => 'meta',
                'lang' => $contentLang,
                'type' => 'http-equiv'
            ];

            if ($htmlLang && $contentLang !== $htmlLang) {
                $issues[] = [
                    'type' => 'inconsistent_language',
                    'recommendation' => 'Ensure consistent language declaration across HTML and meta tags',
                    'reference' => 'https://www.w3.org/International/questions/qa-html-language-declarations#inconsistent',
                    'example' => '<html lang="en">\n<meta http-equiv="content-language" content="en">'
                ];
                $details[] = 'Inconsistent language declarations found';
            }
        }

        // Check for language direction
        // Example: <html lang="ar" dir="rtl">
        if ($html instanceof DOMElement && !$html->hasAttribute('dir')) {
            $issues[] = [
                'type' => 'missing_direction',
                'recommendation' => 'Add dir attribute to HTML element for text direction',
                'reference' => 'https://www.w3.org/International/questions/qa-html-dir',
                'example' => '<html lang="ar" dir="rtl">'
            ];
            $details[] = 'Missing text direction declaration';
        }

        // Check for valid BCP 47 language codes
        // Examples: "en", "en-US", "zh-Hans"
        if ($htmlLang && !preg_match('/^[a-zA-Z]{2,3}(-[a-zA-Z]{2,4})?$/', $htmlLang)) {
            $issues[] = [
                'type' => 'invalid_language_code',
                'recommendation' => 'Use valid BCP 47 language codes',
                'reference' => 'https://www.w3.org/International/articles/language-tags/',
                'examples' => ['en', 'en-US', 'zh-Hans']
            ];
            $details[] = 'Invalid language code format detected';
        }

        // Check for language-specific meta tags
        // Example: <meta name="description" lang="en" content="Page description">
        $metaDescriptions = $xpath->query('//meta[@name="description"]');
        foreach ($metaDescriptions as $meta) {
            if (!$meta instanceof DOMElement || !$meta->hasAttribute('lang')) {
                $issues[] = [
                    'type' => 'missing_meta_lang',
                    'recommendation' => 'Add lang attribute to meta description for better localization',
                    'reference' => 'https://www.w3.org/International/questions/qa-meta-lang',
                    'example' => '<meta name="description" lang="en" content="Page description">'
                ];
                $details[] = 'Meta description missing language attribute';
            }
        }

        // Check for language-specific Open Graph tags
        // Example: <meta property="og:locale" content="en_US">
        $ogLocales = $xpath->query('//meta[@property="og:locale"]');
        if ($ogLocales->length === 0) {
            $issues[] = [
                'type' => 'missing_og_locale',
                'recommendation' => 'Add og:locale meta tag for social media sharing',
                'reference' => 'https://www.w3.org/International/questions/qa-social-media-tags',
                'example' => '<meta property="og:locale" content="en_US">'
            ];
            $details[] = 'Missing Open Graph locale declaration';
        }

        // Check for bi-directional text support
        // Example: <p dir="rtl">مرحبا بالعالم</p>
        $bidiElements = $xpath->query('//*[@dir]');
        if ($bidiElements->length === 0 && $htmlLang) {
            // Check if the language might need RTL support
            $rtlLanguages = ['ar', 'he', 'fa', 'ur'];
            $langPrefix = substr($htmlLang, 0, 2);
            if (in_array($langPrefix, $rtlLanguages)) {
                $issues[] = [
                    'type' => 'missing_rtl_support',
                    'recommendation' => 'Add RTL support for right-to-left language content',
                    'reference' => 'https://www.w3.org/International/questions/qa-scripts',
                    'example' => '<p dir="rtl">مرحبا بالعالم</p>'
                ];
                $details[] = 'Missing RTL directionality support';
            }
        }

        // Check for language-specific title tags
        // Example: <title lang="en">Page Title</title>
        $titles = $xpath->query('//title');
        if ($titles->length > 0 && !$titles->item(0)->parentNode->hasAttribute('lang')) {
            $issues[] = [
                'type' => 'missing_title_lang',
                'recommendation' => 'Add lang attribute to title element parent for better SEO',
                'reference' => 'https://www.w3.org/International/questions/qa-html-language-declarations#title',
                'example' => '<title lang="en">Page Title</title>'
            ];
            $details[] = 'Title element missing language context';
        }

        // Check for language annotations in text content
        // Example: <p lang="fr">Bonjour le monde</p>
        $textElements = $xpath->query('//text()[normalize-space()]');
        foreach ($textElements as $text) {
            if ($text->parentNode instanceof DOMElement && 
                $text->parentNode->nodeName !== 'script' && 
                $text->parentNode->nodeName !== 'style' &&
                !$text->parentNode->hasAttribute('lang')) {
                $issues[] = [
                    'type' => 'text_without_lang',
                    'path' => $this->getNodePath($text),
                    'recommendation' => 'Consider adding lang attributes to text-containing elements',
                    'reference' => 'https://www.w3.org/International/questions/qa-lang-why',
                    'example' => '<p lang="fr">Bonjour le monde</p>'
                ];
                $details[] = 'Text content found without language context';
                break; // Only add this issue once
            }
        }

        return [
            'passed' => !empty($htmlLang) && empty($issues),
            'importance' => 'high',
            'language_elements' => $langElements,
            'issues' => $issues,
            'details' => $details,
            'primary_language' => $htmlLang,
            'has_direction' => $html instanceof DOMElement && $html->hasAttribute('dir'),
            'bidi_support' => $bidiElements->length > 0,
            'meta_lang_support' => $metaDescriptions->length > 0,
            'og_locale_support' => $ogLocales->length > 0,
            'text_elements_analyzed' => $textElements->length,
            'total_lang_declarations' => count($langElements)
        ];
    }


   
    public function analyzeLanguageAlternates(DOMDocument $domDocument): array
    {
        $alternates = [];
        $issues = [];
        $details = [];
        $languages = [];

        // Check for alternate language links
        $xpath = new DOMXPath($domDocument);
        $altLinks = $xpath->query('//link[@rel="alternate"][@hreflang]');

        foreach ($altLinks as $link) {
            if (!$link instanceof DOMElement) {
                continue;
            }

            $hreflang = $link->getAttribute('hreflang');
            $href = $link->getAttribute('href');
            $path = $link->getNodePath();
            
            // Validate hreflang format
            if (!preg_match('/^[a-z]{2}(-[A-Z]{2})?|x-default$/', $hreflang)) {
                $issues[] = [
                    'type' => 'invalid_hreflang',
                    'value' => $hreflang,
                    'path' => $path,
                    'recommendation' => 'Use valid language codes in hreflang attributes',
                    'reference' => 'https://www.w3.org/International/articles/language-tags/',
                    'example' => '<link rel="alternate" hreflang="en-US" href="https://example.com/en-us">'
                ];
            }

            // Check for empty/invalid href
            if (empty($href)) {
                $issues[] = [
                    'type' => 'missing_href',
                    'hreflang' => $hreflang,
                    'path' => $path,
                    'recommendation' => 'Provide valid href for language alternate',
                    'reference' => 'https://www.w3.org/International/questions/qa-css-lang',
                    'example' => '<link rel="alternate" hreflang="es" href="https://example.com/es">'
                ];
            }
            
            $alternates[] = [
                'language' => $hreflang,
                'url' => $href,
                'path' => $path
            ];

            $languages[] = $hreflang;
        }

        // Check for x-default
        $hasDefaultLang = in_array('x-default', $languages);
        if (!$hasDefaultLang && !empty($alternates)) {
            $issues[] = [
                'type' => 'missing_default_language',
                'recommendation' => 'Add x-default hreflang tag for language fallback',
                'reference' => 'https://www.w3.org/International/questions/qa-hreflang-default',
                'example' => '<link rel="alternate" hreflang="x-default" href="https://example.com/">'
            ];
            $details[] = 'No x-default language alternate specified';
        }

        // Check for bidirectional linking
        $uniqueLanguages = array_unique($languages);
        if (count($uniqueLanguages) !== count($languages)) {
            $issues[] = [
                'type' => 'duplicate_languages',
                'recommendation' => 'Remove duplicate hreflang tags for the same language',
                'reference' => 'https://www.w3.org/International/questions/qa-duplicate-hreflang',
                'example' => '<!-- Incorrect -->
<link rel="alternate" hreflang="en" href="https://example.com/en">
<link rel="alternate" hreflang="en" href="https://example.com/english">

<!-- Correct -->
<link rel="alternate" hreflang="en" href="https://example.com/en">'
            ];
        }

        if (empty($alternates)) {
            $issues[] = [
                'type' => 'no_alternates',
                'recommendation' => 'Consider adding language alternates for multilingual support',
                'reference' => 'https://www.w3.org/International/questions/qa-when-hreflang',
                'example' => '<link rel="alternate" hreflang="en" href="https://example.com/en">
<link rel="alternate" hreflang="es" href="https://example.com/es">
<link rel="alternate" hreflang="fr" href="https://example.com/fr">'
            ];
            $details[] = 'No language alternates found';
        }

        // Check for self-referencing hreflang
        $html = $domDocument->getElementsByTagName('html')->item(0);
        if ($html instanceof DOMElement && $html->hasAttribute('lang')) {
            $documentLang = $html->getAttribute('lang');
            $hasDocumentLang = false;
            foreach ($languages as $lang) {
                if (strpos($lang, $documentLang) === 0) {
                    $hasDocumentLang = true;
                    break;
                }
            }
            if (!$hasDocumentLang && !empty($alternates)) {
                $issues[] = [
                    'type' => 'missing_self_reference',
                    'recommendation' => 'Add hreflang tag for document\'s primary language',
                    'reference' => 'https://www.w3.org/International/questions/qa-hreflang-self',
                    'example' => '<!-- For a French page -->
<link rel="alternate" hreflang="fr" href="https://example.com/fr">
<link rel="alternate" hreflang="en" href="https://example.com/en">
<link rel="alternate" hreflang="es" href="https://example.com/es">'
                ];
            }
        }

        $details = array_merge($details, [
            'Use hreflang attributes for language alternates',
            'Include x-default for language fallback',
            'Ensure bidirectional linking between alternates',
            'Use valid language codes in hreflang',
            'Include self-referencing hreflang tags',
            'Avoid duplicate language declarations',
            'Provide valid URLs in href attributes'
        ]);

        return [
            'passed' => !empty($alternates) && empty($issues),
            'importance' => 'medium',
            'alternates' => $alternates,
            'issues' => $issues,
            'details' => $details,
            'languages' => array_values($uniqueLanguages),
            'has_default' => $hasDefaultLang,
            'total_alternates' => count($alternates)
        ];
    }

    public function analyzeContentLocalization(DOMDocument $domDocument): array
    {
        $issues = [];
        $details = [];
        $localizationElements = [];
        $xpath = new DOMXPath($domDocument);

        // Check for numeric formats
        // Example: "1,234.56" vs "1.234,56"
        // W3C: https://www.w3.org/International/articles/language-tags/#numbers
        $numbers = $xpath->query('//text()[not(ancestor::script) and not(ancestor::style)][contains(.,",") or contains(.,".")]');
        foreach ($numbers as $element) {
            if (preg_match('/[\d,.]+/', $element->textContent)) {
                $localizationElements[] = [
                    'type' => 'number_format',
                    'content' => trim($element->textContent),
                    'path' => $element->getNodePath(),
                    'recommendation' => 'Consider using locale-specific number formatting',
                    'reference' => 'https://www.w3.org/International/articles/language-tags/#numbers',
                    'example' => 'US: 1,234.56 | DE: 1.234,56'
                ];
            }
        }

        // Check for date formats
        // Example: "2024-01-31" vs "31/01/2024"
        // W3C: https://www.w3.org/International/articles/language-tags/#dates
        $datePatterns = $xpath->query('//text()[not(ancestor::script) and not(ancestor::style)][contains(.,"/") or contains(.,"-")]');
        foreach ($datePatterns as $element) {
            if (preg_match('/\d{1,4}[-\/]\d{1,2}[-\/]\d{1,4}/', $element->textContent)) {
                $localizationElements[] = [
                    'type' => 'date_format',
                    'content' => trim($element->textContent),
                    'path' => $element->getNodePath(),
                    'recommendation' => 'Use ISO 8601 format (YYYY-MM-DD) or locale-specific date format',
                    'reference' => 'https://www.w3.org/International/articles/language-tags/#dates',
                    'example' => 'ISO: 2024-01-31 | US: 01/31/2024 | UK: 31/01/2024'
                ];
            }
        }

        // Check for currency symbols
        // Example: "$100" vs "100€"
        // W3C: https://www.w3.org/International/articles/language-tags/#currency
        $currencyElements = $xpath->query('//text()[not(ancestor::script) and not(ancestor::style)][contains(text(),"$") or contains(text(),"€") or contains(text(),"£") or contains(text(),"¥")]');
        foreach ($currencyElements as $element) {
            if (preg_match('/[\$€£¥]/', $element->textContent)) {
                $localizationElements[] = [
                    'type' => 'currency',
                    'content' => trim($element->textContent),
                    'path' => $element->getNodePath(),
                    'recommendation' => 'Use locale-specific currency symbols and formats',
                    'reference' => 'https://www.w3.org/International/articles/language-tags/#currency',
                    'example' => 'US: $100.00 | EU: 100,00 € | JP: ¥100'
                ];
            }
        }

        // Check for hardcoded text that might need translation
        // Example: "Welcome" vs "Bienvenue"
        // W3C: https://www.w3.org/International/articles/language-tags/#translation
        $textElements = $xpath->query('//text()[not(ancestor::script) and not(ancestor::style)][string-length(normalize-space()) > 0]');
        foreach ($textElements as $element) {
            $text = trim($element->textContent);
            if (strlen($text) > 20 && !preg_match('/^[0-9\s\p{P}]+$/u', $text)) {
                $localizationElements[] = [
                    'type' => 'translatable_text',
                    'content' => substr($text, 0, 100) . (strlen($text) > 100 ? '...' : ''),
                    'path' => $element->getNodePath(),
                    'recommendation' => 'Consider using translation keys for text content',
                    'reference' => 'https://www.w3.org/International/articles/language-tags/#translation',
                    'example' => '<?php echo __("welcome_message"); ?>'
                ];
            }
        }

        // Check for time formats
        // Example: "13:45" vs "1:45 PM"
        // W3C: https://www.w3.org/International/articles/language-tags/#time
        $timeElements = $xpath->query('//text()[not(ancestor::script) and not(ancestor::style)][contains(text(),":")]');
        foreach ($timeElements as $element) {
            if (preg_match('/\d{1,2}:\d{2}(?::\d{2})?(?:\s*[AaPp][Mm])?/', $element->textContent)) {
                $localizationElements[] = [
                    'type' => 'time_format',
                    'content' => trim($element->textContent),
                    'path' => $element->getNodePath(),
                    'recommendation' => 'Use locale-specific time formats (12/24 hour)',
                    'reference' => 'https://www.w3.org/International/articles/language-tags/#time',
                    'example' => '24h: 13:45 | 12h: 1:45 PM'
                ];
            }
        }

        if (empty($localizationElements)) {
            $details[] = 'No localizable content detected';
            $issues[] = [
                'type' => 'no_localizable_content',
                'description' => 'No content requiring localization was found'
            ];
        } else {
            $details[] = sprintf('Found %d elements with localizable content', count($localizationElements));
            
            // Group elements by type for better analysis
            $elementTypes = array_count_values(array_column($localizationElements, 'type'));
            foreach ($elementTypes as $type => $count) {
                $details[] = sprintf('Found %d %s elements', $count, str_replace('_', ' ', $type));
            }
        }

        $details = array_merge($details, [
            [
                'text' => 'Use appropriate number formatting for target locales',
                'example' => '1,234.56 (en-US) vs 1.234,56 (de-DE)',
                'reference' => 'https://www.w3.org/International/articles/language-tags/#numbers'
            ],
            [
                'text' => 'Adapt date formats to local conventions',
                'example' => '12/31/2023 (en-US) vs 31/12/2023 (en-GB)',
                'reference' => 'https://www.w3.org/International/articles/language-tags/#dates'
            ],
            [
                'text' => 'Consider currency conversion and format localization',
                'example' => '$1,234.56 (en-US) vs 1.234,56 € (de-DE)',
                'reference' => 'https://www.w3.org/International/articles/language-tags/#currency'
            ],
            [
                'text' => 'Implement locale-specific content variations where needed',
                'example' => 'color (en-US) vs colour (en-GB)',
                'reference' => 'https://www.w3.org/International/articles/language-tags/#variants'
            ],
            [
                'text' => 'Use translation management system for text content',
                'example' => '<?php echo __("welcome_message"); ?>',
                'reference' => 'https://www.w3.org/International/articles/language-tags/#translation'
            ],
            [
                'text' => 'Consider right-to-left (RTL) layout requirements',
                'example' => '<html dir="rtl" lang="ar">',
                'reference' => 'https://www.w3.org/International/articles/language-tags/#direction'
            ],
            [
                'text' => 'Implement locale-specific sorting and collation',
                'example' => 'å (after z in English, after a in Swedish)',
                'reference' => 'https://www.w3.org/International/articles/language-tags/#sorting'
            ],
            [
                'text' => 'Use appropriate time formats (12/24 hour) per locale',
                'example' => '13:45 (fr-FR) vs 1:45 PM (en-US)',
                'reference' => 'https://www.w3.org/International/articles/language-tags/#time'
            ]
        ]);

        return [
            'passed' => !empty($localizationElements) && empty($issues),
            'importance' => 'medium',
            'elements' => $localizationElements,
            'issues' => $issues,
            'details' => $details,
            'total_elements' => count($localizationElements),
            'element_types' => $elementTypes ?? [],
            'recommendations' => array_unique(array_column($localizationElements, 'recommendation'))
        ];
    }


   
    /**
     * Analyzes date formats in the content and returns detected patterns
     *
     * @param DOMXPath $xpath The XPath object for the document
     * @return array Array of detected date formats with their locations
     */
    public function analyzeDateFormats(DOMDocument|DOMXPath $xpath): array
    {
        if ($xpath instanceof DOMDocument) {
            $xpath = new DOMXPath($xpath);
        }

        $dateFormats = [];
        $issues = [];
        $details = [];

        // Common date patterns to check, following W3C internationalization guidelines
        // See: https://www.w3.org/International/articles/date-time/
        $patterns = [
            'mm/dd/yyyy' => '/\b(0?[1-9]|1[0-2])\/(0?[1-9]|[12]\d|3[01])\/(\d{4}|\d{2})\b/', // Example: 12/31/2023
            'dd/mm/yyyy' => '/\b(0?[1-9]|[12]\d|3[01])\/(0?[1-9]|1[0-2])\/(\d{4}|\d{2})\b/', // Example: 31/12/2023
            'yyyy-mm-dd' => '/\b(\d{4})-([0-1]\d)-([0-3]\d)\b/', // Example: 2023-12-31 (ISO 8601)
            'dd-mm-yyyy' => '/\b([0-3]\d)-([0-1]\d)-(\d{4})\b/', // Example: 31-12-2023
            'month dd, yyyy' => '/\b(?:Jan(?:uary)?|Feb(?:ruary)?|Mar(?:ch)?|Apr(?:il)?|May|Jun(?:e)?|Jul(?:y)?|Aug(?:ust)?|Sep(?:tember)?|Oct(?:ober)?|Nov(?:ember)?|Dec(?:ember)?)\s+(\d{1,2})(?:st|nd|rd|th)?,\s+(\d{4})\b/i' // Example: December 31, 2023
        ];

        // Find elements containing potential dates, following W3C recommendations
        // for content parsing and internationalization
        $dateElements = $xpath->query('//*[contains(text(),"/") or contains(text(),"-") or contains(text(),",")]');

        foreach ($dateElements as $element) {
            if (!$element instanceof DOMElement) {
                continue;
            }

            $text = trim($element->textContent);
            $path = $element->getNodePath();

            foreach ($patterns as $format => $pattern) {
                if (preg_match($pattern, $text, $matches)) {
                    $dateFormats[] = [
                        'format' => $format,
                        'value' => $matches[0],
                        'path' => $path,
                        'element' => $element->tagName,
                        'suggestion' => $this->getDateFormatSuggestion($format),
                        'w3c_reference' => 'https://www.w3.org/International/articles/date-time/#recommendations'
                    ];

                    // Flag non-ISO formats as issues per W3C guidelines
                    if ($format !== 'yyyy-mm-dd') {
                        $issues[] = [
                            'type' => 'non_standard_date_format',
                            'format' => $format,
                            'path' => $path,
                            'impact' => 'medium',
                            'recommendation' => 'Use ISO 8601 format (YYYY-MM-DD) for better internationalization',
                            'example' => '2023-12-31 instead of ' . $matches[0],
                            'w3c_reference' => 'https://www.w3.org/International/articles/date-time/#iso'
                        ];
                    }
                }
            }
        }

        return [
            'formats' => $dateFormats,
            'issues' => $issues,
            'total_dates' => count($dateFormats),
            'unique_formats' => count(array_unique(array_column($dateFormats, 'format'))),
            'recommendations' => [
                [
                    'text' => 'Use ISO 8601 format (YYYY-MM-DD) for machine-readable dates',
                    'example' => '2023-12-31',
                    'w3c_reference' => 'https://www.w3.org/International/articles/date-time/#iso'
                ],
                [
                    'text' => 'Consider locale-specific date formatting for displayed dates',
                    'example' => '31 décembre 2023 (fr-FR) vs December 31, 2023 (en-US)',
                    'w3c_reference' => 'https://www.w3.org/International/articles/date-time/#locales'
                ],
                [
                    'text' => 'Include timezone information where relevant',
                    'example' => '2023-12-31T23:59:59+01:00',
                    'w3c_reference' => 'https://www.w3.org/International/articles/date-time/#timezone'
                ],
                [
                    'text' => 'Use HTML5 time element with datetime attribute',
                    'example' => '<time datetime="2023-12-31">December 31, 2023</time>',
                    'w3c_reference' => 'https://www.w3.org/International/articles/date-time/#markup'
                ],
                [
                    'text' => 'Avoid ambiguous date formats (e.g. 03/04/2024)',
                    'example' => 'Is 03/04/2024 April 3rd or March 4th?',
                    'w3c_reference' => 'https://www.w3.org/International/articles/date-time/#ambiguous'
                ]
            ]
        ];
    }

    private function getDateFormatSuggestion(string $format): string 
    {
        return match($format) {
            'mm/dd/yyyy', 'dd/mm/yyyy' => 'Replace slash-separated dates with ISO 8601 format (YYYY-MM-DD)',
            'dd-mm-yyyy' => 'Use YYYY-MM-DD format instead of DD-MM-YYYY',
            'month dd, yyyy' => 'Consider using numeric date format YYYY-MM-DD alongside written dates',
            default => 'Use consistent ISO 8601 date formatting'
        };
    }


    /**
     * Analyzes number formats in the content and returns detected patterns
     *
     * @param DOMXPath $xpath The XPath object for the document
     * @return array Array of detected number formats with their locations
     */
    public function analyzeNumberFormats(DOMDocument|DOMXPath $xpath): array
    {
        if ($xpath instanceof DOMDocument) {
            $xpath = new DOMXPath($xpath);
        }

        $numberFormats = [];
        $issues = [];
        $details = [];

        // Check for numbers with decimal separators
        $numberPatterns = $xpath->query('//*[contains(text(),",") or contains(text(),".")]');
        foreach ($numberPatterns as $element) {
            if (!$element instanceof DOMElement) {
                continue;
            }

            $text = trim($element->textContent);
            $path = $element->getNodePath();

            // Match decimal numbers with comma or period separators
            if (preg_match('/\d+[,\.]\d+/', $text)) {
                $numberFormats[] = [
                    'type' => 'decimal',
                    'value' => $text,
                    'path' => $path,
                    'element' => $element->tagName
                ];
            }

            // Check for potentially ambiguous number formats
            if (preg_match('/\d{1,3}(?:,\d{3})*(?:\.\d+)?/', $text)) {
                $issues[] = [
                    'type' => 'ambiguous_format',
                    'value' => $text,
                    'path' => $path,
                    'recommendation' => 'Use locale-specific number formatting'
                ];
            }
        }

        // Check for currency values
        $currencyPatterns = $xpath->query('//*[contains(text(),"$") or contains(text(),"€") or contains(text(),"£")]');
        foreach ($currencyPatterns as $element) {
            if (!$element instanceof DOMElement) {
                continue;
            }

            $text = trim($element->textContent);
            $path = $element->getNodePath();

            if (preg_match('/[\$€£]\s*\d+/', $text)) {
                $numberFormats[] = [
                    'type' => 'currency',
                    'value' => $text,
                    'path' => $path,
                    'element' => $element->tagName
                ];
                
                $issues[] = [
                    'type' => 'hardcoded_currency',
                    'value' => $text,
                    'path' => $path,
                    'recommendation' => 'Use locale-aware currency formatting'
                ];
            }
        }

        return [
            'formats' => $numberFormats,
            'issues' => $issues,
            'total_numbers' => count($numberFormats),
            'recommendations' => [
                [
                    'text' => 'Use locale-aware number formatting functions',
                    'example' => 'number_format(1234.56, 2, ",", ".") // 1.234,56 (de-DE)',
                    'w3c_reference' => 'https://www.w3.org/International/articles/numeric-formats/'
                ],
                [
                    'text' => 'Avoid hardcoding decimal/thousands separators',
                    'example' => 'Wrong: $num = "1,234.56" | Right: $num = number_format(1234.56)',
                    'w3c_reference' => 'https://www.w3.org/International/articles/numeric-formats/#summary'
                ],
                [
                    'text' => 'Consider cultural differences in number formatting',
                    'example' => '1,234.56 (en-US) vs 1.234,56 (de-DE) vs 1 234,56 (fr-FR)',
                    'w3c_reference' => 'https://www.w3.org/International/articles/numeric-formats/#recommendations'
                ],
                [
                    'text' => 'Use appropriate currency formatting for each locale',
                    'example' => '$1,234.56 (en-US) vs 1.234,56 € (de-DE)',
                    'w3c_reference' => 'https://www.w3.org/International/articles/numeric-formats/#currency'
                ],
                [
                    'text' => 'Test with different locale number formats',
                    'example' => 'Test: 1234.56, 1234,56, 1 234.56, 1.234,56',
                    'w3c_reference' => 'https://www.w3.org/International/articles/numeric-formats/#testing'
                ]
            ],
            'details' => array_merge($details, [
                'Number formats detected: ' . count($numberFormats),
                'Formatting issues found: ' . count($issues)
            ])
        ];
    }



    /**
     * Analyzes the translation readiness of the document content
     * 
     * @param DOMDocument|DOMXPath $xpath The XPath object for the document
     * @return array Analysis results including translation-ready elements and issues
     */
    public function analyzeTranslationReadiness(DOMDocument|DOMXPath $xpath): array
    {
        if ($xpath instanceof DOMDocument) {
            $xpath = new DOMXPath($xpath);
        }

        $translatableElements = [];
        $issues = [];

        // Check for text content in elements
        $textElements = $xpath->query('//*[not(self::script)][not(self::style)][not(self::noscript)][string-length(normalize-space(text())) > 0]');
        foreach ($textElements as $element) {
            if (!$element instanceof DOMElement) {
                continue;
            }

            $text = trim($element->textContent);
            if (!empty($text)) {
                $translatableElements[] = [
                    'type' => 'text',
                    'element' => $element->nodeName,
                    'text' => substr($text, 0, 100) . (strlen($text) > 100 ? '...' : ''),
                    'has_lang' => $element->hasAttribute('lang')
                ];

                if (!$element->hasAttribute('lang')) {
                    $issues[] = [
                        'type' => 'missing_lang_attribute',
                        'element' => $element->nodeName,
                        'recommendation' => 'Add lang attribute to translatable content',
                        'example' => '<p lang="en">English text</p>',
                        'w3c_reference' => 'https://www.w3.org/International/questions/qa-lang-why'
                    ];
                }
            }
        }

        // Check for translatable attributes
        $attributeElements = $xpath->query('//*[@title or @alt or @placeholder or @aria-label or @aria-description or @value]');
        foreach ($attributeElements as $element) {
            if (!$element instanceof DOMElement) {
                continue;
            }

            $attributes = [];
            foreach (['title', 'alt', 'placeholder', 'aria-label', 'aria-description', 'value'] as $attr) {
                if ($element->hasAttribute($attr)) {
                    $value = $element->getAttribute($attr);
                    if (!empty(trim($value))) {
                        $attributes[$attr] = $value;
                    }
                }
            }

            if (!empty($attributes)) {
                $translatableElements[] = [
                    'type' => 'attribute',
                    'element' => $element->nodeName,
                    'attributes' => $attributes,
                    'has_lang' => $element->hasAttribute('lang')
                ];
            }
        }

        // Check for hardcoded strings in JavaScript
        $scripts = $xpath->query('//script[not(@src)]');
        foreach ($scripts as $script) {
            if (!$script instanceof DOMElement) {
                continue;
            }

            if (preg_match_all('/[\'"]([^\'"]{3,}?)[\'"]/m', $script->textContent, $matches)) {
                $issues[] = [
                    'type' => 'hardcoded_strings',
                    'strings' => array_slice($matches[1], 0, 5),
                    'total_strings' => count($matches[1]),
                    'recommendation' => 'Move text strings to translation files or i18n system',
                    'example' => "const messages = { en: { welcome: 'Welcome' }, fr: { welcome: 'Bienvenue' } }",
                    'w3c_reference' => 'https://www.w3.org/International/questions/qa-scripts'
                ];
            }
        }

        // Check for translation-related meta tags and links
        $translationMeta = $xpath->query('//link[@rel="alternate" and @hreflang]|//meta[@name="language"]|//meta[@http-equiv="Content-Language"]');
        if ($translationMeta->length === 0) {
            $issues[] = [
                'type' => 'missing_translation_meta',
                'recommendation' => 'Add language alternates and meta tags for translation support',
                'example' => '<link rel="alternate" hreflang="fr" href="https://example.com/fr/">',
                'w3c_reference' => 'https://www.w3.org/International/questions/qa-link-lang'
            ];
        }

        return [
            'translatable_content' => $translatableElements,
            'issues' => $issues,
            'total_translatable_elements' => count($translatableElements),
            'recommendations' => [
                'technical' => [
                    [
                        'text' => 'Implement a robust i18n framework',
                        'example' => "import i18n from 'i18next';\ni18n.init({ lng: 'en', resources: {...} });",
                        'w3c_reference' => 'https://www.w3.org/International/i18n-drafts/nav/programming'
                    ],
                    [
                        'text' => 'Use translation key system',
                        'example' => "t('welcome.message') instead of 'Welcome'",
                        'w3c_reference' => 'https://www.w3.org/International/questions/qa-i18n'
                    ],
                    [
                        'text' => 'Add language switcher component',
                        'example' => '<select onchange="changeLanguage(this.value)"><option value="en">English</option></select>',
                        'w3c_reference' => 'https://www.w3.org/International/questions/qa-navigation'
                    ]
                ],
                'content' => [
                    [
                        'text' => 'Plan for text expansion/contraction',
                        'example' => 'German text can be 30% longer than English',
                        'w3c_reference' => 'https://www.w3.org/International/articles/article-text-size'
                    ],
                    [
                        'text' => 'Consider locale-specific formatting',
                        'example' => 'Dates: MM/DD/YYYY (US) vs DD/MM/YYYY (EU)',
                        'w3c_reference' => 'https://www.w3.org/International/questions/qa-format-date-time'
                    ],
                    [
                        'text' => 'Document translation guidelines',
                        'example' => 'Style guides, glossaries, and translation memories',
                        'w3c_reference' => 'https://www.w3.org/International/techniques/authoring-html#guidelines'
                    ]
                ]
            ]
        ];
    }


}