<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('site_title')->nullable();
            $table->string('default_title')->nullable();
            $table->string('company')->nullable();
            $table->string('phone')->nullable();
            $table->string('fax')->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->string('crop_dimensions')->nullable();
            $table->string('image')->nullable();
            $table->string('brochure')->nullable();
            $table->string('youtube')->nullable();
            $table->string('twitter')->nullable();
            $table->string('zalo')->nullable();
            $table->string('tiktok')->nullable();
            $table->string('fb_domain_verification')->nullable();
            $table->string('facebook_page_url')->nullable();
            $table->string('fb_app_id')->nullable();
            $table->string('fb_admins')->nullable();
            $table->string('google_site_verification')->nullable();
            $table->text('google_tracking_code')->nullable();
            $table->text('facebook_pixel_code')->nullable();
            $table->text('end_body_scripts')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->text('meta_description')->nullable();
            $table->timestamps();
        });

        DB::table('settings')->insert([
            [
                'site_title' => 'Tiệm bánh nhà Mon',
                'default_title' => 'Tiệm bánh nhà Mon',
                'company' => 'Tiệm bánh nhà Mon',
                'phone' => '0964131655',
                'fax' => null,
                'email' => 'info@hbr.edu.vn',
                'address' => '201 Cầu Giấy, Hà Nội',
                'crop_dimensions' => null,
                'image' => null,
                'youtube' => null,
                'twitter' => null,
                'zalo' => null,
                'tiktok' => null,
                'fb_domain_verification' => null,
                'facebook_page_url' => null,
                'fb_app_id' => null,
                'fb_admins' => null,
                'google_site_verification' => null,
                'google_tracking_code' => null,
                'facebook_pixel_code' =>null,
                'end_body_scripts' => null,
                'meta_title' => 'Tiệm bánh nhà Mon',
                'meta_keywords' => 'Tiệm bánh nhà Mon',
                'meta_description' => 'Tiệm bánh nhà Mon',
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
