<?php

namespace Shaferllc\Analytics\Traits;

trait Aggregates
{
    public function groupVisitorCount($value, $key)
    {
        return $value->groupBy($key)
        ->map(function($group) {
            return [
                'count' => $group->count(),
                'visits' => $group->sum('total_visits')
            ];
        })->toArray();
    }

    public function cpu($value, $key)
    {
        return $value->groupBy($key)
        ->map(function($group) {
            return $group->sum('total_visits');
        })->toArray();
    }
}
