<?php

namespace Shaferllc\Analytics\Crawler;

use App\Models\Site;
use Shaferllc\Analytics\Models\CrawledPage;
use Illuminate\Support\Str;
use Shaferllc\Analytics\Jobs\BrowserConsoleCheck;
use Psr\Http\Message\UriInterface;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;
use Spatie\Crawler\CrawlObservers\CrawlObserver as SpatieCrawlObserver;
use Illuminate\Support\Facades\Log;

class CrawlObserver extends SpatieCrawlObserver
{
    private Site $site;
    private array $crawlStats = [
        'start_time' => null,
        'end_time' => null,
        'total_time' => null,
        'pages_per_minute' => null
    ];

    public function __construct(Site $site)
    {
        $this->site = $site;
        $this->crawlStats['start_time'] = now();
    }

    public function willCrawl(UriInterface $url, ?string $linkText): void
    {
        $path = parse_url((string)$url, PHP_URL_PATH);
        $this->site->crawledPages()->firstOrCreate([
            'url' => (string)$url,
            'path' => $path
        ]);
    }

    public function crawled(
        UriInterface $url,
        ResponseInterface $response,
        ?UriInterface $foundOnUrl = null,
        ?string $linkText = null,
    ): void {

        $page = $this->site->crawledPages()->where('url', (string)$url)->first();

        $page->exception = null;
        $page->response = $response->getStatusCode() . ' - ' . $response->getReasonPhrase();

        if ($foundOnUrl) {
            $page->found_on = array_unique(array_merge($page->found_on ?? [], [(string)$foundOnUrl]));
        }

        // Store response headers and body for analysis
        $page->headers = $response->getHeaders();
        $page->content_type = $response->getHeaderLine('Content-Type');

        // Extract and store page title if HTML
        // if (str_contains($page->content_type, 'text/html')) {
        //     $body = (string)$response->getBody();
        //     if (preg_match('/<title>(.*?)<\/title>/i', $body, $matches)) {
        //         $page->title = trim($matches[1]);
        //     }
        // }

        // Track crawl timing
        $page->crawled_at = now();

        // Check for meta robots tags
        // if (str_contains($page->content_type, 'text/html')) {
        //     $body = (string)$response->getBody();
        //     if (preg_match('/<meta[^>]*name=["\']robots["\'][^>]*content=["\']([^"\']*)["\']/', $body, $matches)) {
        //         $page->meta_robots = $matches[1];
        //     }
        // }

        BrowserConsoleCheck::dispatch($this->site, $page);

        $page->save();

        Log::info('Successfully crawled URL', [
            'url' => (string)$url,
            'status' => $response->getStatusCode(),
            'content_type' => $page->content_type,
            'title' => $page->title ?? null
        ]);
    }

    public function crawlFailed(
        UriInterface $url,
        RequestException $requestException,
        ?UriInterface $foundOnUrl = null,
        ?string $linkText = null,
    ): void {
        $page = $this->site->crawledPages()->where('url', (string)$url)->first();

        $page->response = null;
        $page->exception = $requestException->getCode() . ' - ' . $requestException->getMessage();
        $page->failed_at = now();

        // Store the referring page for failed URLs
        if ($foundOnUrl) {
            $page->found_on = array_unique(array_merge($page->found_on ?? [], [(string)$foundOnUrl]));
        }

        BrowserConsoleCheck::dispatch($this->site, $page);

        $page->save();

        Log::error('Failed to crawl URL', [
            'url' => (string)$url,
            'error' => $requestException->getMessage(),
            'code' => $requestException->getCode(),
            'found_on' => $page->found_on
        ]);
    }

    /**
     * Called when the crawl has ended.
     */
    public function finishedCrawling(): void
    {
        // Calculate crawl statistics
        $totalPages = $this->site->crawledPages()->count();
        $successfulPages = $this->site->crawledPages()->whereNotNull('response')->count();
        $failedPages = $this->site->crawledPages()->whereNotNull('exception')->count();

        // Calculate timing statistics
        $this->crawlStats['end_time'] = now();
        $this->crawlStats['total_time'] = $this->crawlStats['end_time']->diffInMinutes($this->crawlStats['start_time']);
        $this->crawlStats['pages_per_minute'] = $this->crawlStats['total_time'] > 0
            ? round($totalPages / $this->crawlStats['total_time'], 2)
            : $totalPages;

        Log::info('Finished crawling site', [
            'domain' => $this->site->domain,
            'total_pages' => $totalPages,
            'successful_pages' => $successfulPages,
            'failed_pages' => $failedPages,
            'crawl_duration_minutes' => $this->crawlStats['total_time'],
            'pages_per_minute' => $this->crawlStats['pages_per_minute']
        ]);

        // Store crawl statistics
        $this->site->last_crawl_stats = [
            'total_pages' => $totalPages,
            'successful_pages' => $successfulPages,
            'failed_pages' => $failedPages,
            'crawl_duration' => $this->crawlStats['total_time'],
            'pages_per_minute' => $this->crawlStats['pages_per_minute'],
            'completed_at' => now()->toDateTimeString()
        ];

        $this->site->save();
    }
}
