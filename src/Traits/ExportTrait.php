<?php

namespace ShaferLLC\Analytics\Traits;

use Carbon\Carbon;
use League\Csv as CSV;
use Carbon\CarbonTimeZone;
use Illuminate\Http\Request;
use ShaferLLC\Analytics\Models\Website;

trait ExportTrait
{
    private function exportGeneric(Request $request, $id, $title, $nameColumn, $countColumn, $dataMethod)
    {
        $website = Website::where('domain', $id)->firstOrFail();

        if ($this->statsGuard($website)) {
            return view('stats.password', ['website' => $website]);
        }

        $range = $this->range();
        $search = $request->input('search');
        $searchBy = in_array($request->input('search_by'), ['value']) ? $request->input('search_by') : 'value';
        $sortBy = in_array($request->input('sort_by'), ['count', 'value']) ? $request->input('sort_by') : 'count';
        $sort = in_array($request->input('sort'), ['asc', 'desc']) ? $request->input('sort') : 'desc';

        return $this->exportCSV($request, $website, $title, $range, $nameColumn, $countColumn, $this->$dataMethod($website, $range, $search, $searchBy, $sortBy, $sort)->get());
    }

    public function exportPages(Request $request, $id)
    {
        return $this->exportGeneric($request, $id, __('Pages'), __('URL'), __('Pageviews'), 'getPages');
    }

    public function exportLandingPages(Request $request, $id)
    {
        return $this->exportGeneric($request, $id, __('Landing pages'), __('URL'), __('Visitors'), 'getLandingPages');
    }

    public function exportReferrers(Request $request, $id)
    {
        return $this->exportGeneric($request, $id, __('Referrers'), __('Website'), __('Visitors'), 'getReferrers');
    }

    public function exportSearchEngines(Request $request, $id)
    {
        return $this->exportGeneric($request, $id, __('Search engines'), __('Website'), __('Visitors'), 'getSearchEngines');
    }

    public function exportSocialNetworks(Request $request, $id)
    {
        return $this->exportGeneric($request, $id, __('Social networks'), __('Website'), __('Visitors'), 'getSocialNetworks');
    }

    public function exportCampaigns(Request $request, $id)
    {
        return $this->exportGeneric($request, $id, __('Campaigns'), __('Name'), __('Visitors'), 'getCampaigns');
    }

    public function exportContinents(Request $request, $id)
    {
        return $this->exportGeneric($request, $id, __('Continents'), __('Name'), __('Visitors'), 'getContinents');
    }

    public function exportCountries(Request $request, $id)
    {
        return $this->exportGeneric($request, $id, __('Countries'), __('Name'), __('Visitors'), 'getCountries');
    }

    public function exportCities(Request $request, $id)
    {
        return $this->exportGeneric($request, $id, __('Cities'), __('Name'), __('Visitors'), 'getCities');
    }

    public function exportLanguages(Request $request, $id)
    {
        return $this->exportGeneric($request, $id, __('Languages'), __('Name'), __('Visitors'), 'getLanguages');
    }

    public function exportOperatingSystems(Request $request, $id)
    {
        return $this->exportGeneric($request, $id, __('Operating systems'), __('Name'), __('Visitors'), 'getOperatingSystems');
    }

    public function exportBrowsers(Request $request, $id)
    {
        return $this->exportGeneric($request, $id, __('Browsers'), __('Name'), __('Visitors'), 'getBrowsers');
    }

    public function exportScreenResolutions(Request $request, $id)
    {
        return $this->exportGeneric($request, $id, __('Screen resolutions'), __('Size'), __('Visitors'), 'getScreenResolutions');
    }

    public function exportDevices(Request $request, $id)
    {
        return $this->exportGeneric($request, $id, __('Devices'), __('Type'), __('Visitors'), 'getDevices');
    }

    public function exportEvents(Request $request, $id)
    {
        return $this->exportGeneric($request, $id, __('Events'), __('Name'), __('Completions'), 'getEvents');
    }

    private function exportCSV($request, $website, $title, $range, $name, $count, $results)
    {
        if ($website->user->cannot('dataExport', ['App\Models\User'])) {
            abort(403);
        }

        $content = CSV\Writer::createFromFileObject(new \SplTempFileObject);

        $headerData = [
            [__('Website'), $website->domain],
            [__('Type'), $title],
            [__('Interval'), $range['from'] . ' - ' . $range['to']],
            [__('Date'), Carbon::now()->format(__('Y-m-d H:i:s')) . ' (' . CarbonTimeZone::create(config('app.timezone'))->toOffsetName() . ')'],
            [__('URL'), $request->fullUrl()],
            [__(' ')]
        ];
        $content->insertAll($headerData);

        $summaryData = [
            [__('Visitors'), $this->getSumForStat($website->id, 'visitors', $range)],
            [__('Pageviews'), $this->getSumForStat($website->id, 'pageviews', $range)],
            [__(' ')]
        ];
        $content->insertAll($summaryData);

        $content->insertOne([__($name), __($count)]);
        $content->insertAll($results->map->toArray());

        return response((string) $content, 200, [
            'Content-Type' => 'text/csv',
            'Content-Transfer-Encoding' => 'binary',
            'Content-Disposition' => 'attachment; filename="' . formatTitle([$website->domain, $title, $range['from'], $range['to'], config('settings.title')]) . '.csv"'
        ]);
    }
}