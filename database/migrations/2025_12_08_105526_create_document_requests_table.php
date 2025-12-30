<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('document_requests', function (Blueprint $table) {
            $table->id();
            
            // 1. Who is asking?
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); 
            
            // 2. Tracking & Identification
            $table->string('tracking_code')->unique();
            $table->string('department');
            $table->string('document_type');  // ✅ FIXED: Changed from service_type
            
            // 3. The Magic Column (JSON data)
            $table->json('data'); 
            
            // 4. File Uploads (JSON array of paths)
            $table->json('attachments')->nullable(); 

            // 5. Status & Communication
            $table->enum('status', [
                'pending',
                'processing',
                'approved',           // ✅ ADDED
                'ready_for_pickup',
                'completed',
                'rejected'
            ])->default('pending');
            
            $table->text('user_remarks')->nullable();   // ✅ ADDED
            $table->text('admin_remarks')->nullable();
            $table->timestamp('appointment_date')->nullable();

            $table->timestamps();
            
            // Indexes for performance
            $table->index('user_id');
            $table->index('tracking_code');
            $table->index('status');
            $table->index('department');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_requests');
    }
};