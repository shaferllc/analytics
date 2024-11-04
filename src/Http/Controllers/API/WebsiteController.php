<?php

namespace Shaferllc\Analytics\Http\Controllers\API;

use Shaferllc\Analytics\Http\Controllers\Controller;
use Shaferllc\Analytics\Http\Requests\API\{StoreWebsiteRequest, UpdateWebsiteRequest};
use Shaferllc\Analytics\Http\Resources\WebsiteResource;
use Shaferllc\Analytics\Models\Website;
use Shaferllc\Analytics\Traits\WebsiteTrait;
use Illuminate\Http\{Request, JsonResponse, Resources\Json\AnonymousResourceCollection};

class WebsiteController extends Controller
{
    use WebsiteTrait;

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $search = $request->input('search');
        $searchBy = $request->input('search_by', 'domain');
        $sortBy = $request->input('sort_by', 'id');
        $sort = $request->input('sort', 'desc');
        $perPage = $request->input('per_page', config('settings.paginate'));

        $websites = Website::where('user_id', $request->user()->id)
            ->when($search, fn($query) => $query->searchDomain($search))
            ->orderBy($sortBy, $sort)
            ->paginate($perPage)
            ->appends($request->only(['search', 'search_by', 'sort_by', 'sort', 'per_page']));

        return WebsiteResource::collection($websites)->additional(['status' => 200]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreWebsiteRequest $request
     * @return WebsiteResource|JsonResponse
     */
    public function store(StoreWebsiteRequest $request)
    {
        $created = $this->websiteStore($request);

        return $created
            ? WebsiteResource::make($created)
            : $this->notFoundResponse();
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param int $id
     * @return WebsiteResource|JsonResponse
     */
    public function show(Request $request, int $id)
    {
        $website = Website::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        return $website
            ? WebsiteResource::make($website)
            : $this->notFoundResponse();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateWebsiteRequest $request
     * @param int $id
     * @return WebsiteResource|JsonResponse
     */
    public function update(UpdateWebsiteRequest $request, int $id)
    {
        $website = Website::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $updated = $this->websiteUpdate($request, $website);

        return $updated
            ? WebsiteResource::make($updated)
            : $this->notFoundResponse();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $website = Website::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if ($website) {
            $website->delete();
            return response()->json([
                'id' => $website->id,
                'object' => 'website',
                'deleted' => true,
                'status' => 200
            ]);
        }

        return $this->notFoundResponse();
    }

    /**
     * Return a not found JSON response.
     *
     * @return JsonResponse
     */
    private function notFoundResponse(): JsonResponse
    {
        return response()->json([
            'message' => __('Resource not found.'),
            'status' => 404
        ], 404);
    }
}
