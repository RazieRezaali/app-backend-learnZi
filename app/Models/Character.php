<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Character extends Model
{
    protected $fillable = [
        'character',
        'pinyin',
        'definition',
        'traditional',
        'stroke_count',
        'radical',
        'frequency_rank',
        'hsk_level'
    ];
}
