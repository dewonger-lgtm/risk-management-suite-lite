<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration untuk membuat tabel Users
 * 
 * Tabel ini menyimpan data pengguna aplikasi dengan role-based access control.
 * Setiap user memiliki role yang menentukan permissions mereka.
 * 
 * Design Rationale:
 * - UUID primary key untuk keamanan
 * - Role disimpan sebagai enum untuk performance
 * - department_id untuk organisasi struktur
 * - is_active untuk soft deactivate
 * - last_login_at untuk tracking user activity
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            // Primary Key
            $table->uuid('id')->primary();
            
            // Foreign Keys
            $table->uuid('company_id');
            $table->uuid('department_id')->nullable();
            
            // User Information
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('phone')->nullable();
            $table->rememberToken();
            
            // Role & Status
            $table->enum('role', [
                'administrator',
                'risk_manager',
                'auditor',
                'department_head',
                'supervisor',
                'staff',
                'viewer'
            ])->default('staff');
            $table->boolean('is_active')->default(true)->index();
            
            // Activity Tracking
            $table->timestamp('last_login_at')->nullable();
            
            // Timestamps & Soft Delete
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('company_id');
            $table->index('department_id');
            $table->index('role');
            $table->index('is_active');
            $table->index('created_at');
            $table->unique(['company_id', 'email']);
            
            // Foreign Key Constraints
            $table->foreign('company_id')
                ->references('id')
                ->on('companies')
                ->onDelete('cascade');
                
            $table->foreign('department_id')
                ->references('id')
                ->on('departments')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};