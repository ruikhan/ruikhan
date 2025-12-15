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
    Schema::create('market_prices', function (Blueprint $table) {
        $table->id();
        $table->string('item_name'); // e.g. "Galunggong"
        $table->string('category');  // e.g. "Fish", "Meat", "Vegetables"
        $table->decimal('price', 8, 2); // e.g. 240.00
        $table->string('unit')->default('kg'); // e.g. "kg", "pc"
        $table->enum('trend', ['up', 'down', 'stable'])->default('stable'); // For the arrow icon
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
        Schema::dropIfExists('market_prices');
    }
};
