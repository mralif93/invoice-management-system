<x-layouts.admin header="Malaysian SST-02 Bi-Monthly Tax Return">
    <div class="space-y-6" x-data="{
        period: '2026-04', // Jul - Aug 2026
        exportType: 'xlsx',
        sales8: 48000.00,
        tax8: 3840.00,
        sales6: 12000.00,
        tax6: 720.00,
        exempt: 5500.00,
        inputTax: 1140.00,
        showExportConfirm: false,

        get totalOutputTax() {
            return this.tax8 + this.tax6;
        },

        get netPayable() {
            return this.totalOutputTax - this.inputTax;
        },

        formatMoney(amount) {
            return (amount || 0).toLocaleString('en-MY', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },

        proceedExport() {
            this.showExportConfirm = false;
            window.location.href = '{{ route('admin.reports.sst02.export') }}?period=' + this.period;
        }
    }">
        <!-- Top Control Bar -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                    <i data-lucide="file-spreadsheet" class="w-5 h-5"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-slate-900 dark:text-white">JKDM Form SST-02 Preparation</h2>
                    <p class="text-xs text-slate-500">Royal Malaysian Customs Department Bi-Monthly Return</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <select x-model="period" class="text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white px-3 py-2">
                    <option value="2026-04">Period 4: Jul - Aug 2026</option>
                    <option value="2026-03">Period 3: May - Jun 2026</option>
                    <option value="2026-02">Period 2: Mar - Apr 2026</option>
                </select>

                <button type="button" @click="showExportConfirm = true; $nextTick(() => { if (window.initLucideIcons) window.initLucideIcons(); })" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-xs transition-colors">
                    <i data-lucide="download" class="w-3.5 h-3.5"></i>
                    <span>Export CSV / Excel</span>
                </button>
                <x-button variant="outline" size="sm" icon="printer" onclick="window.print()">
                    Print Return
                </x-button>
            </div>
        </div>

        <!-- Confirmation Modal for SST-02 Return Export -->
        <x-modal 
            show="showExportConfirm" 
            title="Confirm SST-02 Report Export" 
            subtitle="Royal Malaysian Customs Department (JKDM) Statutory Submission" 
            icon="file-spreadsheet" 
            maxWidth="md"
        >
            <div class="space-y-3">
                <div class="p-3.5 rounded-xl bg-emerald-50/70 dark:bg-emerald-950/40 border border-emerald-200/80 dark:border-emerald-800/80 space-y-2">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-600 dark:text-slate-400">Selected Filing Period:</span>
                        <span class="font-bold font-mono text-emerald-700 dark:text-emerald-300" x-text="period === '2026-04' ? 'Jul - Aug 2026 (Period 4)' : (period === '2026-03' ? 'May - Jun 2026 (Period 3)' : 'Mar - Apr 2026 (Period 2)')"></span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-600 dark:text-slate-400">Output Tax (Box 11d):</span>
                        <span class="font-bold font-mono text-slate-900 dark:text-white" x-text="'MYR ' + formatMoney(totalOutputTax)"></span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-600 dark:text-slate-400">Net Payable (Box 13):</span>
                        <span class="font-bold font-mono text-indigo-600 dark:text-indigo-400" x-text="'MYR ' + formatMoney(netPayable)"></span>
                    </div>
                </div>

                <p class="text-xs text-slate-600 dark:text-slate-300">
                    Are you sure you want to generate and export this statutory CSV tax filing report? The file contains official calculation summaries compliant with Malaysian SST-02 schedules.
                </p>
            </div>

            <x-slot:footer>
                <button type="button" @click="showExportConfirm = false" class="px-3.5 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                    Cancel
                </button>
                <button type="button" @click="proceedExport()" class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-xs transition-colors">
                    <i data-lucide="download" class="w-3.5 h-3.5"></i>
                    <span>Confirm & Download CSV</span>
                </button>
            </x-slot:footer>
        </x-modal>

        <!-- Summary Metric Cards Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
            <x-stat-card 
                title="Total Output Tax Collected" 
                value="MYR 4,560.00" 
                subtitle="From AR Customer Invoices (8% & 6%)"
                icon="arrow-up-right"
                iconVariant="indigo"
            />
            <x-stat-card 
                title="Total Input Tax Incurred" 
                value="MYR 1,140.00" 
                subtitle="From AP Supplier Bills"
                icon="arrow-down-left"
                iconVariant="amber"
            />
            <x-stat-card 
                title="Net SST Amount Payable to JKDM" 
                value="MYR 3,420.00" 
                subtitle="Due by: 30 Sep 2026"
                icon="check-circle-2"
                iconVariant="emerald"
            />
        </div>

        <!-- Breakdown Table Card -->
        <x-card title="SST-02 Section 11 Breakdown" subtitle="Detailed Schedule of Taxable Supplies & Output Tax">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-800 text-slate-400 font-semibold uppercase">
                            <th class="pb-3">Field / Item Description</th>
                            <th class="pb-3 text-center">Tax Rate</th>
                            <th class="pb-3 text-right">Taxable Value (MYR)</th>
                            <th class="pb-3 text-right">Tax Amount (MYR)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium">
                        <tr>
                            <td class="py-3 text-slate-900 dark:text-white">11a. Value of Service Tax at Standard Rate</td>
                            <td class="py-3 text-center"><x-badge variant="indigo">8%</x-badge></td>
                            <td class="py-3 text-right font-mono" x-text="formatMoney(sales8)"></td>
                            <td class="py-3 text-right font-mono font-bold text-slate-900 dark:text-white" x-text="formatMoney(tax8)"></td>
                        </tr>
                        <tr>
                            <td class="py-3 text-slate-900 dark:text-white">11b. Value of Service Tax at Specific Services Rate</td>
                            <td class="py-3 text-center"><x-badge variant="amber">6%</x-badge></td>
                            <td class="py-3 text-right font-mono" x-text="formatMoney(sales6)"></td>
                            <td class="py-3 text-right font-mono font-bold text-slate-900 dark:text-white" x-text="formatMoney(tax6)"></td>
                        </tr>
                        <tr>
                            <td class="py-3 text-slate-900 dark:text-white">11c. Value of Exempt / Zero-Rated Supplies</td>
                            <td class="py-3 text-center"><x-badge variant="slate">0% (Exempt)</x-badge></td>
                            <td class="py-3 text-right font-mono" x-text="formatMoney(exempt)"></td>
                            <td class="py-3 text-right font-mono text-slate-400">0.00</td>
                        </tr>
                        <tr class="border-t-2 border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/40">
                            <td class="py-3.5 font-bold text-slate-900 dark:text-white">12. TOTAL SERVICE TAX PAYABLE BEFORE DEDUCTION</td>
                            <td class="py-3.5 text-center"></td>
                            <td class="py-3.5 text-right font-mono font-bold" x-text="formatMoney(sales8 + sales6 + exempt)"></td>
                            <td class="py-3.5 text-right font-mono font-extrabold text-indigo-600 dark:text-indigo-400 text-sm" x-text="formatMoney(totalOutputTax)"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </x-card>
    </div>
</x-layouts.admin>
