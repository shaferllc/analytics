<?php

declare(strict_types=1);

namespace Shaferllc\Analytics\Models;

use App\Models\Site;

use Shaferllc\Analytics\Models\Event;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Page extends Model
{
    use HasUlids;

    protected $table = 'analytics_pages';

    protected $fillable = [
        'site_id',
        'page',
        'path',
        'meta_data',
        'title',
        'charset',
        'visit_count',
        'page_view_count',
        'keywords',
        'description',
        'canonical_url',
        'redirect_count',
        'referrer',
        'robots_meta',
        'hreflang_tags',
        'og_metadata',
        'twitter_metadata',
        'structured_data',
        'last_modified',
        'total_visits',
        'meta_title',
        'meta_description',
        'og_title',
        'og_description',
        'og_image',
        'twitter_title',
        'twitter_description',
        'twitter_image',
    ];

    protected $casts = [
        'title' => 'array',
        'og_metadata' => 'array',
        'hreflang_tags' => 'array',
        'twitter_metadata' => 'array',
        'structured_data' => 'array',
        'last_modified' => 'datetime',
        'total_visits' => 'integer',
        'meta_title' => 'array',
        'meta_description' => 'array',
        'og_title' => 'array',
        'og_description' => 'array',
        'twitter_title' => 'array',
        'twitter_description' => 'array',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function visitors(): BelongsToMany
    {
        return $this->belongsToMany(Visitor::class, 'analytics_page_visitors')
            ->using(PageVisitor::class)
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
            ])
            ->withTimestamps();
    }

    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'analytics_eventables');
    }

    public function pageVisitors(): HasManyThrough
    {
        return $this->hasManyThrough(PageVisitor::class, Visitor::class);
    }

    // Helper method to get total visits across all visitors
    public function getTotalVisits(): int
    {
        return $this->visitors()
            ->wherePivot('is_base_record', true)
            ->sum('total_visits');
    }

    // Helper method to increment total visits
    public function incrementTotalVisits(): void
    {
        $this->increment('total_visits');
    }

    // Helper method to get unique visitors count
    public function getUniqueVisitorsCount(): int
    {
        return $this->visitors()
            ->wherePivot('is_base_record', true)
            ->count();
    }
}
