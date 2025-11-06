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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            
            // Foreign key to user who created the document
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();
            
            // Document details
            $table->string('title');
            $table->text('content');
            $table->decimal('amount', 10, 2);
            
            // Document status (state machine)
            $table->enum('status', [
                'draft',
                'pending_manager_approval',
                'pending_finance_approval',
                'approved',
                'rejected'
            ])->default('draft');
            
            // Optional file attachment
            $table->string('file_path')->nullable();
            
            $table->timestamps();
            
            // Indexes for better query performance
            $table->index('user_id');
            $table->index('status');
            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
