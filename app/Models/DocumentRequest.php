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
        'admin_note',
        'admin_signature',
        'admin_signature_date',
        'appointment_date',
        'workflow_history',
        // ✅ Blockchain fields
        'blockchain_tx_hash',
        'blockchain_document_hash',
        'blockchain_anchored_at',
        'blockchain_network',
        'blockchain_status',
    ];

protected $casts = [
    'data'                   => 'array',
    'attachments'            => 'array',
    'workflow_history'       => 'array', // ✅ ADD — stops json_decode errors
    'appointment_date'       => 'datetime',
    'blockchain_anchored_at' => 'datetime',
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

    private static function getDepartmentPrefix($dept)
    {
        $map = [
            'Municipal Civil Registrar' => 'MCR',
            'Municipal Health Office'   => 'MHO',
            'Business Permits (BPLO)'   => 'BPLO',
            'Engineering Office'        => 'ENG',
            'Agriculture Office'        => 'AGRI',
            'Social Welfare (MSWDO)'    => 'DSWD',
            'Treasurer\'s Office'       => 'TREAS',
            'Assessor\'s Office'        => 'ASSR',
            'MPDO'                      => 'MPDO',
            'MDRRMO'                    => 'DRRM',
            'Barangay Certifications'   => 'BRGY',
        ];
        return $map[$dept] ?? 'REQ';
    }

    // ✅ Blockchain helper — check if anchored on-chain
    public function isBlockchainAnchored(): bool
    {
        return !empty($this->blockchain_tx_hash) && $this->blockchain_status === 'anchored';
    }

    // ✅ Blockchain helper — get PolygonScan explorer URL
    public function getBlockchainExplorerUrlAttribute(): ?string
    {
        if (!$this->blockchain_tx_hash) return null;
        $base = $this->blockchain_network === 'polygon' 
            ? 'https://polygonscan.com/tx/' 
            : 'https://amoy.polygonscan.com/tx/';
        return $base . $this->blockchain_tx_hash;
    }

    // ── Existing methods below — all unchanged ────────────────────────────────

    public static function hasBarangayProfile($userId)
    {
        return self::where('user_id', $userId)
            ->where('department', 'Barangay Certifications')
            ->where('status', '!=', 'rejected')
            ->exists();
    }

    public static function getBarangayProfile($userId)
    {
        $request = self::where('user_id', $userId)
            ->where('department', 'Barangay Certifications')
            ->where('status', '!=', 'rejected')
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$request || empty($request->data)) return null;

        $profileFields = [
            'request_level', 'applicant_last_name', 'applicant_first_name',
            'applicant_middle_name', 'date_of_birth', 'age', 'sex',
            'civil_status', 'purok_street', 'barangay', 'municipality',
            'province', 'place_of_birth', 'residency_duration',
            'valid_id_type', 'cedula_number',
        ];

        return array_intersect_key($request->data, array_flip($profileFields));
    }

    public function hasSignature()
    {
        return isset($this->data['signature']) && !empty($this->data['signature']);
    }

    public function getSignatureAttribute()
    {
        return $this->data['signature'] ?? null;
    }

    public function getSignatureTimestampAttribute()
    {
        return $this->data['signature_timestamp'] ?? null;
    }

    public function isQuickSubmit()
    {
        return $this->hasSignature()
            && $this->department === 'Barangay Certifications'
            && !empty($this->data['purpose']);
    }

    public function getApplicantFullNameAttribute()
    {
        if (!isset($this->data['applicant_first_name'])) return 'N/A';
        $firstName  = $this->data['applicant_first_name'] ?? '';
        $middleName = $this->data['applicant_middle_name'] ?? '';
        $lastName   = $this->data['applicant_last_name'] ?? '';
        return trim("$firstName $middleName $lastName");
    }

    public function getApplicantAddressAttribute()
    {
        if (!isset($this->data['barangay'])) return 'N/A';
        $parts = array_filter([
            $this->data['purok_street'] ?? null,
            $this->data['barangay'] ?? null,
            $this->data['municipality'] ?? null,
            $this->data['province'] ?? null,
        ]);
        return implode(', ', $parts);
    }

    public static function countBarangayCertificates($userId)
    {
        return self::where('user_id', $userId)
            ->where('department', 'Barangay Certifications')
            ->where('status', '!=', 'rejected')
            ->count();
    }

    public static function getCertificateHistory($userId, $certificateType)
    {
        return self::where('user_id', $userId)
            ->where('department', 'Barangay Certifications')
            ->where('document_type', $certificateType)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function isProfileOutdated()
    {
        if ($this->department !== 'Barangay Certifications') return false;
        return $this->created_at->lt(now()->subMonths(6));
    }

    public function getAttachmentUrlAttribute()
    {
        return $this->attachments ? asset('storage/' . $this->attachments) : null;
    }

    public function hasAttachment()
    {
        return !empty($this->attachments);
    }

    public function scopeBarangayCertifications($query)
    {
        return $query->where('department', 'Barangay Certifications');
    }

    public function scopeActive($query)
    {
        return $query->where('status', '!=', 'rejected');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}