<x-layouts.admin header="Financial & Compliance Overview">
    <!-- Top Mode Status Banner -->
    <div class="p-4 rounded-2xl border transition-all duration-300"
         :class="{
            'bg-slate-900 text-white border-slate-800': eInvoiceMode === 'off',
            'bg-gradient-to-r from-amber-950 to-slate-900 border-amber-800/80 text-white': eInvoiceMode === 'sandbox',
            'bg-gradient-to-r from-emerald-950 to-slate-900 border-emerald-800/80 text-white': eInvoiceMode === 'production'
         }">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center font-bold"
                     :class="{
                        'bg-slate-800 text-slate-300': eInvoiceMode === 'off',
                        'bg-amber-500/20 text-amber-400 border border-amber-500/30': eInvoiceMode === 'sandbox',
                        'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30': eInvoiceMode === 'production'
                     }">
                    <i data-lucide="shield-check" class="w-5 h-5"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="font-extrabold text-sm sm:text-base tracking-tight">
                            System Operating Mode: <span class="uppercase tracking-wider" x-text="eInvoiceMode === 'off' ? 'Standard Mode (OFF)' : 'e-Invois ' + eInvoiceMode"></span>
                        </h3>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold"
                              :class="{
                                'bg-slate-800 text-slate-400': eInvoiceMode === 'off',
                                'bg-amber-500/30 text-amber-300 border border-amber-500/40 animate-pulse': eInvoiceMode === 'sandbox',
                                'bg-emerald-500/30 text-emerald-300 border border-emerald-500/40 animate-pulse': eInvoiceMode === 'production'
                              }"
                              x-text="eInvoiceMode === 'off' ? 'Zero Friction' : 'LHDN Connected'">
                        </span>
                    </div>
                    <p class="text-xs text-slate-400 mt-0.5" x-text="eInvoiceMode === 'off' ? 'Invoices are issued instantly as JKDM-compliant PDFs with DuitNow QR codes.' : 'Invoices are digitally signed (X.509) and validated with LHDN MyInvois.'"></p>
                </div>
            </div>

            <!-- Quick Mode Switch Pills -->
            <div class="flex items-center gap-1.5 bg-slate-800/80 p-1 rounded-xl border border-slate-700/60 text-xs">
                <button type="button" @click="setEInvoiceMode('off')" class="px-3 py-1.5 rounded-lg font-bold transition-all" :class="eInvoiceMode === 'off' ? 'bg-indigo-600 text-white' : 'text-slate-400 hover:text-white'">
                    Standard
                </button>
                <button type="button" @click="setEInvoiceMode('sandbox')" class="px-3 py-1.5 rounded-lg font-bold transition-all" :class="eInvoiceMode === 'sandbox' ? 'bg-amber-500 text-white' : 'text-slate-400 hover:text-white'">
                    Sandbox
                </button>
                <button type="button" @click="setEInvoiceMode('production')" class="px-3 py-1.5 rounded-lg font-bold transition-all" :class="eInvoiceMode === 'production' ? 'bg-emerald-600 text-white' : 'text-slate-400 hover:text-white'">
                    Production
                </button>
            </div>
        </div>
    </div>

    <!-- Live KPI Metric Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Metric 1: Total Outstanding AR -->
        <x-stat-card 
            title="Outstanding AR" 
            value="MYR {{ number_format($totalOutstandingAr ?? 19824.00, 2) }}" 
            subtitle="{{ $overdueCount ?? 1 }} Invoice(s) Overdue (>30 Days)"
            icon="file-text"
            iconVariant="indigo"
        />

        <!-- Metric 2: Pending AP Approvals -->
        <x-stat-card 
            title="Pending AP Bills" 
            value="MYR {{ number_format($pendingApTotal ?? 9010.00, 2) }}" 
            subtitle="{{ $pendingApCount ?? 1 }} Bill(s) > MYR 5,000 Threshold"
            icon="receipt"
            iconVariant="amber"
        />

        <!-- Metric 3: SST-02 Estimated Net -->
        <x-stat-card 
            title="SST-02 Net Tax" 
            value="MYR {{ number_format($netSst ?? 1086.00, 2) }}" 
            subtitle="Period: Jul - Aug 2026"
            icon="file-spreadsheet"
            iconVariant="emerald"
        />

        <!-- Metric 4: e-Invoicing Clearance -->
        <x-stat-card 
            title="LHDN Validated" 
            value="100.0%" 
            subtitle="All compliance invoices cleared"
            icon="check-check"
            iconVariant="indigo"
        />
    </div>

    <!-- Recent Actions & Table Demo -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left: Real Seeded Invoices List (2 Cols) -->
        <div class="lg:col-span-2 p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">Recent Customer Invoices (AR)</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Database Records from `invoices` table</p>
                </div>
                <a href="{{ route('admin.invoices.create') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-sm transition-all">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                    <span>New Invoice</span>
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-800 text-slate-400 font-semibold uppercase tracking-wider">
                            <th class="pb-3">Invoice #</th>
                            <th class="pb-3">Customer</th>
                            <th class="pb-3">Date</th>
                            <th class="pb-3 text-right">Amount (MYR)</th>
                            <th class="pb-3 text-center">Mode</th>
                            <th class="pb-3 text-center">Status</th>
                            <th class="pb-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 font-medium">
                        @forelse ($invoices as $inv)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
                                <td class="py-3 font-mono font-bold text-indigo-600 dark:text-indigo-400">{{ $inv->invoice_number }}</td>
                                <td class="py-3 text-slate-900 dark:text-white font-semibold">{{ $inv->customer->name ?? 'Unknown Customer' }}</td>
                                <td class="py-3 text-slate-500">{{ $inv->issue_date->format('d M Y') }}</td>
                                <td class="py-3 text-right font-mono font-bold">{{ number_format($inv->grand_total, 2) }}</td>
                                <td class="py-3 text-center">
                                    @if ($inv->einvoice_mode === 'production')
                                        <x-badge variant="emerald" size="sm" pulse>e-Invois ON</x-badge>
                                    @elseif ($inv->einvoice_mode === 'sandbox')
                                        <x-badge variant="amber" size="sm" pulse>Sandbox</x-badge>
                                    @else
                                        <x-badge variant="slate" size="sm">Standard</x-badge>
                                    @endif
                                </td>
                                <td class="py-3 text-center">
                                    @if ($inv->status === 'paid')
                                        <x-badge variant="emerald" size="sm">Paid</x-badge>
                                    @elseif ($inv->status === 'issued')
                                        <x-badge variant="amber" size="sm">Pending</x-badge>
                                    @else
                                        <x-badge variant="slate" size="sm">{{ ucfirst($inv->status) }}</x-badge>
                                    @endif
                                </td>
                                <td class="py-3 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <button class="p-1 text-slate-400 hover:text-indigo-600 transition-colors" title="Quick Share WhatsApp">
                                            <i data-lucide="share-2" class="w-4 h-4"></i>
                                        </button>
                                        <button class="p-1 text-slate-400 hover:text-indigo-600 transition-colors" title="Download PDF">
                                            <i data-lucide="download" class="w-4 h-4"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-6 text-center text-slate-400">No invoices found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right: Fast Actions & DuitNow QR Widget -->
        <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-sm space-y-4">
            <h2 class="text-base font-bold text-slate-900 dark:text-white">Quick Operations</h2>
            
            <div class="space-y-2.5">
                <a href="{{ route('admin.invoices.create') }}" class="w-full flex items-center justify-between p-3 rounded-xl bg-slate-50 dark:bg-slate-800 hover:bg-indigo-50 dark:hover:bg-indigo-950/50 border border-slate-200 dark:border-slate-700 text-xs font-semibold transition-colors group">
                    <div class="flex items-center gap-2.5">
                        <i data-lucide="plus-circle" class="w-4 h-4 text-indigo-600 dark:text-indigo-400"></i>
                        <span>Create Tax Invoice (< 60s)</span>
                    </div>
                    <i data-lucide="chevron-right" class="w-4 h-4 text-slate-400 group-hover:translate-x-0.5 transition-transform"></i>
                </a>

                <a href="{{ route('admin.bills.upload') }}" class="w-full flex items-center justify-between p-3 rounded-xl bg-slate-50 dark:bg-slate-800 hover:bg-indigo-50 dark:hover:bg-indigo-950/50 border border-slate-200 dark:border-slate-700 text-xs font-semibold transition-colors group">
                    <div class="flex items-center gap-2.5">
                        <i data-lucide="upload-cloud" class="w-4 h-4 text-indigo-600 dark:text-indigo-400"></i>
                        <span>Upload Supplier Bill (OCR & 2-Way)</span>
                    </div>
                    <i data-lucide="chevron-right" class="w-4 h-4 text-slate-400 group-hover:translate-x-0.5 transition-transform"></i>
                </a>

                <a href="{{ route('admin.reports.sst02') }}" class="w-full flex items-center justify-between p-3 rounded-xl bg-slate-50 dark:bg-slate-800 hover:bg-indigo-50 dark:hover:bg-indigo-950/50 border border-slate-200 dark:border-slate-700 text-xs font-semibold transition-colors group">
                    <div class="flex items-center gap-2.5">
                        <i data-lucide="file-spreadsheet" class="w-4 h-4 text-emerald-600 dark:text-emerald-400"></i>
                        <span>Export SST-02 Return Excel</span>
                    </div>
                    <i data-lucide="chevron-right" class="w-4 h-4 text-slate-400 group-hover:translate-x-0.5 transition-transform"></i>
                </a>
            </div>
        </div>
    </div>
</x-layouts.admin>
