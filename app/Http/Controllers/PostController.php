<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Tag;
use App\Models\Comment;
use Illuminate\Http\Request;
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

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        $tags = Tag::all(); // 传递所有标签到视图
        return view('posts.edit', compact('post', 'tags'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'address' => 'nullable|string|max:255',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $paths = is_string($post->images) ? json_decode($post->images, true) : $post->images;
        $paths = $paths ?? [];

        if ($request->hasFile('images')) {
            foreach ($paths as $path) {
                Storage::disk('public')->delete($path);
            }

            $paths = [];
            foreach ($request->file('images') as $file) {
                $paths[] = $file->store('images', 'public');
            }
        }

        // 更新帖子数据
        $post->update([
            'title' => $request->title,
            'content' => $request->content,
            'address' => $request->address,
            'images' => json_encode($paths),
        ]);

        // 更新标签
        if ($request->has('tags')) {
            $post->tags()->sync($request->tags);
        } else {
            $post->tags()->detach();
        }

        return redirect()->route('posts.index')->with('success', 'Post updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
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
}
