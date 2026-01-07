<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if table exists first
        if (!Schema::hasTable('document_requests')) {
            // Create the entire table if it doesn't exist
            Schema::create('document_requests', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('document_type');
                $table->text('purpose')->nullable();
                $table->string('status')->default('pending');
                $table->text('admin_note')->nullable()->comment('Admin notes about the document');
                $table->json('attachments')->nullable()->comment('JSON array of file paths');
                $table->longText('admin_signature')->nullable()->comment('Base64 encoded signature image');
                $table->timestamp('admin_signature_date')->nullable()->comment('When admin signed');
                $table->longText('user_signature')->nullable()->comment('Base64 encoded signature image');
                $table->timestamp('user_signature_date')->nullable()->comment('When user signed');
                $table->json('workflow_history')->nullable()->comment('JSON array of workflow status changes');
                $table->timestamps();
            });
        } else {
            // Table exists, add missing columns
            Schema::table('document_requests', function (Blueprint $table) {
                // First, add admin_note if it doesn't exist
                if (!Schema::hasColumn('document_requests', 'admin_note')) {
                    $table->text('admin_note')->nullable()->comment('Admin notes about the document');
                }

                // Add attachments if missing
                if (!Schema::hasColumn('document_requests', 'attachments')) {
                    $table->json('attachments')->nullable()->after('purpose')->comment('JSON array of file paths');
                }

                // Add admin signature if missing
                if (!Schema::hasColumn('document_requests', 'admin_signature')) {
                    $table->longText('admin_signature')->nullable()->comment('Base64 encoded signature image');
                }

                // Add admin signature date if missing
                if (!Schema::hasColumn('document_requests', 'admin_signature_date')) {
                    $table->timestamp('admin_signature_date')->nullable()->comment('When admin signed');
                }

                // Add user signature if missing
                if (!Schema::hasColumn('document_requests', 'user_signature')) {
                    $table->longText('user_signature')->nullable()->comment('Base64 encoded signature image');
                }

                // Add user signature date if missing
                if (!Schema::hasColumn('document_requests', 'user_signature_date')) {
                    $table->timestamp('user_signature_date')->nullable()->comment('When user signed');
                }

                // Add workflow history if missing
                if (!Schema::hasColumn('document_requests', 'workflow_history')) {
                    $table->json('workflow_history')->nullable()->comment('JSON array of workflow status changes');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('document_requests', function (Blueprint $table) {
            $columns = [
                'admin_note',
                'attachments',
                'admin_signature',
                'admin_signature_date',
                'user_signature',
                'user_signature_date',
                'workflow_history'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('document_requests', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};