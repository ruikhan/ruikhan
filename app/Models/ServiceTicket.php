<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ticket_number',
        'department',
        'location',
        'issue',
        'status',
        'photo_path'
    ];

    // Relationship: A ticket belongs to a User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}