<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendingUpgrade extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'business_id',
        'current_tier',
        'target_tier',
        'payment_reference',
        'amount',
        'screenshot_path',
        'transaction_id',
        'status',
        'submitted_at',
        'approved_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}