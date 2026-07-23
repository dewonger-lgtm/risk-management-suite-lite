<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration untuk membuat tabel KPI Targets
 * 
 * Tabel ini menyimpan target KPI untuk setiap periode waktu.
 * Memungkinkan tracking target changes dan historical data.
 * 
 * Design Rationale:
 * - period format: 2024-01 (monthly), 2024-Q1 (quarterly), 2024 (yearly)
 * - Time-based structure untuk flexibility
 * - History tracking untuk audit trail
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kpi_targets', function (Blueprint $table) {
            // Primary Key
            $table->uuid('id')->primary();
            
            // Foreign Keys
            $table->uuid('kpi_id');
            
            // Target Information
            $table->string('period')->comment('Format: 2024-01 (monthly), 2024-Q1 (quarterly), 2024 (yearly)');
            $table->decimal('target_value', 10, 2);
            
            // Timestamps
            $table->timestamps();
            
            // Indexes
            $table->index('kpi_id');
            $table->index('period');
            $table->unique(['kpi_id', 'period']);
            
            // Foreign Key Constraints
            $table->foreign('kpi_id')
                ->references('id')
                ->on('kpis')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kpi_targets');
    }
};