<?php

namespace ShaferLLC\Analytics\Commands;

use ShaferLLC\Analytics\Mail\LimitExceededMail;
use ShaferLLC\Analytics\Models\Stat;
use ShaferLLC\Analytics\Models\User;
use ShaferLLC\Analytics\Models\Website;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

class VerifyUserLimitsCommand extends Command
{
    protected $signature = 'cron:verify-user-limits';
    protected $description = 'Verify the user limits';

    public function handle()
    {
        $now = Carbon::now();
        $startOfMonth = $now->startOfMonth();
        $endOfMonth = $now->endOfMonth();

        User::where('has_websites', 1)->chunk(100, function ($users) use ($startOfMonth, $endOfMonth) {
            foreach ($users as $user) {
                $pageviews = $this->getUserPageviews($user, $startOfMonth, $endOfMonth);
                $this->updateUserTrackingStatus($user, $pageviews);
            }
        });

        return 0;
    }

    private function getUserPageviews(User $user, Carbon $startOfMonth, Carbon $endOfMonth): int
    {
        return Stat::where('name', 'pageviews')
            ->whereIn('website_id', $user->websites()->pluck('id'))
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->sum('count');
    }

    private function updateUserTrackingStatus(User $user, int $pageviews): void
    {
        $planPageviews = $user->plan->features->pageviews;
        $shouldTrack = $planPageviews == -1 || $pageviews < $planPageviews;

        if ($user->can_track !== $shouldTrack) {
            $user->can_track = $shouldTrack;
            $user->save();

            if (!$shouldTrack && $user->email_account_limit) {
                $this->sendLimitExceededEmail($user);
            }
        }
    }

    private function sendLimitExceededEmail(User $user): void
    {
        try {
            Mail::to($user->email)->locale($user->locale)->send(new LimitExceededMail());
        } catch (\Exception $e) {
            // Log the exception or handle it as needed
        }
    }
}
