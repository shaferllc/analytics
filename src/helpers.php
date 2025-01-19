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
    $devices = [
        'desktop' => 'heroicon-o-computer-desktop',
        'mobile' => 'heroicon-o-device-phone-mobile',
        'tablet' => 'heroicon-o-device-tablet',
        'television' => 'heroicon-o-tv',
        'watch' => 'heroicon-o-watch'
    ];

    return $devices[$key] ?? 'heroicon-o-question-mark-circle';
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
        'Chromium' => 'chromium',
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
        'Seamonkey' => 'seamonkey',
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
