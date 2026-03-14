<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Aiven MySQL enforces sql_require_primary_key=ON globally.
        // Disable it for this session so UUID primary keys work correctly.
        if (DB::getDriverName() === 'mysql') { DB::statement('SET SESSION sql_require_primary_key=0'); }

        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        // Restore the setting for subsequent migrations in this session
        if (DB::getDriverName() === 'mysql') { DB::statement('SET SESSION sql_require_primary_key=1'); }
    }

    public function down()
    {
        Schema::dropIfExists('notifications');
    }
};