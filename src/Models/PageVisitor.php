<?php

declare(strict_types=1);

namespace Shaferllc\Analytics\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Spatie\SchemalessAttributes\Casts\SchemalessAttributes;

class PageVisitor extends Pivot
{
    protected $table = 'analytics_page_visitors';

    public $incrementing = true;

    protected $fillable = [
        'visitor_id',
        'page_id',
        'referrer',
        'hash',
        'query',
        'campaign',
        'landing_page',
        'search_engine',
        'social_network',
        'start_time',
        'end_time',
        'total_duration',
        'exit_page',
        'performance_metrics',
        'navigation_type',
        'session_duration',
        'engagement_metrics',
        'page_depth',
        'url_query',
        'load_time',
        'engagement_score',
        'engagement_count',
        'last_engagement_time',
        'engagement_history',
        'last_visit_at',
        'first_visit_at',
        'is_base_record',
        'visit_key',
        'total_visits',
    ];

    protected $casts = [
        'hash' => 'string',
        'query' => 'string',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'total_duration' => 'integer',
        'performance_metrics' => 'array',
        'engagement_metrics' => 'array',
        'page_depth' => 'array',
        'navigation_type' => 'array',
        'engagement_score' => 'integer',
        'engagement_count' => 'integer',
        'last_engagement_time' => 'datetime',
        'engagement_history' => 'array',
        'last_visit_at' => 'datetime',
        'first_visit_at' => 'datetime',
        'total_visits' => 'integer',
    ];

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function visitor(): BelongsTo
    {
        return $this->belongsTo(Visitor::class);
    }

    public function meta(): MorphMany
    {
        return $this->morphMany(Meta::class, 'metaable');
    }

    public function incrementTotalVisits(): void
    {
        $this->increment('total_visits');
    }

    public function getAllVisits(): int
    {
        if ($this->is_base_record) {
            return $this->page->visitors()
                ->where('visitor_id', $this->visitor_id)
                ->sum('total_visits');
        }
        return $this->total_visits;
    }
}
