<?php

declare(strict_types=1);

namespace Shaferllc\Analytics\Models;

use App\Models\Site;

use Shaferllc\Analytics\Models\Event;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Spatie\SchemalessAttributes\Casts\SchemalessAttributes;

class Page extends Model
{
    use HasUlids;

    protected $table = 'analytics_pages';

    protected $fillable = [
        'site_id',
        'page',
        'path',
        'meta_data',
        // 'title',
        // 'query_string',
        // 'hash',
        // 'first_visit_at',
        // 'last_visit_at',
        // 'total_visits',
        // 'avg_time_on_page',
        // 'bounce_rate',
        // 'exit_rate'
    ];

    protected $casts = [
        'meta_data' => SchemalessAttributes::class,
        // 'first_visit_at' => 'datetime',
        // 'last_visit_at' => 'datetime',
        // 'total_visits' => 'integer',
        // 'avg_time_on_page' => 'float',
        // 'bounce_rate' => 'float',
        // 'exit_rate' => 'float'
    ];
    public function meta(): MorphMany
    {
        return $this->morphMany(Meta::class, 'metaable');
    }

    public function scopeWithMetaData(): Builder
    {
        return $this->meta_data->modelScope();
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function visitors(): BelongsToMany
    {
        return $this->belongsToMany(Visitor::class, 'analytics_page_visitors')
            ->as('page_visitor')
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

    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'analytics_eventables');
    }

    public function pageVisitors(): HasManyThrough
    {
        return $this->hasManyThrough(PageVisitor::class, Visitor::class);
    }
}
