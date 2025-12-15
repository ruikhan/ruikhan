<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'establishment_id', 'service_type', 'scheduled_at', 'notes', 'status'];

// Optional: Relationships (so we can grab names later)
public function establishment() {
    return $this->belongsTo(Establishment::class);
}
public function user() {
    return $this->belongsTo(User::class);
}
}
