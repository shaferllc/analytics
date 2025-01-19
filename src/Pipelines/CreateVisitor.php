<?php

namespace Shaferllc\Analytics\Pipelines;

use Illuminate\Support\Arr;

class CreateVisitor
{
    public function handle($payload, \Closure $next)
    {
        $data = Arr::get($payload, 'data');
        $page = Arr::get($payload, 'page');
        $events = Arr::get($data, 'events', []);
        ray($events);
        $startSession = collect($events)->where('name', 'start_session')->first();

        // if (!$startSession) {
        //     return response()->json(['message' => 'Start session event not found'], 400);
        // }

        $visitor = $page->visitors()->firstOrCreate(
            [
                'session_id' => Arr::get($data, 'session_id'),
            ],
            [
                'timezone' => Arr::get($startSession, 'value.time_zone'),
                'language' => Arr::get($startSession, 'value.language'),
                'user_id' => Arr::get($startSession, 'value.user_id'),
            ]
        );

        $geoFields = ['city', 'country', 'continent'];
        foreach ($geoFields as $field) {
            $values = collect($visitor->meta_data->get($field, []))
                ->push(Arr::get($startSession, 'value.' . $field))
                ->unique()
                ->filter()
                ->values();
            $visitor->meta_data->set($field, $values);
        }
        $visitor->save();

        return $next(array_merge($payload, ['visitor' => $visitor]));
    }
}
