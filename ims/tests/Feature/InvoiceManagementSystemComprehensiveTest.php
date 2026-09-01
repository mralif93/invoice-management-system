<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Customer;
use App\Models\Vendor;
use App\Models\Invoice;
use App\Models\Bill;
use App\Models\CompanySetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceManagementSystemComprehensiveTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed the modular seeders for authentic realistic tests
        $this->seed();

        $this->adminUser = User::where('email', 'admin@ims-malaysia.com')->first() ?? User::factory()->create([
            'email' => 'admin@ims-malaysia.com',
            'name' => 'System Administrator',
            'password' => bcrypt('password123')
        ]);
    }

    /**
     * 1. Public Routes Test (Landing & Verification)
     */
    public function test_public_landing_and_verification_pages_load_successfully(): void
    {
        $landingResponse = $this->get('/');
        $landingResponse->assertStatus(200);
        $landingResponse->assertSee('IMS Malaysia');

        $verifyResponse = $this->get('/verify');
        $verifyResponse->assertStatus(200);
        $verifyResponse->assertSee('Verify');
    }

    /**
     * 2. Authentication & Guest Redirection Test
     */
    public function test_guest_is_redirected_to_admin_login(): void
    {
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect('/admin/login');
    }

    public function test_login_page_renders_successfully(): void
    {
        $response = $this->get('/admin/login');
        $response->assertStatus(200);
        $response->assertSee('Admin Portal Sign In');
    }

    public function test_admin_can_authenticate_and_redirect_to_dashboard(): void
    {
        $response = $this->post('/admin/login', [
            'email' => 'admin@ims-malaysia.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticatedAs($this->adminUser);
    }

    /**
     * 3. Admin Overview Dashboard Test
     */
    public function test_authenticated_admin_can_view_dashboard_with_kpis(): void
    {
        $response = $this->actingAs($this->adminUser)->get('/admin/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Outstanding AR');
        $response->assertSee('Pending AP Bills');
        $response->assertSee('SST-02 Net Tax');
    }

    /**
     * 4. Accounts Receivable (AR) Invoices & Customers Test
     */
    public function test_authenticated_admin_can_view_invoices_registry(): void
    {
        $response = $this->actingAs($this->adminUser)->get('/admin/invoices');
        $response->assertStatus(200);
        $response->assertSee('Customer Invoices');
        $response->assertSee('All Customer Invoices');
    }

    public function test_authenticated_admin_can_view_invoice_create_builder(): void
    {
        $response = $this->actingAs($this->adminUser)->get('/admin/invoices/create');
        $response->assertStatus(200);
        $response->assertSee('Create Tax Invoice (AR)');
        $response->assertSee('Preview PDF Invoice');
    }

    public function test_authenticated_admin_can_view_customers_directory(): void
    {
        $response = $this->actingAs($this->adminUser)->get('/admin/customers');
        $response->assertStatus(200);
        $response->assertSee('Customers & Patients');
        $response->assertSee('Customer & Patient Directory');
    }

    /**
     * 5. Accounts Payable (AP) Bills & 2-Way Match Test
     */
    public function test_authenticated_admin_can_view_bills_registry(): void
    {
        $response = $this->actingAs($this->adminUser)->get('/admin/bills');
        $response->assertStatus(200);
        $response->assertSee('Supplier Bills');
        $response->assertSee('2-Way Matched');
    }

    public function test_authenticated_admin_can_view_bill_upload_and_2way_matching(): void
    {
        $response = $this->actingAs($this->adminUser)->get('/admin/bills/upload');
        $response->assertStatus(200);
        $response->assertSee('Supplier Bill Ingestion');
        $response->assertSee('2-Way Comparison');
    }

    public function test_authenticated_admin_can_view_vendors_master(): void
    {
        $response = $this->actingAs($this->adminUser)->get('/admin/vendors');
        $response->assertStatus(200);
        $response->assertSee('Vendors & Suppliers');
        $response->assertSee('Vendor Master Directory');
    }

    /**
     * 6. Banking & Batch Payouts Test
     */
    public function test_authenticated_admin_can_view_batch_payouts(): void
    {
        $response = $this->actingAs($this->adminUser)->get('/admin/banking/batch-payouts');
        $response->assertStatus(200);
        $response->assertSee('Bank Batch Payouts');
        $response->assertSee('Multi-Bank Batch Payout Generator');
    }

    /**
     * 7. Statutory Tax Reports (SST-02 & Aging) Test
     */
    public function test_authenticated_admin_can_view_sst_02_return_report(): void
    {
        $response = $this->actingAs($this->adminUser)->get('/admin/reports/sst-02');
        $response->assertStatus(200);
        $response->assertSee('SST-02');
    }

    public function test_authenticated_admin_can_view_aging_report(): void
    {
        $response = $this->actingAs($this->adminUser)->get('/admin/reports/aging');
        $response->assertStatus(200);
        $response->assertSee('AR & AP Aging Analysis');
    }

    /**
     * 8. Settings & Compliance Configuration Test
     */
    public function test_authenticated_admin_can_view_settings_page(): void
    {
        $response = $this->actingAs($this->adminUser)->get('/admin/settings');
        $response->assertStatus(200);
        $response->assertSee('e-Invoicing Engine Mode');
        $response->assertSee('Business Issuer Profile');
    }

    /**
     * 10. Invoice Store Action & Validation Test (Feature & UI Functionality)
     */
    public function test_admin_can_successfully_store_new_invoice_with_line_items(): void
    {
        $customer = Customer::first();
        $this->assertNotNull($customer);

        $payload = [
            'customer_id' => $customer->id,
            'invoice_number' => 'INV-TEST-' . rand(1000, 9999),
            'issue_date' => '2026-09-01',
            'due_date' => '2026-10-01',
            'po_number' => 'PO-TEST-99',
            'einvoice_mode' => 'production',
            'items' => [
                [
                    'description' => 'Enterprise System Integration',
                    'qty' => 2,
                    'unit_price' => 1000.00,
                    'sst_rate' => 8,
                ],
                [
                    'description' => 'Telecommunication Setup',
                    'qty' => 1,
                    'unit_price' => 500.00,
                    'sst_rate' => 6,
                ],
            ],
        ];

        $response = $this->actingAs($this->adminUser)->post('/admin/invoices', $payload);

        $response->assertRedirect('/admin/invoices');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('invoices', [
            'invoice_number' => $payload['invoice_number'],
            'customer_id' => $customer->id,
            'subtotal' => 2500.00,
            'tax_total' => 190.00, // (2000 * 0.08 = 160) + (500 * 0.06 = 30) = 190
            'grand_total' => 2690.00,
            'einvoice_mode' => 'production',
        ]);

        $createdInvoice = Invoice::where('invoice_number', $payload['invoice_number'])->first();
        $this->assertCount(2, $createdInvoice->items);
    }

    public function test_invoice_store_validation_fails_on_empty_items_or_invalid_customer(): void
    {
        $response = $this->actingAs($this->adminUser)->post('/admin/invoices', [
            'customer_id' => 99999, // Non-existent
            'invoice_number' => 'INV-FAIL-1',
            'issue_date' => '2026-09-01',
            'due_date' => '2026-08-01', // Due date before issue date
            'items' => [],
        ]);

        $response->assertSessionHasErrors(['customer_id', 'due_date', 'items']);
    }

    /**
     * 11. UI Search & Multi-Filter Structure Test
     */
    public function test_invoices_and_bills_views_contain_interactive_filters(): void
    {
        $invoicesView = $this->actingAs($this->adminUser)->get('/admin/invoices');
        $invoicesView->assertStatus(200);
        $invoicesView->assertSee('x-model="search"', false);
        $invoicesView->assertSee('x-model="statusFilter"', false);
        $invoicesView->assertSee('x-model="modeFilter"', false);

        $billsView = $this->actingAs($this->adminUser)->get('/admin/bills');
        $billsView->assertStatus(200);
        $billsView->assertSee('x-model="search"', false);
        $billsView->assertSee('x-model="matchFilter"', false);
        $billsView->assertSee('x-model="approvalFilter"', false);
    }

    /**
     * 12. Quick Customer Registration Test (Option 4)
     */
    public function test_quick_customer_registration_creates_and_returns_customer(): void
    {
        $payload = [
            'name' => 'Apex Healthcare Clinics Sdn. Bhd.',
            'identification_type' => 'BRN',
            'ssm_brn' => '202401099888',
            'tin_number' => 'C29988776655',
            'sst_number' => 'W10-2401-998877',
            'email' => 'finance@apexhealth.com.my',
            'phone' => '+6012-345-6789',
            'address_line1' => 'Solaris Dutamas, Publika',
            'city' => 'Kuala Lumpur',
            'state' => 'Wilayah Persekutuan',
        ];

        $response = $this->actingAs($this->adminUser)->postJson('/admin/customers/quick', $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'customer' => [
                'name' => 'Apex Healthcare Clinics Sdn. Bhd.',
                'ssm_brn' => '202401099888',
            ]
        ]);

        $this->assertDatabaseHas('customers', [
            'name' => 'Apex Healthcare Clinics Sdn. Bhd.',
            'ssm_brn' => '202401099888',
        ]);
    }

    /**
     * 13. Reports CSV Export Streams Test (Option 3 & 5)
     */
    public function test_sst02_and_aging_reports_can_export_csv_streams(): void
    {
        $sst02Export = $this->actingAs($this->adminUser)->get('/admin/reports/sst-02/export');
        $sst02Export->assertStatus(200);
        $sst02Export->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        $agingExport = $this->actingAs($this->adminUser)->get('/admin/reports/aging/export');
        $agingExport->assertStatus(200);
        $agingExport->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }

    /**
     * 14. Comprehensive Invoices, Bills, Customers, Vendors & Bank Batch Export Tests
     */
    public function test_all_master_registries_and_batch_payouts_can_export_csv_streams(): void
    {
        // 1. Invoices Registry Export
        $invoicesExport = $this->actingAs($this->adminUser)->get('/admin/invoices/export');
        $invoicesExport->assertStatus(200);
        $invoicesExport->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        // 2. Customers Directory Export
        $customersExport = $this->actingAs($this->adminUser)->get('/admin/customers/export');
        $customersExport->assertStatus(200);
        $customersExport->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        // 3. AP Bills Registry Export
        $billsExport = $this->actingAs($this->adminUser)->get('/admin/bills/export');
        $billsExport->assertStatus(200);
        $billsExport->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        // 4. Vendors Directory Export
        $vendorsExport = $this->actingAs($this->adminUser)->get('/admin/vendors/export');
        $vendorsExport->assertStatus(200);
        $vendorsExport->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        // 5. Corporate Bank Batch Payout Export
        $payoutExport = $this->actingAs($this->adminUser)->post('/admin/banking/batch-payouts/export', [
            'bank' => 'maybank'
        ]);
        $payoutExport->assertStatus(200);
        $payoutExport->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }

    /**
     * 15. Export & Ingestion Confirmation Modals Rendering Test
     */
    public function test_views_render_export_and_import_confirmation_modals_properly(): void
    {
        // SST-02 Return View with Confirmation Modal
        $sst02View = $this->actingAs($this->adminUser)->get('/admin/reports/sst-02');
        $sst02View->assertStatus(200);
        $sst02View->assertSee('Confirm SST-02 Report Export');

        // Aging View with Confirmation Modal
        $agingView = $this->actingAs($this->adminUser)->get('/admin/reports/aging');
        $agingView->assertStatus(200);
        $agingView->assertSee('Confirm Aging Ledger Export');

        // Invoices Index with Confirmation Modal
        $invoicesView = $this->actingAs($this->adminUser)->get('/admin/invoices');
        $invoicesView->assertStatus(200);
        $invoicesView->assertSee('Confirm Invoices Registry Export');

        // Bills Upload & Ingestion with Confirmation Modals
        $uploadView = $this->actingAs($this->adminUser)->get('/admin/bills/upload');
        $uploadView->assertStatus(200);
        $uploadView->assertSee('Confirm Supplier Bill Upload');
        $uploadView->assertSee('Confirm Bill Payout Approval');

        // Banking Batch Payout View with Confirmation Modal
        $bankingView = $this->actingAs($this->adminUser)->get('/admin/banking/batch-payouts');
        $bankingView->assertStatus(200);
        $bankingView->assertSee('Confirm Corporate Bank Batch Payout Export');
    }
}
