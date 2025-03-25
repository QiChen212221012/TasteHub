<div class="bg-gray-800 text-white h-full w-64 fixed top-0 left-0 pt-5">
    <h2 class="text-center text-xl font-bold mb-5">Admin Panel</h2>
    <ul class="space-y-3 px-4">
        <li>
            <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 hover:bg-gray-700">📊 Dashboard</a>
        </li>
        <li>
            <a href="{{ route('admin.comments') }}" class="block px-4 py-2 hover:bg-gray-700">💬 Manage Comments</a>
        </li>
        <li>
            <!-- ✅ Logout 代码要改成 POST 表单 -->
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="block px-4 py-2 hover:bg-red-700 text-red-300">🚪 Logout</button>
            </form>            
        </li>
    </ul>
</div>
