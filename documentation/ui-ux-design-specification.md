# UI/UX Design System & Frontend Architecture Requirements
## Simplified Invoice Management System (IMS) – Malaysia Edition

**Document Version:** 1.0.0  
**Design Philosophy:** Modern, Clean, High-Density FinTech SaaS (Linear / Stripe inspired)  
**Target Viewports:** Desktop (1440px / 1920px), Tablet (768px - 1024px), Mobile Smartphone (375px - 430px)

---

### 1. Aesthetic Identity & Color Palette

The interface must convey trust, precision, and modern Malaysian corporate aesthetics.

#### 1.1 Curated Color Tokens
* **Primary / Brand:**
  * Primary Deep: `#0F172A` (Slate 900)
  * Brand Indigo: `#4F46E5` (Indigo 600) — Primary CTAs, active highlights
  * Brand Indigo Subtle: `#EEF2FF` (Indigo 50) — Active hover states, badges
* **Compliance & Success (LHDN Verified / Paid):**
  * Emerald: `#059669` (Emerald 600)
  * Emerald Surface: `#ECFDF5` (Emerald 50)
  * Emerald Border: `#A7F3D0` (Emerald 200)
* **Pending / Variance Flagged:**
  * Amber: `#D97706` (Amber 600)
  * Amber Surface: `#FFFBEB` (Amber 50)
* **Danger / Overdue / Rejected:**
  * Rose: `#E11D48` (Rose 600)
  * Rose Surface: `#FFF1F2` (Rose 50)
* **Neutrals & Surfaces:**
  * Background Light: `#F8FAFC` (Slate 50)
  * Surface Card: `#FFFFFF` (Pure White) with `1px border border-slate-200/80`
  * Text Primary: `#0F172A` (Slate 900)
  * Text Secondary: `#64748B` (Slate 500)
  * Text Muted / Placeholder: `#94A3B8` (Slate 400)

#### 1.2 Typography
* **Primary Font Family:** `Inter` or `Plus Jakarta Sans` (Clean, geometric, highly legible for numbers).
* **Monospace Font (Invoices, UUIDs, Bank Accounts, Amounts):** `JetBrains Mono` or `Fira Code`.
* **Tabular Figures:** Always apply `font-variant-numeric: tabular-nums` to financial tables so numbers align cleanly on decimals.

---

### 2. Core UI Layout & Screen Architecture

The UI should follow a standard **Left Sidebar + Top Context Header + Main Fluid Content** layout with collapsible navigation on mobile.

```
+-----------------------------------------------------------------------------------------+
| [LOGO] IMS Malaysia  | 🔍 Global Search (INV#, Vendor, Customer...)  | 🔔 (2) | 👤 Admin  |
+----------------------+------------------------------------------------------------------+
| 📊 Dashboard         | Breadcrumb: Invoices / Create New Tax Invoice                   |
| 🧾 Invoices (AR)     +------------------------------------------------------------------+
| 📥 Supplier Bills(AP)| [MODE TOGGLE: Standard (e-Invois OFF) <---> Compliance (LHDN ON)]|
| 👥 Contacts (Buyers) |                                                                  |
| 🏢 Vendors           |  +------------------------------------------------------------+  |
| 📊 SST-02 Reports    |  | Main Invoice Builder / View Area                           |  |
| ⚙️ Settings          |  |                                                            |  |
|                      |  |                                                            |  |
+----------------------+--+------------------------------------------------------------+--+
```

---

### 3. Key Screen Requirements & Suggestions

#### 3.1 Fast Invoice Builder (AR Screen)
* **Speed-Optimized UX (<60s Creation):**
  * Auto-complete customer search dropdown (typing 2 letters fills SSM BRN, TIN, Address, and payment terms).
  * Dynamic Line-Item Grid: Keyboard shortcuts (`Tab` / `Enter` to add new row, arrow keys to navigate).
  * Real-time calculation footer that calculates Net, SST (0%, 6%, 8%), Discount, and Total dynamically without page reload.
  * Instant Action Bar:
    * `[ Save Draft ]`
    * `[ Save & Preview PDF ]`
    * `[ Share WhatsApp 📲 ]` (Generates click-to-chat `wa.me` message with payment link)
    * `[ Issue & Transmit LHDN ]` (Only when Mode = ON)

