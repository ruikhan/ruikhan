<?php
// ── FILE: app/Models/SocialAidApplication.php ────────────────────
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SocialAidApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'tracking_code', 'program_type', 'program_title',
        'application_data', 'attachments', 'status',
        'approved_amount', 'release_date', 'release_venue',
        'admin_note', 'rejection_reason', 'workflow_history',
        'reviewed_by', 'reviewed_at',
    ];

    protected $casts = [
        'application_data' => 'array',
        'attachments'      => 'array',
        'workflow_history' => 'array',
        'release_date'     => 'date',
        'reviewed_at'      => 'datetime',
    ];

    public function user()     { return $this->belongsTo(User::class); }
    public function reviewer() { return $this->belongsTo(User::class, 'reviewed_by'); }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $prefix = match($model->program_type) {
                'Student Assistance'  => 'STUD',
                'Senior Citizen'      => 'SC',
                'Calamity Aid'        => 'CAL',
                'PWD Assistance'      => 'PWD',
                'Solo Parent'         => 'SP',
                default               => 'AID',
            };
            $model->tracking_code = $prefix . '-' . now()->format('Ymd') . '-' . strtoupper(substr(md5(uniqid()), 0, 4));
        });
    }

    public function addWorkflowEntry(string $status, ?string $note = null): void
    {
        $history   = $this->workflow_history ?? [];
        $history[] = [
            'status'     => $status,
            'admin_id'   => auth()->id(),
            'admin_name' => auth()->user()?->name ?? 'System',
            'timestamp'  => now()->toIso8601String(),
            'note'        => $note,
        ];
        $this->workflow_history = $history;
    }

    public function getApplicantNameAttribute(): string
    {
        return $this->application_data['full_name']
            ?? $this->application_data['applicant_name']
            ?? $this->user?->name
            ?? 'N/A';
    }
}