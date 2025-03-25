<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Admin Panel - {{ config('app.name', 'TasteHub') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Custom Styles -->
    <link rel="stylesheet" href="{{ asset('style.css') }}">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="flex min-h-screen">

        <!-- ✅ 侧边栏 -->
        <aside class="w-64 bg-gray-800 text-white h-screen fixed top-0 left-0">
            @include('admin.sidebar') <!-- ✅ 确保这里正确引入 -->
        </aside>

        <!-- ✅ 主要内容区域 -->
        <div class="flex-1 ml-64">
            <!-- ✅ 顶部导航 -->
            @include('layouts.navigation')

            <!-- ✅ 页面标题 -->
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- ✅ 页面内容 -->
            <main class="p-6">
                {{ $slot }}
            </main>
        </div>

    </div>
</body>
</html>
