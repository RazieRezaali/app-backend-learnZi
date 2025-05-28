<?php 
namespace App\Services;

use Exception;
use App\Models\Character;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CharacterService{

    protected $id;
    protected $character;
    protected $pinyin;
    protected $definition;
    protected $strokeCount;
    protected $radical;
    protected $frequencyRank;
    protected $hskLevel;
    protected $keyword;

    public function setId($id){
        $this->id = $id;
        return $this;
    }

    public function setHskLevel($hskLevel){
        $this->hskLevel = $hskLevel;
        return $this;
    }

    public function setCharacter($character){
        $this->character = $character;
        return $this;
    }

    public function getId(){
        return $this->id;
    }

    public function getHskLevel(){
        return $this->hskLevel;
    }

    public function getCharacter(){
        return $this->character;
    }

    public function setKeyword($keyword){
        $this->keyword = $keyword;
        return $this;
    }

    public function getKeyword(){
        return $this->keyword;
    }

    public function getCharacters(){
        try{
            $query = Character::query();
            if($this->getHskLevel()){
                $query->where('hsk_level', $this->getHskLevel());
            }
            if($this->getKeyword()){
                $keyword = $this->getKeyword();
                $query->where(function ($q) use ($keyword) {
                    $q->where('pinyin', 'LIKE', "%{$keyword}%")
                        ->orWhere('character', 'LIKE', "%{$keyword}%")
                        ->orWhere('radical', 'LIKE', "%{$keyword}%");
                });
            }
            if($this->getId()){
                $query->where('id', $this->getId());
                return $query->first();
            }

            return $query->get();
        } 
        catch(Exception $e){
            throw $e;        
        }
    }

    public function getCharacterId(){
        try{
            $query = Character::query();
            $character = $query->where('character', $this->getCharacter())->first();
            if($character){
                return $character->id;
            }
            throw new ModelNotFoundException();
        } 
        catch(Exception $e){
            throw $e;        
        }
    }
}
