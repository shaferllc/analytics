<?php

namespace Shaferllc\Analytics\Traits;

use Carbon\Carbon;

trait DateRangeTrait
{
    /**
     * Generate the range for dates.
     */
    public function range(?Carbon $from = null, ?Carbon $to = null): array
    {
        $to = $to ?: now();
        $from = $from ?: now()->subWeek();
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
            'from' => $from,
            'to' => $to,
            'from_old' => $from_old,
            'to_old' => $to_old,
            'unit' => $unit,
            'format' => $format,
        ];
    }

    /**
     * Determine the unit and format based on date range.
     */
    private function determineUnitAndFormat(Carbon $from, Carbon $to): array
    {
        $diffDays = $from->diffInDays($to);
        $diffMonths = $from->diffInMonths($to);
        $diffYears = $from->diffInYears($to);

        if ($diffDays < 1) {
            return ['hour', ''];
        }
        if ($diffMonths < 3) {
            return ['day', 'Y-m-d'];
        }
        if ($diffYears < 2) {
            return ['month', 'Y-m'];
        }

        return ['year', 'Y'];
    }

    /**
     * Set the time range based on shorthand format (e.g. 7d, 30d, 90d)
     */
    public function setTimeRange(string $range): void
    {
        $this->daterange = $range;

        if ($range === 'today') {
            $this->range(now()->startOfDay(), now());
            return;
        }

        // Extract number and unit from range string
        preg_match('/(\d+)([a-z])/', $range, $matches);

        if (count($matches) === 3) {
            $number = (int)$matches[1];
            $unit = $matches[2];

            $to = now();

            $from = match($unit) {
                'h' => now()->subHours($number),
                'd' => now()->subDays($number),
                'w' => now()->subWeeks($number),
                'm' => now()->subMonths($number),
                'y' => now()->subYears($number),
                default => now()->subDays($number)
            };

            $this->range($from, $to);
        }
    }
    /**
     * Calculate all the possible dates between two time frames.
     *
     * @param  mixed  $output
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
