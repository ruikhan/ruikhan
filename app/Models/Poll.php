<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Poll extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'status', 'end_date'];

    // A poll has many options
    public function options()
    {
        return $this->hasMany(PollOption::class);
    }

    // A poll has many votes
    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    // Check if the current logged-in user has already voted
    public function getHasVotedAttribute()
    {
        return $this->votes()->where('user_id', Auth::id())->exists();
    }
}