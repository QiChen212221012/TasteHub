<x-admin-layout> 
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            📝 Manage Comments
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md rounded-lg p-6">

                <!-- ✅ 筛选按钮 -->
                <div class="flex space-x-3 mb-5">
                    <a href="{{ route('admin.comments') }}" class="px-4 py-2 bg-gray-100 text-gray-800 rounded-lg shadow-sm hover:bg-gray-200">📃 All</a>
                    <a href="{{ route('admin.comments', ['status' => 'reported']) }}" class="px-4 py-2 bg-red-100 text-red-700 rounded-lg shadow-sm hover:bg-red-200">🚩 Reported</a>
                    <a href="{{ route('admin.comments', ['status' => 'approved']) }}" class="px-4 py-2 bg-green-100 text-green-700 rounded-lg shadow-sm hover:bg-green-200">✅ Approved</a>
                    <a href="{{ route('admin.comments', ['type' => 'sarcastic']) }}" class="px-4 py-2 bg-yellow-100 text-yellow-700 rounded-lg shadow-sm hover:bg-yellow-200">😏 Sarcastic</a>
                    <a href="{{ route('admin.comments', ['type' => 'offensive']) }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg shadow-sm hover:bg-gray-300">⚠ Offensive</a>
                </div>

                @if ($comments->isNotEmpty())
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
                                @foreach ($comments as $comment)
                                    <tr class="border border-gray-300">
                                        <td class="border border-gray-300 px-4 py-2">{{ $comment->user->name ?? 'Anonymous' }}</td>
                                        <td class="border border-gray-300 px-4 py-2">{{ $comment->content }}</td>
                                        
                                        <!-- ✅ 确保 `type` 正确解析 -->
                                        <td class="border border-gray-300 px-4 py-2 text-center">
                                            @php
                                                $types = json_decode($comment->type, true);
                                                if (!is_array($types) || empty($types)) {
                                                    $types = ['normal'];
                                                }
                                            @endphp
                                            
                                            @foreach($types as $type)
                                                @if($type === 'sarcastic')
                                                    <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-lg">😏 Sarcastic</span>
                                                @elseif($type === 'offensive')
                                                    <span class="bg-gray-200 text-gray-800 px-3 py-1 rounded-lg">⚠ Offensive</span>
                                                @endif
                                            @endforeach

                                            @if(in_array('normal', $types))
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
