<?php

namespace Shaferllc\Analytics\Commands;

use App\Models\Site;
use Shaferllc\Analytics\Jobs\PageCheck;
use Illuminate\Console\Command;

class CrawlCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crawl';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crawls a website and collects all the linked URLs';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $site = Site::whereDomain('https://www.washingtonian.com')->firstOrFail();

        PageCheck::dispatch($site);
    }
}
