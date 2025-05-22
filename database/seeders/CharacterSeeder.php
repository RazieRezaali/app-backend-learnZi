<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CharacterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fileContent = file_get_contents(base_path() . '/app/Constants/Json/hanzi_db.txt');
        $characterObjects = explode("\n",$fileContent);
        foreach($characterObjects as $character){
            $character = json_decode($character,true);
            if(!empty($character['pinyin']) && !empty($character['character'])){
                DB::table('characters')->insert([
                    'character'         => $character['character'],
                    'pinyin'            => $character['pinyin'],
                    'definition'        => $character['definition'],
                    'stroke_count'      => $character['stroke_count'],
                    'radical'           => $character['radical'], 
                    'frequency_rank'    => $character['frequency_rank'],
                    'hsk_level'         => $character['hsk_level'],
                ]);
            }
        }
    }
}
