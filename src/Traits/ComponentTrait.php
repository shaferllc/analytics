<?php

namespace Shaferllc\Analytics\Traits;

use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Benchmark;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\DB;

trait ComponentTrait
{
    use Aggregates;
    public $from = null;
    public $to = null;
    public $daterange = 'today';
    public $range;
    public $search = '';
    public $sortBy = 'count';
    public $sort = 'asc';
    public $perPage = 12;
    private $query;
    public $display = 'compact';
    public $analytics_display = 'compact';
    public function boot(){

        if($user = request()->user()){
            $this->daterange =  $user->settings->get('analytics_daterange', 'today');
            $this->analytics_display =  $user->settings->get('analytics_display', 'compact');
        }
        $this->setDaterange($this->daterange);
    }

    public function getBrowserData()
    {
        $userAgents = $this->site->visitors()
            ->with(['browser', 'pages' => function($query) {
                $query->with(['visitors' => function($q) {
                    $q->select(
                        'analytics_visitors.id',
                        'analytics_page_visitors.page_id',
                        'analytics_page_visitors.visitor_id',
                        'analytics_page_visitors.first_visit_at',
                        'analytics_page_visitors.last_visit_at',
                        'analytics_page_visitors.total_visits',
                        'analytics_page_visitors.visit_key',
                        'analytics_page_visitors.is_base_record',
                        'analytics_page_visitors.query',
                        'analytics_page_visitors.hash',
                        'analytics_page_visitors.url_query',
                        'analytics_page_visitors.engagement_score',
                        'analytics_page_visitors.engagement_metrics'
                    );
                }]);
            }])
            ->whereBetween('first_visit_at', [$this->from, $this->to])
            ->select('analytics_visitors.id', 'first_visit_at', 'last_visit_at')
            ->get()
            ->groupBy('browser.user_agent')
            ->reject(function($visitors) {
                return $visitors->isEmpty() || !$visitors->first()->browser || !$visitors->first()->browser->user_agent;
            })
            ->map(function($visitors) {
                // Instead of just taking the first browser, collect all unique browser data
                $browsers = $visitors->map(function($visitor) {
                    return $visitor->browser;
                })->filter()->unique('id');

                // Group browsers by major attributes to handle different versions
                $browserVersions = $browsers->groupBy(function($browser) {
                    return $browser->name . '|' . $browser->os_name;
                })->map(function($groupedBrowsers) {
                    return [
                        'name' => $groupedBrowsers->pluck('name')->unique()->mapWithKeys(fn($name) => [
                            $name => $groupedBrowsers->where('name', $name)->count()
                        ]),
                        'versions' => $groupedBrowsers->pluck('version')->unique()->mapWithKeys(fn($version) => [
                            $version => $groupedBrowsers->where('version', $version)->count()
                        ]),
                        'os' => $groupedBrowsers->pluck('os_name')->unique()->mapWithKeys(function($os) use ($groupedBrowsers) {
                            $count = $groupedBrowsers->where('os_name', $os)->count();
                            return [$os => $count];
                        }),
                        'os_versions' => $groupedBrowsers->pluck('os_version')->unique()->mapWithKeys(fn($osVersion) => [
                            $osVersion => $groupedBrowsers->where('os_version', $osVersion)->count()
                        ]),

                        'device' => [
                            'types' => $groupedBrowsers->pluck('device_type')->unique()->mapWithKeys(fn($type) => [
                                $type => $groupedBrowsers->where('device_type', $type)->count()
                            ])->all(),
                            'brands' => $groupedBrowsers->pluck('device_brand')->unique()->mapWithKeys(fn($brand) => [
                                $brand => $groupedBrowsers->where('device_brand', $brand)->count()
                            ])->all(),
                            'models' => $groupedBrowsers->pluck('device_model')->unique()->mapWithKeys(fn($model) => [
                                $model => $groupedBrowsers->where('device_model', $model)->count()
                            ])->all(),
                            'cpu_cores' => $groupedBrowsers->pluck('cpu_cores')->unique()->mapWithKeys(fn($cores) => [
                                $cores => $groupedBrowsers->where('cpu_cores', $cores)->count()
                            ])->all(),
                            'device_memory' => $groupedBrowsers->pluck('device_memory')->unique()->mapWithKeys(fn($mem) => [
                                $mem . ' GB' => $groupedBrowsers->where('device_memory', $mem)->count()
                            ])->all(),
                            'device_memory_by_type' => $groupedBrowsers->groupBy('device_type')->map(function($devices) {
                                return $devices->pluck('device_memory')->mapWithKeys(fn($mem) => [
                                    $mem . ' GB' => $devices->where('device_memory', $mem)->count()
                                ])->all();
                            })->all()
                        ]
                    ];
                })->values()->first();


                // Get base page visits first
                $basePages = $visitors->pluck('pages')
                    ->flatten(1)
                    ->filter(function($page) {
                        return $page->visitors->first()->is_base_record;
                    })
                    ->groupBy('page')
                    ->map(function($pageGroup) {
                        $firstPage = $pageGroup->first();
                        return [
                            'page' => $firstPage->page,
                            'title' => $firstPage->title,
                            'total_visits' => $pageGroup->sum(function($page) {
                                return $page->visitors->first()->total_visits ?? 0;
                            }),
                            'first_visit' => $pageGroup->min(function($page) {
                                return $page->visitors->first()->first_visit_at ?? null;
                            }),
                            'last_visit' => $pageGroup->max(function($page) {
                                return $page->visitors->first()->last_visit_at ?? null;
                            }),
                            'variations' => []
                        ];
                    });

                // Process variations and merge them directly into basePages
                $visitors->pluck('pages')
                    ->flatten(1)
                    ->filter(function($page) {
                        return !$page->visitors->first()->is_base_record;
                    })
                    ->groupBy('page')
                    ->each(function($pageGroup, $pageUrl) use (&$basePages) {
                        if (!isset($basePages[$pageUrl])) return;

                        $basePages[$pageUrl]['variations'] = $pageGroup
                            ->groupBy(function($page) {
                                return $page->visitors->first()->visit_key;
                            })
                            ->map(function($variationGroup) {
                                $firstVariation = $variationGroup->first();
                                $visitor = $firstVariation->visitors->first();
                                return [
                                    'query' => $visitor->query,
                                    'hash' => $visitor->hash,
                                    'url_query' => $visitor->url_query,
                                    'total_visits' => $variationGroup->sum(function($page) {
                                        return $page->visitors->first()->total_visits ?? 0;
                                    }),
                                    'first_visit' => $variationGroup->min(function($page) {
                                        return $page->visitors->first()->first_visit_at ?? null;
                                    }),
                                    'last_visit' => $variationGroup->max(function($page) {
                                        return $page->visitors->first()->last_visit_at ?? null;
                                    })
                                ];
                            })
                            ->values()
                            ->toArray();
                    });

                // Pre-calculate visit days
                $visitDays = $visitors->pluck('pages')
                    ->flatten(1)
                    ->pluck('visitors')
                    ->flatten()
                    ->filter(fn($visitor) => !empty($visitor->first_visit_at))
                    ->map(function($visitor) {
                        return Carbon::parse($visitor->first_visit_at)
                            ->setTimezone('UTC') // Use a consistent timezone
                            ->format('Y-m-d');
                    })
                    ->unique()
                    ->count();

                // Pre-calculate hourly distribution
                $hourlyDistribution = $visitors->pluck('pages')
                    ->flatten(1)
                    ->filter(function($page) {
                        return $page->visitors->first()->is_base_record;
                    })
                    ->groupBy(function($page) {
                        return Carbon::parse($page->visitors->first()->first_visit_at)->hour;
                    })
                    ->map(function($pages) {
                        return $pages->sum(function($page) {
                            return $page->visitors->first()->total_visits ?? 0;
                        });
                    })
                    ->union(array_fill(0, 24, 0))
                    ->sortKeys()
                    ->toArray();

                return [
                    'browser_name' => $visitors->map(function($visitor) {
                        return $visitor->browser->name;
                    })->unique()->first(),
                    'browser_version' => $visitors->map(function($visitor) {
                        return $visitor->browser->version;
                    })->unique()->first(),
                    'user_agent' => $visitors->map(function($visitor) {
                        return $visitor->browser->user_agent;
                    })->unique()->first(),
                    'visit_days' => $visitDays,
                    'hourly_distribution' => $hourlyDistribution,
                    'engagement_metrics' => $visitors->pluck('pages')
                        ->flatten(1)
                        ->pluck('visitors')
                        ->flatten()
                        ->pluck('engagement_metrics')
                        ->filter()
                        ->values(),
                    'preferences' => $visitors->map(function($visitor) {
                        return [
                            'color_scheme' => $visitor->browser->color_scheme,
                            'reduced_motion' => $visitor->browser->reduced_motion
                        ];
                    })->unique()->values(),
                    'browser' => $browserVersions,
                    'languages' => $visitors->pluck('browser.language')
                        ->countBy()
                        ->mapWithKeys(fn($count, $lang) => [$lang => $count])
                        ->sortDesc(),
                    'timezones' => $visitors->pluck('browser.timezone')
                        ->countBy()
                        ->mapWithKeys(fn($count, $tz) => [$tz => $count])
                        ->sortDesc(),
                    'first_seen' => $visitors->min('first_visit_at'),
                    'last_seen' => $visitors->max('last_visit_at'),
                    'unique_visitors' => $visitors->count(),
                    'total_visits' => $visitors->sum(function($visitor) {
                        return $visitor->pages()
                            ->wherePivot('is_base_record', true)
                            ->sum('total_visits');
                    }),
                    'visitors' => $visitors->map(function($visitor) {
                        $pageVisitors = $visitor->pages()
                            ->with('visitors')
                            ->wherePivot('is_base_record', true)
                            ->get()
                            ->pluck('visitors')
                            ->flatten();

                        return [
                            'id' => $visitor->id,
                            'first_visit_at' => $visitor->first_visit_at,
                            'last_visit_at' => $visitor->last_visit_at,
                            'total_visits' => $pageVisitors->sum('total_visits'),
                            'engagement_score' => $pageVisitors->avg('engagement_score'),
                            'engagement_metrics' => $pageVisitors->pluck('engagement_metrics')->filter()->values(),
                            'browser' => [
                                'name' => $visitor->browser->name,
                                'version' => $visitor->browser->version,
                                'os' => $visitor->browser->os_name . ' ' . $visitor->browser->os_version,
                                'device' => [

                                    'type' => $visitor->browser->device_type,
                                    'brand' => $visitor->browser->device_brand,
                                    'model' => $visitor->browser->device_model,
                                    'cpu_cores' => $visitor->browser->cpu_cores,
                                    'device_memory' => $visitor->browser->device_memory,
                                    'os_version' => $visitor->browser->os_version,
                                ],
                                'preferences' => [
                                    'color_scheme' => $visitor->browser->color_scheme,
                                    'reduced_motion' => $visitor->browser->reduced_motion
                                ],
                                'language' => $visitor->browser->language,
                                'timezone' => $visitor->browser->timezone,
                                'user_agent' => $visitor->browser->user_agent
                            ]
                        ];
                    })->values(),
                    'visitor_retention_rate' => ($visitors->count() > 0)
                        ? ($visitors->sum(function($visitor) {
                            return $visitor->pages()
                                ->wherePivot('is_base_record', true)
                                ->sum('total_visits');
                        }) / $visitors->count()) * 100
                        : 0,
                    'pages' => $basePages->values()->toArray(),
                ];
            })->values();

        return [
            'data' => $userAgents,
            'aggregates' => [
                'category_diversity_index' => $userAgents->pipe(function($agents) {
                    $totalVisits = $agents->sum('total_visits');
                    return $agents->groupBy('browser.device.type')->map(function($group) use ($totalVisits) {
                        $proportion = $group->sum('total_visits') / $totalVisits;
                        return -1 * ($proportion * log($proportion));
                    })->sum();
                }),
                'unique_visitor_count' => $userAgents->sum('unique_visitors'),
                'highest_visits_per_visitor' => $userAgents->max(function($agent) {
                    return $agent['total_visits'] / $agent['unique_visitors'];
                }),
                'most_common_percentage' => $userAgents->pipe(function($agents) {
                    $totalVisits = $agents->sum('total_visits');
                    return $agents->max(function($agent) use ($totalVisits) {
                        return ($agent['total_visits'] / $totalVisits) * 100;
                    });
                }),
                'average_engagement_score' => $userAgents->avg(function($agent) {
                    return collect($agent['visitors'])->avg('engagement_score');
                }),
                'engagement_score_distribution' => $userAgents->groupBy(function($agent) {
                    $avgScore = collect($agent['visitors'])->avg('engagement_score');
                    return match(true) {
                        $avgScore >= 80 => 'Very High',
                        $avgScore >= 60 => 'High',
                        $avgScore >= 40 => 'Medium',
                        $avgScore >= 20 => 'Low',
                        default => 'Very Low'
                    };
                })->map(function($group) {
                    return [
                        'count' => $group->count(),
                        'visits' => $group->sum('total_visits'),
                        'unique_visitors' => $group->sum('unique_visitors')
                    ];
                })->toArray(),
                'total_browsers' => $userAgents->groupBy('browser.name')->count(),
                'total_device_types' => $userAgents->groupBy('browser.device.type')->count(),
                'device_type_distribution' => $userAgents->groupBy('browser.device.type')
                    ->map(function($group) {
                        return [
                            'count' => $group->count(),
                            'visits' => $group->sum('total_visits'),
                            'unique_visitors' => $group->sum('unique_visitors')
                        ];
                    })->toArray(),
                'os_distribution' => $userAgents->groupBy('browser.os')
                    ->map(function($group) {
                        return [
                            'count' => $group->count(),
                            'visits' => $group->sum('total_visits'),
                            'unique_visitors' => $group->sum('unique_visitors')
                        ];
                    })->toArray(),
                'total_user_agents' => $userAgents->count(),
                'total_operating_systems' => $userAgents->groupBy('browser.os_name')->count(),
                'total_device_models' => $userAgents->groupBy('browser.device.model')->count(),
                'total_device_brands' => $userAgents->groupBy('browser.device.brand')->count(),
                'total_browser_versions' => $userAgents->groupBy('browser.version')->count(),
                'total_languages' => $userAgents->groupBy('language')->count(),
                'total_timezones' => $userAgents->groupBy('timezone')->count(),
                'total_unique_visitors' => $userAgents->sum('unique_visitors'),
                'total_visits' => $userAgents->sum('total_visits'),
                'average_retention_rate' => $userAgents->avg('visitor_retention_rate'),
                'browser_distribution' => $userAgents->groupBy('browser.name')
                    ->map(function($group) {
                        return [
                            'count' => $group->count(),
                            'visits' => $group->sum('total_visits'),
                            'unique_visitors' => $group->sum('unique_visitors'),
                            'versions' => $group->groupBy('browser.version')
                                ->map(function($versionGroup) {
                                    return [
                                        'count' => $versionGroup->count(),
                                        'visits' => $versionGroup->sum('total_visits'),
                                        'unique_visitors' => $versionGroup->sum('unique_visitors'),
                                        'devices' => $versionGroup->groupBy('browser.device.type')
                                            ->map(function($deviceGroup) {
                                                return [
                                                    'count' => $deviceGroup->count(),
                                                    'visits' => $deviceGroup->sum('total_visits'),
                                                    'unique_visitors' => $deviceGroup->sum('unique_visitors'),
                                                    'brands' => $deviceGroup->groupBy('browser.device.brand')
                                                        ->map(function($brandGroup) {
                                                            return [
                                                                'count' => $brandGroup->count(),
                                                                'visits' => $brandGroup->sum('total_visits'),
                                                                'unique_visitors' => $brandGroup->sum('unique_visitors'),
                                                                'models' => $brandGroup->groupBy('browser.device.model')
                                                                    ->map(function($modelGroup) {
                                                                        return [
                                                                            'count' => $modelGroup->count(),
                                                                            'visits' => $modelGroup->sum('total_visits'),
                                                                            'unique_visitors' => $modelGroup->sum('unique_visitors')
                                                                        ];
                                                                    })->toArray()
                                                            ];
                                                        })->toArray(),
                                                    'operating_systems' => $deviceGroup->groupBy('browser.os')
                                                        ->map(function($osGroup) {
                                                            return [
                                                                'count' => $osGroup->count(),
                                                                'visits' => $osGroup->sum('total_visits'),
                                                                'unique_visitors' => $osGroup->sum('unique_visitors'),
                                                                'versions' => $osGroup->groupBy('browser.device.os_version')
                                                                    ->map(function($osVersionGroup) {
                                                                        return [
                                                                            'count' => $osVersionGroup->count(),
                                                                            'visits' => $osVersionGroup->sum('total_visits'),
                                                                            'unique_visitors' => $osVersionGroup->sum('unique_visitors')
                                                                        ];
                                                                    })->toArray()
                                                            ];
                                                        })->toArray()
                                                ];
                                            })->toArray()
                                    ];
                                })->toArray()
                        ];
                    })->toArray(),
                'language_distribution' => $userAgents->groupBy('language')
                    ->map(function($group) {
                        return [
                            'count' => $group->count(),
                            'visits' => $group->sum('total_visits')
                        ];
                    })->toArray(),
                'timezone_distribution' => $userAgents->groupBy('timezone')
                    ->map(function($group) {
                        return [
                            'count' => $group->count(),
                            'visits' => $group->sum('total_visits')
                        ];
                    })->toArray(),
                'device_brand_distribution' => $userAgents->groupBy('browser.device.brand')
                    ->map(function($group) {
                        return [
                            'count' => $group->count(),
                            'visits' => $group->sum('total_visits')
                        ];
                    })->toArray(),
                'visitor_frequency' => [
                    'single_visit' => $userAgents->filter(function($agent) {
                        return $agent['total_visits'] === 1;
                    })->count(),
                    'returning' => $userAgents->filter(function($agent) {
                        return $agent['total_visits'] > 1 && $agent['total_visits'] <= 5;
                    })->count(),
                    'frequent' => $userAgents->filter(function($agent) {
                        return $agent['total_visits'] > 5;
                    })->count()
                ],
                'average_pages_per_visitor' => $userAgents->avg(function($agent) {
                    return count($agent['pages'] ?? []);
                }),
                'average_visit_days' => $userAgents->avg('visit_days'),
                'average_visits_per_category' => $userAgents->avg('total_visits'),
                'average_visits_per_visitor' => $userAgents->sum('total_visits') / $userAgents->sum('unique_visitors'),
                'device_distribution' => $userAgents->groupBy('browser.device.type')
                    ->map(function($group) {
                        return [
                            'count' => $group->count(),
                            'visits' => $group->sum('total_visits')
                        ];
                    })->toArray(),
                'engagement_metrics' => [
                    'high_visits' => $userAgents->where('total_visits', '>', 5)->count(),
                    'medium_visits' => $userAgents->where('total_visits', '>', 1)
                        ->where('total_visits', '<=', 5)->count(),
                    'short_visits' => $userAgents->where('total_visits', '=', 1)->count()
                ],
                'hourly_distribution' => collect($userAgents->pluck('hourly_distribution'))
                    ->reduce(function ($carry, $hourly) {
                        foreach ($hourly as $hour => $visits) {
                            $carry[$hour] = ($carry[$hour] ?? 0) + $visits;
                        }
                        return $carry;
                    }, []),
                'most_active_browsers' => $userAgents->sortByDesc('total_visits')->take(5)
                    ->map(function($agent) {
                        return [
                            'name' => $agent['browser'][0]['name'] ?? 'Unknown',
                            'version' => collect($agent['browser'])->pluck('versions')->flatten()->first() ?? 'Unknown',
                            'visits' => $agent['total_visits']
                        ];
                    })->values()->toArray(),
                'most_visited_pages' => $userAgents->pluck('pages')
                    ->flatten(1)
                    ->sortByDesc('visits')
                    ->take(5)
                    ->map(function($page) {
                        return [
                            'title' => $page['title'] ?? null,
                            'url' => $page['url'] ?? null,
                            'visits' => $page['visits'] ?? 0
                        ];
                    })->values()->toArray(),
                'peak_activity_percentage' => $userAgents->pluck('hourly_distribution')
                    ->flatten()
                    ->whenNotEmpty(function($collection) {
                        return ($collection->max() / $collection->sum()) * 100;
                    }, fn() => 0),
                'peak_hours' => collect($userAgents->pluck('hourly_distribution'))
                    ->reduce(function ($carry, $hourly) {
                        foreach ($hourly as $hour => $visits) {
                            $carry[$hour] = ($carry[$hour] ?? 0) + $visits;
                        }
                        return $carry;
                    }, []),
                'total_categories' => $userAgents->count(),
                'total_visit_days' => $userAgents->sum('visit_days'),
                'total_visitors' => $userAgents->sum('unique_visitors'),
                'unique_browsers' => $userAgents->count(),
                'unique_visitors' => $userAgents->sum('unique_visitors'),
                'visit_frequency' => [
                    'daily' => $userAgents->where('visit_days', '>=', 1)->count(),
                    'monthly' => $userAgents->where('visit_days', '>=', 30)->count(),
                    'weekly' => $userAgents->where('visit_days', '>=', 7)->count()
                ],
                'visitor_retention_rate' => ($userAgents->sum('unique_visitors') > 0)
                    ? ($userAgents->where('total_visits', '>', 1)->sum('unique_visitors') / $userAgents->sum('unique_visitors')) * 100
                    : 0,
                'device_capabilities' => $userAgents->groupBy(function($agent) {
                    $browser = $agent['browser'] ?? [];
                    $device = $browser[0]['device'] ?? [];
                    return ($device['cpu_cores'] ?? 'Unknown') . ' cores / ' .
                           ($device['device_memory'] ?? 'Unknown') . ' GB';
                })->map(function($group) {
                    return [
                        'count' => $group->count(),
                        'visits' => $group->sum('total_visits'),
                        'unique_visitors' => $group->sum('unique_visitors')
                    ];
                })->toArray(),
                'hardware_distribution' => [
                    'cpu_cores' => $this->groupVisitorCount($userAgents, 'browser.device.cpu_cores'),
                    'memory' => $this->groupVisitorCount($userAgents, 'browser.device.device_memory')
                ],
            ]
        ];
    }

