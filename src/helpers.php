<?php

if (!function_exists('calcGrowth')) {
    function calcGrowth($current, $previous)
    {   
        if ($previous == 0) {
            return 0;
        }
        return ($current - $previous) / $previous * 100;
    }
}
