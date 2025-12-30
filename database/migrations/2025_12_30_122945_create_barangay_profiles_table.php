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
        Schema::create('barangay_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Personal Information
            $table->string('request_level')->nullable();
            $table->string('applicant_last_name');
            $table->string('applicant_first_name');
            $table->string('applicant_middle_name')->nullable();
            $table->date('date_of_birth');
            $table->integer('age');
            $table->enum('sex', ['Male', 'Female']);
            $table->enum('civil_status', ['Single', 'Married', 'Widowed', 'Separated']);
            
            // Address Information
            $table->string('purok_street')->nullable();
            $table->string('barangay');
            $table->string('municipality');
            $table->string('province');
            $table->string('place_of_birth')->nullable();
            
            // Additional Information
            $table->string('residency_duration')->nullable();
            $table->string('valid_id_type')->nullable();
            $table->string('cedula_number')->nullable();
            
            // Metadata
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->integer('usage_count')->default(0); // Track how many times used
            $table->timestamp('last_used_at')->nullable();
            
            $table->timestamps();
            
            // Ensure one profile per user
            $table->unique('user_id');
            
            // Indexes for faster queries
            $table->index(['barangay', 'municipality']);
            $table->index('is_verified');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barangay_profiles');
    }
};