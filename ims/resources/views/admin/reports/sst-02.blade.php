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

        get totalOutputTax() {
            return this.tax8 + this.tax6;
        },

        get netPayable() {
            return this.totalOutputTax - this.inputTax;
        },

        formatMoney(amount) {
            return (amount || 0).toLocaleString('en-MY', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
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

                <x-button variant="success" size="sm" icon="download" onclick="alert('Exporting SST-02 Excel spreadsheet (.xlsx)...')">
                    Export Excel
                </x-button>
                <x-button variant="outline" size="sm" icon="printer" onclick="window.print()">
                    Print Return
                </x-button>
            </div>
        </div>

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
