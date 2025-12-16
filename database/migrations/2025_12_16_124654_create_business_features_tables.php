<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Store settings for the Business Owner
        Schema::create('business_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('plan_type')->default('standard'); // 'standard' or 'premium'
            $table->string('store_theme')->default('classic'); // 'classic', 'modern', 'neon'
            $table->string('gcash_qr_path')->nullable(); // Path to their uploaded receipt
            $table->timestamp('subscription_expires_at')->nullable();
            $table->timestamps();
        });

        // 2. Reviews/Feedbacks for Products
        // Schema::create('product_reviews', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('product_id'); // Assumes you have a products table
        //     $table->foreignId('user_id'); // The resident who commented
        //     $table->text('comment');
        //     $table->integer('rating')->default(5); // 1-5 stars
        //     $table->integer('likes_count')->default(0);
        //     $table->boolean('is_visible')->default(true);
        //     $table->timestamps();
        // });
        
        // 3. SMS Messages Log (For the communication bridge)
        Schema::create('sms_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id'); // Link to business_profiles
            $table->string('customer_phone');
            $table->text('message_body');
            $table->enum('direction', ['incoming', 'outgoing']); // Incoming from Resident, Outgoing from Owner
            $table->string('status')->default('sent');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sms_messages');
        Schema::dropIfExists('business_profiles');
    }
};