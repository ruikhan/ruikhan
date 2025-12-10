<?php

namespace App\Events;

use App\Models\DocumentRequest;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast; // <--- IMPORTANT
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewDocumentRequest implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $request;

    // Receive the actual request data when the event is created
    public function __construct(DocumentRequest $request)
    {
        $this->request = $request;
    }

    // Determine on which channel the event should broadcast
    public function broadcastOn()
    {
        // We broadcast to a public channel named 'admin-channel'
        // In a real app with security, you might use a PrivateChannel
        return new Channel('admin-channel');
    }
    
    // Optional: Name the event something specific for the frontend
    public function broadcastAs()
    {
        return 'document.created';
    }
}