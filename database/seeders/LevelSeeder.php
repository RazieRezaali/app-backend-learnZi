<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class LevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $allLevels = [
            [
                "name"          => 'beginner',
                "HSK"           => 'HSK1',
                "CEFR"          => 'A1',
                "vocabulary"    => 150
            ],
            [
                "name"          => 'beginner',
                "HSK"           => 'HSK2',
                "CEFR"          => 'A1',
                "vocabulary"    => 300
            ],
            [
                "name"          => 'elementary',
                "HSK"           => 'HSK3',
                "CEFR"          => 'A2',
                "vocabulary"    => 600
            ],
            [
                "name"          => 'intermediate',
                "HSK"           => 'HSK4',
                "CEFR"          => 'B1',
                "vocabulary"    => 1200
            ],
            [
                "name"          => 'upper-intermediate',
                "HSK"           => 'HSK5',
                "CEFR"          => 'B2',
                "vocabulary"    => 2500
            ],
            [
                "name"          => 'advanced',
                "HSK"           => 'HSK6',
                "CEFR"          => 'C1',
                "vocabulary"    => 5000
            ],
        ];

        $levels = \App\Models\Level::count();

        if($levels == 0) {
            foreach($allLevels as $level){
                \Illuminate\Support\Facades\DB::table('levels')->insert([
                    'name'       => $level['name'],
                    'HSK'        => $level['HSK'],
                    'CEFR'       => $level['CEFR'], 
                    'vocabulary' => $level['vocabulary']
                ]);
            }
        }
    }
}
