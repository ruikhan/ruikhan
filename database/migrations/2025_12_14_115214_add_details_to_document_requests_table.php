<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('document_requests', function (Blueprint $table) {
            // ❌ REMOVED: tracking_code (already exists in create migration)
            
            // Add only the NEW columns that don't exist in the original table
            if (!Schema::hasColumn('document_requests', 'category')) {
                $table->string('category')->default('personal');
            }
            
            if (!Schema::hasColumn('document_requests', 'business_name')) {
                $table->string('business_name')->nullable();
            }
            
            if (!Schema::hasColumn('document_requests', 'tin_number')) {
                $table->string('tin_number')->nullable();
            }
            
            if (!Schema::hasColumn('document_requests', 'civil_status')) {
                $table->string('civil_status');
            }
            
            if (!Schema::hasColumn('document_requests', 'years_of_residency')) {
                $table->integer('years_of_residency');
            }
            
            if (!Schema::hasColumn('document_requests', 'contact_number')) {
                $table->string('contact_number');
            }
            
            if (!Schema::hasColumn('document_requests', 'valid_id_path')) {
                $table->string('valid_id_path')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('document_requests', function (Blueprint $table) {
            // Only drop columns that this migration added (NOT tracking_code)
            $columns = [
                'category', 
                'business_name', 
                'tin_number', 
                'civil_status', 
                'years_of_residency', 
                'contact_number', 
                'valid_id_path'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('document_requests', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};