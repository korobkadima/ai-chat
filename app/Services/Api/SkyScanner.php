<?php

namespace App\Services\Api;

use Carbon\Carbon;

class SkyScanner
{
    /**
     * @param $airportCodeFrom
     * @param $airportCodeTo
     * @param $date
     * @return string
     */
    public static function getLink($airportCodeFrom, $airportCodeTo, $date)
    {
        return sprintf(
            'https://www.skyscanner.net/transport/flights/%s/%s/%s/?adultsv2=1&cabinclass=economy&childrenv2=&inboundaltsenabled=false&outboundaltsenabled=false&preferdirects=false&ref=home&rtn=0',
            $airportCodeFrom,
            $airportCodeTo,
            Carbon::createFromFormat('d/m/Y', $date)->format('ymd')
        );
    }
}
