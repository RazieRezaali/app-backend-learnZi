<?php 
namespace App\Services;

use App\Models\User;
use App\Models\UserMeta;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService{

    protected $user;
    protected $relations;

    public function setUser($user){
        $this->user = $user;
        return $this;
    }
    
    public function getUser(){
        return $this->user;
    }

    public function setRelations($relations){
        $this->relations = $relations;
        return $this;
    }
    
    public function getRelations(){
        return $this->relations;
    }

    public function register(array $data): User
    {
        try{
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
        catch(Exception $e){
            throw $e;        
        }
    }

    public function login($credentials)
    {
        try{
            if (Auth::attempt($credentials)) {
                $user = Auth::user();
                if($user->status === 1){
                    $token = $user->createToken('learnzi')->plainTextToken;
                    return $token;
                }
            }
            return null;
        } 
        catch(Exception $e){
            throw $e;        
        }
    }

    public function getUserData()
    {
        try{
            $query = User::query();
            if($this->getUser()){
                $query->where('id', $this->getUser()->id);
            }
            if($this->getRelations()){
                $query->with($this->getRelations());
            }
            return $query->first();
        } 
        catch(Exception $e){
            throw $e;        
        }
    }
}
