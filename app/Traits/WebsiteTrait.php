<?php

namespace ShaferLLC\Analytics\Traits;

use ShaferLLC\Analytics\Models\Website;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

trait WebsiteTrait
{
    /**
     * Store the Website.
     *
     * @param Request $request
     * @return Website
     */
    protected function websiteStore(Request $request)
    {
        $website = new Website([
            'domain' => $request->input('domain'),
            'user_id' => Auth::id(),
            'privacy' => $request->input('privacy'),
            'password' => $request->input('password'),
            'email' => $request->input('email'),
            'exclude_bots' => $request->input('exclude_bots', 1),
            'exclude_params' => $request->input('exclude_params'),
            'exclude_ips' => $request->input('exclude_ips'),
        ]);

        $website->save();

        return $website;
    }

    /**
     * Update the Website.
     *
     * @param Request $request
     * @param Website $website
     * @return Website
     */
    protected function websiteUpdate(Request $request, Website $website)
    {
        $fillable = ['privacy', 'email', 'password', 'exclude_bots', 'exclude_params', 'exclude_ips'];

        $website->fill($request->only($fillable));
        $website->save();

        return $website;
    }
}