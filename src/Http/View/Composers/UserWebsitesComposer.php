<?php

namespace ShaferLLC\Analytics\Http\View\Composers;

use ShaferLLC\Analytics\Traits\DateRangeTrait;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class UserWebsitesComposer
{
    use DateRangeTrait;

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
        $websites = $team->websites()->orderBy('domain')->get();

        $view->with([
            'websites' => $websites,
            'range' => $this->range(),
        ]);
    }
}