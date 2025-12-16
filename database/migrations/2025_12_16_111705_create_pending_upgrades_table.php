<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pending_upgrades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('business_id')->constrained()->onDelete('cascade');
            $table->string('current_tier');
            $table->string('target_tier');
            $table->string('payment_reference')->unique();
            $table->decimal('amount', 10, 2);
            $table->string('screenshot_path')->nullable();
            $table->string('transaction_id')->nullable();
            $table->enum('status', ['pending', 'pending_verification', 'approved', 'rejected'])->default('pending');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pending_upgrades');
    }
};