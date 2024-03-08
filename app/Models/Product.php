<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    public $fillable = ['name', 'description', 'price', 'priceBefore', 'image', 'user_id', 'category_id'];
    
    public $casts = [
        "category_id" => "integer",
        "price" => "float",
        "priceBefore" => "float",
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

}
