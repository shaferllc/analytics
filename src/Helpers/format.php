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