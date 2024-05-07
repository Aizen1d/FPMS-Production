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
        Schema::table('admin_tasks_researches_completed', function (Blueprint $table) {
            $table->string('kind_of_research');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admin_tasks_researches_completed', function (Blueprint $table) {
            $table->dropColumn('kind_of_research');
        });
    }
};
