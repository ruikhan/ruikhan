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
    Schema::create('concerns', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        
        $table->string('category'); // e.g., 'Infrastructure', 'Health'
        $table->string('subject');  // Short title
        $table->text('description'); // Full details
        $table->string('location')->nullable(); // Address or Coordinates
        
        $table->string('evidence_image_path')->nullable(); // Path to uploaded photo
        
        $table->enum('status', ['open', 'investigating', 'resolved', 'closed'])->default('open');
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
        Schema::dropIfExists('concerns');
    }
};
