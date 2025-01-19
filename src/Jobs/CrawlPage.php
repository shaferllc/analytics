<?php

namespace Shaferllc\Analytics\Jobs;

use App\Models\Site;
use Spatie\Crawler\Crawler;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Shaferllc\Analytics\Services\Page;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Shaferllc\Analytics\Models\CrawledPage;
use Shaferllc\Analytics\Crawler\CrawlObserver;
use Spatie\Crawler\CrawlProfiles\CrawlInternalUrls;

class CrawlPage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Website $site, public string $url) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Crawler::create()
                ->ignoreRobots()
                // ->setDefaultScheme('https')
                ->executeJavaScript()
                // ->setDelayBetweenRequests(1000)
                // ->setMaximumResponseSize(1024 * 1024 * 3)
                ->setConcurrency(3)
                ->setTotalCrawlLimit(5)
                ->setCrawlObserver(new CrawlObserver($this->site))
                ->setCrawlProfile(new CrawlInternalUrls($this->site))
                ->startCrawling($this->site->url);

    }

    /**
     * Get the tags that should be assigned to the job.
     *
     * @return array<string>
     */
    public function tags(): array
    {
        return [
            static::class,
            'Url:'.$this->url,
        ];
    }
}
