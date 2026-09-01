<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserRoleSeeder::class,
            CompanySettingSeeder::class,
            CustomerSeeder::class,
            VendorSeeder::class,
            InvoiceSeeder::class,
            BillSeeder::class,
        ]);
    }
}
