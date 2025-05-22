<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\CardSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\LevelSeeder;
use Database\Seeders\CountrySeeder;
use Database\Seeders\CategorySeeder;
use Database\Seeders\CharacterSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([CountrySeeder::class]);
        $this->call([LevelSeeder::class]);
        $this->call([UserSeeder::class]);

        $charactersCount = \App\Models\Character::count();
        if($charactersCount == 0){
            $this->call([CharacterSeeder::class]);
        }

        $this->call([CategorySeeder::class]);
        $this->call([CardSeeder::class]);
    }
}
