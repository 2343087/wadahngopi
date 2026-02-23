<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Clean up existing location column and index if they exist
        foreach (['cafes', 'roasteries'] as $tableName) {
            try {
                // Try dropping spatial index first
                DB::statement("ALTER TABLE {$tableName} DROP INDEX IF EXISTS location");
            } catch (\Exception $e) {
            }

            try {
                // Try dropping column
                DB::statement("ALTER TABLE {$tableName} DROP COLUMN IF EXISTS location");
            } catch (\Exception $e) {
            }
        }

        // 2. Add location column as nullable (to allow adding to existing rows)
        Schema::table('cafes', function (Blueprint $table) {
            $table->geometry('location', 'point', 4326)->nullable()->after('longitude');
        });

        Schema::table('roasteries', function (Blueprint $table) {
            $table->geometry('location', 'point', 4326)->nullable()->after('longitude');
        });

        // 3. Populate with standard (Latitude, Longitude) order for MySQL 8.0+ SRID 4326
        DB::statement("UPDATE cafes SET location = ST_GeomFromText(CONCAT('POINT(', latitude, ' ', longitude, ')'), 4326) WHERE latitude IS NOT NULL AND longitude IS NOT NULL");
        DB::statement("UPDATE roasteries SET location = ST_GeomFromText(CONCAT('POINT(', latitude, ' ', longitude, ')'), 4326) WHERE latitude IS NOT NULL AND longitude IS NOT NULL");

        // 4. Force NOT NULL and add Spatial Index
        // Note: Spatial indexes REQUIRE the column to be NOT NULL in MySQL
        DB::statement("UPDATE cafes SET location = ST_GeomFromText('POINT(0 0)', 4326) WHERE location IS NULL");
        DB::statement("UPDATE roasteries SET location = ST_GeomFromText('POINT(0 0)', 4326) WHERE location IS NULL");

        DB::statement('ALTER TABLE cafes MODIFY location GEOMETRY NOT NULL SRID 4326');
        DB::statement('ALTER TABLE roasteries MODIFY location GEOMETRY NOT NULL SRID 4326');

        Schema::table('cafes', function (Blueprint $table) {
            $table->spatialIndex('location');
        });
        Schema::table('roasteries', function (Blueprint $table) {
            $table->spatialIndex('location');
        });
    }

    public function down(): void
    {
        Schema::table('cafes', function (Blueprint $table) {
            $table->dropSpatialIndex(['location']);
            $table->dropColumn('location');
        });

        Schema::table('roasteries', function (Blueprint $table) {
            $table->dropSpatialIndex(['location']);
            $table->dropColumn('location');
        });
    }
};
