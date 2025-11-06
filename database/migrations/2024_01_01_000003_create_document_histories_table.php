<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('document_histories', function (Blueprint $table) {
            $table->id();
            
            // Foreign key to document
            $table->foreignId('document_id')
                ->constrained()
                ->cascadeOnDelete();
            
            // Foreign key to user who performed the action
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();
            
            // Action performed (created, submitted, approved, rejected, etc.)
            $table->string('action');
            
            // Optional comment (e.g., reason for rejection)
            $table->text('comment')->nullable();
            
            $table->timestamps();
            
            // Indexes for audit queries
            $table->index('document_id');
            $table->index('user_id');
            $table->index(['document_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_histories');
    }
};
