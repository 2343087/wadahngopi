<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $connection = Schema::connection($this->getConnection())->getConnection();
        $driver = $connection->getDriverName();
        $isSqlite = $driver === 'sqlite';

        Schema::table('cafes', function (Blueprint $table) use ($isSqlite) {
            // Helper to generate expression
            $expr = function ($path) use ($isSqlite) {
                return $isSqlite
                    ? "json_extract(operating_hours, '$path')"
                    : "CAST(NULLIF(JSON_UNQUOTE(JSON_EXTRACT(operating_hours, '$path')), 'null') AS TIME)";
            };

            // Virtual Generated Columns with Driver-Specific Expression
            $table->time('weekday_open')->virtualAs($expr('$.weekday.open'))->nullable()->index();
            $table->time('weekday_close')->virtualAs($expr('$.weekday.close'))->nullable()->index();
            $table->time('weekend_open')->virtualAs($expr('$.weekend.open'))->nullable()->index();
            $table->time('weekend_close')->virtualAs($expr('$.weekend.close'))->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cafes', function (Blueprint $table) {
            $table->dropColumn(['weekday_open', 'weekday_close', 'weekend_open', 'weekend_close']);
        });
    }
};
