<?php

namespace ShaferLLC\Analytics\Http\View\Composers;

use ShaferLLC\Analytics\Models\Website;
use ShaferLLC\Analytics\Models\Stat;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class UserStatsComposer
{
    /**
     * @var int
     */
    private $pageviewsCount;

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $now = Carbon::now();

            $this->pageviewsCount = Cache::remember("user_{$user->id}_pageviews_count", 60, function () use ($user, $now) {
                return Stat::where('name', 'pageviews')
                    ->whereIn('website_id', function ($query) use ($user) {
                        $query->select('id')
                              ->from('websites')
                              ->where('user_id', $user->id);
                    })
                    ->whereBetween('date', [$now->startOfMonth(), $now->endOfMonth()])
                    ->sum('count');
            });

            $view->with('pageviewsCount', $this->pageviewsCount);
        }
    }
}
