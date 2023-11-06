<?php

namespace App\Services\Api;

use App\Services\Api\Exceptions\SkiClubException;
use Illuminate\Support\Arr;

class SkiClub extends CoreApi
{
    const API_URL = '';
    const TYPE_WEATHER = 'weather';
    const TYPE_SNOW_CONDITION = 'snow';

    private $exception = SkiClubException::class;

    /**
     * @return array
     */
    public function getWeather()
    {
        $data = $this->getResult(self::API_URL, [
            'resort_id' => $this->resort->id,
            'type'      => self::TYPE_WEATHER
        ]);

        $data = array_slice(Arr::get($data, 'data'), 0, 1);

        return $data;
    }

    /**
     * @return array
     */
    public function getSnowCondition()
    {
        $data = $this->getResult(self::API_URL, [
            'resort_id' => $this->resort->id,
            'type'      => self::TYPE_SNOW_CONDITION
        ]);

        return array_filter(Arr::get($data, 'data'), function ($value) {
            return $value !== null && $value !== 'No information';
        });
    }
}
