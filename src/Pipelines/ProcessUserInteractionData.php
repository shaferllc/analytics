<?php

namespace Shaferllc\Analytics\Pipelines;

use Illuminate\Support\Arr;
use Shaferllc\Analytics\Models\Event;
use Shaferllc\Analytics\Models\Meta;

class ProcessUserInteractionData
{
    use PipelineTrait;

    public function handle($payload, \Closure $next)
    {
        $data = $payload['data'];
        $site = $payload['site'];
        $page = $payload['page'];
        $visitor = $payload['visitor'];
        $events = collect(Arr::get($data, 'events', []));

        $userInteractionData = $events->where('name', 'user_interaction_data')->first();

        if ($userInteractionData) {
            $parent_event = $this->createParentEvent($visitor, $site, $page, $userInteractionData);
            $this->processEventValues($parent_event, $userInteractionData['value']);
        }

        return $next($payload);
    }

}
