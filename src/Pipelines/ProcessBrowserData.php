<?php

namespace Shaferllc\Analytics\Pipelines;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Shaferllc\Analytics\Models\Meta;
use Shaferllc\Analytics\Models\Event;
use Illuminate\Support\Facades\DB;

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

        // If the browser data event exists, process the data
        if(Arr::has($browserData, 'value')) {

            $data = array_filter(Arr::get($browserData, 'value'));

            DB::transaction(function () use ($visitor, $data, $site) {
                $visitor->browser()->updateOrCreate([
                    'user_agent' => Arr::get($data, 'userAgent'),
                    'site_id' => $site->id,
                ], [
                    'name' => Arr::get($data, 'browser_name'),
                    'version' => Arr::get($data, 'browser_version'),
                    'engine' => Arr::get($data, 'browser_version'),
                    'engine_version' => Arr::get($data, 'engine_version'),
                    'color_scheme' => Arr::get($data, 'color_scheme'),
                    'reduced_motion' => Arr::get($data, 'reduced_motion'),
                    'language' => Arr::get($data, 'language'),
                    'timezone' => Arr::get($data, 'timezone'),
                    'viewport_width' => Arr::get($data, 'viewport_width'),
                    'viewport_height' => Arr::get($data, 'viewport_height'),
                    'resolution' => Arr::get($data, 'resolution'),
                    'cpu_cores' => Arr::get($data, 'cpu_cores'),
                    'device_brand' => Arr::get($data, 'device_brand'),
                    'os_name' => Arr::get($data, 'os_name'),
                    'os_version' => Arr::get($data, 'os_version'),
                    'device_model' => Arr::get($data, 'device_model'),
                    'device_memory' => Arr::get($data, 'device_memory'),
                    'device_pixel_ratio' => Arr::get($data, 'device_pixel_ratio'),
                    'device_type' => Arr::get($data, 'device_type'),
                    'operating_system' => Arr::get($data, 'operating_system'),
                    'count' => $visitor->browser()->count() + 1,
                ]);

            }, 3); // Retry up to 3 times in case of deadlock

        }

        return $next($payload);
    }

}
