<?php

namespace App\Callables;

use App\Models\Resort;
use App\Services\Api\SkiClub;

#[Name('weather_condition')]
#[Description('Use this when the user asks to create summary for weather for resort')]
class WeatherCondition extends CallableFunction
{
    #[Description('The name of the resort')]
    #[Validation('string')]
    public string $resort;

    public function handle(): array
    {
        $resort = Resort::where('name' , '=', $this->resort)->first();

        $skiClubService = new SkiClub();
        $skiClubService->setResort($resort);

        return [
            'weather' => $skiClubService->getWeather()
        ];
    }
}
