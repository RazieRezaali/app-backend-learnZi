<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Country;
use Illuminate\Http\Request;
use App\Services\CountryService;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CountryController extends Controller
{
    protected $countryService;

    public function __construct(CountryService $countryService) {
        $this->countryService = $countryService;
    }

    /**
    * @OA\Get(
    *     path="/api/countries",
    *     tags={"Country"},
    *     summary="get all countries",
    *     description="This endpoint allows you to get all countries.",
    *     @OA\Response(
    *         response=200,
    *         description="Successful operation",
    *         @OA\JsonContent(
    *             @OA\Property(property="success", type="boolean", example=true),
    *             @OA\Property(property="message", type="string", example="countries are retrieved successfully."),
    *             @OA\Property(property="countries", type="object",
    *                 @OA\Property(property="id", type="integer", example=1),
    *                 @OA\Property(property="name", type="string", example="Albania"),
    *                 @OA\Property(property="dial_code", type="string", example="+1")
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
    *         response=500,
    *         description="Unexpected error",
    *         @OA\JsonContent(
    *             @OA\Property(property="success", type="boolean", example=false),
    *             @OA\Property(property="message", type="string", example="Internal error.")
    *         )
    *     )
    * )
    */
    public function index(){
        try{ 
            $countries = $this->countryService->getCountries();
            return response()->json([
                'message'    => 'Character retrieved successfully.',
                'countries'  => $countries
            ]);
        } catch(ModelNotFoundException $e) {
            return response()->json(['message' => 'Not Found'], 404);
        } catch (Exception $e) {
            \Log::error($e->getMessage());
            return response()->json(['message' => 'Error'], 500);
        }
        
    }
}
