<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration untuk membuat tabel Incidents
 * 
 * Tabel ini menyimpan pelaporan insiden dengan workflow investigation.
 * Setiap incident dapat dikaitkan dengan risks untuk root cause analysis.
 * 
 * Design Rationale:
 * - code unik untuk incident tracking
 * - reported_by dan reported_date untuk audit trail
 * - investigation_findings dan root_cause untuk analysis
 * - Workflow status untuk tracking progress
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incidents', function (Blueprint $table) {
            // Primary Key
            $table->uuid('id')->primary();
            
            // Foreign Keys
            $table->uuid('company_id');
            $table->uuid('reported_by');
            $table->uuid('investigated_by')->nullable();
            
            // Incident Information
            $table->string('code')->unique();
            $table->string('title');
            $table->text('description');
            
            // Incident Classification
            $table->enum('type', ['safety', 'quality', 'security', 'operational'])->index();
            $table->enum('severity', ['critical', 'high', 'medium', 'low'])->index();
            
            // Status & Workflow
            $table->enum('status', ['open', 'investigating', 'resolved', 'closed'])->default('open')->index();
            
            // Dates
            $table->timestamp('reported_date');
            $table->timestamp('occurred_date');
            
            // Investigation
            $table->text('investigation_findings')->nullable();
            $table->text('root_cause')->nullable();
            
            // Timestamps & Soft Delete
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('company_id');
            $table->index('reported_by');
            $table->index('investigated_by');
            $table->index('status');
            $table->index('severity');
            $table->index('created_at');
            $table->unique(['company_id', 'code']);
            
            // Foreign Key Constraints
            $table->foreign('company_id')
                ->references('id')
                ->on('companies')
                ->onDelete('cascade');
                
            $table->foreign('reported_by')
                ->references('id')
                ->on('users')
                ->onDelete('restrict');
                
            $table->foreign('investigated_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incidents');
    }
};