<x-layouts.public title="Verify Malaysian SST & e-Invoice">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Verification Box Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 mb-3 shadow-sm">
                <i data-lucide="shield-check" class="w-6 h-6"></i>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">Verify Malaysian Invoice</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Instant authenticity lookup against LHDN MyInvois & JKDM SST Register</p>
        </div>

        <x-card class="shadow-md" x-data="{
            lookupType: 'uuid', // 'uuid' or 'inv',
            searchVal: 'EINV-20260830-9842-MY',
            searched: false,
            loading: false,
            doSearch() {
                this.loading = true;
                setTimeout(() => {
                    this.loading = false;
                    this.searched = true;
                }, 400);
            }
        }">
            <!-- Tabs -->
            <div class="flex items-center gap-2 border-b border-slate-100 dark:border-slate-800 pb-4 mb-6">
                <button type="button" @click="lookupType = 'uuid'" :class="lookupType === 'uuid' ? 'bg-indigo-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300'" class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5">
                    <i data-lucide="qr-code" class="w-3.5 h-3.5"></i>
                    <span>LHDN UUID Lookup</span>
                </button>
                <button type="button" @click="lookupType = 'inv'" :class="lookupType === 'inv' ? 'bg-indigo-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300'" class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5">
                    <i data-lucide="file-text" class="w-3.5 h-3.5"></i>
                    <span>Invoice Number & TIN</span>
                </button>
            </div>

            <!-- Search Form -->
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1">
                    <x-input 
                        x-model="searchVal"
                        placeholder="Enter LHDN UUID (e.g., EINV-20260830-9842-MY)" 
                        icon="search" 
                    />
                </div>
                <x-button @click="doSearch()" variant="primary" icon="search">
                    <span x-text="loading ? 'Verifying...' : 'Verify Now'"></span>
                </x-button>
            </div>

            <!-- Search Results Demo Component -->
            <div x-show="searched" x-transition class="mt-8 pt-6 border-t border-slate-100 dark:border-slate-800 space-y-4" style="display: none;">
                
                <!-- Status Banner -->
                <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center">
                            <i data-lucide="check-circle-2" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="font-bold text-sm text-emerald-950 dark:text-emerald-200">Valid & Cleared by LHDN</h3>
                                <x-badge variant="emerald" pulse>MyInvois Verified</x-badge>
                            </div>
                            <p class="text-xs text-emerald-700 dark:text-emerald-400 mt-0.5">Validated on 30 Aug 2026, 12:08:44 PM (MYT / UTC+8)</p>
                        </div>
                    </div>
                </div>

                <!-- Verified Details Table -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 space-y-2">
                        <span class="font-bold text-slate-400 uppercase tracking-wider text-[10px]">Supplier Details</span>
                        <p class="font-bold text-slate-900 dark:text-white text-sm">Nexa Digital Sdn. Bhd.</p>
                        <p class="text-slate-600 dark:text-slate-400">SSM: 202101034567 (1434867-M)</p>
                        <p class="text-slate-600 dark:text-slate-400 font-mono">TIN: C25890123000 | SST: W10-1808-32000045</p>
                    </div>

                    <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 space-y-2">
                        <span class="font-bold text-slate-400 uppercase tracking-wider text-[10px]">Document & Total</span>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Invoice Number:</span>
                            <span class="font-mono font-bold text-slate-900 dark:text-white">INV-2026-0892</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Total Taxable SST:</span>
                            <span class="font-mono font-bold text-slate-900 dark:text-white">MYR 424.00</span>
                        </div>
                        <div class="flex items-center justify-between border-t border-slate-200 dark:border-slate-700 pt-1.5">
                            <span class="font-bold text-slate-700 dark:text-slate-300">Grand Total:</span>
                            <span class="font-mono font-extrabold text-indigo-600 dark:text-indigo-400 text-sm">MYR 6,324.00</span>
                        </div>
                    </div>
                </div>
            </div>
        </x-card>
    </div>
</x-layouts.public>
