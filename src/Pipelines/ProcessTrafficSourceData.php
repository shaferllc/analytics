<?php

namespace Shaferllc\Analytics\Pipelines;

use Illuminate\Support\Arr;
use Shaferllc\Analytics\Models\Event;
use Shaferllc\Analytics\Models\Meta;

class ProcessTrafficSourceData
{
    use PipelineTrait;
    public function handle($payload, \Closure $next)
    {
        $data = $payload['data'];
        $site = $payload['site'];
        $page = $payload['page'];
        $visitor = $payload['visitor'];
        $events = collect(Arr::get($data, 'events', []));

        $trafficSourceData = $events->where('name', 'traffic_source_data')->first();

        if ($trafficSourceData) {
            $parent_event = $this->createParentEvent($visitor, $site, $page, $trafficSourceData);
            $this->processEventValues($parent_event, $trafficSourceData['value']);
        }

        return $next($payload);
    }
}
