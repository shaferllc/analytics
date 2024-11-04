<?php

namespace Shaferllc\Analytics\Http\Controllers\API;

use Shaferllc\Analytics\Http\Controllers\Controller;
use Shaferllc\Analytics\Http\Requests\API\SelectStatsRequest;
use Shaferllc\Analytics\Http\Resources\StatResource;
use Shaferllc\Analytics\Models\Stat;
use Shaferllc\Analytics\Models\Website;
use Illuminate\Http\JsonResponse;

class StatController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param SelectStatsRequest $request
     * @param int $id
     * @return StatResource|JsonResponse
     */
    public function show(SelectStatsRequest $request, int $id)
    {
        $website = Website::find($id);

        if (!$website) {
            return response()->json([
                'message' => __('Resource not found.'),
                'status' => 404
            ], 404);
        }

        $search = $request->input('search');
        $searchBy = $request->input('search_by', 'value');
        $sortBy = $request->input('sort_by', 'count');
        $sort = $request->input('sort', 'desc');
        $perPage = $request->input('per_page', config('settings.paginate'));

        $query = Stat::selectRaw('`value`, SUM(`count`) as `count`')
            ->where('website_id', $website->id)
            ->where('name', $request->input('name'))
            ->whereBetween('date', [$request->input('from'), $request->input('to')])
            ->groupBy('value');

        if ($search) {
            $query->searchValue($search);
        }

        $stat = $query->orderBy($sortBy, $sort)
            ->paginate($perPage)
            ->appends($request->only(['name', 'from', 'to', 'search', 'search_by', 'sort_by', 'sort', 'per_page']));

        return StatResource::make($stat);
    }
}
