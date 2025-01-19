<?php

declare(strict_types=1);

namespace Shaferllc\Analytics\Models;

use App\Models\Site;
use Shaferllc\Analytics\Models\Meta;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\SchemalessAttributes\SchemalessAttributesTrait;
use Spatie\SchemalessAttributes\Casts\SchemalessAttributes;

class Event extends Model
{
    use HasUlids, SchemalessAttributesTrait;
    protected $table = 'analytics_events';

    protected $schemalessAttributes = [
        'value',
        'meta_data',
    ];

    protected $fillable = [
        'site_id',
        'visitor_id',
        'page_id',
        'name',
        'value',
        'timestamp',
        'meta_data',
    ];

    protected $casts = [
        'timestamp' => 'datetime',
        'value' => SchemalessAttributes::class,
        'meta_data' => SchemalessAttributes::class,
    ];

    public function scopeWithValue(): Builder
    {
        return $this->value->modelScope();
    }

    public function scopeWithMetaData(): Builder
    {
        return $this->meta_data->modelScope();
    }

    public function meta(): MorphMany
    {
        return $this->morphMany(Meta::class, 'metaable');
    }


    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function visitor(): BelongsTo
    {
        return $this->belongsTo(Visitor::class);
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }
}
