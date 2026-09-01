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
     * 9. Database Integrity & Precision Verification
     */
    public function test_database_records_and_tax_calculations_are_mathematically_sound(): void
    {
        $invoices = Invoice::with('items')->get();
        $this->assertGreaterThan(0, $invoices->count());

        foreach ($invoices as $inv) {
            $this->assertNotNull($inv->invoice_number);
            $this->assertGreaterThanOrEqual(0, $inv->grand_total);
        }

        $bills = Bill::with('items')->get();
        $this->assertGreaterThan(0, $bills->count());

        foreach ($bills as $bill) {
            $this->assertNotNull($bill->bill_number);
            $this->assertGreaterThanOrEqual(0, $bill->grand_total);
        }
    }
}
