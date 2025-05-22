<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CategoryStoreReques;

class CategoryController extends Controller
{
    /**
    * @OA\Post(
    *     path="/api/category",
    *     tags={"Category"},
    *     summary="Register a new category",
    *     description="This endpoint allows you to store a new category.",
    *     operationId="store",
    *     @OA\RequestBody(
    *         required=true,
    *         @OA\MediaType(
    *             mediaType="multipart/form-data",
    *             @OA\Schema(
    *                 required={"name","user_id"},
    *                 @OA\Property(property="name", type="string", example="fruits"),
    *                 @OA\Property(property="parent", type="integer", example="12"),
    *                 @OA\Property(property="user_id", type="integer", example="14")
    *             )
    *         )
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Successful operation",
    *         @OA\JsonContent(
    *             @OA\Property(property="success", type="boolean", example=true),
    *             @OA\Property(property="message", type="string", example="User registered successfully."),
    *             @OA\Property(property="category", type="object",
    *                 @OA\Property(property="id", type="integer", example=1),
    *                 @OA\Property(property="name", type="string", example="fruits"),
    *                 @OA\Property(property="parent", type="integer", example="12"),
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
        $category = Category::create([
            'name'        => $data['name'],
            'parent_id'   => $data['parent_id'] ?? null,
            'user_id'     => $data['user_id']
        ]);
        return response()->json(['message' => 'category stored successfully', 'category' => $category]);
    }

    public function getUserCategories($userId)
    {
        $categories = Category::where('user_id',$userId)->get();
        return response()->json([
            'message' => 'categories are recieved successfully',
            'characters' => $categories
        ]);
    }

    public function getUserCategoriesWithCards($userId)
    {
        $categories = Category::where('user_id', $userId)
            ->with(['cards.character']) // eager load cards + character
            ->get();

        return response()->json($categories);
    }

    public function getUserCategoryTree($userId)
    {
        $categories = Category::with(['childrenRecursive.cards.character', 'cards.character'])
            ->where('user_id', $userId)
            ->whereNull('parent_id')
            ->get();

        return response()->json($categories);
    }

    public function getRootCategories($userId){
        $categories = Category::where('user_id',$userId)->whereNull('parent_id')->get();
        return response()->json($categories);
    }

    public function getCardsAndCategories($categoryId){
        $category = Category::find($categoryId);
        $categoryChildren = $category->children;
        $cards = $category->cards()->with('character')->get();
        return response()->json([
            'categoryChildren' => $categoryChildren,
            'cards'            => $cards 
        ]);
    }

    public function getTestCategories(Request $request){
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
        $categories = Category::where('user_id',$user->id)->get();        
        return response()->json($categories);
    }

    public function getTestCharacters(Request $request, $category_id){
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
        if(!$category_id){
            return response()->json(['message' => 'Not Found'], 404);
        }
        $category = Category::find($category_id);
        $cards = $category->cards()->with('character')->get();
        return response()->json($cards);
    }
}
