<?php

namespace Shaferllc\Analytics\Models;

use App\Models\Site;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EventLogs extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'site_id',
        'event_id',
        'visitor_id',
        'session_id',
        'page_id',
        'event_type',
        'event_data',
        'occurred_at',
        'processed_at',
        'status'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'event_data' => 'array',
        'occurred_at' => 'datetime',
        'processed_at' => 'datetime'
    ];

    /**
     * Get the site that owns the event log.
     */
    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    /**
     * Get the event that owns the log.
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the visitor that owns the event log.
     */
    public function visitor(): BelongsTo
    {
        return $this->belongsTo(PageVisitor::class, 'visitor_id');
    }

    /**
     * Get the page that owns the event log.
     */
    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function logEvent(string $message, $data = []): void
    {
        $currentData = $this->event_data ?? [];
        $currentData[] = [
            'timestamp' => now()->toIso8601String(),
            'message' => $message,
            'data' => $data
        ];

        $this->update([
            'event_data' => $currentData
        ]);

        if (app()->environment('local')) {
            ds($message);
        }
    }
}
