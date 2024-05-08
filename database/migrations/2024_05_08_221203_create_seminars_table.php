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
        Schema::create('seminars', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('classification');
            $table->string('nature');
            $table->string('type');
            $table->string('source_of_fund');
            $table->integer('budget');
            $table->string('organizer');
            $table->string('level');
            $table->string('venue');
            $table->date('from_date');
            $table->date('to_date');
            $table->integer('total_no_hours');
            $table->string('special_order');
            $table->string('certificate');
            $table->string('compiled_photos');
            $table->string('notes');

            $table->unsignedBigInteger('faculty_id');
            $table->foreign('faculty_id')->references('id')->on('faculties')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seminars');
    }
};
