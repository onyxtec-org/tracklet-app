<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration changes the foreign key constraint from CASCADE to SET NULL
     * to prevent users from being deleted when organizations are deleted.
     * Users should remain even if their organization is deleted.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop the existing foreign key constraint
            $table->dropForeign(['organization_id']);
            
            // Re-add the foreign key with SET NULL instead of CASCADE
            // This way, if an organization is deleted, users remain but their organization_id becomes NULL
            $table->foreign('organization_id')
                ->references('id')
                ->on('organizations')
                ->onDelete('set null'); // Changed from 'cascade' to 'set null'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['organization_id']);
            
            // Re-add with CASCADE (original behavior)
            $table->foreign('organization_id')
                ->references('id')
                ->on('organizations')
                ->onDelete('cascade');
        });
    }
};

