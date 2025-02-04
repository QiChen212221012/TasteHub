<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;

class MainController extends Controller
{
    /**
     * 显示主页面
     */
    public function index()
    {
        // 获取所有标签
        $tags = Tag::all();

        // 获取热门帖子（按点赞数和评论数排序，取前 3 条）
        $popularPosts = Post::withCount(['likes', 'comments'])
            ->orderByDesc('likes_count') // 按点赞数排序
            ->orderByDesc('comments_count') // 按评论数排序
            ->take(3)
            ->get();

        // 返回视图并传递数据
        return view('main', [
            'tags' => $tags,
            'popularPosts' => $popularPosts,
        ]);
    }
}
