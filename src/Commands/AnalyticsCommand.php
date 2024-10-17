<?php

namespace ShaferLLC\Analytics\Commands;

use Illuminate\Console\Command;

class AnalyticsCommand extends Command
{
    protected $signature = 'analytics:run';
    protected $description = 'Run the analytics';

    public function handle()
    {
        $this->info('Running analytics...');
    }
}
