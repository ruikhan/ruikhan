<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketPrice extends Model
{
    use HasFactory;

    // THIS IS THE MISSING PART
    protected $fillable = [
        'item_name',
        'category',
        'price',
        'unit',
        'trend'
    ];
}