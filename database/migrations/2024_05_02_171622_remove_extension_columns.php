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
            $table->dropColumn('title');
            $table->dropColumn('partner');
            $table->dropColumn('beneficiaries');
            $table->dropColumn('evaluation');
            $table->dropColumn('date_conducted');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('extension', function (Blueprint $table) {
            $table->string('title');
            $table->string('partner');
            $table->string('beneficiaries');
            $table->string('evaluation');
            $table->date('date_conducted');
        });
    }
};
