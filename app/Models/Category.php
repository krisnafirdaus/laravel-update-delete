<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description'
    ];

    protected static function boot()
    {
        parent::boot();

        // create category slug kosong maka akan auto generate dari name
        static::creating(function ($category) {
            if(empty($category->slug)){
                $category->slug = Str::slug($category->name);
            }
        });

        // update category slug kosong maka atau category name berubah akan auto generate dari name
        static::updating(function ($category) {
            if(empty($category->isDirty('name') && empty($category->slug))){
                $category->slug = Str::slug($category->name);
            }
        });
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function getPostsCountAttribute(): int
    {
        return $this->posts()->count();
    }
}
