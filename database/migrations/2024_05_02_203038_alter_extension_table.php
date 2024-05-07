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
            $table->string('title_of_extension_program')->nullable()->change();
            $table->string('title_of_extension_project')->nullable()->change();
            $table->string('title_of_extension_activity')->nullable()->change();
            $table->string('type_of_extension');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('extension', function (Blueprint $table) {
            $table->string('title_of_extension_program')->nullable(false)->change();
            $table->string('title_of_extension_project')->nullable(false)->change();
            $table->string('title_of_extension_activity')->nullable(false)->change();
            $table->dropColumn('type_of_extension');
        });
    }
};
