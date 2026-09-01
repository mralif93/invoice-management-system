<x-layouts.admin header="Bank Batch Payouts (IBG / Rentas)">
    <div class="space-y-5" x-data="{
        bankFormat: 'maybank',
        selectedBills: ['1', '2'],
        exportSuccess: false,
        
        triggerExport() {
            this.exportSuccess = true;
            setTimeout(() => { this.exportSuccess = false; }, 4000);
        }
    }">
        <!-- Top Stats Row -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <x-stat-card title="Approved Bills Ready" value="MYR 13,546.00" subtitle="2 Supplier Invoices" icon="credit-card" iconVariant="emerald" />
            <x-stat-card title="Batch IBG File Format" value="Maybank MasS" subtitle="Corporate Autopay Format" icon="file-spreadsheet" iconVariant="indigo" />
            <x-stat-card title="Payment Processing" value="Instant T+0" subtitle="Multi-Bank Direct Clearing" icon="clock" iconVariant="amber" />
        </div>

        <!-- Batch Generator Card -->
        <x-card title="Multi-Bank Batch Payout Generator" subtitle="Generate standardized CSV / Text files for direct corporate bank upload">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pb-4 border-b border-slate-200 dark:border-slate-800">
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Target Corporate Bank</label>
                    <select x-model="bankFormat" class="w-full text-xs rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white px-3 py-2">
                        <option value="maybank">Maybank MasS / 2E Format (.csv)</option>
                        <option value="cimb">CIMB BizChannel Batch Format (.txt)</option>
                        <option value="public">Public Bank Enterprise PB Enterprise (.csv)</option>
                        <option value="rhb">RHB Reflex Auto-Debit/IBG (.txt)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Debit From Account</label>
                    <select class="w-full text-xs rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white px-3 py-2 font-mono">
                        <option>Maybank - 5140-1234-8899 (MYR)</option>
                        <option>CIMB - 800-1234-5678 (MYR)</option>
                    </select>
                </div>

                <div class="flex items-end">
                    <button type="button" @click="triggerExport()" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-xs transition-colors">
                        <i data-lucide="download" class="w-4 h-4"></i>
                        <span>Generate & Export Batch File</span>
                    </button>
                </div>
            </div>

            <!-- Toast alert -->
            <div x-show="exportSuccess" x-transition class="p-3 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-xs flex items-center gap-2">
                <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600"></i>
                <span><strong>Batch File Generated:</strong> <code>MAYBANK_PAYOUT_20260901.CSV</code> ready for portal upload.</span>
            </div>

            <!-- Approved Payables Queue Table -->
            <div class="overflow-x-auto pt-2">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-800 text-slate-400 font-semibold uppercase text-[10px] tracking-wider">
                            <th class="pb-3 w-8">
                                <input type="checkbox" checked class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                            </th>
                            <th class="pb-3">Supplier Name</th>
                            <th class="pb-3">Bill Ref</th>
                            <th class="pb-3">Bank Details</th>
                            <th class="pb-3">Beneficiary Account</th>
                            <th class="pb-3 text-right">Amount (MYR)</th>
                            <th class="pb-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80 font-medium">
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors">
                            <td class="py-3"><input type="checkbox" checked class="rounded border-slate-300 text-indigo-600"></td>
                            <td class="py-3 text-slate-900 dark:text-white font-bold">Tekno Logistik Cloud Services Sdn. Bhd.</td>
                            <td class="py-3 font-mono text-slate-500">SUPP-INV-8834</td>
                            <td class="py-3 text-slate-700 dark:text-slate-300">Maybank Berhad</td>
                            <td class="py-3 font-mono font-bold text-indigo-600 dark:text-indigo-400">5140-9988-1122</td>
                            <td class="py-3 text-right font-mono font-bold text-slate-900 dark:text-white">4,536.00</td>
                            <td class="py-3 text-center"><x-badge variant="emerald" size="sm">Approved</x-badge></td>
                        </tr>
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors">
                            <td class="py-3"><input type="checkbox" checked class="rounded border-slate-300 text-indigo-600"></td>
                            <td class="py-3 text-slate-900 dark:text-white font-bold">Wira Network Telecom Sdn. Bhd.</td>
                            <td class="py-3 font-mono text-slate-500">WIRA-TEL-2026-09</td>
                            <td class="py-3 text-slate-700 dark:text-slate-300">CIMB Bank Berhad</td>
                            <td class="py-3 font-mono font-bold text-indigo-600 dark:text-indigo-400">800-1234-5678</td>
                            <td class="py-3 text-right font-mono font-bold text-slate-900 dark:text-white">9,010.00</td>
                            <td class="py-3 text-center"><x-badge variant="emerald" size="sm">Approved</x-badge></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </x-card>
    </div>
</x-layouts.admin>
