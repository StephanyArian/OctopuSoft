<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TechnologySeeder extends Seeder
{
    public function run()
    {
        $technologies = [
            'PHP', 'Laravel', 'JavaScript', 'React', 'Vue.js', 
            'Node.js', 'Python', 'Django', 'MySQL', 'PostgreSQL',
            'MongoDB', 'Docker', 'Kubernetes', 'AWS', 'Git'
        ];

        foreach ($technologies as $technology) {
            DB::table('technologies')->insert([
                'name' => $technology,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}