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
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\SchemalessAttributes\SchemalessAttributesTrait;
use Spatie\SchemalessAttributes\Casts\SchemalessAttributes;

class Event extends Model
{
    use HasUlids, SchemalessAttributesTrait;
    protected $table = 'analytics_events';

    protected $fillable = [
        'hash',
        'name',
        'visit_count',
    ];

    protected $casts = [
        'timestamp' => 'datetime',
        'visit_count' => 'integer',
    ];
    public function meta(): MorphMany
    {
        return $this->morphMany(Meta::class, 'metaable');
    }


    public function site(): BelongsToMany
    {
        return $this->belongsToMany(Site::class);
    }


    public function visitors(): BelongsToMany
    {
        return $this->belongsToMany(Visitor::class, 'analytics_event_visitors');
    }

    public function page(): BelongsToMany
    {
        return $this->belongsToMany(Page::class);
    }
}
