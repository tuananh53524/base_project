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
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->tinyInteger('status')->default(config('app.status.active'));
            $table->text('can')->nullable();
            $table->timestamps();
        });

        // DB::table('roles')->insert(
        //     [
        //         [
        //             'id' => 1,
        //             'name' => 'root',
        //             'status' => config('app.status.active'),
        //             'can' => ''
        //         ],
        //         [
        //             'id' => 2,
        //             'name' => 'admin',
        //             'status' => config('app.status.active'),
        //             'can' => ''
        //         ],
        //         [
        //             'id' => 9,
        //             'name' => 'user',
        //             'status' => config('app.status.active'),
        //             'can' => ''
        //         ]
        //     ]
        // );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
