<x-app-layout>
    <div class="bg-[#FCF8E8] min-h-screen py-12">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <!-- 标题 -->
            <h1 class="text-4xl font-bold text-center text-[#1E5631] mb-6">About TasteHub</h1>

            <!-- 介绍 -->
            <p class="text-lg text-center text-gray-700 max-w-2xl mx-auto leading-relaxed">
                TasteHub is an <span class="text-[#1E5631] font-semibold">AI-Powered Restaurant Forum</span>, where food lovers can share
                their dining experiences, post restaurant reviews, and explore AI-driven
                insights for the best culinary experiences.
            </p>

            <!-- 主要功能 -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-10">
                <!-- AI-Powered Insights -->
                <div class="bg-white p-6 rounded-lg shadow-lg border-2 border-[#D4AF37] text-center">
                    <h2 class="text-2xl font-semibold text-[#1E5631] flex items-center justify-center gap-2">
                        🤖 AI-Powered Insights
                    </h2>
                    <p class="text-gray-700 mt-3">
                        Our intelligent system analyzes reviews and detects **sentiments, sarcasm, and spam**, providing reliable and unbiased recommendations.
                    </p>
                </div>

                <!-- Community Driven -->
                <div class="bg-white p-6 rounded-lg shadow-lg border-2 border-[#D4AF37] text-center">
                    <h2 class="text-2xl font-semibold text-[#1E5631] flex items-center justify-center gap-2">
                        👥 Community-Driven
                    </h2>
                    <p class="text-gray-700 mt-3">
                        Connect with food lovers, share recommendations, and engage in meaningful discussions about **restaurant experiences**.
                    </p>
                </div>

                <!-- Smart Restaurant Data -->
                <div class="bg-white p-6 rounded-lg shadow-lg border-2 border-[#D4AF37] text-center">
                    <h2 class="text-2xl font-semibold text-[#1E5631] flex items-center justify-center gap-2">
                        📊 Smart Restaurant Data
                    </h2>
                    <p class="text-gray-700 mt-3">
                        AI-driven data **predicts trends, highlights top-rated places**, and helps users discover hidden gems.
                    </p>
                </div>
            </div>

            <!-- CTA 按钮 -->
            <div class="text-center mt-10">
                <a href="{{ route('register') }}" class="px-6 py-3 bg-[#1E5631] text-white text-lg font-semibold rounded-lg shadow-md hover:bg-[#144022] transition">
                    Join TasteHub Now
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
