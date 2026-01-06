<?php
// ============================================================================
// FILE 2: app/Notifications/DocumentStatusUpdated.php
// CREATE THIS NEW FILE
// ============================================================================

namespace App\Notifications;

use App\Models\DocumentRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;

class DocumentStatusUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    protected $docRequest;
    protected $oldStatus;

    public function __construct(DocumentRequest $docRequest, $oldStatus = null)
    {
        $this->docRequest = $docRequest;
        $this->oldStatus = $oldStatus;
        
        // Log when notification is created
        \Log::info('DocumentStatusUpdated notification created', [
            'request_id' => $docRequest->id,
            'old_status' => $oldStatus,
            'new_status' => $docRequest->status,
        ]);
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        // Start with database, add broadcast when ready
        return ['database', 'broadcast'];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'type' => 'document_status_update',
            'title' => $this->getTitle(),
            'message' => $this->getMessage(),
            'document_type' => $this->docRequest->document_type,
            'tracking_code' => $this->docRequest->tracking_code,
            'status' => $this->docRequest->status,
            'status_label' => $this->getStatusLabel($this->docRequest->status),
            'old_status' => $this->oldStatus,
            'old_status_label' => $this->getStatusLabel($this->oldStatus),
            'admin_remarks' => $this->docRequest->admin_remarks,
            'appointment_date' => $this->docRequest->appointment_date,
            'department' => $this->docRequest->department,
            'url' => route('request.show', $this->docRequest->id),
            'icon' => $this->getStatusIcon($this->docRequest->status),
            'created_at' => now()->toISOString(),
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast($notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }

    /**
     * Get notification title based on status
     */
    protected function getTitle(): string
    {
        return match($this->docRequest->status) {
            'processing' => '🔄 Document Request In Progress',
            'ready_for_pickup' => '✅ Document Ready for Pickup!',
            'completed' => '🎉 Document Request Completed',
            'rejected' => '⚠️ Document Request Update',
            default => '📄 Document Request Updated'
        };
    }

    /**
     * Get notification message
     */
    protected function getMessage(): string
    {
        $docType = $this->docRequest->document_type;
        $trackingCode = $this->docRequest->tracking_code;
        
        return match($this->docRequest->status) {
            'processing' => "Your {$docType} request (#{$trackingCode}) is now being processed by our team.",
            'ready_for_pickup' => "Great news! Your {$docType} (#{$trackingCode}) is ready for pickup. Please check appointment details.",
            'completed' => "Your {$docType} request (#{$trackingCode}) has been successfully completed.",
            'rejected' => "Your {$docType} request (#{$trackingCode}) requires attention. Please review admin remarks for details.",
            default => "Your {$docType} request (#{$trackingCode}) status has been updated to: " . $this->getStatusLabel($this->docRequest->status)
        };
    }

    /**
     * Get human-readable status label
     */
    protected function getStatusLabel(?string $status): string
    {
        if (!$status) return 'Unknown';
        
        return match($status) {
            'pending' => 'Pending Review',
            'processing' => 'Processing',
            'ready_for_pickup' => 'Ready for Pickup',
            'completed' => 'Completed',
            'rejected' => 'Rejected',
            default => ucfirst(str_replace('_', ' ', $status))
        };
    }

    /**
     * Get icon for status
     */
    protected function getStatusIcon(string $status): string
    {
        return match($status) {
            'pending' => '⏳',
            'processing' => '🔄',
            'ready_for_pickup' => '✅',
            'completed' => '🎉',
            'rejected' => '❌',
            default => '📄'
        };
    }

    /**
     * Optional: Get mail representation of the notification.
     * Uncomment to enable email notifications
     */
    
    public function toMail($notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject($this->getTitle())
            ->greeting("Hello {$notifiable->name},")
            ->line($this->getMessage());

        if ($this->docRequest->admin_remarks) {
            $mail->line("**Admin Remarks:** {$this->docRequest->admin_remarks}");
        }

        if ($this->docRequest->appointment_date && $this->docRequest->status === 'ready_for_pickup') {
            $mail->line("**Pickup Date:** " . date('F j, Y g:i A', strtotime($this->docRequest->appointment_date)));
        }

        $mail->action('View Request Details', route('request.show', $this->docRequest->id))
             ->line('Thank you for using our document request system!');

        return $mail;
    }

}