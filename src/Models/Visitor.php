<?php

declare(strict_types=1);

namespace Shaferllc\Analytics\Models;

use App\Models\Site;
use Illuminate\Support\Collection;
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

class Visitor extends Model
{
    use HasUlids;
    protected $table = 'analytics_visitors';


    protected $fillable = [
        'site_id',
        'session_id',
        'timezone',
        'user_id',
        'language',
    ];

    protected $casts = [
        'start_session_at' => 'datetime',
        'meta_data' => SchemalessAttributes::class,
    ];

    public function meta(): MorphMany
    {
        return $this->morphMany(Meta::class, 'metaable');
    }

    public function scopeWithMetaData(): Builder
    {
        return $this->meta_data->modelScope();
    }

    public function sites(): BelongsToMany
    {
        return $this->belongsToMany(Site::class);
    }

    public function pages(): BelongsToMany
    {
        return $this->belongsToMany(Page::class, 'analytics_page_visitors')
            ->using(PageVisitor::class)
            ->as('page_visitor')
            ->withPivot([
                'id',
                'site_id',
                'page_id',
                'visitor_id',
                'start_session_at',
                'end_session_at',
                'total_duration_seconds',
                'total_time_spent',
                'total_visits',
                'last_visit_at',
                'first_visit_at',
                'total_time_spent',
                'total_visits',
            ])->withTimestamps();
    }

    public function pageVisitors(): BelongsToMany
    {
        return $this->belongsToMany(Page::class, 'analytics_page_visitors')
            ->using(PageVisitor::class)
            ->withPivot([
                'id',
                'site_id',
                'last_visit_at',
                'first_visit_at',
                'total_time_spent',
                'total_visits',
                'start_session_at',
                'end_session_at',
                'total_duration_seconds',
                'meta_data',
                'meta_type'
            ])->withTimestamps();
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

}
