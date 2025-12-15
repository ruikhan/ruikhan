<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangayCase extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'barangay_id',
        'respondent_name',
        'type',
        'narrative',
        'status',
        'stage'
    ];

    // Relationship: A case belongs to a User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}