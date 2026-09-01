# Malaysian Invoice UI Design & Specification

**Document Version:** 1.0.0  
**Target Platform:** Web Application (Responsive Desktop, Tablet & Mobile)  
**Jurisdiction:** Malaysia (LHDN MyInvois & RMCD SST Standards)

---

## 1. Visual Layout Wireframe

```text
+-----------------------------------------------------------------------------------------+
| [TOOLBAR - NO PRINT]                                                                    |
| [Status Badge: Standard / e-Invois ON] [Toggle Switch] [Share WhatsApp] [Print / PDF]   |
+-----------------------------------------------------------------------------------------+
|                                                                                         |
|  +-----------------------------------------------------------------------------------+  |
|  | [BANNER - ONLY IN e-INVOIS ON MODE]                                                |  |
|  | (✓) LHDN MyInvois Verified & Cleared | UUID: EINV-20260830-9842-MY | Validated: ... |  |
|  +-----------------------------------------------------------------------------------+  |
|                                                                                         |
|  [SUPPLIER LOGO & DETAILS]                                   [DOCUMENT METADATA]        |
|  Nexa Digital Sdn. Bhd.                                      TAX INVOICE / INVOIS CUKAI |
|  SSM BRN: 202101034567 (1434867-M)                           INV-2026-0892              |
|  TIN: C25890123000 | MSIC: 62010                             Issue Date: 30 Aug 2026    |
|  SST Reg No: W10-1808-32000045                               Due Date:   29 Sep 2026    |
|  Bangsar South, 59200 Kuala Lumpur                           Currency:   MYR            |
|                                                                                         |
|  +-------------------------------------+   +-----------------------------------------+  |
|  | BILLED TO (BUYER):                  |   | PAYMENT DETAILS:                        |  |
|  | Bintang Global Logistics Sdn. Bhd.  |   | Bank: Malayan Banking Berhad (Maybank)  |  |
|  | SSM BRN: 201801089211 (1289345-T)   |   | Acc No: 5140-1234-8899                  |  |
|  | TIN: C19830214000 | SST: B16-1809...|   | DuitNow Corp ID: 202101034567           |  |
|  | Shah Alam, 40000 Selangor           |   | Status: [ PENDING PAYMENT ]             |  |
|  +-------------------------------------+   +-----------------------------------------+  |
|                                                                                         |
|  +-----------------------------------------------------------------------------------+  |
|  | #  | Item Description & Scope        | Qty | Unit Price | SST Rate | SST   | Total |  |
|  |----+---------------------------------+-----+------------+----------+-------+-------|  |
|  | 01 | Cloud SaaS Subscription (Aug)   |  1  |   3,500.00 |    8%    | 280.00|3,780.0|  |
|  | 02 | ERP API Connector Setup         |  1  |   1,800.00 |    8%    | 144.00|1,944.0|  |
|  | 03 | Staff Onboarding Workshop       |  1  |     600.00 |  0%(Exm) |   0.00|  600.0|  |
|  +-----------------------------------------------------------------------------------+  |
|                                                                                         |
|  +-------------------------------------+   +-----------------------------------------+  |
|  | QR CODE & COMPLIANCE BOX:           |   | FINANCIAL SUMMARY:                      |  |
|  | [ DuitNow QR ] or [ LHDN Val. QR ]  |   | Subtotal Excl. Tax:       MYR  5,900.00 |  |
|  | Scan to pay via bank / MyInvois     |   | Discounts:              - MYR      0.00 |  |
|  | 7-Year Statutory Storage Notice     |   | Service Tax (8%):         MYR    424.00 |  |
|  |                                     |   | Service Tax (0% Exempt):  MYR      0.00 |  |
|  |                                     |   |-----------------------------------------|  |
|  |                                     |   | TOTAL PAYABLE:            MYR  6,324.00 |  |
|  +-------------------------------------+   +-----------------------------------------+  |
|                                                                                         |
|  [AUTHORIZED SIGNATORY & STAMP AREA]                 [DIGITAL VERIFICATION NOTICE]       |
|  Nexa Digital Sdn. Bhd.                              Computer Generated / Validated Doc  |
+-----------------------------------------------------------------------------------------+
```

---

## 2. Adaptive Dual-Mode Architecture

| Feature | Standard Mode (`e-Invois OFF`) | Compliance Mode (`e-Invois ON`) |
| :--- | :--- | :--- |
| **Header Banner** | Hidden | Displays Green LHDN Clearance Ribbon with UUID & Timestamp |
| **QR Code Display** | Dynamic **DuitNow QR** (Scan to pay) | Scannable **LHDN MyInvois Validation QR** |
| **Transmission** | Local instant generation | Signed (X.509) & transmitted to LHDN MyInvois API |
| **Footer Verification** | "Computer generated invoice" notice | Official digital validation signature watermark |

---

## 3. Detailed Component Specifications

### 3.1 Control Toolbar (No-Print Header)
* **Visual Styling:** Card container with `#FFFFFF` background, `border-slate-200`, `rounded-2xl`, and soft elevation shadow (`shadow-sm`).
* **Interactive Elements:**
  * **Mode Toggle Button:** Switches global UI state between Standard Mode and e-Invois Mode.
  * **WhatsApp Quick Share:** Triggers a pre-formatted URL scheme (`https://wa.me/?text=...`) containing invoice number, amount due, and direct payment link.
  * **Print / Export PDF:** Invokes `window.print()` with CSS media rules that suppress toolbars, backgrounds, and shadows.

