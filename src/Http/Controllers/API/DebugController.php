<?php

namespace Shaferllc\Analytics\Http\Controllers\API;

use App\Models\Site;
use JShrink\Minifier;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DebugController
{
    public function __invoke(Request $request)
    {
        $siteId = $request->input('site_id');
        $site = Site::findOrFail($siteId);
        foreach ($request->input('logs') as $log) {
            $site->debug()->create([
                'message' => Arr::get($log, 'message'),
                'user_agent' => Arr::get($log, 'userAgent'),
                'url' => Arr::get($log, 'url'),
                'session_id' => Arr::get($log, 'sessionId'),
                'level' => Arr::get($log, 'level'),
                'timestamp' => Carbon::parse(Arr::get($log, 'timestamp'))->toDateTimeString(),
            ]);
        }

        return response()->json(['message' => __('Debug log received')]);
    }

}
