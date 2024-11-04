<?php

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Shaferllc\Analytics\Models\Stat;

class StatsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Stat::truncate();
        $websites = DB::table('websites')->pluck('id');
        $now = Carbon::now();

        foreach ($websites as $websiteId) {
            // Seed visitors
            for ($i = 0; $i < 100; $i++) {
                DB::table('visitors')->insert([
                    'website_id' => $websiteId,
                    'ip' => $this->generateRandomIp(),
                    'user_agent' => $this->generateRandomUserAgent(),
                    'created_at' => $now->subDays(rand(0, 30))->subHours(rand(0, 23)),
                    'updated_at' => $now,
                ]);
            }

            // Seed pageviews
            for ($i = 0; $i < 500; $i++) {
                DB::table('pageviews')->insert([
                    'website_id' => $websiteId,
                    'url' => $this->generateRandomUrl(),
                    'created_at' => $now->subDays(rand(0, 30))->subHours(rand(0, 23)),
                    'updated_at' => $now,
                ]);
            }

            // Seed events
            // for ($i = 0; $i < 1000; $i++) {
            //     DB::table('stats')->insert([
            //         'id' => Str::ulid(),
            //         'website_id' => $websiteId,
            //         'name' => 'event',
            //         'value' => 'event:' . $i,
            //         'count' => rand(1, 10),
            //         'date' => $now->subDays(rand(0, 30))->subHours(rand(0, 23)),
            //     ]);
            // }
        }
    }

    private function generateRandomIp()
    {
        return rand(1, 255) . '.' . rand(0, 255) . '.' . rand(0, 255) . '.' . rand(0, 255);
    }

    private function generateRandomUserAgent()
    {
        $userAgents = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.1.1 Safari/605.1.15',
            'Mozilla/5.0 (iPhone; CPU iPhone OS 14_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.0 Mobile/15E148 Safari/604.1',
            'Mozilla/5.0 (Linux; Android 11; SM-G991B) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.120 Mobile Safari/537.36',
        ];

        return $userAgents[array_rand($userAgents)];
    }

    private function generateRandomUrl()
    {
        $paths = ['/', '/about', '/contact', '/products', '/services', '/blog', '/faq'];
        return $paths[array_rand($paths)];
    }
}
