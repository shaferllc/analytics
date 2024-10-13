<?php

namespace ShaferLLC\Analytics\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReportMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var array
     */
    public $stats;

    /**
     * @var string
     */
    public $range;

    /**
     * Create a new message instance.
     *
     * @param array $stats
     * @param string $range
     */
    public function __construct(array $stats, string $range)
    {
        $this->stats = $stats;
        $this->range = $range;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject(formatTitle([__('Periodic report'), config('settings.title')]))
                    ->markdown('emails.report', [
                        'introLines' => [__('Your periodic report is ready.')],
                        'stats' => $this->stats,
                        'range' => $this->range
                    ]);
    }
}
