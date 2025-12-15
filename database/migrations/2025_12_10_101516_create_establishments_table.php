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
    Schema::create('establishments', function (Blueprint $table) {
        $table->id();
        $table->string('name');             // e.g. "Jollibee Pili"
        $table->string('type');             // e.g. "food", "hotel", "service", "transport"
        $table->string('location');         // e.g. "Old Albay Road"
        $table->string('contact_number')->nullable();
        $table->string('image')->nullable(); // URL to the shop's photo
        $table->text('description')->nullable();
        $table->string('status')->default('open'); // open, closed
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
        Schema::dropIfExists('establishments');
    }
};
