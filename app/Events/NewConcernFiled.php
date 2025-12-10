<?php

namespace App\Events;

use App\Models\Concern;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewConcernFiled implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $concern;

    public function __construct(Concern $concern)
    {
        $this->concern = $concern;
    }

    public function broadcastOn()
    {
        return new Channel('admin-channel');
    }

    public function broadcastAs()
    {
        return 'concern.created';
    }
}