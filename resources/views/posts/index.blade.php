<x-app-layout> 
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Posts') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
@foreach ($posts as $post)
    <div class="bg-white shadow rounded-lg overflow-hidden">
        
        {{-- 确保 images 字段是数组 --}}
        @php
            $imageArray = is_array($post->images) ? $post->images : json_decode($post->images, true);
        @endphp

        @if (!empty($imageArray) && is_array($imageArray))
            <div class="images-grid">
                @foreach ($imageArray as $image)
                    <div class="image-container">
                        <img src="{{ asset('storage/' . $image) }}" alt="Post Image" class="responsive-image">
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-center text-gray-500">No images available for this post.</p>
        @endif

        <div class="p-4">
            <h3 class="font-bold text-lg">{{ $post->title }}</h3>
            <p class="text-gray-600 mt-2">{{ Str::limit($post->content, 100) }}</p>
            <div class="mt-4 flex justify-between items-center">
                <a href="{{ route('posts.show', $post) }}" class="text-blue-600 text-sm">Read More</a>
                <span class="text-gray-600 text-sm">❤️ {{ count($post->likes ?? []) }}</span>
            </div>
        </div>
    </div>
@endforeach

            <div class="mt-6">
                {{ $posts->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
