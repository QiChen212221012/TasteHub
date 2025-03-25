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
        Schema::create('likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // 记录用户点赞
            $table->unsignedBigInteger('post_id')->nullable(); // 被点赞的帖子 ID
            $table->unsignedBigInteger('comment_id')->nullable(); // 被点赞的评论 ID
            $table->timestamps();

            // 确保同一个用户只能对同一篇帖子或评论点赞一次
            $table->unique(['user_id', 'post_id', 'comment_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('likes');
    }
};
