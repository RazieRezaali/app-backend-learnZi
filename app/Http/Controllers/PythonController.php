<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PythonController extends Controller
{
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
