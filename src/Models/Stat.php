<?php

namespace ShaferLLC\Analytics\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Stat extends Model
{
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'date' => 'datetime:Y-m-d'
    ];

    /**
     * Scope a query to search for a value.
     *
     * @param Builder $query
     * @param string $value
     * @return Builder
     */
    public function scopeSearchValue(Builder $query, string $value): Builder
    {
        return $query->where('value', 'like', "%{$value}%");
    }
}
