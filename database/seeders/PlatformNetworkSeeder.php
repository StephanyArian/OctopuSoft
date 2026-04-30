<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PlataformaRed;

class PlatformNetworkSeeder extends Seeder
{
    public function run(): void
    {
        $platforms = [
            ['name' => 'LinkedIn', 'base_url' => 'https://linkedin.com/in/'],
            ['name' => 'GitHub', 'base_url' => 'https://github.com/'],
            ['name' => 'WhatsApp', 'base_url' => 'https://wa.me/'],
            ['name' => 'Email', 'base_url' => null],
            ['name' => 'Otros', 'base_url' => null],
        ];

        foreach ($platforms as $platform) {
            PlataformaRed::updateOrCreate(
                ['name' => $platform['name']], // evita duplicados
                ['base_url' => $platform['base_url']]
            );
        }
    }
}