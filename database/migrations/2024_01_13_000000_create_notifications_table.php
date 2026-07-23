<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration untuk membuat tabel Notifications
 * 
 * Tabel ini menyimpan notifikasi untuk users menggunakan Laravel's notification system.
 * Mendukung polymorphic relationships untuk berbagai tipe notifikasi.
 * 
 * Design Rationale:
 * - Laravel's built-in notification structure
 * - Polymorphic untuk berbagai notification entity
 * - JSON data untuk flexible notification content
 * - read_at untuk tracking status
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            // Primary Key
            $table->uuid('id')->primary();
            
            // Foreign Keys
            $table->uuid('user_id');
            
            // Notification Information
            $table->string('type')->comment('Notification class name');
            $table->string('notifiable_type')->nullable();
            $table->uuid('notifiable_id')->nullable();
            
            // Data & Content
            $table->json('data');
            
            // Read Status
            $table->timestamp('read_at')->nullable();
            
            // Timestamp
            $table->timestamp('created_at');
            
            // Indexes
            $table->index('user_id');
            $table->index(['notifiable_type', 'notifiable_id']);
            $table->index('read_at');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};