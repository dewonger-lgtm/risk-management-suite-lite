<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration untuk membuat tabel KPI Actuals
 * 
 * Tabel ini menyimpan nilai aktual KPI yang tercatat per periode.
 * Memungkinkan tracking performance vs target.
 * 
 * Design Rationale:
 * - recorded_by untuk audit trail (siapa yang input data)
 * - recorded_date untuk tracking kapan data diinput
 * - Bisa di-update sampai period ditutup
 * - period format sama dengan kpi_targets
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kpi_actuals', function (Blueprint $table) {
            // Primary Key
            $table->uuid('id')->primary();
            
            // Foreign Keys
            $table->uuid('kpi_id');
            $table->uuid('recorded_by');
            
            // Actual Information
            $table->string('period')->comment('Format: 2024-01 (monthly), 2024-Q1 (quarterly), 2024 (yearly)');
            $table->decimal('actual_value', 10, 2);
            $table->timestamp('recorded_date');
            
            // Timestamps
            $table->timestamps();
            
            // Indexes
            $table->index('kpi_id');
            $table->index('recorded_by');
            $table->index('period');
            $table->unique(['kpi_id', 'period']);
            
            // Foreign Key Constraints
            $table->foreign('kpi_id')
                ->references('id')
                ->on('kpis')
                ->onDelete('cascade');
                
            $table->foreign('recorded_by')
                ->references('id')
                ->on('users')
                ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kpi_actuals');
    }
};