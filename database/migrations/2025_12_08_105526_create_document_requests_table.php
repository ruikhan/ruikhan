<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
public function up()
{
    Schema::create('document_requests', function (Blueprint $table) {
        $table->id();
        
        // 1. Who is asking?
        $table->foreignId('user_id')->constrained()->onDelete('cascade'); 
        
        // 2. Tracking & Identification
        $table->string('tracking_code')->unique(); // e.g., 'MCR-2025-8821'
        $table->string('department');              // e.g., 'Civil Registrar', 'Health Office'
        $table->string('service_type');            // e.g., 'Birth Certificate', 'Business Permit'
        
        // 3. The Magic Column (Stores all specific form fields)
        // This will hold Name, Father's Name, Farm Size, TIN, etc.
        $table->json('data'); 
        
        // 4. File Uploads (Stores paths to IDs, Plans, Sketch maps)
        $table->json('attachments')->nullable(); 

        // 5. Status & Admin Info
        $table->enum('status', ['pending', 'processing', 'ready_for_pickup', 'completed', 'rejected'])->default('pending');
        $table->text('admin_remarks')->nullable(); // Reason for rejection or pickup instructions
        $table->timestamp('appointment_date')->nullable(); // For scheduled inspections or interviews

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('document_requests');
    }
};
