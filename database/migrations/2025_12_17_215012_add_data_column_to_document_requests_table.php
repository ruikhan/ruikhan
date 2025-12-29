<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('document_requests', function (Blueprint $table) {
            
            // 1. Add Tracking Code (Essential for the new system)
            if (!Schema::hasColumn('document_requests', 'tracking_code')) {
                $table->string('tracking_code')->nullable()->after('id');
            }

            // 2. Add Department (Essential for sorting requests)
            if (!Schema::hasColumn('document_requests', 'department')) {
                $table->string('department')->nullable()->after('user_id');
            }

            // 3. Add Document Type (e.g., "Birth Certificate")
            if (!Schema::hasColumn('document_requests', 'document_type')) {
                $table->string('document_type')->nullable()->after('department');
            }

            // 4. Add the MAGIC JSON COLUMN (Holds tin_number, mothers_name, etc.)
            if (!Schema::hasColumn('document_requests', 'data')) {
                $table->json('data')->nullable()->after('document_type');
            }

            // 5. Add Remarks/Status if missing
            if (!Schema::hasColumn('document_requests', 'user_remarks')) {
                $table->text('user_remarks')->nullable();
            }
            if (!Schema::hasColumn('document_requests', 'status')) {
                $table->string('status')->default('pending');
            }
            
            // 6. Support for the old "service_type" just in case, to prevent crashes
            if (!Schema::hasColumn('document_requests', 'service_type')) {
                $table->string('service_type')->nullable();
            }
        });
    }

    public function down()
    {
        // We rarely need to reverse this in development, 
        // but this keeps it valid.
        Schema::table('document_requests', function (Blueprint $table) {
            $table->dropColumn(['data', 'department', 'document_type']);
        });
    }
};