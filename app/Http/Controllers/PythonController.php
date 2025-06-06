<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PythonController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/ocr",
     *     tags={"PythonRequests"},
     *     summary="Upload an image for OCR processing",
     *     description="Accepts an image file and forwards it to the Python OCR service to extract text.",
     *     operationId="uploadImageForOCR",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Image file for OCR",
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"image"},
     *                 @OA\Property(
     *                     property="image",
     *                     description="The image file to be processed",
     *                     type="string",
     *                     format="binary"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OCR result returned successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="text",
     *                 type="array",
     *                 @OA\Items(type="string"),
     *                 example={"Detected text line 1", "Detected text line 2"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request - No image uploaded",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="No image uploaded")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error - OCR service failed",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Failed to process image")
     *         )
     *     )
     * )
     */
    public function ocr(Request $request)
    {
        if (!$request->hasFile('image')) {
            return response()->json(['error' => 'No image uploaded'], 400);
        }
        $image = $request->file('image');
        $response = Http::attach(
            'image', file_get_contents($image->getRealPath()), $image->getClientOriginalName()
        )->acceptJson()->post('http://localhost:5000/upload');

        return response()->json($response->json(), $response->status());
    }

    /**
     * @OA\Post(
     *     path="/api/audio",
     *     tags={"PythonRequests"},
     *     summary="Generate audio from character input",
     *     description="Sends a character to the Python TTS service and returns the generated audio.",
     *     operationId="generateAudioFromCharacter",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Character data for getting its audio",
     *         @OA\JsonContent(
     *             required={"character"},
     *             @OA\Property(
     *                 property="character",
     *                 type="string",
     *                 example="你",
     *                 description="The character to generate audio for"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Audio response",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="audio/mpeg",
     *                 @OA\Schema(type="string", format="binary")
     *             )
     *         }
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Failed to generate audio",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Failed to fetch audio")
     *         )
     *     )
     * )
     */
    public function audio(Request $request)
    {
        $character = $request->input('character');
        $response = Http::withHeaders([
            'Content-Type' => 'application/json'
        ])->withBody(json_encode(['character' => $character]), 'application/json')

        ->post('http://localhost:5001/speak');
        if (!$response->ok()) {
            return response()->json(['error' => 'Failed to fetch audio'], 500);
        }

        return response($response->body(), 200)
            ->header('Content-Type', $response->header('Content-Type', 'audio/mpeg'));
    }
}
