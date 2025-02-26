<?php

declare(strict_types=1);

namespace Shaferllc\Analytics\Pipelines;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;
use Shaferllc\Analytics\Models\PageVisitor;

class HandleSessionEnd
{
    public function handle(array $payload, Closure $next): mixed
    {
        $endSession = $this->findEndSessionEvent($payload);

        if ($endSession) {
            // try {
                $this->updateVisitorPage($payload['visitorPage'], $endSession, $next, $payload);
            // } catch (Throwable $e) {
            //     Log::error('Failed to update visitor page on session end', [
            //         'error' => $e->getMessage(),
            //         'visitor_page_id' => $payload['visitorPage']->id ?? null,
            //     ]);
            // }
        }

        return $next($payload);
    }

    private function findEndSessionEvent(array $payload): ?array
    {
        $events = Arr::get($payload['data'], 'events', []);
        return collect($events)->firstWhere('name', 'end_session');
    }

    private function updateVisitorPage($visitorPage, array $endSession, Closure $next, array $payload)
    {
        // if (!$visitorPage || !$visitorPage->exists) {
        //     Log::warning('Skipping visitor page update - invalid visitor page provided', [
        //         'visitor_page_id' => $visitorPage->id ?? null,
        //     ]);
        //     return $next($payload);
        // }

        DB::transaction(function() use ($visitorPage, $endSession) {
            $endTime = Arr::get($endSession, 'value.end_time') ?? now();

            // Lock the visitor page record for update to prevent race conditions
            $lockedVisitorPage = PageVisitor::query()
                ->where('id', $visitorPage->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $lockedVisitorPage->update([
                'exit_page' => Arr::get($endSession, 'value.exit_page'),
                'end_time' => $endTime,
                'total_duration' => Carbon::parse($endTime)->diffInSeconds($lockedVisitorPage->start_time),
                'engagement_metrics' => Arr::get($endSession, 'value.engagement_metrics'),
                'engagement_score' => Arr::get($endSession, 'value.engagement_score'),
                'engagement_count' => $lockedVisitorPage->engagement_count + 1,
                'last_engagement_time' => $endTime,
                'engagement_history' => array_merge(
                    $lockedVisitorPage->engagement_history ?? [],
                    [[
                        'timestamp' => $endTime,
                        'metrics' => Arr::get($endSession, 'value.engagement_metrics'),
                        'score' => (float)Arr::get($endSession, 'value.engagement_score')
                    ]]
                ),
            ]);

            ds($lockedVisitorPage);
        });
    }
}
