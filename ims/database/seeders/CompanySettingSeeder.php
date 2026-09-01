<?php

namespace Database\Seeders;

use App\Models\CompanySetting;
use Illuminate\Database\Seeder;

class CompanySettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CompanySetting::firstOrCreate(
            ['ssm_brn' => '202101034567 (1434867-M)'],
            [
                'company_name' => 'Nexa Digital Sdn. Bhd.',
                'tin_number' => 'C25890123000',
                'sst_number' => 'W10-1808-32000045',
                'msic_code' => '62010',
                'email' => 'billing@nexadigital.com.my',
                'phone' => '+603-2289-4500',
                'address' => 'Level 18, The Horizon, Bangsar South, 59200 Kuala Lumpur, Malaysia',
                'bank_name' => 'Malayan Banking Berhad (Maybank)',
                'bank_account_number' => '5140-1234-8899',
                'bank_account_holder' => 'Nexa Digital Sdn. Bhd.',
                'duitnow_id' => '202101034567',
                'einvoice_mode' => 'off', // Standard standalone by default
            ]
        );
    }
}
