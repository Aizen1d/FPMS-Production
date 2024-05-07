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
        Schema::table('admin_tasks_researches_published', function (Blueprint $table) {
            $table->dropColumn('title');
            $table->dropColumn('authors');

            $table->string('published_at');
            $table->unsignedBigInteger('research_completed_id');
            $table->foreign('research_completed_id')->references('id')->on('admin_tasks_researches_completed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admin_tasks_researches_published', function (Blueprint $table) {
            $table->string('title');
            $table->string('authors');

            $table->dropColumn('published_at');
            $table->dropForeign('admin_tasks_researches_published_research_completed_id_foreign');
            $table->dropColumn('research_completed_id');
        });
    }
};
