<?php

declare(strict_types=1);

namespace Shaferllc\Analytics\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Spatie\SchemalessAttributes\Casts\SchemalessAttributes;

class Meta extends Model
{
    use HasUlids;

    protected $table = 'analytics_meta';

    protected $fillable = [
        'itemable_id',
        'itemable_type',
        'metaable_id',
        'metaable_type',
        'meta_data',
        'meta_type',
        'parent_id',
        'parent_type',
        'visit_count',
    ];

    protected $casts = [
        'meta_data' => SchemalessAttributes::class,
        'visit_count' => 'integer',
    ];

    public function scopeMetaDataAttributes(): Builder
    {
        return $this->meta_data->modelScope();
    }
    public function metaable(): MorphTo
    {
        return $this->morphTo();
    }

    public function parentMeta(): MorphTo
    {
        return $this->morphTo();
    }
}
