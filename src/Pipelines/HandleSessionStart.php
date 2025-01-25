<?php

namespace Shaferllc\Analytics\Pipelines;

use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class HandleSessionStart
{
    public function handle($payload, \Closure $next)
    {
        $data = $payload['data'];
        $page = $payload['page'];
        $visitor = $payload['visitor'];
        $events = Arr::get($data, 'events', []);
        $startSession = collect($events)->where('name', 'start_session')->first();

        $page->visitors()->updateExistingPivot($visitor->id, [
            'last_visit_at' => now(),
            'total_visits' => ($page->visitors()
                ->where('session_id', Arr::get($data, 'session_id'))
                ->first()?->page_visitor?->total_visits ?? 0) + 1,
        ]);

        $visitor->sessions()->create([
            'page_id' => $page->id,
            'site_id' => $page->site_id,
            'session_id' => Arr::get($data, 'session_id'),
            'request_id' => Arr::get($data, 'request_id'),
            'start_session_at' => Carbon::parse(Arr::get($startSession, 'value.start_time'))
        ]);

        return $next($payload);
    }
}
