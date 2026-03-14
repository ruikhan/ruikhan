<?php
// ── FILE: app/Notifications/ApplicationStatusNotification.php ────
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApplicationStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly string $section,       // 'social_aid' | 'health' | 'environment'
        public readonly string $trackingCode,
        public readonly string $status,
        public readonly string $programTitle,
        public readonly ?string $adminNote      = null,
        public readonly ?string $releaseDate    = null,
        public readonly ?string $releaseVenue   = null,
        public readonly ?string $scheduledAt    = null,
        public readonly ?string $assignedDoctor = null,
        public readonly ?string $actionTaken    = null,
    ) {}

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    // ── DATABASE ─────────────────────────────────────────────────
    public function toDatabase($notifiable): array
    {
        return [
            'section'        => $this->section,
            'tracking_code'  => $this->trackingCode,
            'status'         => $this->status,
            'program_title'  => $this->programTitle,
            'admin_note'     => $this->adminNote,
            'release_date'   => $this->releaseDate,
            'release_venue'  => $this->releaseVenue,
            'scheduled_at'   => $this->scheduledAt,
            'assigned_doctor'=> $this->assignedDoctor,
            'action_taken'   => $this->actionTaken,
            'title'          => $this->buildTitle(),
            'message'        => $this->buildMessage(),
            'icon'           => $this->sectionIcon(),
            'color'          => $this->statusColor(),
        ];
    }

    // ── EMAIL ─────────────────────────────────────────────────────
    public function toMail($notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('[E-PILI] ' . $this->buildTitle())
            ->greeting('Hello, ' . ($notifiable->name ?? 'Resident') . '!')
            ->line($this->buildMessage());

        // Release / scheduling details
        if ($this->releaseDate && $this->releaseVenue) {
            $mail->line('📅 **Release Date:** ' . $this->releaseDate)
                 ->line('📍 **Venue:** ' . $this->releaseVenue);
        }
        if ($this->scheduledAt && $this->assignedDoctor) {
            $mail->line('📅 **Appointment:** ' . $this->scheduledAt)
                 ->line('👨‍⚕️ **Doctor:** ' . $this->assignedDoctor);
        }
        if ($this->adminNote) {
            $mail->line('📝 **Note from Admin:** ' . $this->adminNote);
        }
        if ($this->actionTaken) {
            $mail->line('✅ **Action Taken:** ' . $this->actionTaken);
        }

        return $mail
            ->line('Tracking Code: **' . $this->trackingCode . '**')
            ->action('View Application Status', url('/dashboard'))
            ->line('Thank you for using E-PILI Digital Governance System.');
    }

    // ── Helpers ───────────────────────────────────────────────────
    private function buildTitle(): string
    {
        return match($this->status) {
            'approved'      => $this->programTitle . ' — Application Approved ✅',
            'rejected'      => $this->programTitle . ' — Application Rejected',
            'under_review'  => $this->programTitle . ' — Under Review 🔍',
            'scheduled'     => $this->programTitle . ' — Appointment Scheduled 📅',
            'released'      => $this->programTitle . ' — Assistance Released 🎉',
            'acknowledged'  => 'Report Acknowledged 👁',
            'investigating' => 'Report Being Investigated 🔍',
            'resolved'      => 'Report Resolved ✅',
            'dismissed'     => 'Report Dismissed',
            default         => $this->programTitle . ' — Status Updated',
        };
    }

    private function buildMessage(): string
    {
        return match($this->status) {
            'approved'      => "Your application for {$this->programTitle} has been APPROVED. Please proceed to the Municipal Hall on the scheduled release date to claim your assistance.",
            'rejected'      => "We regret to inform you that your application for {$this->programTitle} has not been approved at this time. " . ($this->adminNote ? "Reason: {$this->adminNote}" : ''),
            'under_review'  => "Your application for {$this->programTitle} is currently being reviewed by our team. We will notify you once a decision has been made.",
            'scheduled'     => "Your health application has been approved. Please come to the health center on the scheduled date.",
            'released'      => "Your assistance for {$this->programTitle} has been successfully released. Thank you for availing our social services.",
            'acknowledged'  => "Your environment report ({$this->trackingCode}) has been received and acknowledged. We will investigate this matter promptly.",
            'investigating' => "Your environment report is currently being investigated by the Municipal Environment Office.",
            'resolved'      => "Your environment report has been resolved. " . ($this->actionTaken ? "Action taken: {$this->actionTaken}" : ''),
            'dismissed'     => "Your environment report has been reviewed but could not be acted upon at this time.",
            default         => "Your application status has been updated to: " . strtoupper($this->status) . ".",
        };
    }

    private function sectionIcon(): string
    {
        return match($this->section) {
            'social_aid'  => '🤝',
            'health'      => '🩺',
            'environment' => '🌿',
            default       => '📋',
        };
    }

    private function statusColor(): string
    {
        return match($this->status) {
            'approved', 'released', 'resolved', 'scheduled' => 'emerald',
            'rejected', 'dismissed'                          => 'red',
            'under_review', 'investigating', 'acknowledged'  => 'blue',
            default                                          => 'slate',
        };
    }
}