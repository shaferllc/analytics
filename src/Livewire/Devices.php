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

#[Title('Devices')]
class Devices extends Component
{
    use DateRangeTrait, WithPagination, ComponentTrait;

    #[Locked]
    public Site $site;

    public $page = 0;

    public function render()
    {
        $perPage = 14;
        $deviceData = $this->getDeviceData();

        $sortedDevices = $deviceData['data']->sortByDesc('total_visits');
        $offset = max(0, ($this->page - 1) * $perPage);
        $items = $sortedDevices->slice($offset, $perPage);

        return view('analytics::livewire.devices', [
            'devices' => new LengthAwarePaginator(
                $items,
                $sortedDevices->count(),
                $perPage,
                $this->page
            ),
            'aggregates' => $deviceData['aggregates']
        ]);
    }

    #[Computed]
    public function overallStats()
    {
        return [
            'basic' => [
                'title' => null,
                'icon' => 'heroicon-o-device-phone-mobile',
                'items' => [
                    ['label' => __('Total Devices'), 'key' => 'total_devices', 'icon' => 'heroicon-o-device-phone-mobile', 'color' => 'emerald'],
                    ['label' => __('Total Visitors'), 'key' => 'total_visitors', 'icon' => 'heroicon-o-users', 'color' => 'blue'],
                    ['label' => __('Unique Devices'), 'key' => 'unique_visitors', 'icon' => 'heroicon-o-device-tablet', 'color' => 'purple'],
                    ['label' => __('Device Types'), 'key' => 'total_device_types', 'icon' => 'heroicon-o-device-tablet', 'color' => 'fuchsia'],
                    ['label' => __('Device Brands'), 'key' => 'total_device_brands', 'icon' => 'heroicon-o-tag', 'color' => 'rose'],
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

    public function basicStats($device, $aggregates)
    {
        return [
            [
                'icon' => 'heroicon-o-device-phone-mobile',
                'label' => __('Device Type'),
                'value' => $device['device_type'],
                'tooltip' => __('The type of device used (mobile, desktop, tablet)')
            ],
            [
                'icon' => 'heroicon-o-tag',
                'label' => __('Brand'),
                'value' => $device['device_brand'],
                'tooltip' => __('The manufacturer of the device')
            ],
            [
                'icon' => 'heroicon-o-clock',
                'label' => __('First Seen'),
                'value' => Carbon::parse($device['first_seen'])->format('M j, Y H:i'),
                'tooltip' => __('The date and time the device was first seen on the site.')
            ],
            [
                'icon' => 'heroicon-o-clock',
                'label' => __('Last Seen'),
                'value' => Carbon::parse($device['last_seen'])->format('M j, Y H:i'),
                'tooltip' => __('The date and time the device was last seen on the site.')
            ],
            [
                'icon' => 'heroicon-o-calendar',
                'label' => __('Visit Days'),
                'value' => number_format($device['visit_days']),
                'tooltip' => __('The number of days the device has visited the site.')
            ],
            [
                'icon' => 'heroicon-o-percent-badge',
                'label' => __('Percentage'),
                'value' => number_format(($device['unique_visitors'] / $aggregates['total_visitors']) * 100, 1) . '%',
                'tooltip' => __('The percentage of visitors that have visited the site.')
            ]
        ];
    }

    public function deviceInfo($device)
    {
        return [
            [
                'type' => 'grid',
                'title' => __('Device Specifications'),
                'icon' => 'heroicon-o-cpu-chip',
                'color' => 'indigo-500',
                'fields' => [
                    [
                        'label' => __('CPU Cores'),
                        'key' => 'device_details.0.cpu_cores',
                        'transform' => fn($value) => collect($value)->map(fn($count, $cores) => "$cores ($count)")->join(', '),
                        'tooltip' => __('Number of CPU cores in the device')
                    ],
                    [
                        'label' => __('Memory'),
                        'key' => 'device_details.0.memory',
                        'transform' => fn($value) => collect($value)->map(fn($count, $mem) => "$mem ($count)")->join(', '),
                        'tooltip' => __('Amount of RAM in the device')
                    ],
                    [
                        'label' => __('Device Type'),
                        'key' => 'device_details.0.device_type',
                        'transform' => fn($value) => $value,
                        'tooltip' => __('The type of device used (mobile, desktop, tablet)')
                    ],
                    [
                        'label' => __('Brand'),
                        'key' => 'device_details.0.device_brand',
                        'transform' => fn($value) => $value,
                        'tooltip' => __('The manufacturer of the device')
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
                        'tooltip' => __('<strong>Languages</strong><br>Shows the languages configured in the device.')
                    ],
                    'timezones' => [
                        'icon' => 'heroicon-o-clock',
                        'label' => __('Timezones'),
                        'transform' => fn($value) => collect($value)->filter()->map(fn($count, $tz) => Str::afterLast($tz, '/') . " ($count)")->take(2)->toArray(),
                        'tooltip' => __('<strong>Timezones</strong><br>Indicates the timezones detected from the device.')
                    ]
                ]
            ],
            [
                'type' => 'raw',
                'title' => __('Raw User Agent'),
                'icon' => 'heroicon-o-code-bracket',
                'color' => 'rose-500',
                'value' => $device['parsed']['raw'] ?? $device['user_agent']
            ]
        ];
    }

    public function visitStats($device)
    {
        return [
            [
                'color' => 'blue',
                'icon' => 'heroicon-o-users',
                'label' => __('Unique Visitors'),
                'value' => number_format($device['unique_visitors']),
                'tooltip' => __('Number of unique visitors using this device')
            ],
            [
                'color' => 'emerald',
                'icon' => 'heroicon-o-arrow-path',
                'label' => __('Total Visits'),
                'value' => number_format($device['total_visits']),
                'tooltip' => __('Total visits from this device')
            ],
            [
                'color' => 'purple',
                'icon' => 'heroicon-o-chart-bar',
                'label' => __('Visits/Visitor'),
                'value' => number_format($device['total_visits'] / $device['unique_visitors'], 1),
                'tooltip' => __('The average number of visits per unique visitor.')
            ],
        ];
    }
}
