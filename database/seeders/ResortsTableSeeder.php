<?php

namespace Database\Seeders;

use App\Models\Resort;
use Illuminate\Database\Seeder;

class ResortsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Resort::factory()->create([
            'name' => 'Alpe d\'Huez',
            'ben_bus_code' => 'ADH',
            'ski_club_id' => 106,
        ]);

        Resort::factory()->create([
            'name' => 'Val Thorens',
            'ben_bus_code' => 'VTH GR',
            'ski_club_id' => 159,
        ]);

        Resort::factory()->create([
            'name' => 'Les Menuires',
            'ben_bus_code' => 'MEN',
            'ski_club_id' => 135,
        ]);

        Resort::factory()->create([
            'name' => 'St Martin de Belleville',
            'ben_bus_code' => 'SMB',
            'ski_club_id' => 151,
        ]);

        Resort::factory()->create([
            'name' => 'Val d\'Isere',
            'ben_bus_code' => 'VDI GR',
            'ski_club_id' => 158,
        ]);

        Resort::factory()->create([
            'name' => 'Les Deux Alpes',
            'ben_bus_code' => 'LDA',
            'ski_club_id' => 132,
        ]);

        Resort::factory()->create([
            'name' => 'Tignes',
            'ben_bus_code' => 'TIG',
            'ski_club_id' => 156,
        ]);

        Resort::factory()->create([
            'name' => 'Les Arcs',
            'ben_bus_code' => 'ARC UCPA',
            'ski_club_id' => 129,
        ]);

        Resort::factory()->create([
            'name' => 'La Grave',
            'ben_bus_code' => 'LGR',
            'ski_club_id' => 122,
        ]);

        Resort::factory()->create([
            'name' => 'La Plagne',
            'ben_bus_code' => 'LPG BP',
            'ski_club_id' => 123,
        ]);

        Resort::factory()->create([
            'name' => 'Meribel',
            'ben_bus_code' => 'MER',
            'ski_club_id' => 139,
        ]);

        //TODO: add more resorts
    }
}
