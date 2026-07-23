<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration untuk membuat tabel Risk Categories
 * 
 * Tabel ini menyimpan kategori risiko yang dapat dikustomisasi per company.
 * Contoh: Strategic, Operational, Financial, Compliance, IT Security
 * 
 * Design Rationale:
 * - Customizable per company untuk flexibility
 * - Color field untuk UI representation
 * - Soft delete untuk audit trail
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('risk_categories', function (Blueprint $table) {
            // Primary Key
            $table->uuid('id')->primary();
            
            // Foreign Keys
            $table->uuid('company_id');
            
            // Category Information
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('color')->nullable()->comment('Hex color for UI');
            
            // Status
            $table->boolean('is_active')->default(true)->index();
            
            // Timestamps & Soft Delete
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('company_id');
            $table->index('is_active');
            $table->unique(['company_id', 'name']);
            
            // Foreign Key Constraints
            $table->foreign('company_id')
                ->references('id')
                ->on('companies')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('risk_categories');
    }
};