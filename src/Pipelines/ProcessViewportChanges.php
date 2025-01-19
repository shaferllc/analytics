<?php

namespace Shaferllc\Analytics\Pipelines;

use Illuminate\Support\Arr;
use Shaferllc\Analytics\Models\Event;
use Shaferllc\Analytics\Models\Meta;

class ProcessViewportChanges
{
    use PipelineTrait;
    public function handle($payload, \Closure $next)
    {
        $data = $payload['data'];
        $site = $payload['site'];
        $page = $payload['page'];
        $visitor = $payload['visitor'];
        $events = collect(Arr::get($data, 'events', []));

        $viewportChanges = $events->where('name', 'viewport_change')->all();

        foreach ($viewportChanges as $event) {
            $parent_event = $this->createParentEvent($visitor, $site, $page, $event);
            $this->processEventValues($parent_event, $event['value']);
        }

        return $next($payload);
    }
}
