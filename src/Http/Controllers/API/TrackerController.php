<?php

namespace Shaferllc\Analytics\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Shaferllc\Analytics\Models\Website;

class TrackerController
{
    public function __invoke(Request $request, Website $website)
    {
        $jsContent = view('analytics::tracker.index')->render();
        return response($jsContent)->header('Content-Type', 'application/javascript');
    }

}