<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentRequest extends Model
{
    use HasFactory;

    // ✅ ADD THE NEW COLUMNS HERE
    protected $fillable = [
        'user_id',
        'tracking_code',      // <--- New
        'document_type',
        'category',           // <--- New
        'purpose',
        'civil_status',       // <--- New
        'years_of_residency', // <--- New
        'contact_number',     // <--- New
        'business_name',      // <--- New
        'tin_number',         // <--- New
        'valid_id_path',      // <--- New
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}