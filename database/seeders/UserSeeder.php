<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sample_users = [
            [
                "id"           => 1,
                "fname"        => 'sara',
                "lname"        => 'bagheri',
                "email"        => 'sara@gmail.com',
                "password"     => Hash::make('12345678'),
                "phone"        => '+9812345678',
                "age"          => '21',
                "country_id"   => 2,
                "level_id"     => 3
            ],
            [
                "id"           => 2,
                "fname"        => 'soheil',
                "lname"        => 'alavi',
                "email"        => 'soheil@gmail.com',
                "password"     => Hash::make('12345678'),
                "phone"        => '+9812345678',
                "age"          => '23',
                "country_id"   => 4,
                "level_id"     => 4
            ],
        ];

        $users = \App\Models\User::count();

        if($users == 0) {
            foreach($sample_users as $user){                
                DB::table('users')->insert([
                    "id"           => $user['id'],
                    "fname"        => $user['fname'],
                    "lname"        => $user['lname'],
                    "email"        => $user['email'],
                    "password"     => $user['password'],
                    "phone"        => $user['phone'],
                ]);
                DB::table('user_meta')->insert([
                    "user_id"      => $user['id'],
                    "age"          => $user['age'],
                    "country_id"   => $user['country_id'],
                    "level_id"     => $user['level_id']
                ]);
            }
        }
    }
}
