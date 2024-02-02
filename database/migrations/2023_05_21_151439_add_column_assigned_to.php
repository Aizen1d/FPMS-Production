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
            $table->string('assigned_to')->nullable()->after('faculty_name');
            $table->text('description')->nullable()->after('assigned_to');
            $table->string('attachments')->nullable()->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admin_tasks', function (Blueprint $table) {
            $table->dropColumn('assigned_to');
            $table->dropColumn('description');
            $table->dropColumn('attachments');
        });
    }
};
