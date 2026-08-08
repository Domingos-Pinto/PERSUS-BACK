<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['work_id', 'order', 'path'])]
class WorkImage extends Model
{
    public function work()
    {
        return $this->belongsTo(Work::class);
    }
}