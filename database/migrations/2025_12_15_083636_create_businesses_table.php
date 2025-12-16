<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('businesses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // The owner
            
            $table->string('name');
            $table->text('description')->nullable();
            
            // Track 1: Retail/Resale, Track 2: Service/Hospitality
            $table->enum('category', ['retail', 'service']); 
            
            // Real-time status for the "Management Suite"
            $table->enum('status', ['available', 'open', 'closed', 'full'])->default('closed');
            
            // For the "Verified/VIP" badge logic
            $table->boolean('is_verified')->default(false);
            
            // For Standard/Premium users to save their custom layout/colors
            // Example: {"color": "blue", "layout": "grid"}
            $table->json('theme_settings')->nullable(); 
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('businesses');
    }
};