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
    Schema::create('poll_options', function (Blueprint $table) {
        $table->id();
        $table->foreignId('poll_id')->constrained()->onDelete('cascade');
        $table->string('option_text'); // e.g., "Education", "Roads"
        $table->integer('votes_count')->default(0); // Quick counter
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
        Schema::dropIfExists('poll_options');
    }
};
