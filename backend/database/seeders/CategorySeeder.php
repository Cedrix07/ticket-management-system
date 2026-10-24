<?php

namespace Database\Seeders;

use App\Models\TicketCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       TicketCategory::firstOrCreate(
            ['name' => 'Hardware'],
            ['description' => 'Physical equipment, laptops, and peripherals.']
        );

        TicketCategory::firstOrCreate(
            ['name' => 'Software & Access'],
            ['description' => 'Application issues, login problems, and access rights.']
        );

        TicketCategory::firstOrCreate(
            ['name' => 'Network Connectivity'],
            ['description' => 'Issues with Wi-Fi, VPN, and internet access.']
        );
    }
}
