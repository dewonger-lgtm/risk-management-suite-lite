<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration untuk membuat tabel Approvals
 * 
 * Tabel ini menyimpan workflow approval yang bersifat polymorphic.
 * Dapat digunakan untuk approval CA, Risk, atau Incident dengan multi-level approvals.
 * 
 * Design Rationale:
 * - Polymorphic relationship menggunakan approvalable_type dan approvalable_id
 * - sequence untuk menentukan urutan approval level
 * - status tracking untuk workflow management
 * - comments untuk rejection reasons atau notes
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('approvals', function (Blueprint $table) {
            // Primary Key
            $table->uuid('id')->primary();
            
            // Foreign Keys
            $table->uuid('company_id');
            $table->uuid('approved_by')->nullable();
            
            // Polymorphic Relationship
            $table->string('approvalable_type')->comment('Model class name: CorrectiveAction, Risk, Incident');
            $table->uuid('approvalable_id');
            
            // Approval Sequence & Status
            $table->smallInteger('sequence')->comment('Approval level order');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->index();
            
            // Notes & Comments
            $table->text('comments')->nullable();
            $table->timestamp('approved_date')->nullable();
            
            // Timestamps
            $table->timestamps();
            
            // Indexes
            $table->index('company_id');
            $table->index('approved_by');
            $table->index(['approvalable_type', 'approvalable_id']);
            $table->index('status');
            $table->index('sequence');
            $table->index('created_at');
            
            // Foreign Key Constraints
            $table->foreign('company_id')
                ->references('id')
                ->on('companies')
                ->onDelete('cascade');
                
            $table->foreign('approved_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approvals');
    }
};