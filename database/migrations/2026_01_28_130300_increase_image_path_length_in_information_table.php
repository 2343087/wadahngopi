<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Use raw SQL to modify column types to avoid doctrine/dbal issues
        DB::statement('ALTER TABLE information MODIFY image_path TEXT NULL');
        DB::statement('ALTER TABLE information MODIFY source_url TEXT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No rollback needed
    }
};
