<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;

class PostService
{
    public function list(): Collection
    {
        return Post::latest()->get();
    }

    public function find(int $id): Post
    {
        return Post::findOrFail($id);
    }

    public function create(array $data, $coverImage = null): Post
    {
        if ($coverImage) {
            $data['cover_image'] = $coverImage->store('posts', 's3');
        }

        return Post::create($data);
    }

    public function update(int $id, array $data, $coverImage = null): Post
    {
        $post = Post::findOrFail($id);

        if ($coverImage) {
            if ($post->cover_image) {
                Storage::disk('s3')->delete($post->cover_image);
            }
            $data['cover_image'] = $coverImage->store('posts', 's3');
        }

        $post->update($data);

        return $post;
    }

    public function delete(int $id): void
    {
        $post = Post::findOrFail($id);

        if ($post->cover_image) {
            Storage::disk('s3')->delete($post->cover_image);
        }

        $post->delete();
    }
}