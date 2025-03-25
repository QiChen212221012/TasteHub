<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TasteHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&family=Playfair+Display:wght@500;700&display=swap" rel="stylesheet">
    <link href="{{ asset('style.css') }}" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script> <!-- Font Awesome -->
</head>
<body>

<!-- ✅ 仅在 `main` 页面显示 TasteHub header -->
@if (Request::is('main'))
    <nav class="navbar navbar-expand-lg header-nav">
        <div class="container">
            <a class="navbar-brand title-highlight" href="{{ route('home') }}">
                TasteHub
            </a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-logout">Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
@endif

<!-- ✅ 创建帖子按钮 -->
<div class="container py-4 text-center">
    <a href="{{ route('posts.create') }}" class="btn btn-create">
        ✨ Create a Post
    </a>
</div>

<!-- ✅ 搜索框 -->
<div class="container my-4">
    <form action="{{ route('search') }}" method="GET" class="search-box">
        <input type="text" name="query" placeholder="Search posts..." class="form-control search-input">
        <button type="submit" class="btn btn-search">🔍 Search</button>
    </form>
</div>

<!-- ✅ 热门帖子（优化样式） -->
<div class="container py-4">
    <h1 class="popular-title">🔥 Popular Posts</h1>
    <div class="row">
        @if ($popularPosts->isEmpty())
            <div class="col-12">
                <div class="alert alert-warning text-center">
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
                            <img src="{{ asset('storage/' . $images[0]) }}" class="card-img-top post-img" alt="Post Image">
                        @else
                            <p class="text-center text-muted">No images available</p>
                        @endif

                        <div class="card-body">
                            <h5 class="card-title">{{ $post->title }}</h5>
                            <p class="card-text">{{ Str::limit($post->content, 100) }}</p>
                            <a href="{{ route('posts.show', $post) }}" class="btn btn-view-post">👀 View Post</a>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>

<!-- ✅ 标签部分 -->
<div class="tags-bar container py-4">
    <h3 class="tag-title">🎯 Explore Tags</h3>
    <div class="tags-container">
        @foreach ($tags as $tag)
            <span class="tag-pill">#{{ $tag->name }}</span>
        @endforeach
    </div>
</div>

<!-- ✅ 查看所有帖子按钮 -->
<div class="container py-4 text-center">
    <a href="{{ route('posts.index') }}" class="btn btn-view-all">
        📜 View All Posts
    </a>
</div>

<!-- ✅ 页脚 -->
<footer class="footer">
    <div class="container text-center">
        <div class="social-links">
            <a href="https://facebook.com" target="_blank" class="social-icon"><i class="fab fa-facebook"></i> Facebook</a>
            <a href="https://instagram.com" target="_blank" class="social-icon"><i class="fab fa-instagram"></i> Instagram</a>
            <a href="https://twitter.com" target="_blank" class="social-icon"><i class="fab fa-twitter"></i> Twitter</a>
        </div>
        <p>&copy; 2025 TasteHub | Designed with Love ❤️</p>
    </div>
</footer>

</body>
</html>
