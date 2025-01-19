<?php

namespace Shaferllc\Analytics\Pipelines;

use Illuminate\Support\Arr;
use Shaferllc\Analytics\Models\Event;
use Shaferllc\Analytics\Models\Meta;

class ProcessBrowserData
{
    use PipelineTrait;

    public function handle($payload, \Closure $next)
    {
        $data = $payload['data'];
        $site = $payload['site'];
        $page = $payload['page'];
        $visitor = $payload['visitor'];
        $events = collect(Arr::get($data, 'events', []));

        $browserData = $events->where('name', 'browser_data')->first();

        if ($browserData) {
            $parent_event = $this->createParentEvent($visitor, $site, $page, $browserData);
            $this->processEventValues($parent_event, $browserData['value']);
        }

        return $next($payload);
    }

}
