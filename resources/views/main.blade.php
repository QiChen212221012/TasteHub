<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TasteHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('style.css') }}" rel="stylesheet">
</head>
<body>

<!-- 导航栏 -->
<nav class="navbar navbar-expand-lg" style="background-color: #fac75c;">
    <div class="container">
        <a class="navbar-brand title-highlight" href="{{ route('home') }}" style="color: #0046a0;">TasteHub</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn" style="background-color:rgb(32, 153, 26); color: #f2ece0; font-weight: bold;">Logout</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- 创建帖子按钮 -->
<div class="container py-4 text-center">
    <a href="{{ route('posts.create') }}" class="btn btn-lg" style="background-color: #117c0c; color: #f2ece0; font-weight: bold;">
        Create a Post
    </a>
</div>

<!-- 热门帖子 -->
<div class="container py-4">
    <h1 class="text-center mb-4 page-title" style="color: #0046a0;">Popular Posts</h1>
    <div class="row">
        @if ($popularPosts->isEmpty())
            <div class="col-12">
                <div class="alert alert-warning text-center" style="background-color: #f2ece0; color: #117c0c;">
                    No popular posts found. Please check back later!
                </div>
            </div>
        @else
            @foreach ($popularPosts as $post)
                <div class="col-md-4">
                    <div class="card post-card">
                        @php
                            $images = is_string($post->images) ? json_decode($post->images, true) : $post->images;
                        @endphp

                        @if (!empty($images) && is_array($images) && count($images) > 0)
                            <img src="{{ asset('storage/' . $images[0]) }}" class="card-img-top" alt="Post Image">
                        @else
                            <p class="text-center text-muted">No images available</p>
                        @endif

                        <div class="card-body">
                            <h5 class="card-title" style="color: #117c0c;">{{ $post->title }}</h5>
                            <p class="card-text">{{ Str::limit($post->content, 100) }}</p>
                            <a href="{{ route('posts.show', $post) }}" class="btn btn-primary">View Post</a>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>

<!-- 标签部分 -->
<div class="tags-bar container py-4">
    <h3 style="color: #003366; font-weight: bold; margin-bottom: 10px;">Tags</h3>
    <div class="tags-container" style="display: flex; flex-wrap: wrap; gap: 15px;">
        @foreach ($tags as $tag)
            <a href="{{ route('posts.byTag', ['tag' => $tag->id]) }}" 
               class="tag-pill" 
               style="text-decoration: none; color: white; background-color: #00509E; padding: 10px 15px; border-radius: 20px; font-size: 16px; font-weight: bold; display: inline-block; transition: transform 0.2s, background-color 0.2s;">
                {{ $tag->name }}
            </a>
        @endforeach
    </div>
</div>

<!-- 查看所有帖子按钮 -->
<div class="container py-4 text-center">
    <a href="{{ route('posts.index') }}" class="btn btn-lg" style="background-color: #117c0c; color: white; font-weight: bold;">
        View All Posts
    </a>
</div>

<!-- 页脚 -->
<footer style="background-color: #117c0c; padding: 10px; text-align: center; color: #f2ece0; margin-top: 20px;">
    <p>&copy; 2025 TasteHub | Designed with Love</p>
</footer>
</body>
</html>
