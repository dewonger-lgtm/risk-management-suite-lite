<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration untuk membuat tabel Risks
 * 
 * Tabel ini menyimpan register risiko perusahaan dengan calculation untuk risk scoring.
 * Risk score dihitung dari likelihood × impact (5x5 matrix).
 * 
 * Design Rationale:
 * - code unik untuk tracking dan referencing
 * - likelihood dan impact untuk risk matrix (1-5 scale)
 * - inherent_risk_score sebelum mitigation, residual setelah mitigation
 * - owner_id untuk accountability
 * - created_by untuk audit trail
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('risks', function (Blueprint $table) {
            // Primary Key
            $table->uuid('id')->primary();
            
            // Foreign Keys
            $table->uuid('company_id');
            $table->uuid('category_id');
            $table->uuid('owner_id');
            $table->uuid('created_by');
            
            // Risk Information
            $table->string('code')->unique();
            $table->string('title');
            $table->text('description');
            
            // Risk Assessment (5x5 Matrix)
            $table->tinyInteger('likelihood')->comment('1-5 scale');
            $table->tinyInteger('impact')->comment('1-5 scale');
            $table->smallInteger('inherent_risk_score')->comment('likelihood × impact');
            
            // Mitigation & Residual Risk
            $table->text('mitigation_plan')->nullable();
            $table->smallInteger('residual_risk_score')->nullable()->comment('After mitigation');
            
            // Status
            $table->enum('status', ['open', 'mitigating', 'closed'])->default('open')->index();
            $table->date('risk_date');
            
            // Timestamps & Soft Delete
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('company_id');
            $table->index('category_id');
            $table->index('owner_id');
            $table->index('status');
            $table->index('inherent_risk_score');
            $table->index('created_at');
            $table->unique(['company_id', 'code']);
            
            // Foreign Key Constraints
            $table->foreign('company_id')
                ->references('id')
                ->on('companies')
                ->onDelete('cascade');
                
            $table->foreign('category_id')
                ->references('id')
                ->on('risk_categories')
                ->onDelete('restrict');
                
            $table->foreign('owner_id')
                ->references('id')
                ->on('users')
                ->onDelete('restrict');
                
            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('risks');
    }
};