<x-guest-layout>
    <style>
        body {
            background-color: #4CAF50; /* 高级绿色背景 */
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .reset-container {
            background-color: white; /* 白色背景容器 */
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2); /* 添加阴影效果 */
            max-width: 400px;
            width: 90%; /* 响应式宽度 */
            text-align: center;
        }
        .reset-container h1 {
            font-size: 24px;
            font-weight: bold;
            color: #4CAF50; /* 标题颜色 */
            margin-bottom: 20px;
        }
        .reset-container p {
            font-size: 14px;
            color: #555; /* 说明文字颜色 */
            margin-bottom: 20px;
        }
        .reset-container .form-control {
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 10px;
            margin-bottom: 15px;
            width: 100%;
        }
        .reset-container .btn-primary {
            background-color: #4CAF50;
            border-color: #4CAF50;
            color: white;
            font-weight: bold;
            padding: 10px;
            border-radius: 10px;
            width: 100%;
            font-size: 16px;
        }
        .reset-container .btn-primary:hover {
            background-color: #45a049;
        }
        .reset-container a {
            color: #4CAF50;
            text-decoration: none;
            font-size: 14px;
        }
        .reset-container a:hover {
            text-decoration: underline;
        }
    </style>

    <div class="reset-container">
        <h1>{{ __('Forgot Password?') }}</h1>
        <p>{{ __('No problem. Just let us know your email address, and we will email you a password reset link.') }}</p>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <!-- Email Address -->
            <div>
                <x-input-label for="email" :value="__('Email')" />
                <input id="email" class="form-control" type="email" name="email" :value="old('email')" required autofocus>
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Submit Button -->
            <div class="mt-4">
                <button type="submit" class="btn-primary">
                    {{ __('Email Password Reset Link') }}
                </button>
            </div>
        </form>

        <div class="mt-4">
            <a href="{{ route('login') }}">{{ __('Back to Login') }}</a>
        </div>
    </div>
</x-guest-layout>
