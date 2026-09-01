<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Models\Invoice;
use App\Models\Bill;
use App\Models\Customer;
use App\Models\Vendor;
use App\Models\CompanySetting;

// Public Landing Page (Main Route)
Route::get('/', function () {
    return view('welcome');
})->name('landing');

// Public Invoice Verification
Route::get('/verify', function () {
    return view('public.verify');
})->name('public.verify');

// Admin Guest / Authentication Routes
Route::prefix('admin')->name('admin.')->group(function () {
    
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
        Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');

        Route::get('/forgot-password', function () {
            return view('auth.forgot-password');
        })->name('forgot-password');
    });

    // Admin Protected Dashboard & Operations
    Route::middleware('auth')->group(function () {
        Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

        Route::get('/', function () {
            return redirect()->route('admin.dashboard');
        });

        // 1. Dashboard (Overview)
        Route::get('/dashboard', function () {
            $invoices = Invoice::with('customer')->latest('id')->take(5)->get();
            $totalOutstandingAr = Invoice::whereIn('status', ['issued', 'partially_paid'])->sum('grand_total');
            $overdueCount = Invoice::where('due_date', '<', now())->whereIn('status', ['issued', 'partially_paid'])->count();
            $pendingApTotal = Bill::where('approval_status', 'pending_approval')->sum('grand_total');
            $pendingApCount = Bill::where('approval_status', 'pending_approval')->count();
            $outputTax = Invoice::sum('tax_total');
            $inputTax = Bill::sum('tax_total');
            $netSst = $outputTax - $inputTax;

            return view('admin.dashboard', compact(
                'invoices',
                'totalOutstandingAr',
                'overdueCount',
                'pendingApTotal',
                'pendingApCount',
                'outputTax',
                'inputTax',
                'netSst'
            ));
        })->name('dashboard');

        // 2. Accounts Receivable (AR) Invoices & Customers
        Route::get('/invoices', function () {
            $invoices = Invoice::with('customer')->latest('issue_date')->get();
            $totalInvoiced = Invoice::sum('grand_total');
            $totalPaid = Invoice::where('status', 'paid')->sum('grand_total');
            $totalOutstanding = Invoice::whereIn('status', ['issued', 'partially_paid'])->sum('grand_total');
            $overdueCount = Invoice::where('due_date', '<', now())->whereIn('status', ['issued', 'partially_paid'])->count();
            $lhdnCount = Invoice::where('einvoice_mode', 'production')->count();

            return view('admin.invoices.index', compact(
                'invoices',
                'totalInvoiced',
                'totalPaid',
                'totalOutstanding',
                'overdueCount',
                'lhdnCount'
            ));
        })->name('invoices.index');

        Route::get('/invoices/create', function () {
            $customers = Customer::orderBy('name')->get();
            return view('admin.invoices.create', compact('customers'));
        })->name('invoices.create');

        Route::post('/invoices', function (\Illuminate\Http\Request $request) {
            $validated = $request->validate([
                'customer_id' => 'required|exists:customers,id',
                'invoice_number' => 'required|string|unique:invoices,invoice_number',
                'issue_date' => 'required|date',
                'due_date' => 'required|date|after_or_equal:issue_date',
                'po_number' => 'nullable|string',
                'einvoice_mode' => 'nullable|in:off,sandbox,production',
                'items' => 'required|array|min:1',
                'items.*.description' => 'required|string',
                'items.*.qty' => 'required|numeric|min:1',
                'items.*.unit_price' => 'required|numeric|min:0',
                'items.*.sst_rate' => 'required|numeric|in:0,6,8',
            ]);

            $subtotal = 0;
            $taxTotal = 0;

            foreach ($validated['items'] as $item) {
                $lineSubtotal = $item['qty'] * $item['unit_price'];
                $lineTax = $lineSubtotal * ($item['sst_rate'] / 100);
                $subtotal += $lineSubtotal;
                $taxTotal += $lineTax;
            }

            $grandTotal = $subtotal + $taxTotal;

            $invoice = Invoice::create([
                'customer_id' => $validated['customer_id'],
                'invoice_number' => $validated['invoice_number'],
                'issue_date' => $validated['issue_date'],
                'due_date' => $validated['due_date'],
                'po_number' => $validated['po_number'] ?? null,
                'subtotal' => $subtotal,
                'tax_total' => $taxTotal,
                'grand_total' => $grandTotal,
                'status' => 'issued',
                'einvoice_mode' => $validated['einvoice_mode'] ?? 'off',
                'created_by' => auth()->id() ?? 1,
            ]);

            foreach ($validated['items'] as $item) {
                $lineSubtotal = $item['qty'] * $item['unit_price'];
                $lineTax = $lineSubtotal * ($item['sst_rate'] / 100);
                
                \App\Models\InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'description' => $item['description'],
                    'classification_code' => '001',
                    'quantity' => $item['qty'],
                    'unit_price' => $item['unit_price'],
                    'discount_amount' => 0.00,
                    'sst_rate' => $item['sst_rate'],
                    'sst_amount' => $lineTax,
                    'net_amount' => $lineSubtotal,
                    'total_amount' => $lineSubtotal + $lineTax,
                ]);
            }

            return redirect()->route('admin.invoices.index')->with('success', "Invoice {$invoice->invoice_number} created successfully!");
        })->name('invoices.store');

        Route::get('/invoices/export', function () {
            $invoices = Invoice::with('customer')->orderBy('issue_date', 'desc')->get();
            $filename = "INVOICES_REGISTRY_" . date('Ymd_His') . ".csv";
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"$filename\"",
            ];

            $callback = function () use ($invoices) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['ACCOUNTS RECEIVABLE INVOICES REGISTRY - MALAYSIA']);
                fputcsv($file, ['Exported At', date('Y-m-d H:i:s')]);
                fputcsv($file, ['Total Invoices', $invoices->count()]);
                fputcsv($file, []);
                fputcsv($file, ['Invoice #', 'PO Ref', 'Customer Name', 'Buyer TIN', 'Buyer BRN/NRIC', 'Issue Date', 'Due Date', 'Subtotal (MYR)', 'Tax Total (MYR)', 'Grand Total (MYR)', 'e-Invoice Mode', 'Status']);
                foreach ($invoices as $inv) {
                    fputcsv($file, [
                        $inv->invoice_number,
                        $inv->po_number ?? '-',
                        $inv->customer->name ?? 'Walk-in',
                        $inv->customer->tin_number ?? '-',
                        $inv->customer->ssm_brn ?? '-',
                        $inv->issue_date->format('Y-m-d'),
                        $inv->due_date->format('Y-m-d'),
                        number_format($inv->subtotal, 2, '.', ''),
                        number_format($inv->tax_total, 2, '.', ''),
                        number_format($inv->grand_total, 2, '.', ''),
                        $inv->einvoice_mode,
                        $inv->status,
                    ]);
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        })->name('invoices.export');

        Route::get('/customers', function () {
            $customers = Customer::withCount('invoices')->orderBy('name')->get();
            return view('admin.customers.index', compact('customers'));
        })->name('customers.index');

        Route::get('/customers/export', function () {
            $customers = Customer::withCount('invoices')->orderBy('name')->get();
            $filename = "CUSTOMERS_DIRECTORY_" . date('Ymd_His') . ".csv";
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"$filename\"",
            ];

            $callback = function () use ($customers) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['CUSTOMERS & PATIENTS DIRECTORY - MALAYSIA']);
                fputcsv($file, ['Exported At', date('Y-m-d H:i:s')]);
                fputcsv($file, ['Total Records', $customers->count()]);
                fputcsv($file, []);
                fputcsv($file, ['Name / Entity', 'ID Type', 'SSM BRN / NRIC', 'TIN Number', 'SST Number', 'Email', 'Phone', 'Address', 'City', 'State', 'Payment Terms (Days)']);
                foreach ($customers as $c) {
                    fputcsv($file, [
                        $c->name,
                        $c->identification_type,
                        $c->ssm_brn,
                        $c->tin_number ?? 'EI00000000020',
                        $c->sst_number ?? '-',
                        $c->email ?? '-',
                        $c->phone ?? '-',
                        $c->address_line1 ?? '-',
                        $c->city ?? '-',
                        $c->state ?? '-',
                        $c->payment_terms_days,
                    ]);
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        })->name('customers.export');

        // Quick Customer Store Endpoint (Option 4)
        Route::post('/customers/quick', function (\Illuminate\Http\Request $request) {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'identification_type' => 'required|in:BRN,NRIC,PASSPORT',
                'ssm_brn' => 'required|string|max:50',
                'tin_number' => 'nullable|string|max:50',
                'sst_number' => 'nullable|string|max:50',
                'email' => 'required|email|max:255',
                'phone' => 'nullable|string|max:30',
                'address_line1' => 'nullable|string|max:255',
                'city' => 'nullable|string|max:100',
                'state' => 'nullable|string|max:100',
            ]);

            $customer = Customer::create([
                'name' => $validated['name'],
                'identification_type' => $validated['identification_type'],
                'ssm_brn' => $validated['ssm_brn'],
                'tin_number' => $validated['tin_number'] ?? 'EI00000000020',
                'sst_number' => $validated['sst_number'] ?? null,
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'address_line1' => $validated['address_line1'] ?? 'Kuala Lumpur, Malaysia',
                'city' => $validated['city'] ?? 'Kuala Lumpur',
                'state' => $validated['state'] ?? 'Wilayah Persekutuan',
                'postal_code' => '50000',
                'payment_terms_days' => 30,
            ]);

            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'customer' => $customer]);
            }

            return back()->with('success', "Customer {$customer->name} registered successfully!");
        })->name('customers.quick');

        // 3. Accounts Payable (AP) Bills & Vendors
        Route::get('/bills', function () {
            $bills = Bill::with('vendor')->latest('bill_date')->get();
            return view('admin.bills.index', compact('bills'));
        })->name('bills.index');

        Route::get('/bills/export', function () {
            $bills = Bill::with('vendor')->latest('bill_date')->get();
            $filename = "AP_SUPPLIER_BILLS_" . date('Ymd_His') . ".csv";
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"$filename\"",
            ];

            $callback = function () use ($bills) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['ACCOUNTS PAYABLE SUPPLIER BILLS REGISTRY - MALAYSIA']);
                fputcsv($file, ['Exported At', date('Y-m-d H:i:s')]);
                fputcsv($file, ['Total Records', $bills->count()]);
                fputcsv($file, []);
                fputcsv($file, ['Bill #', 'PO Ref', 'Vendor / Supplier', 'Bill Date', 'Due Date', 'Subtotal (MYR)', 'Input Tax (MYR)', 'Grand Total (MYR)', '2-Way Match', 'Approval Status']);
                foreach ($bills as $b) {
                    fputcsv($file, [
                        $b->bill_number,
                        $b->po_number ?? '-',
                        $b->vendor->name ?? 'Supplier',
                        $b->bill_date->format('Y-m-d'),
                        $b->due_date->format('Y-m-d'),
                        number_format($b->subtotal, 2, '.', ''),
                        number_format($b->tax_total, 2, '.', ''),
                        number_format($b->grand_total, 2, '.', ''),
                        $b->match_status,
                        $b->approval_status,
                    ]);
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        })->name('bills.export');

        Route::get('/bills/upload', function () {
            $vendors = Vendor::orderBy('name')->get();
            return view('admin.bills.upload', compact('vendors'));
        })->name('bills.upload');

        Route::get('/vendors', function () {
            $vendors = Vendor::withCount('bills')->orderBy('name')->get();
            return view('admin.vendors.index', compact('vendors'));
        })->name('vendors.index');

        Route::get('/vendors/export', function () {
            $vendors = Vendor::withCount('bills')->orderBy('name')->get();
            $filename = "VENDORS_DIRECTORY_" . date('Ymd_His') . ".csv";
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"$filename\"",
            ];

            $callback = function () use ($vendors) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['VENDORS & SUPPLIERS DIRECTORY - MALAYSIA']);
                fputcsv($file, ['Exported At', date('Y-m-d H:i:s')]);
                fputcsv($file, ['Total Records', $vendors->count()]);
                fputcsv($file, []);
                fputcsv($file, ['Vendor Name', 'SSM Registration', 'TIN Number', 'SST Number', 'Bank Name', 'Bank Account #', 'Email', 'Phone']);
                foreach ($vendors as $v) {
                    fputcsv($file, [
                        $v->name,
                        $v->ssm_brn,
                        $v->tin_number ?? '-',
                        $v->sst_number ?? '-',
                        $v->bank_name ?? 'Maybank',
                        $v->bank_account_number ?? '-',
                        $v->email ?? '-',
                        $v->phone ?? '-',
                    ]);
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        })->name('vendors.export');

        // 4. Banking & Payouts
        Route::get('/banking/batch-payouts', function () {
            return view('admin.banking.batch-payouts');
        })->name('banking.batch-payouts');

        Route::post('/banking/batch-payouts/export', function (\Illuminate\Http\Request $request) {
            $bank = $request->input('bank', 'maybank');
            $filename = strtoupper($bank) . "_BATCH_PAYOUT_" . date('Ymd_His') . ".csv";
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"$filename\"",
            ];

            $callback = function () use ($bank) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['CORPORATE BATCH PAYMENT AUTOPAY INSTRUCTION', strtoupper($bank)]);
                fputcsv($file, ['Debit Account', '5140-1234-8899', 'Nexa Digital Sdn. Bhd.']);
                fputcsv($file, ['Value Date', date('Y-m-d')]);
                fputcsv($file, ['Total Records', '2', 'Total Amount', '13546.00']);
                fputcsv($file, []);
                fputcsv($file, ['Seq #', 'Beneficiary Name', 'Bank Name', 'Account Number', 'Amount (MYR)', 'Payment Ref', 'Recipient Email']);
                fputcsv($file, ['1', 'Tekno Logistik Cloud Services Sdn. Bhd.', 'Maybank Berhad', '5140-9988-1122', '4536.00', 'SUPP-INV-8834', 'finance@teknologistik.com.my']);
                fputcsv($file, ['2', 'Wira Network Telecom Sdn. Bhd.', 'CIMB Bank Berhad', '800-1234-5678', '9010.00', 'WIRA-TEL-2026-09', 'billing@wiranetwork.my']);
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        })->name('banking.batch-payouts.export');

        // 5. Tax & Compliance Reports & Exports (Option 3)
        Route::get('/reports/sst-02', function () {
            $outputTax = Invoice::sum('tax_total');
            $inputTax = Bill::sum('tax_total');
            $netSst = $outputTax - $inputTax;
            return view('admin.reports.sst-02', compact('outputTax', 'inputTax', 'netSst'));
        })->name('reports.sst02');

        Route::get('/reports/sst-02/export', function () {
            $outputTax = Invoice::sum('tax_total');
            $inputTax = Bill::sum('tax_total');
            $netSst = $outputTax - $inputTax;

            $filename = "SST-02_RETURN_" . date('Y_m') . ".csv";
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"$filename\"",
            ];

            $callback = function () use ($outputTax, $inputTax, $netSst) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['ROYAL MALAYSIAN CUSTOMS DEPARTMENT (JKDM) - SST-02 RETURN EXPORT']);
                fputcsv($file, ['Generated At', date('Y-m-d H:i:s')]);
                fputcsv($file, ['Company', 'Nexa Digital Sdn. Bhd.']);
                fputcsv($file, ['SST Registration No', 'W10-1808-32000045']);
                fputcsv($file, []);
                fputcsv($file, ['Box Item', 'Description', 'Rate', 'Amount (MYR)']);
                fputcsv($file, ['11a', 'Standard Rate Taxable Service Supplies', '8%', number_format(Invoice::sum('subtotal'), 2)]);
                fputcsv($file, ['11b', 'Specific Rate Taxable Service Supplies', '6%', '0.00']);
                fputcsv($file, ['11c', 'Exempted Supplies & Zero Rated', '0%', '0.00']);
                fputcsv($file, ['11d', 'Total Output Service Tax Payable', '-', number_format($outputTax, 2)]);
                fputcsv($file, ['12', 'Less: Input Tax Deduction (Supplier AP Bills)', '-', number_format($inputTax, 2)]);
                fputcsv($file, ['13', 'NET SERVICE TAX PAYABLE TO JKDM', '-', number_format($netSst, 2)]);
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        })->name('reports.sst02.export');

        Route::get('/reports/aging', function () {
            return view('admin.reports.aging');
        })->name('reports.aging');

        Route::get('/reports/aging/export', function () {
            $filename = "AGING_LEDGER_REPORT_" . date('Y_m_d') . ".csv";
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"$filename\"",
            ];

            $callback = function () {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['MALAYSIAN RINGGIT (MYR) - ACCOUNTS RECEIVABLE & PAYABLE AGING LEDGER']);
                fputcsv($file, ['Entity Name', 'Reference #', '0-30 Days', '31-60 Days', '61-90 Days', '90+ Days', 'Total Balance (MYR)']);
                fputcsv($file, ['Bintang Global Logistics Sdn. Bhd.', 'INV-2026-0892', '6324.00', '0.00', '0.00', '0.00', '6324.00']);
                fputcsv($file, ['Borneo Retail Hypermarket Sdn. Bhd.', 'INV-2026-0740', '0.00', '13500.00', '0.00', '0.00', '13500.00']);
                fputcsv($file, ['Tekno Logistik Cloud Services Sdn. Bhd.', 'SUPP-INV-8834', '4536.00', '0.00', '0.00', '0.00', '4536.00']);
                fputcsv($file, ['Wira Network Telecom Sdn. Bhd.', 'WIRA-TEL-2026-09', '9010.00', '0.00', '0.00', '0.00', '9010.00']);
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        })->name('reports.aging.export');

        // 6. Settings & Compliance Config
        Route::get('/settings', function () {
            $settings = CompanySetting::first();
            return view('admin.settings.index', compact('settings'));
        })->name('settings.index');
    });
});
