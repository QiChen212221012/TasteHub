<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $post->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-6">
                
                <!-- 图片展示 -->
                @if (!empty($images) && is_array($images))
                    <div class="images-grid flex flex-wrap gap-4">
                        @foreach ($images as $image)
                            <div class="image-container">
                                <img src="{{ asset('storage/' . $image) }}" alt="Post Image" class="responsive-image">
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-center text-gray-500">No images available for this post.</p>
                @endif

                <!-- 帖子内容 -->
                <div class="p-6">
                    <h3 class="font-bold text-xl">{{ $post->title }}</h3>
                    <p class="text-gray-600 mt-4">{{ $post->content }}</p>

                    <!-- 地址 -->
                    @if (!empty($post->address))
                        <div class="mt-6">
                            <h4 class="font-bold text-lg">📍 Address:</h4>
                            <p class="text-gray-600">{{ $post->address }}</p>
                        </div>
                    @endif

                    <!-- 标签 -->
                    @if ($post->tags->isNotEmpty())
                        <div class="mt-6">
                            <h4 class="font-bold text-lg">🏷️ Tags:</h4>
                            <div class="tags-container flex gap-2">
                                @foreach ($post->tags as $tag)
                                    <span class="bg-blue-600 text-white px-3 py-1 rounded-full text-sm">
                                        {{ $tag->name }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <p class="text-gray-500">No tags selected.</p>
                    @endif

                    <!-- 评论区 -->
                    <div class="mt-6">
                        <h4 class="font-bold text-lg text-green-800">
                            Comments (<span id="comment-count">{{ $post->comments->count() }}</span>)
                        </h4>
                        <div class="comments-section border rounded-lg p-4 bg-white">
                            <p id="no-comments" class="text-gray-500" style="{{ $post->comments->isEmpty() ? '' : 'display: none;' }}">
                                No comments yet. Post your first comment.
                            </p>
                            <ul id="comment-list" class="comments-list flex flex-col space-y-4">
                                @foreach($post->comments as $comment)
                                    <li id="comment-{{ $comment->id }}" class="p-3 bg-white shadow rounded-lg">
                                        <div class="flex justify-between items-center">
                                            <div class="text-green-800 font-semibold">{{ $comment->user->name ?? 'Anonymous' }}</div>
                                            <small class="text-gray-500">{{ $comment->created_at->format('M d, Y H:i') }}</small>
                                        </div>
                                        <p class="mt-2 text-gray-700">{{ $comment->content }}</p>
                                        <div class="flex items-center space-x-2 mt-3">
                                            <!-- Like Button -->
                                            <button class="btn btn-like" data-id="{{ $comment->id }}">
                                                👍 Like (<span id="like-count-{{ $comment->id }}">{{ $comment->likes_count }}</span>)
                                            </button>

                                            <!-- Report Button -->
                                            <button class="btn btn-report {{ $comment->is_reported ? 'reported' : '' }}" data-id="{{ $comment->id }}">
                                                🚩 {{ $comment->is_reported ? 'Reported' : 'Report' }}
                                            </button>

                                            <!-- Delete Button -->
                                            @if(auth()->id() === $comment->user_id || auth()->user()->is_admin)
                                                <button class="btn btn-delete" data-id="{{ $comment->id }}">🗑️ Delete</button>
                                            @endif
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    <!-- 添加评论表单 -->
                    <div class="mt-6">
                        <h4 class="font-bold text-lg text-green-800">Add a Comment:</h4>
                        <div class="add-comment-form p-4 bg-yellow-50 rounded-lg shadow">
                            <form id="comment-form">
                                @csrf
                                <textarea id="comment-content" rows="3" class="form-control w-full mt-2 border border-gray-300 rounded p-2"
                                          placeholder="Write your comment here..." required></textarea>
                                <button type="submit" class="btn btn-submit mt-2 text-white">Submit</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // 处理点赞
    document.querySelector('#comment-list').addEventListener('click', function (event) {
        if (event.target.classList.contains('btn-like')) {
            let commentId = event.target.dataset.id;
            fetch(`/comments/${commentId}/like`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    event.target.innerHTML = `👍 Like (${data.likes})`;
                }
            });
        }
    });

    // 处理举报
    document.querySelector('#comment-list').addEventListener('click', function (event) {
        if (event.target.classList.contains('btn-report')) {
            let commentId = event.target.dataset.id;
            fetch(`/comments/${commentId}/report`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    event.target.innerHTML = "🚩 Reported";
                    event.target.classList.add("reported");
                }
            });
        }
    });

    // 处理删除
    document.querySelector('#comment-list').addEventListener('click', function (event) {
        if (event.target.classList.contains('btn-delete')) {
            let commentId = event.target.dataset.id;
            fetch(`/comments/${commentId}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrfToken }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert("Comment deleted successfully!");
                    document.querySelector(`#comment-${commentId}`).remove();
                }
            });
        }
    });

    // **发表评论**
    document.querySelector('#comment-form').addEventListener('submit', function (event) {
        event.preventDefault();
        let commentContent = document.querySelector('#comment-content').value;
        let postId = "{{ $post->id }}";

        fetch(`/posts/${postId}/comments`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ content: commentContent })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let newComment = `
                    <li id="comment-${data.comment.id}" class="p-3 bg-white shadow rounded-lg">
                        <div class="flex justify-between items-center">
                            <div class="text-green-800 font-semibold">${data.comment.user}</div>
                            <small class="text-gray-500">${data.comment.created_at}</small>
                        </div>
                        <p class="mt-2 text-gray-700">${data.comment.content}</p>
                        <div class="flex items-center space-x-2 mt-3">
                            <button class="btn btn-like" data-id="${data.comment.id}">
                                👍 Like (0)
                            </button>
                            <button class="btn btn-report" data-id="${data.comment.id}">
                                🚩 Report
                            </button>
                            <button class="btn btn-delete" data-id="${data.comment.id}">
                                🗑️ Delete
                            </button>
                        </div>
                    </li>
                `;
                document.querySelector('#comment-list').insertAdjacentHTML('beforeend', newComment);
                document.querySelector('#comment-content').value = ''; // 清空输入框
                document.querySelector('#comment-count').textContent = parseInt(document.querySelector('#comment-count').textContent) + 1;
                document.querySelector('#no-comments').style.display = 'none'; // 隐藏 "No comments yet."
            }
        });
    });

});
</script>

<style>
    .btn {
        padding: 8px 16px;
        border-radius: 5px;
        font-size: 14px;
        font-weight: bold;
        cursor: pointer;
        border: none;
        outline: none;
        transition: all 0.3s ease;
    }
    .btn-like { background-color: #66bb6a; color: white; }
    .btn-report { background-color: #ef5350; color: white; }
    .btn-report.reported { background-color: grey; }
    .btn-delete { background-color: #757575; color: white; }
    .btn-submit { background-color: #117c0c; color: white; }
</style>

</x-app-layout>
