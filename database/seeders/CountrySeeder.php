<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = json_decode(file_get_contents(base_path() . '/app/Constants/Json/Countries.json'));

        $countriesCount = \App\Models\Country::count();

        if($countriesCount == 0) {
            foreach ($countries as $country) {
                if(!empty($country->name) && !empty($country->code) && !empty($country->dial_code)){
                    \Illuminate\Support\Facades\DB::table('countries')->insert([
                        'name'       => $country->name,
                        'code'       => $country->code,
                        'dial_code'  => $country->dial_code 
                    ]);
                }
            }
        }
    }
}