#### 3.2 Supplier Bill Ingestion & 2-Way Match (AP Screen)
* **Split-Screen Reviewer (Modern FinTech Feature):**
  * **Left Side (50%):** Embedded interactive PDF / image viewer for the uploaded supplier bill.
  * **Right Side (50%):** Extracted data form + PO comparison table.
  * **Visual Matching Indicator:**
    * ✅ Green badge when Bill Amount == PO Amount ($\le \pm\text{MYR } 5.00$ tolerance).
    * ⚠️ Amber alert with exact Ringgit variance highlighted if mismatch occurs.
  * **One-Click Approval:** Direct `[ Approve for Payout ]` or `[ Reject with Reason ]`.

#### 3.3 Interactive Financial Dashboard
* **Metrics Cards (KPIs):**
  * Total Outstanding AR (with Overdue count).
  * Pending AP Bills requiring approval ($>\text{MYR } 5,000$).
  * Estimated Bi-monthly SST-02 Net Payable ($Output - Input$).
  * LHDN e-Invoicing Validation Success Rate ($99.8\%$).
* **Aging Bar Charts / Buckets:**
  * Quick visual tabs: `Current`, `1-30 Days`, `31-60 Days`, `61-90 Days`, `90+ Days`.
* **Recent Activity Feed & One-Click Bank Payout Export:**
  * Checkbox list of approved bills $\rightarrow$ `[ Export Maybank/CIMB Batch CSV ]`.

#### 3.4 Adaptive Invoice View / PDF Layout
* **Dynamic Compliance Ribbon:**
  * When e-Invoicing is `OFF`: Clean Malaysian tax invoice with company logo and **DuitNow Dynamic Scan-to-Pay QR**.
  * When e-Invoicing is `ON`: Displays official green LHDN clearance ribbon, UUID, timestamp, and **LHDN Validation QR**.
* **Print Stylesheet:**
  * Automatic removal of navigation, sidebars, and buttons on print/PDF export (`@media print`).

---

### 4. Recommended Frontend Libraries & UI Stack

| Tool / Library | Role & Purpose | Why & Best Practice |
| :--- | :--- | :--- |
| **Tailwind CSS (v3.4 / v4)** | Core Utility Styling | Clean responsive layouts, dark mode (`class="dark"`), custom color tokens (`slate`, `indigo`, `emerald`), and print utility overrides (`print:hidden`). |
| **Lucide Icons (`lucide-static` / Blade Lucide)** | UI Iconography System | Modern, lightweight, consistent 24x24 stroke icons (`FileText`, `QrCode`, `Building2`, `CheckCircle2`, `AlertTriangle`, `Share2`, `Download`, `Printer`). |
| **Micro-Animations (Tailwind + Alpine.js)** | State Transitions & Feedback | Built-in Tailwind transitions (`transition-all duration-200 ease-in-out`), subtle pulse rings for status badges (`animate-pulse`, `animate-ping`), and smooth dropdown/modal fade-ins via Alpine `x-transition`. *(Replaces bulky Animate.css with zero-overhead CSS).* |
| **PDF Preview Plugin (`PDF.js` / PDFObject)** | In-Browser Document Viewer | High-performance split-screen preview for uploaded AP supplier bills and AR invoice proofs before printing/sending. |

---

### 5. PDF Preview Plugin Architecture (AP Split-Screen & AR Previews)

For in-browser previewing of uploaded bills and generated tax invoices without downloading:

