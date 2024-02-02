<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('faculties', function (Blueprint $table) {
            DB::statement("ALTER TABLE faculties MODIFY COLUMN contact_number VARCHAR(255) NULL AFTER email");
            DB::statement("ALTER TABLE faculties MODIFY COLUMN gender VARCHAR(255) NULL AFTER contact_number");
            DB::statement("ALTER TABLE faculties MODIFY COLUMN department VARCHAR(255) NULL AFTER gender");
            DB::statement("ALTER TABLE faculties MODIFY COLUMN department_id BIGINT UNSIGNED NULL AFTER department");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('faculties', function (Blueprint $table) {
            DB::statement("ALTER TABLE faculties MODIFY COLUMN contact_number VARCHAR(255) NULL AFTER updated_at");
            DB::statement("ALTER TABLE faculties MODIFY COLUMN gender VARCHAR(255) NULL AFTER contact_number");
            DB::statement("ALTER TABLE faculties MODIFY COLUMN department VARCHAR(255) NULL AFTER gender");
            DB::statement("ALTER TABLE faculties MODIFY COLUMN department_id BIGINT UNSIGNED NULL AFTER department");
        });
    }
};
