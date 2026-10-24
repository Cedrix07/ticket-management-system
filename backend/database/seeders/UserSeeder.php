<?php

namespace Database\Seeders;

use App\Enums\UserRole;
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
        // Admin
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'role' => UserRole::ADMIN,
                'password' => 'password',
                'is_active' => true,
            ]
        );

        // Support Agent
        User::firstOrCreate(
            ['email' => 'agent@example.com'],
            [
            'name' => 'Support Agent',
            'role' => UserRole::SUPPORT_AGENT,
            'password' => 'password',
            'is_active' => true,
        ]);

        // Active Employee
        User::firstOrCreate([
            'email' => 'john.doe@example.com'],
            [
            'name' => 'John Doe',
            'role' => UserRole::EMPLOYEE,
            'password' => 'password',
            'is_active' => true,
        ]);

        // Inactive Employee
        User::firstOrCreate([
            'email' => 'jane.doe@example.com'],
            [
            'name' => 'Jane Doe',
            'role' => UserRole::EMPLOYEE,
            'password' => 'password',
            'is_active' => false,
        ]);
    }
}
