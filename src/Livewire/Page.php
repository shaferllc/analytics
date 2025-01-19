<?php

namespace Shaferllc\Analytics\Livewire;

use App\Models\Site;
use Livewire\Component;
use Illuminate\Support\Arr;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\Attributes\Locked;
use Shaferllc\Analytics\Models\Report;
use Shaferllc\Analytics\Jobs\CrawlPage;
use Shaferllc\Analytics\Models\CrawledPage;
use Shaferllc\Analytics\Traits\ComponentTrait;
use Shaferllc\Analytics\Traits\DateRangeTrait;
use Shaferllc\Analytics\Services\ReportService;
use Shaferllc\Analytics\Jobs\BrowserConsoleCheck;

#[Title('Page')]
class Page extends Component
{
    use DateRangeTrait, WithPagination, ComponentTrait;

    #[Locked]
    public Site $site;
    public string $page;

    public CrawledPage $crawledPage;
    public ?Report $report;

    public $showModal = false;

    public function mount(Site $site, string $value)
    {
        $this->site = $site;
        $this->page = decrypt($value);

        $this->crawledPage = $this->site->crawledPages()->firstOrCreate([
            'url' => $this->site->domain.$this->page,
            'path' => $this->page
        ]);

    }

    public function getReports() {
        $reports = $this->crawledPage->reports()->latest()->get();

        if($reports->isEmpty()) {
            $dom = (new ReportService)->getHtmlFromUrl($this->crawledPage->url);
            $report = (new ReportService)->generateFullReport($this->crawledPage, $dom);
            $reports->push($report);
        }

        return $reports;
    }


    protected function getTechnicalData(
        $type = 'browser',
        $iconFunction = null,
        $includes = []
    ) {
        return $this->query(
            category: 'technical',
            type: $type,
            from: $this->from,
            to: $this->to,
            page: $this->page,
            paginate: false,
            includes: $includes,
            iconFunction: $iconFunction
        );
    }

    public function getBrowserData() {
        return $this->getTechnicalData('browser', 'formatBrowser', [
            'resolution',
            'plugins',
            'languages',
            'user_agent',
            'colorScheme',
            'memory_usage',
            'devicePixelRatio',
            'webgl'
        ]);
    }

    public function getOperatingSystemData() {
        $data = $this->getTechnicalData('os', 'formatOperatingSystem', [
            'os',
            'device',
            'memory',
            'cpuCores',
            'memory_usage',
            'connection_speed',
            'network_latency',
            'page_load_metrics'
        ]);

        // Format page load metrics into human readable values
        if (!empty($data['data'])) {
            $data['data'] = collect($data['data'])->map(function($item) {
                if (!empty($item->page_load_metrics)) {
                    $item->page_load_metrics = collect($item->page_load_metrics)->map(function($metric) {
                        $value = $metric['value'];

                        // Convert milliseconds to seconds with 2 decimal places
                        $metric['value'] = [
                            'DNS Lookup' => [
                                'value' => number_format(Arr::get($value, 'dnsLookup', 0), 2) . ' ms',
                                'description' => 'Time spent resolving the domain name to an IP address'
                            ],
                            'TCP Connection' => [
                                'value' => number_format(Arr::get($value, 'tcpConnection', 0), 2) . ' ms',
                                'description' => 'Time taken to establish TCP connection with the server'
                            ],
                            'Server Response' => [
                                'value' => number_format(Arr::get($value, 'serverResponse', 0), 2) . ' ms',
                                'description' => 'Time until first byte is received from server (TTFB)'
                            ],
                            'DOM Interactive' => [
                                'value' => number_format(Arr::get($value, 'domInteractive', 0), 2) . ' ms',
                                'description' => 'Time until HTML is parsed and DOM is ready for interaction'
                            ],
                            'DOM Content Loaded' => [
                                'value' => number_format(Arr::get($value, 'domContentLoaded', 0), 2) . ' ms',
                                'description' => 'Time until initial HTML and DOM content is fully loaded'
                            ],
                            'Page Load' => [
                                'value' => number_format(Arr::get($value, 'pageLoad', 0), 2) . ' ms',
                                'description' => 'Total time until page and all resources are fully loaded'
                            ],
                            'First Paint' => [
                                'value' => number_format(Arr::get($value, 'firstPaint', 0), 2) . ' ms',
                                'description' => 'Time until first pixels are painted to the screen'
                            ],
                            'First Contentful Paint' => [
                                'value' => number_format(Arr::get($value, 'firstContentfulPaint', 0), 2) . ' ms',
                                'description' => 'Time until first meaningful content is visible'
                            ],
                            'Resource Count' => [
                                'value' => Arr::get($value, 'resourceCount', 0),
                                'description' => 'Total number of resources (scripts, styles, images etc) loaded'
                            ],
                            'Resource Duration' => [
                                'value' => number_format(Arr::get($value, 'resourceDuration', 0), 2) . ' ms',
                                'description' => 'Total time spent loading all page resources'
                            ],
                            'Decoded Body Size' => [
                                'value' => number_format(Arr::get($value, 'decodedBodySize', 0) / 1024, 2) . ' KB',
                                'description' => 'Size of response body after decompression'
                            ],
                            'Encoded Body Size' => [
                                'value' => number_format(Arr::get($value, 'encodedBodySize', 0) / 1024, 2) . ' KB',
                                'description' => 'Size of response body before decompression'
                            ]
                        ];

                        return $metric;
                    })->toArray();
                }
                return $item;
            })->toArray();
        }

        // Map connection speed values to human readable descriptions
        if (!empty($data['data'])) {
            $data['data'] = collect($data['data'])->map(function($item) {
                if (!empty($item->connection_speed)) {
                    $item->connection_speed = collect($item->connection_speed)->map(function($speed) {
                        $effectiveType = strtolower($speed['value']['effectiveType'] ?? '');
                        $downlink = $speed['value']['downlink'] ?? 0;
                        $rtt = $speed['value']['rtt'] ?? 0;

                        $speed['value'] = match($effectiveType) {
                            '4g' => sprintf('4G LTE (%d Mbps, %d ms)', $downlink, $rtt),
                            '3g' => sprintf('3G (%d Mbps, %d ms)', $downlink, $rtt),
                            '2g' => sprintf('2G (%d Mbps, %d ms)', $downlink, $rtt),
                            'slow-2g' => sprintf('Slow 2G (%d Mbps, %d ms)', $downlink, $rtt),
                            default => sprintf('%s (%d Mbps, %d ms)', ucfirst($effectiveType), $downlink, $rtt)
                        };

                        return $speed;
                    })->toArray();
                }
                return $item;
            })->toArray();
        }
        return $data;
    }

