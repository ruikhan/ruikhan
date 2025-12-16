<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'name',
        'description',
        'price',
        'stock',
        'category',
        'images',
        'is_available',
    ];

    protected $casts = [
        'images' => 'array',
        'is_available' => 'boolean',
        'price' => 'decimal:2',
    ];

    // Relationships
    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function likes()
    {
        return $this->hasMany(ProductLike::class);
    }

    public function reviews()
    {
        return $this->hasMany(ProductReview::class);
    }
    // Helper: Check if user liked this product
    public function isLikedBy($userId)
    {
        return $this->likes()->where('user_id', $userId)->exists();
    }

    // Helper: Get average rating
    public function averageRating()
    {
        return $this->reviews()->avg('rating') ?? 0;
    }
}