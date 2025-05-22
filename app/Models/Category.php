<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'parent_id',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function descendants()
    {
        return $this->children()->with('descendants');
    }

    public function cards()
    {
        return $this->hasMany(Card::class);
    }

    public function childrenRecursive()
    {
        return $this->hasMany(Category::class, 'parent_id')
                    ->with(['childrenRecursive', 'cards.character']);
    }
    
}
