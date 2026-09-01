<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Database\Seeder;

class InvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@ims-malaysia.com')->first();
        $accounts = User::where('email', 'accounts@ims-malaysia.com')->first();

        $b2bCust = Customer::where('ssm_brn', '201801089211 (1289345-T)')->first();
        $patientCust = Customer::where('name', 'Ahmad Daniel bin Razali')->first();
        $walkinCust = Customer::where('tin_number', 'EI00000000020')->first();

        // 1. B2B Corporate Invoice (LHDN Production Validated)
        if ($b2bCust) {
            $inv1 = Invoice::firstOrCreate(
                ['invoice_number' => 'INV-2026-0892'],
                [
                    'customer_id' => $b2bCust->id,
                    'issue_date' => '2026-08-30',
                    'due_date' => '2026-09-29',
                    'currency' => 'MYR',
                    'po_number' => 'PO-2026-981',
                    'subtotal' => 5900.00,
                    'discount_total' => 0.00,
                    'tax_total' => 424.00,
                    'grand_total' => 6324.00,
                    'paid_amount' => 0.00,
                    'status' => 'issued',
                    'einvoice_mode' => 'production',
                    'lhdn_uuid' => 'EINV-20260830-9842-MY',
                    'lhdn_status' => 'valid',
                    'lhdn_validated_at' => now()->subHours(2),
                    'lhdn_validation_url' => 'https://myinvois.hasil.gov.my/verify/EINV-20260830-9842-MY?tin=C25890123000',
                    'created_by' => $admin?->id,
                ]
            );

            if ($inv1->items()->count() === 0) {
                $inv1->items()->createMany([
                    [
                        'description' => 'Cloud SaaS Platform Enterprise Subscription (Aug 2026)',
                        'quantity' => 1,
                        'unit_price' => 3500.00,
                        'sst_rate' => 8.00,
                        'sst_amount' => 280.00,
                        'net_amount' => 3500.00,
                        'total_amount' => 3780.00,
                    ],
                    [
                        'description' => 'ERP Accounting API Connector Setup & Integration',
                        'quantity' => 1,
                        'unit_price' => 1800.00,
                        'sst_rate' => 8.00,
                        'sst_amount' => 144.00,
                        'net_amount' => 1800.00,
                        'total_amount' => 1944.00,
                    ],
                    [
                        'description' => 'Staff Onboarding & Training Workshop (Full-Day)',
                        'quantity' => 1,
                        'unit_price' => 600.00,
                        'sst_rate' => 0.00,
                        'sst_amount' => 0.00,
                        'net_amount' => 600.00,
                        'total_amount' => 600.00,
                    ],
                ]);
            }
        }

        // 2. Individual Patient Clinic Invoice (B2C with NRIC for Tax Relief)
        if ($patientCust) {
            $inv2 = Invoice::firstOrCreate(
                ['invoice_number' => 'INV-2026-0901'],
                [
                    'customer_id' => $patientCust->id,
                    'issue_date' => '2026-08-30',
                    'due_date' => '2026-08-30',
                    'currency' => 'MYR',
                    'po_number' => null,
                    'subtotal' => 450.00,
                    'discount_total' => 0.00,
                    'tax_total' => 0.00, // Healthcare / dental consultations are exempt
                    'grand_total' => 450.00,
                    'paid_amount' => 450.00,
                    'status' => 'paid',
                    'einvoice_mode' => 'off', // Standard visual tax receipt with DuitNow QR
                    'created_by' => $accounts?->id,
                ]
            );

            if ($inv2->items()->count() === 0) {
                $inv2->items()->createMany([
                    [
                        'description' => 'Comprehensive Dental Consultation & Scaling (NRIC Tax Relief eligible)',
                        'quantity' => 1,
                        'unit_price' => 250.00,
                        'sst_rate' => 0.00, // Exempt
                        'sst_amount' => 0.00,
                        'net_amount' => 250.00,
                        'total_amount' => 250.00,
                    ],
                    [
                        'description' => 'Composite Tooth Restoration (Filling)',
                        'quantity' => 1,
                        'unit_price' => 200.00,
                        'sst_rate' => 0.00,
                        'sst_amount' => 0.00,
                        'net_amount' => 200.00,
                        'total_amount' => 200.00,
                    ],
                ]);
            }
        }

        // 3. Walk-In General Patient (Consolidated POS Transaction)
        if ($walkinCust) {
            $inv3 = Invoice::firstOrCreate(
                ['invoice_number' => 'POS-2026-0188'],
                [
                    'customer_id' => $walkinCust->id,
                    'issue_date' => '2026-08-30',
                    'due_date' => '2026-08-30',
                    'currency' => 'MYR',
                    'po_number' => null,
                    'subtotal' => 85.00,
                    'discount_total' => 0.00,
                    'tax_total' => 0.00,
                    'grand_total' => 85.00,
                    'paid_amount' => 85.00,
                    'status' => 'paid',
                    'einvoice_mode' => 'off',
                    'created_by' => $accounts?->id,
                ]
            );

            if ($inv3->items()->count() === 0) {
                $inv3->items()->create([
                    'description' => 'Over-the-counter Oral Hygiene Care Kit & Fluoride Gel',
                    'quantity' => 1,
                    'unit_price' => 85.00,
                    'sst_rate' => 0.00,
                    'sst_amount' => 0.00,
                    'net_amount' => 85.00,
                    'total_amount' => 85.00,
                ]);
            }
        }
    }
}
