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
        Schema::create('admin_tasks_researches_published', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('authors');
            $table->string('name_of_journal');
            $table->timestamp('date_published');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_tasks_researches_published');
    }
};
