<?php

namespace Shaferllc\Analytics\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\Attributes\Locked;
use App\Models\Site;
use Shaferllc\Analytics\Traits\ComponentTrait;
use Shaferllc\Analytics\Traits\DateRangeTrait;

#[Title('User Agents')]
class UserAgents extends Component
{
    use DateRangeTrait, WithPagination, ComponentTrait;

    #[Locked]
    public Site $site;
    public ?string $name = null;
    public ?string $value = null;

    public function mount(Site $site, $value = null): void
    {
        $this->site = $site;
        $this->value = $value;
    }

    public function render()
    {
        $data = $this->query(
            category: 'technical',
            type: 'user_agent',
            from: $this->from,
            value: str_replace('-', ' ', $this->value),
            to: $this->to
        );

        // Transform paginated data to extract browser details
        $transformedData = $data['data']->through(function ($item) {
            $userAgent = $item->value;

            // Extract browser details using regex patterns
            preg_match('/(Chrome|Safari|Firefox|Edge|MSIE|Opera|Trident|OPR|SamsungBrowser|UCBrowser|YaBrowser|Vivaldi|Brave|Instagram|Facebook|WhatsApp|Twitter|LinkedIn|Snapchat|TikTok)\/?((?:[0-9]+\.?)+)/', $userAgent, $browserMatches);
            preg_match('/(Windows NT|Macintosh|Linux|Android|iPhone|iPad|iPod|CrOS|Xbox|PlayStation|Nintendo|BlackBerry|webOS|Windows Phone|Symbian|Ubuntu|Debian|Fedora|Red Hat|CentOS)\s?([0-9\._]+)?/', $userAgent, $osMatches);
            preg_match('/(Mobile|Tablet|Desktop|TV|Console|Wearable|IoT)/', $userAgent, $deviceMatches);
            preg_match('/(x86_64|x86|arm64|armv[0-9]+|aarch64|powerpc|sparc|mips|amd64|i[3-6]86|win32|win64|x64|ia32|ia64)/', $userAgent, $architectureMatches);
            preg_match('/(KHTML|Gecko|Presto|Trident|WebKit|Blink|EdgeHTML)/', $userAgent, $engineMatches);
            preg_match('/(Chrome|Firefox|Safari|Edge|Opera|IE|Instagram|Facebook|WhatsApp|Twitter|LinkedIn|Snapchat|TikTok)\/([\d\.]+)/', $userAgent, $versionMatches);
            preg_match('/(1\d{3}x\d{3}|\d{3,4}x\d{3,4})/', $userAgent, $resolutionMatches);

            // Extract iOS specific details
            preg_match('/CPU.*OS\s+(\d+[._]\d+[._]?\d*)/', $userAgent, $iosVersionMatches);
            preg_match('/iPhone(\d+,\d+)/', $userAgent, $iphoneModelMatches);

            // Extract WebKit version
            preg_match('/AppleWebKit\/(\d+\.\d+)/', $userAgent, $webkitVersionMatches);

            // Extract scale factor
            preg_match('/scale=(\d+\.\d+)/', $userAgent, $scaleMatches);

            // Extract build number
            preg_match('/Mobile\/(\w+)/', $userAgent, $buildMatches);

            // Extract social media app details with more specifics
            preg_match('/(Instagram|Facebook|WhatsApp|Twitter|LinkedIn|Snapchat|TikTok)\s+([\d\.]+\.?\d*\.?\d*\.?\d*)/', $userAgent, $appMatches);

            $browser = $browserMatches[1] ?? 'Unknown';
            $browserVersion = $browserMatches[2] ?? '';
            $os = $osMatches[1] ?? 'Unknown';
            $osVersion = $osMatches[2] ?? '';
            $device = $deviceMatches[1] ?? 'Unknown';
            $architecture = $architectureMatches[1] ?? 'Unknown';
            $engine = $engineMatches[1] ?? 'Unknown';
            $resolution = $resolutionMatches[1] ?? 'Unknown';
            $app = $appMatches[1] ?? null;
            $appVersion = $appMatches[2] ?? null;

            // Additional extracted details
            $iosVersion = str_replace('_', '.', $iosVersionMatches[1] ?? '');
            $iphoneModel = $iphoneModelMatches[1] ?? null;
            $webkitVersion = $webkitVersionMatches[1] ?? null;
            $scaleFactor = $scaleMatches[1] ?? null;
            $buildNumber = $buildMatches[1] ?? null;

            // Handle special cases and normalize data
            if ($browser === 'Trident' || $browser === 'MSIE') {
                $browser = 'Internet Explorer';
            } elseif ($browser === 'OPR') {
                $browser = 'Opera';
            } elseif (strpos($userAgent, 'Brave') !== false) {
                $browser = 'Brave';
            }

            // If it's a social media app, set that as the browser
            if ($app) {
                $browser = $app;
                $browserVersion = $appVersion;
            }

            if ($os === 'iPhone' || $os === 'iPad' || $os === 'iPod') {
                $os = 'iOS';
                $osVersion = $iosVersion;
            } elseif (strpos($os, 'Windows NT') !== false) {
                $windowsVersions = [
                    '10.0' => 'Windows 10/11',
                    '6.3' => 'Windows 8.1',
                    '6.2' => 'Windows 8',
                    '6.1' => 'Windows 7',
                    '6.0' => 'Windows Vista',
                    '5.2' => 'Windows XP x64',
                    '5.1' => 'Windows XP',
                ];
                $os = $windowsVersions[$osVersion] ?? 'Windows';
            }

            // Enhanced device detection
            if (preg_match('/(iPhone|Android.*Mobile|Mobile.*Firefox|Opera Mobi)/', $userAgent)) {
                $device = 'Mobile';
            } elseif (preg_match('/(iPad|Android(?!.*Mobile)|Tablet)/', $userAgent)) {
                $device = 'Tablet';
            } elseif (preg_match('/(TV|SmartTV|WebTV|HbbTV)/', $userAgent)) {
                $device = 'TV';
            } elseif (preg_match('/(Xbox|PlayStation|Nintendo|OUYA)/', $userAgent)) {
                $device = 'Console';
            } elseif (preg_match('/(Glass|Watch|Gear)/', $userAgent)) {
                $device = 'Wearable';
            } else {
                $device = 'Desktop';
            }

            $item->parsed = [
                'browser' => $browser,
                'browser_version' => $browserVersion,
                'operating_system' => $os,
                'os_version' => $osVersion,
                'device_type' => $device,
                'architecture' => $architecture,
                'engine' => $engine,
                'screen_resolution' => $resolution,
                'is_mobile' => $device === 'Mobile',
                'is_tablet' => $device === 'Tablet',
                'is_desktop' => $device === 'Desktop',
                'is_tv' => $device === 'TV',
                'is_console' => $device === 'Console',
                'is_wearable' => $device === 'Wearable',
                'is_social_app' => $app !== null,
                'social_app' => $app,
                'social_app_version' => $appVersion,
                'is_bot' => (bool)preg_match('/(bot|crawler|spider|slurp|googlebot|bingbot|yahoo|baidu|yandex|duckduckbot)/i', $userAgent),
                'is_secure' => strpos($userAgent, 'https') !== false,
                'supports_javascript' => !preg_match('/(Lynx|Links|w3m|WebbIE)/i', $userAgent),
                'supports_cookies' => !preg_match('/(CookieBlock|Cookie ?Killer)/i', $userAgent),
                'language' => $this->extractLanguage($userAgent),
                'raw' => $userAgent,
                'webkit_version' => $webkitVersion,
                'scale_factor' => $scaleFactor,
                'build_number' => $buildNumber,
                'device_model' => $iphoneModel
            ];

            return $item;
        });

        // Update the data array with transformed results
        $data['data'] = $transformedData;

        return view('analytics::livewire.user-agents', [
            'data' => $data['data'],
            'first' => $data['first'],
            'last' => $data['last'],
            'total' => $data['total'],
            'aggregates' => $data['aggregates'],
            'range' => $this->range,
        ]);
    }

    /**
     * Extract language from user agent string
     *
     * @param string $userAgent
     * @return string|null
     */
    private function extractLanguage($userAgent)
    {
        // Match language codes like en-US, fr-FR, etc.
        if (preg_match('/[a-z]{2}[-_][A-Z]{2}/', $userAgent, $matches)) {
            return $matches[0];
        }

        // Match simple language codes like en, fr, etc.
        if (preg_match('/;\s*([a-z]{2})\s*[;)]/', $userAgent, $matches)) {
            return $matches[1];
        }

        return null;
    }
}
