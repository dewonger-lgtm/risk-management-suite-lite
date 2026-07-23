<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration untuk membuat tabel Corrective Actions
 * 
 * Tabel ini menyimpan rencana aksi korektif/preventif yang dapat dikaitkan dengan incident atau risk.
 * Setiap CA memiliki workflow dari open → in_progress → completed → verified → closed.
 * 
 * Design Rationale:
 * - Polymorphic relationship (incident_id atau risk_id)
 * - Verification step untuk effectiveness check
 * - assigned_to untuk ownership dan accountability
 * - Priority untuk prioritization dalam execution
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('corrective_actions', function (Blueprint $table) {
            // Primary Key
            $table->uuid('id')->primary();
            
            // Foreign Keys
            $table->uuid('company_id');
            $table->uuid('incident_id')->nullable();
            $table->uuid('risk_id')->nullable();
            $table->uuid('assigned_to');
            $table->uuid('verified_by')->nullable();
            
            // CA Information
            $table->string('code')->unique();
            $table->string('title');
            $table->text('description');
            
            // Classification
            $table->enum('type', ['corrective', 'preventive'])->index();
            $table->enum('priority', ['critical', 'high', 'medium', 'low'])->index();
            
            // Status & Workflow
            $table->enum('status', ['open', 'in_progress', 'completed', 'verified', 'closed'])
                ->default('open')
                ->index();
            
            // Dates
            $table->date('due_date');
            $table->date('implementation_date')->nullable();
            $table->date('verification_date')->nullable();
            
            // Effectiveness
            $table->text('effectiveness_notes')->nullable();
            
            // Timestamps & Soft Delete
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('company_id');
            $table->index('incident_id');
            $table->index('risk_id');
            $table->index('assigned_to');
            $table->index('verified_by');
            $table->index('status');
            $table->index('type');
            $table->index('priority');
            $table->index('created_at');
            $table->unique(['company_id', 'code']);
            
            // Foreign Key Constraints
            $table->foreign('company_id')
                ->references('id')
                ->on('companies')
                ->onDelete('cascade');
                
            $table->foreign('incident_id')
                ->references('id')
                ->on('incidents')
                ->onDelete('set null');
                
            $table->foreign('risk_id')
                ->references('id')
                ->on('risks')
                ->onDelete('set null');
                
            $table->foreign('assigned_to')
                ->references('id')
                ->on('users')
                ->onDelete('restrict');
                
            $table->foreign('verified_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('corrective_actions');
    }
};