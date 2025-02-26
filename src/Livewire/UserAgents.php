<?php

namespace Shaferllc\Analytics\Livewire;

use App\Models\Site;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithPagination;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Title;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Computed;
use Shaferllc\Analytics\Traits\ComponentTrait;
use Shaferllc\Analytics\Traits\DateRangeTrait;
use Illuminate\Pagination\LengthAwarePaginator;

#[Title('User Agents')]
class UserAgents extends Component
{
    use DateRangeTrait, WithPagination, ComponentTrait;

    #[Locked]
    public Site $site;

    public $page = 0;

    public function render()
    {
        $perPage = 14;
        $browserData = $this->getBrowserData();

        // Partition and paginate
        [$bots, $regularUserAgents] = collect($browserData['data'])->partition(fn($ua) =>
            Str::contains(strtolower($ua['user_agent']), array_keys(bots()), true)
        );

        $sortedUserAgents = $regularUserAgents->sortByDesc('total_visits');
        $offset = max(0, ($this->page - 1) * $perPage);
        $items = $sortedUserAgents->slice($offset, $perPage);

        return view('analytics::livewire.user-agents', [
            'userAgents' => new LengthAwarePaginator(
                $items,
                $sortedUserAgents->count(),
                $perPage,
                $this->page
            ),
            'bots' => $bots,
            'aggregates' => $browserData['aggregates']
        ]);
    }

    #[Computed]
    public function overallStats()
    {
        return [
            'basic' => [
                'title' => null,
                'icon' => 'heroicon-o-globe-alt',
                'items' => [
                    ['label' => __('Total User Agents'), 'key' => 'total_categories', 'icon' => 'heroicon-o-globe-alt', 'color' => 'emerald'],
                    ['label' => __('Total Visitors'), 'key' => 'total_visitors', 'icon' => 'heroicon-o-users', 'color' => 'blue'],
                    ['label' => __('Unique Visitors'), 'key' => 'unique_visitors', 'icon' => 'heroicon-o-user', 'color' => 'purple', 'tooltip' => __('The number of unique visitors to the site.')],
                    ['label' => __('Total Browsers'), 'key' => 'total_browsers', 'icon' => 'heroicon-o-globe-alt', 'color' => 'sky', 'tooltip' => __('The total number of browsers used by visitors.')],
                    ['label' => __('Device Types'), 'key' => 'total_device_types', 'icon' => 'heroicon-o-device-tablet', 'color' => 'fuchsia', 'tooltip' => __('The total number of device types used by visitors.')],
                ]
            ],
            'visit_metrics' => [
                'title' => __('Visit Metrics'),
                'icon' => 'heroicon-o-chart-bar',
                'items' => [
                    ['label' => __('Total Visits'), 'key' => 'total_visits', 'icon' => 'heroicon-o-arrow-path', 'color' => 'red'],
                    ['label' => __('Avg Visits/Category'), 'key' => 'average_visits_per_category', 'icon' => 'heroicon-o-calculator', 'color' => 'orange'],
                    ['label' => __('Avg Visits/Visitor'), 'key' => 'average_visits_per_visitor', 'icon' => 'heroicon-o-user-group', 'color' => 'yellow'],
                    ['label' => __('Languages'), 'key' => 'total_languages', 'icon' => 'heroicon-o-language', 'color' => 'lime'],
                    ['label' => __('Timezones'), 'key' => 'total_timezones', 'icon' => 'heroicon-o-clock', 'color' => 'amber'],
                ]
            ],
            'engagement' => [
                'title' => __('Engagement'),
                'icon' => 'heroicon-o-chart-pie',
                'items' => [
                    ['label' => __('Retention Rate'), 'key' => 'visitor_retention_rate', 'icon' => 'heroicon-o-heart', 'color' => 'pink'],
                    ['label' => __('Engagement Score'), 'key' => 'average_engagement_score', 'icon' => 'heroicon-o-star', 'color' => 'violet'],
                    ['label' => __('Peak Activity'), 'key' => 'peak_activity_percentage', 'icon' => 'heroicon-o-bolt', 'color' => 'cyan'],
                    ['label' => __('Device Models'), 'key' => 'total_device_models', 'icon' => 'heroicon-o-device-phone-mobile', 'color' => 'indigo'],
                    ['label' => __('Device Brands'), 'key' => 'total_device_brands', 'icon' => 'heroicon-o-tag', 'color' => 'rose'],
                ]
            ],
            'advanced_metrics' => [
                'title' => __('Advanced Metrics'),
                'icon' => 'heroicon-o-beaker',
                'items' => [
                    ['label' => __('Diversity Index'), 'key' => 'category_diversity_index', 'icon' => 'heroicon-o-variable', 'color' => 'teal'],
                    ['label' => __('Most Common'), 'key' => 'most_common_percentage', 'icon' => 'heroicon-o-document-text', 'color' => 'lime'],
                    ['label' => __('Highest Visits/User'), 'key' => 'highest_visits_per_visitor', 'icon' => 'heroicon-o-arrow-trending-up', 'color' => 'emerald'],
                    ['label' => __('Avg Pages/Visitor'), 'key' => 'average_pages_per_visitor', 'icon' => 'heroicon-o-document-chart-bar', 'color' => 'blue'],
                    ['label' => __('Avg Visit Days'), 'key' => 'average_visit_days', 'icon' => 'heroicon-o-calendar-days', 'color' => 'purple'],
                ]
            ]
        ];
    }

