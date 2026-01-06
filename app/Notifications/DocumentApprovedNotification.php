<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

// ============================================================================
// 1. DOCUMENT REQUEST NOTIFICATIONS
// ============================================================================

class DocumentApprovedNotification extends Notification
{
    use Queueable;

    public $documentType;
    public $documentId;

    public function __construct($documentType, $documentId)
    {
        $this->documentType = $documentType;
        $this->documentId = $documentId;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'approval',
            'title' => 'Document Approved! ✅',
            'message' => "Your {$this->documentType} has been approved and is ready for pickup.",
            'body' => "Your {$this->documentType} has been approved and is ready for pickup.",
            'url' => "/request/{$this->documentId}/journey",
            'icon' => '✅',
            'priority' => 'high'
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'type' => 'approval',
            'title' => 'Document Approved! ✅',
            'message' => "Your {$this->documentType} has been approved.",
            'url' => "/request/{$this->documentId}/journey"
        ]);
    }
}

class DocumentProcessingNotification extends Notification
{
    use Queueable;

    public $documentType;
    public $documentId;
    public $currentStep;

    public function __construct($documentType, $documentId, $currentStep)
    {
        $this->documentType = $documentType;
        $this->documentId = $documentId;
        $this->currentStep = $currentStep;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'update',
            'title' => 'Document Update 📄',
            'message' => "Your {$this->documentType} is now at: {$this->currentStep}",
            'body' => "Your {$this->documentType} is now at: {$this->currentStep}",
            'url' => "/request/{$this->documentId}/journey",
            'icon' => '📄'
        ];
    }
}

class DocumentRejectedNotification extends Notification
{
    use Queueable;

    public $documentType;
    public $documentId;
    public $reason;

    public function __construct($documentType, $documentId, $reason)
    {
        $this->documentType = $documentType;
        $this->documentId = $documentId;
        $this->reason = $reason;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'rejection',
            'title' => 'Action Required ❌',
            'message' => "Your {$this->documentType} needs revision: {$this->reason}",
            'body' => "Your {$this->documentType} needs revision: {$this->reason}",
            'url' => "/request/{$this->documentId}/journey",
            'icon' => '❌',
            'priority' => 'high'
        ];
    }
}

// ============================================================================
// 2. EMERGENCY NOTIFICATIONS
// ============================================================================

class EmergencyAlertNotification extends Notification
{
    use Queueable;

    public $alertType;
    public $message;
    public $location;

    public function __construct($alertType, $message, $location)
    {
        $this->alertType = $alertType;
        $this->message = $message;
        $this->location = $location;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'emergency',
            'title' => "🚨 Emergency Alert: {$this->alertType}",
            'message' => "{$this->message} - Location: {$this->location}",
            'body' => "{$this->message} - Location: {$this->location}",
            'url' => '/emergency',
            'icon' => '🚨',
            'priority' => 'urgent',
            'broadcast' => 'all'
        ];
    }
}

class EmergencyResponseNotification extends Notification
{
    use Queueable;

    public $emergencyId;
    public $status;

    public function __construct($emergencyId, $status)
    {
        $this->emergencyId = $emergencyId;
        $this->status = $status;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'info',
            'title' => 'Emergency Response Update',
            'message' => "Your emergency report status: {$this->status}. Help is on the way!",
            'body' => "Your emergency report status: {$this->status}",
            'url' => '/emergency',
            'icon' => 'ℹ️',
            'priority' => 'high'
        ];
    }
}

// ============================================================================
// 3. PAYMENT NOTIFICATIONS
// ============================================================================

class PaymentReceivedNotification extends Notification
{
    use Queueable;

    public $amount;
    public $billType;
    public $receiptNumber;

    public function __construct($amount, $billType, $receiptNumber)
    {
        $this->amount = $amount;
        $this->billType = $billType;
        $this->receiptNumber = $receiptNumber;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'payment',
            'title' => 'Payment Received 💰',
            'message' => "₱{$this->amount} payment for {$this->billType} confirmed. Receipt: #{$this->receiptNumber}",
            'body' => "₱{$this->amount} payment confirmed",
            'url' => '/bills/history',
            'icon' => '💰',
            'priority' => 'normal'
        ];
    }
}

