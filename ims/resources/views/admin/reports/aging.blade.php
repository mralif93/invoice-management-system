<x-layouts.admin header="AR & AP Aging Analysis">
    <div class="space-y-5" x-data="{ tab: 'ar' }">
        <!-- Tab Selector -->
        <div class="flex items-center gap-2 p-1 rounded-xl bg-slate-200/80 dark:bg-slate-800 w-fit">
            <button type="button" @click="tab = 'ar'" class="px-4 py-1.5 rounded-lg text-xs font-bold transition-all" :class="tab === 'ar' ? 'bg-white dark:bg-slate-700 text-slate-900 dark:text-white shadow-xs' : 'text-slate-500 hover:text-slate-900 dark:text-slate-400'">
                Accounts Receivable (Customer Aging)
            </button>
            <button type="button" @click="tab = 'ap'" class="px-4 py-1.5 rounded-lg text-xs font-bold transition-all" :class="tab === 'ap' ? 'bg-white dark:bg-slate-700 text-slate-900 dark:text-white shadow-xs' : 'text-slate-500 hover:text-slate-900 dark:text-slate-400'">
                Accounts Payable (Supplier Aging)
            </button>
        </div>

        <!-- Aging Bucket Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-3.5">
            <div class="p-3.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs">
                <span class="text-[10px] font-bold text-slate-400 uppercase">Current (0-30 Days)</span>
                <p class="text-base font-extrabold text-emerald-600 font-mono mt-1" x-text="tab === 'ar' ? 'MYR 6,324.00' : 'MYR 13,546.00'"></p>
                <span class="text-[10px] text-slate-400">Within terms</span>
            </div>

            <div class="p-3.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs">
                <span class="text-[10px] font-bold text-slate-400 uppercase">31 - 60 Days</span>
                <p class="text-base font-extrabold text-amber-600 font-mono mt-1" x-text="tab === 'ar' ? 'MYR 13,500.00' : 'MYR 0.00'"></p>
                <span class="text-[10px] text-slate-400">Due reminder sent</span>
            </div>

            <div class="p-3.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs">
                <span class="text-[10px] font-bold text-slate-400 uppercase">61 - 90 Days</span>
                <p class="text-base font-extrabold text-rose-500 font-mono mt-1">MYR 0.00</p>
                <span class="text-[10px] text-slate-400">Escalation queue</span>
            </div>

            <div class="p-3.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs">
                <span class="text-[10px] font-bold text-slate-400 uppercase">90+ Days (Critical)</span>
                <p class="text-base font-extrabold text-rose-600 font-mono mt-1">MYR 0.00</p>
                <span class="text-[10px] text-slate-400">Legal notice review</span>
            </div>

            <div class="p-3.5 rounded-xl bg-indigo-50 dark:bg-indigo-950/40 border border-indigo-200 dark:border-indigo-800 shadow-xs col-span-2 lg:col-span-1">
                <span class="text-[10px] font-bold text-indigo-700 dark:text-indigo-400 uppercase">Total Outstanding</span>
                <p class="text-base font-extrabold text-indigo-700 dark:text-indigo-300 font-mono mt-1" x-text="tab === 'ar' ? 'MYR 19,824.00' : 'MYR 13,546.00'"></p>
                <span class="text-[10px] text-indigo-500">Gross balance</span>
            </div>
        </div>

        <!-- Aging Ledger Table -->
        <x-card title="Aging Ledger Breakdown" subtitle="Detailed Aging per Buyer & Supplier entity">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-800 text-slate-400 font-semibold uppercase text-[10px] tracking-wider">
                            <th class="pb-3">Entity Name</th>
                            <th class="pb-3">Reference #</th>
                            <th class="pb-3 text-right">0-30 Days</th>
                            <th class="pb-3 text-right">31-60 Days</th>
                            <th class="pb-3 text-right">61-90 Days</th>
                            <th class="pb-3 text-right">90+ Days</th>
                            <th class="pb-3 text-right">Total Balance (MYR)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80 font-medium">
                        <template x-if="tab === 'ar'">
                            <tr>
                                <td class="py-3 text-slate-900 dark:text-white font-bold">Bintang Global Logistics Sdn. Bhd.</td>
                                <td class="py-3 font-mono text-indigo-600">INV-2026-0892</td>
                                <td class="py-3 text-right font-mono font-bold text-emerald-600">6,324.00</td>
                                <td class="py-3 text-right font-mono text-slate-400">0.00</td>
                                <td class="py-3 text-right font-mono text-slate-400">0.00</td>
                                <td class="py-3 text-right font-mono text-slate-400">0.00</td>
                                <td class="py-3 text-right font-mono font-bold text-slate-900 dark:text-white">6,324.00</td>
                            </tr>
                        </template>
                        <template x-if="tab === 'ar'">
                            <tr>
                                <td class="py-3 text-slate-900 dark:text-white font-bold">Borneo Retail Hypermarket Sdn. Bhd.</td>
                                <td class="py-3 font-mono text-rose-600">INV-2026-0740</td>
                                <td class="py-3 text-right font-mono text-slate-400">0.00</td>
                                <td class="py-3 text-right font-mono font-bold text-amber-600">13,500.00</td>
                                <td class="py-3 text-right font-mono text-slate-400">0.00</td>
                                <td class="py-3 text-right font-mono text-slate-400">0.00</td>
                                <td class="py-3 text-right font-mono font-bold text-slate-900 dark:text-white">13,500.00</td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </x-card>
    </div>
</x-layouts.admin>
