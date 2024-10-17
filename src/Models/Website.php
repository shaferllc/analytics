<?php

namespace ShaferLLC\Analytics\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use ShaferLLC\Analytics\Models\User;
use ShaferLLC\Analytics\Models\Stat;
use ShaferLLC\Analytics\Models\Recent;
use App\Models\Website as AppWebsite;
/**
 * Class Website
 *
 * @mixin Builder
 * @package ShaferLLC\Analytics\Models
 */
class Website extends AppWebsite
{
 

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
