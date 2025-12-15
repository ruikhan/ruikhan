<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('document_requests', function (Blueprint $table) {

            $table->string('tracking_code')->after('id');
            $table->string('category')->default('personal');
            $table->string('business_name')->nullable();
            $table->string('tin_number')->nullable();
            $table->string('civil_status');
            $table->integer('years_of_residency');
            $table->string('contact_number');
            
            // ❌ REMOVED: $table->text('purpose'); (Already exists in previous migration)
            
            $table->string('valid_id_path')->nullable();
        });
    }

    public function down()
    {
        Schema::table('document_requests', function (Blueprint $table) {
            $table->dropColumn([
                'tracking_code',
                'category', 
                'business_name', 
                'tin_number', 
                'civil_status', 
                'years_of_residency', 
                'contact_number', 
                'valid_id_path'
            ]);
        });
    }
};