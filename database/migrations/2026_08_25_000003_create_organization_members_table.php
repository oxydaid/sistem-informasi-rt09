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
        Schema::create('organization_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->string('name');
            $table->string('gender')->nullable(); // male, female
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('avatar')->nullable();
            $table->text('bio')->nullable();
            $table->unsignedSmallInteger('join_year')->nullable();
            $table->string('status')->default('active'); // active, inactive, alumni
            $table->timestamps();

            $table->index(['organization_id', 'status'], 'org_members_org_status_idx');
        });

        Schema::create('position_member_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_member_id')->constrained('organization_members')->cascadeOnDelete();
            $table->foreignId('organization_position_id')->constrained('organization_positions')->cascadeOnDelete();
            $table->foreignId('organization_period_id')->constrained('organization_periods')->cascadeOnDelete();
            $table->string('notes')->nullable();
            $table->timestamps();

            $table->index(['organization_period_id', 'organization_position_id'], 'pmp_period_pos_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('position_member_periods');
        Schema::dropIfExists('organization_members');
    }
};
