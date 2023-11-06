<?php

namespace App\Services\Api\Transformers;

use Illuminate\Support\Arr;
use League\Fractal\TransformerAbstract;

class BenBusTransformer extends TransformerAbstract
{
    public function transform(array $transfer = []): array
    {
        return [
            'date' => Arr::get($transfer, 'inbound.depart.date'),
            'time' => Arr::get($transfer, 'inbound.depart.time'),
        ];
    }
}
