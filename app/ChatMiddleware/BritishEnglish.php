<?php

namespace App\ChatMiddleware;

use Illuminate\Support\Str;

class BritishEnglish extends ChatMiddleware
{
    protected static array $words = [
        'diaper'    => 'nappy',
        'crib'      => 'cot',
        'garbage'   => 'rubbish',
        'trash'     => 'rubbish',
        'rest room' => 'toilet',
        'stroller'  => 'buggy',

        'color'      => 'colour',
        'centre'     => 'center',
        'analyze'    => 'analyse',
        'specialty'  => 'speciality',
        'pajamas'    => 'pyjamas',
        'mold'       => 'mould',
        'flavor'     => 'flavor',
        'moisturize' => 'moisturise',
    ];

    public static function handle(string &$content): string
    {
        $american = array_keys(self::$words);
        $british  = array_values(self::$words);

        $americanPlurals = collect($american)->map(function ($word) {
            return Str::plural($word);
        })->toArray();

        $britishPlurals = collect($british)->map(function ($word) {
            return Str::plural($word);
        })->toArray();

        $content = str_replace($americanPlurals, $britishPlurals, $content);

        return $content = str_replace($american, $british, $content);
    }
}
