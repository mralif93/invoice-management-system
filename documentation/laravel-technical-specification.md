# Laravel Technical Architecture & Implementation Specification
## Simplified Invoice Management System (IMS) – Malaysia Edition

**Document Version:** 1.0.0  
**Target Framework:** Laravel 11.x / 12.x (PHP 8.2+)  
**Architecture Pattern:** Modular Action-Domain-Responder / Service-Repository Layer with Filament / Blade / Livewire

---

### Table of Contents
1. [Recommended Tech Stack & Ecosystem](#1-recommended-tech-stack--ecosystem)
2. [Database Schema & Migrations Architecture](#2-database-schema--migrations-architecture)
3. [Eloquent Models & Relationships](#3-eloquent-models--relationships)
4. [Role-Based Access Control (RBAC) & Policies](#4-role-based-access-control-rbac--policies)
5. [Modular e-Invoicing Architecture (Driver Pattern)](#5-modular-e-invoicing-architecture-driver-pattern)
6. [Core Services & Action Classes](#6-core-services--action-classes)
7. [Job Queues & Scheduled Commands](#7-job-queues--scheduled-commands)
8. [Recommended Packages & Configuration](#8-recommended-packages--configuration)

---

### 1. Recommended Tech Stack & Ecosystem

| Layer | Recommended Technology | Rationale |
| :--- | :--- | :--- |
| **Framework** | Laravel 11.x / 12.x (PHP 8.2 / 8.3) | Modern features (slim skeleton, native concurrency, typed properties). |
| **Frontend / UI Engine** | Laravel Livewire 3 + Alpine.js + Tailwind CSS | Full control over custom FinTech aesthetics, instant reactive line-item calculations, split-screen PDF previewers, and zero bloat. |
| **Icons** | Lucide Icons (`blade-lucide-icons` / `lucide`) | Consistent, clean 24x24 SVG line iconography. |
| **PDF Generation** | `barryvdh/laravel-dompdf` or `spatie/laravel-pdf` (Chromium-based) | High fidelity rendering of tax invoices, CSS `@media print` support, and barcode/QR embedding. |
| **QR Code Engine** | `simplesoftwareio/simple-qrcode` | Instant generation of DuitNow EMVCo QR and LHDN MyInvois Validation URLs. |
| **Permissions / RBAC**| `spatie/laravel-permission` | Industry standard for roles (`admin`, `accounts`, `approver`, `auditor`). |
| **Excel / CSV Export**| `maatwebsite/excel` (Laravel Excel) | SST-02 bi-monthly summary export and bank payment batch generation (IBG CSV). |
| **Storage / Archiving**| Laravel Storage (S3 / Local encrypted) | Immutable 7-year PDF archive retention with signed URLs. |

---

### 2. Database Schema & Migrations Architecture

```
                    ┌─────────────────────────┐
                    │          users          │
                    ├─────────────────────────┤
                    │ id                      │
                    │ name, email, password   │
                    │ role (spatie roles)     │
                    └────────────┬────────────┘
                                 │ 1
                                 │
                                 │ ∞
┌───────────────────┐       ┌────┴──────────────┐       ┌───────────────────┐
│     customers     │       │     invoices      │       │      vendors      │
├───────────────────┤       ├───────────────────┤       ├───────────────────┤
│ id                │       │ id                │       │ id                │
│ name              │ 1   ∞ │ invoice_number    │ 1   ∞ │ name              │
│ ssm_brn           ├───────┤ customer_id (FK)  │   ┌───┤ ssm_brn           │
│ tin_number        │       │ status            │   │   │ tin_number        │
│ sst_number        │       │ subtotal          │   │   │ sst_number        │
│ email, phone      │       │ tax_total         │   │   │ bank_name, acc_no │
│ address           │       │ grand_total       │   │   └─────────┬─────────┘
└───────────────────┘       │ einvoice_mode     │   │             │ 1
                            │ lhdn_uuid         │   │             │
                            │ lhdn_status       │   │             │ ∞
                            └────────┬──────────┘   │   ┌─────────┴─────────┐
                                     │ 1            │   │       bills       │
                                     │              │   ├───────────────────┤
                                     │ ∞            │   │ id                │
                            ┌────────┴──────────┐   │   │ vendor_id (FK)    │
                            │ invoice_items     │   └───┤ po_number         │
                            ├───────────────────┤       │ bill_number       │
                            │ id                │       │ subtotal          │
                            │ invoice_id (FK)   │       │ tax_total         │
                            │ description       │       │ grand_total       │
                            │ quantity          │       │ match_status      │
                            │ unit_price        │       │ approval_status   │
                            │ sst_rate (0/6/8)  │       │ file_path         │
                            │ tax_amount        │       └─────────┬─────────┘
                            │ total_amount      │                 │ 1
                            └───────────────────┘                 │ ∞
                                                        ┌─────────┴─────────┐
                                                        │    bill_items     │
                                                        ├───────────────────┤
                                                        │ id                │
                                                        │ bill_id (FK)      │
                                                        │ description       │
                                                        │ quantity, price   │
                                                        │ tax_amount, total │
                                                        └───────────────────┘
```

#### Key Migrations Table Structure:

```php
// 1. invoices table
Schema::create('invoices', function (Blueprint $table) {
    $table->id();
    $table->string('invoice_number')->unique();
    $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
    $table->date('issue_date');
    $table->date('due_date');
    $table->string('currency', 3)->default('MYR');
    
    // Financial Fields (Sen-accurate decimal)
    $table->decimal('subtotal', 15, 2)->default(0.00);
    $table->decimal('discount_total', 15, 2)->default(0.00);
    $table->decimal('tax_total', 15, 2)->default(0.00);
    $table->decimal('grand_total', 15, 2)->default(0.00);
    $table->decimal('paid_amount', 15, 2)->default(0.00);
    
    // Status & Workflow
    $table->enum('status', ['draft', 'issued', 'partially_paid', 'paid', 'cancelled'])->default('draft');
    
    // e-Invoicing Integration
    $table->enum('einvoice_mode', ['off', 'sandbox', 'production'])->default('off');
    $table->string('lhdn_uuid')->nullable()->index();
    $table->string('lhdn_submission_uid')->nullable();
    $table->enum('lhdn_status', ['not_submitted', 'submitted', 'valid', 'invalid', 'cancelled'])->default('not_submitted');
    $table->timestamp('lhdn_validated_at')->nullable();
    $table->text('lhdn_validation_url')->nullable();
    $table->json('lhdn_response_payload')->nullable();

    $table->text('notes')->nullable();
    $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
    $table->timestamps();
    $table->softDeletes();
});

// 2. invoice_items table
Schema::create('invoice_items', function (Blueprint $table) {
    $table->id();
    $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
    $table->string('description');
    $table->string('classification_code')->default('001'); // LHDN MSIC/Classification code
    $table->decimal('quantity', 12, 4)->default(1);
    $table->decimal('unit_price', 15, 2);
    $table->decimal('discount_amount', 15, 2)->default(0.00);
    $table->decimal('sst_rate', 5, 2)->default(8.00); // 0.00, 6.00, 8.00
    $table->decimal('sst_amount', 15, 2)->default(0.00);
    $table->decimal('net_amount', 15, 2);
    $table->decimal('total_amount', 15, 2);
    $table->timestamps();
});

// 3. bills (AP) table
Schema::create('bills', function (Blueprint $table) {
    $table->id();
    $table->string('bill_number');
    $table->foreignId('vendor_id')->constrained()->cascadeOnDelete();
    $table->string('po_number')->nullable()->index();
    $table->date('bill_date');
    $table->date('due_date');
    
    $table->decimal('subtotal', 15, 2);
    $table->decimal('tax_total', 15, 2)->default(0.00);
    $table->decimal('grand_total', 15, 2);
    
    $table->enum('match_status', ['unmatched', 'matched', 'variance_flagged'])->default('unmatched');
    $table->decimal('matching_variance', 15, 2)->default(0.00);
    $table->enum('approval_status', ['draft', 'pending_approval', 'approved', 'rejected'])->default('draft');
    $table->foreignId('approved_by')->nullable()->constrained('users');
    $table->timestamp('approved_at')->nullable();
    $table->text('approval_remarks')->nullable();
    
    $table->string('file_attachment_path')->nullable();
    $table->timestamps();
    $table->softDeletes();
    
    $table->unique(['vendor_id', 'bill_number']); // Prevent duplicate bills
});
```

---

### 3. Eloquent Models & Relationships

* **`App\Models\Invoice`**:
  * Relations: `belongsTo(Customer::class)`, `hasMany(InvoiceItem::class)`, `belongsTo(User::class, 'created_by')`.
  * Scopes: `scopeOverdue()`, `scopePaid()`, `scopeUnpaid()`, `scopeEInvoicePending()`.
  * Accessors / Casts: `is_overdue`, `lhdn_qr_url`, casts for decimal rounding.
* **`App\Models\Bill`**:
  * Relations: `belongsTo(Vendor::class)`, `hasMany(BillItem::class)`, `belongsTo(User::class, 'approved_by')`.
  * Scopes: `scopeNeedsApproval()`, `scopeOverdue()`.
* **`App\Models\CompanySetting`**:
  * Singleton or Key-Value store for SSM BRN, TIN, MSIC, SST ID, Bank accounts, and e-Invoicing Mode (`off`, `sandbox`, `production`).

---

### 4. Role-Based Access Control (RBAC) & Policies

Implemented using `spatie/laravel-permission` & Laravel Policies:

| Role | Defined Permissions | Guard / Policy |
| :--- | :--- | :--- |
| `admin` | `manage-settings`, `toggle-einvoice-mode`, `manage-users`, `*` | Full access via `Gate::before` super-admin rule |
| `accounts` | `create-invoices`, `edit-invoices`, `send-invoices`, `record-payments`, `upload-bills`, `export-reports` | `InvoicePolicy`, `BillPolicy` |
| `approver` | `view-bills`, `approve-bills`, `reject-bills`, `approve-payouts` | `BillPolicy@approve` (enforcing $>$ MYR 5,000 threshold check) |
| `auditor` | `view-invoices`, `view-bills`, `view-audit-logs`, `export-sst-02` | Read-only across all resources |

---

### 5. Modular e-Invoicing Architecture (Driver Pattern)

Using Laravel's Manager pattern for zero-friction switching between Standalone Mode, Sandbox, and Production LHDN:

```
App\Services\EInvoice\
├── EInvoiceManager.php             (Manager class selecting driver based on config)
├── Contracts\
│   └── EInvoiceDriverInterface.php (submit, validate, cancel, getStatus)
├── Drivers\
│   ├── NullDriver.php              (When Mode = 'off', returns instant local success)
│   ├── LhdnSandboxDriver.php       (Connects to Sandbox API with test certificates)
│   └── LhdnProductionDriver.php    (Signs UBL 2.1 JSON with X.509 cert & live transmission)
└── DTOs\
    ├── InvoicePayloadDTO.php
    └── LhdnValidationResultDTO.php
```

#### Driver Interface Example:
```php
namespace App\Services\EInvoice\Contracts;

use App\Models\Invoice;
use App\Services\EInvoice\DTOs\LhdnValidationResultDTO;

interface EInvoiceDriverInterface
{
    public function submitInvoice(Invoice $invoice): LhdnValidationResultDTO;
    public function checkStatus(string $uuid): LhdnValidationResultDTO;
    public function cancelInvoice(Invoice $invoice, string $reason): bool;
}
```

---

### 6. Core Services & Action Classes

* **`App\Actions\Invoice\CalculateInvoiceTotalsAction`**:
  * Accurately calculates line-item net prices, SST (0%, 6%, 8%), discount deductions, and 5-sen cash roundings.
* **`App\Actions\Invoice\GenerateInvoicePdfAction`**:
  * Renders visual PDF using blade template `resources/views/pdf/invoice.blade.php`.
  * Generates DuitNow QR (Standard) or LHDN Clearance QR (e-Invois ON).
* **`App\Actions\Bill\PerformTwoWayMatchAction`**:
  * Compares uploaded Bill totals and items against PO lines within configurable $\pm\text{MYR } 5.00$ tolerance.
* **`App\Services\Payment\DuitNowQrGenerator`**:
  * Encodes EMVCo QR format with company Bank Account, SSM BRN, and Invoice Total Amount.
* **`App\Services\Banking\BankBatchExportService`**:
  * Formats bulk supplier payouts into bank-ready IBG / DuitNow CSV formats (Maybank, CIMB, Public Bank, RHB).
* **`App\Services\Reports\Sst02ExportService`**:
  * Aggregates output tax (Sales by 6%, 8%, Exempt) and input tax into Excel/CSV for JKDM bi-monthly filing.

---

### 7. Job Queues & Scheduled Commands

* **`App\Jobs\TransmitToLhdnJob`**:
  * Dispatched when an invoice is finalized while `einvoice_mode !== 'off'`. Handles background signing, retry backoff, and webhook listeners.
* **`App\Console\Commands\CheckOverdueInvoicesCommand`**:
  * Daily cron (`0 8 * * *`): Flags overdue AR invoices and sends automated WhatsApp / Email payment reminders.
* **`App\Console\Commands\SyncLhdnPendingInvoicesCommand`**:
  * Hourly cron: Queries LHDN for batches pending validation verification.

---

### 8. Recommended `composer.json` Dependencies

```json
{
    "require": {
        "php": "^8.2",
        "filament/filament": "^3.2",
        "spatie/laravel-permission": "^6.4",
        "barryvdh/laravel-dompdf": "^3.0",
        "simplesoftwareio/simple-qrcode": "^4.2",
        "maatwebsite/excel": "^3.1"
    }
}
```
