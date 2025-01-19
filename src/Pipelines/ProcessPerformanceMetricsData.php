<?php

namespace Shaferllc\Analytics\Pipelines;

use Illuminate\Support\Arr;
use Shaferllc\Analytics\Models\Event;
use Shaferllc\Analytics\Models\Meta;

class ProcessPerformanceMetricsData
{
    use PipelineTrait;

    public function handle($payload, \Closure $next)
    {
        $data = $payload['data'];
        $site = $payload['site'];
        $page = $payload['page'];
        $visitor = $payload['visitor'];
        $events = collect(Arr::get($data, 'events', []));

        $performanceMetricsData = $events->where('name', 'performance_metrics')->first();

        if ($performanceMetricsData) {
            $parent_event = $this->createParentEvent($visitor, $site, $page, $performanceMetricsData);
            $this->processEventValues($parent_event, $performanceMetricsData['value']);
        }

        return $next($payload);
    }

}
