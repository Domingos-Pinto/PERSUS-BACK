<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Storage;

class SettingService
{
    private const IMAGE_FIELDS = [
        'welcome_hero_image',
        'welcome_secondary_image',
        'about_image',
        'about_image_2',
        'about_image_3',
        'footer_image_left',
        'footer_image_right',
    ];

    public function get(): Setting
    {
        return Setting::firstOrCreate([]);
    }

    public function update(array $data, array $images = []): Setting
    {
        $setting = $this->get();

        foreach (self::IMAGE_FIELDS as $field) {
            if (!empty($images[$field])) {
                if ($setting->{$field}) {
                    Storage::disk('s3')->delete($setting->{$field});
                }
                $data[$field] = $images[$field]->store('settings', 's3');
            }
        }

        $setting->update($data);
        return $setting;
    }
}
