<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check database driver for compatibility
        $driver = DB::connection()->getDriverName();
        
        if ($driver === 'sqlite') {
            // SQLite: Recreate table strategy (SQLite doesn't support MODIFY)
            Schema::table('document_requests', function (Blueprint $table) {
                // Add new column temporarily
                $table->string('status_new')->default('pending')->after('status');
            });
            
            // Copy data
            DB::table('document_requests')->update([
                'status_new' => DB::raw('status')
            ]);
            
            // Drop old, rename new
            Schema::table('document_requests', function (Blueprint $table) {
                $table->dropColumn('status');
            });
            
            Schema::table('document_requests', function (Blueprint $table) {
                $table->renameColumn('status_new', 'status');
            });
            
        } else {
            // MySQL/PostgreSQL: Use native ENUM or column modification
            if ($driver === 'mysql') {
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
            } else {
                // PostgreSQL or others: Use ALTER COLUMN
                Schema::table('document_requests', function (Blueprint $table) {
                    $table->string('status')->default('pending')->change();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::connection()->getDriverName();
        
        if ($driver === 'mysql') {
            DB::statement("
                ALTER TABLE document_requests 
                MODIFY COLUMN status ENUM(
                    'pending', 
                    'approved', 
                    'rejected'
                ) DEFAULT 'pending'
            ");
        } else {
            Schema::table('document_requests', function (Blueprint $table) {
                $table->string('status')->default('pending')->change();
            });
        }
    }
};