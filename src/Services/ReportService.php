<?php

namespace Shaferllc\Analytics\Services;

use DOMNode;
use Exception;
use DOMElement;
use DOMDocument;
use App\Models\Site;
use Illuminate\Support\Arr;
use Spatie\Browsershot\Browsershot;
use Shaferllc\Analytics\Concerns\Media;
use Shaferllc\Analytics\Traits\Finders;
use Shaferllc\Analytics\Concerns\Assets;
use Shaferllc\Analytics\Concerns\Mobile;
use Shaferllc\Analytics\Concerns\Search;
use Shaferllc\Analytics\Concerns\Social;
use Illuminate\Support\{Str, Collection};
use Shaferllc\Analytics\Concerns\Content;
use Shaferllc\Analytics\Traits\Analyzers;
use Shaferllc\Analytics\Traits\Detectors;
use Symfony\Component\DomCrawler\Crawler;
use Shaferllc\Analytics\Concerns\MetaData;
use Shaferllc\Analytics\Concerns\MetaTags;
use Shaferllc\Analytics\Concerns\Security;
use Shaferllc\Analytics\Concerns\Semantic;
use Shaferllc\Analytics\Traits\Extractors;
use Shaferllc\Analytics\Concerns\Resources;
use Shaferllc\Analytics\Concerns\Structure;
use Shaferllc\Analytics\Concerns\Technical;
use Shaferllc\Analytics\Concerns\Usability;
use Shaferllc\Analytics\Models\CrawledPage;
use Shaferllc\Analytics\Concerns\Optimization;
use Shaferllc\Analytics\Concerns\Accessibility;
use Shaferllc\Analytics\Concerns\StructuredData;
use Shaferllc\Analytics\Jobs\BrowserConsoleCheck;
use Illuminate\Support\Facades\{Http, Cache, Log};
use Shaferllc\Analytics\Concerns\SemanticStructure;
use Shaferllc\Analytics\Concerns\Internationalization;

class ReportService
{

    private string $domain;
    private array $cache = [];
    private const CACHE_TTL = 3600; // 1 hour cache
    private ?DOMDocument $dom = null;

    // Add more content types and their extensions
    private const RESOURCE_TYPES = [
        'documents' => ['pdf', 'doc', 'docx', 'txt', 'rtf'],
        'spreadsheets' => ['xls', 'xlsx', 'csv'],
        'archives' => ['zip', 'rar', '7z', 'tar', 'gz'],
        'images' => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif', 'svg'],
        'audio' => ['mp3', 'wav', 'ogg', 'm4a'],
        'video' => ['mp4', 'webm', 'mov', 'avi']
    ];

    private const SOCIAL_DOMAINS = [
        'facebook.com',
        'twitter.com',
        'linkedin.com',
        'instagram.com',
        'youtube.com',
        'pinterest.com',
        'tiktok.com',
        'reddit.com'
    ];

    public function __construct(string $domain = '')
    {
        $this->domain = $domain;
    }

