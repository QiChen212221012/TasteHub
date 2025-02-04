<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;

class AdminController extends Controller
{
    /**
     * 显示管理员仪表盘
     */
    public function index()
    {
        $approvedComments = Comment::where('reported', false)->count();
        $reportedComments = Comment::where('reported', true)->count();
        return view('admin.dashboard', compact('approvedComments', 'reportedComments'));
    }

    /**
     * 管理评论页面
     */
    public function manageComments()
    {
        $comments = Comment::all();
        return view('admin.comments', compact('comments'));
    }

    /**
     * 审核通过评论
     */
    public function approveComment($id)
    {
        $comment = Comment::findOrFail($id);
        $comment->reported = false;
        $comment->save();

        return redirect()->back()->with('success', 'Comment approved.');
    }

    /**
     * 删除评论
     */
    public function deleteComment($id)
    {
        $comment = Comment::findOrFail($id);
        $comment->delete();

        return redirect()->back()->with('success', 'Comment deleted.');
    }
}
