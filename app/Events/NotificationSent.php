<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationSent implements ShouldBroadcastNow // Use ShouldBroadcastNow for instant delivery
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $notification;

    public function __construct($user, $notification)
    {
        $this->user = $user;
        $this->notification = $notification;
    }

    // Broadcast to specific user or all users
    public function broadcastOn()
    {
        $channels = [];

        // If user is specified, send to their private channel
        if ($this->user) {
            $channels[] = new PrivateChannel('App.Models.User.' . $this->user->id);
        }
        
        // If broadcast to all, use public channel
        if (isset($this->notification['broadcast']) && $this->notification['broadcast'] === 'all') {
            $channels[] = new Channel('notifications');
        }

        return $channels;
    }

    // Data to broadcast
    public function broadcastWith()
    {
        return [
            'id' => uniqid('notif_'),
            'type' => 'App\Notifications\AdminNotification',
            'data' => $this->notification,
            'created_at' => now()->toISOString(),
            'read_at' => null,
            'userId' => $this->user?->id
        ];
    }

    // Event name
    public function broadcastAs()
    {
        return 'notification.sent';
    }
}