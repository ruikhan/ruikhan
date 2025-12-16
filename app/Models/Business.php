<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'category',       // 'retail' or 'service'
        'status',         // 'available', 'open', 'closed', 'full'
        'is_verified',
        'theme_settings', // JSON field for dashboard customization
    ];

    // Automatically convert the JSON database column to a PHP array
    protected $casts = [
        'theme_settings' => 'array',
        'is_verified' => 'boolean',
    ];

    // Relationship: A Business belongs to one Owner (User)
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relationship: A Business has many Subscription records (history)
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    // ✅ ADD THIS - Relationship: A Business has many Products
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    // Helper: Check if the business is currently open
    public function isOpen()
    {
        return $this->status === 'open';
    }
}