<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class NotificationSeeder extends Seeder
{
    public function run()
    {
        $userId = 1; // Change to your test user ID
        
        $notifications = [
            // Document Approved
            [
                'id' => Str::uuid(),
                'type' => 'App\\Notifications\\DocumentApprovedNotification',
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => $userId,
                'data' => json_encode([
                    'type' => 'approval',
                    'title' => 'Document Approved! ✅',
                    'message' => 'Your Barangay Clearance has been approved and is ready for pickup.',
                    'body' => 'Your Barangay Clearance has been approved.',
                    'url' => '/request/1/journey',
                    'icon' => '✅',
                    'priority' => 'high'
                ]),
                'read_at' => null,
                'created_at' => Carbon::now()->subMinutes(5),
                'updated_at' => Carbon::now()->subMinutes(5),
            ],
            
            // Document Processing
            [
                'id' => Str::uuid(),
                'type' => 'App\\Notifications\\DocumentProcessingNotification',
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => $userId,
                'data' => json_encode([
                    'type' => 'update',
                    'title' => 'Document Update 📄',
                    'message' => 'Your Certificate of Indigency is now at: Review & Approval',
                    'body' => 'Your document is being reviewed.',
                    'url' => '/request/2/journey',
                    'icon' => '📄'
                ]),
                'read_at' => Carbon::now()->subMinutes(2),
                'created_at' => Carbon::now()->subHours(2),
                'updated_at' => Carbon::now()->subMinutes(2),
            ],
            
            // Payment Received
            [
                'id' => Str::uuid(),
                'type' => 'App\\Notifications\\PaymentReceivedNotification',
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => $userId,
                'data' => json_encode([
                    'type' => 'payment',
                    'title' => 'Payment Received 💰',
                    'message' => '₱150.00 payment for Community Tax confirmed. Receipt: #BR-2026-001',
                    'body' => '₱150.00 payment confirmed',
                    'url' => '/bills/history',
                    'icon' => '💰',
                    'priority' => 'normal'
                ]),
                'read_at' => null,
                'created_at' => Carbon::now()->subHours(5),
                'updated_at' => Carbon::now()->subHours(5),
            ],
            
            // Bill Due Reminder
            [
                'id' => Str::uuid(),
                'type' => 'App\\Notifications\\BillDueReminderNotification',
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => $userId,
                'data' => json_encode([
                    'type' => 'alert',
                    'title' => 'Bill Due Reminder ⚠️',
                    'message' => 'Your Water Bill of ₱345.00 is due on January 15, 2026',
                    'body' => 'Your Water Bill is due soon',
                    'url' => '/bills/pay',
                    'icon' => '⚠️',
                    'priority' => 'high'
                ]),
                'read_at' => null,
                'created_at' => Carbon::now()->subDay(),
                'updated_at' => Carbon::now()->subDay(),
            ],
            
            // Emergency Alert
            [
                'id' => Str::uuid(),
                'type' => 'App\\Notifications\\EmergencyAlertNotification',
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => $userId,
                'data' => json_encode([
                    'type' => 'emergency',
                    'title' => '🚨 Emergency Alert: Fire',
                    'message' => 'Fire reported at Purok 3. Please stay alert and avoid the area.',
                    'body' => 'Fire reported at Purok 3',
                    'url' => '/emergency',
                    'icon' => '🚨',
                    'priority' => 'urgent',
                    'broadcast' => 'all'
                ]),
                'read_at' => Carbon::now()->subMinutes(30),
                'created_at' => Carbon::now()->subHours(1),
                'updated_at' => Carbon::now()->subMinutes(30),
            ],
            
            // Business Approved
            [
                'id' => Str::uuid(),
                'type' => 'App\\Notifications\\BusinessApprovedNotification',
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => $userId,
                'data' => json_encode([
                    'type' => 'success',
                    'title' => 'Business Registration Approved! ✨',
                    'message' => 'Congratulations! Gloria General Store has been approved. You can now start selling.',
                    'body' => 'Gloria General Store has been approved',
                    'url' => '/business/dashboard',
                    'icon' => '✨',
                    'priority' => 'high'
                ]),
                'read_at' => null,
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now()->subDays(2),
            ],
            
            // Appointment Confirmed
            [
                'id' => Str::uuid(),
                'type' => 'App\\Notifications\\AppointmentConfirmedNotification',
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => $userId,
                'data' => json_encode([
                    'type' => 'approval',
                    'title' => 'Appointment Confirmed ✅',
                    'message' => 'Your Medical Check-up appointment is confirmed for January 10, 2026 at 9:00 AM',
                    'body' => 'Appointment confirmed for January 10, 2026',
                    'url' => '/health',
                    'icon' => '✅',
                    'priority' => 'high'
                ]),
                'read_at' => null,
                'created_at' => Carbon::now()->subDays(3),
                'updated_at' => Carbon::now()->subDays(3),
            ],
            
            // New Poll
            [
                'id' => Str::uuid(),
                'type' => 'App\\Notifications\\NewPollNotification',
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => $userId,
                'data' => json_encode([
                    'type' => 'info',
                    'title' => 'New Community Poll 📊',
                    'message' => 'New poll available: "What infrastructure project should we prioritize this year?". Cast your vote now!',
                    'body' => 'New poll: Infrastructure priorities',
                    'url' => '/polls',
                    'icon' => 'ℹ️',
                    'priority' => 'normal',
                    'broadcast' => 'all'
                ]),
                'read_at' => Carbon::now()->subHours(6),
                'created_at' => Carbon::now()->subDays(1),
                'updated_at' => Carbon::now()->subHours(6),
            ],
            
            // Order Received
            [
                'id' => Str::uuid(),
                'type' => 'App\\Notifications\\OrderReceivedNotification',
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => $userId,
                'data' => json_encode([
                    'type' => 'message',
                    'title' => 'New Order Received! 🛍️',
                    'message' => 'Juan Dela Cruz ordered your Fresh Vegetables. Order #ORD-001',
                    'body' => 'New order for Fresh Vegetables',
                    'url' => '/business/dashboard',
                    'icon' => '💬',
                    'priority' => 'high'
                ]),
                'read_at' => null,
                'created_at' => Carbon::now()->subMinutes(15),
                'updated_at' => Carbon::now()->subMinutes(15),
            ],
            
            // Announcement
            [
                'id' => Str::uuid(),
                'type' => 'App\\Notifications\\AnnouncementNotification',
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => $userId,
                'data' => json_encode([
                    'type' => 'info',
                    'title' => '📢 Barangay Assembly Meeting',
                    'message' => 'Monthly barangay assembly this Saturday, January 11 at 2:00 PM. All residents are encouraged to attend.',
                    'body' => 'Barangay assembly this Saturday',
                    'url' => '/dashboard',
                    'icon' => 'ℹ️',
                    'priority' => 'normal',
                    'broadcast' => 'all'
                ]),
                'read_at' => null,
                'created_at' => Carbon::now()->subHours(12),
                'updated_at' => Carbon::now()->subHours(12),
            ],
        ];

        DB::table('notifications')->insert($notifications);
        
        $this->command->info('✅ Sample notifications created successfully!');
    }
}