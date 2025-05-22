<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sample_categories = [
            [
                "id"          => 1,
                "name"        => 'Animals',
                "parent_id"   => null,
                "user_id"     => 1
            ],
            [
                "id"          => 2,
                "name"        => 'Birds',
                "parent_id"   => 1,
                "user_id"     => 1
            ],
            [
                "id"          => 3,
                "name"        => 'Farm Animals',
                "parent_id"   => 1,
                "user_id"     => 1
            ],
            [
                "id"          => 4,
                "name"        => 'Jobs',
                "parent_id"   => null,
                "user_id"     => 1
            ],
            [
                "id"          => 5,
                "name"        => 'Colors',
                "parent_id"   => null,
                "user_id"     => 1
            ],
            [
                "id"          => 6,
                "name"        => 'Transposrt',
                "parent_id"   => null,
                "user_id"     => 2
            ],
            [
                "id"          => 7,
                "name"        => 'Cars',
                "parent_id"   => 6,
                "user_id"     => 2
            ],
            [
                "id"          => 8,
                "name"        => 'public',
                "parent_id"   => 6,
                "user_id"     => 2
            ],
        ];

        $categories = Category::count();

        if($categories == 0) {
            foreach($sample_categories as $category){                
                DB::table('categories')->insert([
                    "id"          => $category['id'],
                    "name"        => $category['name'],
                    "parent_id"      => $category['parent_id'],
                    "user_id"     => $category['user_id']
                ]);
            }
        }
    }
}
