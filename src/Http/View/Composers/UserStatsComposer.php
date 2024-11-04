<?php

namespace Shaferllc\Analytics\Http\View\Composers;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Shaferllc\Analytics\Models\Stat;

class UserStatsComposer
{
    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        if (!Auth::check()) {
            return;
        }

        $team = Auth::user()->currentTeam();
        $now = Carbon::now();

        $pageviewsCount = $this->getPageviewsCount($team, $now);

        $view->with('pageviewsCount', $pageviewsCount);
    }

    /**
     * Get the pageviews count for the user.
     *
     * @param  \App\Models\User  $user
     * @param  Carbon  $now
     * @return int
     */
    private function getPageviewsCount($team, Carbon $now)
    {
        return Cache::remember("user_{$team->id}_pageviews_count", 60, function () use ($team, $now) {
            return Stat::where('name', 'pageviews')
                ->whereIn('website_id', function ($query) use ($team) {
                    $query->select('id')
                          ->from('websites')
                          ->where('team_id', $team->id);
                })
                ->whereBetween('date', [$now->startOfMonth(), $now->endOfMonth()])
                ->sum('count');
        });
    }
}
