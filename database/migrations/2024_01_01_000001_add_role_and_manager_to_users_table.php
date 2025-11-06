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
        Schema::table('users', function (Blueprint $table) {
            // Add role column with enum type
            $table->enum('role', ['user', 'manager', 'finance', 'admin'])
                ->default('user')
                ->after('email');

            // Add manager_id for hierarchical structure
            $table->foreignId('manager_id')
                ->nullable()
                ->after('role')
                ->constrained('users')
                ->nullOnDelete();

            // Add index for better performance
            $table->index('role');
            $table->index('manager_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['manager_id']);
            $table->dropIndex(['role']);
            $table->dropIndex(['manager_id']);
            $table->dropColumn(['role', 'manager_id']);
        });
    }
};
