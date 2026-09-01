# Invoice Management System (IMS) – Malaysia Edition 🇲🇾

[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-v4.0-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
[![Alpine.js](https://img.shields.io/badge/Alpine.js-3.x-8BC0D0?style=for-the-badge&logo=alpine.js&logoColor=white)](https://alpinejs.dev)
[![PHP](https://img.shields.io/badge/PHP-8.4-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![Tests](https://img.shields.io/badge/Tests-49%20Passed%20(139%20Assertions)-emerald?style=for-the-badge)](https://phpunit.de)

A lightweight, modern, and high-performance **Invoice & e-Invoicing Management System** tailored specifically for Malaysian businesses, SMEs, corporate accounting teams, and medical clinics.

Designed for full Malaysian statutory compliance with the **Sales Tax & Service Tax Act 2018 (SST 8% & 6%)**, **7-Year Statutory Document Retention** (Companies Act 2016 / Income Tax Act 1967), **PDPA 2010 compliance**, and seamless **LHDN MyInvois e-Invoicing (UBL 2.1 JSON)** integration.

---

## 🌟 Key Features & Functional Modules

### 1. ⚡ Fast Accounts Receivable (AR) Invoicing
* **Create Invoices in < 60s:** Clean slate invoice entry with automatic sequence generation (`INV-2026-xxxx`).
* **B2B & B2C / Walk-in / Clinic Support:**
  * Corporate buyers with SSM Business Registration Number (BRN) and SST exemption codes.
  * Individual clinic patients with NRIC or Expat Passport numbers for personal tax relief claims.
  * Consolidated General Public walk-in e-Invoicing (`TIN: EI00000000020`).
* **In-Context Quick Customer Registration Modal:** Register a new B2B Company or B2C Patient directly from within the invoice builder without leaving or refreshing the page.
* **Interactive Live PDF Preview:** In-browser A4 Tax Invoice document rendering with 1-click direct print and PDF export.
* **Vector SVG QR Code Engine:** High-precision scalable vector QR codes dynamically switching between **DuitNow Dynamic QR** and **LHDN Clearance QR**.
* **Instant Sharing:** 1-click WhatsApp billing link dispatcher with online settlement links.

### 2. 🧾 Accounts Payable (AP) & OCR 2-Way Match
* **Supplier Bill Ingestion & Confirmation:** Drag-and-drop PDF/image upload with simulated Optical Character Recognition (OCR) backed by confirmation dialogs before document processing.
* **Automated 2-Way Matching:** Compares Supplier Bill vs. Approved Purchase Order (PO) with automated variance calculation and $\pm \text{RM } 5.00$ tolerance checks.
* **Manager Approval Workflows with Confirmation:** Multi-tier authorization threshold for bills exceeding $\text{RM } 5,000$ with double-confirmation dialogs before releasing bank payout queue.
* **Duplicate Detection:** Automatic verification preventing double-billing on supplier reference numbers.

### 3. 🏦 Domestic Banking & Batch Payouts
* **Multi-Bank Batch IBG File Generator with Confirmation:** Confirms beneficiary count, debit bank account, and total disbursement sum before generating standardized payment files:
  * **Maybank MasS / 2E Format** (`.csv`)
  * **CIMB BizChannel Batch Format** (`.txt`)
  * **Public Bank Enterprise** (`.csv`)
  * **RHB Reflex Auto-Debit / IBG** (`.txt`)
* **DuitNow Dynamic QR:** Embeds dynamic DuitNow payment QR codes with Maybank/CIMB account details for instant client settlement.

### 4. 🇲🇾 Statutory Tax & Compliance Engine
* **Tri-Mode Operation:**
  * **Standard Mode (`OFF`):** Instant offline invoicing with DuitNow QR for internal bookkeeping and zero-friction operations.
  * **Sandbox Mode:** Connects to LHDN Preprod API for staff onboarding, testing, and mock clearance UUIDs.
  * **LHDN Live Mode (`ON`):** Real-time statutory clearance with official LHDN verification QR codes linking to MyInvois portal.
* **Confirmed Real CSV / Excel Exports:** Interactive confirmation modals verify record counts and total financial amounts before downloading:
  * **JKDM SST-02 Return CSV** (Box 11a, 11b, 11d, 12, 13)
  * **AR & AP Aging Ledger CSV** (`0-30`, `31-60`, `61-90`, and `90+` critical days)
  * **AR Invoices Master Registry CSV**
  * **AP Bills Master Registry CSV**
  * **Customers & Vendors Master Directories CSV**
* **Real-Time Client-Side Filter Engine:** Instant multi-parameter search and status/mode filtering across Invoices and Supplier Bills with zero page reloads.

---

## 🎨 UI/UX Highlights
* **Tailwind CSS v4 & Alpine.js:** No bloated Filament dependencies; blazing-fast reactive front-end.
* **Exclusive Sidebar Accordion:** Smart route-aware navigation keeping only one module group open at a time.
* **Header Collapse Rail:** Full desktop rail collapse mode centered on Lucide icons.
* **Class-Based Dark Mode:** Instant toggle between Light Mode and Slate Dark Mode with persistent `localStorage` state.
* **Mobile Responsive:** Automatic transformation of large tables into touch-friendly cards on small screens.

---

## 📁 Repository Structure

```text
invoice-management-system/
├── index.html                 # Standalone Public Showcase Page (GitHub Pages ready)
├── documentation/             # System specifications & architectural blueprints
│   ├── software-requirement-specification.md
│   ├── ui-ux-design-specification.md
│   ├── invoice_ui_design_spec.md
│   └── laravel-technical-specification.md
└── ims/                       # Laravel Application Root
    ├── app/
    │   └── Models/            # Eloquent ORM (Invoice, Bill, Customer, Vendor, etc.)
    ├── database/
    │   ├── migrations/        # PostgreSQL / SQLite schema definitions
    │   └── seeders/           # Modular Seeders (UserRole, CompanySetting, Customer, Vendor, Invoice, Bill)
    ├── resources/
    │   ├── css/app.css        # Tailwind v4 configuration & @custom-variant dark
    │   └── views/
    │       ├── components/    # Blade components (<x-card>, <x-button>, <x-badge>, etc.)
    │       ├── admin/         # Admin views (invoices, bills, customers, vendors, banking, reports, settings)
    │       └── welcome.blade.php
    ├── routes/web.php         # Web routes & streaming export endpoints
    └── tests/
        ├── Unit/              # SST, 2-Way Match & Model relationship Unit tests
        └── Feature/           # End-to-end Comprehensive & Smoke test suites
```

---

## 🚀 Quick Start Guide

### Prerequisites
* **PHP 8.2+** (PHP 8.4 recommended) with `sqlite3`, `pdo`, `mbstring`, `openssl` extensions.
* **Composer 2.x**
* **Node.js 18+ & NPM**

### 1. Installation
```bash
# Clone the repository
git clone https://github.com/mralif93/invoice-management-system.git
cd invoice-management-system/ims

# Install dependencies
composer install
npm install
```

### 2. Environment & Database Setup
```bash
# Setup environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Run modular migrations and seeders
php artisan migrate:fresh --seed

# Build frontend assets
npm run build
```

### 3. Start Development Server
```bash
php artisan serve --port=8004
```
Access the application in your browser:
* **Public Landing Page:** `http://127.0.0.1:8004`
* **Admin Portal:** `http://127.0.0.1:8004/admin/login`

---

## 🔐 Default Seeded Credentials

| Role | Email | Password | Access Scope |
| :--- | :--- | :--- | :--- |
| **System Administrator** | `admin@ims-malaysia.com` | `password123` | Full administrative, setting & e-invoice configuration |
| **Accounts Executive** | `accounts@ims-malaysia.com` | `password123` | AR invoicing, AP bill entry, batch payouts |
| **Finance Approver** | `approver@ims-malaysia.com` | `password123` | High-value AP bill approval (> RM 5,000) |
| **Tax Auditor** | `auditor@ims-malaysia.com` | `password123` | Read-only access to SST-02 and 7-year audit archives |

---

## 🧪 Automated Testing Suite

The repository includes complete **Unit**, **Feature**, and **Smoke Test Suites** (**49 tests, 139 assertions**):

```bash
cd ims
php artisan test
```

### Test Breakdown:
* **Unit Tests (`tests/Unit/`):**
  * `TaxAndFinancialCalculationTest.php`: SST $8\%$, $6\%$, and $0\%$ formulas, 2-way match variance tolerance ($\pm \text{RM } 5.00$).
  * `DomainModelRelationshipTest.php`: Eloquent model relationships and cascade rules.
* **Feature & Smoke Tests (`tests/Feature/`):**
  * `InvoiceManagementSystemComprehensiveTest.php`: Full lifecycle user auth, AR invoice generation & store persistence, Quick Customer modal creation, CSV export streams, AP matching, and reporting.
  * `SmokeAllPagesTest.php`: Tests every single public and admin URL endpoint for `HTTP 200/302` response.

---

## 🌐 GitHub Pages Live Demo

You can preview the live, interactive frontend without a server via GitHub Pages:
👉 **[Live GitHub Pages Showcase](https://mralif93.github.io/invoice-management-system/)**

---

## 📄 License & Compliance

Developed with ❤️ for Malaysian Businesses. Distributed under the **MIT License**.
Compliant with Malaysian statutory guidelines from **Lembaga Hasil Dalam Negeri (LHDN)** and **Jabatan Kastam Diraja Malaysia (JKDM)**.
