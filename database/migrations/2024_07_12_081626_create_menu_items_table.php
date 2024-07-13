<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('menu_id')->default(0);
            $table->string('name');
            $table->string('href')->nullable();
            $table->string('target')->nullable()->comment('_blank|_self|_parent|_top|framename');
            $table->unsignedInteger('parent_id')->default(0);
            $table->unsignedTinyInteger('level')->default(0);
            $table->unsignedTinyInteger('status')->default(config('app.status.active'));
            $table->unsignedTinyInteger('order_number')->nullable()->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
