<?php

namespace Shaferllc\Analytics\Commands;

use Shaferllc\Analytics\Models\Recent;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ClearRecentsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:clear-recents';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear the `recents` database table';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Clearing recents table...');

        DB::table('recents')->truncate();

        $this->info('Recents table cleared successfully.');

        return 0;
    }
}
