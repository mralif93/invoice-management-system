<?php

namespace Tests\Unit;

use App\Models\Customer;
use App\Models\Vendor;
use App\Models\Invoice;
use App\Models\Bill;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DomainModelRelationshipTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /**
     * Test Customer relationship with Invoices.
     */
    public function test_customer_has_many_invoices_relationship(): void
    {
        $customer = Customer::has('invoices')->first();
        $this->assertNotNull($customer);
        $this->assertInstanceOf(Invoice::class, $customer->invoices->first());
    }

    /**
     * Test Invoice belongs to Customer and Creator.
     */
    public function test_invoice_belongs_to_customer_and_creator(): void
    {
        $invoice = Invoice::first();
        $this->assertNotNull($invoice);
        $this->assertInstanceOf(Customer::class, $invoice->customer);
        $this->assertInstanceOf(User::class, $invoice->creator);
    }

    /**
     * Test Vendor relationship with Bills.
     */
    public function test_vendor_has_many_bills_relationship(): void
    {
        $vendor = Vendor::has('bills')->first();
        $this->assertNotNull($vendor);
        $this->assertInstanceOf(Bill::class, $vendor->bills->first());
    }

    /**
     * Test Bill belongs to Vendor.
     */
    public function test_bill_belongs_to_vendor(): void
    {
        $bill = Bill::first();
        $this->assertNotNull($bill);
        $this->assertInstanceOf(Vendor::class, $bill->vendor);
    }
}
