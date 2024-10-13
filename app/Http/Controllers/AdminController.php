<?php

namespace ShaferLLC\Analytics\Http\Controllers;

use ShaferLLC\Analytics\Http\Requests\{
    StoreCouponRequest,
    UpdateCouponRequest,
    UpdateWebsiteRequest
};
use ShaferLLC\Analytics\Models\{
    Coupon,
    Page,
    Payment,
    Plan,
    Setting,
    TaxRate,
    User,
    Website
};
use ShaferLLC\Analytics\Traits\{UserTrait, WebsiteTrait};
use ShaferLLC\Analytics\Mail\PaymentMail;
use Carbon\Carbon;
use GuzzleHttp\Client as HttpClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Hash, Mail};
use Illuminate\Support\Str;

class AdminController extends Controller
{
    use UserTrait, WebsiteTrait;

    /**
     * Show the Dashboard.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function dashboard()
    {
        $stats = [
            'users' => User::withTrashed()->count(),
            'plans' => Plan::withTrashed()->count(),
            'websites' => Website::count()
        ];

        $users = User::withTrashed()->latest('id')->limit(5)->get();

  
        $websites = Website::latest('id')->limit(5)->get();

        return view('admin.dashboard.index', compact('stats', 'users', 'websites'));
    }

    /**
     * List the Websites.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\View
     */
    public function indexWebsites(Request $request)
    {
        $search = $request->input('search');
        $searchBy = $request->input('search_by', 'domain');
        $userId = $request->input('user_id');
        $sortBy = $request->input('sort_by', 'id');
        $sort = $request->input('sort', 'desc');
        $perPage = $request->input('per_page', config('settings.paginate'));

        $websites = Website::with('user')
            ->when($userId, fn($query) => $query->ofUser($userId))
            ->when($search, fn($query) => $query->searchDomain($search))
            ->orderBy($sortBy, $sort)
            ->paginate($perPage)
            ->appends($request->only(['search', 'search_by', 'user_id', 'sort_by', 'sort', 'per_page']));

        $filters = [];
        if ($userId && $user = User::find($userId)) {
            $filters['user'] = $user->name;
        }

        return view('admin.container', ['view' => 'admin.websites.list', 'websites' => $websites, 'filters' => $filters]);
    }

    /**
     * Show the edit Website form.
     *
     * @param int $id
     * @return \Illuminate\Contracts\View\View
     */
    public function editWebsite(int $id)
    {
        $website = Website::findOrFail($id);

        return view('admin.container', ['view' => 'websites.edit', 'website' => $website]);
    }

    /**
     * Update the Website.
     *
     * @param UpdateWebsiteRequest $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateWebsite(UpdateWebsiteRequest $request, int $id)
    {
        $website = Website::findOrFail($id);

        $this->websiteUpdate($request, $website);

        return redirect()->route('admin.websites.edit', $id)->with('success', __('Settings saved.'));
    }

    /**
     * Delete the Website.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyWebsite(int $id)
    {
        $website = Website::findOrFail($id);
        $website->delete();

        $user = User::find($website->user_id);
        $user->has_websites = Website::where('user_id', $user->id)->exists();
        $user->save();

        return redirect()->route('admin.websites')->with('success', __(':name has been deleted.', ['name' => $website->domain]));
    }
}
