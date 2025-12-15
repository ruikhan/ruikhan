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
public function up(): void
{
    Schema::create('service_tickets', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained(); // Link to user
        $table->string('ticket_number')->unique(); // TKT-2023-XYZ
        $table->string('department'); // Engineering, Health, etc.
        $table->string('location'); // "Corner of Main St."
        $table->text('issue'); // "Big pothole here"
        $table->string('status')->default('open'); // open, in-progress, resolved
        $table->string('photo_path')->nullable(); // For uploaded evidence
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
        Schema::dropIfExists('service_tickets');
    }
};
