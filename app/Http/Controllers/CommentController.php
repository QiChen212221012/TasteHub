<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use App\Models\Like;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class CommentController extends Controller
{
    /**
     * 存储评论
     */
    public function store(Request $request, $postId)
    {
        $request->validate([
            'content' => 'required|string|max:255',
        ]);

        $post = Post::findOrFail($postId);
        $text = $request->content;
        $userId = auth()->id();
        $status = 'approved'; // 默认状态
        $type = []; // NLP 识别的类别

        try {
            $client = new Client(['verify' => false, 'timeout' => 10]);
            $apiKey = config('app.huggingface_api_key');
            $sarcasmApiUrl = env('NLP_SARCASTIC_API_URL');
            $offensiveApiUrl = env('NLP_OFFENSIVE_API_URL');

            if (!$apiKey || !$sarcasmApiUrl || !$offensiveApiUrl) {
                throw new \Exception('Missing Hugging Face API configuration');
            }

            // 讽刺检测
            $sarcasmResponse = $client->post($sarcasmApiUrl, [
                'headers' => [
                    'Authorization' => "Bearer {$apiKey}",
                    'Content-Type' => 'application/json',
                ],
                'json' => ['inputs' => $text],
                'http_errors' => false
            ]);
            $sarcasmResult = json_decode($sarcasmResponse->getBody(), true);
            
            Log::info('Sarcasm API Response:', is_array($sarcasmResult) ? $sarcasmResult : ['raw' => $sarcasmResult]);

            if (!empty($sarcasmResult[0])) {
                foreach ($sarcasmResult[0] as $item) {
                    if ($item['label'] === 'LABEL_2' && $item['score'] > 0.6) { // 负面情绪
                        $type[] = 'sarcastic';
                    }
                }
            }

            // 不当言论检测
            $offensiveResponse = $client->post($offensiveApiUrl, [
                'headers' => [
                    'Authorization' => "Bearer {$apiKey}",
                    'Content-Type' => 'application/json',
                ],
                'json' => ['inputs' => $text],
                'http_errors' => false
            ]);
            $offensiveResult = json_decode($offensiveResponse->getBody(), true);

            Log::info('Offensive API Response:', is_array($offensiveResult) ? $offensiveResult : ['raw' => $offensiveResult]);

            if (!empty($offensiveResult[0])) {
                foreach ($offensiveResult[0] as $item) {
                    if ($item['label'] === 'offensive' && $item['score'] > 0.5) {
                        $type[] = 'offensive';
                    }
                }
            }

            // 状态设置
            if (!empty($type)) {
                $status = 'reported'; // NLP 识别到问题，状态改为 reported
            }

        } catch (\Exception $e) {
            Log::error('NLP API Request Failed: ' . $e->getMessage());
        }

        // 🚩 **确保 `type` 存储 JSON 数组，避免存储 `"normal"`**
        $comment = $post->comments()->create([
            'content' => $text,
            'user_id' => $userId,
            'status' => $status,
            'type' => json_encode($type, JSON_UNESCAPED_UNICODE), // 正确存储 JSON 数组
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Comment added successfully!',
            'comment' => [
                'id' => $comment->id,
                'content' => $comment->content,
                'user' => auth()->user()->name ?? 'Anonymous',
                'created_at' => $comment->created_at->format('M d, Y H:i'),
                'status' => $status,
                'type' => $type,
            ],
        ]);
    }

    /**
     * 获取所有评论（支持 type 和 status 筛选）
     */
    public function getComments(Request $request)
    {
        $query = Comment::query()->with('user');

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->has('type')) {
            $type = $request->input('type');
            $query->whereRaw("JSON_CONTAINS(type, ?)", [json_encode($type)]);
        }

        $comments = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'comments' => $comments,
        ]);
    }

    /**
     * 用户举报评论
     */
    public function report($id)
    {
        $comment = Comment::findOrFail($id);

        if ($comment->status === 'reported') {
            $comment->update(['status' => 'approved']);
            return response()->json([
                'success' => true,
                'message' => 'Comment report removed.',
                'status' => 'approved'
            ]);
        } else {
            $comment->update(['status' => 'reported']);
            return response()->json([
                'success' => true,
                'message' => 'Comment reported successfully.',
                'status' => 'reported'
            ]);
        }
    }

    /**
     * 管理员审核通过评论
     */
    public function approveComment($id)
    {
        $comment = Comment::findOrFail($id);

        if ($comment->status === 'reported') {
            $comment->update([
                'status' => 'approved'
            ]);
        }

        return redirect()->back()->with('success', 'Comment approved.');
    }

    /**
     * 删除评论
     */
    public function destroy($id)
    {
        $comment = Comment::findOrFail($id);

        if (auth()->id() === $comment->user_id || auth()->user()->is_admin) {
            $comment->delete();
            return response()->json([
                'success' => true,
                'message' => 'Comment deleted successfully.',
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Unauthorized action.'], 403);
    }

    /**
     * 点赞或取消点赞评论
     */
    public function likeComment($commentId)
    {
        $comment = Comment::findOrFail($commentId);
        $userId = auth()->id();

        $existingLike = Like::where('user_id', $userId)
            ->where('comment_id', $comment->id)
            ->first();

        if ($existingLike) {
            $existingLike->delete();

            return response()->json([
                'success' => true,
                'liked' => false,
                'likes_count' => $comment->likes()->count(),
            ]);
        }

        Like::create([
            'user_id' => $userId,
            'comment_id' => $comment->id,
        ]);

        return response()->json([
            'success' => true,
            'liked' => true,
            'likes_count' => $comment->likes()->count(),
        ]);
    }
}
