<?php

namespace App\Http\Controllers;

use Log;
use Exception;
use App\Models\Category;
use App\Services\CategoryService;
use App\Http\Requests\CategoryRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CategoryController extends Controller
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService) {
        $this->categoryService = $categoryService;
    }
    /**
    * @OA\Post(
    *     path="/api/categories",
    *     tags={"Category"},
    *     security={{"sanctum":{}}},
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
    public function store(CategoryRequest $request)
    {
        try{
            $data = $request->all();
            $user = auth()->user();
            $category = $this->categoryService
                                ->setUser($user)
                                ->store($data);
            return response()->json([
                'message' => 'category stored successfully',
                'category' => $category
            ]);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Validation Error'], 422);
        } catch (Exception $e) {
            \Log::error($e->getMessage());
            return response()->json(['message' => 'Error'], 500);
        }
    }

    /**
    * @OA\Get(
    *     path="/api/categories",
    *     tags={"Category"},
    *     security={{"sanctum":{}}},
    *     summary="get user categories",
    *     description="This endpoint allows you to get user categories.",
    *     @OA\Response(
    *         response=200,
    *         description="Successful operation",
    *         @OA\JsonContent(
    *             @OA\Property(property="success", type="boolean", example=true),
    *             @OA\Property(property="message", type="string", example="categories are retrieved successfully."),
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
    *         response=404,
    *         description="Not found",
    *         @OA\JsonContent(
    *             @OA\Property(property="success", type="boolean", example=false),
    *             @OA\Property(property="message", type="string", example="Not found.")
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
    public function index()
    {
        try{
            $user = auth()->user();
            $categories = $this->categoryService
                                ->setUser($user)
                                ->getUserCategories($user);
            return response()->json([
                'message' => 'categories retrieved successfully.',
                'categories' => $categories
            ]);
        } catch(ModelNotFoundException $e) {
            return response()->json(['message' => 'Not Found'], 404);
        } catch (Exception $e) {
            \Log::error($e->getMessage());
            return response()->json(['message' => 'Error'], 500);
        }
    }

    /**
    * @OA\Get(
    *     path="/api/categories/tree",
    *     tags={"Category"},
    *     security={{"sanctum":{}}},
    *     summary="get user categories",
    *     description="This endpoint allows you to get user nested categories.",
    *     @OA\Response(
    *         response=200,
    *         description="Successful operation",
    *         @OA\JsonContent(
    *             @OA\Property(property="success", type="boolean", example=true),
    *             @OA\Property(property="message", type="string", example="categories are retrieved successfully."),
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
    *         response=404,
    *         description="Not found",
    *         @OA\JsonContent(
    *             @OA\Property(property="success", type="boolean", example=false),
    *             @OA\Property(property="message", type="string", example="Not found.")
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
    public function getCategoryTree()
    {
        try{
            $user = auth()->user();
            $categories = $this->categoryService
                                ->setUser($user)
                                ->getUserNestedCategories($user);
            return response()->json([
                'message' => 'categories retrieved successfully.',
                'categories' => $categories
            ]);
        } catch(ModelNotFoundException $e) {
            return response()->json(['message' => 'Not Found'], 404);
        } catch (Exception $e) {
            \Log::error($e->getMessage());
            return response()->json(['message' => 'Error'], 500);
        }
    }

    /**
    * @OA\Get(
    *     path="/api/categories/root",
    *     tags={"Category"},
    *     security={{"sanctum":{}}},
    *     summary="get user categories",
    *     description="This endpoint allows you to get user root categories.",
    *     @OA\Response(
    *         response=200,
    *         description="Successful operation",
    *         @OA\JsonContent(
    *             @OA\Property(property="success", type="boolean", example=true),
    *             @OA\Property(property="message", type="string", example="categories are retrieved successfully."),
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
    *         response=404,
    *         description="Not found",
    *         @OA\JsonContent(
    *             @OA\Property(property="success", type="boolean", example=false),
    *             @OA\Property(property="message", type="string", example="Not found.")
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
    public function getCategoryRoot(){
        try{
            $user = auth()->user();
            $categories = $this->categoryService
                                ->setUser($user)
                                ->getUserRootCategories($user);
            return response()->json([
                'message' => 'categories retrieved successfully.',
                'categories' => $categories
            ]);
        } catch(ModelNotFoundException $e) {
            return response()->json(['message' => 'Not Found'], 404);
        } catch (Exception $e) {
            \Log::error($e->getMessage());
            return response()->json(['message' => 'Error'], 500);
        }
    }

    /**
    * @OA\Get(
    *     path="/api/categories/cards/{categoryId}",
    *     tags={"Category"},
    *     security={{"sanctum":{}}},
    *     summary="get user card categories",
    *     description="This endpoint allows you to get user's cards of the choosen category.",
    *     @OA\Parameter(
    *         name="categoryId",
    *         in="path",
    *         required=true,
    *         description="The ID of the category to retrieve",
    *         @OA\Schema(type="integer", example=1)
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Successful operation",
    *         @OA\JsonContent(
    *             @OA\Property(property="success", type="boolean", example=true),
    *             @OA\Property(property="message", type="string", example="cards are retrieved successfully."),
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
    public function getCategoryCards(CategoryRequest $request){
        try{
            $user = auth()->user();
            $category = Category::find($request->categoryId);
            if($category->user_id !== $user->id){
                throw new AuthorizationException();
            }
            $categoryChildren = $category->children;
            $cards = $category->cards()->with('character')->get();
            return response()->json([
                'message' => 'cards retrieved successfully.',
                'categoryChildren' => $categoryChildren,
                'cards'            => $cards 
            ]);
        } catch(AuthorizationException $e) {
            return response()->json(['message' => 'Authorization Exception, you don\'t have the right permission to get this category cards'], 403);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Validation Error'], 422);
        } catch(ModelNotFoundException $e) {
            return response()->json(['message' => 'Not Found'], 404);
        } catch (Exception $e) {
            \Log::error($e->getMessage());
            return response()->json(['message' => 'Error'], 500);
        }
    }

    /**
    * @OA\Get(
    *     path="/api/categories/quiz/{categoryId}",
    *     tags={"Category"},
    *     security={{"sanctum":{}}},
    *     summary="get user card categories",
    *     description="This endpoint allows you to get user's cards of the choosen category.",
    *     @OA\Parameter(
    *         name="categoryId",
    *         in="path",
    *         required=true,
    *         description="The ID of the category to retrieve",
    *         @OA\Schema(type="integer", example=1)
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Successful operation",
    *         @OA\JsonContent(
    *             @OA\Property(property="success", type="boolean", example=true),
    *             @OA\Property(property="message", type="string", example="cards are retrieved successfully."),
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
    public function getCategoryQuizCharacters(CategoryRequest $request){
        try{
            $user = auth()->user();
            $category = Category::find($request->categoryId);
            if($category->user_id !== $user->id){
                throw new AuthorizationException();
            }
            $cards = $category->cards()->with('character')->get();
            return response()->json([
                'message' => 'cards retrieved successfully.',
                'cards'   => $cards 
            ]);
        } catch(AuthorizationException $e) {
            return response()->json(['message' => 'Authorization Exception, you don\'t have the right permission to get this category cards'], 403);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Validation Error'], 422);
        } catch(ModelNotFoundException $e) {
            return response()->json(['message' => 'Not Found'], 404);
        } catch (Exception $e) {
            \Log::error($e->getMessage());
            return response()->json(['message' => 'Error'], 500);
        }
    }

    /**
    * @OA\Put(
    *     path="/api/categories/{categoryId}",
    *     tags={"Category"},
    *     security={{"sanctum":{}}},
    *     summary="rename a category",
    *     description="This endpoint allows you to rename a category.",
    *     @OA\Parameter(
    *         name="categoryId",
    *         in="path",
    *         required=true,
    *         description="The ID of the category to retrieve",
    *         @OA\Schema(type="integer", example=1)
    *     ),
    *     @OA\RequestBody(
    *         required=true,
    *         @OA\MediaType(
    *             mediaType="application/json",
    *             @OA\Schema(
    *                 required={"name"},
    *                 @OA\Property(property="name", type="string", example="fruits"),
    *             )
    *         )
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Successful operation",
    *         @OA\JsonContent(
    *             @OA\Property(property="success", type="boolean", example=true),
    *             @OA\Property(property="message", type="string", example="category renamed successfully."),
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
    public function updateCategoryName(CategoryRequest $request){
        try{
            $user = auth()->user();
            $category = $this->categoryService
                        ->setUser($user)
                        ->setId($request->categoryId)
                        ->checkUserCategory()
                        ->setName($request->name)
                        ->updateCategoryName();
            return response()->json([
                'message'  => 'category renamed successfully.',
                'category' => $category
            ]);
        } catch(AuthorizationException $e) {
            return response()->json(['message' => 'Authorization Exception, you don\'t have the right permission to update this category'], 403);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Validation Error'], 422);
        } catch(ModelNotFoundException $e) {
            return response()->json(['message' => 'Not Found'], 404);
        } catch (Exception $e) {
            \Log::error($e->getMessage());
            return response()->json(['message' => 'Error'], 500);
        }
    }

    /**
    * @OA\Delete(
    *     path="/api/categories/{categoryId}",
    *     tags={"Category"},
    *     security={{"sanctum":{}}},
    *     summary="delete a category",
    *     description="This endpoint allows you to delete a category.",
    *     @OA\Parameter(
    *         name="categoryId",
    *         in="path",
    *         required=true,
    *         description="The ID of the category to retrieve",
    *         @OA\Schema(type="integer", example=1)
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Successful operation",
    *         @OA\JsonContent(
    *             @OA\Property(property="success", type="boolean", example=true),
    *             @OA\Property(property="message", type="string", example="category deleted successfully.")
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
    public function destroy(CategoryRequest $request){
        try{
            $user = auth()->user();
            $category = $this->categoryService
                        ->setUser($user)
                        ->setId($request->categoryId)
                        ->checkUserCategory()
                        ->deleteCategoryWithAllChildren();
            return response()->json([
                'message'  => 'category deleted successfully.'
            ]);
        } catch(AuthorizationException $e) {
            return response()->json(['message' => 'Authorization Exception, you don\'t have the right permission to delete this category'], 403);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Validation Error'], 422);
        } catch(ModelNotFoundException $e) {
            return response()->json(['message' => 'Not Found'], 404);
        } catch (Exception $e) {
            \Log::error($e->getMessage());
            return response()->json(['message' => 'Error'], 500);
        }
    }
}