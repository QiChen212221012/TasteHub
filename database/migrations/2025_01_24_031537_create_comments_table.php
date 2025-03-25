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
        Schema::create('comments', function (Blueprint $table) {
            $table->id(); // 主键
            $table->foreignId('post_id')->constrained()->onDelete('cascade'); // 外键关联到 posts 表
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // 外键关联到 users 表
            $table->text('content'); // 评论内容
            $table->enum('status', ['approved', 'reported', 'deleted'])
                  ->default('approved')
                  ->index(); // ✅ 添加索引，提高查询效率
            $table->timestamps(); // 创建时间和更新时间
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments'); // 删除表
    }
};
