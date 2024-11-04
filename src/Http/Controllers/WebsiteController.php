<?php

namespace Shaferllc\Analytics\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Shaferllc\Analytics\Models\Website;
use Shaferllc\Analytics\Traits\DateRangeTrait;
use Shaferllc\Analytics\Rules\ValidateBadWordsRule;
use Shaferllc\Analytics\Rules\ValidateDomainNameRule;

class WebsiteController
{
    use DateRangeTrait;

    /**
     * Show the create Website form.
     */
    public function create(): View
    {
        return view('analytics::websites.create');
    }

    /**
     * Show the edit Website form.
     */
    public function edit(Request $request, Website $website): View
    {
        return view('websites.container', ['view' => 'edit', 'website' => $website]);
    }

    /**
     * Store the Website.
     */
    public function store(Request $request): RedirectResponse
    {
        $domain = preg_replace('/^(https?:\/\/)?(www\.)?/', '', mb_strtolower($request->input('domain')));
        $request->merge(['domain' => $domain]);
        
        // $request->validate([
        //    'domain' => [
        //         'required',
        //         'max:255',
        //         new ValidateDomainNameRule(),
        //         'unique:websites,domain',   
        //         new ValidateBadWordsRule()
        //     ],
        //     'privacy' => ['nullable', 'integer', 'between:0,2'],
        //     'password' => [
        //         Rule::requiredIf(fn() => $request->input('privacy') == 2),
        //         'nullable',
        //         'string',
        //         'min:1',
        //         'max:128'
        //     ],
        //     'exclude_bots' => ['nullable', 'boolean'],
        //     'exclude_params' => ['nullable', 'string'],
        //     'exclude_ips' => ['nullable', 'string'],
        //     'email' => ['nullable', 'integer']
        // ]);
        // dd($request);
        $website = $request->user()->currentTeam()->websites()->create($request->only(['domain', 'privacy', 'password', 'exclude_bots', 'exclude_ips', 'exclude_params', 'email']));

        $success = __(':name has been created.', ['name' => $website->domain]);
       

        return redirect()->route('stats.overview', $website)->with('success', $success);
    }

    /**
     * Update the Website.
     */
    public function update(Request $request, Website $website): RedirectResponse
    {

        if (!$request->user()->currentTeam()->websites->doesntContain($website)) {
            abort(403);
        }

        $request->validate([
            'privacy' => ['sometimes', 'required', 'integer', Rule::in([0, 1, 2])],
            'password' => [
                $request->input('privacy') < 2 ? 'nullable' : 'sometimes',
                'string',
                'min:1',
                'max:128'
            ],
            'exclude_bots' => ['sometimes', 'boolean'],
            'exclude_ips' => ['sometimes', 'nullable', 'string'],
            'exclude_params' => ['sometimes', 'nullable', 'string'],
            'email' => ['sometimes', 'nullable', 'integer']
        ]);
      
        $website->update($request->only($request->validated()));

        return back()->with('success', __('Settings saved.'));
    }

    /**
     * Delete the Website.
     */
    public function destroy(Request $request, Website $website): RedirectResponse
    {

        if (!$request->user()->currentTeam()->websites->doesntContain($website)) {
            abort(403);
        }

        $message = __(':name has been deleted.', ['name' => $website->domain]);

        $website->delete();

        return redirect()->route('analytics')->with('success', $message);
    }
}
