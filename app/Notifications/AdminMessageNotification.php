<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class AdminMessageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $message;
    protected $title;
    protected $url;

    public function __construct($title, $message, $url = null)
    {
        $this->title = $title;
        $this->message = $message;
        $this->url = $url;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'message',
            'title' => $this->title,
            'message' => $this->message,
            'url' => $this->url,
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'type' => 'message',
            'title' => $this->title,
            'message' => $this->message,
            'url' => $this->url,
        ]);
    }
    public function broadcastMessage(Request $request)
{
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'message' => 'required|string',
        'url' => 'nullable|url'
    ]);

    // Send to all users
    $users = User::all();
    
    foreach ($users as $user) {
        $user->notify(
            new AdminMessageNotification(
                $validated['title'],
                $validated['message'],
                $validated['url'] ?? null
            )
        );
    }

    return redirect()->back()->with('success', 'Message broadcasted to all users!');
}
}