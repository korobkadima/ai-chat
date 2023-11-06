<?php

namespace App\Services\Api;

use App\Services\Api\Exceptions\BenBusException;
use App\Services\Api\Transformers\BenBusTransformer;
use Carbon\Carbon;
use Illuminate\Support\Arr;

class BenBus extends CoreApi
{
    const API_URL = '';

    private $exception = BenBusException::class;

    /**
     * @return array
     */
    public function getTransfers($airportCode, $resortCode, $date)
    {
        $this->setApiKey(env('BENBUS_API_KEY'));

        $data = $this->getResult(self::API_URL, [
            'no_of_passengers' => 1,
            'inbound_airport'  => $airportCode,
            'inbound_resort'   => $resortCode,
            'inbound_date'     => Carbon::createFromFormat('d/m/Y', $date)->format('Ymd')
        ]);

        if (isset($data['data'][0]['inbound']['message'])) {
            return [];
        }

        return fractal()
            ->collection(Arr::get($data, 'data'))
            ->transformWith(BenBusTransformer::class)
            ->toArray();
    }
}
