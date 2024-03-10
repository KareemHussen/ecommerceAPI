<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    public $fillable = ['name', 'description', 'price', 'priceBefore', 'image', 'user_id', 'category_id' , 'live' , 'quantity' , 'special_offer' , 'daily_offer'];
    
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

    // The users had this product in their cart
    public function users()
    {
        return $this->belongsToMany(User::class , 'product_user')->withPivot('quantity');
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class , 'order_product')->withPivot('quantity');
    }

    public function scopeIsLive($query , bool $live)
    {
        return $query->where('live', $live);
    }

}
