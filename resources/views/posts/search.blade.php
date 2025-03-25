<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-900 leading-tight text-center">
            Search Results for: <span class="text-green-500">{{ $query }}</span>
        </h2>
    </x-slot>

    <div class="py-12 bg-green-50 min-h-screen"> {{-- ✅ 确保背景色填满整个页面 --}}
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if($posts->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($posts as $post)
                        <div class="bg-white shadow-md rounded-lg overflow-hidden hover:shadow-xl transition transform hover:scale-105">
                            {{-- ✅ 封面图片 --}}
                            @php
                                $imageArray = is_array($post->images) ? $post->images : json_decode($post->images, true);
                            @endphp
                            <div class="relative">
                                <a href="{{ route('posts.show', $post) }}">
                                    @if (!empty($imageArray) && is_array($imageArray))
                                        <img src="{{ asset('storage/' . $imageArray[0]) }}" alt="Post Image" class="w-full h-64 object-cover"> {{-- ✅ 调整高度 --}}
                                    @else
                                        <img src="{{ asset('images/default.jpg') }}" alt="Default Image" class="w-full h-64 object-cover">
                                    @endif
                                </a>
                            </div>

                            {{-- ✅ 文章内容 --}}
                            <div class="p-5">
                                <a href="{{ route('posts.show', $post) }}">
                                    <h3 class="font-bold text-xl text-gray-900 hover:text-green-600 transition">
                                        {!! str_replace($query, "<span class='bg-yellow-200 px-1'>$query</span>", $post->title) !!}
                                    </h3>
                                </a>
                                <p class="text-gray-600 mt-2">
                                    {!! str_replace($query, "<span class='bg-yellow-200 px-1'>$query</span>", Str::limit($post->content, 100)) !!}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- ✅ 分页 --}}
                <div class="mt-6 flex justify-center pb-12"> {{-- ✅ 增加 padding-bottom，避免底部空白 --}}
                    {{ $posts->links() }}
                </div>
            @else
                <p class="text-gray-600 text-center mt-10">No results found for <strong>{{ $query }}</strong></p>
            @endif
        </div>
    </div>
</x-app-layout>
