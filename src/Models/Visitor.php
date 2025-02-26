<?php

declare(strict_types=1);

namespace Shaferllc\Analytics\Models;

use App\Models\Site;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Shaferllc\Analytics\Models\Browser;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Visitor extends Model
{
    use HasUlids;
    protected $table = 'analytics_visitors';


    protected $fillable = [
        'site_id',
        'session_id',
        'timezone',
        'user_id',
        'ip_address',
        'user_agent',
        'referer',
        'country',
        'city',
        'region',
        'language',
        'continent',
        'latitude',
        'longitude',
        'last_visit_at',
        'first_visit_at',
        'total_visits',
        'iso_time',
        'locale_string',
        'utc_string',
        'start_time',
        'unix_timestamp',
        'time_zone',
        'session_duration',
    ];

    protected $casts = [
        'last_visit_at' => 'datetime',
        'first_visit_at' => 'datetime',
        'total_visits' => 'int',
        'iso_time' => 'datetime',
        'locale_string' => 'datetime',
        'utc_string' => 'datetime',
        'start_time' => 'datetime',
        'unix_timestamp' => 'datetime',
    ];

    public function meta(): MorphMany
    {
        return $this->morphMany(Meta::class, 'metaable');
    }


    public function sites(): BelongsToMany
    {
        return $this->belongsToMany(Site::class, 'analytics_site_visitor');
    }

    public function pages(): BelongsToMany
    {
        return $this->belongsToMany(Page::class, 'analytics_page_visitors')
            ->using(PageVisitor::class)
            ->as('page_visitor')
            ->withPivot([
                'referrer',
                'hash',
                'query',
                'campaign',
                'landing_page',
                'search_engine',
                'social_network',
                'start_time',
                'performance_metrics',
                'navigation_type',
                'session_duration',
                'page_depth',
                'url_query',
                'load_time',
                'first_visit_at',
                'last_visit_at',
                'total_visits',
                'visit_key',
                'is_base_record'
            ])->withTimestamps();
    }

    public function pageVisitors(): BelongsToMany
    {
        return $this->belongsToMany(Page::class, 'analytics_page_visitors')
            ->using(PageVisitor::class)
            ->withPivot([
                'id',
                'page_id',
                'visitor_id',
                'last_visit_at',
                'first_visit_at',
                'total_visits',
            ]);
    }


    public function browser(): HasOne
    {
        return $this->hasOne(Browser::class);
    }


    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'analytics_event_visitors');
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    // Helper method to get total visits across all pages
    public function getTotalPageVisits(): int
    {
        return $this->pages()
            ->wherePivot('is_base_record', true)
            ->sum('total_visits');
    }

    // Helper method to increment total visits
    public function incrementTotalVisits(): void
    {
        $this->increment('total_visits');
    }
}
