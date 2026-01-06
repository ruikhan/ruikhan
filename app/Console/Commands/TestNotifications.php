<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\DocumentApprovedNotification;
use App\Notifications\PaymentReceivedNotification;
use App\Notifications\EmergencyAlertNotification;
use Illuminate\Console\Command;

class TestNotifications extends Command
{
    protected $signature = 'test:notifications {user_id=1}';
    protected $description = 'Send test notifications to a user';

    public function handle()
    {
        $userId = $this->argument('user_id');
        $user = User::find($userId);

        if (!$user) {
            $this->error("User with ID {$userId} not found!");
            return;
        }

        $this->info("Sending test notifications to {$user->name}...");

        // Send various notifications
        $user->notify(new DocumentApprovedNotification('Barangay Clearance', 1));
        $this->info('✅ Document Approval sent');

        sleep(1);

        $user->notify(new PaymentReceivedNotification(150.00, 'Community Tax', 'BR-2026-001'));
        $this->info('💰 Payment notification sent');

        sleep(1);

        $user->notify(new EmergencyAlertNotification('Fire', 'Fire reported at Purok 3', 'Purok 3, Gloria'));
        $this->info('🚨 Emergency alert sent');

        $this->info("\n✨ All test notifications sent successfully!");
    }
}