    public function getDeviceData()
    {
        $devices = $this->site->visitors()
            ->with(['browser', 'pages' => function($query) {
                $query->with(['visitors' => function($q) {
                    $q->select(
                        'analytics_visitors.id',
                        'analytics_page_visitors.page_id',
                        'analytics_page_visitors.visitor_id',
                        'analytics_page_visitors.first_visit_at',
                        'analytics_page_visitors.last_visit_at',
                        'analytics_page_visitors.total_visits',
                        'analytics_page_visitors.visit_key',
                        'analytics_page_visitors.is_base_record',
                        'analytics_page_visitors.query',
                        'analytics_page_visitors.hash',
                        'analytics_page_visitors.url_query',
                        'analytics_page_visitors.engagement_score',
                        'analytics_page_visitors.engagement_metrics'
                    );
                }]);
            }])
            ->whereBetween('first_visit_at', [$this->from, $this->to])
            ->select('analytics_visitors.id', 'first_visit_at', 'last_visit_at')
            ->get()
            ->groupBy('browser.device_type')
            ->reject(function($visitors) {
                return $visitors->isEmpty() || !$visitors->first()->browser;
            })
            ->map(function($visitors) {
                // Group by device brand and model
                $deviceDetails = $visitors->groupBy(function($visitor) {
                    return $visitor->browser->device_brand . '|' . $visitor->browser->device_model;
                })->map(function($groupedDevices) {
                    return [
                        'brand' => $groupedDevices->pluck('browser.device_brand')->unique()->first(),
                        'model' => $groupedDevices->pluck('browser.device_model')->unique()->first(),
                        'types' => $groupedDevices->pluck('browser.device_type')->unique()->mapWithKeys(fn($type) => [
                            $type => $groupedDevices->where('browser.device_type', $type)->count()
                        ])->all(),
                        'cpu_cores' => $groupedDevices->pluck('browser.cpu_cores')->unique()->mapWithKeys(fn($cores) => [
                            $cores => $groupedDevices->where('browser.cpu_cores', $cores)->count()
                        ])->all(),
                        'memory' => $groupedDevices->pluck('browser.device_memory')->unique()->mapWithKeys(fn($mem) => [
                            $mem . ' GB' => $groupedDevices->where('browser.device_memory', $mem)->count()
                        ])->all(),
                        'os_versions' => $groupedDevices->pluck('browser.os_version')->unique()->mapWithKeys(fn($osVersion) => [
                            $osVersion => $groupedDevices->where('browser.os_version', $osVersion)->count()
                        ])->all(),
                        'browsers' => $groupedDevices->pluck('browser.name')->unique()->mapWithKeys(fn($browser) => [
                            $browser => $groupedDevices->where('browser.name', $browser)->count()
                        ])->all()
                    ];
                })->values();

                // Get base page visits first
                $basePages = $visitors->pluck('pages')
                    ->flatten(1)
                    ->filter(function($page) {
                        return $page->visitors->first()->is_base_record;
                    })
                    ->groupBy('page')
                    ->map(function($pageGroup) {
                        $firstPage = $pageGroup->first();
                        return [
                            'page' => $firstPage->page,
                            'title' => $firstPage->title,
                            'total_visits' => $pageGroup->sum(function($page) {
                                return $page->visitors->first()->total_visits ?? 0;
                            }),
                            'first_visit' => $pageGroup->min(function($page) {
                                return $page->visitors->first()->first_visit_at ?? null;
                            }),
                            'last_visit' => $pageGroup->max(function($page) {
                                return $page->visitors->first()->last_visit_at ?? null;
                            }),
                            'variations' => []
                        ];
                    });

                // Process variations and merge them directly into basePages
                $visitors->pluck('pages')
                    ->flatten(1)
                    ->filter(function($page) {
                        return !$page->visitors->first()->is_base_record;
                    })
                    ->groupBy('page')
                    ->each(function($pageGroup, $pageUrl) use (&$basePages) {
                        if (!isset($basePages[$pageUrl])) return;

                        $basePages[$pageUrl]['variations'] = $pageGroup
                            ->groupBy(function($page) {
                                return $page->visitors->first()->visit_key;
                            })
                            ->map(function($variationGroup) {
                                $firstVariation = $variationGroup->first();
                                $visitor = $firstVariation->visitors->first();
                                return [
                                    'query' => $visitor->query,
                                    'hash' => $visitor->hash,
                                    'url_query' => $visitor->url_query,
                                    'total_visits' => $variationGroup->sum(function($page) {
                                        return $page->visitors->first()->total_visits ?? 0;
                                    }),
                                    'first_visit' => $variationGroup->min(function($page) {
                                        return $page->visitors->first()->first_visit_at ?? null;
                                    }),
                                    'last_visit' => $variationGroup->max(function($page) {
                                        return $page->visitors->first()->last_visit_at ?? null;
                                    })
                                ];
                            })
                            ->values()
                            ->toArray();
                    });

                // Pre-calculate visit days
                $visitDays = $visitors->pluck('pages')
                    ->flatten(1)
                    ->pluck('visitors')
                    ->flatten()
                    ->filter(fn($visitor) => !empty($visitor->first_visit_at))
                    ->map(function($visitor) {
                        return Carbon::parse($visitor->first_visit_at)
                            ->setTimezone('UTC') // Use a consistent timezone
                            ->format('Y-m-d');
                    })
                    ->unique()
                    ->count();

                // Pre-calculate hourly distribution
                $hourlyDistribution = $visitors->pluck('pages')
                    ->flatten(1)
                    ->filter(function($page) {
                        return $page->visitors->first()->is_base_record;
                    })
                    ->groupBy(function($page) {
                        return Carbon::parse($page->visitors->first()->first_visit_at)->hour;
                    })
                    ->map(function($pages) {
                        return $pages->sum(function($page) {
                            return $page->visitors->first()->total_visits ?? 0;
                        });
                    })
                    ->union(array_fill(0, 24, 0))
                    ->sortKeys()
                    ->toArray();

                return [
                    'device_type' => $visitors->pluck('browser.device_type')->unique()->first(),
                    'device_brand' => $visitors->pluck('browser.device_brand')->unique()->first(),
                    'device_model' => $visitors->pluck('browser.device_model')->unique()->first(),
                    'device_details' => $deviceDetails,
                    'visit_days' => $visitDays,
                    'hourly_distribution' => $hourlyDistribution,
                    'engagement_metrics' => $visitors->pluck('pages')
                        ->flatten(1)
                        ->pluck('visitors')
                        ->flatten()
                        ->pluck('engagement_metrics')
                        ->filter()
                        ->values(),
                    'preferences' => $visitors->map(function($visitor) {
                        return [
                            'color_scheme' => $visitor->browser->color_scheme,
                            'reduced_motion' => $visitor->browser->reduced_motion
                        ];
                    })->unique()->values(),
                    'total_visits' => $visitors->sum(function($visitor) {
                        return $visitor->pages()
                            ->wherePivot('is_base_record', true)
                            ->sum('total_visits');
                    }),
                    'unique_visitors' => $visitors->count(),
                    'visitor_retention_rate' => ($visitors->count() > 0)
                        ? ($visitors->sum(function($visitor) {
                            return $visitor->pages()
                                ->wherePivot('is_base_record', true)
                                ->sum('total_visits');
                        }) / $visitors->count()) * 100
                        : 0,
                    'pages' => $basePages->values()->toArray(),
                ];
            })->values();

        return [
            'data' => $devices,
            'aggregates' => [
                'total_devices' => $devices->count(),
                'total_visitors' => $devices->sum('unique_visitors'),
                'total_device_types' => $devices->groupBy('device_type')->count(),
                'total_device_brands' => $devices->groupBy('device_brand')->count(),
                'total_device_models' => $devices->groupBy('device_model')->count(),
                'device_type_distribution' => $devices->groupBy('device_type')
                    ->map(function($group) {
                        return [
                            'count' => $group->count(),
                            'visits' => $group->sum('total_visits'),
                            'unique_visitors' => $group->sum('unique_visitors')
                        ];
                    })->toArray(),
                'device_brand_distribution' => $devices->groupBy('device_brand')
                    ->map(function($group) {
                        return [
                            'count' => $group->count(),
                            'visits' => $group->sum('total_visits'),
                            'unique_visitors' => $group->sum('unique_visitors')
                        ];
                    })->toArray(),
                'device_model_distribution' => $devices->groupBy('device_model')
                    ->map(function($group) {
                        return [
                            'count' => $group->count(),
                            'visits' => $group->sum('total_visits'),
                            'unique_visitors' => $group->sum('unique_visitors')
                        ];
                    })->toArray(),
                'total_visits' => $devices->sum('total_visits'),
                'unique_visitors' => $devices->sum('unique_visitors'),
                'average_visit_days' => $devices->avg('visit_days'),
                'average_visits_per_visitor' => $devices->sum('total_visits') / $devices->sum('unique_visitors'),
                'device_capabilities' => $devices->groupBy(function($device) {
                    $browser = $device['browser'] ?? [];
                    $deviceDetails = $device['device_details'] ?? [];
                    $deviceInfo = $deviceDetails[0] ?? [];

                    // Safely handle array values for cpu_cores and memory
                    $cpuCores = $deviceInfo['cpu_cores'] ?? 'Unknown';
                    $memory = $deviceInfo['memory'] ?? 'Unknown';

                    // Convert arrays to strings if necessary
                    $cpuCores = is_array($cpuCores) ? implode(', ', $cpuCores) : $cpuCores;
                    $memory = is_array($memory) ? implode(', ', $memory) : $memory;

                    return "{$cpuCores} cores / {$memory} GB";
                })->map(function($group) {
                    return [
                        'count' => $group->count(),
                        'visits' => $group->sum('total_visits'),
                        'unique_visitors' => $group->sum('unique_visitors')
                    ];
                })->toArray(),
                'hardware_distribution' => [
                    'cpu_cores' => $devices->groupBy('cpu_cores')
                        ->map(function($group) {
                            return [
                                'count' => $group->count(),
                                'visits' => $group->sum('total_visits')
                            ];
                        })->toArray(),
                    'memory' => $devices->groupBy('memory')
                        ->map(function($group) {
                            return [
                                'count' => $group->count(),
                                'visits' => $group->sum('total_visits')
                            ];
                        })->toArray(),
                ],
            ]
        ];
    }

