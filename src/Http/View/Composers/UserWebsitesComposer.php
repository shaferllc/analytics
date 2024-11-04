<?php

namespace Shaferllc\Analytics\Http\View\Composers;

use Shaferllc\Analytics\Traits\DateRangeTrait;
use Shaferllc\Analytics\Models\Website;
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
        if (Auth::check()) {
            $team = Auth::user()->currentTeam();

            $websites = $team->websites()->orderBy('domain')->get();

            $view->with([
                'websites' => $websites,
                'range' => $this->range(),
            ]);
        }
    }
}