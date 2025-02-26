<?php

use Illuminate\Support\Str;

if (!function_exists('calcGrowth')) {
    function calcGrowth($current, $previous)
    {
        if ($previous == 0) {
            return 0;
        }
        return ($current - $previous) / $previous * 100;
    }
}

/**
 * Format the flag icon
 *
 * @param string $value
 * @return string
 */
function formatFlag($value)
{
    return isset($value) ? strtolower($value) : 'unknown';
}

/**
 * Format the devices icon
 *
 * @param string $key
 * @return string
 */
function formatDevice($key)
{
    $key = Str::slug($key);
    $devices = [
        'desktop' => 'desktop',
        'mobile-phone' => 'mobile',
        'mobile-device' => 'mobile',
        'mobile' => 'mobile-screen',
        'tablet' => 'tablet',
        'television' => 'tv',
        'watch' => 'clock',
        'vr-headset' => 'vr-cardboard',
        'unknown' => 'unknown',
        'gaming-console' => 'gamepad',
        'smart-tv' => 'tv',
        'wearable' => 'watch',
        'desktop-browser' => 'desktop',
        'mobile-browser' => 'mobile-screen',
        'tablet-browser' => 'tablet',
        'botcrawler' => 'robot',
        'bot' => 'robot',
        'crawler' => 'robot',
        'spider' => 'spider',
        'slurp' => 'robot',
        'googlebot' => 'robot',
        'bingbot' => 'robot',
    ];

    return $devices[$key] ?? 'question';
}


/**
 * Format the browser icon
 *
 * @param string $key
 * @return string
 */
function formatBrowser($key)
{
    $browsers = [
        '360 Secure Browser' => '360secure',
        'Aloha Browser' => 'aloha',
        'Android Browser' => 'android',
        'Apple Safari' => 'safari',
        'Avant Browser' => 'avant',
        'Avast Secure Browser' => 'avast',
        'Baidu Browser' => 'baidu',
        'Basilisk' => 'basilisk',
        'BlackBerry Browser' => 'bbbrowser',
        'Brave' => 'brave',
        'Brave Browser' => 'brave',
        'Bromite' => 'bromite',
        'Cent Browser' => 'cent',
        'Chrome' => 'chrome',
        'Chromium' => 'chrome',
        'CM Browser' => 'cm',
        'Coc Coc' => 'coccoc',
        'Colibri' => 'colibri',
        'Comodo Dragon' => 'comodo',
        'Dolphin' => 'dolphin',
        'Dooble' => 'dooble',
        'DuckDuckGo' => 'duckduckgo',
        'Edge' => 'edge',
        'Ecosia' => 'ecosia',
        'Epiphany' => 'epiphany',
        'Falkon' => 'falkon',
        'Fennec F-Droid' => 'fennec',
        'Firefox' => 'firefox',
        'Firefox Mobile' => 'firefox',
        'Ghostery Dawn' => 'ghostery',
        'Google Chrome' => 'chrome',
        'IceCat' => 'icecat',
        'Internet Explorer' => 'ie',
        'Iridium Browser' => 'iridium',
        'K-Meleon' => 'kmeleon',
        'Kiwi Browser' => 'kiwi',
        'Konqueror' => 'konqueror',
        'Links' => 'links',
        'Lunascape' => 'lunascape',
        'Lynx' => 'lynx',
        'Maxthon' => 'maxthon',
        'Microsoft Edge' => 'edge',
        'Midori' => 'midori',
        'Mobile Internet Explorer' => 'ie',
        'Mozilla' => 'firefox',
        'Mozilla Firefox' => 'firefox',
        'Mozilla Firefox Mobile' => 'firefox',
        'Naver Whale' => 'whale',
        'Opera' => 'opera',
        'Opera GX' => 'opera',
        'Opera Mini' => 'opera',
        'Opera Mobile' => 'opera',
        'Opera Touch' => 'operatouch',
        'Orfox' => 'orfox',
        'Otter' => 'otter',
        'Pale Moon' => 'palemoon',
        'Phoenix Browser' => 'phoenix',
        'Puffin' => 'puffin',
        'QQ Browser' => 'qq',
        'Qwant' => 'qwant',
        'Safari' => 'safari',
        'Samsung Internet' => 'samsung',
        'SamsungBrowser' => 'samsung',
        'Seamonkey' => 'seamonkey',
        'Instagram' => 'instagram',
        'Silk' => 'silk',
        'Sleipnir' => 'sleipnir',
        'Slimjet' => 'slimjet',
        'Sogou Explorer' => 'sogou',
        'SRWare Iron' => 'iron',
        'Tor Browser' => 'tor',
        'Torch Browser' => 'torch',
        'UC Browser' => 'ucbrowser',
        'Ungoogled Chromium' => 'ungoogled',
        'Vivaldi' => 'vivaldi',
        'w3m' => 'w3m',
        'Waterfox' => 'waterfox',
        'Whale Browser' => 'whale',
        'Yandex Browser' => 'yandex',
        'Yandex Browser Lite' => 'yandex'
    ];

    $lowercaseKey = Str::lower($key);
    foreach ($browsers as $browserName => $icon) {

        if (Str::contains($lowercaseKey, Str::lower($browserName))) {
            return $icon;
        }
    }
    return 'unknown';
}



