<?php

namespace App\Services;

use App\Enums\PostStatus;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;

class PostService
{
    public function list(): Collection
    {
        return Post::with('author')->latest()->get();
    }

    public function listPublished(): Collection
    {
        return Post::with('author')
            ->where('status', PostStatus::PUBLISHED)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->latest('published_at')
            ->get();
    }

    public function find(int $id): Post
    {
        return Post::with('author')->findOrFail($id);
    }

    public function findBySlug(string $slug): Post
    {
        return Post::with('author')
            ->where('slug', $slug)
            ->where('status', PostStatus::PUBLISHED)
            ->firstOrFail();
    }

    public function create(array $data, $coverImage = null): Post
    {
        $data['slug'] = $this->uniqueSlug($data['title']);
        $data['user_id'] = Auth::id();

        if (($data['status'] ?? null) === PostStatus::PUBLISHED->value && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        if ($coverImage) {
            $data['cover_image'] = $coverImage->store('posts', 's3');
        }

        return Post::create($data);
    }

    public function update(int $id, array $data, $coverImage = null): Post
    {
        $post = Post::findOrFail($id);

        if (isset($data['title']) && $data['title'] !== $post->title) {
            $data['slug'] = $this->uniqueSlug($data['title'], $post->id);
        }

        if (($data['status'] ?? null) === PostStatus::PUBLISHED->value && !$post->published_at && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

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

    private function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $i = 1;

        while (
            Post::where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }
}
