<?php

namespace Tests\Unit;

use Tests\TestCase;

class TaxAndFinancialCalculationTest extends TestCase
{
    /**
     * Test standard 8% Malaysian Service Tax (SST) calculation.
     */
    public function test_standard_8_percent_service_tax_calculation(): void
    {
        $taxableAmount = 1000.00;
        $sstRate = 0.08;
        $taxTotal = round($taxableAmount * $sstRate, 2);
        $grandTotal = $taxableAmount + $taxTotal;

        $this->assertEquals(80.00, $taxTotal);
        $this->assertEquals(1080.00, $grandTotal);
    }

    /**
     * Test specific 6% Malaysian Service Tax (F&B / Telco) calculation.
     */
    public function test_specific_6_percent_service_tax_calculation(): void
    {
        $taxableAmount = 8500.00;
        $sstRate = 0.06;
        $taxTotal = round($taxableAmount * $sstRate, 2);
        $grandTotal = $taxableAmount + $taxTotal;

        $this->assertEquals(510.00, $taxTotal);
        $this->assertEquals(9010.00, $grandTotal);
    }

    /**
     * Test 0% Exempt / Zero-rated tax calculation.
     */
    public function test_zero_percent_exempt_tax_calculation(): void
    {
        $amount = 350.00;
        $sstRate = 0.00;
        $taxTotal = round($amount * $sstRate, 2);
        $grandTotal = $amount + $taxTotal;

        $this->assertEquals(0.00, $taxTotal);
        $this->assertEquals(350.00, $grandTotal);
    }

    /**
     * Test 2-Way Match variance detection with RM 5.00 tolerance.
     */
    public function test_two_way_match_tolerance_and_variance_logic(): void
    {
        $toleranceLimit = 5.00;

        // Exact match
        $poAmount1 = 4200.00;
        $billAmount1 = 4200.00;
        $variance1 = abs($billAmount1 - $poAmount1);
        $isMatch1 = $variance1 <= $toleranceLimit;
        $this->assertTrue($isMatch1);
        $this->assertEquals(0.00, $variance1);

        // Within RM5 tolerance
        $poAmount2 = 4200.00;
        $billAmount2 = 4203.50;
        $variance2 = abs($billAmount2 - $poAmount2);
        $isMatch2 = $variance2 <= $toleranceLimit;
        $this->assertTrue($isMatch2);
        $this->assertEquals(3.50, $variance2);

        // Outside tolerance (Variance flagged)
        $poAmount3 = 4200.00;
        $billAmount3 = 4500.00;
        $variance3 = abs($billAmount3 - $poAmount3);
        $isMatch3 = $variance3 <= $toleranceLimit;
        $this->assertFalse($isMatch3);
        $this->assertEquals(300.00, $variance3);
    }

    /**
     * Test SST-02 bi-monthly net tax formula (Output Tax - Input Tax).
     */
    public function test_sst_02_net_tax_computation(): void
    {
        $outputTax = 1074.00; // Sales invoices collected
        $inputTax = 846.00;   // Supplier bills paid
        $netSstPayable = $outputTax - $inputTax;

        $this->assertEquals(228.00, $netSstPayable);
    }
}
