<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Card;
use Illuminate\Http\Request;
use App\Services\CardService;
use App\Traits\TraitCkeditor;
use App\Http\Requests\CardRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CardController extends Controller
{
    use TraitCkeditor;

    protected $cardService;

    public function __construct(CardService $cardService) {
        $this->cardService = $cardService;
    }

    /**
    * @OA\Post(
    *     path="/api/cards",
    *     tags={"Card"},
    *     security={{"sanctum":{}}},
    *     summary="Register a new card",
    *     description="This endpoint allows you to store a new card.",
    *     @OA\RequestBody(
    *         required=true,
    *         @OA\MediaType(
    *             mediaType="multipart/form-data",
    *             @OA\Schema(
    *                 required={"character_id","category_id"},
    *                 @OA\Property(property="character_id", type="integer", example="2"),
    *                 @OA\Property(property="category_id", type="integer", example="5"),
    *             )
    *         )
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Successful operation",
    *         @OA\JsonContent(
    *             @OA\Property(property="success", type="boolean", example=true),
    *             @OA\Property(property="message", type="string", example="card stored successfully."),
    *             @OA\Property(property="card", type="object",
    *                 @OA\Property(property="id", type="integer", example=1),
    *                 @OA\Property(property="category_id", type="integer", example=1),
    *                 @OA\Property(property="character_id", type="integer", example=1),
    *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-06-19T14:00:00Z"),
    *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-06-19T14:00:00Z")
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
    *         response=403, description=" Forbidden",
    *         @OA\JsonContent(
    *             @OA\Property(property="success", type="boolean", example=true),
    *             @OA\Property(property="message", type="string", example="you don't have the right permission")
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
    public function store(CardRequest $request)
    {
        try{
            $user = auth()->user();
            $card = $this->cardService
                            ->setUser($user)
                            ->setCategoryId($request['category_id'])
                            ->setCharacterId($request['character_id'])
                            ->checkUserCategory()
                            ->store();
            return response()->json([
                'message' => 'card stored successfully',
                'card'    => $card
            ]);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Validation Error'], 422);
        } catch (AuthorizationException $e) {
            return response()->json(['message' => 'Authorization Exception'], 403);
        } catch(ModelNotFoundException $e) {
            return response()->json(['message' => 'Not Found'], 404);
        } catch (Exception $e) {
            \Log::error($e->getMessage());
            return response()->json(['message' => 'Error'], 500);
        }
    }

    /**
    * @OA\Get(
    *     path="/api/cards/{cardId}",
    *     tags={"Card"},
    *     security={{"sanctum":{}}},
    *     summary="get user card",
    *     description="This endpoint allows you to get an specific card.",
    *     @OA\Parameter(
    *         name="cardId",
    *         in="path",
    *         required=true,
    *         description="The ID of the card to retrieve",
    *         @OA\Schema(type="integer", example=1)
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Successful operation",
    *         @OA\JsonContent(
    *             @OA\Property(property="success", type="boolean", example=true),
    *             @OA\Property(property="message", type="string", example="card retrieved successfully."),
    *             @OA\Property(property="card", type="object",
    *                 @OA\Property(property="id", type="integer", example=1),
    *                 @OA\Property(property="category_id", type="integer", example=1),
    *                 @OA\Property(property="character_id", type="integer", example=1),
    *                 @OA\Property(property="description", type="string", example="some description about this card"),
    *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-06-19T14:00:00Z"),
    *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-06-19T14:00:00Z")
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
    *         response=403, description=" Forbidden",
    *         @OA\JsonContent(
    *             @OA\Property(property="success", type="boolean", example=true),
    *             @OA\Property(property="message", type="string", example="you don't have the right permission")
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
    public function show(CardRequest $request){
        try{
            $user = auth()->user();
            $card = $this->cardService
                            ->setUser($user)
                            ->setId($request['cardId'])
                            ->setRelations(['character'])
                            ->checkUserCard()
                            ->getCard();
            return response()->json([
                'message' => 'card retrieved successfully',
                'card'    => $card
            ]);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Validation Error'], 422);
        } catch (AuthorizationException $e) {
            return response()->json(['message' => 'Authorization Exception'], 403);
        } catch(ModelNotFoundException $e) {
            return response()->json(['message' => 'Not Found'], 404);
        } catch (Exception $e) {
            \Log::error($e->getMessage());
            return response()->json(['message' => 'Error'], 500);
        }
    }

    /**
    * @OA\Post(
    *     path="/api/cards/description/{cardId}",
    *     tags={"Card"},
    *     security={{"sanctum":{}}},
    *     summary="add note for a card",
    *     description="This endpoint allows you to add note for a card.",
    *     @OA\RequestBody(
    *         required=true,
    *         @OA\MediaType(
    *             mediaType="multipart/form-data",
    *             @OA\Schema(
    *                 @OA\Property(property="description", type="string", example="some description about this card")
    *             )
    *         )
    *     ),
    *     @OA\Parameter(
    *         name="cardId",
    *         in="path",
    *         required=true,
    *         description="The ID of the card to retrieve",
    *         @OA\Schema(type="integer", example=1)
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Successful operation",
    *         @OA\JsonContent(
    *             @OA\Property(property="success", type="boolean", example=true),
    *             @OA\Property(property="message", type="string", example="card stored successfully."),
    *             @OA\Property(property="card", type="object",
    *                 @OA\Property(property="id", type="integer", example=1),
    *                 @OA\Property(property="category_id", type="integer", example=1),
    *                 @OA\Property(property="character_id", type="integer", example=1),
    *                 @OA\Property(property="description", type="string", example="some description about this card"),
    *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-06-19T14:00:00Z"),
    *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-06-19T14:00:00Z"),
    *                 @OA\Property(property="character", type="object",
    *                     @OA\Property(property="id", type="integer", example=1),
    *                     @OA\Property(property="character", type="string", example="党"),
    *                     @OA\Property(property="pinyin", type="string", example="dǎng"),
    *                     @OA\Property(property="definition", type="string", example="political party, gang"),
    *                     @OA\Property(property="stroke_count", type="integer", example="10"),
    *                     @OA\Property(property="radical", type="string", example="儿"),
    *                     @OA\Property(property="frequency_rank", type="integer", example="411"),
    *                     @OA\Property(property="hsk_level", type="integer", example="6")
    *                )
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
    *         response=403, description=" Forbidden",
    *         @OA\JsonContent(
    *             @OA\Property(property="success", type="boolean", example=true),
    *             @OA\Property(property="message", type="string", example="you don't have the right permission")
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
    public function storeDescription(CardRequest $request){
        try{
            $description = $this->getDescription($request->input('description'));
            $user = auth()->user();
            $card = $this->cardService
                            ->setUser($user)
                            ->setId($request['cardId'])
                            ->setDescription($description)
                            ->setRelations(['character'])
                            ->checkUserCard()
                            ->updateDescription()
                            ->getCard();
            
            return response()->json([
                'message' => 'card retrieved successfully',
                'card'    => $card
            ]);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Validation Error'], 422);
        } catch (AuthorizationException $e) {
            return response()->json(['message' => 'Authorization Exception'], 403);
        } catch(ModelNotFoundException $e) {
            return response()->json(['message' => 'Not Found'], 404);
        } catch (Exception $e) {
            \Log::error($e->getMessage());
            return response()->json(['message' => 'Error'], 500);
        }
    }

    // public function destroy(Card $card){
    //     $card->delete();
    //     return response()->json(['message' => 'card deleted successfully']);
    // }
}
