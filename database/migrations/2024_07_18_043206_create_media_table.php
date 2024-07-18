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
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('path', 500);
            $table->string('name', 500);
            $table->string('folder', 500)->nullable();
            $table->unsignedTinyInteger('type')->default(0);
            $table->unsignedTinyInteger('favourite')->default(0);
            $table->unsignedTinyInteger('status')->default(config('app.status.active'));
            $table->text('log')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
