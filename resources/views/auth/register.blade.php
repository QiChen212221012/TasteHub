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
        footer {
            margin-top: 20px;
            font-size: 12px;
            color: #777;
        }
    </style>

    <div class="auth-container">
        <h1>Register for TasteHub</h1>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Name -->
            <div>
                <x-input-label for="name" :value="__('Name')" />
                <x-text-input id="name" class="form-control" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <!-- Email Address -->
            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="form-control" type="email" name="email" :value="old('email')" required autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div>
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input id="password" class="form-control"
                                type="password"
                                name="password"
                                required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Confirm Password -->
            <div>
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                <x-text-input id="password_confirmation" class="form-control"
                                type="password"
                                name="password_confirmation" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <!-- Actions -->
            <div class="mt-4">
                <a href="{{ route('login') }}" class="underline text-sm text-gray-600 hover:text-gray-900">
                    {{ __('Already registered? Log in') }}
                </a>

                <button type="submit" class="btn btn-primary mt-4">
                    {{ __('Register') }}
                </button>
            </div>
        </form>
    </div>
</x-guest-layout>
