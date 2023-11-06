<?php

namespace App\Callables;

#[Name('current_year')]
#[Description('Gets the current year')]
class CurrentYear extends CallableFunction
{
    public function handle(): array
    {
        return [
            'year' => date('Y'),
        ];
    }
}
