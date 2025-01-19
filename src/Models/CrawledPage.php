<?php

namespace Shaferllc\Analytics\Models;

use App\Models\Site;
use Streamline\Jobs\ScreenShot;
use Shaferllc\Analytics\Models\Stat;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\SchemalessAttributes\Casts\SchemalessAttributes;

class CrawledPage extends Model
{

    use HasUlids;

    public $fillable = [
        'site_id',
        'found_on',
        'messages',
        'response',
        'exception',
        'headers',
        'content_type',
        'crawled_at',
        'failed_at',
        'path',
        'screenshots'
    ];
    protected $casts = [
        'found_on' => 'array',
        'messages' => 'array',
        'headers' => 'array',
        'crawled_at' => 'datetime',
        'failed_at' => 'datetime',
        'screenshots' => SchemalessAttributes::class
    ];

    protected static function booted()
    {
        static::created(function ($page) {
            // Screenshot::dispatch($page);
        });

        static::updated(function ($page) {
            // Screenshot::dispatch($page);
        });
    }

    public function scopeWithScreenshots(): Builder
    {
        return $this->screenshots->modelScope();
    }


    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }



    public function latestReport(): HasOne
    {
        return $this->hasOne(Report::class)->latestOfMany();
    }

    public function getScreenshotAttribute($value)
    {
        return storage_path('crawled-page-screenshots/'.$value);
    }

    public function getUrlAttribute()
    {
        return $this->site->domain . $this->path;
    }
}
