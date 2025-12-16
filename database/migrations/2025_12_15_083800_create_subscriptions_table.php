<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('business_id')->constrained()->onDelete('cascade');
            
            $table->enum('tier', ['basic', 'standard', 'premium']);
            $table->decimal('price_paid', 8, 2);
            
            // FIX: Changed 'timestamp' to 'dateTime' to fix MySQL 1067 Error
            $table->dateTime('starts_at');
            $table->dateTime('expires_at');
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('subscriptions');
    }
};