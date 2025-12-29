<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('document_requests', function (Blueprint $table) {
            // Check for attachments column before adding
            if (!Schema::hasColumn('document_requests', 'attachments')) {
                $table->string('attachments')->nullable()->after('data');
            }

            // Admin remarks
            if (!Schema::hasColumn('document_requests', 'admin_remarks')) {
                $table->text('admin_remarks')->nullable()->after('status');
            }
            
            // Appointment date
            if (!Schema::hasColumn('document_requests', 'appointment_date')) {
                $table->dateTime('appointment_date')->nullable()->after('admin_remarks');
            }
        });
    }

    public function down()
    {
        Schema::table('document_requests', function (Blueprint $table) {
            // Check each column exists before dropping
            if (Schema::hasColumn('document_requests', 'attachments')) {
                $table->dropColumn('attachments');
            }
            
            if (Schema::hasColumn('document_requests', 'admin_remarks')) {
                $table->dropColumn('admin_remarks');
            }
            
            if (Schema::hasColumn('document_requests', 'appointment_date')) {
                $table->dropColumn('appointment_date');
            }
        });
    }
};