<x-layouts.admin header="Vendors & Suppliers">
    <div class="space-y-5" x-data="{
        showExportConfirm: false,
        proceedExport() {
            this.showExportConfirm = false;
            window.location.href = '{{ route('admin.vendors.export') }}';
        }
    }">
        <!-- Top Stats Row -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <x-stat-card title="Registered Vendors" value="{{ $vendors->count() }}" subtitle="Active Accounts Payable" icon="building-2" iconVariant="indigo" />
            <x-stat-card title="SST Registered" value="{{ $vendors->whereNotNull('sst_number')->count() }}" subtitle="Input Tax Claim Eligible" icon="file-text" iconVariant="emerald" />
            <x-stat-card title="Bank Details Verified" value="{{ $vendors->whereNotNull('bank_account_number')->count() }}" subtitle="Batch IBG Ready" icon="credit-card" iconVariant="amber" />
        </div>

        <!-- Export Vendors Confirmation Modal -->
        <x-modal 
            show="showExportConfirm" 
            title="Confirm Vendors Directory Export" 
            subtitle="Master Accounts Payable Directory Data (CSV)" 
            icon="file-spreadsheet" 
            maxWidth="md"
        >
            <div class="space-y-3">
                <div class="p-3.5 rounded-xl bg-indigo-50/70 dark:bg-indigo-950/40 border border-indigo-200/80 dark:border-indigo-800/80 space-y-2 text-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-600 dark:text-slate-400">Total Supplier Records:</span>
                        <span class="font-bold font-mono text-slate-900 dark:text-white">{{ $vendors->count() }} Vendors</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-600 dark:text-slate-400">Bank Details Configured:</span>
                        <span class="font-bold font-mono text-emerald-600 dark:text-emerald-400">{{ $vendors->whereNotNull('bank_account_number')->count() }} Accounts</span>
                    </div>
                </div>

                <p class="text-xs text-slate-600 dark:text-slate-300">
                    Are you sure you want to export the master vendors directory with banking settlement details and SST registration numbers in CSV format?
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

        <!-- Master Vendors Card -->
        <x-card title="Vendor Master Directory" subtitle="Suppliers, Bank Settlement Accounts & Tax Numbers">
            <x-slot:action>
                <button type="button" @click="showExportConfirm = true; $nextTick(() => { if (window.initLucideIcons) window.initLucideIcons(); })" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-bold transition-colors">
                    <i data-lucide="download" class="w-3.5 h-3.5"></i>
                    <span>Export CSV</span>
                </button>
            </x-slot:action>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-800 text-slate-400 font-semibold uppercase text-[10px] tracking-wider">
                            <th class="pb-3">Vendor / Company Name</th>
                            <th class="pb-3">SSM Registration</th>
                            <th class="pb-3">TIN Number</th>
                            <th class="pb-3">SST Registration</th>
                            <th class="pb-3">Bank Settlement Account</th>
                            <th class="pb-3">Contact</th>
                            <th class="pb-3 text-right">Bills Count</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80 font-medium">
                        @forelse ($vendors as $v)
                            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors">
                                <td class="py-3 text-slate-900 dark:text-white font-bold">{{ $v->name }}</td>
                                <td class="py-3 font-mono text-slate-600 dark:text-slate-300">{{ $v->ssm_brn }}</td>
                                <td class="py-3 font-mono font-bold text-indigo-600 dark:text-indigo-400">{{ $v->tin_number ?? '-' }}</td>
                                <td class="py-3 font-mono text-slate-500">{{ $v->sst_number ?? 'Non-SST' }}</td>
                                <td class="py-3">
                                    <div class="font-bold text-slate-900 dark:text-white">{{ $v->bank_name ?? 'Maybank' }}</div>
                                    <div class="font-mono text-indigo-600 dark:text-indigo-400 text-[11px]">{{ $v->bank_account_number ?? '-' }}</div>
                                </td>
                                <td class="py-3 text-slate-500">
                                    <div>{{ $v->email ?? '-' }}</div>
                                    <div class="text-[10px] text-slate-400">{{ $v->phone ?? '-' }}</div>
                                </td>
                                <td class="py-3 text-right font-mono font-bold text-slate-900 dark:text-white">
                                    {{ $v->bills_count ?? $v->bills()->count() }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-slate-400">No vendors registered yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>
    </div>
</x-layouts.admin>
