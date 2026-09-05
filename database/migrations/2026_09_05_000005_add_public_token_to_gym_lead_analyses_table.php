<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Añade el token público que identifica el informe comercial.
     *
     * El token es un recurso secreto que permite acceder al informe sin
     * autenticación. No sustituye el ID interno: únicamente identifica el
     * análisis publicado. Los análisis existentes (NULL) siguen funcionando
     * desde Admin; el token se genera la próxima vez que se persistan.
     */
    public function up(): void
    {
        Schema::table('gym_lead_analyses', function (Blueprint $table) {
            $table->string('public_token', 80)->nullable()->unique()->after('gym_lead_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gym_lead_analyses', function (Blueprint $table) {
            $table->dropUnique(['public_token']);
            $table->dropColumn('public_token');
        });
    }
};
