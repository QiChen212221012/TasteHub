<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Post;
use App\Policies\PostPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Post::class => PostPolicy::class, // ✅ 绑定 PostPolicy
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // ✅ Laravel 9+ 及以上版本会自动注册 Policies，不需要手动调用 registerPolicies()

        // ✅ 额外的权限控制（可选）
        Gate::define('manage-post', function ($user, $post) {
            return $user->id === $post->user_id || $user->isAdmin();
        });
    }
}
