<?php

namespace App\Traits;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

trait TraitCkeditor
{
    public function getDescription($description){
        $pattern = '/<img[^>]+src="data:image\/([a-zA-Z]*);base64,([^\"]+)"/';
        preg_match_all($pattern, $description, $matches);
        foreach ($matches[0] as $index => $base64Image) {
            $base64String = $matches[2][$index];
            $imageName = 'image_' . time() . '_' . Str::random(10) . '.png';
            $imageData = base64_decode($base64String);
            $path = Storage::disk('public')->put('description-images/' . $imageName, $imageData);
            $imageUrl = asset('storage/description-images/' . $imageName);
            $description = str_replace($base64Image, '<img src="' . $imageUrl . '" />', $description);
        }
        return $description;
    }
}
