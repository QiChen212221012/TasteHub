<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use App\Models\Like;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Store a newly created comment in storage.
     */
    public function store(Request $request, $postId)
    {
        $request->validate([
            'content' => 'required|string|max:255',
        ]);

        $post = Post::findOrFail($postId);

        $comment = $post->comments()->create([
            'content' => $request->content,
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
                'likes_count' => 0 // Newly created comment has 0 likes
            ]
        ]);
    }

    /**
     * Like a comment.
     */
    public function like($id)
    {
        $comment = Comment::findOrFail($id);
        $userId = auth()->id();

        // Check if the user already liked this comment
        $existingLike = Like::where('user_id', $userId)
                            ->where('comment_id', $id)
                            ->first();

        if ($existingLike) {
            return response()->json([
                'success' => false,
                'message' => 'You have already liked this comment.'
            ]);
        }

        // Create a new like
        Like::create([
            'user_id' => $userId,
            'comment_id' => $id,
        ]);

        // Update likes count
        $likesCount = Like::where('comment_id', $id)->count();

        return response()->json([
            'success' => true,
            'likes' => $likesCount
        ]);
    }

    /**
     * Report a comment.
     */
    public function report($id)
    {
        $comment = Comment::findOrFail($id);
        $comment->update(['reported' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Comment reported successfully.'
        ]);
    }

    /**
     * Delete a comment.
     */
    public function destroy($id)
    {
        $comment = Comment::findOrFail($id);

        // Only allow comment creator or admin to delete
        if (auth()->id() === $comment->user_id || auth()->user()->is_admin) {
            $comment->delete();
            return response()->json([
                'success' => true,
                'message' => 'Comment deleted successfully.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Unauthorized action.'
        ], 403);
    }
}
