<?php

namespace Shaferllc\Analytics\Pipelines;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;

class CreatePage
{
    public function handle($payload, \Closure $next)
    {
        $data = Arr::get($payload, 'data');
        $site = Arr::get($payload, 'site');

        $events = collect(Arr::get($data, 'events', []));

        $pageData = $events->where('name', 'page_data')->first();

        // Move all updates into the transaction to prevent race conditions
        $page = DB::transaction(function() use ($site, $data, $pageData, $events) {
            $updateData = [
                'page' => Arr::get($data, 'page'),
                'charset' => Arr::get($pageData, 'value.charset'),
                'keywords' => Arr::get($pageData, 'value.page_keywords'),
                'description' => Arr::get($pageData, 'value.page_description'),
                'canonical_url' => Arr::get($pageData, 'value.canonical_url'),
                'redirect_count' => Arr::get($pageData, 'value.redirect_count'),
                'robots_meta' => Arr::get($pageData, 'value.robots_meta'),
                'hreflang_tags' => Arr::get($pageData, 'value.hreflang_tags'),
                'last_modified' => Arr::get($pageData, 'value.last_modified'),
                'og_metadata' => is_array(Arr::get($pageData, 'value.og_metadata')) ? json_encode(Arr::get($pageData, 'value.og_metadata')) : Arr::get($pageData, 'value.og_metadata'),
                'twitter_metadata' => is_array(Arr::get($pageData, 'value.twitter_metadata')) ? json_encode(Arr::get($pageData, 'value.twitter_metadata')) : Arr::get($pageData, 'value.twitter_metadata'),
                'structured_data' => is_array(Arr::get($pageData, 'value.structured_data')) ? json_encode(Arr::get($pageData, 'value.structured_data')) : Arr::get($pageData, 'value.structured_data'),
            ];

            // First try to find existing page
            $page = $site->pages()
                ->where('path', Arr::get($data, 'path'))
                ->lockForUpdate()  // Add pessimistic lock
                ->first();

            if ($page) {
                $page->forceFill($updateData)->save();
            } else {
                $page = $site->pages()->create(array_merge([
                    'path' => Arr::get($data, 'path'),
                    'site_id' => $site->id
                ], $updateData));
            }

            // Handle title and counter updates
            if (Arr::get($pageData, 'value.page_title')) {
                $titles = collect($page->title)
                    ->push(Arr::get($pageData, 'value.page_title'))
                    ->unique()
                    ->values();
                $page->title = $titles;
            }

            $sessionStart = collect($events)->where('name', 'start_session')->first();

            // Update counters directly since we're in a transaction
            $page->visit_count = $sessionStart ? ($page->visit_count ?? 0) + 1 : 1;
            $page->page_view_count = ($page->page_view_count ?? 0) + 1;

            $page->save();

            return $page;
        }, 5);


        return $next(array_merge($payload, ['page' => $page]));
    }
}
