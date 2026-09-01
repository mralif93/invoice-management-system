<x-layouts.admin header="Customers & Patients (Buyers)">
    <div class="space-y-5" x-data="{
        showExportConfirm: false,
        proceedExport() {
            this.showExportConfirm = false;
            window.location.href = '{{ route('admin.customers.export') }}';
        }
    }">
        <!-- Top Stats Row -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <x-stat-card title="Total Buyers" value="{{ $customers->count() }}" subtitle="Registered in Master Database" icon="users" iconVariant="indigo" />
            <x-stat-card title="Corporate (B2B)" value="{{ $customers->where('identification_type', 'BRN')->count() }}" subtitle="SST & BRN Verified" icon="building-2" iconVariant="emerald" />
            <x-stat-card title="Individual / Patients (B2C)" value="{{ $customers->whereIn('identification_type', ['NRIC', 'PASSPORT'])->count() }}" subtitle="Personal Tax Relief Eligible" icon="user-check" iconVariant="amber" />
        </div>

        <!-- Export Customers Confirmation Modal -->
        <x-modal 
            show="showExportConfirm" 
            title="Confirm Customers Directory Export" 
            subtitle="Master Accounts Receivable Directory Data (CSV)" 
            icon="file-spreadsheet" 
            maxWidth="md"
        >
            <div class="space-y-3">
                <div class="p-3.5 rounded-xl bg-indigo-50/70 dark:bg-indigo-950/40 border border-indigo-200/80 dark:border-indigo-800/80 space-y-2 text-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-600 dark:text-slate-400">Total Customer Records:</span>
                        <span class="font-bold font-mono text-slate-900 dark:text-white">{{ $customers->count() }} Buyers</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-600 dark:text-slate-400">Corporate SSM BRN Verified:</span>
                        <span class="font-bold font-mono text-emerald-600 dark:text-emerald-400">{{ $customers->where('identification_type', 'BRN')->count() }} Companies</span>
                    </div>
                </div>

                <p class="text-xs text-slate-600 dark:text-slate-300">
                    Are you sure you want to export the master customer directory including Malaysian TIN numbers, BRN identifiers, and contact details in CSV format?
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

        <!-- Master Registry Card -->
        <x-card title="Customer & Patient Directory" subtitle="Accounts Receivable Master Database with Malaysian Statutory Identifiers">
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
                            <th class="pb-3">Name / Entity</th>
                            <th class="pb-3">ID Type</th>
                            <th class="pb-3">SSM BRN / NRIC</th>
                            <th class="pb-3">TIN Number</th>
                            <th class="pb-3">SST Registration</th>
                            <th class="pb-3">Contact</th>
                            <th class="pb-3">Terms</th>
                            <th class="pb-3 text-right">Invoices</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80 font-medium">
                        @forelse ($customers as $c)
                            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors">
                                <td class="py-3 text-slate-900 dark:text-white">
                                    <div class="font-bold">{{ $c->name }}</div>
                                    <div class="text-[10px] text-slate-400">{{ $c->city ? ($c->city . ', ' . $c->state) : 'Malaysia' }}</div>
                                </td>
                                <td class="py-3">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $c->identification_type === 'BRN' ? 'bg-indigo-50 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300' : 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300' }}">
                                        {{ $c->identification_type }}
                                    </span>
                                </td>
                                <td class="py-3 font-mono text-slate-600 dark:text-slate-300">{{ $c->ssm_brn }}</td>
                                <td class="py-3 font-mono font-bold text-indigo-600 dark:text-indigo-400">{{ $c->tin_number ?? 'EI00000000020' }}</td>
                                <td class="py-3 font-mono text-slate-500">{{ $c->sst_number ?? 'Exempt / Non-SST' }}</td>
                                <td class="py-3 text-slate-500">
                                    <div>{{ $c->email ?? '-' }}</div>
                                    <div class="text-[10px] text-slate-400">{{ $c->phone ?? '-' }}</div>
                                </td>
                                <td class="py-3 font-mono font-bold text-slate-700 dark:text-slate-300">
                                    {{ $c->payment_terms_days == 0 ? 'Due on Receipt' : $c->payment_terms_days . ' Days' }}
                                </td>
                                <td class="py-3 text-right font-mono font-bold text-indigo-600 dark:text-indigo-400">
                                    {{ $c->invoices_count ?? $c->invoices()->count() }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-8 text-center text-slate-400">No customers registered yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>
    </div>
</x-layouts.admin>
