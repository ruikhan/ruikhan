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
    Schema::create('job_postings', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade'); // The Business Owner
        
        $table->string('company_name');
        $table->string('job_title');
        $table->text('description');
        $table->string('salary_range')->nullable(); // e.g., '15k - 20k'
        $table->string('location');
        
        $table->boolean('is_active')->default(true); // Is the job still open?
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
        Schema::dropIfExists('job_postings');
    }
};
