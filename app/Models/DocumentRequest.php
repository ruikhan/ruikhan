<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tracking_code',
        'department',
        'document_type',
        'data',
        'attachments',
        'status',
        'user_remarks',
        'admin_remarks',
        'appointment_date'
    ];

    protected $casts = [
        'data' => 'array',
        'attachments' => 'array',  // ✅ ADD THIS LINE
        'appointment_date' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    // Auto-generate Tracking Code on creation
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            $prefix = self::getDepartmentPrefix($model->department);
            $model->tracking_code = $prefix . '-' . now()->format('Ymd') . '-' . strtoupper(substr(md5(uniqid()), 0, 4));
        });
    }

    private static function getDepartmentPrefix($dept) {
        $map = [
            'Municipal Civil Registrar' => 'MCR',
            'Municipal Health Office' => 'MHO',
            'Business Permits (BPLO)' => 'BPLO',
            'Engineering Office' => 'ENG',
            'Agriculture Office' => 'AGRI',
            'Social Welfare (MSWDO)' => 'DSWD',
            'Treasurer’s Office' => 'TREAS',
            'Assessor’s Office' => 'ASSR',
            'MPDO' => 'MPDO',
            'MDRRMO' => 'DRRM',
            'Barangay Certifications' => 'BRGY',
        ];
        return $map[$dept] ?? 'REQ';
    }

    // ✅ NEW: Check if user has previous barangay certification
    public static function hasBarangayProfile($userId)
    {
        return self::where('user_id', $userId)
            ->where('department', 'Barangay Certifications')
            ->where('status', '!=', 'rejected')
            ->exists();
    }

    // ✅ NEW: Get user's barangay profile data
    public static function getBarangayProfile($userId)
    {
        $request = self::where('user_id', $userId)
            ->where('department', 'Barangay Certifications')
            ->where('status', '!=', 'rejected')
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$request || empty($request->data)) {
            return null;
        }

        // Extract only profile fields (reusable across certificates)
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

        return array_intersect_key(
            $request->data,
            array_flip($profileFields)
        );
    }

    // ✅ NEW: Check if request has digital signature
    public function hasSignature()
    {
        return isset($this->data['signature']) && !empty($this->data['signature']);
    }

    // ✅ NEW: Get signature data URL
    public function getSignatureAttribute()
    {
        return $this->data['signature'] ?? null;
    }

    // ✅ NEW: Get signature timestamp
    public function getSignatureTimestampAttribute()
    {
        return $this->data['signature_timestamp'] ?? null;
    }

    // ✅ NEW: Check if this is a quick submit request (used existing profile)
    public function isQuickSubmit()
    {
        return $this->hasSignature() && 
               $this->department === 'Barangay Certifications' &&
               !empty($this->data['purpose']); // Has minimal fields
    }

    // ✅ NEW: Get full applicant name from data
    public function getApplicantFullNameAttribute()
    {
        if (!isset($this->data['applicant_first_name'])) {
            return 'N/A';
        }

        $firstName = $this->data['applicant_first_name'] ?? '';
        $middleName = $this->data['applicant_middle_name'] ?? '';
        $lastName = $this->data['applicant_last_name'] ?? '';

        return trim("$firstName $middleName $lastName");
    }

    // ✅ NEW: Get applicant address from data
    public function getApplicantAddressAttribute()
    {
        if (!isset($this->data['barangay'])) {
            return 'N/A';
        }

        $parts = array_filter([
            $this->data['purok_street'] ?? null,
            $this->data['barangay'] ?? null,
            $this->data['municipality'] ?? null,
            $this->data['province'] ?? null
        ]);

        return implode(', ', $parts);
    }

    // ✅ NEW: Count user's barangay certificate requests
    public static function countBarangayCertificates($userId)
    {
        return self::where('user_id', $userId)
            ->where('department', 'Barangay Certifications')
            ->where('status', '!=', 'rejected')
            ->count();
    }

    // ✅ NEW: Get user's request history for specific certificate type
    public static function getCertificateHistory($userId, $certificateType)
    {
        return self::where('user_id', $userId)
            ->where('department', 'Barangay Certifications')
            ->where('document_type', $certificateType)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    // ✅ NEW: Check if profile data needs updating (older than 6 months)
    public function isProfileOutdated()
    {
        if ($this->department !== 'Barangay Certifications') {
            return false;
        }

        return $this->created_at->lt(now()->subMonths(6));
    }

    // EXISTING: Helper method to get full attachment URL
    public function getAttachmentUrlAttribute()
    {
        return $this->attachments 
            ? asset('storage/' . $this->attachments)
            : null;
    }

    // EXISTING: Check if request has attachment
    public function hasAttachment()
    {
        return !empty($this->attachments);
    }

    // ✅ NEW: Scope for barangay certifications
    public function scopeBarangayCertifications($query)
    {
        return $query->where('department', 'Barangay Certifications');
    }

    // ✅ NEW: Scope for active requests (not rejected)
    public function scopeActive($query)
    {
        return $query->where('status', '!=', 'rejected');
    }

    // ✅ NEW: Scope for user's requests
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}