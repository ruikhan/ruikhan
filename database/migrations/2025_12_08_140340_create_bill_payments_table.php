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
    Schema::create('bill_payments', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained(); // Who paid?
        
        $table->string('biller_name');    // e.g. Meralco, Maynilad
        $table->string('account_number'); // The user's account ID
        $table->decimal('amount', 10, 2); // Money (e.g., 1500.50)
        
        $table->string('reference_code')->unique(); // Unique Receipt ID
        $table->string('status'); // 'successful' or 'failed'
        
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
        Schema::dropIfExists('bill_payments');
    }
};
