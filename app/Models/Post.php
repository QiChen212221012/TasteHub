<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'content',
        'images', // 支持多图片的 JSON 格式
        'address', // 添加地址字段
    ];

    /**
     * Cast attributes to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'images' => 'array', // 确保 images 字段被转换为数组
    ];

    /**
     * Define the relationship with the Tag model.
     * A post can have many tags.
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'post_tag', 'post_id', 'tag_id')->withTimestamps();
    }

    /**
     * Define the relationship with the Comment model.
     * A post can have many comments.
     */
    public function comments()
    {
        return $this->hasMany(Comment::class, 'post_id')->latest(); // 按时间排序评论，最新的评论在前
    }

    /**
     * Define the relationship with the Like model.
     * A post can have many likes.
     */
    public function likes()
    {
        return $this->hasMany(Like::class, 'post_id');
    }

    /**
     * 获取帖子关联的标签名称列表（用于前端显示）。
     */
    public function getTagNamesAttribute()
    {
        return $this->tags->isNotEmpty() ? $this->tags->pluck('name')->implode(', ') : 'No Tags';
    }
}
