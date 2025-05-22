<?php 
namespace App\Services;

use App\Models\User;
use App\Models\UserMeta;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService{

    public function register(array $data): User
    {
        $user = User::create([
            'fname'     => $data['fname'],
            'lname'     => $data['lname'],
            'email'     => $data['email'],
            'password'  => Hash::make($data['password']),
            'phone'     => $data['phone']
        ]);

        UserMeta::create([
            'user_id'       => $user->id,
            'age'           => $data['age'],
            'country_id'    => $data['country_id'],
            'level_id'      => $data['level_id']
        ]);
        return $user;
    }

    public function login(array $credentials): ?string
    {
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            if($user->status === 1){
                $token = $user->createToken('learnzi')->plainTextToken;
                return $token;
            }
        }
        return null;
    }
}
