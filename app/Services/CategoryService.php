<?php 
namespace App\Services;

use App\Models\Category;

class CategoryService{

    protected $user;

    public function setUser($user){
        $this->user = $user;
        return $this;
    }

    public function getUser(){
        return $this->user;
    }

    public function store($data){
        try{
            return Category::create([
                'name'        => $data['name'],
                'parent_id'   => $data['parent_id'] ?? null,
                'user_id'     => $this->getUser()->id,
            ]);
        } catch(Exception $e){
            throw $e;        
        }
    }

    public function getUserCategories(){
        try{
            return Category::where('user_id', $this->getUser()->id)->get();
        } catch(Exception $e){
            throw $e;        
        }
    }

    public function getUserNestedCategories(){
        try{
            return Category::with(['childrenRecursive.cards.character', 'cards.character'])
                ->where('user_id', $this->getUser()->id)
                ->whereNull('parent_id')
                ->get();
        } catch(Exception $e){
            throw $e;        
        }
    }
    public function getUserRootCategories(){
        try{
            return Category::where('user_id', $this->getUser()->id)
                ->whereNull('parent_id')
                ->get();
        } catch(Exception $e){
            throw $e;        
        }
    }
}
