<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = [
            // 1. Corporate B2B
            [
                'name' => 'Bintang Global Logistics Sdn. Bhd.',
                'identification_type' => 'BRN',
                'ssm_brn' => '201801089211 (1289345-T)',
                'tin_number' => 'C19830214000',
                'sst_number' => 'B16-1809-32000012',
                'email' => 'finance@bintanglogistics.com.my',
                'phone' => '+6012-345-6789',
                'address_line1' => 'No. 28, Jalan Persiaran Industri 4',
                'address_line2' => 'Kawasan Perindustrian Bukit Raja',
                'city' => 'Shah Alam',
                'state' => 'Selangor',
                'postal_code' => '40000',
                'payment_terms_days' => 30,
            ],
            // 2. Corporate B2B
            [
                'name' => 'Perintis Mahir Solutions Sdn. Bhd.',
                'identification_type' => 'BRN',
                'ssm_brn' => '202001045123 (1389456-K)',
                'tin_number' => 'C23419087000',
                'sst_number' => 'W10-1901-21000088',
                'email' => 'accounts@perintismahir.my',
                'phone' => '+6019-876-5432',
                'address_line1' => 'Suite 12-03, Menara Cyberjaya',
                'address_line2' => 'Persiaran Multimedia',
                'city' => 'Cyberjaya',
                'state' => 'Selangor',
                'postal_code' => '63000',
                'payment_terms_days' => 14,
            ],
            // 3. Individual Patient / Customer (B2C with NRIC for Tax Relief)
            [
                'name' => 'Ahmad Daniel bin Razali',
                'identification_type' => 'NRIC',
                'ssm_brn' => '940315-10-5821', // NRIC Number
                'tin_number' => 'IG28471920000',
                'sst_number' => null,
                'email' => 'daniel.razali@gmail.com',
                'phone' => '+6017-889-1234',
                'address_line1' => 'No. 12, Jalan SS2/45, Petaling Jaya',
                'address_line2' => null,
                'city' => 'Petaling Jaya',
                'state' => 'Selangor',
                'postal_code' => '47300',
                'payment_terms_days' => 0, // Due on receipt / Instant
            ],
            // 4. Foreign Patient / Tourist (Passport)
            [
                'name' => 'Johnathan Davies (Expat Patient)',
                'identification_type' => 'PASSPORT',
                'ssm_brn' => 'GB982145672',
                'tin_number' => 'EI00000000030', // LHDN Foreigner Default TIN
                'sst_number' => null,
                'email' => 'jdavies.kl@gmail.com',
                'phone' => '+6011-2345-6789',
                'address_line1' => 'Unit 22-01, Pavilion Residences, Bukit Bintang',
                'address_line2' => null,
                'city' => 'Kuala Lumpur',
                'state' => 'Wilayah Persekutuan',
                'postal_code' => '55100',
                'payment_terms_days' => 0,
            ],
            // 5. Walk-In General Consumer (Consolidated e-Invoice Target)
            [
                'name' => 'General Public / Walk-in Patient',
                'identification_type' => 'BRN',
                'ssm_brn' => 'NA',
                'tin_number' => 'EI00000000020', // Official LHDN General Public Buyer TIN
                'sst_number' => null,
                'email' => 'walkin@clinic-pos.my',
                'phone' => null,
                'address_line1' => 'Walk-In Counter Settlement',
                'address_line2' => null,
                'city' => 'Kuala Lumpur',
                'state' => 'Wilayah Persekutuan',
                'postal_code' => '50000',
                'payment_terms_days' => 0,
            ],
        ];

        foreach ($customers as $data) {
            Customer::firstOrCreate(['name' => $data['name']], $data);
        }
    }
}
