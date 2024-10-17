<?php

namespace ShaferLLC\Analytics\Traits;

use Carbon\Carbon;

trait DateRangeTrait
{
    /**
     * Generate the range for dates.
     *
     * @param Carbon|null $from
     * @param Carbon|null $to
     * @return array
     */
    private function range(Carbon $from = null, Carbon $to = null): array
    {
        $to = $this->parseDate(request()->input('to'), $to ?? Carbon::now());
        $from = $this->parseDate(request()->input('from'), $from ?? $to);

        [$unit, $format] = $this->determineUnitAndFormat($from, $to);

        // Reset the date range if it exceeds the limits
        if ($from->diffInYears($to) >= 100) {
            $to = Carbon::now();
            $from = $to;
        }

        // Get the old period date range
        $to_old = (clone $from)->subDay();
        $from_old = (clone $to_old)->subDays($from->diffInDays($to));

        return [
            'from' => $from->format('Y-m-d'),
            'to' => $to->format('Y-m-d'),
            'from_old' => $from_old->format('Y-m-d'),
            'to_old' => $to_old->format('Y-m-d'),
            'unit' => $unit,
            'format' => $format
        ];
    }

    /**
     * Parse date from input or fallback to default.
     *
     * @param string|null $input
     * @param Carbon $default
     * @return Carbon
     */
    private function parseDate(?string $input, Carbon $default): Carbon
    {
        if ($input) {
            try {
                return Carbon::createFromFormat('Y-m-d', $input);
            } catch (\Exception $e) {
                // Silently fall back to default on parse error
            }
        }
        return $default;
    }

    /**
     * Determine the unit and format based on date range.
     *
     * @param Carbon $from
     * @param Carbon $to
     * @return array
     */
    private function determineUnitAndFormat(Carbon $from, Carbon $to): array
    {
        $diffDays = $from->diffInDays($to);
        $diffMonths = $from->diffInMonths($to);
        $diffYears = $from->diffInYears($to);

        if ($diffDays < 1) return ['hour', ''];
        if ($diffMonths < 3) return ['day', 'Y-m-d'];
        if ($diffYears < 2) return ['month', 'Y-m'];
        return ['year', 'Y'];
    }

    /**
     * Calculate all the possible dates between two time frames.
     *
     * @param string $from
     * @param string $to
     * @param string $unit
     * @param string $format
     * @param mixed $output
     * @return array
     */
    private function calcAllDates(string $from, string $to, string $unit, string $format, $output = 0): array
    {
        $from = Carbon::createFromFormat($format, $from);
        $to = Carbon::createFromFormat($format, $to);

        $possibleDateResults = [$from->format($format) => $output];

        while ($from->lt($to)) {
            $from = $this->incrementDate($from, $unit);

            if ($from->lte($to)) {
                $possibleDateResults[$from->format($format)] = $output;
            }
        }

        return $possibleDateResults;
    }

    /**
     * Increment date based on unit.
     *
     * @param Carbon $date
     * @param string $unit
     * @return Carbon
     */
    private function incrementDate(Carbon $date, string $unit): Carbon
    {
        switch ($unit) {
            case 'year':
                return $date->addYear();
            case 'month':
                return $date->addMonth();
            case 'day':
                return $date->addDay();
            case 'hour':
                return $date->addHour();
            case 'second':
                return $date->addSecond();
            default:
                return $date;
        }
    }
}
