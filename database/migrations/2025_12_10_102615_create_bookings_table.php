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
    Schema::create('bookings', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade'); // The Resident
        $table->foreignId('establishment_id')->constrained()->onDelete('cascade'); // The Shop
        $table->string('service_type'); // e.g., "Table Reservation", "Food Delivery", "Room Booking"
        $table->dateTime('scheduled_at'); // When do they want it?
        $table->text('notes')->nullable(); // Special instructions (e.g. "No onions")
        $table->string('status')->default('pending'); // pending, confirmed, cancelled
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
        Schema::dropIfExists('bookings');
    }
};
