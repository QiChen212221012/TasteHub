<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\MainController; // 引入 MainController
use App\Http\Controllers\CommentController; // 引入 CommentController
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// 首页路由
Route::get('/', function () {
    return view('home'); // 默认封面页
})->name('home');

// Breeze 提供的认证路由
require __DIR__ . '/auth.php';

// 自定义 Logout 路由（POST 方法）
Route::post('/logout', function () {
    auth()->logout();
    return redirect()->route('home')->with('status', 'You have been logged out.');
})->name('logout');

// 登录路由（默认由 Breeze 提供）
Route::get('/login', function () {
    return view('auth.login');
})->middleware('guest')->name('login');

// 注册路由（默认由 Breeze 提供）
Route::get('/register', function () {
    return view('auth.register');
})->middleware('guest')->name('register');

// 帖子相关路由
Route::get('/posts/tag/{tag}', [PostController::class, 'filterByTag'])->name('posts.byTag');
Route::resource('posts', PostController::class);

// Main 页面路由
Route::get('/main', [MainController::class, 'index'])->name('main');

// 添加评论相关的路由
Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('comments.store');
Route::post('/comments/{id}/like', [CommentController::class, 'like'])->name('comments.like');
Route::post('/comments/{id}/report', [CommentController::class, 'report'])->name('comments.report');
Route::delete('/comments/{id}', [CommentController::class, 'destroy'])->name('comments.destroy');

Route::middleware(['auth', 'isAdmin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::patch('/admin/comments/{comment}/report', [AdminController::class, 'reportComment'])->name('admin.comments.report');
    Route::delete('/admin/comments/{comment}/delete', [AdminController::class, 'deleteComment'])->name('admin.comments.delete');
});
