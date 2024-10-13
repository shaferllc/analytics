<?php

/**
 * Format the page titles.
 *
 * @param null|array|string $value
 * @return string|null
 */
function formatTitle($value = null)
{
    return is_array($value) ? implode(" - ", $value) : $value;
}

/**
 * Format money.
 *
 * @param float $amount
 * @param string $currency
 * @param bool $separator
 * @param bool $translate
 * @return string
 */
function formatMoney($amount, $currency, $separator = true, $translate = true)
{
    $decimals = in_array(strtoupper($currency), config('currencies.zero_decimals')) ? 0 : 2;
    $decimalPoint = $translate ? __('.') : '.';
    $thousandsSeparator = $separator ? ($translate ? __(',') : ',') : '';
    
    return number_format($amount, $decimals, $decimalPoint, $thousandsSeparator);
}

/**
 * Get and format the Gravatar URL.
 *
 * @param string $email
 * @param int $size
 * @param string $default
 * @param string $rating
 * @return string
 */
function gravatar($email, $size = 80, $default = 'identicon', $rating = 'g')
{
    return sprintf(
        'https://www.gravatar.com/avatar/%s?s=%d&d=%s&r=%s',
        md5(strtolower(trim($email))),
        $size,
        $default,
        $rating
    );
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
        'Chrome' => 'chrome',
        'Chromium' => 'chromium',
        'Firefox' => 'firefox',
        'Firefox Mobile' => 'firefox',
        'Edge' => 'edge',
        'Internet Explorer' => 'ie',
        'Mobile Internet Explorer' => 'ie',
        'Vivaldi' => 'vivaldi',
        'Brave' => 'brave',
        'Safari' => 'safari',
        'Opera' => 'opera',
        'Opera Mini' => 'opera',
        'Opera Mobile' => 'opera',
        'Opera Touch' => 'operatouch',
        'Yandex Browser' => 'yandex',
        'UC Browser' => 'ucbrowser',
        'Samsung Internet' => 'samsung',
        'QQ Browser' => 'qq',
        'BlackBerry Browser' => 'bbbrowser',
        'Maxthon' => 'maxthon'
    ];

    return $browsers[$key] ?? 'unknown';
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
        'FreeBSD' => 'freebsd',
        'Android' => 'android',
        'Chrome OS' => 'chromeos',
        'BlackBerry OS' => 'bbos',
        'Tizen' => 'tizen',
        'KaiOS' => 'kaios',
        'BlackBerry Tablet OS' => 'bbos'
    ];

    return $operatingSystems[$key] ?? 'unknown';
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
        'desktop' => 'desktop',
        'mobile' => 'mobile',
        'tablet' => 'tablet',
        'television' => 'tv',
        'gaming' => 'gaming',
        'watch' => 'watch'
    ];

    return $devices[$key] ?? 'unknown';
}

/**
 * Format the flag icon
 *
 * @param string $value
 * @return string
 */
function formatFlag($value)
{
    $country = explode(':', $value);
    return !empty($country[0]) ? strtolower($country[0]) : 'unknown';
}

/**
 * Convert a number into a readable one.
 *
 * @param int $number The number to be transformed
 * @return string
 */
function shortenNumber($number)
{
    $suffix = ["", "K", "M", "B"];
    $precision = 1;
    
    for ($i = 0; $i < count($suffix); $i++) {
        $divide = $number / pow(1000, $i);
        if ($divide < 1000) {
            return round($divide, $precision) . $suffix[$i];
        }
    }

    return (string) $number;
}