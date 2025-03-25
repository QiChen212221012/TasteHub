<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NLPController;
use App\Http\Controllers\CommentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| 这里注册 API 路由，所有 API 路由都属于 "api" 中间件组
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// ✅ Hugging Face NLP API 路由
Route::post('/api/detect-sarcasm-and-offensive', [NLPController::class, 'detectSarcasmAndOffensive'])
    ->name('api.detectSarcasmAndOffensive');
