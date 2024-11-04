<?php

namespace Shaferllc\Analytics\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LimitExceededMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject(formatTitle([__('Account limit exceeded'), config('settings.title')]))
            ->markdown('vendor.notifications.email', [
                'introLines' => [__('Your account has exceeded the pageviews limit. If you wish to continue tracking your visitors, upgrade your account.')],
                'actionText' => __('Upgrade'),
                'actionUrl' => route('pricing')
            ]);
    }
}
