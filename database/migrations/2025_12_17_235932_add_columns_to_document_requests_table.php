<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('document_requests', function (Blueprint $table) {
            // 1. The missing column causing your error
            // We use 'text' or 'string' to store the file path
            $table->string('attachments')->nullable()->after('data');

            // 2. These are used in the Admin Controller we updated earlier
            // If you already have 'admin_note', you can ignore this or rename it
            if (!Schema::hasColumn('document_requests', 'admin_remarks')) {
                $table->text('admin_remarks')->nullable()->after('status');
            }
            
            if (!Schema::hasColumn('document_requests', 'appointment_date')) {
                $table->dateTime('appointment_date')->nullable()->after('admin_remarks');
            }
        });
    }

    public function down()
    {
        Schema::table('document_requests', function (Blueprint $table) {
            $table->dropColumn(['attachments', 'admin_remarks', 'appointment_date']);
        });
    }
};