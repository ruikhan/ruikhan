<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobPosting extends Model
{
    use HasFactory;

    protected $fillable = [
    'user_id', 'company_name', 'job_title', 'description', 'salary_range', 'location', 'is_active'
];

public function user()
{
    return $this->belongsTo(User::class);
}
}
