<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    protected $fillable = [
        'character_id',
        'category_id',
        'description',
    ];

    public function category() 
    {
        return $this->belongsTo(Category::class);
    }

    public function character() 
    {
        return $this->belongsTo(Character::class);
    }
}
