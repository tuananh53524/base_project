<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('roles')->insert(
            [
                [
                    'id' => 1,
                    'name' => 'root',
                    'status' => config('app.status.active'),
                    'can' => ''
                ],
                [
                    'id' => 2,
                    'name' => 'admin',
                    'status' => config('app.status.active'),
                    'can' => ''
                ],
                [
                    'id' => 9,
                    'name' => 'user',
                    'status' => config('app.status.active'),
                    'can' => ''
                ]
            ]
        );
    }
}
