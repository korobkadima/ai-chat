<?php

namespace App\Services\Api;

use Carbon\Carbon;

class AirBnb
{
    /**
     * @param $resort
     * @param $date
     * @return string
     */
    public static function getLink($resort, $date)
    {
        return sprintf(
            'https://www.airbnb.co.uk/s/%s/homes?tab_id=home_tab&checkin=%s&checkout=%s',
            $resort,
            Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d'),
            Carbon::createFromFormat('d/m/Y', $date)->addDays(2)->format('Y-m-d'),
        );
    }
}