    protected function getLocationData()
    {
        return $this->query(
            category: 'location',
            type: 'country',
            from: $this->from,
            to: $this->to,
            page: $this->page,
            includes: ['city', 'continent', 'country', 'timezone'],
            paginate: false,
            iconFunction: 'formatFlag'
        );
    }

    protected function getPageData()
    {
        $pageData = $this->query(
            category: 'page_data',
            type: 'url',
            from: $this->from,
            to: $this->to,
            page: $this->page,
            includes: ['url', 'title', 'path', 'query', 'landing_page'],
            paginate: false
        );

        // Add campaign data if available
        if (!empty($pageData['data'])) {
            foreach ($pageData['data'] as &$page) {
                if (!empty($page->query)) {


                    // Parse query parameters from the query string
                    $queryParams = [];
                    foreach ($page->query as $queryData) {
                        // Extract parameters from query string
                        parse_str(ltrim($queryData['value'], '?'), $params);

                        // Merge parameters, keeping track of counts
                        foreach ($params as $key => $value) {
                            if (!isset($queryParams[$key])) {
                                $queryParams[$key] = [
                                    'value' => $value,
                                    'count' => $queryData['count']
                                ];
                            } else {
                                $queryParams[$key]['count'] += $queryData['count'];
                            }
                        }
                    }

                    // Common campaign tracking parameters with source URLs
                    $campaignParams = [
                        'utm_source' => [
                            'value' => $queryParams['utm_source']['value'] ?? null,
                            'count' => $queryParams['utm_source']['count'] ?? 0,
                            'url' => $this->getCampaignSourceUrl('utm_source', $queryParams['utm_source']['value'] ?? null),
                            'description' => 'Identifies which site sent the traffic'
                        ],
                        'utm_medium' => [
                            'value' => $queryParams['utm_medium']['value'] ?? null,
                            'count' => $queryParams['utm_medium']['count'] ?? 0,
                            'url' => $this->getCampaignSourceUrl('utm_medium', $queryParams['utm_medium']['value'] ?? null),
                            'description' => 'Marketing medium (cpc, social, email, etc)'
                        ],
                        'utm_campaign' => [
                            'value' => $queryParams['utm_campaign']['value'] ?? null,
                            'count' => $queryParams['utm_campaign']['count'] ?? 0,
                            'url' => $this->getCampaignSourceUrl('utm_campaign', $queryParams['utm_campaign']['value'] ?? null),
                            'description' => 'Specific campaign name, slogan, or promotion code'
                        ],
                        'utm_term' => [
                            'value' => $queryParams['utm_term']['value'] ?? null,
                            'count' => $queryParams['utm_term']['count'] ?? 0,
                            'url' => $this->getCampaignSourceUrl('utm_term', $queryParams['utm_term']['value'] ?? null),
                            'description' => 'Identifies search terms paid for'
                        ],
                        'utm_content' => [
                            'value' => $queryParams['utm_content']['value'] ?? null,
                            'count' => $queryParams['utm_content']['count'] ?? 0,
                            'url' => $this->getCampaignSourceUrl('utm_content', $queryParams['utm_content']['value'] ?? null),
                            'description' => 'Used to differentiate similar content or links'
                        ],
                        'gclid' => [
                            'value' => $queryParams['gclid']['value'] ?? null,
                            'count' => $queryParams['gclid']['count'] ?? 0,
                            'url' => isset($queryParams['gclid']) ? 'https://ads.google.com/aw/overview' : null,
                            'description' => 'Google Click Identifier for AdWords traffic'
                        ],
                        'fbclid' => [
                            'value' => $queryParams['fbclid']['value'] ?? null,
                            'count' => $queryParams['fbclid']['count'] ?? 0,
                            'url' => isset($queryParams['fbclid']) ? 'https://business.facebook.com/ads/manager/account_settings' : null,
                            'description' => 'Facebook Click Identifier'
                        ]
                    ];


                    // Calculate campaign effectiveness metrics
                    $page->campaign_metrics = [
                        'has_utm_params' => !empty(array_filter([
                            $queryParams['utm_source'] ?? null,
                            $queryParams['utm_medium'] ?? null,
                            $queryParams['utm_campaign'] ?? null
                        ])),
                        'tracking_completeness' => $this->calculateTrackingCompleteness($queryParams),
                        'is_paid_traffic' => $this->isPaidTraffic($queryParams)
                    ];

                    // Remove entries with null values
                    $page->campaign_data = array_filter($campaignParams, function($param) {
                        return !is_null($param['value']);
                    });
                }
            }
        }
        return $pageData;
    }

