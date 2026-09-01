<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $accountsRole = Role::firstOrCreate(['name' => 'accounts']);
        $approverRole = Role::firstOrCreate(['name' => 'approver']);
        $auditorRole = Role::firstOrCreate(['name' => 'auditor']);

        // 2. System Administrator
        $admin = User::firstOrCreate(
            ['email' => 'admin@ims-malaysia.com'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );
        $admin->syncRoles([$adminRole]);

        // 3. Accounts User
        $accounts = User::firstOrCreate(
            ['email' => 'accounts@ims-malaysia.com'],
            [
                'name' => 'Nurul Huda (Accounts)',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );
        $accounts->syncRoles([$accountsRole]);

        // 4. Approver / Manager
        $approver = User::firstOrCreate(
            ['email' => 'approver@ims-malaysia.com'],
            [
                'name' => 'Razak Ahmad (Finance Director)',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );
        $approver->syncRoles([$approverRole]);

        // 5. Auditor (Read-Only)
        $auditor = User::firstOrCreate(
            ['email' => 'auditor@ims-malaysia.com'],
            [
                'name' => 'KPMG Malaysia (External Auditor)',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );
        $auditor->syncRoles([$auditorRole]);
    }
}
