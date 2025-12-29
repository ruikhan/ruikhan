<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('document_requests', function (Blueprint $table) {
            // 1. ADD MISSING COLUMNS
            if (!Schema::hasColumn('document_requests', 'attachments')) {
                $table->string('attachments')->nullable()->after('data');
            }
            if (!Schema::hasColumn('document_requests', 'appointment_date')) {
                $table->dateTime('appointment_date')->nullable()->after('status');
            }

            // 2. RELAX LEGACY COLUMNS (Make them nullable)
            $table->text('purpose')->nullable()->change();
            $table->string('civil_status')->nullable()->change();
            $table->string('contact_number')->nullable()->change();
            $table->integer('years_of_residency')->nullable()->change();
        });

        // 3. STANDARDIZE NAMING (Handle Duplicates Safely)
        // We do this in a separate block to ensure logic checks run correctly
        if (Schema::hasColumn('document_requests', 'admin_note')) {
            
            if (Schema::hasColumn('document_requests', 'admin_remarks')) {
                // Scenario A: Both exist. We don't need 'admin_note' anymore.
                Schema::table('document_requests', function (Blueprint $table) {
                    $table->dropColumn('admin_note');
                });
            } else {
                // Scenario B: Only 'admin_note' exists. Rename it.
                Schema::table('document_requests', function (Blueprint $table) {
                    $table->renameColumn('admin_note', 'admin_remarks');
                });
            }
        }
    }

    public function down()
    {
        Schema::table('document_requests', function (Blueprint $table) {
            // We can't easily undo the drops/renames because we don't know 
            // exactly which state it was in, but we can remove the new columns.
            $table->dropColumn(['attachments', 'appointment_date']);
        });
    }
};