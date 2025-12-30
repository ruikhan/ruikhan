<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class BarangayProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'request_level',
        'applicant_last_name',
        'applicant_first_name',
        'applicant_middle_name',
        'date_of_birth',
        'age',
        'sex',
        'civil_status',
        'purok_street',
        'barangay',
        'municipality',
        'province',
        'place_of_birth',
        'residency_duration',
        'valid_id_type',
        'cedula_number',
        'is_verified',
        'verified_at',
        'verified_by',
        'usage_count',
        'last_used_at'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
        'last_used_at' => 'datetime',
        'usage_count' => 'integer',
        'age' => 'integer'
    ];

    // =========================================================================
    // RELATIONSHIPS
    // =========================================================================

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function documentRequests()
    {
        return $this->hasMany(DocumentRequest::class, 'user_id', 'user_id')
                    ->where('department', 'Barangay Certifications');
    }

    // =========================================================================
    // ACCESSORS
    // =========================================================================

    public function getFullNameAttribute()
    {
        return trim("{$this->applicant_first_name} {$this->applicant_middle_name} {$this->applicant_last_name}");
    }

    public function getFullAddressAttribute()
    {
        $parts = array_filter([
            $this->purok_street,
            $this->barangay,
            $this->municipality,
            $this->province
        ]);

        return implode(', ', $parts);
    }

    public function getAgeFromBirthdateAttribute()
    {
        return $this->date_of_birth 
            ? Carbon::parse($this->date_of_birth)->age 
            : $this->age;
    }

    // =========================================================================
    // METHODS
    // =========================================================================

    /**
     * Create or update profile from request data
     */
    public static function createOrUpdateFromRequest($userId, array $data)
    {
        $profileData = self::extractProfileFields($data);
        $profileData['user_id'] = $userId;

        return self::updateOrCreate(
            ['user_id' => $userId],
            $profileData
        );
    }

    /**
     * Extract only profile-related fields from request data
     */
    public static function extractProfileFields(array $data)
    {
        $profileFields = [
            'request_level',
            'applicant_last_name',
            'applicant_first_name',
            'applicant_middle_name',
            'date_of_birth',
            'age',
            'sex',
            'civil_status',
            'purok_street',
            'barangay',
            'municipality',
            'province',
            'place_of_birth',
            'residency_duration',
            'valid_id_type',
            'cedula_number'
        ];

        return array_intersect_key($data, array_flip($profileFields));
    }

    /**
     * Get profile data as array (for form pre-filling)
     */
    public function toFormData()
    {
        return [
            'request_level' => $this->request_level,
            'applicant_last_name' => $this->applicant_last_name,
            'applicant_first_name' => $this->applicant_first_name,
            'applicant_middle_name' => $this->applicant_middle_name,
            'date_of_birth' => $this->date_of_birth?->format('Y-m-d'),
            'age' => $this->age,
            'sex' => $this->sex,
            'civil_status' => $this->civil_status,
            'purok_street' => $this->purok_street,
            'barangay' => $this->barangay,
            'municipality' => $this->municipality,
            'province' => $this->province,
            'place_of_birth' => $this->place_of_birth,
            'residency_duration' => $this->residency_duration,
            'valid_id_type' => $this->valid_id_type,
            'cedula_number' => $this->cedula_number,
        ];
    }

    /**
     * Mark profile as used (increment counter, update timestamp)
     */
    public function markAsUsed()
    {
        $this->increment('usage_count');
        $this->update(['last_used_at' => now()]);
    }

    /**
     * Verify profile (admin action)
     */
    public function verify($verifierId)
    {
        $this->update([
            'is_verified' => true,
            'verified_at' => now(),
            'verified_by' => $verifierId
        ]);
    }

    /**
     * Check if profile needs updating (older than 6 months)
     */
    public function isOutdated()
    {
        return $this->updated_at->lt(now()->subMonths(6));
    }

    /**
     * Check if profile is recently updated (within 30 days)
     */
    public function isRecent()
    {
        return $this->updated_at->gt(now()->subDays(30));
    }

    /**
     * Get profile completeness percentage
     */
    public function getCompletenessPercentage()
    {
        $requiredFields = [
            'applicant_last_name',
            'applicant_first_name',
            'date_of_birth',
            'age',
            'sex',
            'civil_status',
            'barangay',
            'municipality',
            'province'
        ];

        $optionalFields = [
            'request_level',
            'applicant_middle_name',
            'purok_street',
            'place_of_birth',
            'residency_duration',
            'valid_id_type',
            'cedula_number'
        ];

        $requiredFilled = 0;
        foreach ($requiredFields as $field) {
            if (!empty($this->$field)) {
                $requiredFilled++;
            }
        }

        $optionalFilled = 0;
        foreach ($optionalFields as $field) {
            if (!empty($this->$field)) {
                $optionalFilled++;
            }
        }

        // Required fields are weighted 70%, optional 30%
        $requiredScore = ($requiredFilled / count($requiredFields)) * 70;
        $optionalScore = ($optionalFilled / count($optionalFields)) * 30;

        return round($requiredScore + $optionalScore);
    }

    // =========================================================================
    // SCOPES
    // =========================================================================

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeUnverified($query)
    {
        return $query->where('is_verified', false);
    }

    public function scopeOutdated($query)
    {
        return $query->where('updated_at', '<', now()->subMonths(6));
    }

    public function scopeForBarangay($query, $barangay)
    {
        return $query->where('barangay', $barangay);
    }

    public function scopeRecentlyUsed($query, $days = 30)
    {
        return $query->where('last_used_at', '>=', now()->subDays($days));
    }
}