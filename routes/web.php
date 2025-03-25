<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\NLPController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ✅ 首页（所有用户可访问）
Route::get('/', function () {
    return view('home'); // 默认封面页
})->name('home');

// ✅ Laravel Breeze 认证路由
require __DIR__ . '/auth.php';

// ✅ 修正 Logout，确保使用 `POST` 方法
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

// ✅ 登录后，按照角色重定向到不同页面
Route::get('/redirect', function () {
    if (Auth::check()) {
        return Auth::user()->is_admin
            ? redirect()->route('admin.dashboard') // 管理员跳转后台
            : redirect()->route('main'); // 普通用户跳转主页
    }
    return redirect()->route('login');
})->name('redirect');

// ✅ 受保护的用户路由（需要登录）
Route::middleware(['auth'])->group(function () {
    Route::get('/main', [MainController::class, 'index'])->name('main');

    // ✅ 帖子相关
    Route::get('/posts/tag/{tag}', [PostController::class, 'filterByTag'])->name('posts.byTag');
    Route::resource('posts', PostController::class)->except(['edit', 'update', 'destroy']);

    // ✅ 只有帖子作者或管理员可以编辑 & 删除帖子
    Route::middleware('auth')->group(function () {
        Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit'); // 编辑帖子
        Route::put('/posts/{post}', [PostController::class, 'update'])->name('posts.update'); // 更新帖子
        Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy'); // 删除帖子
    });

    // ✅ 新增【帖子点赞】路由
    Route::post('/posts/{post}/like', [PostController::class, 'likePost'])->name('posts.like');

    // ✅ 评论相关
    Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::post('/comments/{id}/like', [CommentController::class, 'likeComment'])->name('comments.like');
    Route::post('/comments/{id}/report', [CommentController::class, 'report'])->name('comments.report');
    Route::delete('/comments/{id}', [CommentController::class, 'destroy'])->name('comments.destroy');
});

// ✅ 受保护的管理员路由（必须是管理员）
Route::middleware(['auth', 'isAdmin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/comments', [AdminController::class, 'manageComments'])->name('admin.comments');

    // ✅ 修正评论审批 & 删除路由，确保 ID 传递正确
    Route::patch('/admin/comments/{id}/approve', [AdminController::class, 'approveComment'])->name('admin.comments.approve');
    Route::delete('/admin/comments/{id}/delete', [AdminController::class, 'deleteComment'])->name('admin.comments.delete');
});

// ✅ NLP 相关 API 只能通过 `api.php` 访问
Route::post('/api/detect-sarcasm-and-offensive', [NLPController::class, 'detectSarcasmAndOffensive'])
    ->name('api.detectSarcasmAndOffensive');

//✅ 搜索功能
Route::get('/search', [PostController::class, 'search'])->name('search');

Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/privacy', [PageController::class, 'privacy'])->name('privacy');
Route::get('/terms', [PageController::class, 'terms'])->name('terms');