/**
 * Format the operating system icon
 *
 * @param string $key
 * @return string
 */
function formatOperatingSystem($key)
{
    $operatingSystems = [
        'Linux x86_64' => 'linux',
        'Linux i686' => 'linux',
        'Linux aarch64' => 'linux',
        'Linux armv7l' => 'linux',
        'Linux armv8l' => 'linux',
        'Linux armv9l' => 'linux',
        'Linux arm64' => 'linux',
        'MacIntel' => 'apple',
        'MacPPC' => 'apple',
        'MacARM' => 'apple',
        'MacIntel64' => 'apple',
        'MacPPC64' => 'apple',
        'MacARM64' => 'apple',
        'Win32' => 'windows',
        'Win64' => 'windows',
        'Windows' => 'windows',
        'Linux' => 'linux',
        'Ubuntu' => 'ubuntu',
        'Windows Phone' => 'windows',
        'iOS' => 'apple',
        'OS X' => 'apple',
        'macOS' => 'apple',
        'FreeBSD' => 'freebsd',
        'OpenBSD' => 'openbsd',
        'NetBSD' => 'netbsd',
        'Android' => 'android',
        'Chrome OS' => 'chromeos',
        'BlackBerry OS' => 'bbos',
        'Tizen' => 'tizen',
        'KaiOS' => 'kaios',
        'BlackBerry Tablet OS' => 'bbos',
        'Fedora' => 'fedora',
        'Debian' => 'debian',
        'Red Hat' => 'redhat',
        'CentOS' => 'centos',
        'Arch Linux' => 'archlinux',
        'Manjaro' => 'manjaro',
        'Mint' => 'mint',
        'Solaris' => 'solaris',
        'ReactOS' => 'reactos',
        'Haiku' => 'haiku',
        'Elementary OS' => 'elementary',
        'Zorin OS' => 'zorin',
        'Kali Linux' => 'kali',
        'Raspbian' => 'raspbian'
    ];

    $lowercaseKey = Str::lower($key);
    foreach ($operatingSystems as $osName => $icon) {
        if (Str::contains($lowercaseKey, Str::lower($osName))) {
            return $icon;
        }
    }
    return 'unknown';
}

/**
 * Translate the operating system
 *
 * @param string $key
 * @return string
 */
function translateAnalyticData($key)
{
   return match($key) {
        'macintel' => 'macOS',
        'macppc' => 'macOS',
        'macarm' => 'macOS',
        'macintel64' => 'macOS',
        'macppc64' => 'macOS',
        'macarm64' => 'macOS',

        'win32' => 'Windows 32-bit',
        'win64' => 'Windows 64-bit',

        'linux' => 'Linux',
        'ubuntu' => 'Ubuntu',
        'windows' => 'Windows',
        'ios' => 'iOS',
        'macos' => 'macOS',
        'freebsd' => 'FreeBSD',
        'openbsd' => 'OpenBSD',
        'netbsd' => 'NetBSD',
        'android' => 'Android',
        'chromeos' => 'Chrome OS',
        'bbos' => 'BlackBerry OS',
        'tizen' => 'Tizen',
        'kaios' => 'KaiOS',
        'linux x86_64' => 'Linux',
        'linux aarch64' => 'Linux',
        'linux armv7l' => 'Linux',
        'linux armv8l' => 'Linux',
        'linux armv9l' => 'Linux',

        'desktop' => 'Desktop',
        'mobile' => 'Mobile',
        'tablet' => 'Tablet',
        'television' => 'Television',
        'watch' => 'Watch',

        'chrome' => 'Chrome',
        'chromium' => 'Chromium',
        'cm' => 'CM Browser',
        'coccoc' => 'Coc Coc',


        default => Str::title($key),
    };
}


