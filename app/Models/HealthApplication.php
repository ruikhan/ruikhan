<?php
// ── FILE: app/Models/HealthApplication.php ───────────────────────
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HealthApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'tracking_code', 'application_type',
        'application_data', 'attachments', 'status',
        'scheduled_at', 'assigned_doctor', 'clinic_location',
        'assistance_amount', 'admin_note', 'rejection_reason',
        'workflow_history', 'reviewed_by', 'reviewed_at',
    ];

    protected $casts = [
        'application_data' => 'array',
        'attachments'      => 'array',
        'workflow_history' => 'array',
        'scheduled_at'     => 'datetime',
        'reviewed_at'      => 'datetime',
    ];

    public function user()     { return $this->belongsTo(User::class); }
    public function reviewer() { return $this->belongsTo(User::class, 'reviewed_by'); }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $prefix = match($model->application_type) {
                'Medical Assistance'      => 'MED',
                'Free Consultation'       => 'CONS',
                'Medicine Request'        => 'MEDS',
                'Laboratory Request'      => 'LAB',
                'Mental Health Support'   => 'MH',
                default                   => 'HLTH',
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
}


// ── FILE: app/Models/EnvironmentReport.php ───────────────────────
// (Create as a separate file)
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EnvironmentReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'tracking_code', 'report_type', 'severity',
        'location', 'latitude', 'longitude',
        'report_data', 'attachments', 'status',
        'admin_note', 'action_taken', 'resolution_date',
        'workflow_history', 'assigned_to', 'acknowledged_at',
    ];

    protected $casts = [
        'report_data'      => 'array',
        'attachments'      => 'array',
        'workflow_history' => 'array',
        'resolution_date'  => 'date',
        'acknowledged_at'  => 'datetime',
        'latitude'         => 'decimal:7',
        'longitude'        => 'decimal:7',
    ];

    public function user()       { return $this->belongsTo(User::class); }
    public function assignedTo() { return $this->belongsTo(User::class, 'assigned_to'); }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->tracking_code = 'ENV-' . now()->format('Ymd') . '-' . strtoupper(substr(md5(uniqid()), 0, 4));
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

    public function getSeverityColorAttribute(): string
    {
        return match($this->severity) {
            'low'      => 'emerald',
            'moderate' => 'amber',
            'high'     => 'orange',
            'critical' => 'red',
            default    => 'slate',
        };
    }
}