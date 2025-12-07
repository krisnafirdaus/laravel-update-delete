<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'price',
        'stock',
    ];

    // cast dipkae modifikasi table ke data
    protected $casts = [
        'name' => 'string',
        'description' => 'string', // varahcer, string, text
        'price' => 'integer',
        'stock' => 'integer',
        'deleted_at' => 'datetime',
    ];

    protected $guarded = [
        'id',
    ];
}
