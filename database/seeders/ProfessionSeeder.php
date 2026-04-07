<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProfessionSeeder extends Seeder
{
    public function run()
    {
        $professions = [
            'Software Engineer',
            'Frontend Developer',
            'Backend Developer',
            'Full Stack Developer',
            'UI/UX Designer',
            'DevOps Engineer',
            'Data Scientist',
            'Product Manager',
            'Project Manager',
            'QA Engineer',
        ];

        foreach ($professions as $profession) {
            DB::table('professions')->insert([
                'name' => $profession,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}