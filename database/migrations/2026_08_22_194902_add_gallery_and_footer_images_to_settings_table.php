<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('about_image_2')->nullable()->after('about_image');
            $table->string('about_image_3')->nullable()->after('about_image_2');
            $table->string('footer_image_left')->nullable()->after('about_image_3');
            $table->string('footer_image_right')->nullable()->after('footer_image_left');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['about_image_2', 'about_image_3', 'footer_image_left', 'footer_image_right']);
        });
    }
};
