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
    protected $table = 'analytics_metas';

    protected $fillable = [
        'metaable_id',
        'metaable_type',
        'meta_key',
        'meta_value',
        'timestamp',
    ];

    protected $casts = [
        'timestamp' => 'datetime',
    ];

    public function metaable(): MorphTo
    {
        return $this->morphTo();
    }
}