    public function updatedDaterange(string $range = null)
    {
        if ($user = request()->user()) {
            $user->settings->set('analytics_daterange', $range);
            $user->save();
        }

        $this->setDaterange($range);
    }

    public function updatedDisplay(string $display = null)
    {
        if ($user = request()->user()) {
            $user->settings->set('analytics_display', $display);
            $user->save();
        }
    }

    private function setDaterange(string $range = null)
    {
        $this->daterange = $range;
        $this->from = null;
        $this->to = null;

        switch ($this->daterange) {
            case 'today':
                $this->from = Carbon::today();
                $this->to = Carbon::today()->endOfDay();
                break;
            case 'yesterday':
                $this->from = Carbon::yesterday();
                $this->to = Carbon::yesterday()->endOfDay();
                break;
            case 'last7days':
                $this->from = Carbon::now()->subDays(6)->startOfDay();
                $this->to = Carbon::now()->endOfDay();
                break;
            case 'last30days':
                $this->from = Carbon::now()->subDays(29)->startOfDay();
                $this->to = Carbon::now()->endOfDay();
                break;
            case 'thisMonth':
                $this->from = Carbon::now()->startOfMonth();
                $this->to = Carbon::now()->endOfMonth();
                break;
            case 'lastMonth':
                $this->from = Carbon::now()->subMonth()->startOfMonth();
                $this->to = Carbon::now()->subMonth()->endOfMonth();
                break;
            case 'last3Months':
                $this->from = Carbon::now()->subMonths(2)->startOfMonth();
                $this->to = Carbon::now()->endOfMonth();
                break;
            case 'last6Months':
                $this->from = Carbon::now()->subMonths(5)->startOfMonth();
                $this->to = Carbon::now()->endOfMonth();
                break;
            case 'thisYear':
                $this->from = Carbon::now()->startOfYear();
                $this->to = Carbon::now()->endOfYear();
                break;
            case 'lastYear':
                $this->from = Carbon::now()->subYear()->startOfYear();
                $this->to = Carbon::now()->subYear()->endOfYear();
                break;
            case 'all':
                $this->from = Carbon::now()->subYears(100);
                $this->to = Carbon::now();
                break;
            case 'custom':
                // Do nothing, as custom range is handled separately
                break;
        }

        $from = Carbon::parse($this->from)->startOfDay();
        $to = Carbon::parse($this->to)->endOfDay();

        $this->range = $this->range($from, $to);
    }

    public function formatDuration(float $seconds): string
    {
        $seconds = round($seconds);

        if ($seconds < 60) {
            return $seconds . 's';
        }

        $minutes = floor($seconds / 60);
        $remainingSeconds = $seconds % 60;

        if ($minutes < 60) {
            return $minutes . 'm ' . $remainingSeconds . 's';
        }

        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;

        return $hours . 'h ' . $remainingMinutes . 'm ' . $remainingSeconds . 's';
    }

    public function resetFilters(){
        $this->reset(['search', 'sortBy', 'sort', 'perPage']);
    }

    public function setSortBy(string $sortBy): void
    {
        $this->sortBy = $sortBy;
        $this->sort = $this->sort === 'desc' ? 'asc' : 'desc';
    }
}
