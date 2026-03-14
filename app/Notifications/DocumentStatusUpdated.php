<?php

namespace App\Notifications;

use App\Models\DocumentRequest;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

// ✅ REMOVED: ShouldQueue + Queueable
// Render free tier has no queue worker — queued notifications sit in the
// jobs table forever and are never delivered. Sync delivery works instantly.
class DocumentStatusUpdated extends Notification
{
    protected $docRequest;
    protected $oldStatus;

    public function __construct(DocumentRequest $docRequest, $oldStatus = null)
    {
        $this->docRequest = $docRequest;
        $this->oldStatus  = $oldStatus;

        \Log::info('DocumentStatusUpdated notification created', [
            'request_id' => $docRequest->id,
            'old_status' => $oldStatus,
            'new_status' => $docRequest->status,
        ]);
    }

    /**
     * Delivery channels.
     * ✅ database only — no broadcast (requires Pusher) and no queue worker needed.
     */
    public function via($notifiable): array
    {
        return ['database'];
    }

    /**
     * Stored in the notifications table — read by NotificationCenter.vue polling.
     */
    public function toArray($notifiable): array
    {
        return [
            'type'             => 'document_status_update',
            'title'            => $this->getTitle(),
            'message'          => $this->getMessage(),
            'document_type'    => $this->docRequest->document_type,
            'tracking_code'    => $this->docRequest->tracking_code,
            'status'           => $this->docRequest->status,
            'status_label'     => $this->getStatusLabel($this->docRequest->status),
            'old_status'       => $this->oldStatus,
            'old_status_label' => $this->getStatusLabel($this->oldStatus),
            // ✅ FIXED: field is admin_note, not admin_remarks
            'admin_remarks'    => $this->docRequest->admin_note,
            'appointment_date' => $this->docRequest->appointment_date,
            'department'       => $this->docRequest->department,
            'url'              => route('request.show', $this->docRequest->id),
            'icon'             => $this->getStatusIcon($this->docRequest->status),
            'created_at'       => now()->toISOString(),
        ];
    }

    /**
     * Optional mail — only sent when mail driver is properly configured.
     */
    public function toMail($notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject($this->getTitle())
            ->greeting("Hello {$notifiable->name},")
            ->line($this->getMessage());

        if ($this->docRequest->admin_note) {
            $mail->line("**Admin Remarks:** {$this->docRequest->admin_note}");
        }

        if ($this->docRequest->appointment_date && $this->docRequest->status === 'ready_for_pickup') {
            $mail->line("**Pickup Date:** " . date('F j, Y g:i A', strtotime($this->docRequest->appointment_date)));
        }

        return $mail
            ->action('View Request Details', route('request.show', $this->docRequest->id))
            ->line('Thank you for using the E-PILI portal!');
    }

    protected function getTitle(): string
    {
        return match($this->docRequest->status) {
            'processing'       => '🔄 Document Request In Progress',
            'ready_for_pickup' => '✅ Document Ready for Pickup!',
            'completed'        => '🎉 Document Request Completed',
            'rejected'         => '❌ Document Request Rejected',
            default            => '📄 Document Request Updated',
        };
    }

    protected function getMessage(): string
    {
        $docType      = $this->docRequest->document_type;
        $trackingCode = $this->docRequest->tracking_code;

        return match($this->docRequest->status) {
            'processing'       => "Your {$docType} request (#{$trackingCode}) is now being processed.",
            'ready_for_pickup' => "Your {$docType} (#{$trackingCode}) is ready for pickup. Please check the appointment details.",
            'completed'        => "Your {$docType} request (#{$trackingCode}) has been completed successfully.",
            'rejected'         => "Your {$docType} request (#{$trackingCode}) was rejected. Please review the admin remarks.",
            default            => "Your {$docType} request (#{$trackingCode}) status updated to: " . $this->getStatusLabel($this->docRequest->status),
        };
    }

    protected function getStatusLabel(?string $status): string
    {
        if (!$status) return 'Unknown';
        return match($status) {
            'pending'          => 'Pending Review',
            'processing'       => 'Processing',
            'ready_for_pickup' => 'Ready for Pickup',
            'completed'        => 'Completed',
            'rejected'         => 'Rejected',
            default            => ucfirst(str_replace('_', ' ', $status)),
        };
    }

    protected function getStatusIcon(string $status): string
    {
        return match($status) {
            'pending'          => '⏳',
            'processing'       => '🔄',
            'ready_for_pickup' => '✅',
            'completed'        => '🎉',
            'rejected'         => '❌',
            default            => '📄',
        };
    }
}