class BillDueReminderNotification extends Notification
{
    use Queueable;

    public $billType;
    public $amount;
    public $dueDate;

    public function __construct($billType, $amount, $dueDate)
    {
        $this->billType = $billType;
        $this->amount = $amount;
        $this->dueDate = $dueDate;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'alert',
            'title' => 'Bill Due Reminder ⚠️',
            'message' => "Your {$this->billType} of ₱{$this->amount} is due on {$this->dueDate}",
            'body' => "Your {$this->billType} is due soon",
            'url' => '/bills/pay',
            'icon' => '⚠️',
            'priority' => 'high'
        ];
    }
}

// ============================================================================
// 4. BUSINESS & MARKETPLACE NOTIFICATIONS
// ============================================================================

class BusinessApprovedNotification extends Notification
{
    use Queueable;

    public $businessName;

    public function __construct($businessName)
    {
        $this->businessName = $businessName;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'success',
            'title' => 'Business Registration Approved! ✨',
            'message' => "Congratulations! {$this->businessName} has been approved. You can now start selling.",
            'body' => "{$this->businessName} has been approved",
            'url' => '/business/dashboard',
            'icon' => '✨',
            'priority' => 'high'
        ];
    }
}

class OrderReceivedNotification extends Notification
{
    use Queueable;

    public $orderId;
    public $productName;
    public $buyerName;

    public function __construct($orderId, $productName, $buyerName)
    {
        $this->orderId = $orderId;
        $this->productName = $productName;
        $this->buyerName = $buyerName;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'message',
            'title' => 'New Order Received! 🛍️',
            'message' => "{$this->buyerName} ordered your {$this->productName}. Order #{$this->orderId}",
            'body' => "New order for {$this->productName}",
            'url' => '/business/dashboard',
            'icon' => '💬',
            'priority' => 'high'
        ];
    }
}

// ============================================================================
// 5. HEALTH & APPOINTMENTS
// ============================================================================

class AppointmentConfirmedNotification extends Notification
{
    use Queueable;

    public $appointmentDate;
    public $service;

    public function __construct($appointmentDate, $service)
    {
        $this->appointmentDate = $appointmentDate;
        $this->service = $service;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'approval',
            'title' => 'Appointment Confirmed ✅',
            'message' => "Your {$this->service} appointment is confirmed for {$this->appointmentDate}",
            'body' => "Appointment confirmed for {$this->appointmentDate}",
            'url' => '/health',
            'icon' => '✅',
            'priority' => 'high'
        ];
    }
}

class AppointmentReminderNotification extends Notification
{
    use Queueable;

    public $appointmentDate;
    public $service;

    public function __construct($appointmentDate, $service)
    {
        $this->appointmentDate = $appointmentDate;
        $this->service = $service;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'alert',
            'title' => 'Appointment Reminder 📅',
            'message' => "Don't forget your {$this->service} appointment tomorrow at {$this->appointmentDate}",
            'body' => "Appointment reminder for {$this->service}",
            'url' => '/health',
            'icon' => '⚠️',
            'priority' => 'normal'
        ];
    }
}

// ============================================================================
// 6. POLLS & COMMUNITY
// ============================================================================

class NewPollNotification extends Notification
{
    use Queueable;

    public $pollTitle;
    public $pollId;

    public function __construct($pollTitle, $pollId)
    {
        $this->pollTitle = $pollTitle;
        $this->pollId = $pollId;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'info',
            'title' => 'New Community Poll 📊',
            'message' => "New poll available: \"{$this->pollTitle}\". Cast your vote now!",
            'body' => "New poll: {$this->pollTitle}",
            'url' => '/polls',
            'icon' => 'ℹ️',
            'priority' => 'normal',
            'broadcast' => 'all'
        ];
    }
}

class AnnouncementNotification extends Notification
{
    use Queueable;

    public $title;
    public $message;

    public function __construct($title, $message)
    {
        $this->title = $title;
        $this->message = $message;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'info',
            'title' => "📢 {$this->title}",
            'message' => $this->message,
            'body' => $this->message,
            'url' => '/dashboard',
            'icon' => 'ℹ️',
            'priority' => 'normal',
            'broadcast' => 'all'
        ];
    }
}