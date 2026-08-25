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
        Schema::create('contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->string('type'); // 'announcement', 'article', 'event', 'activity', 'page'
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->json('content')->nullable(); // Tiptap JSON source of truth
            $table->longText('content_html')->nullable(); // Cached HTML for fast rendering
            $table->string('thumbnail')->nullable();
            $table->string('status')->default('draft'); // 'draft', 'published', 'archived'
            $table->timestamp('published_at')->nullable();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedInteger('view_count')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->timestamps();

            $table->index(['organization_id', 'type', 'status', 'published_at'], 'contents_org_type_stat_pub_idx');
        });

        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_id')->constrained('contents')->cascadeOnDelete();
            $table->dateTime('start_at');
            $table->dateTime('end_at')->nullable();
            $table->string('location');
            $table->text('maps_url')->nullable();
            $table->boolean('is_online')->default(false);
            $table->timestamps();

            $table->index(['start_at']);
        });

        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_id')->constrained('contents')->cascadeOnDelete();
            $table->date('activity_date');
            $table->string('location')->nullable();
            $table->timestamps();

            $table->index(['activity_date']);
        });

        Schema::create('activity_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')->constrained('activities')->cascadeOnDelete();
            $table->foreignId('media_id')->constrained('media')->cascadeOnDelete();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('content_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_id')->constrained('contents')->cascadeOnDelete();
            $table->foreignId('media_id')->constrained('media')->cascadeOnDelete();
            $table->string('display_name');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_attachments');
        Schema::dropIfExists('activity_media');
        Schema::dropIfExists('activities');
        Schema::dropIfExists('events');
        Schema::dropIfExists('contents');
    }
};
