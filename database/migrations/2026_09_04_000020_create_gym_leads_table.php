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
        Schema::create('gym_leads', function (Blueprint $table) {
            $table->id();
            $table->string('gym_name');
            $table->string('contact_name');
            $table->string('email');
            $table->string('software')->nullable();
            $table->unsignedInteger('members')->nullable();
            $table->decimal('average_fee', 10, 2)->nullable();
            $table->unsignedInteger('inactive_members')->nullable();
            $table->unsignedInteger('monthly_cancellations')->nullable();
            $table->decimal('estimated_opportunity', 12, 2)->nullable();
            $table->timestamp('consent_at')->nullable();
            $table->timestamps();
        });

        Schema::dropIfExists('leads');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gym_leads');
    }
};
