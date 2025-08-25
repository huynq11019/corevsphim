<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('shorts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('slug')->unique();
            $table->string('video_url'); // Link video
            $table->string('thumbnail_url'); // Thumbnail
            $table->integer('duration')->comment('Duration in seconds');
            $table->string('quality')->default('720p');
            $table->integer('views')->default(0);
            $table->integer('likes')->default(0);
            $table->integer('dislikes')->default(0);
            $table->integer('shares')->default(0);
            $table->json('hashtags')->nullable(); // Array of hashtags
            $table->enum('status', ['active', 'inactive', 'pending'])->default('active');
            $table->boolean('is_featured')->default(false);
            $table->integer('sort_order')->default(0);
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('source')->nullable(); // 'upload', 'crawl', etc
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index(['views', 'created_at']);
            $table->index(['likes', 'created_at']);
            $table->index('is_featured');
        });
    }

    public function down()
    {
        Schema::dropIfExists('shorts');
    }
};
