<?php

namespace Shaferllc\Analytics\Pipelines;

use Illuminate\Support\Arr;
use Shaferllc\Analytics\Models\Event;
use Shaferllc\Analytics\Models\Meta;

class ProcessDeviceData
{
    use PipelineTrait;

    public function handle($payload, \Closure $next)
    {
        $data = $payload['data'];
        $site = $payload['site'];
        $page = $payload['page'];
        $visitor = $payload['visitor'];
        $events = collect(Arr::get($data, 'events', []));

        $deviceData = $events->where('name', 'device_data')->first();

        if ($deviceData) {
            $parent_event = $this->createParentEvent($visitor, $site, $page, $deviceData);
            $this->processEventValues($parent_event, $deviceData['value']);
        }

        return $next($payload);
    }

}
