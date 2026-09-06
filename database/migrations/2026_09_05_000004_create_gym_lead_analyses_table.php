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
        Schema::create('gym_lead_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gym_lead_id')->unique()->constrained('gym_leads')->cascadeOnDelete();
            $table->date('reference_date');
            $table->integer('members_total')->default(0);
            $table->integer('members_valid')->default(0);
            $table->integer('members_with_errors')->default(0);
            $table->integer('duplicate_member_ids')->default(0);
            $table->integer('status_active')->default(0);
            $table->integer('status_inactive')->default(0);
            $table->integer('status_cancelled')->default(0);
            $table->integer('active_at_risk')->default(0);
            $table->integer('active_high_risk')->default(0);
            $table->integer('cancellations_last_90_days')->default(0);
            $table->decimal('fees_average', 10, 2)->nullable();
            $table->decimal('value_at_risk', 12, 2)->default(0);
            $table->decimal('reactivation_potential', 12, 2)->default(0);
            $table->json('opportunities')->nullable();
            $table->json('quality')->nullable();
            $table->json('extra')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gym_lead_analyses');
    }
};
