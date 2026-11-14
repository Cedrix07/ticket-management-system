<?php

namespace Database\Seeders;

use App\Models\TicketCategory;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TicketCategory::updateOrCreate(
            ['name' => 'Network'],
            ['description' => 'Issues with Wi-Fi, VPN, and office network connectivity.', 'is_active' => true]
        );

        TicketCategory::updateOrCreate(
            ['name' => 'Hardware'],
            ['description' => 'Physical equipment, laptops, monitors, displays, and peripherals.', 'is_active' => true]
        );

        TicketCategory::updateOrCreate(
            ['name' => 'Software'],
            ['description' => 'Applications, Figma licenses, email clients, and OS issues.', 'is_active' => true]
        );

        TicketCategory::updateOrCreate(
            ['name' => 'Access'],
            ['description' => 'Shared drive permissions, payroll portal access, and accounts.', 'is_active' => true]
        );
    }
}
