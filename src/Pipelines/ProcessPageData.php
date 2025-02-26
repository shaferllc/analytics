<?php

namespace Shaferllc\Analytics\Pipelines;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Shaferllc\Analytics\Models\Meta;
use Shaferllc\Analytics\Models\Event;

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

        ds($pageData);
        // If the browser data event exists, process the data
        if(Arr::has($pageData, 'value')) {
            DB::transaction(function() use ($pageData, $page) {
                $data = array_filter(Arr::get($pageData, 'value'));

                foreach ($data as $key => $value) {
                    // Skip if the value is an array, we should never have an array here
                    if(is_array($value)) {
                        $value = json_encode($value);
                    }

                    $page->meta()->lockForUpdate()->firstOrCreate([
                        'meta_key' => Str::slug($key),
                        'meta_value' => $value,
                        'timestamp' => $pageData['timestamp'],
                    ]);
                }
            }, 5); // Retry up to 5 times if there's a deadlock
        }

        return $next($payload);
    }
}
