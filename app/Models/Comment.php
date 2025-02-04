<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'user_id',
        'post_id',
        'reported' // Ensure this is included if using reporting
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
     * 定义与 Like 的关系 (一个评论有多个点赞)
     */
    public function likes(): HasMany
    {
        return $this->hasMany(Like::class, 'comment_id');
    }
}