function bots()
{
    return [
        'Googlebot' => 'Googlebot',
        'Bingbot' => 'Bingbot',
        'Baiduspider' => 'Baiduspider',
        'YandexBot' => 'YandexBot',
        'Sogou' => 'Sogou',
        'Exabot' => 'Exabot',
        'facebookexternalhit' => 'Facebook',
        'Twitterbot' => 'Twitter',
        'Slackbot' => 'Slack',
        'LinkedInBot' => 'LinkedIn',
        'Applebot' => 'Apple',
        'DuckDuckBot' => 'DuckDuckGo',
        'Pinterestbot' => 'Pinterest',
        'AhrefsBot' => 'Ahrefs',
        'SemrushBot' => 'Semrush',
        'MJ12bot' => 'MJ12',
        'DotBot' => 'DotBot',
        'SeznamBot' => 'Seznam',
        'PetalBot' => 'Petal',
        'CCBot' => 'Common Crawl',
        'Neevabot' => 'Neeva',
        'Qwantify' => 'Qwant',
        'Bytespider' => 'ByteDance',
        'ZoominfoBot' => 'ZoomInfo',
        'Yahoo! Slurp' => 'Yahoo',
        'AlexaBot' => 'Alexa',
        'ArchiveBot' => 'Archive.org',
        'Barkrowler' => 'Barkrowler',
        'BLEXBot' => 'BLEXBot',
        'DataForSeoBot' => 'DataForSeo',
        'DomainCrawler' => 'DomainCrawler',
        'DuckDuckGo-Favicons-Bot' => 'DuckDuckGo Favicons',
        'Ezooms' => 'Ezooms',
        'Gigabot' => 'Gigabot',
        'HubSpot Crawler' => 'HubSpot',
        'MauiBot' => 'MauiBot',
        'MojeekBot' => 'Mojeek',
        'NetcraftSurveyAgent' => 'Netcraft',
        'OrangeBot' => 'Orange',
        'PaperLiBot' => 'Paper.li',
        'Pleroma' => 'Pleroma',
        'Riddler' => 'Riddler',
        'Screaming Frog SEO Spider' => 'Screaming Frog',
        'Siteimprove' => 'Siteimprove',
        'SputnikBot' => 'Sputnik',
        'SurveyBot' => 'SurveyBot',
        'TurnitinBot' => 'Turnitin',
        'Uptimebot' => 'Uptimebot',
        'Webmeup-Crawler' => 'Webmeup',
        'Yeti' => 'Naver',
        'ZumBot' => 'ZumBot',
        'BingPreview' => 'Bing Preview',
        'Google-InspectionTool' => 'Google Inspection',
        'Google-Read-Aloud' => 'Google Read Aloud',
        'Google-Site-Verification' => 'Google Verification',
        'Google-Adwords-Instant' => 'Google Adwords',
        'Google-Apps-Script' => 'Google Apps',
        'Google-Cloud-Scheduler' => 'Google Cloud',
        'Google-Cloud-Tasks' => 'Google Cloud Tasks',
        'Google-Cloud-Functions' => 'Google Cloud Functions',
        'Google-Cloud-Run' => 'Google Cloud Run',
        'Google-Cloud-Shell' => 'Google Cloud Shell',
        'Google-Cloud-Dataflow' => 'Google Dataflow',
        'Google-Cloud-Dataproc' => 'Google Dataproc',
        'Google-Cloud-Composer' => 'Google Composer',
        'Google-Cloud-Bigtable' => 'Google Bigtable',
        'Google-Cloud-Spanner' => 'Google Spanner',
        'Google-Cloud-Firestore' => 'Google Firestore',
        'Google-Cloud-Memorystore' => 'Google Memorystore',
        'Google-Cloud-SQL' => 'Google Cloud SQL',
        'Google-Cloud-Storage' => 'Google Cloud Storage',
        'Google-Cloud-Pub/Sub' => 'Google Pub/Sub',
    ];
}
