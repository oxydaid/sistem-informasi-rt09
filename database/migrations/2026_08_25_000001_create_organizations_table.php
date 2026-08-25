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
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('type')->default('rt'); // 'rt' or 'karang_taruna'
            $table->string('code')->nullable();
            $table->text('description')->nullable();
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('logo')->nullable();
            $table->string('cover_image')->nullable();
            $table->json('social_links')->nullable();
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('admin_rt')->after('password'); // super_admin, admin_rt, admin_karang_taruna
            $table->foreignId('organization_id')->nullable()->after('role')->constrained('organizations')->nullOnDelete();
            $table->string('phone')->nullable()->after('organization_id');
            $table->boolean('is_active')->default(true)->after('phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['organization_id']);
            $table->dropColumn(['role', 'organization_id', 'phone', 'is_active']);
        });

        Schema::dropIfExists('organizations');
    }
};
