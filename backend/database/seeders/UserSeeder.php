<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Admin (Alex Morgan)
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Alex Morgan',
                'role' => UserRole::ADMIN,
                'password' => 'password',
                'is_active' => true,
            ]
        );

        // 2. Support Agent (Jamie Chen)
        User::updateOrCreate(
            ['email' => 'agent@example.com'],
            [
                'name' => 'Jamie Chen',
                'role' => UserRole::SUPPORT_AGENT,
                'password' => 'password',
                'is_active' => true,
            ]
        );

        // 3. Support Agent 2 (Sam Rivera)
        User::updateOrCreate(
            ['email' => 'sam@example.com'],
            [
                'name' => 'Sam Rivera',
                'role' => UserRole::SUPPORT_AGENT,
                'password' => 'password',
                'is_active' => true,
            ]
        );

        // 4. Employee (Maya Patel - matches employee@example.com)
        User::updateOrCreate(
            ['email' => 'employee@example.com'],
            [
                'name' => 'Maya Patel',
                'role' => UserRole::EMPLOYEE,
                'password' => 'password',
                'is_active' => true,
            ]
        );

        // 5. Employee (Noah Williams)
        User::updateOrCreate(
            ['email' => 'noah@example.com'],
            [
                'name' => 'Noah Williams',
                'role' => UserRole::EMPLOYEE,
                'password' => 'password',
                'is_active' => true,
            ]
        );

        // 6. Employee (Olivia Brown)
        User::updateOrCreate(
            ['email' => 'olivia@example.com'],
            [
                'name' => 'Olivia Brown',
                'role' => UserRole::EMPLOYEE,
                'password' => 'password',
                'is_active' => true,
            ]
        );

        // 7. Employee (Sofia Garcia)
        User::updateOrCreate(
            ['email' => 'sofia@example.com'],
            [
                'name' => 'Sofia Garcia',
                'role' => UserRole::EMPLOYEE,
                'password' => 'password',
                'is_active' => true,
            ]
        );
    }
}
