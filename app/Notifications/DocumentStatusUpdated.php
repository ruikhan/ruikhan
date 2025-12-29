<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;



class DocumentStatusUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    protected $document;
    protected $status;

    public function __construct($document, $status)
    {
        $this->document = $document;
        $this->status = $status;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => $this->getNotificationType(),
            'title' => $this->getTitle(),
            'message' => $this->getMessage(),
            'url' => route('services.show', $this->document->id),
            'document_id' => $this->document->id,
            'status' => $this->status
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'type' => $this->getNotificationType(),
            'title' => $this->getTitle(),
            'message' => $this->getMessage(),
            'url' => route('services.show', $this->document->id),
        ]);
    }

    protected function getNotificationType()
    {
        return match($this->status) {
            'approved' => 'approval',
            'rejected' => 'rejection',
            'processing' => 'update',
            default => 'info'
        };
    }

    protected function getTitle()
    {
        return match($this->status) {
            'approved' => 'Document Approved! ✅',
            'rejected' => 'Document Rejected',
            'processing' => 'Document Processing',
            default => 'Status Update'
        };
    }

    protected function getMessage()
    {
        return match($this->status) {
            'approved' => "Your {$this->document->document_type} has been approved and is ready for pickup.",
            'rejected' => "Your {$this->document->document_type} has been rejected. Please check for details.",
            'processing' => "Your {$this->document->document_type} is now being processed.",
            default => "Status updated for your {$this->document->document_type}."
        };
    }
}