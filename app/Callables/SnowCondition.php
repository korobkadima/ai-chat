<?php

namespace App\Callables;

use App\Models\Resort;
use App\Services\Api\SkiClub;

#[Name('snow_condition')]
#[Description('Use this when the user asks to create summary for snow conditions for resort')]
class SnowCondition extends CallableFunction
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
            'snow' => $skiClubService->getSnowCondition()
        ];
    }
}
