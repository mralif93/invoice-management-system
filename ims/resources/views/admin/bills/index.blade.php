<x-layouts.admin header="Supplier Bills (AP)">
    <div class="space-y-5">
        <!-- Top Stats Row -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <x-stat-card title="Total AP Bills" value="MYR {{ number_format($bills->sum('grand_total'), 2) }}" subtitle="{{ $bills->count() }} Registered Bills" icon="receipt" iconVariant="indigo" />
            <x-stat-card title="2-Way Matched" value="{{ $bills->where('match_status', 'matched')->count() }}" subtitle="PO & Bill Total Aligned" icon="check-check" iconVariant="emerald" />
            <x-stat-card title="Pending Approval" value="MYR {{ number_format($bills->where('approval_status', 'pending_approval')->sum('grand_total'), 2) }}" subtitle="> RM5,000 Threshold" icon="clock" iconVariant="amber" />
            <x-stat-card title="Input Tax (SST Claim)" value="MYR {{ number_format($bills->sum('tax_total'), 2) }}" subtitle="Eligible for SST-02 Deduction" icon="file-spreadsheet" iconVariant="emerald" />
        </div>

        <!-- Main Bills Card Table -->
        <x-card title="Supplier Invoices Registry (Accounts Payable)" subtitle="2-Way Matching and Multi-Level Manager Approvals">
            <x-slot:action>
                <a href="{{ route('admin.bills.upload') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-xs transition-colors">
                    <i data-lucide="upload-cloud" class="w-3.5 h-3.5"></i>
                    <span>Upload Bill (OCR)</span>
                </a>
            </x-slot:action>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-800 text-slate-400 font-semibold uppercase text-[10px] tracking-wider">
                            <th class="pb-3">Bill #</th>
                            <th class="pb-3">Vendor / Supplier</th>
                            <th class="pb-3">PO Reference</th>
                            <th class="pb-3">Bill Date</th>
                            <th class="pb-3">Due Date</th>
                            <th class="pb-3 text-right">Tax (MYR)</th>
                            <th class="pb-3 text-right">Amount (MYR)</th>
                            <th class="pb-3 text-center">2-Way Match</th>
                            <th class="pb-3 text-center">Approval</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80 font-medium">
                        @forelse ($bills as $bill)
                            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors">
                                <td class="py-3 font-mono font-bold text-amber-600 dark:text-amber-400">{{ $bill->bill_number }}</td>
                                <td class="py-3 text-slate-900 dark:text-white font-bold">{{ $bill->vendor->name ?? 'Unknown Vendor' }}</td>
                                <td class="py-3 font-mono text-slate-500">{{ $bill->po_number ?? 'Direct Invoice' }}</td>
                                <td class="py-3 text-slate-500">{{ $bill->bill_date->format('d M Y') }}</td>
                                <td class="py-3 text-slate-500">{{ $bill->due_date->format('d M Y') }}</td>
                                <td class="py-3 text-right font-mono text-slate-500">{{ number_format($bill->tax_total, 2) }}</td>
                                <td class="py-3 text-right font-mono font-bold text-slate-900 dark:text-white">{{ number_format($bill->grand_total, 2) }}</td>
                                <td class="py-3 text-center">
                                    @if ($bill->match_status === 'matched')
                                        <x-badge variant="emerald" size="sm">Matched (0% Var)</x-badge>
                                    @else
                                        <x-badge variant="danger" size="sm">Variance Flagged</x-badge>
                                    @endif
                                </td>
                                <td class="py-3 text-center">
                                    @if ($bill->approval_status === 'approved')
                                        <x-badge variant="emerald" size="sm">Approved</x-badge>
                                    @else
                                        <x-badge variant="amber" size="sm" pulse>Pending Approval</x-badge>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-8 text-center text-slate-400">No supplier bills recorded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>
    </div>
</x-layouts.admin>