    public function getHtmlFromUrl(string $url): ?DOMDocument
    {


        // Cache the HTML string instead of the DOMDocument
        $html = Cache::remember('html_' . md5($url), self::CACHE_TTL, function() use ($url) {
            try {
                $response = Http::timeout(10)
                    ->withHeaders([
                        'User-Agent' => config('services.report.user_agent', 'ReportService/1.0'),
                        'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9',
                        'Accept-Language' => 'en-US,en;q=0.5'
                    ])
                    ->get($url);

                if ($response->successful()) {
                    return $this->cleanHtml($response->body());
                }

                Log::error("HTTP request failed for URL: {$url}", [
                    'status_code' => $response->status(),
                    'response' => $response->body()
                ]);
            } catch (Exception $e) {
                Log::error("Failed to fetch URL: {$url}", [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }

            return null;
        });

        if (!$html) {
            Log::error("No valid HTML content available for URL: {$url}");
            return null;
        }
        // Create DOMDocument from cached HTML
        try {
            $this->dom = $this->createDomDocument($html);
            return $this->dom;
        } catch (Exception $e) {
            Log::error("Failed to create DOMDocument from cached HTML", [
                'error' => $e->getMessage(),
                'url' => $url
            ]);
            return null;
        }
    }

    private function cleanHtml(string $html): string
    {
        // Remove BOM and normalize whitespace
        $html = ltrim($html, "\xEF\xBB\xBF");
        $html = preg_replace('/\s+/', ' ', $html);

        // Fix common HTML issues
        $html = str_replace('&', '&amp;', $html);
        $html = preg_replace('/<(br|hr|img|input|link|meta|area|base|col|command|embed|keygen|param|source|track|wbr)([^>]*)(?<!\/)>/', '<$1$2 />', $html);

        return $html;
    }

    private function createDomDocument(string $html): DOMDocument
    {
        $domDocument = new DOMDocument();
        libxml_use_internal_errors(true);

        // Add UTF-8 meta tag if missing
        if (!preg_match('/<meta[^>]+charset=[^>]+>/i', $html)) {
            $html = '<meta charset="UTF-8">' . $html;
        }

        $domDocument->loadHTML($html, LIBXML_NOERROR | LIBXML_NOWARNING | LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        return $domDocument;
    }


    private function isResourceLink(string $path): bool
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        return in_array($extension, ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'zip']);
    }

    private function isSocialLink(string $host, array $socialDomains): bool
    {
        return Str::contains($host, $socialDomains);
    }

    private function isInternalLink(?string $host): bool
    {
        return !$host || Str::contains($host, $this->domain);
    }



    private function url(string $url): string
    {
        if (str_starts_with($url, '//')) {
            return "https:{$url}";
        }
        if (str_starts_with($url, '/')) {
            return "{$this->domain}{$url}";
        }
        return $url;
    }


    public function generateFullReport(CrawledPage $crawledPage, DOMDocument $domDocument)
    {

        $data = [
            // 'internationalization' => [
            //     'language_detection' => (new Internationalization)->analyzeLanguageDeclaration($domDocument),
            //     'character_encoding' => (new Internationalization)->analyzeCharacterEncoding($domDocument),
            //     'text_direction' => (new Internationalization)->analyzeTextDirection($domDocument),
            //     'language_alternates' => (new Internationalization)->analyzeLanguageAlternates($domDocument),
            //     // 'content_localization' => (new Internationalization)->analyzeContentLocalization($domDocument),
            //     // 'date_formats' => (new Internationalization)->analyzeDateFormats($domDocument),
            //     // 'number_formats' => (new Internationalization)->analyzeNumberFormats($domDocument),
            //     // 'translation_readiness' => (new Internationalization)->analyzeTranslationReadiness($domDocument),
            //     'language' => [
            //         'tags' => (new Internationalization)->findLanguageTags($domDocument),
            //         'hreflang' => (new Internationalization)->analyzeHreflangTags($domDocument),
            //     ],
            //     // 'content' => (new Internationalization)->findLocalizedContent($domDocument)
            // ],
            // 'seo' => [
                // 'meta_tags' => (new MetaTags())->analyzeMetaTags($domDocument),
                // 'content' => [
                    // 'quality' => (new Content())->analyzeContentQuality($domDocument),
            //         'text' => (new Content())->analyzeTextContent($domDocument),
            //         'readability' => (new Content())->analyzeReadability($domDocument),
            //         'keyword_density' => (new Content())->analyzeKeywordDensity($domDocument),
            //         'headings' => (new Content())->analyzeHeadingStructure($domDocument),
            //         'duplicate' => (new Content())->analyzeDuplicateContent($domDocument),
            //         'ratio' => (new Content())->analyzeContentToHtmlRatio($domDocument)
                // ],
            //     'structure' => [
            //         'headings' => (new Structure())->analyzeHeadingHierarchy($domDocument),
            //         'links' => (new Structure())->analyzeLinkStructure($domDocument),
            //         'url' => (new Structure())->analyzeUrlStructure($domDocument),
            //         'navigation' => (new Structure())->analyzeNavigationStructure($domDocument),
            //         'footer' => (new Structure())->analyzeFooterStructure($domDocument)
            //     ],
            //     'media' => [
            //         'images' => (new Media())->analyzeImageOptimization($domDocument)
            //     ],
            //     'mobile' => [
            //         'seo' => (new Mobile())->analyzeMobileSEO($domDocument),
            //         'friendly' => (new Mobile())->analyzeMobileFriendly($domDocument),
            //         'optimization' => (new Mobile())->analyzeMobileOptimization($domDocument)
            //     ],
            //     'search' => [
            //         'forms' => (new Search)->analyzeSearchForms($domDocument),
            //         'inputs' => (new Search)->findSearchInputs($domDocument),
            //         'results' => (new Search)->analyzeSearchResults($domDocument),
            //         'filters' => (new Search)->detectSearchFilters($domDocument),
            //         'tracking' => (new Search)->analyzeSearchTracking($domDocument),
            //         'markup' => (new Search)->analyzeSiteSearchMarkup($domDocument)
            //     ],
            //     'structured_data' => [
            //         'schema' => (new StructuredData)->analyzeSchemaMarkup($domDocument),
            //         'microdata' => (new StructuredData)->analyzeMicrodata($domDocument),
            //         'json_ld' => (new StructuredData)->extractJsonLD($domDocument)
            //     ],
            //     'social' => [
            //         'signals' => (new Social)->analyzeSocialSignals($domDocument),
            //         'metadata' => (new Social)->analyzeSocialMetadata($domDocument),
            //         'open_graph' => (new Social)->analyzeOpenGraph($domDocument),
            //         'twitter_cards' => (new Social)->analyzeTwitterCards($domDocument)
            //     ],
            //     'technical' => [
            //         'canonical' => (new Technical)->analyzeCanonicalLinks($domDocument),
            //         'robots' => (new Technical)->analyzeRobotsDirectives($domDocument),
            //         'internationalization' => (new Technical)->analyzeInternationalization($domDocument),
            //         'security' => (new Technical)->analyzeSecurityHeaders($domDocument),
            //         'sitemap' => (new Technical)->analyzeSitemapImplementation($domDocument)
            //     ],
            //     'semantic' => [
            //         'structure' => (new Semantic)->analyzeSemanticStructure($domDocument),
            //         'html5' => (new Semantic)->analyzeHTML5Elements($domDocument),
            //         'landmarks' => (new Semantic)->analyzeLandmarkRoles($domDocument),
            //         'content' => (new Semantic)->analyzeContentStructure($domDocument),
            //         'lists' => (new Semantic)->analyzeListElements($domDocument),
            //         'paragraphs' => (new Semantic)->analyzeParagraphs($domDocument),
            //         'tables' => (new Semantic)->analyzeTableStructure($domDocument)
            //     ]
            // ],
            // 'performance' => [
            //     'resources' => [
            //         'hints' => (new Resources)->detectResourceHints($domDocument),
            //         'http_requests' => (new Resources)->extractHttpRequests($domDocument),
            //         'loading' => (new Resources)->analyzeResourceLoading($domDocument),
            //         'missing' => (new Resources)->detectMissingResourceHints($domDocument)
            //     ],
            //     'optimization' => [
            //         'opportunities' => (new Optimization)->detectOptimizationOpportunities($domDocument),
            //         'bottlenecks' => (new Optimization)->detectPerformanceBottlenecks($domDocument),
            //         'issues' => (new Optimization)->detectPerformanceIssues($domDocument),
            //         'render_blocking' => (new Optimization)->detectRenderBlocking($domDocument),
            //         'heavy_elements' => (new Optimization)->detectHeavyElements($domDocument)
            //     ],
            //     'assets' => [
            //         'images' => (new Assets)->extractImageFormats($domDocument),
            //         'scripts' => (new Assets)->extractAssetInfo($domDocument)['scripts'],
            //         'stylesheets' => (new Assets)->extractAssetInfo($domDocument)['stylesheets']
            //     ]
            // ],
            // 'accessibility' => [
            //     'aria' => (new Accessibility)->analyzeAriaUsage($domDocument),
            //     'images' => (new Accessibility)->analyzeAltTexts($domDocument),
            //     'colors' => (new Accessibility)->analyzeColorContrast($domDocument),
            //     'keyboard' => (new Accessibility)->analyzeKeyboardNavigation($domDocument),
            //     'forms' => (new Accessibility)->analyzeFormLabels($domDocument),
            //     'focus' => (new Accessibility)->analyzeFocusManagement($domDocument)
            // ],
            // 'security' => [
            //     'issues' => (new Security)->findSecurityIssues($domDocument),
            //     'content' => [
            //         'mixed' => (new Security)->findMixedContent($domDocument),
            //         'security' => (new Security)->analyzeContentSecurity($domDocument),
            //         'external' => (new Security)->analyzeExternalResources($domDocument)
            //     ],
            //     'forms' => (new Security)->analyzeFormSecurity($domDocument)
            // ],
            // 'technical' => [
            //     'stack' => (new Technical)->analyzeTechnologyStack($domDocument),
            //     'compatibility' => (new Technical)->analyzeBrowserCompatibility($domDocument),
            //     'validation' => [
            //         'html' => (new Technical)->validateHtml($domDocument),
            //         'unused_css' => (new Technical)->detectUnusedSelectors($domDocument)
            //     ],
            //     'code' => [
            //         'javascript' => (new Technical)->analyzeJavascriptUsage($domDocument),
            //         'css' => (new Technical)->analyzeCssUsage($domDocument)
            //     ]
            // ],
            // 'usability' => [
            //     'navigation' => (new Usability)->analyzeNavigation($domDocument),
            //     'forms' => (new Usability)->analyzeFormUsability($domDocument),
            //     'layout' => [
            //         'page' => (new Usability)->analyzePageLayout($domDocument),
            //         'visual' => (new Usability)->analyzeVisualHierarchy($domDocument)
            //     ],
            //     'interaction' => [
            //         'elements' => (new Usability)->analyzeInteractionElements($domDocument),
            //     ]
            // ],
            // 'assets' => [
            //     'scripts' => [
            //         'inline' => (new Assets)->findInlineScripts($domDocument),
            //         'external' => (new Assets)->findExternalScripts($domDocument),
            //         'loading' => (new Assets)->analyzeAsyncDefer($domDocument),
            //         'jquery' => (new Assets)->detectJQueryUsage($domDocument),
            //         'frameworks' => (new Assets)->detectFrameworks($domDocument),
            //         'third_party' => (new Assets)->analyzeThirdPartyScripts($domDocument)
            //     ],
            //     'styles' => [
            //         'inline' => (new Assets)->findInlineStyles($domDocument),
            //         'external' => (new Assets)->findExternalStylesheets($domDocument),
            //         'critical' => (new Assets)->analyzeCriticalCss($domDocument),
            //         'unused' => (new Technical)->detectUnusedSelectors($domDocument),
            //         'media_queries' => (new Assets)->analyzeMediaQueries($domDocument)
            //     ],
            //     'resources' => [
            //         'size' => (new Assets)->calculateResourceSizes($domDocument),
            //         'blocking' => (new Assets)->findRenderBlockingResources($domDocument),
            //         'lazy_loading' => (new Assets)->analyzeLazyLoading($domDocument),
            //         'hints' => (new Assets)->analyzeResourceHints($domDocument)
            //     ]
            // ],
            // 'metadata' => [
            //     'schema' => (new MetaData)->analyzeSchemaTypes($domDocument),
            //     'social' => [
            //         'open_graph' => (new MetaData)->extractOpenGraphTags($domDocument),
            //         'twitter_cards' => (new MetaData)->extractTwitterCards($domDocument),
            //         'schema' => (new MetaData)->extractSchemaMarkup($domDocument),
            //         'widgets' => (new MetaData)->detectSocialWidgets($domDocument)
            //     ],
            //     'privacy' => [
            //         'cookies' => (new MetaData)->detectCookieUsage($domDocument),
            //         'tracking' => (new MetaData)->detectTrackingScripts($domDocument),
            //         'gdpr' => (new MetaData)->findGDPRElements($domDocument),
            //         'policy' => (new MetaData)->findPrivacyPolicyLinks($domDocument),
            //         'forms' => (new MetaData)->analyzeDataCollectionForms($domDocument)
            //     ]
            // ],
            // 'mobile' => [
            //     'viewport' => (new Mobile)->analyzeViewportSettings($domDocument),
            //     'interaction' => [
            //         'touch' => (new Mobile)->analyzeTouchElements($domDocument),
            //         'tap_targets' => (new Mobile)->analyzeTapTargets($domDocument)
            //     ],
            //     'typography' => (new Mobile)->analyzeFontSizes($domDocument),
            //     'friendly' => (new Mobile)->checkMobileFriendly($domDocument)
            // ],
            // 'security_additional' => [
            //     'unsafe_links' => (new Security)->checkUnsafeCrossOriginLinks($domDocument),
            //     'emails' => (new Security)->findPlaintextEmails($domDocument),
            //     'forms' => (new Security)->checkFormSecurity($domDocument)
            // ]
        ];


        $report = $crawledPage->reports()->create([
            // 'internationalization' => Arr::get($data, 'internationalization'),
            'generated_at' => now(),
        ]);

        // BrowserConsoleCheck::dispatch($crawledPage->website, $crawledPage, $report);

        return $report;
    }

}
