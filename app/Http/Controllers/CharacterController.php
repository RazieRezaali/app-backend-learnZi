<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Character;
use Illuminate\Http\Request;
use App\Services\CharacterService;
use App\Http\Requests\CharacterRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CharacterController extends Controller
{
    protected $characterService;

    public function __construct(CharacterService $characterService) {
        $this->characterService = $characterService;
    }
    
    /**
    * @OA\Get(
    *     path="/api/characters/level/{hskLevel}",
    *     tags={"Character"},
    *     summary="get characters by level",
    *     description="This endpoint allows you to get all the characters of a certain level.",
    *     @OA\Parameter(
    *         name="hskLevel",
    *         in="path",
    *         required=true,
    *         description="The hsk level that you want to get its characters",
    *         @OA\Schema(type="integer", example=1)
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Successful operation",
    *         @OA\JsonContent(
    *             @OA\Property(property="success", type="boolean", example=true),
    *             @OA\Property(property="message", type="string", example="characters are retrieved successfully."),
    *             @OA\Property(property="characters", type="object",
    *                 @OA\Property(property="id", type="integer", example=1),
    *                 @OA\Property(property="character", type="string", example="党"),
    *                 @OA\Property(property="pinyin", type="string", example="dǎng"),
    *                 @OA\Property(property="definition", type="string", example="political party, gang"),
    *                 @OA\Property(property="stroke_count", type="integer", example="10"),
    *                 @OA\Property(property="radical", type="string", example="儿"),
    *                 @OA\Property(property="frequency_rank", type="integer", example="411"),
    *                 @OA\Property(property="hsk_level", type="integer", example="6")
    *             )
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
    public function getCharactersByLevel(CharacterRequest $request)
    {
        try{
            $characters = $this->characterService
                                ->setHskLevel($request->hskLevel)
                                ->getCharacters();
            return response()->json([
                'message'    => 'characters are retrieved successfully', 
                'characters' => $characters
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

    /**
    * @OA\Get(
    *     path="/api/characters/search",
    *     tags={"Character"},
    *     summary="get characters by keyword",
    *     description="This endpoint allows you to get all the characters with a certain keyword.",
    *     @OA\Parameter(
    *         name="keyword",
    *         in="query",
    *         required=true,
    *         description="The keyword that you want to get characters with it",
    *         @OA\Schema(type="string", example="key")
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Successful operation",
    *         @OA\JsonContent(
    *             @OA\Property(property="success", type="boolean", example=true),
    *             @OA\Property(property="message", type="string", example="characters are retrieved successfully."),
    *             @OA\Property(property="characters", type="object",
    *                 @OA\Property(property="id", type="integer", example=1),
    *                 @OA\Property(property="character", type="string", example="党"),
    *                 @OA\Property(property="pinyin", type="string", example="dǎng"),
    *                 @OA\Property(property="definition", type="string", example="political party, gang"),
    *                 @OA\Property(property="stroke_count", type="integer", example="10"),
    *                 @OA\Property(property="radical", type="string", example="儿"),
    *                 @OA\Property(property="frequency_rank", type="integer", example="411"),
    *                 @OA\Property(property="hsk_level", type="integer", example="6")
    *             )
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
    public function getCharactersBySearch(CharacterRequest $request)
    {
        try{   
            $characters = $this->characterService
                            ->setKeyword($keyword)
                            ->getCharacters();
            return response()->json([
                'message'    => 'characters are retrieved successfully', 
                'characters' => $characters
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

    /**
    * @OA\Get(
    *     path="/api/characters/{characterId}",
    *     tags={"Character"},
    *     summary="get details of a character",
    *     description="This endpoint allows you to get details of a character.",
    *     @OA\Parameter(
    *         name="characterId",
    *         in="path",
    *         required=true,
    *         description="The character id that you want to get details of it",
    *         @OA\Schema(type="integer", example="2")
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Successful operation",
    *         @OA\JsonContent(
    *             @OA\Property(property="success", type="boolean", example=true),
    *             @OA\Property(property="message", type="string", example="character retrieved successfully."),
    *             @OA\Property(property="characters", type="object",
    *                 @OA\Property(property="id", type="integer", example=1),
    *                 @OA\Property(property="character", type="string", example="党"),
    *                 @OA\Property(property="pinyin", type="string", example="dǎng"),
    *                 @OA\Property(property="definition", type="string", example="political party, gang"),
    *                 @OA\Property(property="stroke_count", type="integer", example="10"),
    *                 @OA\Property(property="radical", type="string", example="儿"),
    *                 @OA\Property(property="frequency_rank", type="integer", example="411"),
    *                 @OA\Property(property="hsk_level", type="integer", example="6")
    *             )
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
    public function getCharacterDetail(CharacterRequest $request)
    {
        try{ 
            $character = $this->characterService
                            ->setId($request->characterId)
                            ->getCharacters();
            return response()->json([
                'message'    => 'Character retrieved successfully.',
                'character'  => $character
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

    /**
    * @OA\Get(
    *     path="/api/characters/id/{character}",
    *     tags={"Character"},
    *     summary="get id of a character",
    *     description="This endpoint allows you to get id of a character.",
    *     @OA\Parameter(
    *         name="character",
    *         in="path",
    *         required=true,
    *         description="The character that you want to get its id",
    *         @OA\Schema(type="string", example="党")
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Successful operation",
    *         @OA\JsonContent(
    *             @OA\Property(property="message", type="string", example="character id retrieved successfully."),
    *             @OA\Property(property="characterId", type="integer", example=1)
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
    public function getCharacterId(CharacterRequest $request)
    {
        try{
            $characterId = $this->characterService
                            ->setCharacter($request->character)
                            ->getCharacterId();
            return response()->json([
                'message'     => 'Character id retrieved successfully.',
                'characterId' => $characterId
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
