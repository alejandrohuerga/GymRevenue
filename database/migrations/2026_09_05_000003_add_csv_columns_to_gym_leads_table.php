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
        Schema::table('gym_leads', function (Blueprint $table) {
            $table->string('csv_path')->nullable();
            $table->string('csv_original_name')->nullable();
            $table->timestamp('csv_uploaded_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gym_leads', function (Blueprint $table) {
            $table->dropColumn(['csv_path', 'csv_original_name', 'csv_uploaded_at']);
        });
    }
};
