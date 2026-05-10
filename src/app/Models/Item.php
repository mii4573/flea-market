<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_id',
        'name',
        'brand_name',
        'price',
        'description',
        'image_path',
        'condition',
    ];

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function likedByUsers()
    {
       return $this->belongsToMany(User::class, 'likes','item_id','user_id'); 
    }

    public function purchase()
    {
       return $this->hasOne(Purchase::class); 
    }

    public function isSold(): bool
    {
       return $this->purchase()->exists();
    }

    public function category()
    {
       return $this->belongsTo(Category::class); 
    }

}
