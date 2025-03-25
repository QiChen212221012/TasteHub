<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- ✅ 确保 CSRF 令牌可用 -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        window.csrfToken = "{{ csrf_token() }}"; // ✅ 让 JS 能全局访问 CSRF 令牌
    </script>

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- ✅ 加载 Tailwind（确保它可用） -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- ✅ Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- ✅ 自定义 CSS -->
    <link rel="stylesheet" href="{{ asset('style.css') }}">

    <!-- ✅ 确保 Tailwind 能覆盖冲突的 CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen flex flex-col">  {{-- ✅ 解决空白问题 --}}

        <!-- ✅ 其他页面只显示导航栏 -->
        @include('layouts.navigation')

        <!-- ✅ 页面标题 -->
        @if (isset($header))
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <!-- ✅ 页面内容 -->
        <main class="flex-grow">  {{-- ✅ 去掉 `mt-6` 避免顶部空白 --}}
            {{ $slot }}
        </main>
    </div>
</body>
</html>
