<?php

namespace App\Services\Api;

use Carbon\Carbon;

class Booking
{
    /**
     * @param $resort
     * @param $date
     * @return string
     */
    public static function getLink($resort, $date)
    {
        return sprintf(
            'https://www.booking.com/searchresults.en-gb.html?ss=%s&checkin=%s&checkout=%s&group_adults=1&no_rooms=1&group_children=0',
            $resort,
            Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d'),
            Carbon::createFromFormat('d/m/Y', $date)->addDays(2)->format('Y-m-d'),
        );
    }
}
