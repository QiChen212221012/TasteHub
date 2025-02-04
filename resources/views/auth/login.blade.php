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
        .auth-container {
            background-color: white; /* 白色背景容器 */
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2); /* 添加阴影效果 */
            max-width: 400px;
            width: 90%; /* 响应式调整 */
            text-align: center;
        }
        .auth-container h1 {
            font-size: 24px;
            font-weight: bold;
            color: #4CAF50; /* 标题颜色 */
            margin-bottom: 20px;
        }
        .auth-container .form-control {
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 10px;
            margin-bottom: 15px;
        }
        .auth-container .btn-primary {
            background-color: #4CAF50;
            border-color: #4CAF50;
            color: white;
            font-weight: bold;
            padding: 10px;
            border-radius: 10px;
            width: 100%;
            font-size: 16px;
        }
        .auth-container .btn-primary:hover {
            background-color: #45a049;
        }
        .auth-container a {
            color: #4CAF50;
            text-decoration: none;
            font-size: 14px;
        }
        .auth-container a:hover {
            text-decoration: underline;
        }
        .remember-me {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            margin-bottom: 15px;
        }
        .remember-me input {
            margin-right: 10px;
        }
        .register-prompt {
            margin-top: 20px;
            font-size: 14px;
        }
    </style>

    <div class="auth-container">
        <h1>Login to TasteHub</h1>
        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email Address -->
            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="form-control" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div>
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input id="password" class="form-control"
                                type="password"
                                name="password"
                                required autocomplete="current-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Remember Me -->
            <div class="remember-me">
                <label for="remember_me">
                    <input id="remember_me" type="checkbox" name="remember">
                    <span>{{ __('Remember me') }}</span>
                </label>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between">
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    {{ __('Log in') }}
                </button>
            </div>
        </form>

        <!-- Register Prompt -->
        <div class="register-prompt">
            Don't have an account? 
            <a href="{{ route('register') }}">Register now</a>
        </div>
    </div>
</x-guest-layout>
