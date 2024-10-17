<?php

namespace ShaferLLC\Analytics\Traits;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;

trait WebhookTrait
{
    /**
     * Call webhook event.
     *
     * @param string|null $url
     * @param array $data
     */
    protected function callWebhook(?string $url, array $data): void
    {
        if (!$url) {
            return;
        }

        try {
            Http::timeout(5)->post($url, $data);
        } catch (RequestException $e) {
            // Log the exception or handle it as needed
        }
    }
}