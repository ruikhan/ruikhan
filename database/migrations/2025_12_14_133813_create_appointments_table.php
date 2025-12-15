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
    Schema::create('appointments', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained(); // The Patient
        $table->foreignId('doctor_id')->constrained(); // The Provider
        $table->dateTime('scheduled_at'); // Date and Time
        $table->string('type'); // 'Consultation', 'Check-up', 'Emergency'
        $table->text('symptoms')->nullable(); // Patient notes
        $table->string('status')->default('pending'); // pending, confirmed, completed, cancelled
        $table->string('visit_link')->nullable(); // For Telehealth
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
        Schema::dropIfExists('appointments');
    }
};
