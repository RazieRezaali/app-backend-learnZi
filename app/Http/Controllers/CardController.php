<?php

namespace App\Http\Controllers;

use App\Models\Card;
use Illuminate\Http\Request;
use App\Services\CardService;
use App\Traits\TraitCkeditor;

class CardController extends Controller
{
    use TraitCkeditor;

    protected $cardService;

    public function __construct(CardService $cardService) {
        $this->cardService = $cardService;
    }

    /**
    * @OA\Post(
    *     path="/api/card",
    *     tags={"Card"},
    *     summary="Register a new card",
    *     description="This endpoint allows you to store a new card.",
    *     operationId="storeCard",
    *     @OA\RequestBody(
    *         required=true,
    *         @OA\MediaType(
    *             mediaType="multipart/form-data",
    *             @OA\Schema(
    *                 required={"character_id","category_id"},
    *                 @OA\Property(property="character_id", type="integer", example="2"),
    *                 @OA\Property(property="category_id", type="integer", example="3")
    *             )
    *         )
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Successful operation",
    *         @OA\JsonContent(
    *             @OA\Property(property="success", type="boolean", example=true),
    *             @OA\Property(property="message", type="string", example="User registered successfully."),
    *             @OA\Property(property="card", type="object",
    *                 @OA\Property(property="id", type="integer", example=1),
    *                 @OA\Property(property="character_id", type="integer", example="2")
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
    
    public function storeCard(Request $request)
    {
        $card = Card::create([
            'character_id'  => $request['character_id'],
            'category_id'   => $request['category_id']
        ]);
        return response()->json(['message' => 'card stored successfully', 'card' => $card]);
    }

    public function destroy(Card $card){
        $card->delete();
        return response()->json(['message' => 'card deleted successfully']);
    }

    public function show($cardId){
        $card = Card::with('character')->findOrFail($cardId);
        return response()->json(['card' => $card]);
    }

    public function description(Card $card, Request $request){
        $description = $this->getDescription($request->input('description'));
        if($description){
            $card->update([
                'description' => $description
            ]);    
        }
        return response()->json(['card' => $card]);
    }
}
