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
        // Link request to a specific resident (User)
        $table->foreignId('user_id')->constrained()->onDelete('cascade'); 
        
        $table->string('document_type'); // e.g., 'Barangay Clearance'
        $table->text('purpose');         // Reason for request
        
        // Status tracking
        $table->enum('status', ['pending', 'processing', 'ready_for_pickup', 'completed', 'rejected'])->default('pending');
        
        $table->text('admin_note')->nullable(); // Admin feedback (optional)
        $table->timestamps(); // Tracks created_at and updated_at
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