    protected function calculateTrackingCompleteness(array $queryParams): float
    {
        $requiredParams = ['utm_source', 'utm_medium', 'utm_campaign'];
        $optionalParams = ['utm_term', 'utm_content'];

        // Count required parameters that are present and not empty
        $requiredCount = count(array_filter($queryParams, function($param) use ($requiredParams) {
            return in_array($param['value'] ?? null, $requiredParams) && !empty($param['value']);
        }));

        // Count optional parameters that are present and not empty
        $optionalCount = count(array_filter($queryParams, function($param) use ($optionalParams) {
            return in_array($param['value'] ?? null, $optionalParams) && !empty($param['value']);
        }));

        // Calculate completeness score
        // Required params are worth 80% of the score (26.67% each)
        // Optional params are worth 20% of the score (10% each)
        $requiredScore = ($requiredCount / count($requiredParams)) * 0.8;
        $optionalScore = ($optionalCount / count($optionalParams)) * 0.2;

        return ($requiredScore + $optionalScore) * 100;
    }

    protected function isPaidTraffic(array $queryParams): bool
    {
        // Check for common paid traffic indicators in UTM parameters
        $paidMediums = ['cpc', 'ppc', 'paid', 'paidsearch', 'display'];
        $medium = strtolower($queryParams['utm_medium']['value'] ?? '');

        if (in_array($medium, $paidMediums)) {
            return true;
        }

        // Check for paid campaign sources
        $paidSources = ['adwords', 'google_ads', 'bing_ads', 'facebook_ads', 'linkedin_ads'];
        $source = strtolower($queryParams['utm_source']['value'] ?? '');

        if (in_array($source, $paidSources)) {
            return true;
        }

        // Check for ad platform click IDs
        $clickIds = ['gclid', 'msclkid', 'fbclid'];
        foreach ($clickIds as $id) {
            if (!empty($queryParams[$id]['value'])) {
                return true;
            }
        }

        return false;
    }

