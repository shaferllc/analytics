<?php

namespace Shaferllc\Analytics\Jobs;

use App\Models\Site;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Spatie\Browsershot\Browsershot;
use Illuminate\Queue\SerializesModels;
use Shaferllc\Analytics\Models\Report;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Shaferllc\Analytics\Models\CrawledPage;

class BrowserConsoleCheck implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Website $site, public CrawledPage $page, public Report $report) {}

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // try {
            // Ensure directory exists
            $directory = storage_path('app/public/browser-console-output');
            if (!file_exists($directory)) {
                mkdir($directory, 0777, true);
            }

            $pathToImage = storage_path('app/public/browser-console-output/'.$this->page->id.'.png');
            Browsershot::url($this->page->url)
                ->setBinPath(app_path('Crawler/browser.js'))
                ->waitUntilNetworkIdle()
                ->dismissDialogs()
                ->save($pathToImage);

            // $mobilePathToImage = storage_path('app/public/browser-console-output/'.$this->page->id.'-mobile.png');
            // Browsershot::url($this->page->url)
            //     ->setBinPath(app_path('Crawler/browser.js'))
            //     ->mobile()
            //     ->touch()
            //     ->dismissDialogs()
            //     ->save($mobilePathToImage);

            // $this->report->update([
            //     'screenshot' => $pathToImage,
            //     'screenshot_mobile' => $mobilePathToImage
            // ]);
        // } catch (\Throwable $th) {
        //     Log::error('Browsershot error', ['url' => $this->page->url, 'error' => $th->getMessage()]);
        // }

        // try {
        //     $messages = Browsershot::url($this->page->url)->consoleMessages();
        //     $this->page->messages = $messages;
        //     $this->page->save();
        // } catch (\Throwable $th) {
        //     Log::error('Browsershot console messages error', ['url' => $this->page->url, 'error' => $th->getMessage()]);
        // }
    }

    public function tags(): array
    {
        return [
            static::class,
            'Url:'.$this->page->url,
        ];
    }
}
