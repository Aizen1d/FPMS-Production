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
        Schema::table('admin_tasks_researches_presented', function (Blueprint $table) {
            $table->dropForeign(['research_completed_id']);

            $table->foreign('research_completed_id')
                ->references('id')
                ->on('admin_tasks_researches_completed')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admin_tasks_researches_presented', function (Blueprint $table) {
            $table->dropForeign(['research_completed_id']);

            $table->foreign('research_completed_id')
                ->references('id')
                ->on('admin_tasks_researches_completed');
        });
    }
};
