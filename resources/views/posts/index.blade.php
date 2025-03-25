<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-900 leading-tight text-center">
            {{ __('All Posts') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-green-50"> {{-- 背景色更柔和 --}}
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                    {{ session('success') }}
                </div>
            @endif

            {{-- ✅ 帖子列表卡片布局 --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($posts as $post)
                    <div class="bg-white shadow-md rounded-lg overflow-hidden hover:shadow-xl transition transform hover:scale-105 mx-auto w-80"> {{-- 调整宽度 --}}
                        {{-- ✅ 封面图片 --}}
                        @php
                            $imageArray = is_array($post->images) ? $post->images : json_decode($post->images, true);
                        @endphp
                        <div class="relative">
                            <a href="{{ route('posts.show', $post) }}">
                                @if (!empty($imageArray) && is_array($imageArray))
                                    <img src="{{ asset('storage/' . $imageArray[0]) }}" alt="Post Image" class="w-full h-56 object-cover"> {{-- 适当调整图片比例 --}}
                                @else
                                    <img src="{{ asset('images/default.jpg') }}" alt="Default Image" class="w-full h-56 object-cover">
                                @endif
                            </a>
                        </div>

                        {{-- ✅ 文章内容 --}}
                        <div class="p-5">
                            <a href="{{ route('posts.show', $post) }}">
                                <h3 class="font-bold text-lg text-gray-900 hover:text-blue-600 transition">{{ $post->title }}</h3>
                            </a>
                            <p class="text-gray-600 mt-2">{{ Str::limit($post->content, 100) }}</p>

                            {{-- ❤️ 点赞数量 + 按钮 --}}
                            <div class="mt-4 flex items-center space-x-2">
                                <button class="like-btn" data-post-id="{{ $post->id }}">
                                    <span class="text-red-500 text-lg" id="like-icon-{{ $post->id }}">
                                        @if($post->likes->where('user_id', auth()->id())->count() > 0)
                                            ❤️
                                        @else
                                            🤍
                                        @endif
                                    </span>
                                </button>
                                <span class="text-gray-700 text-sm font-semibold" id="like-count-{{ $post->id }}">
                                    {{ $post->likes->count() ?? 0 }}
                                </span>
                            </div>

                            {{-- ✅ 操作按钮（编辑 & 删除）--}}
                            @auth
                                @if(auth()->user()->id === $post->user_id)
                                    <div class="mt-4 flex space-x-3">
                                        {{-- 编辑按钮 --}}
                                        <a href="{{ route('posts.edit', $post) }}" class="flex-1 text-center px-4 py-2 bg-[#1E5631] text-[#D4AF37] font-semibold rounded-lg hover:bg-[#D4AF37] hover:text-[#1E5631] transition">
                                            Edit
                                        </a>
                                        
                                        {{-- 删除按钮 --}}
                                        <form action="{{ route('posts.destroy', $post) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this post?');" class="flex-1">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-full px-4 py-2 bg-[#C0392B] text-white font-semibold rounded-lg hover:bg-[#A93226] transition">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            @endauth
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- ✅ 分页 --}}
            <div class="mt-6 flex justify-center">
                {{ $posts->links() }}
            </div>
        </div>
    </div>

    {{-- ✅ JavaScript 处理点赞 --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.like-btn').forEach(button => {
                button.addEventListener('click', function () {
                    let postId = this.dataset.postId;
                    let likeCount = document.getElementById(`like-count-${postId}`);
                    let likeIcon = document.getElementById(`like-icon-${postId}`);
                    
                    fetch(`/posts/${postId}/like`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            likeCount.textContent = data.likes_count;
                            likeIcon.textContent = data.likes_count > 0 ? '❤️' : '🤍';
                        }
                    })
                    .catch(error => console.error('Error:', error));
                });
            });
        });
    </script>
</x-app-layout>
