<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\User;

class AdminController extends Controller
{
    /**
     * 确保所有 admin 页面都需要管理员权限
     */
    public function __construct()
    {
        $this->middleware('isAdmin');
    }

    /**
     * 显示管理员仪表盘
     */
    public function dashboard()
{
    $approvedComments = Comment::where('status', 'approved')->count();
    $reportedComments = Comment::where('status', 'reported')->count();
    $deletedComments = Comment::where('status', 'deleted')->count();
    $totalUsers = User::count();

    // ✅ 确保 JSON 有效再进行查询，防止 SQL 错误
    $sarcasticComments = Comment::whereRaw("JSON_VALID(type) AND JSON_CONTAINS(type, '\"sarcastic\"')")->count();
    $offensiveComments = Comment::whereRaw("JSON_VALID(type) AND JSON_CONTAINS(type, '\"offensive\"')")->count();

    // ✅ 获取所有评论
    $allComments = Comment::orderBy('created_at', 'desc')->with('user')->get();

    return view('admin.dashboard', [
        'approvedComments' => $approvedComments,
        'reportedComments' => $reportedComments,
        'deletedComments' => $deletedComments,
        'sarcasticComments' => $sarcasticComments,
        'offensiveComments' => $offensiveComments,
        'totalUsers' => $totalUsers,
        'allComments' => $allComments,
    ]);
}

    /**
     * 管理评论（支持筛选）
     */
    public function manageComments(Request $request)
    {
        $query = Comment::query()->orderBy('created_at', 'desc')->with('user');

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->has('type')) {
            $type = $request->input('type');
            $query->whereRaw("JSON_VALID(type) AND JSON_CONTAINS(type, ?)", [json_encode([$type])]);
        }

        $allComments = Comment::orderBy('created_at', 'desc')->with('user')->get();
        $approvedComments = Comment::where('status', 'approved')->count();
        $reportedComments = Comment::where('status', 'reported')->count();

        // ✅ 修正 JSON 查询，确保 `type` 是 JSON 格式
        $sarcasticComments = Comment::whereRaw("JSON_VALID(type) AND JSON_CONTAINS(type, ?)", [json_encode(["sarcastic"])])->count();
        $offensiveComments = Comment::whereRaw("JSON_VALID(type) AND JSON_CONTAINS(type, ?)", [json_encode(["offensive"])])->count();

        return view('admin.comments', [
            'comments' => $query->get(),
            'allComments' => $allComments,
            'approvedComments' => $approvedComments,
            'reportedComments' => $reportedComments,
            'sarcasticComments' => $sarcasticComments,
            'offensiveComments' => $offensiveComments,
        ]);
    }

    /**
     * 审核通过评论
     */
    public function approveComment($id)
    {
        $comment = Comment::findOrFail($id);

        if ($comment->status === 'reported') {
            $comment->update([
                'status' => 'approved',
            ]);
        }

        return redirect()->back()->with('success', 'Comment approved.');
    }

    /**
     * 删除评论（标记为删除状态，而非真正删除）
     */
    public function deleteComment($id)
    {
        $comment = Comment::findOrFail($id);

        // 🚀 直接从数据库删除，而不是仅修改 status
        $comment->delete();

        return redirect()->back()->with('success', 'Comment deleted successfully.');
    }
}
