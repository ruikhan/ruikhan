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

    // ✅ MAGIC HAPPENS HERE
    // This automatically converts the JSON database column into a PHP Array
    protected $casts = [
        'data' => 'array',
        'attachments' => 'array',
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
            // Generates codes like: MCR-20251219-X7Y2 or BRGY-20251219-X7Y2
            $prefix = self::getDepartmentPrefix($model->department);
            $model->tracking_code = $prefix . '-' . now()->format('Ymd') . '-' . strtoupper(substr(md5(uniqid()), 0, 4));
        });
    }

    // Helper to get short code for tracking
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
            'Barangay Certifications' => 'BRGY', // ✅ NEW
        ];
        return $map[$dept] ?? 'REQ';
    }
}