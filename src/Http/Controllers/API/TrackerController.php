<?php

namespace Shaferllc\Analytics\Http\Controllers\API;

use App\Models\Site;
use JShrink\Minifier;
use Illuminate\Http\Request;

class TrackerController
{
    public function __invoke(Request $request, Site $site)
    {
        // $jsContent = cache()->remember('analytics-tracker-js', now()->addHour(), function() {
        //     $jsContent = view('analytics::tracker.index')->render();
        //     return Minifier::minify($jsContent);
        // });


        $jsContent = view('analytics::tracker.index')->render();
        return response($jsContent)
            ->header('Content-Type', 'application/javascript');
            // ->header('Cache-Control', 'public, max-age=3600')
            // ->header('Expires', now()->addHour()->toRfc7231String())
            // ->header('ETag', md5($jsContent));
    }

}
