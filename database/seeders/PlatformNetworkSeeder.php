<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlatformNetworkSeeder extends Seeder
{
    public function run()
    {
        $platforms = [
            ['name' => 'LinkedIn', 'base_url' => 'https://linkedin.com/in/'],
            ['name' => 'GitHub', 'base_url' => 'https://github.com/'],
            ['name' => 'Twitter', 'base_url' => 'https://twitter.com/'],
            ['name' => 'Portfolio', 'base_url' => null],
            ['name' => 'Medium', 'base_url' => 'https://medium.com/@'],
            ['name' => 'Dev.to', 'base_url' => 'https://dev.to/'],
        ];

        foreach ($platforms as $platform) {
            DB::table('platform_network')->insert([
                'name' => $platform['name'],
                'base_url' => $platform['base_url'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}