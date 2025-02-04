<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight page-title">
            🔍 Admin Dashboard - Comment Management
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <!-- 仪表盘统计 -->
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div class="bg-green-100 p-4 rounded-lg shadow">
                        <h3 class="text-lg font-semibold text-green-800">✅ Approved Comments</h3>
                        <p class="text-2xl font-bold">{{ $approvedCount }}</p>
                    </div>
                    <div class="bg-red-100 p-4 rounded-lg shadow">
                        <h3 class="text-lg font-semibold text-red-800">🚫 Deleted Comments</h3>
                        <p class="text-2xl font-bold">{{ $deletedCount }}</p>
                    </div>
                </div>

                <!-- 评论管理表 -->
                <table class="w-full border-collapse border border-gray-300">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border border-gray-300 p-2">User</th>
                            <th class="border border-gray-300 p-2">Comment</th>
                            <th class="border border-gray-300 p-2">Status</th>
                            <th class="border border-gray-300 p-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($comments as $comment)
                            <tr class="border border-gray-300">
                                <td class="p-2">{{ $comment->user->name }}</td>
                                <td class="p-2">{{ Str::limit($comment->content, 50) }}</td>
                                <td class="p-2 text-center">
                                    @if ($comment->reported)
                                        <span class="text-red-600">Reported 🚨</span>
                                    @else
                                        <span class="text-green-600">Approved ✅</span>
                                    @endif
                                </td>
                                <td class="p-2 flex space-x-2">
                                    @if (!$comment->reported)
                                        <form action="{{ route('admin.comments.report', $comment) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="bg-yellow-500 text-white px-2 py-1 rounded">Report</button>
                                        </form>
                                    @endif
                                    <form action="{{ route('admin.comments.delete', $comment) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-500 text-white px-2 py-1 rounded">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</x-app-layout>
