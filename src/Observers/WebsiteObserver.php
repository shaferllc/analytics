<?php

namespace ShaferLLC\Analytics\Observers;

use ShaferLLC\Analytics\Models\Website;
use Illuminate\Support\Facades\DB;

class WebsiteObserver
{
    /**
     * Handle the Website "deleting" event.
     *
     * @param  Website  $website
     * @return void
     */
    public function deleting(Website $website)
    {
        DB::transaction(function () use ($website) {
            $website->stats()->delete();
            $website->recents()->delete();
        });
    }
}
