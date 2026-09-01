<x-layouts.admin header="Customer Invoices (AR)">
    <div class="space-y-5" x-data="{
        search: '',
        statusFilter: 'all',
        modeFilter: 'all'
    }">
        <!-- Top Stats Row -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <x-stat-card title="Total Invoiced (AR)" value="MYR {{ number_format($totalInvoiced, 2) }}" subtitle="All Time Issued" icon="file-text" iconVariant="indigo" />
            <x-stat-card title="Total Paid" value="MYR {{ number_format($totalPaid, 2) }}" subtitle="Collected Successfully" icon="check-circle-2" iconVariant="emerald" />
            <x-stat-card title="Outstanding AR" value="MYR {{ number_format($totalOutstanding, 2) }}" subtitle="{{ $overdueCount }} Overdue (>30 Days)" icon="clock" iconVariant="amber" />
            <x-stat-card title="LHDN e-Invoices" value="{{ $lhdnCount }}" subtitle="Validated UUID Clearance" icon="shield-check" iconVariant="indigo" />
        </div>

        <!-- Main Invoices Card Table -->
        <x-card title="All Customer Invoices" subtitle="Accounts Receivable Registry & Tax Invoices">
            <x-slot:action>
                <a href="{{ route('admin.invoices.create') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-xs transition-colors">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                    <span>New Invoice</span>
                </a>
            </x-slot:action>

            <!-- Filters Bar -->
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 mb-4">
                <div class="relative flex-1 max-w-sm">
                    <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-2.5"></i>
                    <input type="text" x-model="search" placeholder="Search invoice #, customer name, TIN..." class="w-full text-xs rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 pl-9 pr-3 py-2 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition-colors">
                </div>

                <div class="flex items-center gap-2">
                    <select x-model="statusFilter" class="text-xs rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white px-3 py-2">
                        <option value="all">All Statuses</option>
                        <option value="issued">Issued / Unpaid</option>
                        <option value="paid">Paid</option>
                        <option value="draft">Draft</option>
                    </select>

                    <select x-model="modeFilter" class="text-xs rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white px-3 py-2">
                        <option value="all">All Modes</option>
                        <option value="production">LHDN Live</option>
                        <option value="sandbox">Sandbox</option>
                        <option value="off">Standard</option>
                    </select>
                </div>
            </div>

            <!-- Invoices Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-800 text-slate-400 font-semibold uppercase text-[10px] tracking-wider">
                            <th class="pb-3">Invoice #</th>
                            <th class="pb-3">Customer / Buyer</th>
                            <th class="pb-3">Issue Date</th>
                            <th class="pb-3">Due Date</th>
                            <th class="pb-3 text-right">Tax (MYR)</th>
                            <th class="pb-3 text-right">Total (MYR)</th>
                            <th class="pb-3 text-center">e-Invois</th>
                            <th class="pb-3 text-center">Status</th>
                            <th class="pb-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80 font-medium">
                        @forelse ($invoices as $inv)
                            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors">
                                <td class="py-3 font-mono font-bold text-indigo-600 dark:text-indigo-400">
                                    {{ $inv->invoice_number }}
                                    @if ($inv->po_number)
                                        <span class="block text-[10px] text-slate-400 font-normal">PO: {{ $inv->po_number }}</span>
                                    @endif
                                </td>
                                <td class="py-3 text-slate-900 dark:text-white">
                                    <div class="font-bold truncate max-w-[200px]">{{ $inv->customer->name ?? 'Walk-in Customer' }}</div>
                                    <div class="text-[10px] text-slate-400 font-mono">{{ $inv->customer->tin_number ?? 'EI00000000020' }}</div>
                                </td>
                                <td class="py-3 text-slate-500">{{ $inv->issue_date->format('d M Y') }}</td>
                                <td class="py-3 {{ $inv->due_date < now() && $inv->status !== 'paid' ? 'text-rose-600 font-bold' : 'text-slate-500' }}">
                                    {{ $inv->due_date->format('d M Y') }}
                                </td>
                                <td class="py-3 text-right font-mono text-slate-500">{{ number_format($inv->tax_total, 2) }}</td>
                                <td class="py-3 text-right font-mono font-bold text-slate-900 dark:text-white">{{ number_format($inv->grand_total, 2) }}</td>
                                <td class="py-3 text-center">
                                    @if ($inv->einvoice_mode === 'production')
                                        <x-badge variant="emerald" size="sm" pulse>LHDN Live</x-badge>
                                    @elseif ($inv->einvoice_mode === 'sandbox')
                                        <x-badge variant="amber" size="sm">Sandbox</x-badge>
                                    @else
                                        <x-badge variant="slate" size="sm">Standard</x-badge>
                                    @endif
                                </td>
                                <td class="py-3 text-center">
                                    @if ($inv->status === 'paid')
                                        <x-badge variant="emerald" size="sm">Paid</x-badge>
                                    @elseif ($inv->due_date < now())
                                        <x-badge variant="danger" size="sm">Overdue</x-badge>
                                    @else
                                        <x-badge variant="amber" size="sm">Issued</x-badge>
                                    @endif
                                </td>
                                <td class="py-3 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <button class="p-1.5 rounded-lg text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors" title="Quick Share">
                                            <i data-lucide="share-2" class="w-3.5 h-3.5"></i>
                                        </button>
                                        <button class="p-1.5 rounded-lg text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors" title="Download PDF" onclick="window.print()">
                                            <i data-lucide="download" class="w-3.5 h-3.5"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-8 text-center text-slate-400">No invoices recorded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>
    </div>
</x-layouts.admin>
