<?php

namespace Database\Seeders;

use App\Models\Bill;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Seeder;

class BillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@ims-malaysia.com')->first();
        $vendor1 = Vendor::where('ssm_brn', '201501023456 (1154321-X)')->first();
        $vendor2 = Vendor::where('ssm_brn', '201701056789 (1243567-V)')->first();

        // 1. Matched & Approved AP Bill
        if ($vendor1) {
            $bill1 = Bill::firstOrCreate(
                ['bill_number' => 'SUPP-INV-8834'],
                [
                    'vendor_id' => $vendor1->id,
                    'po_number' => 'PO-2026-0412',
                    'bill_date' => '2026-08-28',
                    'due_date' => '2026-09-27',
                    'subtotal' => 4200.00,
                    'tax_total' => 336.00,
                    'grand_total' => 4536.00,
                    'match_status' => 'matched',
                    'matching_variance' => 0.00,
                    'approval_status' => 'approved',
                    'approved_by' => $admin?->id,
                    'approved_at' => now(),
                ]
            );

            if ($bill1->items()->count() === 0) {
                $bill1->items()->create([
                    'description' => 'Dedicated Cloud Server Hosting & Storage (Aug 2026)',
                    'quantity' => 1,
                    'unit_price' => 4200.00,
                    'sst_rate' => 8.00,
                    'sst_amount' => 336.00,
                    'total_amount' => 4536.00,
                ]);
            }
        }

        // 2. Pending Approval AP Bill (> MYR 5,000 limit)
        if ($vendor2) {
            $bill2 = Bill::firstOrCreate(
                ['bill_number' => 'WIRA-TEL-2026-09'],
                [
                    'vendor_id' => $vendor2->id,
                    'po_number' => 'PO-2026-0450',
                    'bill_date' => '2026-08-29',
                    'due_date' => '2026-09-28',
                    'subtotal' => 8500.00,
                    'tax_total' => 510.00,
                    'grand_total' => 9010.00,
                    'match_status' => 'matched',
                    'matching_variance' => 0.00,
                    'approval_status' => 'pending_approval',
                ]
            );

            if ($bill2->items()->count() === 0) {
                $bill2->items()->create([
                    'description' => 'Dedicated Fiber Leased Line 1Gbps & SIP Trunk Service',
                    'quantity' => 1,
                    'unit_price' => 8500.00,
                    'sst_rate' => 6.00,
                    'sst_amount' => 510.00,
                    'total_amount' => 9010.00,
                ]);
            }
        }
    }
}
