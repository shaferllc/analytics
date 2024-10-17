<?php

namespace ShaferLLC\Analytics\Http\Controllers;

use ShaferLLC\Analytics\Http\Controllers\Controller;
use ShaferLLC\Analytics\Traits\DateRangeTrait;
use ShaferLLC\Analytics\Models\Website;
use ShaferLLC\Analytics\Http\Requests\StoreWebsiteRequest;
use ShaferLLC\Analytics\Http\Requests\UpdateWebsiteRequest;
use ShaferLLC\Analytics\Traits\WebsiteTrait;
use Illuminate\Http\Request;

class WebsiteController
{
    use WebsiteTrait, DateRangeTrait;

    /**
     * Show the create Website form.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('analytics::websites.create');
    }

    /**
     * Show the edit Website form.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function edit(Request $request, Website $id)
    {
        $website = $this->getUserWebsite($request->user()->id, $id);

        return view('websites.container', ['view' => 'edit', 'website' => $website]);
    }

    /**
     * Store the Website.
     *
     * @param StoreWebsiteRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreWebsiteRequest $request)
    {
        $this->websiteStore($request);

        $this->updateUserWebsiteStatus($request->user());

        return redirect()->route('dashboard')->with('success', __(':name has been created.', ['name' => $request->input('domain')]));
    }

    /**
     * Update the Website.
     *
     * @param UpdateWebsiteRequest $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateWebsiteRequest $request, $id)
    {
        $website = $this->getUserWebsite($request->user()->id, $id);

        $this->websiteUpdate($request, $website);

        return back()->with('success', __('Settings saved.'));
    }

    /**
     * Delete the Website.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, $id)
    {
        $website = $this->getUserWebsite($request->user()->id, $id);

        $website->delete();

        $this->updateUserWebsiteStatus($request->user());

        return redirect()->route('dashboard')->with('success', __(':name has been deleted.', ['name' => $website->domain]));
    }

    /**
     * Get user's website or fail.
     *
     * @param int $userId
     * @param int $websiteId
     * @return Website
     */
    private function getUserWebsite($userId, $websiteId)
    {
        return Website::where('id', $websiteId)->where('user_id', $userId)->firstOrFail();
    }

    /**
     * Update user's has_websites status.
     *
     * @param \App\Models\User $user
     */
    private function updateUserWebsiteStatus($user)
    {
        $user->has_websites = Website::where('user_id', $user->id)->exists();
        $user->save();
    }
}
