<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'specialization',
        'avatar',
        'availability'
    ];

    // 1. Automatically convert the database JSON to a PHP Array
    protected $casts = [
        'availability' => 'array',
    ];

    // 2. Relationship: A doctor has many appointments
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
}