<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin', // ✅ 确保 is_admin 字段可填充
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_admin' => 'boolean', // ✅ 确保 is_admin 以布尔值返回
    ];

    /**
     * ✅ 确保用户是否为管理员
     */
    public function isAdmin(): bool
    {
        return (bool) $this->is_admin; // 确保返回 true/false
    }     

    /**
     * ✅ 定义用户与帖子 (Post) 的关系
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    /**
     * ✅ 定义用户与评论 (Comment) 的关系
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * ✅ 定义用户与点赞 (Like) 的关系
     */
    public function likes()
    {
        return $this->hasMany(Like::class);
    }
}
