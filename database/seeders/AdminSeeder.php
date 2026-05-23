<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Admin::updateOrCreate(
            ['email' => 'admin@company.com'],
            [
                'name'      => 'Administrator',
                'email'     => 'admin@company.com',
                'password'  => \Illuminate\Support\Facades\Hash::make('admin123'),
                'role'      => 'superadmin',
                'is_active' => true,
            ]
        );
    }
}
