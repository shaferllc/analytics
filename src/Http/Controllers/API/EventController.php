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
use Shaferllc\Analytics\Models\PageVisitor;
use Symfony\Component\HttpFoundation\IpUtils;
use Illuminate\Support\Facades\Cache;

class EventController
{
    /**
     * The tracking mechanism.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        ray($request->all());
        $siteId = $request->input('site_id');
        if (!$siteId) {
            return response()->json(['message' => 'Site ID is required.'], 400);
        }

        // Generate a unique request ID based on the request data
        $requestHash = md5(json_encode([
            'site_id' => $siteId,
            'data' => $request->all(),
            'ip' => $request->ip(),
            'userAgent' => $request->header('User-Agent')
        ]));

        // Check if this exact request was already processed
        if (Cache::has("analytics_request:{$requestHash}")) {
            return response()->json(['message' => 'Request already processed'], 200);
        }

        ray($request->all());

        try {
            DB::beginTransaction();

            $site = Site::findOrFail($siteId);
            $data = $this->getVisitorData($request);

            $response = app(Pipeline::class)
                ->send(['data' => $data, 'site' => $site])
                ->through([
                    \Shaferllc\Analytics\Pipelines\CreatePage::class,
                    \Shaferllc\Analytics\Pipelines\UpdatePageMeta::class,
                    \Shaferllc\Analytics\Pipelines\CreateVisitor::class,
                    \Shaferllc\Analytics\Pipelines\HandleSessionStart::class,
                    \Shaferllc\Analytics\Pipelines\HandleSessionEnd::class,

                    // Process Data
                    \Shaferllc\Analytics\Pipelines\ProcessBrowserData::class,
                    \Shaferllc\Analytics\Pipelines\ProcessDeviceData::class,
                    \Shaferllc\Analytics\Pipelines\ProcessOutboundLinkClicks::class,
                    \Shaferllc\Analytics\Pipelines\ProcessPageData::class,
                    \Shaferllc\Analytics\Pipelines\ProcessPerformanceMetricsData::class,
                    \Shaferllc\Analytics\Pipelines\ProcessTrafficSourceData::class,
                    \Shaferllc\Analytics\Pipelines\ProcessUserInteractionData::class,
                    \Shaferllc\Analytics\Pipelines\ProcessViewportChanges::class,
                ])
                ->then(function ($result) {
                    return response(200);
                });

            // Store the request hash with a TTL (e.g., 24 hours)
            Cache::put("analytics_request:{$requestHash}", true, now()->addHours(24));

            DB::commit();
            return $response;

        } catch (\Exception $e) {
            DB::rollBack();
            // Log the error
            \Log::error('Analytics processing failed: ' . $e->getMessage(), [
                'request_hash' => $requestHash,
                'exception' => $e
            ]);
            return response()->json(['message' => 'Error processing analytics data'], 500);
        }
    }

    private function getPageUrl($page)
    {
        $url = $page['path'] ?? '/';
        if (isset($page['query']) && !empty($page['query'])) {
            parse_str($page['query'], $queryParams);
        }
        return mb_substr($url, 0, 255);
    }

    private function getVisitorData(Request $request): array
    {
        return array_merge(
            array_filter($request->all()),
            $request->has('ip') ? $this->getGeoData($request->input('ip')) : [],
            []
        );
    }

    private function getGeoData($ip)
    {
        try {
            $cacheKey = "geoip_data:{$ip}";
            return Cache::remember($cacheKey, now()->addHours(24), function () use ($ip) {
                $geoip = (new GeoIP(storage_path('app/geoip/GeoLite2-City.mmdb')))->city($ip);
                return [
                    'continent' => $geoip->continent->code . ':' . $geoip->continent->name,
                    'country' => $geoip->country->isoCode . ':' . $geoip->country->name,
                    'city' => $geoip->country->isoCode . ':' . $geoip->city->name . (isset($geoip->mostSpecificSubdivision->isoCode) ? ', ' . $geoip->mostSpecificSubdivision->isoCode : ''),
                ];
            });
        } catch (\Exception $e) {
            \Log::error('GeoIP lookup failed: ' . $e->getMessage(), ['ip' => $ip]);
            return [];
        }
    }
}
