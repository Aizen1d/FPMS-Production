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
        Schema::create('faculty_tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('task_id')->nullable();
            $table->foreign('task_id')->references('id')->on('admin_tasks');

            $table->unsignedBigInteger('submitted_by_id')->nullable();
            $table->foreign('submitted_by_id')->references('id')->on('faculties');

            $table->string('submitted_by')->nullable();
            $table->text('description')->nullable();
            $table->string('attachments')->nullable();
            $table->string('status')->nullable();
            $table->string('decision')->nullable();
            $table->timestamp('date_submitted')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faculty_tasks');
    }
};
