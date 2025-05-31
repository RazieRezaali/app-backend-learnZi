<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\AuthRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AuthController extends Controller
{
/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="LearnZi API",
 *     description="API documentation for LearnZi - Chinese learning app",
 *     @OA\Contact(
 *         email="support@learnzi.com"
 *     ),
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 *
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="API Server"
 * )
 */
    protected $authService;

    public function __construct(AuthService $authService) {
        $this->authService = $authService;
    }

    /**
    * @OA\Post(
    *     path="/api/user/register",
    *     tags={"Auth"},
    *     summary="Register a new user",
    *     description="This endpoint allows you to register a new user.",
    *     operationId="register",
    *     @OA\RequestBody(
    *         required=true,
    *         @OA\MediaType(
    *             mediaType="multipart/form-data",
    *             @OA\Schema(
    *                 required={"fname", "lname", "email", "password", "password_confirmation", "phone", "age", "country_id", "level_id"},
    *                 @OA\Property(property="fname", type="string", example="Saul"),
    *                 @OA\Property(property="lname", type="string", example="Goodman"),
    *                 @OA\Property(property="email", type="string", format="email", example="saul@example.com"),
    *                 @OA\Property(property="password", type="string", format="password", example="Pass123!"),
    *                 @OA\Property(property="password_confirmation", type="string", format="password", example="Pass123!"),
    *                 @OA\Property(property="phone", type="string", example="+989123456789"),
    *                 @OA\Property(property="age", type="string", example="32"),
    *                 @OA\Property(property="country_id", type="integer", example=1),
    *                 @OA\Property(property="level_id", type="integer", example=3)
    *             )
    *         )
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Successful operation",
    *         @OA\JsonContent(
    *             @OA\Property(property="success", type="boolean", example=true),
    *             @OA\Property(property="message", type="string", example="User registered successfully."),
    *             @OA\Property(property="user", type="object",
    *                 @OA\Property(property="id", type="integer", example=1),
    *                 @OA\Property(property="fname", type="string", example="Saul"),
    *                 @OA\Property(property="lname", type="string", example="Goodman"),
    *                 @OA\Property(property="email", type="string", example="saul@example.com"),
    *                 @OA\Property(property="phone", type="string", example="09123456789"),
    *                 @OA\Property(property="age", type="string", example=32),
    *                 @OA\Property(property="country_id", type="integer", example=1),
    *                 @OA\Property(property="level_id", type="integer", example=3),
    *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-04-10T10:00:00Z")
    *             )
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
    public function register(AuthRequest $request)
    {
        try{
            $user = $this->authService->register($request->all());
            $token = $user->createToken('learnzi')->plainTextToken;
            return response()->json([
                'message' => 'user registered successfully.',
                'token'   => $token,
                'user'    => $user,
            ]);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Validation Error'], 422);
        } catch (Exception $e) {
            \Log::error($e->getMessage());
            return response()->json(['message' => 'Error'], 500);
        }
    }

    /**
    * @OA\Post(
    *     path="/api/user/login",
    *     summary="User Login",
    *     tags={"Auth"},
    *     description="This endpoint allows you to login.",
    *     operationId="login",
    *     @OA\RequestBody(
    *         required=true,
    *         @OA\MediaType(
    *             mediaType="multipart/form-data",
    *             @OA\Schema(
    *                 required={"email", "password"},
    *                       @OA\Property(property="email", type="string", example="user@example.com"),
    *                       @OA\Property(property="password", type="string", example="password123")
    *             )
    *         )
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Login successful",
    *         @OA\JsonContent(
    *             @OA\Property(property="success", type="boolean", example=true),
    *             @OA\Property(property="message", type="string", example="user login successfully."),
    *             @OA\Property(property="token", type="string", example="eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."),
    *              @OA\Property(property="user", type="object",
    *                 @OA\Property(property="id", type="integer", example=1),
    *                 @OA\Property(property="fname", type="string", example="Saul"),
    *                 @OA\Property(property="lname", type="string", example="Goodman"),
    *                 @OA\Property(property="email", type="string", example="saul@example.com"),
    *                 @OA\Property(property="phone", type="string", example="09123456789"),
    *                 @OA\Property(property="age", type="string", example=32),
    *                 @OA\Property(property="country_id", type="integer", example=1),
    *                 @OA\Property(property="level_id", type="integer", example=3),
    *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-04-10T10:00:00Z")
    *             )
    *         )
    *     ),
    *     @OA\Response(
    *         response=400,
    *         description="Invalid credentials"
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
    public function login(AuthRequest $request)
    {
        try{
            $credentials = $request->only('email', 'password');        
            $token = $this->authService->login($credentials);
            if (!$token) {
                return response()->json(['message' => 'Invalid credentials'], 400);
            }
            return response()->json([
                'message' => 'user login successfully.',
                'token'   => $token,
                'user'    => auth()->user(),
            ]);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Validation Error'], 422);
        } catch (Exception $e) {
            \Log::error($e->getMessage());
            return response()->json(['message' => 'Error'], 500);
        }
    }

    /**
    * @OA\Post(
    *     path="/api/user/logout",
    *     summary="User logout",
    *     security={{"sanctum":{}}},
    *     tags={"Auth"},
    *     description="This endpoint allows you to logout.",
    *     operationId="logout",
    *     @OA\Response(
    *         response=200,
    *         description="logout successful",
    *         @OA\JsonContent(
    *             @OA\Property(property="success", type="boolean", example=true),
    *             @OA\Property(property="message", type="string", example="user login successfully."),
    *             @OA\Property(property="token", type="string", example="eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."),
    *              @OA\Property(property="user", type="object",
    *                 @OA\Property(property="id", type="integer", example=1),
    *                 @OA\Property(property="fname", type="string", example="Saul"),
    *                 @OA\Property(property="lname", type="string", example="Goodman"),
    *                 @OA\Property(property="email", type="string", example="saul@example.com"),
    *                 @OA\Property(property="phone", type="string", example="09123456789"),
    *                 @OA\Property(property="age", type="string", example=32),
    *                 @OA\Property(property="country_id", type="integer", example=1),
    *                 @OA\Property(property="level_id", type="integer", example=3),
    *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-04-10T10:00:00Z")
    *             )
    *         )
    *     ),
    *     @OA\Response(
    *         response=400,
    *         description="you are not logged in"
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
    public function logout()
    {
        try{
            if(auth()->user()){
                auth()->user()->currentAccessToken()->delete();
                return response()->json([
                    'message' => 'user logout successfully.'
                ]);
            }
            return response()->json(['message' => 'you are not logged in'], 400);
        } catch (Exception $e) {
            \Log::error($e->getMessage());
            return response()->json(['message' => 'Error'], 500);
        }
    }

    /**
    * @OA\Get(
    *     path="/api/user/profile",
    *     tags={"Auth"},
    *     security={{"sanctum":{}}},
    *     summary="get the user's profile",
    *     description="This endpoint allows you to get the user's profile.",
    *     operationId="getProfile",
    *     @OA\Response(
    *         response=200,
    *         description="Successful operation",
    *         @OA\JsonContent(
    *             @OA\Property(property="message", type="string", example="User registered successfully."),
    *             @OA\Property(property="user", type="object",
    *                 @OA\Property(property="id", type="integer", example=1),
    *                 @OA\Property(property="fname", type="string", example="Saul"),
    *                 @OA\Property(property="lname", type="string", example="Goodman"),
    *                 @OA\Property(property="email", type="string", example="saul@example.com"),
    *                 @OA\Property(property="phone", type="string", example="09123456789"),
    *                 @OA\Property(property="age", type="string", example=32),
    *                 @OA\Property(property="country_id", type="integer", example=1),
    *                 @OA\Property(property="level_id", type="integer", example=3),
    *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-04-10T10:00:00Z"),
    *                 @OA\Property(property="user_meta", type="object",
    *                    @OA\Property(property="user_id", type="integer", example=1),
    *                    @OA\Property(property="age", type="string", example="22"),
    *                    @OA\Property(property="country_id", type="integer", example="25"),
    *                    @OA\Property(property="level_id", type="integer", example="3"),
    *                    @OA\Property(property="country", type="object",
    *                       @OA\Property(property="id", type="integer", example=1),
    *                       @OA\Property(property="name", type="string", example="Albania"),
    *                       @OA\Property(property="code", type="string", example="AL"),
    *                       @OA\Property(property="dial_code", type="string", example="+355")
    *                    ),
    *                    @OA\Property(property="level", type="object",
    *                       @OA\Property(property="id", type="integer", example=3),
    *                       @OA\Property(property="name", type="string", example="elementary"),
    *                       @OA\Property(property="HSK", type="string", example="HSK3"),
    *                       @OA\Property(property="CEFR", type="string", example="A2"),
    *                       @OA\Property(property="vocabulary", type="integer", example="600")
    *                    )
    *                 )
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
    *         response=500,
    *         description="Unexpected error",
    *         @OA\JsonContent(
    *             @OA\Property(property="success", type="boolean", example=false),
    *             @OA\Property(property="message", type="string", example="Internal error.")
    *         )
    *     )
    * )
    */
    public function getProfile()
    {
        try{
            $user = auth()->user();
            if (!$user) {
                return response()->json(['message' => 'Unauthenticated'], 401);
            }
            $userProfile = $this->authService
                            ->setUser($user)
                            ->setRelations(['userMeta', 'userMeta.country', 'userMeta.level'])
                            ->getUserData();
            return response()->json([
                'message'     => 'user profile retrieved successfully.',
                'userProfile' => $userProfile,
            ]);
        } catch (Exception $e) {
            \Log::error($e->getMessage());
            return response()->json(['message' => 'Error'], 500);
        }
    }

    /**
    * @OA\Put(
    *     path="/api/user/profile",
    *     tags={"Auth"},
    *     security={{"sanctum":{}}},
    *     summary="update user's profile",
    *     description="This endpoint allows you to update user's profile.",
    *     operationId="updateProfile",
    *     @OA\RequestBody(
    *         required=true,
    *         @OA\MediaType(
    *             mediaType="application/json",
    *             @OA\Schema(
    *                 required={"fname", "lname", "email", "password", "password_confirmation", "phone", "age", "country_id", "level_id"},
    *                 @OA\Property(property="fname", type="string", example="Saul"),
    *                 @OA\Property(property="lname", type="string", example="Goodman"),
    *                 @OA\Property(property="email", type="string", format="email", example="saul@example.com"),
    *                 @OA\Property(property="password", type="string", format="password", example="Pass123!"),
    *                 @OA\Property(property="password_confirmation", type="string", format="password", example="Pass123!"),
    *                 @OA\Property(property="phone", type="string", example="+989123456789"),
    *                 @OA\Property(property="age", type="string", example="32"),
    *                 @OA\Property(property="country_id", type="integer", example=1),
    *                 @OA\Property(property="level_id", type="integer", example=3)
    *             )
    *         )
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Successful operation",
    *         @OA\JsonContent(
    *             @OA\Property(property="success", type="boolean", example=true),
    *             @OA\Property(property="message", type="string", example="User profile updated successfully."),
    *             @OA\Property(property="user", type="object",
    *                 @OA\Property(property="id", type="integer", example=1),
    *                 @OA\Property(property="fname", type="string", example="Saul"),
    *                 @OA\Property(property="lname", type="string", example="Goodman"),
    *                 @OA\Property(property="email", type="string", example="saul@example.com"),
    *                 @OA\Property(property="phone", type="string", example="09123456789"),
    *                 @OA\Property(property="age", type="string", example="32"),
    *                 @OA\Property(property="country_id", type="integer", example=1),
    *                 @OA\Property(property="level_id", type="integer", example=3),
    *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-04-10T10:00:00Z"),
    *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-04-10T10:00:00Z")
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
    public function updateProfile(AuthRequest $request)
    {
        try{
            $user = auth()->user();
            if (!$user) {
                return response()->json(['message' => 'Unauthenticated'], 401);
            }
            $user = $this->authService
                            ->setId($user->id)
                            ->updateUser($request->all());
            return response()->json([
                'message' => 'user profile updated successfully.',
                'user'    => $user,
            ]);
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
