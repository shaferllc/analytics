<?php

namespace Shaferllc\Analytics\Http\Controllers;

use League\Csv\Reader;
use League\Csv\Writer;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Routing\Controller;
use Illuminate\Support\Collection;
use League\Csv\CannotInsertRecord;
use Illuminate\Support\Facades\Auth;
use Shaferllc\Analytics\Models\Stat;
use Shaferllc\Analytics\Models\Website;
use Illuminate\Database\Eloquent\Builder;
use Shaferllc\Analytics\Traits\DateRangeTrait;
use Shaferllc\Analytics\Http\Requests\ValidateWebsitePasswordRequest;

class BaseController extends Controller
{
    use DateRangeTrait;

      /**
     * Validate the link's password.
     *
     * @param ValidateWebsitePasswordRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function validatePassword(ValidateWebsitePasswordRequest $request, $id)
    {
        session([md5($id) => true]);
        return redirect()->back();
    }

    /**
     * Guard the Stat.
     */
    public function guard(Website $website):bool    
    {
        if ($website->privacy === 0) {
            return false;
        }

        $currentTeam = Auth::user()->currentTeam();

        if ($website->privacy === 1 && ($currentTeam === null || $currentTeam !== $website->team_id)) {
            abort(403);
        }

        if ($website->privacy === 2 && !session(md5($website->domain)) && ($currentTeam === null || $currentTeam !== $website->team_id)) {
            return true;
        }

        return false;
    }

    public function getPages(Website $website, array $range, array $params, string $sortBy = null, string $sort = null): Builder
    {

        return Stat::selectRaw('`value`, SUM(`count`) as `count`')
        ->where([
            ['website_id', '=', $website->id],
            ['name', '=', 'page']
        ])
        ->when(Arr::has($params, 'search'), function ($query) use ($params) {
            return $query->searchValue($params['search']);
        })
        ->whereBetween('date', [$range['from'], $range['to']])
        ->groupBy('value')
        ->orderBy($sortBy ?? $params['sortBy'], $sort ?? $params['sort']);
    }

      /**
     * Export data in CSV format.
     * @throws CannotInsertRecord
     */
    public function exportCSV(
        Request $request, 
        Website $website, 
        string $title, 
        array $range, 
        string $name, 
        string $count, 
        Collection $results
    ): Writer
    {
        $now = Carbon::now();

        $content = Writer::createFromFileObject(new \SplTempFileObject);

        // Generate the header
        $content->insertOne([__('Website'), $website->domain]);
        $content->insertOne([__('Type'), $title]);
        $content->insertOne([__('Interval'), $range['from'] . ' - ' . $range['to']]);
        $content->insertOne([__('Date'), $now->format(__('Y-m-d')) . ' ' . $now->format('H:i:s') . ' (' . $now->getOffsetString() . ')']);
        $content->insertOne([__('URL'), $request->fullUrl()]);
        $content->insertOne([__(' ')]);

        // Generate the summary
        $content->insertOne([__('Visitors'), Stat::where([['website_id', '=', $website->id], ['name', '=', 'visitors']])
            ->whereBetween('date', [$range['from'], $range['to']])
            ->sum('count')]);
        $content->insertOne([__('Pageviews'), Stat::where([['website_id', '=', $website->id], ['name', '=', 'pageviews']])
            ->whereBetween('date', [$range['from'], $range['to']])
            ->sum('count')]);
        $content->insertOne([__(' ')]);

        // Generate the content
        $content->insertOne([__($name), __($count)]);
        foreach ($results as $result) {
            $content->insertOne($result->toArray());
        }

        // Set the output BOM
        $content->setOutputBOM(Reader::BOM_UTF8);

        return response((string) $content, 200, [
            'Content-Type' => 'text/csv',
            'Content-Transfer-Encoding' => 'binary',
            'Content-Disposition' => 'attachment; filename="' . formatTitle([$website->domain, $title, $range['from'], $range['to'], config('settings.title')]) . '.csv"'
        ]);
    }
}