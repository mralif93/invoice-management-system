<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SmokeAllPagesTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        
        $this->admin = User::where('email', 'admin@ims-malaysia.com')->first();
    }

    /**
     * 1. Public Landing Page Smoke Test
     */
    public function test_smoke_public_landing_page(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    /**
     * 2. Public Verify Page Smoke Test
     */
    public function test_smoke_public_verify_page(): void
    {
        $response = $this->get('/verify');
        $response->assertStatus(200);
    }

    /**
     * 3. Admin Login Page Smoke Test
     */
    public function test_smoke_admin_login_page(): void
    {
        $response = $this->get('/admin/login');
        $response->assertStatus(200);
    }

    /**
     * 4. Admin Forgot Password Page Smoke Test
     */
    public function test_smoke_admin_forgot_password_page(): void
    {
        $response = $this->get('/admin/forgot-password');
        $response->assertStatus(200);
    }

    /**
     * 5. Admin Root Redirect Smoke Test
     */
    public function test_smoke_admin_root_redirect(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin');
        $response->assertRedirect('/admin/dashboard');
    }

    /**
     * 6. Admin Dashboard Smoke Test
     */
    public function test_smoke_admin_dashboard_page(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/dashboard');
        $response->assertStatus(200);
    }

    /**
     * 7. AR - Create Invoice Page Smoke Test
     */
    public function test_smoke_ar_create_invoice_page(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/invoices/create');
        $response->assertStatus(200);
    }

    /**
     * 8. AR - Invoices List Page Smoke Test
     */
    public function test_smoke_ar_invoices_list_page(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/invoices');
        $response->assertStatus(200);
    }

    /**
     * 9. AR - Customers & Patients Page Smoke Test
     */
    public function test_smoke_ar_customers_page(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/customers');
        $response->assertStatus(200);
    }

    /**
     * 10. AP - Upload Supplier Bill Page Smoke Test
     */
    public function test_smoke_ap_upload_bill_page(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/bills/upload');
        $response->assertStatus(200);
    }

    /**
     * 11. AP - Supplier Bills List Page Smoke Test
     */
    public function test_smoke_ap_bills_list_page(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/bills');
        $response->assertStatus(200);
    }

    /**
     * 12. AP - Vendors Directory Page Smoke Test
     */
    public function test_smoke_ap_vendors_page(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/vendors');
        $response->assertStatus(200);
    }

    /**
     * 13. Banking - Batch Payouts Page Smoke Test
     */
    public function test_smoke_banking_batch_payouts_page(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/banking/batch-payouts');
        $response->assertStatus(200);
    }

    /**
     * 14. Reports - SST-02 Tax Return Page Smoke Test
     */
    public function test_smoke_reports_sst02_page(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/reports/sst-02');
        $response->assertStatus(200);
    }

    /**
     * 15. Reports - AR & AP Aging Page Smoke Test
     */
    public function test_smoke_reports_aging_page(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/reports/aging');
        $response->assertStatus(200);
    }

    /**
     * 16. Configuration - Settings Page Smoke Test
     */
    public function test_smoke_settings_page(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/settings');
        $response->assertStatus(200);
    }
}
