<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration untuk membuat tabel Departments
 * 
 * Tabel ini menyimpan struktur organisasi departemen dengan mendukung hierarchical structure.
 * Setiap departemen dapat memiliki subdepartemen (parent_id).
 * 
 * Design Rationale:
 * - parent_id untuk hierarchical structure (e.g., HSE > Safety > Safety Inspection)
 * - company_id untuk multi-company support
 * - Soft delete untuk audit trail
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('departments', function (Blueprint $table) {
            // Primary Key
            $table->uuid('id')->primary();
            
            // Foreign Keys
            $table->uuid('company_id');
            $table->uuid('parent_id')->nullable()->comment('Parent department for hierarchical structure');
            
            // Department Information
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            
            // Status
            $table->boolean('is_active')->default(true)->index();
            
            // Timestamps & Soft Delete
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('company_id');
            $table->index('parent_id');
            $table->index('is_active');
            $table->unique(['company_id', 'code']);
            
            // Foreign Key Constraints
            $table->foreign('company_id')
                ->references('id')
                ->on('companies')
                ->onDelete('cascade');
                
            $table->foreign('parent_id')
                ->references('id')
                ->on('departments')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};