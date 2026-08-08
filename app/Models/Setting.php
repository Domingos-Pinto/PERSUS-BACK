<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['phone', 'email', 'address', 'whatsapp_link', 'instagram_link', 'facebook_link', 'maintenance_mode'])]
class Setting extends Model
{
    protected function casts(): array
    {
        return ['maintenance_mode' => 'boolean'];
    }
}