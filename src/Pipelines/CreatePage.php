<?php

namespace Shaferllc\Analytics\Pipelines;

use Illuminate\Support\Arr;

class CreatePage
{
    public function handle($payload, \Closure $next)
    {
        $data = Arr::get($payload, 'data');
        $site = Arr::get($payload, 'site');

        $page = $site->pages()->firstOrCreate([
            'path' => Arr::get($data, 'path'),
            'page' => Arr::get($data, 'page'),
        ]);

        return $next(array_merge($payload, ['page' => $page]));
    }
}
