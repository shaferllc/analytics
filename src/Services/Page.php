<?php

namespace Shaferllc\Analytics\Services;

use Exception;
use App\Models\Site;
use Shaferllc\Analytics\Models\CrawledPage;
use Spatie\Crawler\Crawler;
use GuzzleHttp\RequestOptions;
use Shaferllc\Analytics\Crawler\CrawlObserver;
use Spatie\Crawler\CrawlProfiles\CrawlInternalUrls;

class Page
{
    public function __construct(public Website $site) {
    }


    public function run(): void
    {
        try {
            Crawler::create()
                ->ignoreRobots()
                // ->setDefaultScheme('https')
                ->executeJavaScript()
                // ->setDelayBetweenRequests(1000)
                // ->setMaximumResponseSize(1024 * 1024 * 3)
                ->setConcurrency(3)
                ->setCrawlObserver(new CrawlObserver($this->site))
                ->setCrawlProfile(new CrawlInternalUrls($this->site->domain))
                ->startCrawling($this->site->domain);
        } catch (Exception $exception) {

        }
    }
}
