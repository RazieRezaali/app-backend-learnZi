<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    protected $table = 'levels';

    protected $fillable = [
        'name',
        'standard_name',
        'word_count',
        'label'
    ];

    public function users()
    {
        return $this->hasMany(UserMeta::class);
    }
}
