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
    Schema::create('polls', function (Blueprint $table) {
        $table->id();
        $table->string('title'); // e.g., "Budget Priority 2025"
        $table->text('description')->nullable();
        $table->string('status')->default('open'); // open, closed
        $table->dateTime('end_date')->nullable();
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
        Schema::dropIfExists('polls');
    }
};
