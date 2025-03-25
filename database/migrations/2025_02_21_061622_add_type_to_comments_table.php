<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            // ✅ 确保 `type` 字段存在，并且是 JSON 类型，允许为空
            if (!Schema::hasColumn('comments', 'type')) {
                $table->json('type')->nullable()->default(json_encode([]))->after('status');
            }
        });

        // ✅ 兼容旧数据，避免 NULL 值
        DB::table('comments')->whereNull('type')->update(['type' => json_encode([])]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            if (Schema::hasColumn('comments', 'type')) {
                $table->dropColumn('type');
            }
        });
    }
};
