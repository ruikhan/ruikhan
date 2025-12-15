<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Check if table exists to avoid conflicts
        if (!Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->id();
                $table->string('name');       // For 'citizen', 'admin', etc.
                $table->string('guard_name'); // For 'web'
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('roles');
    }
};
