<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;

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
    *                 @OA\Property(property="age", type="integer", example=32),
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
    *                 @OA\Property(property="age", type="integer", example=32),
    *                 @OA\Property(property="country_id", type="integer", example=1),
    *                 @OA\Property(property="level_id", type="integer", example=3),
    *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-04-10T10:00:00Z")
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
    public function register(UserRegisterRequest $request)
    {
        $user = $this->authService->register($request->validated());
        $token = $user->createToken('learnzi')->plainTextToken;
        return response()->json([
            'token' => $token,
            'user'  => $user,
        ]);
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
    *             @OA\Property(property="token", type="string", example="eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."),
    *             @OA\Property(property="user", type="object", @OA\Property(property="id", type="integer", example=1))
    *         )
    *     ),
    *     @OA\Response(
    *         response=401,
    *         description="Invalid credentials"
    *     ),@OA\Response(
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
    public function login(UserLoginRequest $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');
        
        $token = $this->authService->login($credentials);
        if (!$token) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
        return response()->json([
            'token' => $token,
            'user' => auth()->user(),
        ]);
    }

    public function logout(): JsonResponse
    {
        auth()->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }

    public function getProfile()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
        $userProfile = User::with(['userMeta', 'userMeta.country', 'userMeta.level'])
                            ->where('id', $user->id)
                            ->first();
        return response()->json($userProfile);
    }
}
