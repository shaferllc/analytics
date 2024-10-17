<?php

namespace ShaferLLC\Analytics\Commands;

use ShaferLLC\Analytics\Mail\ReportMail;
use ShaferLLC\Analytics\Models\User;
use ShaferLLC\Analytics\Models\Website;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

class EmailReportsCommand extends Command
{
    protected $signature = 'cron:email-reports';
    protected $description = 'Send out websites analytics email reports';

    public function handle()
    {
        $now = Carbon::now();
        [$from, $to] = $this->getDateRange($now);

        User::where('has_websites', 1)
            ->cursor()
            ->each(function ($user) use ($from, $to) {
                if ($user->can('emailReports', ['App\Models\User'])) {
                    $stats = $this->getWebsiteStats($user, $from, $to);
                    
                    if (!empty($stats)) {
                        $this->sendEmail($user, $stats, $from, $to);
                    }
                }
            });

        return 0;
    }

    private function getDateRange(Carbon $now): array
    {
        if (config('settings.email_reports_period') == 'weekly') {
            return [
                (clone $now)->startOfWeek()->subWeek(),
                (clone $now)->endOfWeek()->subWeek()
            ];
        }
        
        return [
            (clone $now)->startOfMonth()->subMonthsNoOverflow(1),
            (clone $now)->endOfMonth()->subMonthsNoOverflow(1)
        ];
    }

    private function getWebsiteStats(User $user, Carbon $from, Carbon $to): array
    {
        return Website::with([
            'visitors' => function ($query) use ($from, $to) {
                $query->whereBetween('date', [$from->format('Y-m-d'), $to->format('Y-m-d')]);
            },
            'pageviews' => function ($query) use ($from, $to) {
                $query->whereBetween('date', [$from->format('Y-m-d'), $to->format('Y-m-d')]);
            }
        ])
        ->where('user_id', $user->id)
        ->where('email', 1)
        ->get()
        ->map(function ($website) {
            return [
                'domain' => $website->domain,
                'visitors' => $website->visitors->sum('count') ?? 0,
                'pageviews' => $website->pageviews->sum('count') ?? 0
            ];
        })
        ->toArray();
    }

    private function sendEmail(User $user, array $stats, Carbon $from, Carbon $to): void
    {
        try {
            Mail::to($user->email)
                ->locale($user->locale)
                ->send(new ReportMail($stats, $from->format('Y-m-d') . '|' . $to->format('Y-m-d')));
        } catch (\Exception $e) {
            // Log the exception or handle it as needed
        }
    }
}
