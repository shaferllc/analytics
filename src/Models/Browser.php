<?php

declare(strict_types=1);

namespace Shaferllc\Analytics\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class Browser extends Model
{
    use HasUlids;

    protected $table = 'analytics_browsers';

    protected $fillable = [
        'user_agent',
        'name',
        'site_id',
        'operating_system',
        'device_pixel_ratio',
        'cpu_cores',
        'device_memory',
        'count',
        'resolution',
        'color_scheme',
        'reduced_motion',
        'language',
        'timezone',
        'viewport_width',
        'viewport_height',
        'version',
        'engine',
        'engine_version',
        'device_type',
        'device_brand',
        'device_model',
        'os_name',
        'os_version',
    ];

    protected $casts = [
        'viewport_width' => 'integer',
        'viewport_height' => 'integer',
        'reduced_motion' => 'boolean'
    ];

    public function visitors(): BelongsTo
    {
        return $this->belongsTo(Visitor::class, 'visitor_id');
    }
}
