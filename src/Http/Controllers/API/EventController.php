<?php

namespace Shaferllc\Analytics\Http\Controllers\API;

use App\Models\Site;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Streamline\Classes\Agent;
use Illuminate\Support\Carbon;
use Streamline\Jobs\Screenshot;
use Illuminate\Routing\Pipeline;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use GeoIp2\Database\Reader as GeoIP;
use Shaferllc\Analytics\Models\Meta;
use Shaferllc\Analytics\Models\Event;
use Shaferllc\Analytics\Models\EventLogs;
use Shaferllc\Analytics\Models\PageVisitor;
use Symfony\Component\HttpFoundation\IpUtils;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;

class EventController extends Controller
{
    private const MAX_REQUEST_SIZE = 1024 * 1024; // 1MB
    private const REQUESTS_PER_MINUTE = 1000;
    private const MAX_BACKOFF_TIME = 300; // 5 minutes
    private const REQUEST_CACHE_TTL = 24; // hours
    private ?EventLogs $eventLog = null;

    /**
     * The tracking mechanism.
     *
     * @param Request $request
     */
    public function __invoke(Request $request)
    {
        // Extract site_id from request data
        $siteId = $request->input('event.site_id');

        // $this->eventLog = EventLogs::create([
        //     'event_type' => 'analytics_request',
        //     'event_data' => [],
        //     'status' => 'started',
        //     'site_id' => $siteId ?: null,  // Change to null instead of 0
        //     'occurred_at' => now(),
        // ]);

        // if (app()->environment('local')) {
        //     $this->logEvent('Starting event processing', $request->all());
        // }

        try {
            // Basic validation
            if (!$this->validateBasicRequirements($request)) {
                $this->logEvent('Invalid request parameters', [
                    'headers' => $request->headers->all(),
                    'method' => $request->method()
                ]);
                return response()->json(['message' => 'Invalid request parameters'], 400);
            }

            $ipAddress = $request->ip();
            // if (app()->environment('local')) {
            //     $this->logEvent('IP Address', $ipAddress);
            // }

            // IP Validation
            if (!$this->validateIpAddress($ipAddress)) {
                $this->logEvent('IP Validation failed', ['ip' => $ipAddress]);
                return response()->json(['message' => 'Invalid or disallowed IP address'], 403);
            }

            // Bot Detection
            // if ($this->isBot($request)) {
            //     $this->logEvent('Bot detected', [
            //         'user_agent' => $request->header('User-Agent'),
            //         'ip' => $ipAddress
            //     ]);
            //     return response()->json(['message' => 'Bot traffic not allowed'], 403);
            // }

            // Rate Limiting
            if ($rateLimitResponse = $this->handleRateLimit($ipAddress)) {
                $this->logEvent('Rate limit exceeded', [
                    'ip' => $ipAddress,
                    'response' => $rateLimitResponse->getData()
                ]);
                return $rateLimitResponse;
            }

            // Request Validation
            if (!$this->validateRequest($request)) {
                $this->logEvent('Invalid request format or size', [
                    'content_length' => $request->header('Content-Length'),
                    'is_json' => $request->isJson(),
                    'has_event' => $request->has('event')
                ]);
                return response()->json(['message' => 'Invalid request format or size'], 400);
            }

            // Process Analytics Data
            // $this->logEvent('Processing analytics data', $request->input('event'));
            return $this->processAnalyticsData($request);

        } catch (\Exception $e) {
            $this->logEvent('Exception caught', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            report($e); // Log the error
            return response()->json(['message' => 'Internal server error'], 500);
        }
    }

    private function logEvent(string $message, $data = []): void
    {
        if (!app()->environment('local')) {
            return;
        }

        if ($this->eventLog) {
            $currentData = $this->eventLog->event_data ?? [];
            $currentData[] = [
                'timestamp' => now()->toIso8601String(),
                'message' => $message,
                'data' => $data
            ];

            $this->eventLog->update([
                'event_data' => $currentData
            ]);
        }

        if (app()->environment('local')) {
            // ds($message, $data)->toScreen('EventController');
        }
    }

    private function validateBasicRequirements(Request $request): bool
    {
        $result = $request->hasHeader('Accept')
            && $request->hasHeader('Content-Type')
            && in_array($request->method(), ['POST', 'OPTIONS']);

        // $this->logEvent('Basic requirements validation', [
        //     'has_accept' => $request->hasHeader('Accept'),
        //     'has_content_type' => $request->hasHeader('Content-Type'),
        //     'method' => $request->method(),
        //     'result' => $result
        // ]);

        return $result;
    }

    private function validateIpAddress(string $ipAddress): bool
    {
        if (!filter_var($ipAddress, FILTER_VALIDATE_IP)) {
            $this->logEvent('Invalid IP format', $ipAddress);
            return false;
        }

        // Allow localhost/private IPs in testing environment
        if (app()->environment('testing', 'local')) {
            $this->logEvent('Local environment - allowing private IP', $ipAddress);
            return true;
        }

        $privateIpRanges = [
            '127.0.0.0/8', '10.0.0.0/8', '172.16.0.0/12',
            '192.168.0.0/16', '169.254.0.0/16', 'fc00::/7'
        ];

        $isPrivateIp = IpUtils::checkIp($ipAddress, $privateIpRanges);

        if ($isPrivateIp) {
            $this->logEvent('Private IP address detected', [
                'ip' => $ipAddress,
                'matched_range' => collect($privateIpRanges)->first(fn($range) =>
                    IpUtils::checkIp($ipAddress, $range)
                )
            ]);
        }

        return !$isPrivateIp;
    }

    private function isBot(Request $request): bool
    {
        $userAgent = $request->header('User-Agent');
        $isEmptyUserAgent = empty($userAgent);
        $isBot = false;
        $matchedBot = null;

        if (!$isEmptyUserAgent) {
            $botPatterns = [
                'bot', 'crawler', 'spider', 'slurp', 'yahoo', 'bingbot', 'googlebot',
                'headless', 'phantom', 'selenium', 'wget', 'curl', 'python-requests',
                'scrapy', 'puppeteer', 'chrome-lighthouse', 'ahrefsbot', 'semrushbot',
                'yandexbot', 'mj12bot', 'dotbot', 'rogerbot', 'exabot', 'screaming frog',
                'pingdom', 'uptimerobot', 'monitoring', 'health check', 'statuspage',
                'newrelic', 'datadog'
            ];

            foreach ($botPatterns as $pattern) {
                if (stripos($userAgent, $pattern) !== false) {
                    $isBot = true;
                    $matchedBot = $pattern;
                    break;
                }
            }
        }

        // if ($isEmptyUserAgent || $isBot || app()->environment('local')) {
        //     $this->logEvent('Bot detection', [
        //         'method' => 'isBot',
        //         'user_agent' => $userAgent,
        //         'is_empty' => $isEmptyUserAgent,
        //         'pattern_match' => $isBot,
        //         'matched_bot' => $matchedBot,
        //         'result' => $isEmptyUserAgent || $isBot
        //     ]);
        // }

        return $isEmptyUserAgent || $isBot;
    }

    private function handleRateLimit(string $ipAddress)
    {
        $key = "analytics_rate_limit:{$ipAddress}";

        // $this->logEvent('Rate limit check', [
        //     'ip' => $ipAddress,
        //     'key' => $key,
        //     'attempts' => RateLimiter::attempts($key),
        //     'remaining' => RateLimiter::remaining($key, self::REQUESTS_PER_MINUTE)
        // ]);

        if (RateLimiter::tooManyAttempts($key, self::REQUESTS_PER_MINUTE)) {
            $backoffTime = RateLimiter::availableIn($key);

            return response()->json([
                'message' => 'Rate limit exceeded',
                'retry_after' => $backoffTime,
                'limit' => self::REQUESTS_PER_MINUTE,
                'reset' => now()->addSeconds($backoffTime)->timestamp
            ], 429)->header('Retry-After', $backoffTime);
        }

        RateLimiter::hit($key, 60);
        return null;
    }

    private function validateRequest(Request $request): bool
    {
        $isJson = $request->isJson();
        $hasEvent = $request->input('event');
        $contentLength = $request->header('Content-Length');
        $isValidSize = $contentLength <= self::MAX_REQUEST_SIZE;

        $isValid = $isJson && $hasEvent && $isValidSize;

        // $this->logEvent('Request validation', [
        //     'is_json' => $isJson,
        //     'has_event' => $hasEvent,
        //     'content_length' => $contentLength,
        //     'max_size' => self::MAX_REQUEST_SIZE,
        //     'is_valid_size' => $isValidSize,
        //     'result' => $isValid
        // ]);

        return $isValid;
    }

    private function processAnalyticsData(Request $request)
    {
        $data = array_filter($request->input('event'));
        $siteId = Arr::get($data, 'site_id');

        // if (app()->environment('local')) {
        //     $this->logEvent('Processing analytics data', [
        //         'filtered_data' => $data,
        //         'site_id' => $siteId
        //     ]);
        // }

        // Validate site_id is numeric and positive
        if (!$siteId || !is_string($siteId)) {
            $this->logEvent('Invalid site ID', ['site_id' => $siteId]);
            return response()->json(['message' => 'Valid site ID is required'], 400);
        }

        // Prevent duplicate requests
        $requestHash = $this->generateRequestHash($request, $siteId);
        // $this->logEvent('Request hash', [
        //     'hash' => $requestHash,
        //     'exists' => Cache::has("analytics_request:{$requestHash}")
        // ]);

        if (Cache::has("analytics_request:{$requestHash}")) {
            $this->logEvent('Request already processed', [
                'hash' => $requestHash
            ]);
            return response()->json(['message' => 'Request already processed'], 200);
        }

        try {
            DB::beginTransaction();

            $site = Site::findOrFail($siteId);
            // if (app()->environment('local')) {
            //     $this->logEvent('Site found', $site->toArray());
            // }

            $response = $this->runPipeline($data, $site);

            Cache::put(
                "analytics_request:{$requestHash}",
                true,
                now()->addHours(self::REQUEST_CACHE_TTL)
            );

            DB::commit();

            // if (app()->environment('local')) {
            //     $this->logEvent('Transaction committed successfully');
            // }

            // $this->eventLog->update(['status' => 'completed']);
            return $response;

        } catch (\Exception $e) {
            DB::rollBack();
            // $this->logEvent('Transaction rolled back', [
            //     'error' => $e->getMessage(),
            //     'trace' => $e->getTraceAsString()
            // ]);

            // $this->eventLog->update(['status' => 'failed']);
            report($e);
            return response()->json(['message' => 'Error processing analytics data'], 500);
        }
    }

    private function generateRequestHash(Request $request, string $siteId): string
    {
        $hashData = [
            'site_id' => $siteId,
            'data' => $request->all(),
            'ip' => $request->ip(),
            'userAgent' => $request->header('User-Agent'),
            'timestamp' => now()->startOfMinute()
        ];

        // if (app()->environment('local')) {
        //     $this->logEvent('Generating request hash', $hashData);
        // }

        return md5(json_encode($hashData));
    }

    private function runPipeline(array $data, Site $site)
    {
        // $this->logEvent('Starting pipeline', [
        //     'data' => $data,
        //     'site' => $site->toArray()
        // ]);

        return app(Pipeline::class)
            ->send([
                'data' => $data,
                'site' => $site,
                'eventLog' => $this->eventLog
            ])
            ->through([
                \Shaferllc\Analytics\Pipelines\CreatePage::class,
                \Shaferllc\Analytics\Pipelines\CreateVisitor::class,
                \Shaferllc\Analytics\Pipelines\HandleSessionEnd::class,
                \Shaferllc\Analytics\Pipelines\ProcessBrowserData::class,
                // \Shaferllc\Analytics\Pipelines\ProcessUserData::class,
                // \Shaferllc\Analytics\Pipelines\ProcessDeviceData::class,
                // \Shaferllc\Analytics\Pipelines\ProcessTrafficSourceData::class,
                // \Shaferllc\Analytics\Pipelines\ProcessPageData::class,
            ])
            ->then(function($result) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Event processed successfully'
                ]);
            });
    }
}
