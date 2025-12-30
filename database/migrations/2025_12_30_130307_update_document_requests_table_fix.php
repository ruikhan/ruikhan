<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('document_requests', function (Blueprint $table) {
            
            // ✅ 1. Check and rename service_type to document_type
            if (Schema::hasColumn('document_requests', 'service_type') && 
                !Schema::hasColumn('document_requests', 'document_type')) {
                $table->renameColumn('service_type', 'document_type');
            }
            
            // ✅ 2. Add document_type if it doesn't exist
            if (!Schema::hasColumn('document_requests', 'document_type')) {
                $table->string('document_type')->after('department');
            }
            
            // ✅ 3. Add user_remarks only if it doesn't exist
            if (!Schema::hasColumn('document_requests', 'user_remarks')) {
                $table->text('user_remarks')->nullable()->after('attachments');
            }
        });
        
        // ✅ 4. Update status enum to include 'approved'
        // This safely updates the enum values
        DB::statement("
            ALTER TABLE document_requests 
            MODIFY COLUMN status ENUM(
                'pending', 
                'processing', 
                'approved', 
                'ready_for_pickup', 
                'completed', 
                'rejected'
            ) DEFAULT 'pending'
        ");
    }

    public function down(): void
    {
        Schema::table('document_requests', function (Blueprint $table) {
            
            // Reverse changes
            if (Schema::hasColumn('document_requests', 'document_type') && 
                !Schema::hasColumn('document_requests', 'service_type')) {
                $table->renameColumn('document_type', 'service_type');
            }
            
            if (Schema::hasColumn('document_requests', 'user_remarks')) {
                $table->dropColumn('user_remarks');
            }
        });
    }
};