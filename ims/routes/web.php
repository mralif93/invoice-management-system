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

        Route::get('/customers', function () {
            $customers = Customer::withCount('invoices')->orderBy('name')->get();
            return view('admin.customers.index', compact('customers'));
        })->name('customers.index');

        // 3. Accounts Payable (AP) Bills & Vendors
        Route::get('/bills', function () {
            $bills = Bill::with('vendor')->latest('bill_date')->get();
            return view('admin.bills.index', compact('bills'));
        })->name('bills.index');

        Route::get('/bills/upload', function () {
            $vendors = Vendor::orderBy('name')->get();
            return view('admin.bills.upload', compact('vendors'));
        })->name('bills.upload');

        Route::get('/vendors', function () {
            $vendors = Vendor::withCount('bills')->orderBy('name')->get();
            return view('admin.vendors.index', compact('vendors'));
        })->name('vendors.index');

        // 4. Banking & Payouts
        Route::get('/banking/batch-payouts', function () {
            return view('admin.banking.batch-payouts');
        })->name('banking.batch-payouts');

        // 5. Tax & Compliance Reports
        Route::get('/reports/sst-02', function () {
            $outputTax = Invoice::sum('tax_total');
            $inputTax = Bill::sum('tax_total');
            $netSst = $outputTax - $inputTax;
            return view('admin.reports.sst-02', compact('outputTax', 'inputTax', 'netSst'));
        })->name('reports.sst02');

        Route::get('/reports/aging', function () {
            return view('admin.reports.aging');
        })->name('reports.aging');

        // 6. Settings & Compliance Config
        Route::get('/settings', function () {
            $settings = CompanySetting::first();
            return view('admin.settings.index', compact('settings'));
        })->name('settings.index');
    });
});
