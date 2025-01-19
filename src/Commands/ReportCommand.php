<?php

namespace Shaferllc\Analytics\Commands;

use DOMDocument;
use App\Models\Site;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Shaferllc\Analytics\Models\CrawledPage;
use Shaferllc\Analytics\Services\ReportService;

class ReportCommand extends Command
{
    protected $signature = 'report:analyze';
    protected $description = 'Generate a comprehensive analysis report for all websites';

    private ReportService $reportService;

    public function __construct(ReportService $reportService)
    {
        parent::__construct();
        $this->reportService = $reportService;
    }


    public function handle()
    {
        Website::all()->each(function (Website $site) {

            $site->load('crawledPages');
            $site->crawledPages->each(function (CrawledPage $crawledPage) {
                $url = $crawledPage->url;
                // Load DOM
                $dom = $this->reportService->getHtmlFromUrl($url);

                // Generate full report
                if($dom) {
                    $this->reportService->generateFullReport($crawledPage, $dom);
                }
            });
        });
    }
}
