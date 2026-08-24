<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('welcome_hero_image')->nullable()->after('facebook_link');
            $table->string('welcome_secondary_image')->nullable()->after('welcome_hero_image');
            $table->string('about_image')->nullable()->after('welcome_secondary_image');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['welcome_hero_image', 'welcome_secondary_image', 'about_image']);
        });
    }
};