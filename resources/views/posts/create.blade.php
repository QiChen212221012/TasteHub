<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight page-title">
            🌿 Create a New Restaurant Post
        </h2>
    </x-slot>

    <div class="py-12 create-post-page"> {{-- ✅ 统一背景颜色 --}}
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">    
            <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data" class="post-form">
                @csrf
                <!-- 标题 -->
                <div class="mb-4">
                    <label for="title" class="block text-sm font-medium">Restaurant Name</label>
                    <input type="text" name="title" id="title" class="input-field" placeholder="Enter the restaurant name..." required>
                </div>

                <!-- 内容 -->
                <div class="mb-4">
                    <label for="content" class="block text-sm font-medium">Description</label>
                    <textarea name="content" id="content" rows="4" class="input-field" placeholder="Describe the restaurant (e.g., cuisine, special dishes, etc.)..." required></textarea>
                </div>

                <!-- 多图片上传 -->
                <div class="mb-4">
                    <label for="images" class="block text-sm font-medium">Upload Images</label>
                    <input type="file" name="images[]" id="images" class="input-field" accept="image/*" multiple>
                </div>

                <!-- 地址 -->
                <div class="mb-4">
                    <label for="address" class="block text-sm font-medium">Address</label>
                    <input type="text" name="address" id="address" class="input-field" placeholder="Enter restaurant address..." required>
                    <button type="button" onclick="getLocation()" class="address-btn">Use Current Location</button>
                </div>

                <!-- 标签 -->
                <div class="mb-4">
                    <label for="tags" class="block text-sm font-medium">Tags (Choose up to 6)</label>
                    <div class="tags-container">
                        @foreach ($tags as $tag)
                        <label class="tag-label">
                            <input type="checkbox" name="tags[]" value="{{ $tag->id }}" class="tag-checkbox" onchange="limitTagSelection(this)">
                            <span>{{ $tag->name }}</span>
                        </label>
                        @endforeach
                    </div>
                    <p id="tagCountMessage" style="margin-top: 10px; color: #666;">No tags selected.</p>
                </div>

                <!-- 提交按钮 -->
                <button type="submit" class="submit-btn">🚀 Submit</button>
            </form>
        </div>
    </div>

    <!-- 定位脚本和标签限制脚本 -->
    <script>
        function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(success, error);
    } else {
        alert("Geolocation is not supported by this browser.");
    }
}

function success(position) {
    const lat = position.coords.latitude;
    const long = position.coords.longitude;

    // 调用 Google Maps Geocoding API
    const apiKey = "AIzaSyBDhd2lW-G9Q1kL6xUK85UXU7oRGPfnjPE"; // 替换为您的密钥
    const url = `https://maps.googleapis.com/maps/api/geocode/json?latlng=${lat},${long}&key=${apiKey}`;

    fetch(url)
        .then((response) => response.json())
        .then((data) => {
            if (data.status === "OK") {
                const address = data.results[0].formatted_address;
                document.getElementById("address").value = address;
            } else {
                alert("Unable to fetch location details: " + data.status);
            }
        })
        .catch(() => {
            alert("Unable to fetch location details.");
        });
}

function error(err) {
    alert(`Unable to fetch location details: ${err.message}`);
}

// 限制标签选择数量
function limitTagSelection(checkbox) {
    let selectedTags = document.querySelectorAll('.tag-checkbox:checked').length;
    let message = document.getElementById("tagCountMessage");

    if (selectedTags > 6) {
        alert("You can only select up to 6 tags!");
        checkbox.checked = false;
        return;
    }

    message.innerText = selectedTags === 0 ? "No tags selected." : `${selectedTags} tags selected.`;
}
    </script>
</x-app-layout>
