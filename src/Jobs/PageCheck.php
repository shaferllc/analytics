<?php

namespace Shaferllc\Analytics\Jobs;

use Shaferllc\Analytics\Services\Page;
use App\Models\Site;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PageCheck implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Website $site)
    {

    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        (new Page($this->site))->run();
    }

    public function tags(): array
    {
        return [
            static::class,
            'Website:'.$this->site->id,
        ];
    }
}