```html
<!-- PDF Previewer Component Structure (Alpine.js + PDF.js / iframe) -->
<div x-data="{ pdfUrl: '/storage/bills/bill-2026-001.pdf', zoom: 100 }" class="w-full h-full flex flex-col bg-slate-900 rounded-2xl overflow-hidden border border-slate-800 shadow-xl">
  <!-- Viewer Control Toolbar -->
  <div class="flex items-center justify-between px-4 py-2 bg-slate-800 text-slate-200 text-xs border-b border-slate-700">
    <div class="flex items-center gap-2">
      <i data-lucide="file-text" class="w-4 h-4 text-indigo-400"></i>
      <span class="font-mono font-medium">supplier_invoice_inv9842.pdf</span>
    </div>
    <div class="flex items-center gap-3">
      <button @click="zoom = Math.max(50, zoom - 10)" class="hover:text-white"><i data-lucide="zoom-out" class="w-4 h-4"></i></button>
      <span x-text="`${zoom}%`" class="font-mono">100%</span>
      <button @click="zoom = Math.min(200, zoom + 10)" class="hover:text-white"><i data-lucide="zoom-in" class="w-4 h-4"></i></button>
      <a :href="pdfUrl" target="_blank" class="hover:text-white pl-2 border-l border-slate-600"><i data-lucide="external-link" class="w-4 h-4"></i></a>
    </div>
  </div>
  
  <!-- PDF Render Area -->
  <div class="flex-1 bg-slate-950 overflow-auto flex items-center justify-center p-4">
    <iframe :src="`${pdfUrl}#toolbar=0&navpanes=0`" class="w-full h-full rounded shadow-md border-0 bg-white"></iframe>
  </div>
</div>
```

---

### 6. Lucide Icon Reference Mapping for Core Workflows

| Action / Entity | Lucide Icon | Usage |
| :--- | :--- | :--- |
| **Tax Invoice (AR)** | `<i data-lucide="file-text"></i>` | Invoices list and AR creation |
| **Supplier Bill (AP)** | `<i data-lucide="receipt"></i>` | Supplier bills and 2-way match |
| **LHDN Status (Valid)** | `<i data-lucide="shield-check"></i>` | Verified e-Invoicing badge |
| **DuitNow / LHDN QR** | `<i data-lucide="qr-code"></i>` | Scan-to-pay & validation modals |
| **WhatsApp Share** | `<i data-lucide="send"></i>` / `<i data-lucide="share-2"></i>` | One-click direct dispatch |
| **2-Way Match Passed** | `<i data-lucide="check-check"></i>` | PO matching success |
| **Variance Flagged** | `<i data-lucide="alert-triangle"></i>` | AP mismatch warning |
| **Print / Export PDF** | `<i data-lucide="printer"></i>` / `<i data-lucide="download"></i>` | PDF action bar |

---

### 7. Implementation Stack Architecture: Livewire 3 + Alpine.js + Tailwind CSS

**Selected Frontend Stack:** **Laravel Blade + Livewire 3 + Alpine.js + Tailwind CSS + Lucide Icons**

| Component | Responsibility |
| :--- | :--- |
| **Laravel 11/12 + Blade** | Base layout templates, components (`<x-card>`, `<x-button>`, `<x-badge>`), SEO meta, and auth scaffolding. |
| **Livewire 3** | Reactive backend-driven UI without full page refreshes: real-time invoice line additions, dynamic SST tax calculations, PO 2-way matching comparison, and filterable aging tables. |
| **Alpine.js** | Client-side micro-interactions: collapsible sidebar, dark/light mode toggles, instant copy-to-clipboard buttons, DuitNow QR modals, and PDF previewer controls. |
| **Tailwind CSS (v4)** | Custom FinTech design tokens, tabular typography, responsive grids, and `@media print` PDF styles. |
| **Lucide Icons** | SVG icon system (`blade-lucide-icons` or Lucide JS). |

---

### 8. Micro-Interactions & UX Polish Checklist

1. **Auto-Formatting Currency Inputs:** Typing `1500` automatically formats to `1,500.00` on blur with `MYR` prefix.
2. **Status Badges with Pulse Indicators:**
   * `Valid LHDN`: Soft green background with glowing green dot (`animate-pulse`).
   * `Pending Submission`: Subtle rotating spinner icon.
3. **Copy-to-Clipboard Buttons:** One-click copy for LHDN UUIDs, Bank Account numbers, and DuitNow IDs with a checkmark animation.
4. **Toast Notifications:** Smooth notifications on invoice creation, WhatsApp share URL generation, and batch export downloads.
5. **Dark Mode Support:** Crisp dark theme (Slate 900 background, Slate 800 cards, high-contrast typography).
