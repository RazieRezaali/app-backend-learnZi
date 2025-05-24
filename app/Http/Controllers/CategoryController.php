<?php

namespace App\Http\Controllers;

use Log;
use Exception;
use App\Models\Category;
use App\Http\Requests\CategoryStoreReques;

class CategoryController extends Controller
{
    /**
    * @OA\Post(
    *     path="/api/categories",
    *     tags={"Category"},
    *     summary="Register a new category",
    *     description="This endpoint allows you to store a new category.",
    *     @OA\RequestBody(
    *         required=true,
    *         @OA\MediaType(
    *             mediaType="multipart/form-data",
    *             @OA\Schema(
    *                 required={"name"},
    *                 @OA\Property(property="name", type="string", example="fruits"),
    *                 @OA\Property(property="parent_id", type="integer", example="12"),
    *             )
    *         )
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Successful operation",
    *         @OA\JsonContent(
    *             @OA\Property(property="success", type="boolean", example=true),
    *             @OA\Property(property="message", type="string", example="category stored successfully."),
    *             @OA\Property(property="category", type="object",
    *                 @OA\Property(property="id", type="integer", example=1),
    *                 @OA\Property(property="name", type="string", example="fruits"),
    *                 @OA\Property(property="parent_id", type="integer", example="12"),
    *                 @OA\Property(property="user_id", type="integer", example="14")
    *             )
    *         )
    *     ),
    *     @OA\Response(
    *         response=401,
    *         description="Unauthenticated",
    *         @OA\JsonContent(
    *             @OA\Property(property="success", type="boolean", example=false),
    *             @OA\Property(property="message", type="string", example="Authentication needed.")
    *         )
    *     ),
    *     @OA\Response(
    *         response=403,
    *         description="Forbidden",
    *         @OA\JsonContent(
    *             @OA\Property(property="success", type="boolean", example=false),
    *             @OA\Property(property="message", type="string", example="Authorization needed.")
    *         )
    *     ),
    *     @OA\Response(
    *         response=404,
    *         description="Not found",
    *         @OA\JsonContent(
    *             @OA\Property(property="success", type="boolean", example=false),
    *             @OA\Property(property="message", type="string", example="Not found.")
    *         )
    *     ),
    *     @OA\Response(
    *         response=422,
    *         description="Validation error",
    *         @OA\JsonContent(
    *             @OA\Property(property="success", type="boolean", example=false),
    *             @OA\Property(property="message", type="string", example="Validation failed."),
    *             @OA\Property(
    *                 property="errors",
    *                 type="object",
    *                 @OA\Property(
    *                     property="email",
    *                     type="array",
    *                     @OA\Items(type="string", example="The email has already been taken.")
    *                 )
    *             )
    *         )
    *     ),
    *     @OA\Response(
    *         response=500,
    *         description="Unexpected error",
    *         @OA\JsonContent(
    *             @OA\Property(property="success", type="boolean", example=false),
    *             @OA\Property(property="message", type="string", example="Internal error.")
    *         )
    *     )
    * )
    */
    
    public function store(CategoryStoreReques $request)
    {
        $data = $request->validated();
        try{
            $category = Category::create([
                'name'        => $data['name'],
                'parent_id'   => $data['parent_id'] ?? null,
                'user_id'     => auth()->user()->id,
            ]);
            return response()->json(['message' => 'category stored successfully', 'category' => $category]);
        } catch(Exception $e){
            Log::info($e->getMessage());
            return response()->json(['message' => 'error'], 500);
        }
    }

    public function index()
    {
        try{
            $categories = Category::where('user_id', auth()->user()->id)->get();
            return response()->json([
                'message' => 'categories retrieved successfully.',
                'categories' => $categories
            ]);
        } catch(Exception $e){
            Log::info($e->getMessage());
            return response()->json(['message' => 'error'], 500);
        }
    }

    public function getCategoryTree()
    {
        try{
            $categories = Category::with(['childrenRecursive.cards.character', 'cards.character'])
                ->where('user_id', auth()->user()->id)
                ->whereNull('parent_id')
                ->get();
            return response()->json([
                'message' => 'categories retrieved successfully.',
                'characters' => $categories
            ]);
        } catch(Exception $e){
            Log::info($e->getMessage());
            return response()->json(['message' => 'error'], 500);
        }
    }

    public function getCategoryRoot(){
        try{
            $categories = Category::where('user_id', auth()->user()->id)->whereNull('parent_id')->get();
            return response()->json([
                'message'    => 'categories retrieved successfully.',
                'categories' => $categories
            ]);
        } catch(Exception $e){
            Log::info($e->getMessage());
            return response()->json(['message' => 'error'], 500);
        }
    }

    public function getCategoryCards($categoryId){
        try{
            $category = Category::find($categoryId);
            $categoryChildren = $category->children;
            $cards = $category->cards()->with('character')->get();
            return response()->json([
                'message' => 'cards retrieved successfully.',
                'categoryChildren' => $categoryChildren,
                'cards'            => $cards 
            ]);
        } catch(Exception $e){
            Log::info($e->getMessage());
            return response()->json(['message' => 'error'], 500);
        }  
    }

    public function getCategoryQuizCharacters($categoryId){
        try{
            $category = Category::find($categoryId);
            $cards = $category->cards()->with('character')->get();
            return response()->json([
                'message' => 'cards retrieved successfully.',
                'cards'   => $cards 
            ]);
        } catch(Exception $e){
            Log::info($e->getMessage());
            return response()->json(['message' => 'error'], 500);
        }
    }

    // public function getUserCategoriesWithCards($userId)
    // {
    //     $categories = Category::where('user_id', $userId)
    //         ->with(['cards.character']) // eager load cards + character
    //         ->get();

    //     return response()->json($categories);
    // }

    // public function getTestCategories(Request $request){
    //     $user = Auth::user();
    //     if (!$user) {
    //         return response()->json(['message' => 'Unauthenticated'], 401);
    //     }
    //     $categories = Category::where('user_id',$user->id)->get();        
    //     return response()->json($categories);
    // }

}