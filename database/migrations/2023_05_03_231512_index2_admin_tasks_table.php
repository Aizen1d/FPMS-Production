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
        Schema::table('admin_tasks', function (Blueprint $table) {
            $table->index(['task_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admin_tasks', function (Blueprint $table) {
            $table->dropIndex('admin_tasks_task_name_index');
            $table->dropIndex('admin_tasks_faculty_name_due_date_index');
        });
    }
};
