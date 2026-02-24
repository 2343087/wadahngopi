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
        $connection = Schema::connection($this->getConnection())->getConnection();
        $driver = $connection->getDriverName();
        $isSqlite = $driver === 'sqlite';

        Schema::table('cafes', function (Blueprint $table) use ($isSqlite) {
            // Helper to generate expression - Modern JSON_VALUE avoids "Truncated incorrect INTEGER value" in MySQL 8.4
            $expr = function ($path) use ($isSqlite) {
                return $isSqlite
                    ? "json_extract(operating_hours, '$path')"
                    : "JSON_VALUE(`operating_hours`, '$path' RETURNING TIME)";
            };

            // Redefine Generated Columns with Safer Expression
            $table->time('weekday_open')->virtualAs($expr('$.weekday.open'))->nullable()->change();
            $table->time('weekday_close')->virtualAs($expr('$.weekday.close'))->nullable()->change();
            $table->time('weekend_open')->virtualAs($expr('$.weekend.open'))->nullable()->change();
            $table->time('weekend_close')->virtualAs($expr('$.weekend.close'))->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverting to the old expression if needed (though it was buggy)
        Schema::table('cafes', function (Blueprint $table) {
            $expr = "CAST(NULLIF(JSON_UNQUOTE(JSON_EXTRACT(operating_hours, '$.weekday.open')), 'null') AS TIME)";
            $table->time('weekday_open')->virtualAs($expr)->nullable()->change();
            // ... (other columns can stay as is for down, usually rollback is for dropping)
        });
    }
};
