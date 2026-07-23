<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration untuk membuat tabel KPIs
 * 
 * Tabel ini menyimpan definisi Key Performance Indicators (KPI) yang dapat dikustomisasi per department.
 * Formula field memungkinkan kalkulasi dinamis untuk KPI yang kompleks.
 * 
 * Design Rationale:
 * - code unik untuk KPI tracking
 * - formula untuk flexible calculation logic
 * - frequency untuk periode tracking (daily, weekly, monthly, dll)
 * - target_value dan current_value untuk monitoring
 * - Department-specific untuk flexibility
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kpis', function (Blueprint $table) {
            // Primary Key
            $table->uuid('id')->primary();
            
            // Foreign Keys
            $table->uuid('company_id');
            $table->uuid('department_id')->nullable();
            $table->uuid('owner_id');
            
            // KPI Information
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->text('formula')->nullable()->comment('Calculation formula or expression');
            
            // Targets & Values
            $table->decimal('target_value', 10, 2);
            $table->decimal('current_value', 10, 2)->nullable();
            $table->string('unit')->comment('%, piece, days, hours, etc');
            
            // Frequency & Tracking
            $table->enum('frequency', ['daily', 'weekly', 'monthly', 'quarterly', 'yearly'])->index();
            
            // Status
            $table->enum('status', ['active', 'inactive'])->default('active')->index();
            
            // Timestamps & Soft Delete
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('company_id');
            $table->index('department_id');
            $table->index('owner_id');
            $table->index('status');
            $table->index('frequency');
            $table->index('created_at');
            $table->unique(['company_id', 'code']);
            
            // Foreign Key Constraints
            $table->foreign('company_id')
                ->references('id')
                ->on('companies')
                ->onDelete('cascade');
                
            $table->foreign('department_id')
                ->references('id')
                ->on('departments')
                ->onDelete('set null');
                
            $table->foreign('owner_id')
                ->references('id')
                ->on('users')
                ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kpis');
    }
};