<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    use HasFactory;

    protected $table = 'likes';

    /**
     * 允许批量赋值的字段
     */
    protected $fillable = [
        'user_id',
        'post_id',  // 仅用于帖子点赞
        'comment_id' // 仅用于评论点赞
    ];

    /**
     * 关联帖子（如果是对帖子的点赞）
     */
    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * 关联评论（如果是对评论的点赞）
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
     * 判断用户是否已点赞某个帖子或评论
     */
    public static function hasLiked($userId, $postId = null, $commentId = null)
    {
        return self::where('user_id', $userId)
            ->when($postId, fn($query) => $query->where('post_id', $postId))
            ->when($commentId, fn($query) => $query->where('comment_id', $commentId))
            ->exists();
    }
}
