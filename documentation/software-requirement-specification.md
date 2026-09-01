# Software Requirements Specification (SRS)
## Simplified Invoice Management System (IMS) – Malaysia Edition

**Document Version:** 1.0.0  
**Target Market / Jurisdiction:** Malaysia (LHDN MyInvois & RMCD / JKDM SST Compliant)  
**Standard Currency:** Malaysian Ringgit ($\text{MYR}$)

---

### Table of Contents
1. [Executive Summary & Business Objectives](#1-executive-summary--business-objectives)
2. [User Roles & Access Control (RBAC)](#2-user-roles--access-control-rbac)
3. [Configurable System Defaults & Parameters](#3-configurable-system-defaults--parameters)
4. [Functional Requirements](#4-functional-requirements)
   - 4.1 [Modular e-Invoicing Mode Engine (ON / OFF Switch)](#41-modular-e-invoicing-mode-engine-on--off-switch)
   - 4.2 [Accounts Receivable (AR) – Customer Invoicing](#42-accounts-receivable-ar--customer-invoicing)
   - 4.3 [Accounts Payable (AP) – Supplier Bill Tracking](#43-accounts-payable-ap--supplier-bill-tracking)
   - 4.4 [Approval Workflows](#44-approval-workflows)
   - 4.5 [Reporting, Analytics & Tax Preparation](#45-reporting-analytics--tax-preparation)
5. [Non-Functional & Statutory Requirements](#5-non-functional--statutory-requirements)
   - 5.1 [Malaysian Legal & Statutory Compliance](#51-malaysian-legal--statutory-compliance)
   - 5.2 [Performance, Reliability & Usability](#52-performance-reliability--usability)
6. [Data Model & Entity-Relationship Architecture](#6-data-model--entity-relationship-architecture)
7. [Implementation Roadmap & Milestones](#7-implementation-roadmap--milestones)

---

### 1. Executive Summary & Business Objectives

The **Simplified Invoice Management System (IMS)** is tailored specifically for Malaysian Small and Medium Enterprises (SMEs), corporate finance teams, and accounting personnel. It streamlines Accounts Receivable (AR) and Accounts Payable (AP) operations while offering zero-friction compliance with statutory tax guidelines.

#### Key Business Objectives
* **Fast AR Billing:** Issue professional, Malaysian SST-compliant invoices in $\text{MYR}$ within 60 seconds and deliver them via Email or WhatsApp.
* **Effortless AP Tracking:** Capture supplier bills via PDF/scanned uploads, match against Purchase Orders (POs), and prevent duplicate bill payments.
* **Optional e-Invoicing (Zero Operational Friction):** Run completely standalone in **Standard Mode (`OFF`)** for uninterrupted daily billing, with the ability to switch to **Compliance Mode (`ON`)** whenever mandated.
* **Malaysian Statutory Compliance:** Adhere to the Sales Tax Act 2018, Service Tax Act 2018, 7-year document retention under Companies Act 2016 § 245 / Income Tax Act 1967 § 82, and the Personal Data Protection Act 2010 (PDPA).

```
                      +-----------------------------+
                      |   Invoice / Bill Creation   |
                      +--------------+--------------+
                                     |
                                     v
                      +-----------------------------+
                      | e-Invoicing Mode Configured?|
                      +--------------+--------------+
                                     |
                    +----------------+----------------+
                    |                                 |
           [ Mode = OFF ]                    [ Mode = ON / LIVE ]
                    |                                 |
                    v                                 v
   +--------------------------------+  +--------------------------------+
   | Instant Standard Invoice       |  | Validate & Transmit to LHDN    |
   | - JKDM SST Compliant           |  | - UBL 2.1 JSON/XML Payload     |
   | - DuitNow Dynamic QR Code      |  | - Digital Signature (X.509)    |
   | - Instant PDF / WhatsApp Share |  | - LHDN Validation UUID & QR    |
   +--------------------------------+  +--------------------------------+
```

---

### 2. User Roles & Access Control (RBAC)

The system enforces simple, role-based permission boundaries:

| Role | Target Persona | Key Permissions |
| :--- | :--- | :--- |
| **System Admin** | Business Owner / IT Lead | Full system configuration, company profile setup, bank account settings, user provisioning, and e-Invoicing mode toggle control. |
| **Finance / Accounts User** | Accounts Clerk / Bookkeeper | Create and issue customer invoices, record supplier bills, perform 2-way matching, record manual payments, and export tax summaries. |
| **Manager / Approver** | Department Head / Director | Review and approve/reject supplier bills and payment batches exceeding authorized spending limits. |
| **Auditor / Tax Agent** | External Accountant *(Read-Only)* | View-only access to invoice archives, payment ledgers, and SST-02 audit reports. |

---

### 3. Configurable System Defaults & Parameters

All operational thresholds, tax percentages, and terms can be customized via the Admin Settings page:

| Parameter Category | System Default | Configurable Range / Options |
| :--- | :--- | :--- |
| **Base Currency** | Malaysian Ringgit ($\text{MYR}$) | Single-currency ($\text{MYR}$) or Multi-currency ($\text{USD, SGD, EUR, RMB}$) with manual or BNM daily rate feed |
| **e-Invoicing Mode** | `OFF (Standard Mode)` | `OFF` (Standalone), `SANDBOX` (Test submission), `PRODUCTION` (Live LHDN clearance) |
| **SST Tax Rates** | • Standard Service Tax: $8\%$<br>• Specific Services (F&B, Telco): $6\%$<br>• Zero-Rated / Exempt: $0\%$ | Custom tax codes (e.g., Sales Tax $5\%, 10\%$, customs tariff codes) |
| **Approval Threshold (Tier 1)** | $\le \text{MYR } 5,000.00$ | Auto-approved or single-step Accounts review ($\text{MYR } 1,000.00 - 20,000.00$) |
| **Approval Threshold (Tier 2)** | $> \text{MYR } 5,000.00$ | Requires Department Manager or Director approval |
| **Matching Tolerance** | $\pm \text{MYR } 5.00$ or $\pm 1.0\%$ | Ringgit variance ($\text{MYR } 0.00 - 50.00$) or percentage ($\pm 0.1\% - 5.0\%$) |
| **Payment Terms** | Net 30 Days | Due on Receipt, Net 7, Net 14, Net 30, Net 60, End of Month (EOM), Custom Date |
| **AR/AP Aging Intervals** | `0–30`, `31–60`, `61–90`, `90+` Days | Configurable bucket days (e.g., `1–15`, `16–30`, `31–45`, `45+` Days) |
| **Rounding Rule** | Mathematical rounding to $2$ decimal places ($\text{sen}$) | Standard 2 decimals or Malaysian 5-sen rounding mechanism for point-of-sale cash settlements |

---

### 4. Functional Requirements

#### 4.1 Modular e-Invoicing Mode Engine (ON / OFF Switch)
* **FR-1.1 Global Mode Switch:**
  * The Admin can set the operational state:
    * `OFF` (Default): Operates completely standalone. PDFs are generated instantly with no external API calls to LHDN.
    * `SANDBOX`: Connects to LHDN MyInvois Sandbox environment for testing and staff training.
    * `PRODUCTION`: Automatically validates and transmits all approved invoices to the live LHDN MyInvois API.
* **FR-1.2 Standard Mode Behavior (`OFF`):**
  * Instant generation of JKDM-compliant tax invoices in PDF format.
  * Collects Malaysian business identifiers in the background (SSM Business Registration Number, SST ID, Tax Identification Number - TIN) to ensure day-one readiness.
* **FR-1.3 Compliance Mode Behavior (`ON`):**
  * Converts invoice data into UBL 2.1 JSON/XML payload.
  * Signs payload using company digital certificate (X.509 SHA-256) per the Digital Signature Act 1997.
  * Receives and stores LHDN Unique Identifier Number (UUID) and validation timestamp.
  * Automatically embeds a scannable **LHDN Validation QR Code** on visual PDF and customer web views.
  * Enforces the statutory **72-hour** buyer cancellation/rejection window.

---

#### 4.2 Accounts Receivable (AR) – Customer Invoicing
* **FR-2.1 Invoice Creation & Line-Item Entry:**
  * Quick customer lookup (auto-fills TIN, SSM BRN, registered address, payment terms).
  * Line items with Item Description, Quantity, Unit Price, Discount ($\text{MYR}$ or $\%$), and SST Rate selection ($0\%, 6\%, 8\%$).
  * Automatic real-time mathematical calculations:
    $$\text{Line Net Amount} = (\text{Quantity} \times \text{Unit Price}) - \text{Discount}$$
    $$\text{Line SST} = \text{Line Net Amount} \times \text{SST Rate}$$
    $$\text{Invoice Grand Total} = \sum \text{Line Net Amount} + \sum \text{Line SST}$$
* **FR-2.2 Document Types Supported:**
  * Standard Tax Invoice ("Invois Cukai")
  * Proforma Invoice / Quotation
  * Credit Note & Debit Note
  * Payment Receipt
* **FR-2.3 Customer Dispatch:**
  * One-click PDF generation with company branding/logo.
  * Direct email delivery with read-receipt tracking and secure invoice view link.
  * Instant WhatsApp message dispatch with pre-filled message template and invoice link.
* **FR-2.4 Payment Collection & Recording:**
  * Generate dynamic **DuitNow QR** codes on invoices for instant customer mobile banking scan-to-pay.
  * Support payment links for **FPX** online banking and card payments.
  * Record partial or full offline payments (Cash, Cheque, Bank Transfer) with automated status updates (`Draft` $\rightarrow$ `Sent` $\rightarrow$ `Partially Paid` $\rightarrow$ `Paid`).

---

#### 4.3 Accounts Payable (AP) – Supplier Bill Tracking
* **FR-3.1 Bill Ingestion & Upload:**
  * Drag-and-drop file upload for supplier bills (PDF, PNG, JPG).
  * Basic optical recognition (OCR) auto-fills Supplier Name, Invoice Number, Date, Subtotal, SST Amount, and Total Amount for user confirmation.
* **FR-3.2 2-Way Purchase Order Matching:**
  * Link supplier bill against an existing Purchase Order (PO).
  * Compare line items and total amounts.
  * Invoices within matching tolerance ($\pm \text{MYR } 5.00$) are auto-approved for payment scheduling; variances above threshold are flagged for supervisor review.
* **FR-3.3 Duplicate Bill Detection:**
  * System alerts the user if a bill with the same **Supplier + Invoice Number + Amount** already exists in the system.
* **FR-3.4 Payment Scheduling & Disbursement:**
  * Track supplier bill due dates with early payment discount reminders.
  * Generate domestic bank payment batch files (Maybank, CIMB, Public Bank, RHB) in standard format (Interbank GIRO - IBG / DuitNow Batch / CSV).

---

#### 4.4 Approval Workflows
* **FR-4.1 Threshold-Based Routing:**
  * Bills $\le \text{MYR } 5,000.00$: Approved automatically upon successful 2-way match, or routed for single Accounts sign-off.
  * Bills $> \text{MYR } 5,000.00$: Routed to Manager / Business Owner approval queue.
* **FR-4.2 Approver Actions:**
  * Actions: **Approve**, **Reject** (with mandatory remark), or **Request Revision**.
  * Instant email/in-app notification to the original submitter upon decision.

---

#### 4.5 Reporting, Analytics & Tax Preparation
* **FR-5.1 SST-02 Summary Report:**
  * Calculates taxable sales by rate ($6\%$ vs. $8\%$), zero-rated supplies, and total SST output tax collected.
  * Calculates total SST input tax incurred on supplier bills.
  * Ready-to-use breakdown for filing Malaysian Customs (JKDM) SST-02 returns bi-monthly.
  * Export formats: **Excel (.xlsx)**, **CSV**, and **Printable PDF**.
* **FR-5.2 AR & AP Aging Summaries:**
  * Interactive aging buckets: `Current`, `1–30 Days`, `31–60 Days`, `61–90 Days`, `90+ Days`.
  * Filterable by customer, vendor, overdue status, and date range.
* **FR-5.3 Audit Log & Financial Export:**
  * Full export of general ledger transactions ready for import into external accounting software (AutoCount, SQL Account, Bukku, Xero, QuickBooks).

---

### 5. Non-Functional & Statutory Requirements

#### 5.1 Malaysian Legal & Statutory Compliance
* **NFR-1.1 7-Year Statutory Record Retention:**
  * All invoices, credit notes, receipts, and vendor bills must be retained in an immutable, read-only electronic archive for **7 years** to satisfy the Income Tax Act 1967 § 82 and Companies Act 2016 § 245.
  * System must provide fast search and bulk PDF download for audit inspections.
* **NFR-1.2 Personal Data Protection Act 2010 (PDPA):**
  * Mask sensitive individual identifiers (such as personal NRIC numbers for sole proprietors or retail clients: `XXXXXX-XX-1234`) on general staff screens.
  * Strict role-based permission barriers to prevent unauthorized export of customer personal data.
* **NFR-1.3 Data Sovereignty & Backups:**
  * System database hosted within secure cloud infrastructure (e.g., AWS Malaysia Region, Azure Malaysia Central, or certified local Tier III data centers).
  * Daily automated encrypted database backups retained for disaster recovery.

#### 5.2 Performance, Reliability & Usability
* **NFR-2.1 Response Time:**
  * Invoice generation and PDF download in Standard Mode within $\le 1.0\text{ second}$.
  * Search and aging report generation within $\le 2.0\text{ seconds}$ for datasets up to $50,000$ records.
* **NFR-2.2 Usability & Responsiveness:**
  * Clean, clutter-free web UI optimized for desktop, tablet, and mobile smartphone browsers.
  * Support for English and Bahasa Melayu user interface languages.

---

### 6. Data Model & Entity-Relationship Architecture

```
┌─────────────────────────┐             ┌─────────────────────────┐
│        Customer         │             │         Vendor          │
├─────────────────────────┤             ├─────────────────────────┤
│ • id (PK)               │             │ • id (PK)               │
│ • name                  │             │ • name                  │
│ • ssm_brn               │             │ • ssm_brn               │
│ • tin_number            │             │ • tin_number            │
│ • sst_number            │             │ • sst_number            │
│ • email / phone         │             │ • bank_name / acc_no    │
└────────────┬────────────┘             └────────────┬────────────┘
             │ 1                                     │ 1
             │                                       │
             │ ∞                                     │ ∞
┌────────────┴────────────┐             ┌────────────┴────────────┐
│      Invoice (AR)       │             │       Bill (AP)         │
├─────────────────────────┤             ├─────────────────────────┤
│ • id (PK)               │             │ • id (PK)               │
│ • invoice_number        │             │ • bill_number           │
│ • customer_id (FK)      │             │ • vendor_id (FK)        │
│ • issue_date / due_date │             │ • po_number             │
│ • subtotal              │             │ • subtotal              │
│ • sst_total             │             │ • sst_total             │
│ • grand_total           │             │ • grand_total           │
│ • status                │             │ • match_status          │
│ • einvoice_mode (OFF/ON)│             │ • approval_status       │
│ • lhdn_uuid (Nullable)  │             │ • file_attachment_url   │
└────────────┬────────────┘             └────────────┬────────────┘
             │ 1                                     │ 1
             │                                       │
             │ ∞                                     │ ∞
┌────────────┴────────────┐             ┌────────────┴────────────┐
│    InvoiceLineItem      │             │       BillLineItem      │
├─────────────────────────┤             ├─────────────────────────┤
│ • id (PK)               │             │ • id (PK)               │
│ • invoice_id (FK)       │             │ • bill_id (FK)          │
│ • description           │             │ • description           │
│ • quantity / unit_price │             │ • quantity / unit_price │
│ • sst_rate (0/6/8%)     │             │ • sst_rate              │
│ • line_total            │             │ • line_total            │
└─────────────────────────┘             └─────────────────────────┘
```

---

### 7. Implementation Roadmap & Milestones

| Milestone | Deliverables | Target Timeline |
| :--- | :--- | :--- |
| **Phase 1: Core System Setup & AR** | Company profile, SST rates, customer master data, fast AR invoice generator, PDF engine, WhatsApp/Email dispatch. | Week 1 – 3 |
| **Phase 2: AP Bill Tracking & Approvals** | Supplier bill upload, 2-way PO matching, threshold-based approval routing ($\text{MYR } 5,000$), duplicate detection. | Week 4 – 5 |
| **Phase 3: Payments & SST-02 Reporting** | DuitNow QR generation, bank batch payout export (IBG/CSV), SST-02 bi-monthly tax summary, AR/AP aging tables. | Week 6 – 7 |
| **Phase 4: e-Invoicing Activation Readiness** | LHDN Sandbox connection testing, digital signing (X.509), QR code validation engine, one-click toggle live activation. | Week 8 |