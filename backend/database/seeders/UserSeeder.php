<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Product Manager',
            'email' => 'pm@example.com',
            'password' => Hash::make('password'),
            'role' => 'product_manager',
        ]);

        User::create([
            'name' => 'Project Manager',
            'email' => 'pj@example.com',
            'password' => Hash::make('password'),
            'role' => 'project_manager',
        ]);

        User::create([
            'name' => 'Engineer',
            'email' => 'engineer@example.com',
            'password' => Hash::make('password'),
            'role' => 'engineer',
        ]);
    }
}
