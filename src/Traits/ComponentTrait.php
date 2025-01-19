<?php

namespace Shaferllc\Analytics\Traits;

use Illuminate\Support\Carbon;
use Livewire\Attributes\Computed;

trait ComponentTrait
{
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


    #[Computed]
    public function getFormattedDateRange(){
        return $this->range['from']->format('m/d/Y') . ' - ' . $this->range['to']->format('m/d/Y');
    }
    /**
     * Retrieve session analytics data with page views and aggregated statistics.
     */
    public function sessions(?Carbon $from = null, ?Carbon $to = null): array
    {


        $visitors = $this->site->visitors()
            ->with(['pages'])
            // ->whereBetween('analytics_page_visitors.created_at', [$this->range['from'], $this->range['to']])
            ->paginate($this->perPage)
            ->through(function ($visitor) {
                $pages = $visitor->pages;

                // Calculate page-level aggregates
                $totalDuration = $pages->sum('page_visitor.total_duration_seconds');
                $totalTimeSpent = $pages->sum('page_visitor.total_time_spent');
                $totalVisits = $pages->sum('page_visitor.total_visits');
                $avgTimePerPage = $pages->count() > 0 ? $totalDuration / $pages->count() : 0;
                $firstVisit = $pages->min('page_visitor.first_visit_at');
                $lastVisit = $pages->max('page_visitor.last_visit_at');
                $lastVisit = $pages->max('page_visitor.last_visit_at');

                return [
                    'id' => $visitor->id,
                    'session_id' => $visitor->session_id,
                    'timezone' => $visitor->timezone,
                    'language' => $visitor->language,
                    'city' => $visitor->meta_data->city,
                    'country' => $visitor->meta_data->country,
                    'continent' => $visitor->meta_data->continent,

                    // Session aggregates
                    'total_pages' => $pages->count(),
                    'total_duration' => $totalDuration,
                    'total_time_spent' => $totalTimeSpent,
                    'total_visits' => $totalVisits,
                    'avg_time_per_page' => $avgTimePerPage,
                    'first_visit' => $firstVisit,
                    'last_visit' => $lastVisit,
                    'session_duration' => $lastVisit ? $firstVisit->diffInSeconds($lastVisit) : 0,

                    'pages' => $visitor->pages->map(function ($page) {
                        return (object) [
                            // page data
                            'id' => $page->id,
                            'page' => $page->page,
                            'created_at' => $page->created_at,
                            'updated_at' => $page->updated_at,
                            'path' => $page->path,
                            'title' => $page->meta_data->title,
                            'charset' => $page->meta_data->charset,
                            'visit_count' => $page->meta_data->visit_count,

                            // page visitor data
                            'visitor_id' => $page->page_visitor->visitor_id,
                            'page_id' => $page->page_visitor->page_id,
                            'site_id' => $page->page_visitor->site_id,
                            'start_session_at' => $page->page_visitor->start_session_at,
                            'end_session_at' => $page->page_visitor->end_session_at,
                            'total_duration_seconds' => $page->page_visitor->total_duration_seconds,
                            'total_time_spent' => $page->page_visitor->total_time_spent,
                            'total_visits' => $page->page_visitor->total_visits,
                            'last_visit_at' => $page->page_visitor->last_visit_at,
                            'first_visit_at' => $page->page_visitor->first_visit_at,

                            // Additional page metrics
                            'avg_time_on_page' => $page->page_visitor->total_visits > 0
                                ? $page->page_visitor->total_duration_seconds / $page->page_visitor->total_visits
                                : 0,
                            'bounce_rate' => $page->page_visitor->total_duration_seconds < 10 ? 100 : 0, // Simple bounce rate calculation

                            // Engagement metrics
                            'engagement_rate' => $page->page_visitor->total_duration_seconds > 30 ? 100 : 0,
                            // 'exit_rate' => $page === $page->page_visitor->pages->last() ? 100 : 0,

                            // // Time-based metrics
                            // 'time_of_day' => $page->page_visitor->first_visit_at ? $page->page_visitor->first_visit_at->format('H:i') : null,
                            // 'day_of_week' => $page->page_visitor->first_visit_at ? $page->page_visitor->first_visit_at->format('l') : null,

                            // // Session depth metrics
                            // 'page_depth' => $page->page_visitor->visitor->pages->search(function($p) use ($page) {
                            //     return $p->id === $page->id;
                            // }) + 1,
                            // 'is_landing_page' => $page === $page->page_visitor->visitor->pages->first() ? true : false,
                            // 'is_exit_page' => $page === $page->page_visitor->visitor->pages->last() ? true : false,

                            // // Interaction metrics
                            // 'time_to_next_page' => $page !== $page->page_visitor->visitor->pages->last()
                            //     ? $page->page_visitor->visitor->pages->get(
                            //         $page->page_visitor->visitor->pages->search(function($p) use ($page) {
                            //             return $p->id === $page->id;
                            //         }) + 1
                            //     )->page_visitor->first_visit_at->diffInSeconds($page->page_visitor->first_visit_at)
                            //     : null
                        ];
                    })
                ];
            });

        // Add global aggregates
        $allVisitors = $this->site->visitors()->with(['pages'])->get();
        $globalStats = [
            'total_sessions' => (int)$allVisitors->count(),
            'total_pageviews' => (int)$allVisitors->sum(function($visitor) {
                return $visitor->pages->sum('page_visitor.total_visits');
            }),
            'avg_session_duration' => $this->formatDuration((int)$allVisitors->avg(function($visitor) {
                $lastVisit = $visitor->pages->max('page_visitor.last_visit_at');
                $firstVisit = $visitor->pages->min('page_visitor.first_visit_at');
                // The issue is that we're calculating last - first which could be negative
                // We should swap the order to ensure positive duration
                return $lastVisit && $firstVisit ? Carbon::parse($firstVisit)->diffInSeconds(Carbon::parse($lastVisit)) : 0;
            })),
            'avg_pages_per_session' => (int)$allVisitors->avg(function($visitor) {
                return $visitor->pages->count();
            }),
            'bounce_rate' => (int)$allVisitors->avg(function($visitor) {
                // A bounce is when a visitor only views one page and leaves
                // If visitor viewed only 1 page, bounce rate is 100%
                // If visitor viewed more than 1 page, bounce rate is 0%
                return $visitor->pages->count() === 1 ? 100 : 0;
            }),
            // Session metrics
            'sessions_today' => (function() use ($allVisitors) {
                $visitors = $allVisitors->filter(function($visitor) {
                    // dump($visitor->pages->first()->page_visitor->first_visit_at);
                    return $visitor->pages->contains(function($page) {
                        return $page->page_visitor->first_visit_at >= Carbon::today();
                    });
                });
                return (int)$visitors->count();
            })(),
            'sessions_yesterday' => (function() use ($allVisitors) {
                $visitors = $allVisitors->filter(function($visitor) {
                    return $visitor->pages->contains(function($page) {
                        return $page->page_visitor->first_visit_at >= Carbon::yesterday() &&
                               $page->page_visitor->first_visit_at < Carbon::today();
                    });
                });
                return (int)$visitors->count();
            })(),
            'sessions_this_week' => (function() use ($allVisitors) {
                $visitors = $allVisitors->filter(function($visitor) {
                    return $visitor->pages->contains(function($page) {
                        return $page->page_visitor->first_visit_at >= Carbon::now()->startOfWeek();
                    });
                });
                return (int)$visitors->count();
            })(),
            'sessions_last_week' => (function() use ($allVisitors) {
                $visitors = $allVisitors->filter(function($visitor) {
                    return $visitor->pages->contains(function($page) {
                        return $page->page_visitor->first_visit_at >= Carbon::now()->subWeek()->startOfWeek() &&
                               $page->page_visitor->first_visit_at < Carbon::now()->startOfWeek();
                    });
                });
                return (int)$visitors->count();
            })(),
            'sessions_this_month' => (function() use ($allVisitors) {
                $visitors = $allVisitors->filter(function($visitor) {
                    return $visitor->pages->contains(function($page) {
                        return $page->page_visitor->first_visit_at >= Carbon::now()->startOfMonth();
                    });
                });
                return (int)$visitors->count();
            })(),
            'sessions_last_month' => (function() use ($allVisitors) {
                $visitors = $allVisitors->filter(function($visitor) {
                    return $visitor->pages->contains(function($page) {
                        return $page->page_visitor->first_visit_at >= Carbon::now()->subMonth()->startOfMonth() &&
                               $page->page_visitor->first_visit_at < Carbon::now()->startOfMonth();
                    });
                });
                return (int)$visitors->count();
            })(),
            'sessions_this_year' => (function() use ($allVisitors) {
                $visitors = $allVisitors->filter(function($visitor) {
                    return $visitor->pages->contains(function($page) {
                        return $page->page_visitor->first_visit_at >= Carbon::now()->startOfYear();
                    });
                });
                return (int)$visitors->count();
            })(),
            'sessions_last_year' => (function() use ($allVisitors) {
                $visitors = $allVisitors->filter(function($visitor) {
                    return $visitor->pages->contains(function($page) {
                        return $page->page_visitor->first_visit_at >= Carbon::now()->subYear()->startOfYear() &&
                               $page->page_visitor->first_visit_at < Carbon::now()->startOfYear();
                    });
                });
                return (int)$visitors->count();
            })(),



            // // Top users and sessions
            'top_sessions_today' => $allVisitors->filter(function($visitor) {
                    foreach ($visitor->pages as $page) {
                        if ($page->page_visitor->first_visit_at >= Carbon::today()) {
                            return true;
                        }
                    }
                    return false;
                })
                ->sortByDesc(function($visitor) {
                    $totalVisits = 0;
                    foreach ($visitor->pages as $page) {
                        $totalVisits += $page->page_visitor->total_visits;
                    }
                    return $totalVisits;
                })
                ->take(10)
                ->values(),
            'top_users_all_time' => $allVisitors
                ->unique('visitor_id')
                ->sortByDesc(function($visitor) {
                    return $visitor->pages->sum('page_visitor.total_visits');
                })
                ->take(10)
                ->values(),
            'top_session_pages_today' => $allVisitors->flatMap->pages
                ->where('page_visitor.first_visit_at', '>=', Carbon::today())
                ->groupBy('path')
                ->map(function ($pages) {
                    return [
                        'path' => $pages->first()->path,
                        'title' => $pages->first()->meta_data->title,
                        'visits' => (int)$pages->sum('page_visitor.total_visits')
                    ];
                })
                ->sortByDesc('visits')
                ->take(10)
                ->values(),
            'peak_hour_this_week' => $allVisitors->flatMap->pages
                ->filter(function($page) {
                    return $page->page_visitor->first_visit_at >= Carbon::now()->startOfWeek();
                })
                ->groupBy(function($page) {
                    return Carbon::parse($page->page_visitor->first_visit_at)->format('H');
                })
                ->map->count()
                ->sortDesc()
                ->keys()
                ->first(),

            // Additional metrics
            'avg_time_per_visit' => $this->formatDuration($allVisitors->avg(function($visitor) {
                // Calculate total time spent across all pages for this visitor
                $totalTimeSpent = $visitor->pages->sum(function($page) {
                    return $page->page_visitor->total_duration_seconds; // Use duration in seconds instead of time_spent
                });

                // Calculate total visits across all pages
                $totalVisits = $visitor->pages->sum(function($page) {
                    return $page->page_visitor->total_visits;
                });

                // Return average time per visit in seconds
                return $totalVisits > 0 ? $totalTimeSpent / $totalVisits : 0;
            })),
            'returning_visitors' => (int)$allVisitors->filter(function($visitor) {
                return $visitor->pages->sum('page_visitor.total_visits') > 1;
            })->count(),
            'engagement_rate' => (int)$allVisitors->avg(function($visitor) {
                $totalTimeSpent = $visitor->pages->sum('page_visitor.total_time_spent');
                $totalVisits = $visitor->pages->sum('page_visitor.total_visits');
                return $totalVisits > 0 ? min(100, ($totalTimeSpent / $totalVisits) * 10) : 0;
            }),
            'peak_hour_today' => $allVisitors->flatMap->pages
                ->where('page_visitor.first_visit_at', '>=', Carbon::today())
                ->groupBy(function($page) {
                    return Carbon::parse($page->page_visitor->first_visit_at)->format('H');
                })
                ->map->count()
                ->sortDesc()
                ->keys()
                ->first(),
            // Most active days of week
            'most_active_day' => $allVisitors->flatMap->pages
                ->groupBy(function($page) {
                    return Carbon::parse($page->page_visitor->first_visit_at)->format('l');
                })
                ->map->count()
                ->sortDesc()
                ->keys()
                ->first(),

            // Average session depth (pages per session)
            'avg_session_depth' => round($allVisitors->avg(function($visitor) {
                return $visitor->pages->unique('id')->count();
            }), 1),

            // Exit rate - percentage of sessions that end on each page
            'exit_rate' => round($allVisitors->avg(function($visitor) {
                $lastPage = $visitor->pages->sortBy('page_visitor.last_visit_at')->last();
                return $lastPage ? 100 : 0;
            }), 1),

            // New vs returning visitor ratio
            'new_visitor_ratio' => round(($allVisitors->filter(function($visitor) {
                return $visitor->pages->sum('page_visitor.total_visits') === 1;
            })->count() / max(1, $allVisitors->count())) * 100, 1),

            // Average time between visits (for returning visitors)
            'avg_time_between_visits' => $this->formatDuration((int)$allVisitors
                ->filter(function($visitor) {
                    return $visitor->pages->sum('page_visitor.total_visits') > 1;
                })
                ->avg(function($visitor) {
                    $visits = $visitor->pages->pluck('page_visitor.first_visit_at')->sort();
                    if($visits->count() < 2) return 0;
                    return $visits->zip($visits->slice(1))
                        ->map(function($pair) {
                            return Carbon::parse($pair[0])->diffInSeconds(Carbon::parse($pair[1]));
                        })
                        ->avg();
                })),
            // Average engagement time per page
            'avg_engagement_time' => $this->formatDuration((int)$allVisitors->flatMap->pages
                ->avg('page_visitor.total_duration_seconds')),

            // Most visited page path
            'most_visited_path' => $allVisitors->flatMap->pages
                ->groupBy('path')
                ->map->count()
                ->sortDesc()
                ->keys()
                ->first(),

            // Average pages per session by hour of day
            'peak_pages_per_session_hour' => $allVisitors
                ->groupBy(function($visitor) {
                    return $visitor->pages->min('page_visitor.first_visit_at')->format('H');
                })
                ->map(function($visitors) {
                    return round($visitors->avg(function($visitor) {
                        return $visitor->pages->count();
                    }), 1);
                })
                ->sortDesc()
                ->keys()
                ->first(),

            // Percentage of visitors that view more than 5 pages
            'deep_engagement_rate' => round(($allVisitors->filter(function($visitor) {
                return $visitor->pages->count() > 5;
            })->count() / max(1, $allVisitors->count())) * 100, 1),

            // Average session duration by day of week
            'longest_session_day' => $allVisitors
                ->groupBy(function($visitor) {
                    return $visitor->pages->min('page_visitor.first_visit_at')->format('l');
                })
                ->map(function($visitors) {
                    return round($visitors->avg(function($visitor) {
                        $firstVisit = $visitor->pages->min('page_visitor.first_visit_at');
                        $lastVisit = $visitor->pages->max('page_visitor.last_visit_at');
                        return $lastVisit && $firstVisit ? Carbon::parse($firstVisit)->diffInSeconds(Carbon::parse($lastVisit)) : 0;
                    }));
                })
                ->sortDesc()
                ->keys()
                ->first(),
        ];

        return [$visitors, $globalStats];
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

    public function formatDuration(int $seconds): string
    {
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
