<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'user_id',
        'post_id',
        'status',       // ✅ 继续使用 status（用户手动举报：reported / approved）
        'type',         // ✅ NLP 分类（sarcastic / offensive / normal）
        'is_reviewed',  // ✅ 管理员审核状态
    ];

    /**
     * ✅ 确保 `reported` 和 `is_reviewed` 被正确解析
     */
    protected $casts = [
        'reported' => 'boolean',    // 用户手动举报
        'is_reviewed' => 'boolean', // 是否被管理员审核
    ];

    /**
     * 定义与 Post 的关系
     */
    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * 定义与 User 的关系
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 定义与 Like 的多态关系 (评论可以被点赞)
     */
    public function likes()
    {
        return $this->hasMany(Like::class, 'comment_id');
    }
}
