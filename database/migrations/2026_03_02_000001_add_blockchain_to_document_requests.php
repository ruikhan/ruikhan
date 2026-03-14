<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('document_requests', function (Blueprint $table) {
            $table->string('blockchain_tx_hash', 70)->nullable()->after('workflow_history');
            $table->string('blockchain_document_hash', 64)->nullable()->after('blockchain_tx_hash');
            $table->timestamp('blockchain_anchored_at')->nullable()->after('blockchain_document_hash');
            $table->string('blockchain_network', 30)->nullable()->after('blockchain_anchored_at');
            $table->string('blockchain_status', 20)->nullable()->default('pending')->after('blockchain_network');
            $table->index('blockchain_tx_hash');
        });
    }

    public function down(): void
    {
        Schema::table('document_requests', function (Blueprint $table) {
            $table->dropColumn([
                'blockchain_tx_hash',
                'blockchain_document_hash',
                'blockchain_anchored_at',
                'blockchain_network',
                'blockchain_status',
            ]);
        });
    }
};