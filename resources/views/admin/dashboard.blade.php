<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            🔍 Admin Dashboard - Comment Management
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md rounded-lg p-6">

                <!-- ✅ 仪表盘统计 -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6 text-center">
                    <a href="{{ route('admin.comments', ['status' => 'approved']) }}" class="block bg-green-100 p-4 rounded-lg shadow hover:bg-green-200 transition">
                        <h3 class="text-lg font-semibold text-green-800">✅ Approved Comments</h3>
                        <p class="text-2xl font-bold">{{ $approvedComments ?? 0 }}</p>
                    </a>
                    <a href="{{ route('admin.comments', ['status' => 'reported']) }}" class="block bg-red-100 p-4 rounded-lg shadow hover:bg-red-200 transition">
                        <h3 class="text-lg font-semibold text-red-800">🚩 Reported Comments</h3>
                        <p class="text-2xl font-bold">{{ $reportedComments ?? 0 }}</p>
                    </a>
                    <a href="{{ route('admin.comments', ['type' => 'sarcastic']) }}" class="block bg-yellow-100 p-4 rounded-lg shadow hover:bg-yellow-200 transition">
                        <h3 class="text-lg font-semibold text-yellow-800">😏 Sarcastic Comments</h3>
                        <p class="text-2xl font-bold">{{ $sarcasticComments ?? 0 }}</p>
                    </a>
                    <a href="{{ route('admin.comments', ['type' => 'offensive']) }}" class="block bg-gray-100 p-4 rounded-lg shadow hover:bg-gray-200 transition">
                        <h3 class="text-lg font-semibold text-gray-800">⚠ Offensive Comments</h3>
                        <p class="text-2xl font-bold">{{ $offensiveComments ?? 0 }}</p>
                    </a>
                </div>

                <!-- ✅ 确保所有评论正确显示 -->
                @if(!empty($allComments) && $allComments->count() > 0)
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">💬 All Comments</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse border border-gray-300">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="border border-gray-300 px-4 py-2">👤 User</th>
                                    <th class="border border-gray-300 px-4 py-2">💬 Content</th>
                                    <th class="border border-gray-300 px-4 py-2">🔍 Type</th>
                                    <th class="border border-gray-300 px-4 py-2">📌 Status</th>
                                    <th class="border border-gray-300 px-4 py-2 text-center">⚙ Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($allComments as $comment)
                                    @php
                                        $types = json_decode($comment->type, true);
                                        if (!is_array($types)) {
                                            $types = ['normal'];
                                        }
                                    @endphp
                                    <tr class="border border-gray-300">
                                        <td class="border border-gray-300 px-4 py-2">{{ $comment->user->name ?? 'Unknown' }}</td>
                                        <td class="border border-gray-300 px-4 py-2">{{ \Illuminate\Support\Str::limit($comment->content, 50) }}</td>

                                        <!-- ✅ 类型标签（支持多个类型） -->
                                        <td class="border border-gray-300 px-4 py-2 text-center">
                                            @foreach($types as $type)
                                                @if($type === 'sarcastic')
                                                    <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-lg">😏 Sarcastic</span>
                                                @elseif($type === 'offensive')
                                                    <span class="bg-gray-200 text-gray-800 px-3 py-1 rounded-lg">⚠ Offensive</span>
                                                @endif
                                            @endforeach
                                            @if(empty($types) || in_array('normal', $types))
                                                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-lg">✅ Normal</span>
                                            @endif
                                        </td>

                                        <!-- ✅ 状态标签 -->
                                        <td class="border border-gray-300 px-4 py-2 text-center">
                                            @if($comment->status == 'reported')
                                                <span class="bg-red-100 text-red-700 px-3 py-1 rounded-lg">🚩 Reported</span>
                                            @else
                                                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-lg">✅ Approved</span>
                                            @endif
                                        </td>

                                        <!-- ✅ 操作按钮 -->
                                        <td class="border border-gray-300 px-4 py-2 text-center flex space-x-2">
                                            <form action="{{ route('admin.comments.approve', $comment->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="bg-green-600 text-white px-4 py-1 rounded-lg hover:bg-green-700">✔ Approve</button>
                                            </form>
                                            <form action="{{ route('admin.comments.delete', $comment->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="bg-red-600 text-white px-4 py-1 rounded-lg hover:bg-red-700">❌ Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-6 text-gray-600">
                        💬 No comments found.
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-admin-layout>