    protected function getCampaignSourceUrl(string $param, ?string $value): ?string
    {
        if (is_null($value)) {
            return null;
        }

        return match($param) {
            'utm_source' => match(strtolower($value)) {
                'google' => 'https://www.google.com',
                'facebook' => 'https://www.facebook.com',
                'twitter' => 'https://twitter.com',
                'linkedin' => 'https://www.linkedin.com',
                'instagram' => 'https://www.instagram.com',
                'bing' => 'https://www.bing.com',
                'yahoo' => 'https://www.yahoo.com',
                'tiktok' => 'https://www.tiktok.com',
                'pinterest' => 'https://www.pinterest.com',
                default => null
            },
            'utm_medium' => match(strtolower($value)) {
                'cpc', 'ppc' => 'https://ads.google.com',
                'social', 'social-media' => 'https://business.facebook.com',
                'email' => 'https://mail.google.com',
                'display' => 'https://displayads.google.com',
                'affiliate' => 'https://analytics.google.com',
                default => null
            },
            'utm_campaign', 'utm_term', 'utm_content' => null,
            default => null
        };
    }

    protected function getSearchEngineData()
    {
        return $this->query(
            category: 'source',
            type: 'search_engine',
            from: $this->from,
            to: $this->to,
            page: $this->page,
            paginate: false
        );
    }

    protected function getViewportData()
    {
        $viewport = $this->query(
            category: ['viewport_size', 'viewport_change'],
            groupBy: 'session_id',
            from: $this->from,
            to: $this->to,
            page: $this->page,
            paginate: false
        );

        return $viewport;
    }

    protected function getJsErrorData()
    {
        $jsErrors = $this->query(
            category: 'js_error',
            from: $this->from,
            to: $this->to,
            page: $this->page,
            paginate: false
        );

        return collect($jsErrors['data'])
            ->groupBy(function ($group) {
                $value = json_decode($group->value, true);
                $errorData = $value['value'];

                return implode('|', [
                    Arr::get($errorData, 'error.name', 'Unknown Error'),
                    Arr::get($errorData, 'message', ''),
                    Arr::get($errorData, 'source', '')
                ]);
            })
            ->map(function ($group) {
                $value = json_decode($group->first()->value, true);
                $errorData = $value['value'];

                return (object)[
                    'url' => Arr::get($errorData, 'url'),
                    'type' => Arr::get($errorData, 'type'),
                    'error' => Arr::get($errorData, 'error'),
                    'source' => Arr::get($errorData, 'source'),
                    'message' => Arr::get($errorData, 'message'),
                    'timestamp' => Arr::get($errorData, 'timestamp'),
                    'user_agent' => $this->extractUserAgents($group),
                    'count' => $group->sum('count'),
                    'unique_sessions' => $group->sum('unique_sessions'),
                    'stack' => $this->extractErrorStacks($group)
                ];
            })
            ->values();
    }

    protected function getSessionData()
    {
        return $this->query(
            category: 'session_id',
            from: $this->from,
            to: $this->to,
            page: $this->page,
            paginate: false
        );
    }

    protected function getOutboundLinkData()
    {
        return $this->query(
            category: 'outbound_link_click',
            from: $this->from,
            to: $this->to,
            page: $this->page,
            paginate: false
        );
    }

    protected function getGroupedData($group, $key, $metrics = [])
    {
        if (!empty($metrics)) {
            return collect($group->pluck($key))
                ->filter()
                ->unique()
                ->map(function($item) use ($metrics) {
                    $item = json_decode($item, true);
                    return collect($metrics)
                    ->mapWithKeys(function($metric) use ($item) {
                        return [$metric => Arr::get($item, $metric)];
                    })
                    ->toArray();
                })
                ->values()
                ->toArray();
        }
       return collect($group->pluck($key)->filter()->unique())
            ->map(fn($value) => [
                'value' => $value,
                'count' => $group->where($key, $value)->count()
            ])
            ->values()
            ->toArray();
    }

    protected function extractUserAgents($group)
    {
        return collect($group->pluck('value'))
            ->map(function($value) {
                $data = json_decode($value, true);
                return Arr::get($data, 'value', 'Unknown User Agent');
            })
            ->unique()
            ->values()
            ->toArray();
    }

    protected function extractErrorStacks($group)
    {
        return collect($group->pluck('value'))
            ->map(function($value) {
                $data = json_decode($value, true);
                return $data['value'];
            })
            ->toArray();
    }

    public function render()
    {
        return view('analytics::livewire.page', [
            'page' => $this->page,
            'browsers' => ['data' => $this->getBrowserData()],
            'locations' => ['data' => $this->getLocationData()],
            'searchEngine' => ['data' => $this->getSearchEngineData()],
            'jsErrors' => ['data' => $this->getJsErrorData()],
            'sessions' => ['data' => $this->getSessionData()],
            'outboundLinks' => ['data' => $this->getOutboundLinkData()],
            'reports' => $this->getReports()
        ]);
    }
}
