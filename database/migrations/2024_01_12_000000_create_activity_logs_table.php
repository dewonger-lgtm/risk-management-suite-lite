<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration untuk membuat tabel Activity Logs
 * 
 * Tabel ini menyimpan comprehensive audit trail dari semua aktivitas user.
 * Setiap aksi (create, update, delete, view) dicatat untuk compliance dan security.
 * 
 * Design Rationale:
 * - Tanpa soft delete karena log harus permanent
 * - changes dalam JSON untuk flexibility dan compression
 * - ip_address dan user_agent untuk security tracking
 * - Comprehensive untuk audit compliance
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            // Primary Key
            $table->uuid('id')->primary();
            
            // Foreign Keys
            $table->uuid('user_id');
            $table->uuid('company_id');
            
            // Activity Information
            $table->string('action')->comment('create, update, delete, view, export, import');
            $table->string('model')->comment('Model class name');
            $table->uuid('model_id');
            
            // Changes Tracking
            $table->json('changes')->nullable()->comment('Old vs new values as JSON');
            
            // Security & Network
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            
            // Timestamp (No soft delete for logs)
            $table->timestamp('created_at');
            
            // Indexes
            $table->index('user_id');
            $table->index('company_id');
            $table->index('action');
            $table->index('model');
            $table->index(['model', 'model_id']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};