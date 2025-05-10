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
        Schema::table('time_entries', function (Blueprint $table) {
            $table->decimal('total_hours', 8, 2)->default(0)->after('check_out');
            $table->decimal('overtime_hours', 8, 2)->default(0)->after('total_hours');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('time_entries', function (Blueprint $table) {
            $table->dropColumn(['total_hours', 'overtime_hours']);
        });
    }
}; 