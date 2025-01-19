<?php

declare(strict_types=1);

namespace Shaferllc\Analytics\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Spatie\SchemalessAttributes\Casts\SchemalessAttributes;

class PageVisitor extends Pivot
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'analytics_page_visitors';

    protected $withTimestamps = true;

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'visitor_id',
        'page_id',
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
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'last_visit_at' => 'datetime',
        'first_visit_at' => 'datetime',
        'total_time_spent' => 'int',
        'total_visits' => 'int',
        'start_session_at' => 'datetime',
        'end_session_at' => 'datetime',
        'total_duration_seconds' => 'int',
        'meta_data' => SchemalessAttributes::class,
    ];

    public function scopeWithMetaData(): Builder
    {
        return $this->meta_data->modelScope();
    }

    public function meta(): MorphMany
    {
        return $this->morphMany(Meta::class, 'metaable', 'metaable_id', 'metaable_type', 'id');
    }
}
