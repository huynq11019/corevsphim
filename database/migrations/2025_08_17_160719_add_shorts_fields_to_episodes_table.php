<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShortsFieldsToEpisodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('episodes', function (Blueprint $table) {
            $table->boolean('is_short')->default(false)->after('source');
            $table->json('hashtags')->nullable()->after('is_short');
            $table->integer('view')->default(0)->after('hashtags');
            $table->integer('likes')->default(0)->after('view');
            $table->integer('dislikes')->default(0)->after('likes');
            $table->integer('shares')->default(0)->after('dislikes');
            $table->integer('duration_seconds')->nullable()->after('shares')->comment('Duration in seconds for shorts');
            $table->enum('status', ['active', 'inactive'])->default('active')->after('duration_seconds');

            // Indexes for shorts
            $table->index(['is_short', 'status', 'created_at']);
            $table->index(['is_short', 'likes', 'created_at']);
            $table->index(['is_short', 'view', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('episodes', function (Blueprint $table) {
            $table->dropIndex(['is_short', 'status', 'created_at']);
            $table->dropIndex(['is_short', 'likes', 'created_at']);
            $table->dropIndex(['is_short', 'view', 'created_at']);

            $table->dropColumn([
                'is_short',
                'hashtags',
                'view',
                'likes',
                'dislikes',
                'shares',
                'duration_seconds',
                'status'
            ]);
        });
    }
}
