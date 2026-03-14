<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── 1. SOCIAL AID APPLICATIONS ────────────────────────────────
        Schema::create('social_aid_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('tracking_code')->unique();
            $table->string('program_type');            // 'Student Assistance', 'Senior Citizen', 'Calamity Aid', 'PWD Assistance', 'Solo Parent'
            $table->string('program_title');
            $table->json('application_data');          // form fields specific to each program
            $table->json('attachments')->nullable();   // uploaded requirements
            $table->string('status')->default('pending'); // pending, under_review, approved, rejected, released
            $table->decimal('approved_amount', 10, 2)->nullable();
            $table->date('release_date')->nullable();
            $table->string('release_venue')->nullable();
            $table->text('admin_note')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->json('workflow_history')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });

        // ── 2. HEALTH APPLICATIONS ────────────────────────────────────
        Schema::create('health_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('tracking_code')->unique();
            $table->string('application_type');        // 'Medical Assistance', 'Free Consultation', 'Medicine Request', 'Laboratory Request', 'Mental Health Support'
            $table->json('application_data');          // patient info, symptoms, requirements
            $table->json('attachments')->nullable();
            $table->string('status')->default('pending'); // pending, under_review, approved, scheduled, completed, rejected
            $table->datetime('scheduled_at')->nullable();  // when to come to health center
            $table->string('assigned_doctor')->nullable();
            $table->string('clinic_location')->nullable();
            $table->decimal('assistance_amount', 10, 2)->nullable();
            $table->text('admin_note')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->json('workflow_history')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });

        // ── 3. ENVIRONMENT REPORTS ────────────────────────────────────
        Schema::create('environment_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('tracking_code')->unique();
            $table->string('report_type');             // 'Illegal Dumping', 'Air Pollution', 'Water Contamination', 'Flood Hazard', 'Deforestation', 'Noise Pollution', 'Other'
            $table->string('severity');                // 'low', 'moderate', 'high', 'critical'
            $table->string('location');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->json('report_data');               // description, affected_area, estimated_persons, etc.
            $table->json('attachments')->nullable();   // photo evidence
            $table->string('status')->default('pending'); // pending, acknowledged, investigating, resolved, dismissed
            $table->text('admin_note')->nullable();
            $table->text('action_taken')->nullable();
            $table->date('resolution_date')->nullable();
            $table->json('workflow_history')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('acknowledged_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('environment_reports');
        Schema::dropIfExists('health_applications');
        Schema::dropIfExists('social_aid_applications');
    }
};