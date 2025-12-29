<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number', // Added: Needed for registration
        'role',         // Added: Needed for 'resident', 'business_owner', etc.
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /* |--------------------------------------------------------------------------
    | Existing Relationships
    |--------------------------------------------------------------------------
    */

    public function documentRequests()
    {
        return $this->hasMany(DocumentRequest::class);
    }

    public function concerns()
    {
        return $this->hasMany(Concern::class);
    }

    public function jobPostings()
    {
        return $this->hasMany(JobPosting::class);
    }

    public function billPayments()
    {
        return $this->hasMany(BillPayment::class);
    }

    /* |--------------------------------------------------------------------------
    | New Business Module Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * A User owns ONE Business Profile
     */
    public function business()
    {
        return $this->hasOne(Business::class);
    }

    /**
     * A User can have many subscription records (history of payments)
     */
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    /* |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */
 public function receivesBroadcastNotificationsOn()
    {
        return 'App.Models.User.' . $this->id;
    }
    /**
     * Check if the user has a business profile
     */
    public function isBusinessOwner()
    {
        // Checks if role is set AND if they actually have a business record created
        return $this->role === 'business_owner' || $this->business()->exists();
    }

    /**
     * ✅ NEW: Check if user has an active subscription
     * This is used by the HasBusiness middleware
     */
    public function hasActiveSubscription()
    {
        // If user doesn't have a business, they can't have a subscription
        if (!$this->business) {
            return false;
        }

        // Get the most recent subscription
        $latestSubscription = $this->business->subscriptions()
            ->orderBy('created_at', 'desc')
            ->first();

        // Check if it exists and is still valid
        return $latestSubscription && $latestSubscription->isValid();
    }

    /**
     * ✅ NEW: Get the user's current subscription tier
     * Useful for conditional features (e.g., "Premium only" features)
     */
    public function currentSubscriptionTier()
    {
        // If user doesn't have a business, no tier
        if (!$this->business) {
            return null;
        }

        // Get the most recent ACTIVE subscription
        $latestSubscription = $this->business->subscriptions()
            ->where('expires_at', '>', now())
            ->orderBy('created_at', 'desc')
            ->first();

        return $latestSubscription?->tier; // Returns 'basic', 'standard', 'premium', or null
    }

    /**
     * ✅ BONUS: Get days remaining in current subscription
     */
    public function subscriptionDaysRemaining()
    {
        if (!$this->hasActiveSubscription()) {
            return 0;
        }

        $latestSubscription = $this->business->subscriptions()
            ->where('expires_at', '>', now())
            ->orderBy('created_at', 'desc')
            ->first();

        return $latestSubscription->expires_at->diffInDays(now());
    }

    /**
     * ✅ BONUS: Check if user has a specific tier
     * Usage: $user->hasTier('premium')
     */
    public function hasTier($tier)
    {
        return $this->currentSubscriptionTier() === $tier;
    }

    
}