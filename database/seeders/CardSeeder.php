<?php

namespace Database\Seeders;

use App\Models\Card;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sample_cards = [
            [   
                "id"            => 1,
                "character_id"  => 71,
                "category_id"   => 1,
            ],
            [
                "id"            => 2,
                "character_id"  => 1391,
                "category_id"   => 2,
            ],
            [
                "id"            => 3,
                "character_id"  => 4097,
                "category_id"   => 2,
            ],
            [
                "id"            => 4,
                "character_id"  => 6302,
                "category_id"   => 2,
            ],
            [
                "id"            => 5,
                "character_id"  => 2331,
                "category_id"   => 2,
            ],
            [
                "id"            => 6,
                "character_id"  => 276,
                "category_id"   => 3,
            ],
            [
                "id"            => 7,
                "character_id"  => 1281,
                "category_id"   => 3,
            ],
            [
                "id"            => 8,
                "character_id"  => 1673,
                "category_id"   => 3,
            ],
            [
                "id"            => 9,
                "character_id"  => 482,
                "category_id"   => 4,
            ],
            [
                "id"            => 10,
                "character_id"  => 1190,
                "category_id"   => 5,
            ],
            [
                "id"            => 11,
                "character_id"  => 561,
                "category_id"   => 5,
            ],
            [
                "id"            => 12,
                "character_id"  => 502,
                "category_id"   => 5,
            ],
            [
                "id"            => 13,
                "character_id"  => 286,
                "category_id"   => 5,
            ],
            [
                "id"            => 14,
                "character_id"  => 304,
                "category_id"   => 5,
            ],
        ];

        $cards = Card::count();

        if($cards == 0) {
            foreach($sample_cards as $card){                
                DB::table('cards')->insert([
                    "id"             => $card['id'],
                    "character_id"   => $card['character_id'],
                    "category_id"    => $card['category_id'],
                ]);
            }
        }
    }
}
