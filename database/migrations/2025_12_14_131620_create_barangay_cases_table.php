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
    Schema::create('barangay_cases', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained(); // Link to the user who filed it
        $table->unsignedBigInteger('barangay_id')->default(1); // Default to 1 for now
        $table->string('respondent_name'); // Who they are fighting with
        $table->string('type'); // 'Debt', 'Slander', etc.
        $table->text('narrative'); // The full story
        $table->string('status')->default('filed'); // filed, closed, settled
        $table->string('stage')->default('mediation_pending'); // mediation, conciliation
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
        Schema::dropIfExists('barangay_cases');
    }
};
