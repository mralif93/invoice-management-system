<?php

namespace Database\Seeders;

use App\Models\Vendor;
use Illuminate\Database\Seeder;

class VendorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vendors = [
            [
                'name' => 'Tekno Logistik Cloud Services Sdn. Bhd.',
                'ssm_brn' => '201501023456 (1154321-X)',
                'tin_number' => 'C12345678000',
                'sst_number' => 'W10-1808-11000077',
                'email' => 'billing@teknologistik.com',
                'phone' => '+603-8899-7700',
                'bank_name' => 'Maybank',
                'bank_account_number' => '5140-9988-1122',
            ],
            [
                'name' => 'Wira Network Telecom Sdn. Bhd.',
                'ssm_brn' => '201701056789 (1243567-V)',
                'tin_number' => 'C18765432000',
                'sst_number' => 'W10-1808-44000099',
                'email' => 'finance@wiranetwork.my',
                'phone' => '+603-7766-5544',
                'bank_name' => 'CIMB Bank',
                'bank_account_number' => '800-1234-5678',
            ],
            [
                'name' => 'Pustaka Mega Stationery & Hardware',
                'ssm_brn' => '001893847-X',
                'tin_number' => 'C10928374000',
                'sst_number' => null,
                'email' => 'orders@pustakamega.com',
                'phone' => '+603-5511-2233',
                'bank_name' => 'Public Bank',
                'bank_account_number' => '312-8877-665',
            ],
        ];

        foreach ($vendors as $data) {
            Vendor::firstOrCreate(['ssm_brn' => $data['ssm_brn']], $data);
        }
    }
}
