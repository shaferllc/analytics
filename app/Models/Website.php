<?php

namespace ShaferLLC\Analytics\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use ShaferLLC\Analytics\Models\User;
use ShaferLLC\Analytics\Models\Stat;
use ShaferLLC\Analytics\Models\Recent;

/**
 * Class Website
 *
 * @mixin Builder
 * @package ShaferLLC\Analytics\Models
 */
class Website extends Model
{
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'password' => 'encrypted',
    ];

    /**
     * Scope a query to search for a domain.
     *
     * @param Builder $query
     * @param string $value
     * @return Builder
     */
    public function scopeSearchDomain(Builder $query, string $value): Builder
    {
        return $query->where('domain', 'like', "%{$value}%");
    }

    /**
     * Get the user that owns the website.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    /**
     * Scope a query to filter by user.
     *
     * @param Builder $query
     * @param int $userId
     * @return Builder
     */
    public function scopeOfUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Get the visitors count for a specific date range.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function visitors()
    {
        return $this->stats()->where('name', 'visitors');
    }

    /**
     * Get the pageviews count for a specific date range.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pageviews()
    {
        return $this->stats()->where('name', 'pageviews');
    }

    /**
     * Get the website's stats.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function stats()
    {
        return $this->hasMany(Stat::class);
    }

    /**
     * Get the website's recent stats.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function recents()
    {
        return $this->hasMany(Recent::class);
    }
}
