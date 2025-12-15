<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'doctor_id',
        'scheduled_at',
        'type',
        'symptoms',
        'status',
        'visit_link'
    ];

    // 1. Automatically convert these columns to Carbon Date objects
    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    // 2. Relationship: An appointment belongs to a Doctor
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    // 3. Relationship: An appointment belongs to a User (Patient)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}