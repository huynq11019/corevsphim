<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('short_interactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('short_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('ip_address')->nullable();
            $table->enum('type', ['view', 'like', 'dislike', 'share']);
            $table->timestamp('created_at');

            // Prevent duplicate interactions
            $table->unique(['short_id', 'user_id', 'type', 'ip_address'], 'unique_interaction');
            $table->index(['short_id', 'type']);
            $table->index(['user_id', 'type']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('short_interactions');
    }
};
