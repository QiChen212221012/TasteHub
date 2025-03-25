<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    /**
     * 判断用户是否可以管理帖子（包括更新和删除）
     */
    public function manage(User $user, Post $post)
    {
        return $user->id === $post->user_id || $user->isAdmin();
    }

    /**
     * 判断用户是否可以更新帖子
     */
    public function update(User $user, Post $post)
    {
        return $user->id === $post->user_id;
    }

    /**
     * 判断用户是否可以删除帖子
     */
    public function delete(User $user, Post $post)
    {
        return $user->id === $post->user_id || $user->isAdmin();
    }
}
