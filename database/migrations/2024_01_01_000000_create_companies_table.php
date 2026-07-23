<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration untuk membuat tabel Companies
 * 
 * Tabel ini menyimpan data perusahaan untuk mendukung multi-company feature.
 * Setiap perusahaan dapat memiliki departments, risks, incidents, dan users yang berbeda.
 * 
 * Design Rationale:
 * - UUID primary key untuk keamanan dan skalabilitas
 * - Unique code untuk identifier bisnis
 * - Soft delete untuk audit trail
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            // Primary Key
            $table->uuid('id')->primary();
            
            // Company Information
            $table->string('name')->unique();
            $table->string('code')->unique()->comment('Unique business identifier');
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('logo_path')->nullable();
            
            // Status
            $table->boolean('is_active')->default(true)->index();
            
            // Timestamps & Soft Delete
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('is_active');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};