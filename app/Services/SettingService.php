<?php

namespace App\Services;

use App\Models\Setting;

class SettingService
{
    public function get(): Setting
    {
        return Setting::firstOrCreate([]);
    }

    public function update(array $data): Setting
    {
        $setting = $this->get();
        $setting->update($data);

        return $setting;
    }
}