### 3.2 LHDN Clearance Ribbon (`#lhdnBanner`)
* **Conditional Visibility:** Active only when `isEInvoiceOn === true`.
* **Visual Styling:** Background `#ECFDF5` (`bg-emerald-50`), border `#A7F3D0` (`border-emerald-200`), text `#064E3B` (`text-emerald-950`).
* **Contained Metadata:**
  * Status indicator badge with pulsing dot animation.
  * Official LHDN Clearance UUID: `EINV-YYYYMMDD-XXXX-XXXX-MY`.
  * Validation timestamp in Malaysia Standard Time (`UTC+8`).

### 3.3 Supplier & Document Header
* **Brand Logo:** Distinct badge with primary color accent (`bg-indigo-600`), white typography, `rounded-2xl`.
* **Company Legal Information (Mandatory Malaysian Fields):**
  * Registered Company Name (e.g., `NEXA DIGITAL SDN. BHD.`)
  * SSM Business Registration Number (New 12-digit format + old format in brackets, e.g., `202101034567 (1434867-M)`)
  * Tax Identification Number (TIN) (e.g., `C25890123000`)
  * Malaysia Standard Industrial Classification (MSIC) 5-digit code (e.g., `62010`)
  * Royal Malaysian Customs Department (JKDM) SST Number (e.g., `W10-1808-32000045`)
  * Physical Business Address
* **Document Meta Block:**
  * Tax Invoice Badge (`bg-indigo-50 text-indigo-700 font-extrabold`)
  * Sequential Document Number (e.g., `INV-2026-0892` in monospace font)
  * Issue Date, Due Date (with payment terms, e.g., `Net 30`), Purchase Order reference, and ISO Currency (`MYR`).

### 3.4 Party Information Grid (2-Column Card)
* **Left Card (Billed To / Customer):**
  * Customer Legal Name
  * Customer SSM BRN, TIN, and SST Registration Number (if applicable)
  * Billing Address & Attention Contact Person
* **Right Card (Payment Details):**
  * Beneficiary Bank Name (e.g., `Malayan Banking Berhad`)
  * Bank Account Number (Monospace format)
  * Beneficiary Account Name
  * DuitNow Corporate ID (SSM BRN)
  * Current Payment Status Badge (`PENDING PAYMENT`, `PAID`, or `PARTIALLY PAID`)

---

## 4. Line Items Table & Calculation Breakdown

### 4.1 Table Column Structure

| Column Header | Alignment | Width | Content / Data Type |
| :--- | :--- | :--- | :--- |
| **#** | Center | `w-12` | 2-digit zero-padded index (`01`, `02`, etc.) |
| **Description & Scope** | Left | Fluid (`auto`) | Primary title (bold) + secondary service explanation |
| **Qty** | Center | `w-16` | Unit quantity integer or decimal |
| **Unit Price (MYR)** | Right | `w-28` | Unit price in Ringgit (2 decimal places) |
| **SST Rate** | Center | `w-20` | Badge showing tax rate (`8%`, `6%`, or `0% Exempt`) |
| **SST (MYR)** | Right | `w-24` | Computed line tax amount: $(\text{Qty} \times \text{Price}) \times \text{Rate}$ |
| **Total (MYR)** | Right | `w-32` | Line total inclusive of tax: $\text{Line Net} + \text{Line SST}$ |

### 4.2 Financial Summary Calculation Box
* **Subtotal (Excl. Tax):** $\sum (\text{Qty} \times \text{Unit Price} - \text{Discount})$
* **Discounts:** Subtotal reduction in $\text{MYR}$.
* **Service Tax (8%):** Calculated sum of all items taxed at standard $8\%$ rate.
* **Service Tax (6%):** Calculated sum of specific taxable services.
* **Exempt / Zero-Rated (0%):** Sum of zero-rated line items.
* **Grand Total Payable:** $\text{Subtotal} - \text{Discount} + \text{Total SST}$

---

## 5. Dual-Mode QR Code Module

### 5.1 Standard Mode: DuitNow Instant Payment QR
* **Purpose:** Allows customers to open any Malaysian mobile banking app (Maybank MAE, CIMB OCTO, PB engage, TNG eWallet) and make instant scan-to-pay settlement.
* **Payload Structure:** EMVCo Malaysian QR standard containing Merchant Account, Merchant Name, Reference Number, and exact invoice amount.

### 5.2 Compliance Mode: LHDN Validation QR
* **Purpose:** Enables tax auditors and buyers to scan and immediately verify the authenticity of the invoice directly on the official LHDN MyInvois validation portal.
* **Payload URL Format:**
  `https://myinvois.hasil.gov.my/verify/{UUID}?tin={SUPPLIER_TIN}`

---

## 6. CSS Print & PDF Optimization Rules

To ensure high-fidelity physical printing and PDF export via headless Chrome or standard browser print dialogs, apply the following CSS rules:

```css
@media print {
  /* Suppress UI controls, shadows, and screen backgrounds */
  body {
    background-color: #ffffff !important;
    padding: 0 !important;
    font-size: 11pt !important;
  }

  .no-print {
    display: none !important;
  }

  .invoice-card {
    box-shadow: none !important;
    border: none !important;
    padding: 0 !important;
    max-width: 100% !important;
    width: 100% !important;
  }

  /* Prevent table row breaking across pages */
  tr {
    page-break-inside: avoid !important;
  }

  /* Force background colors and borders to render in print */
  * {
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
  }
}
```
