<?php 
namespace App\Services;

use Exception;
use App\Models\Card;
use App\Models\Category;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CardService{

    protected $user;
    protected $id;
    protected $categoryId;
    protected $characterId;
    protected $relations;
    protected $description;

    public function setUser($user){
        $this->user = $user;
        return $this;
    }

    public function getUser(){
        return $this->user;
    }

    public function setDescription($description){
        $this->description = $description;
        return $this;
    }

    public function getDescription(){
        return $this->description;
    }

    public function setId($id){
        $this->id = $id;
        return $this;
    }

    public function getId(){
        return $this->id;
    }

    public function setCategoryId($categoryId){
        $this->categoryId = $categoryId;
        return $this;
    }

    public function getCategoryId(){
        return $this->categoryId;
    }

    public function setCharacterId($characterId){
        $this->characterId = $characterId;
        return $this;
    }

    public function getCharacterId(){
        return $this->characterId;
    }

    public function setRelations($relations){
        $this->relations = $relations;
        return $this;
    }

    public function getRelations(){
        return $this->relations;
    }

    public function store(){
        try{
            return Card::create([
                'category_id'   => $this->getCategoryId(),
                'character_id'  => $this->getCharacterId(),
            ]);
        } catch(Exception $e){
            throw $e;        
        }
    }

    public function checkUserCategory(){
        try{
            $category = Category::find($this->getCategoryId());
            if($category->user_id !== $this->getUser()->id){
                throw new AuthorizationException();
            }
            return $this;
        } catch(Exception $e){
            throw $e;        
        }        
    }

    public function checkUserCard(){
        try{
            $card = Card::find($this->getId());
            if($card->category->user_id !== $this->getUser()->id){
                throw new AuthorizationException();
            }
            return $this;
        } catch(Exception $e){
            throw $e;        
        }        
    }

    public function getCard(){
        try{
            $query = Card::query();
            if($this->getRelations()){
                $query->with($this->getRelations());
            }
            $card = $query->find($this->getId());
            if(!$card){                
                throw new ModelNotFoundException();
            }
            return $card;
        } catch(Exception $e){
            throw $e;        
        }
    }

    public function updateDescription(){
        try{
            $card = Card::find($this->getId());
            if(!$card){                
                throw new ModelNotFoundException();
            }
            $card->update([
                'description' => $this->getDescription()
            ]);    
            return $this;
        } catch(Exception $e){
            throw $e;        
        }
    }

    public function deleteCard(){
        try{
            $this->getCard()->delete();
        } catch(Exception $e){
            throw $e;        
        }        
    }

}
