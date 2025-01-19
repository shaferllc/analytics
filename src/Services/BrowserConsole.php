<?php

namespace Shaferllc\Analytics\Services;

use App\Models\Site;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Spatie\Browsershot\Browsershot;
use Shaferllc\Analytics\Models\CrawledPage;
// use ShaferLLC\Analytics\Crawler\Browsershot;

class BrowserConsole
{

    public function run(Website $site, CrawledPage $page) {

        try {
            // Ensure directory exists
            $directory = storage_path('app/public/browser-console-output');
            if (!file_exists($directory)) {
                mkdir($directory, 0777, true);
            }

            $pathToImage = storage_path('app/public/browser-console-output/'.$page->id.'.png');

            Browsershot::url($page->url)
                ->dismissDialogs()
                ->save($pathToImage);

            $mobilePathToImage = storage_path('app/public/browser-console-output/'.$page->id.'-mobile.png');
            Browsershot::url($page->url)
                ->mobile()
                ->touch()
                ->dismissDialogs()
                ->save($mobilePathToImage);

        } catch (\Throwable $th) {
            Log::error('Browsershot error', ['url' => $page->url, 'error' => $th->getMessage()]);
        }

        try {
            $messages = Browsershot::url($page->url)->consoleMessages();
            $page->messages = $messages;
            $page->save();
        } catch (\Throwable $th) {
            Log::error('Browsershot console messages error', ['url' => $page->url, 'error' => $th->getMessage()]);
        }


    }
}
