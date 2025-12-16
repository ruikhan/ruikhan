<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'business_id',
        'tier',        // 'basic', 'standard', 'premium'
        'price_paid',
        'starts_at',
        'expires_at',
    ];

    // Automatically convert these columns to Carbon date objects
    protected $casts = [
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'price_paid' => 'decimal:2',
    ];

    // Relationship: A Subscription belongs to a Business
    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    // Helper: Is this subscription currently valid?
    public function isValid()
    {
        return $this->expires_at->isFuture();
    }
}