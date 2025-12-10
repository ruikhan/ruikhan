<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillPayment extends Model
{
    use HasFactory;

    // THIS IS THE MISSING PART
    protected $fillable = [
        'user_id',
        'biller_name',
        'account_number',
        'amount',
        'reference_code',
        'status'
    ];

    // Link back to the user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}