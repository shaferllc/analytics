<?php

namespace Shaferllc\Analytics\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Sessions extends Model
{
    protected $table = 'analytics_sessions';

    protected $fillable = [
        'session_id',
        'request_id',
        'visitor_id',
        'page_id',
        'start_session_at',
        'end_session_at',
        'total_duration_seconds',
        'total_duration',
        'site_id',
    ];

    protected $casts = [
        'start_session_at' => 'datetime',
        'end_session_at' => 'datetime',
        'total_duration' => 'integer',
        'total_duration_seconds' => 'integer',
    ];

    public function visitor(): BelongsTo
    {
        return $this->belongsTo(Visitor::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }
}
