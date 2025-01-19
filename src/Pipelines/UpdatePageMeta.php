<?php

namespace Shaferllc\Analytics\Pipelines;

use Illuminate\Support\Arr;

class UpdatePageMeta
{
    public function handle($payload, \Closure $next)
    {
        $data = Arr::get($payload, 'data');
        $page = Arr::get($payload, 'page');
        $events = Arr::get($data, 'events', []);
        $sessionStart = collect($events)->where('name', 'start_session')->first();
        // Update page meta
        $titles = collect($page->meta_data->get('title', []))
            ->push(Arr::get($data, 'title'))
            ->unique()
            ->values();
        $page->meta_data->set('title', $titles);

        // Update charset
        $charset = collect($page->meta_data->get('charset', []))
            ->push(Arr::get($data, 'charset'))
            ->unique()
            ->values();
        $page->meta_data->set('charset', $charset);

        // Update visit count
        if ($sessionStart) {
            $visitCount = $page->meta_data->get('visit_count', 0);
            $page->meta_data->set('visit_count', $visitCount + 1);
        }
        $page->save();


        return $next($payload);
    }
}