    public function basicStats($agent, $aggregates)
    {
        return [
            [
                'icon' => 'heroicon-o-clock',
                'label' => __('First Seen'),
                'value' => Carbon::parse($agent['first_seen'])->format('M j, Y H:i'),
                'tooltip' => __('The date and time the user agent was first seen on the site.')
            ],
            [
                'icon' => 'heroicon-o-clock',
                'label' => __('Last Seen'),
                'value' => Carbon::parse($agent['last_seen'])->format('M j, Y H:i'),
                'tooltip' => __('The date and time the user agent was last seen on the site.')
            ],
            [
                'icon' => 'heroicon-o-calendar',
                'label' => __('Visit Days'),
                'value' => number_format($agent['visit_days']),
                'tooltip' => __('The number of days the user agent has visited the site.')
            ],
            [
                'icon' => 'heroicon-o-percent-badge',
                'label' => __('Percentage'),
                'value' => number_format(($agent['unique_visitors'] / $aggregates['total_visitors']) * 100, 1) . '%',
                'tooltip' => __('The percentage of visitors that have visited the site.')
            ]
        ];
    }

    public function browserInfo($agent)
    {
        return [
            [
                'type' => 'grid',
                'title' => __('Browser Information'),
                'icon' => 'heroicon-o-globe-alt',
                'color' => 'indigo-500',
                'fields' => [
                    [
                        'label' => __('Browser'),
                        'key' => 'browser.name',
                        'class' => 'col-span-2',
                        'transform' => fn($value) => $value->map(fn($count, $name) => "$name ($count)")->join(', '),
                        'tooltip' => __('The browser used by the visitor. This includes the specific browser name and version detected during their visits. Browsers are identified through the user agent string sent by the visitor\'s device. <br><br>Examples: Chrome, Firefox, Safari, Edge. <br><br>Multiple entries indicate the visitor used different browsers across sessions.'),
                        'iconString' => fn($value) => '<img src="'.asset('vendor/analytics/icons/browsers/'.formatBrowser($value).'.svg').'" alt="'.formatBrowser($value).'" class="w-4 h-4">',
                    ],
                    [
                        'label' => __('Version'),
                        'key' => 'browser.versions',
                        'transform' => fn($value) => $value->map(fn($count, $version) => "$version ($count)")->join(', '),
                        'tooltip' => __('<strong>Browser Version</strong><br>Shows the specific versions of the browser used by the visitor across their sessions. Multiple versions indicate the visitor accessed your site from different browser versions.<br><br><span class="text-sm text-slate-500">Example:<br>Chrome 120.0.0.0 (3)<br>Chrome 119.0.0.0 (1)</span>'),
                        'iconString' => fn($value) => '<img src="'.asset('vendor/analytics/icons/browsers/'.formatBrowser($value).'.svg').'" alt="'.formatBrowser($value).'" class="w-4 h-4">',
                    ],
                    [
                        'label' => __('OS'),
                        'key' => 'browser.os',
                        'transform' => fn($value) => $value->map(fn($count, $os) => "$os ($count)")->join(', '),
                        'tooltip' => __('<strong>Operating System</strong><br>Indicates the operating system platform used by the visitor. This helps identify whether they\'re using Windows, macOS, Linux, iOS, Android, or other systems.<br><br><span class="text-sm text-slate-500">Example:<br>Windows (4)<br>macOS (2)</span>'),
                        'iconString' => fn($value) => '<img src="'.asset('vendor/analytics/icons/os/'.formatOperatingSystem($value).'.svg').'" alt="'.formatOperatingSystem($value).'" class="w-4 h-4">',
                    ],
                    [
                        'label' => __('OS Version'),
                        'key' => 'browser.os_versions',
                        'transform' => fn($value) => $value->map(fn($count, $osVersion) => "$osVersion ($count)")->join(', '),
                        'tooltip' => __('<strong>OS Version</strong><br>Shows the specific versions of the operating system used by the visitor. This provides granular insight into the visitor\'s system environment.<br><br><span class="text-sm text-slate-500">Example:<br>Windows 10 (3)<br>Windows 11 (1)</span>'),
                        'iconString' => fn($value) => '<img src="'.asset('vendor/analytics/icons/os/'.formatBrowser($value).'.svg').'" alt="'.formatBrowser($value).'" class="w-4 h-4">',
                    ]
                ]
            ],
            [
                'type' => 'key-value',
                'title' => __('Device Information'),
                'icon' => 'heroicon-o-device-phone-mobile',
                'color' => 'purple-500',
                'fields' => [
                    'browser.device.types' => [
                        'label' => __('Device Types'),
                        'transform' => fn($value) => collect($agent['browser']['device']['types'])
                            ->map(fn($count, $type) => "$type ($count)")
                            ->toArray(),
                        'tooltip' => __('<strong>Device Types</strong><br>Shows the types of devices used by the visitor, such as mobile, desktop, or tablet. This helps identify the primary device category used to access your site.<br><br><span class="text-sm text-slate-500">Example:<br>Mobile (4)<br>Desktop (2)</span>'),
                        'iconString' => fn($value) => '<img src="'.asset('vendor/analytics/icons/devices/'.formatDevice($value).'.svg').'" alt="'.formatDevice($value).'" class="w-4 h-4">',
                    ],
                    'browser.device.brands' => [
                        'label' => __('Brands'),
                        'transform' => fn($value) => collect($agent['browser']['device']['brands'])
                            ->map(fn($count, $brand) => "$brand ($count)")
                            ->toArray(),
                        'tooltip' => __('<strong>Device Brands</strong><br>Indicates the manufacturer brands of the devices used by the visitor. This provides insight into the hardware ecosystem of your visitors.<br><br><span class="text-sm text-slate-500">Example:<br>Apple (3)<br>Samsung (1)</span>'),
                        'iconString' => fn($value) => '<img src="'.asset('vendor/analytics/icons/devices/'.formatDevice($value).'.svg').'" alt="'.formatDevice($value).'" class="w-4 h-4">',
                    ],
                    'browser.device.models' => [
                        'label' => __('Models'),
                        'transform' => fn($value) => collect($agent['browser']['device']['models'])
                            ->map(fn($count, $model) => "$model ($count)")
                            ->toArray(),
                        'tooltip' => __('<strong>Device Models</strong><br>Shows the specific device models used by the visitor. This provides granular insight into the exact hardware being used.<br><br><span class="text-sm text-slate-500">Example:<br>iPhone 14 Pro (2)<br>Galaxy S23 (1)</span>'),
                        'iconString' => fn($value) => '<img src="'.asset('vendor/analytics/icons/devices/'.formatDevice($value).'.svg').'" alt="'.formatDevice($value).'" class="w-4 h-4">',
                    ],
                    'browser.device.cpu_cores' => [
                        'label' => __('CPU Cores'),
                        'icon' => 'heroicon-o-cpu-chip',
                        'transform' => fn($value) => collect($agent['browser']['device']['cpu_cores'])
                            ->map(fn($count, $cores) => "$cores ($count)")
                            ->toArray(),
                        'tooltip' => __('<strong>CPU Cores</strong><br>Indicates the number of processor cores in the devices used by the visitor. This provides insight into the processing power of visitor devices.<br><br><span class="text-sm text-slate-500">Example:<br>8 (3)<br>4 (1)</span>'),
                        'iconString' => fn($value) => '<img src="'.asset('vendor/analytics/icons/devices/'.formatDevice($value).'.svg').'" alt="'.formatDevice($value).'" class="w-4 h-4">',
                    ],
                    'browser.device.device_memory' => [
                        'label' => __('Memory'),
                        'transform' => fn($value) => collect($agent['browser']['device']['device_memory'])
                            ->map(fn($count, $mem) => "{$mem} ($count)")
                            ->toArray(),
                        'tooltip' => __('<strong>Device Memory</strong><br>Shows the amount of RAM in the devices used by the visitor. This provides insight into the memory capacity of visitor devices.<br><br><span class="text-sm text-slate-500">Example:<br>8GB (3)<br>16GB (1)</span>'),
                        'iconString' => fn($value) => '<img src="'.asset('vendor/analytics/icons/devices/'.formatDevice($value).'.svg').'" alt="'.formatDevice($value).'" class="w-4 h-4">',
                    ]
                ]
            ],
            [
                'type' => 'key-value',
                'title' => __('Additional Features'),
                'icon' => 'heroicon-o-puzzle-piece',
                'color' => 'amber-500',
                'fields' => [
                    'languages' => [
                        'icon' => 'heroicon-o-language',
                        'label' => __('Languages'),
                        'transform' => fn($value) => collect($value)->filter()->map(fn($count, $lang) => Str::limit($lang, 5) . " ($count)")->take(3)->toArray(),
                        'tooltip' => __('<strong>Languages</strong><br>Shows the languages configured in the visitor\'s browser. This helps identify the preferred languages of your visitors.<br><br><span class="text-sm text-slate-500">Example:<br>en (3)<br>es (1)</span>')
                    ],
                    'timezones' => [
                        'icon' => 'heroicon-o-clock',
                        'label' => __('Timezones'),
                        'transform' => fn($value) => collect($value)->filter()->map(fn($count, $tz) => Str::afterLast($tz, '/') . " ($count)")->take(2)->toArray(),
                        'tooltip' => __('<strong>Timezones</strong><br>Indicates the timezones detected from the visitor\'s browser. This provides insight into the geographical distribution of your visitors.<br><br><span class="text-sm text-slate-500">Example:<br>New_York (2)<br>London (1)</span>')
                    ]
                ]
            ],
            [
                'type' => 'raw',
                'title' => __('Raw User Agent'),
                'icon' => 'heroicon-o-code-bracket',
                'color' => 'rose-500',
                'value' => $agent['parsed']['raw'] ?? $agent['user_agent']
            ]
        ];
    }

    public function visitStats($agent)
    {
        return [
            [
                'color' => 'blue',
                'icon' => 'heroicon-o-users',
                'label' => __('Unique Visitors'),
                'value' => number_format($agent['unique_visitors']),
                'tooltip' => __('The number of unique visitors to the site.')
            ],
            [
                'color' => 'emerald',
                'icon' => 'heroicon-o-arrow-path',
                'label' => __('Total Visits'),
                'value' => number_format($agent['total_visits']),
                'tooltip' => __('The total number of visits to the site.')
            ],
            [
                'color' => 'purple',
                'icon' => 'heroicon-o-chart-bar',
                'label' => __('Visits/Visitor'),
                'value' => number_format($agent['total_visits'] / $agent['unique_visitors'], 1),
                'tooltip' => __('The average number of visits per unique visitor.')
            ],
        ];
    }

}
