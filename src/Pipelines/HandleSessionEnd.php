<?php

namespace Shaferllc\Analytics\Pipelines;

use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Shaferllc\Analytics\Models\PageVisitor;

class HandleSessionEnd
{
    public function handle($payload, \Closure $next)
    {
        $data = $payload['data'];
        $page = $payload['page'];
        $visitor = $payload['visitor'];
        $events = Arr::get($data, 'events', []);
        $endSession = collect($events)->where('name', 'end_session')->first();

        if ($endSession) {
            $this->handleSessionEnd($page, $visitor, $data, $endSession);
            $this->handleExitPageMeta($visitor, $endSession);
        }

        return $next($payload);
    }

    private function handleSessionEnd($page, $visitor, $data, $endSession)
    {

        $session = $visitor->sessions()->where('request_id', Arr::get($data, 'request_id'))->orWhere('session_id', Arr::get($data, 'session_id'))->latest()->first();

        $session->update([
            'end_session_at' => Carbon::parse(Arr::get($endSession, 'value.end_time')),
            'total_duration_seconds' => Arr::get($endSession, 'value.total_duration_seconds', 0),
            'total_duration' => Arr::get($endSession, 'value.total_duration', 0),
        ]);
        $session->save();

    }

    private function handleExitPageMeta($visitor, $endSession)
    {
        try {
            $pageVisitor = $visitor->pageVisitors()->first();

            if (!$pageVisitor) {
                Log::error('No page visitor found for visitor ID: ' . $visitor->id);
                return;
            }

            $meta = $pageVisitor->meta()->firstOrCreate([
                'itemable_id' => $pageVisitor->id,
                'itemable_type' => PageVisitor::class,
                'meta_type' => 'session_end',
            ]);

            $exit_url = Arr::get($endSession, 'value.exit_page');
            $exit_pages = collect($meta->meta_data->get('exit_page', []));

            $this->updateExitPages($exit_pages, $exit_url);

            $meta->meta_data
                ->set('exit_page', $exit_pages->values())
                ->set('exit_page_count', [$exit_pages->count()]);
            $meta->save();
        } catch (\Exception $e) {
            Log::error('Error handling session end meta: ' . $e->getMessage());
        }

    }

    private function updateExitPages($exit_pages, $exit_url)
    {
        if ($page = $exit_pages->firstWhere('url', $exit_url)) {
            $exit_pages->transform(function ($p) use ($exit_url) {
                return $p['url'] === $exit_url
                    ? ['url' => $p['url'], 'count' => $p['count'] + 1]
                    : $p;
            });
        } else {
            $exit_pages->push(['url' => $exit_url, 'count' => 1]);
        }
    }
}
