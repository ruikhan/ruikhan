<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Concern extends Model
{
    use HasFactory;

    protected $fillable = [
    'user_id', 'category', 'subject', 'description', 'location', 'evidence_image_path', 'status'
];

public function user()
{
    return $this->belongsTo(User::class);
}
}
