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
    Schema::create('users', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('email')->unique();
        
        // --- NEW FIELDS FOR YOUR PROVINCE SYSTEM ---
        $table->string('role')->default('resident'); // 'resident', 'admin', 'business_owner'
        $table->string('phone_number')->nullable();
        $table->string('address')->nullable();       // Important for "residents in province"
        $table->boolean('is_verified')->default(false); // To approve business owners/residents
        // -------------------------------------------

        $table->timestamp('email_verified_at')->nullable();
        $table->string('password');
        $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
};
