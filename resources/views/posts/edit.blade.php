<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-900 leading-tight text-center">
            ✏ Edit Post
        </h2>
    </x-slot>

    <div class="py-12 bg-blue-50"> {{-- 背景色 --}}
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md rounded-lg p-6">
                {{-- ✅ 表单开始 --}}
                <form action="{{ route('posts.update', $post) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    {{-- 标题 --}}
                    <div class="mb-4">
                        <label class="block text-gray-700 font-bold mb-2">Title:</label>
                        <input type="text" name="title" value="{{ old('title', $post->title) }}"
                               class="w-full p-2 border border-gray-300 rounded-lg focus:ring focus:ring-blue-300">
                    </div>

                    {{-- 内容 --}}
                    <div class="mb-4">
                        <label class="block text-gray-700 font-bold mb-2">Content:</label>
                        <textarea name="content" rows="5"
                                  class="w-full p-2 border border-gray-300 rounded-lg focus:ring focus:ring-blue-300">{{ old('content', $post->content) }}</textarea>
                    </div>

                    {{-- 地址（可选）--}}
                    <div class="mb-4">
                        <label class="block text-gray-700 font-bold mb-2">Address (optional):</label>
                        <input type="text" name="address" value="{{ old('address', $post->address) }}"
                               class="w-full p-2 border border-gray-300 rounded-lg focus:ring focus:ring-blue-300">
                    </div>

                    {{-- 标签 --}}
                    <div class="mb-4">
                        <label class="block text-gray-700 font-bold mb-2">Tags:</label>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                            @foreach ($tags as $tag)
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="tags[]" value="{{ $tag->id }}"
                                           @if ($post->tags->contains($tag->id)) checked @endif>
                                    <span>{{ $tag->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- 现有图片（可删除） --}}
                    <div class="mb-4">
                        <label class="block text-gray-700 font-bold mb-2">Current Images:</label>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            @foreach (json_decode($post->images, true) ?? [] as $image)
                                <div class="relative">
                                    <img src="{{ asset('storage/' . $image) }}" class="w-full h-32 object-cover rounded-lg">
                                    <label class="absolute top-2 right-2 bg-red-500 text-white text-xs px-2 py-1 rounded cursor-pointer">
                                        <input type="checkbox" name="remove_images[]" value="{{ $image }}"> Remove
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- 上传新图片 --}}
                    <div class="mb-4">
                        <label class="block text-gray-700 font-bold mb-2">Upload New Images:</label>
                        <input type="file" name="images[]" multiple
                               class="w-full p-2 border border-gray-300 rounded-lg focus:ring focus:ring-blue-300">
                    </div>

                    {{-- 按钮 --}}
                    <div class="flex justify-between mt-6">
                        <a href="{{ route('posts.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                            Cancel
                        </a>
                        <button type="submit" class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                            Update Post
                        </button>
                    </div>
                </form>
                {{-- ✅ 表单结束 --}}
            </div>
        </div>
    </div>
</x-app-layout>
