<?php

namespace ShaferLLC\Analytics\Http\Controllers;

use ShaferLLC\Analytics\Http\Requests\ValidateWebsitePasswordRequest;
use ShaferLLC\Analytics\Traits\DateRangeTrait;
use ShaferLLC\Analytics\Models\Website;
use ShaferLLC\Analytics\Models\Recent;
use ShaferLLC\Analytics\Models\Stat;
use Carbon\Carbon;
use Carbon\CarbonTimeZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use League\Csv as CSV;
use ShaferLLC\Analytics\Traits\StatTrait;
use ShaferLLC\Analytics\Traits\ExportTrait;
use ShaferLLC\Analytics\Traits\TypesTrait;

class StatController extends Controller
{
    use DateRangeTrait, StatTrait, ExportTrait, TypesTrait;

    public function index(Request $request, $id)
    {
        $website = $this->getWebsiteOrFail($id);

        if ($this->statsGuard($website)) {
            return $this->passwordView($website);
        }

        $range = $this->range();
        $stats = $this->getOverviewStats($website, $range);

        return $this->containerView('overview', $website, $range, $stats);
    }

    public function realtime(Request $request, $id)
    {
        $website = $this->getWebsiteOrFail($id);

        if ($this->statsGuard($website)) {
            return $this->passwordView($website);
        }

        $range = $this->range();

        return $request->wantsJson()
            ? $this->getRealtimeJsonResponse($website)
            : $this->containerView('realtime', $website, $range);
    }

    private function formatRealtimeCounts(array $countsMap)
    {
        return array_combine(
            array_map(fn($key) => Carbon::parse($key)->diffForHumans(['options' => Carbon::JUST_NOW]), array_keys($countsMap)),
            $countsMap
        );
    }

    public function showStats(Request $request, $id, $type)
    {
        $website = $this->getWebsiteOrFail($id);

        if ($this->statsGuard($website)) {
            return $this->passwordView($website);
        }

        $range = $this->range();
        $params = $this->validateAndGetParams($request);
        $statName = $this->getStatName($type);
        $websites = $this->getWebsitesForType($type);

        $total = $this->getTotal($website, $range, $statName, $websites);
        $data = $this->getData($website, $range, $params['search'], $params['searchBy'], $params['sortBy'], $params['sort'], $type);
        $paginatedData = $data->paginate($params['perPage'])->appends($request->except('page'));

        $first = $data->orderBy('count', 'desc')->first();
        $last = $data->orderBy('count', 'asc')->first();

        return $this->containerView($type, $website, $range, [
            'export' => "stats.export.{$type}",
            $type => $paginatedData,
            'first' => $first,
            'last' => $last,
            'total' => $total
        ]);
    }

    public function validatePassword(ValidateWebsitePasswordRequest $request, $id)
    {
        session([md5($id) => true]);
        return redirect()->back();
    }

    private function statsGuard($website)
    {
        if ($website->privacy === 0) {
            return false;
        }

        $user = Auth::user();

        return match ($website->privacy) {
            1 => $this->guardPrivateWebsite($user, $website),
            2 => $this->guardPasswordProtectedWebsite($user, $website),
            default => false,
        };
    }

    private function guardPrivateWebsite($user, $website)
    {
        if (!$user || ($user->id != $website->user_id && $user->role != 1)) {
            abort(403);
        }
        return false;
    }

    private function guardPasswordProtectedWebsite($user, $website)
    {
        if (!session(md5($website->domain))) {
            if (!$user || ($user->id != $website->user_id && $user->role != 1)) {
                return true;
            }
        }
        return false;
    }

    private function getWebsiteOrFail($id)
    {
        return Website::where('domain', $id)->firstOrFail();
    }

    private function passwordView($website)
    {
        return view('stats.password', ['website' => $website]);
    }

    private function containerView($view, $website, $range, $additionalData = [])
    {
        return view('stats.container', array_merge([
            'view' => $view,
            'website' => $website,
            'range' => $range,
        ], $additionalData));
    }

    private function validateAndGetParams(Request $request)
    {
        return [
            'search' => $request->input('search'),
            'searchBy' => in_array($request->input('search_by'), ['value']) ? $request->input('search_by') : 'value',
            'sortBy' => in_array($request->input('sort_by'), ['count', 'value']) ? $request->input('sort_by') : 'count',
            'sort' => in_array($request->input('sort'), ['asc', 'desc']) ? $request->input('sort') : 'desc',
            'perPage' => in_array($request->input('per_page'), [10, 25, 50, 100]) ? $request->input('per_page') : config('settings.paginate'),
        ];
    }

    private function getWebsitesForType($type)
    {
        return match ($type) {
            'social-networks' => $this->getSocialNetworksList(),
            'search-engines' => $this->getSearchEnginesList(),
            default => null,
        };
    }
}
