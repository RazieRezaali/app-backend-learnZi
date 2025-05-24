<?php

namespace App\Http\Controllers;

use App\Models\Character;
use App\Services\CardService;
use Illuminate\Http\Request;

class CharacterController extends Controller
{
    protected $cardService;

    public function __construct(CardService $cardService) {
        $this->cardService = $cardService;
    }
    
    public function index()
    {
        $characters = Character::whereNotNull('hsk_level')->get();
        return response()->json(['message' => 'characters are recieved successfully', 'characters' => $characters]);
    }

    public function getCharactersByLevel($level)
    {
        $characters = Character::where('hsk_level',$level)->get();
        return response()->json([
            'message'    => 'characters are recieved successfully', 
            'characters' => $characters
        ]);
    }

    public function getCharactersBySearch(Request $request)
    {
        $keyword = $request->query('keyword');
        if (!$keyword) {
            return response()->json(['message' => 'No keyword provided.', 'characters' => []], 400);
        }
        $characters = Character::where(function ($query) use ($keyword) {
            $query->where('pinyin', 'LIKE', "%{$keyword}%")
                ->orWhere('character', 'LIKE', "%{$keyword}%")
                ->orWhere('radical', 'LIKE', "%{$keyword}%");
        })->get();
        return response()->json([
            'message' => 'Characters retrieved successfully.',
            'characters' => $characters
        ]);
    }

    public function getCharacterDetail(Character $character)
    {
        return response()->json([
            'message'    => 'Character retrieved successfully.',
            'character'  => $character
        ]);
    }
}
