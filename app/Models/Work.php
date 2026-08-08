<?php

namespace App\Models;

use App\Enums\Category;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Override;

#[Fillable(['title', 'category', 'description'])]
class Work extends Model
{
    #[Override]
    protected function casts(): array
    {
        return [
            'category' => Category::class
        ];
    }

    public function images()
    {
        return $this->hasMany(WorkImage::class);
    }
}