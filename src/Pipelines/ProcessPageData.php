<?php

namespace Shaferllc\Analytics\Pipelines;

use Illuminate\Support\Arr;
use Shaferllc\Analytics\Models\Event;
use Shaferllc\Analytics\Models\Meta;

class ProcessPageData
{
    use PipelineTrait;
    public function handle($payload, \Closure $next)
    {
        $data = $payload['data'];
        $site = $payload['site'];
        $page = $payload['page'];
        $visitor = $payload['visitor'];
        $events = collect(Arr::get($data, 'events', []));

        $pageData = $events->where('name', 'page_data')->first();

        if ($pageData) {
            $parent_event = $this->createParentEvent($visitor, $site, $page, $pageData);
            $this->processEventValues($parent_event, $pageData['value']);
        }

        return $next($payload);
    }
}
