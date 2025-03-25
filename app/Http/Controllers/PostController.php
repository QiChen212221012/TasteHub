<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Tag;
use App\Models\Comment;
use App\Models\Like;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::latest()->paginate(6);
        return view('posts.index', compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $tags = Tag::all(); // 传递所有标签到视图
        return view('posts.create', compact('tags'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'address' => 'nullable|string|max:255',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id', // 确保标签ID在数据库中存在
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // 处理图片上传
        $paths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $paths[] = $file->store('images', 'public');
            }
        }

        // 创建新帖子
        $post = Post::create([
            'user_id' => Auth::id(), // 绑定用户ID
            'title' => $request->title,
            'content' => $request->content,
            'address' => $request->address,
            'images' => json_encode($paths),
        ]);

        // 关联标签
        if ($request->has('tags')) {
            $post->tags()->sync($request->tags);
        }

        return redirect()->route('posts.index')->with('success', 'Post created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        $post->load(['tags', 'comments.user', 'comments.likes']);

        $images = is_string($post->images) ? json_decode($post->images, true) : $post->images;

        return view('posts.show', compact('post', 'images'));
    }

    public function edit(Post $post)
    {
        // ✅ 让 Laravel 自动检查权限
        $this->authorize('update', $post);

        $tags = Tag::all();
        return view('posts.edit', compact('post', 'tags'));
    }

    public function update(Request $request, Post $post)
{
    // ✅ 确保用户有权限更新帖子
    $this->authorize('update', $post);

    // ✅ 表单验证
    $request->validate([
        'title' => 'required|string|max:255',
        'content' => 'required|string',
        'address' => 'nullable|string|max:255',
        'tags' => 'nullable|array',
        'tags.*' => 'exists:tags,id',
        'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'remove_images' => 'nullable|array', // ✅ 确保 remove_images 是数组
    ]);

    // ✅ 获取当前的图片数据
    $paths = is_string($post->images) ? json_decode($post->images, true) : $post->images;
    $paths = $paths ?? []; // 避免 null 变成数组

    // ✅ 处理删除选中的图片
    if ($request->has('remove_images')) {
        foreach ($request->remove_images as $removeImage) {
            if (in_array($removeImage, $paths)) {
                Storage::disk('public')->delete($removeImage); // 删除存储的文件
                $paths = array_diff($paths, [$removeImage]); // 从数组中移除
            }
        }
    }

    // ✅ 处理新上传的图片（不会删除未被选中的旧图片）
    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $file) {
            $paths[] = $file->store('images', 'public'); // ✅ 追加到已有图片数组
        }
    }

    // ✅ 更新帖子数据
    $post->update([
        'title' => $request->title,
        'content' => $request->content,
        'address' => $request->address,
        'images' => json_encode(array_values($paths)), // ✅ 确保数组索引连续
    ]);

    // ✅ 处理标签更新
    if ($request->has('tags')) {
        $post->tags()->sync($request->tags);
    } else {
        $post->tags()->detach();
    }

    return redirect()->route('posts.index')->with('success', 'Post updated successfully!');
}

    public function destroy(Post $post)
    {
        // ✅ 让 Laravel 自动检查权限
        $this->authorize('delete', $post);

        $paths = is_string($post->images) ? json_decode($post->images, true) : $post->images;
        $paths = $paths ?? [];

        foreach ($paths as $path) {
            Storage::disk('public')->delete($path);
        }
        $post->delete();

        return redirect()->route('posts.index')->with('success', 'Post deleted successfully!');
    }
    
    /**
     * Store a comment for the given post.
     */
    public function storeComment(Request $request, $postId)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $post = Post::findOrFail($postId);

        $comment = $post->comments()->create([
            'content' => $validated['content'],
            'user_id' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Comment added successfully!',
            'comment' => [
                'id' => $comment->id,
                'content' => $comment->content,
                'user' => auth()->user()->name ?? 'Anonymous',
                'created_at' => $comment->created_at->format('M d, Y H:i'),
                'likes_count' => 0
            ]
        ]);
    }

    public function search(Request $request)
    {
        $query = $request->input('query');

        // ✅ 搜索标题 & 内容
        $posts = Post::where('title', 'like', "%$query%")
            ->orWhere('content', 'like', "%$query%")
            ->paginate(6); // 可调整分页数量

        return view('posts.search', compact('posts', 'query'));
    }

    /**
 * 处理帖子点赞或取消点赞
 */
public function likePost(Request $request, $postId)
{
    $userId = auth()->id();
    $post = Post::findOrFail($postId);

    // 检查用户是否已经点赞
    $existingLike = Like::where('user_id', $userId)
        ->where('post_id', $post->id)
        ->first();

    if ($existingLike) {
        // 取消点赞
        $existingLike->delete();
        return response()->json([
            'success' => true,
            'message' => 'Like removed.',
            'likes_count' => $post->likes()->count(), // 更新点赞数
        ]);
    }

    // 创建新的点赞
    Like::create([
        'user_id' => $userId,
        'post_id' => $post->id,
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Post liked!',
        'likes_count' => $post->likes()->count(), // 更新点赞数
    ]);
}
}
