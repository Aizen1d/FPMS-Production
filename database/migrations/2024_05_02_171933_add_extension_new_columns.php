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
        Schema::table('extension', function (Blueprint $table) {
            $table->string('title_of_extension_program');
            $table->string('title_of_extension_project');
            $table->string('title_of_extension_activity');
            $table->string('place')->nullable();
            $table->string('level');
            $table->string('classification');
            $table->string('type');
            $table->string('keywords')->nullable();
            $table->string('type_of_funding');
            $table->string('funding_agency')->nullable();
            $table->double('amount_of_funding')->nullable();
            $table->integer('total_no_of_hours')->nullable();
            $table->integer('no_of_trainees')->nullable();
            $table->string('classification_of_trainees');
            $table->string('nature_of_involvement');
            $table->string('status');
            $table->timestamp('from_date')->nullable();
            $table->timestamp('to_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('extension', function (Blueprint $table) {
            $table->dropColumn('title_of_extension_program');
            $table->dropColumn('title_of_extension_project');
            $table->dropColumn('title_of_extension_activity');
            $table->dropColumn('place');
            $table->dropColumn('level');
            $table->dropColumn('classification');
            $table->dropColumn('type');
            $table->dropColumn('keywords');
            $table->dropColumn('type_of_funding');
            $table->dropColumn('funding_agency');
            $table->dropColumn('amount_of_funding');
            $table->dropColumn('total_no_of_hours');
            $table->dropColumn('no_of_trainees');
            $table->dropColumn('classification_of_trainees');
            $table->dropColumn('nature_of_involvement');
            $table->dropColumn('status');
            $table->dropColumn('from_date');
            $table->dropColumn('to_date');
        });
    }
};
