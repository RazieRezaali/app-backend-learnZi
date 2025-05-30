<?php 
namespace App\Services;

use App\Models\Category;
use Illuminate\Auth\Access\AuthorizationException;

class CategoryService{

    protected $user;
    protected $id;
    protected $relations;
    protected $name;
    protected $category;

    public function setUser($user){
        $this->user = $user;
        return $this;
    }

    public function getUser(){
        return $this->user;
    }

    public function setName($name){
        $this->name = $name;
        return $this;
    }

    public function getName(){
        return $this->name;
    }

    public function setId($id){
        $this->id = $id;
        return $this;
    }

    public function getId(){
        return $this->id;
    }

    public function setRelations($relations){
        $this->relations = $relations;
        return $this;
    }

    public function getRelations(){
        return $this->relations;
    }

    public function setCategory($category){
        $this->category = $category;
        return $this;
    }

    public function getCategory(){
        return $this->category;
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

    public function checkUserCategory(){
        try{
            $category = Category::find($this->getId());
            if($category->user_id !== $this->getUser()->id){
                throw new AuthorizationException();
            }
            return $this;
        } catch(Exception $e){
            throw $e;        
        }
    }

    public function updateCategoryName(){
        try{
            $category = Category::find($this->getId());
            return $category->update([
                'name' => $this->getName()
            ]);
        } catch(Exception $e){
            throw $e;        
        }
    }

    public function deleteCategoryWithAllChildren(){
        try{
            $category = Category::with('children')->find($this->getId());
            $descendantsIds = $category->getAllDescendantsFlat()->pluck('id')->toArray();
            $categoryIdsForDelete = array_merge($descendantsIds, [$category->id]);
            foreach ($categoryIdsForDelete as $categoryId) {
                $this->setId($categoryId);
                $this->delete();
            }
        } catch(Exception $e){
            throw $e;        
        }
    }

    public function delete(){
        try{
            $category = Category::find($this->getId());
            $category->delete();
        } catch(Exception $e){
            throw $e;        
        }
    }
}
