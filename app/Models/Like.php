<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    use HasFactory;

    /**
     * 可填充的字段
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'post_id', // 如果是帖子点赞
        'comment_id' // 如果是评论点赞
    ];

    /**
     * 确保 `user_id` 和 `post_id/comment_id` 组合唯一，防止重复点赞
     */
    protected $table = 'likes';

    /**
     * 关联帖子（如果 `Like` 是帖子点赞）
     */
    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * 关联评论（如果 `Like` 是评论点赞）
     */
    public function comment()
    {
        return $this->belongsTo(Comment::class);
    }

    /**
     * 关联用户（点赞人）
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 判断用户是否已经点赞过某个帖子或评论
     */
    public static function hasLiked($userId, $postId = null, $commentId = null)
    {
        return self::where('user_id', $userId)
            ->when($postId, fn($query) => $query->where('post_id', $postId))
            ->when($commentId, fn($query) => $query->where('comment_id', $commentId))
            ->exists();
    }
}
