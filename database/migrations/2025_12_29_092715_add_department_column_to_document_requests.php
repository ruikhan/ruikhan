<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('document_requests', function (Blueprint $table) {
            // 1. ADD THE MISSING 'department' COLUMN (This is what's causing the error!)
            if (!Schema::hasColumn('document_requests', 'department')) {
                $table->string('department')->after('tracking_code');
            }

            // 2. ADD OTHER MISSING COLUMNS
            if (!Schema::hasColumn('document_requests', 'attachments')) {
                $table->string('attachments')->nullable()->after('data');
            }
            if (!Schema::hasColumn('document_requests', 'appointment_date')) {
                $table->dateTime('appointment_date')->nullable()->after('status');
            }

            // 3. RELAX LEGACY COLUMNS (Make them nullable if they exist)
            if (Schema::hasColumn('document_requests', 'purpose')) {
                $table->text('purpose')->nullable()->change();
            }
            if (Schema::hasColumn('document_requests', 'civil_status')) {
                $table->string('civil_status')->nullable()->change();
            }
            if (Schema::hasColumn('document_requests', 'contact_number')) {
                $table->string('contact_number')->nullable()->change();
            }
            if (Schema::hasColumn('document_requests', 'years_of_residency')) {
                $table->integer('years_of_residency')->nullable()->change();
            }
        });

        // 4. STANDARDIZE NAMING (Handle admin_note/admin_remarks duplication)
        if (Schema::hasColumn('document_requests', 'admin_note')) {
            if (Schema::hasColumn('document_requests', 'admin_remarks')) {
                // Both exist - drop the old one
                Schema::table('document_requests', function (Blueprint $table) {
                    $table->dropColumn('admin_note');
                });
            } else {
                // Only admin_note exists - rename it
                Schema::table('document_requests', function (Blueprint $table) {
                    $table->renameColumn('admin_note', 'admin_remarks');
                });
            }
        }
    }

    public function down()
    {
        Schema::table('document_requests', function (Blueprint $table) {
            if (Schema::hasColumn('document_requests', 'department')) {
                $table->dropColumn('department');
            }
            if (Schema::hasColumn('document_requests', 'attachments')) {
                $table->dropColumn('attachments');
            }
            if (Schema::hasColumn('document_requests', 'appointment_date')) {
                $table->dropColumn('appointment_date');
            }
        });
    }
};