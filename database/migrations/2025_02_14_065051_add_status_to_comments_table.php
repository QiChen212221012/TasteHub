<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('comments', function (Blueprint $table) {
            // ✅ 确保 status 字段存在（approved / reported / deleted）
            if (!Schema::hasColumn('comments', 'status')) {
                $table->enum('status', ['approved', 'reported', 'deleted'])
                      ->default('approved')
                      ->after('content');
            }

            // ✅ 添加 `reported` 让用户可以手动举报
            if (!Schema::hasColumn('comments', 'reported')) {
                $table->boolean('reported')->default(false)->after('status');
            }

            // ✅ 确保 `is_reviewed` 存在，表示管理员是否审核过
            if (!Schema::hasColumn('comments', 'is_reviewed')) {
                $table->boolean('is_reviewed')->default(false)->after('reported');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('comments', function (Blueprint $table) {
            if (Schema::hasColumn('comments', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('comments', 'reported')) {
                $table->dropColumn('reported');
            }
            if (Schema::hasColumn('comments', 'is_reviewed')) {
                $table->dropColumn('is_reviewed');
            }
        });
    }
};
