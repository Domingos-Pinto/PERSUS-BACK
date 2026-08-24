<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'phone', 'email', 'address', 'whatsapp_link', 'instagram_link', 'facebook_link',
    'maintenance_mode', 'welcome_hero_image', 'welcome_secondary_image', 'about_image',
    'about_image_2', 'about_image_3', 'footer_image_left', 'footer_image_right',
])]
class Setting extends Model
{
    protected function casts(): array
    {
        return ['maintenance_mode' => 'boolean'];
    }
}
