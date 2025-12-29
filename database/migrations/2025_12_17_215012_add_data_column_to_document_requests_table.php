<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('document_requests', function (Blueprint $table) {
            
            // ❌ REMOVED: tracking_code (already in create migration)
            // ❌ REMOVED: department (already in create migration)
            // ❌ REMOVED: data (already in create migration as json)

            // 3. Add Document Type only if missing (alias for service_type)
            if (!Schema::hasColumn('document_requests', 'document_type')) {
                $table->string('document_type')->nullable()->after('service_type');
            }

            // 5. Add User Remarks if missing
            if (!Schema::hasColumn('document_requests', 'user_remarks')) {
                $table->text('user_remarks')->nullable();
            }
            
            // Note: status and service_type already exist in create migration
        });
    }

    public function down()
    {
        Schema::table('document_requests', function (Blueprint $table) {
            if (Schema::hasColumn('document_requests', 'document_type')) {
                $table->dropColumn('document_type');
            }
            if (Schema::hasColumn('document_requests', 'user_remarks')) {
                $table->dropColumn('user_remarks');
            }
        });
    }
};