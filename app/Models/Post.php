<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['title', 'content', 'cover_image', 'published_at'])]
class Post extends Model
{
    protected function casts(): array
    {
        return ['published_at' => 'datetime'];
    }
}