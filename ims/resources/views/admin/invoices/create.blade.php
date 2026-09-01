<x-layouts.admin header="Create Tax Invoice (AR)">
    <div x-data="{
        customerId: '',
        customerData: @js($customers ?? []),
        issueDate: '{{ date('Y-m-d') }}',
        dueDate: '{{ date('Y-m-d', strtotime('+30 days')) }}',
        currency: 'MYR',
        poNumber: '',
        invoiceNo: 'INV-{{ date('Y') }}-{{ str_pad(rand(100, 999), 4, '0', STR_PAD_LEFT) }}',
        showPdfModal: false,
        
        // Starts completely empty for new invoice entry
        items: [
            { description: '', qty: 1, price: 0.00, sstRate: 8, discount: 0 }
        ],

        get selectedCustomer() {
            if (!this.customerId) return null;
            return this.customerData.find(c => String(c.id) === String(this.customerId));
        },

        addItem() {
            this.items.push({ description: '', qty: 1, price: 0.00, sstRate: 8, discount: 0 });
            this.$nextTick(() => { if (window.initLucideIcons) window.initLucideIcons(); });
        },

        removeItem(index) {
            if (this.items.length > 1) {
                this.items.splice(index, 1);
            } else {
                this.items[0] = { description: '', qty: 1, price: 0.00, sstRate: 8, discount: 0 };
            }
        },

        get subtotal() {
            return this.items.reduce((sum, item) => sum + ((parseFloat(item.qty) || 0) * (parseFloat(item.price) || 0) - (parseFloat(item.discount) || 0)), 0);
        },

        get sst8Total() {
            return this.items
                .filter(i => parseInt(i.sstRate) === 8)
                .reduce((sum, item) => sum + (((parseFloat(item.qty) || 0) * (parseFloat(item.price) || 0) - (parseFloat(item.discount) || 0)) * 0.08), 0);
        },

        get sst6Total() {
            return this.items
                .filter(i => parseInt(i.sstRate) === 6)
                .reduce((sum, item) => sum + (((parseFloat(item.qty) || 0) * (parseFloat(item.price) || 0) - (parseFloat(item.discount) || 0)) * 0.06), 0);
        },

        get totalSst() {
            return this.sst8Total + this.sst6Total;
        },

        get grandTotal() {
            return this.subtotal + this.totalSst;
        },

        formatMoney(amount) {
            return (amount || 0).toLocaleString('en-MY', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },

        openWhatsAppShare() {
            let text = `Salam/Hi! Here is your Invoice ${this.invoiceNo} for MYR ${this.formatMoney(this.grandTotal)}. Pay online via DuitNow/FPX: https://ims-malaysia.test/inv/${this.invoiceNo}`;
            window.open(`https://wa.me/?text=${encodeURIComponent(text)}`, '_blank');
        }
    }">
        <!-- Responsive Flex/Grid Layout -->
        <div class="w-full flex flex-col xl:flex-row gap-5 items-start">
            
            <!-- Left Main Form (Empty Clean Slate) -->
            <div class="w-full xl:flex-1 space-y-5 min-w-0">
                
                <!-- 1. Party & Metadata Card -->
                <x-card title="Invoice Details" subtitle="Malaysian Statutory Tax Invoice Information">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Customer Selection -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">
                                Select Customer (Buyer / Patient) <span class="text-rose-500">*</span>
                            </label>
                            <select x-model="customerId" class="w-full text-xs rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white px-3 py-2 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition-colors">
                                <option value="">-- Choose a Customer / Buyer --</option>
                                @if (isset($customers))
                                    @foreach ($customers as $c)
                                        <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->identification_type }}: {{ $c->ssm_brn }})</option>
                                    @endforeach
                                @endif
                            </select>
                            
                            <!-- Dynamic Buyer Tax Info Pill -->
                            <template x-if="selectedCustomer">
                                <div class="mt-2.5 p-2.5 rounded-lg bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/80 text-[11px] space-y-1">
                                    <div class="flex items-center gap-2 font-mono font-bold text-indigo-700 dark:text-indigo-400">
                                        <span x-text="'TIN: ' + (selectedCustomer.tin_number || 'N/A')"></span>
                                        <span>•</span>
                                        <span x-text="'SST: ' + (selectedCustomer.sst_number || 'Exempt / None')"></span>
                                    </div>
                                    <p class="text-slate-500 dark:text-slate-400 text-[10px]" x-text="selectedCustomer.address_line1 ? (selectedCustomer.address_line1 + ', ' + (selectedCustomer.city || '') + ', ' + (selectedCustomer.state || '')) : 'No address specified'"></p>
                                </div>
                            </template>

                            <template x-if="!selectedCustomer">
                                <p class="mt-2 text-[10px] text-slate-400 dark:text-slate-500 italic">Select a registered buyer or patient to automatically populate SST & TIN information.</p>
                            </template>
                        </div>

                        <!-- Dates & Numbers Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <x-input label="Issue Date" type="date" x-model="issueDate" required />
                            <x-input label="Due Date" type="date" x-model="dueDate" required />
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Invoice No.</label>
                                <input type="text" x-model="invoiceNo" class="w-full text-xs rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white px-3 py-1.5 font-mono font-bold">
                            </div>
                            <x-input label="PO Reference" placeholder="Optional PO Number" x-model="poNumber" />
                        </div>
                    </div>
                </x-card>

                <!-- 2. Line Items Card with Responsive Desktop Table & Mobile Cards -->
                <x-card title="Line Items & Tax Code Breakdown" subtitle="Calculates SST 0%, 6%, and 8% with precision">
                    
                    <!-- Desktop / Tablet Table View (md and up) -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead>
                                <tr class="border-b border-slate-200 dark:border-slate-800 text-slate-400 font-semibold uppercase text-[10px] tracking-wider">
                                    <th class="pb-2.5 w-8 text-center">#</th>
                                    <th class="pb-2.5 min-w-[240px]">Item Description</th>
                                    <th class="pb-2.5 w-16 text-center">Qty</th>
                                    <th class="pb-2.5 w-28 text-right">Price (MYR)</th>
                                    <th class="pb-2.5 w-28 text-center">Tax Code</th>
                                    <th class="pb-2.5 w-28 text-right">Total (MYR)</th>
                                    <th class="pb-2.5 w-10 text-center"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80 font-medium">
                                <template x-for="(item, index) in items" :key="index">
                                    <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30 transition-colors">
                                        <td class="py-2.5 text-center font-mono font-bold text-slate-400 text-[11px]" x-text="String(index + 1).padStart(2, '0')"></td>
                                        <td class="py-2.5 pr-2">
                                            <input type="text" x-model="item.description" placeholder="Enter item description or service..." class="w-full text-xs rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-1.5 text-slate-900 dark:text-white placeholder:text-slate-400 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition-colors">
                                        </td>
                                        <td class="py-2.5 px-1">
                                            <input type="number" min="1" x-model="item.qty" class="w-full text-xs rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 py-1.5 text-center text-slate-900 dark:text-white font-mono focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600">
                                        </td>
                                        <td class="py-2.5 px-1">
                                            <input type="number" step="0.01" min="0" x-model="item.price" placeholder="0.00" class="w-full text-xs rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 py-1.5 text-right font-mono text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600">
                                        </td>
                                        <td class="py-2.5 px-1">
                                            <select x-model="item.sstRate" class="w-full text-xs rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 py-1.5 text-center text-slate-900 dark:text-white font-bold focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600">
                                                <option value="8">8% (Std SST)</option>
                                                <option value="6">6% (F&B / Telco)</option>
                                                <option value="0">0% (Exempt)</option>
                                            </select>
                                        </td>
                                        <td class="py-2.5 pl-2 text-right font-mono font-bold text-slate-900 dark:text-white text-xs">
                                            <span x-text="formatMoney((item.qty * item.price) + ((item.qty * item.price) * (item.sstRate / 100)))"></span>
                                        </td>
                                        <td class="py-2.5 text-center">
                                            <button type="button" @click="removeItem(index)" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition-colors" title="Delete Row">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Stacked Card View (Screen width < 768px) -->
                    <div class="md:hidden space-y-3">
                        <template x-for="(item, index) in items" :key="index">
                            <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/80 space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="font-mono font-bold text-indigo-600 dark:text-indigo-400 text-xs" x-text="'Item #' + (index + 1)"></span>
                                    <button type="button" @click="removeItem(index)" class="text-rose-500 hover:text-rose-700 p-1">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </div>
                                <input type="text" x-model="item.description" placeholder="Enter item description..." class="w-full text-xs rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2 text-slate-900 dark:text-white">
                                <div class="grid grid-cols-3 gap-2">
                                    <div>
                                        <label class="text-[10px] text-slate-400 block mb-0.5">Qty</label>
                                        <input type="number" x-model="item.qty" class="w-full text-xs rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 py-1.5 text-center font-mono">
                                    </div>
                                    <div>
                                        <label class="text-[10px] text-slate-400 block mb-0.5">Price (MYR)</label>
                                        <input type="number" step="0.01" x-model="item.price" placeholder="0.00" class="w-full text-xs rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 py-1.5 text-right font-mono">
                                    </div>
                                    <div>
                                        <label class="text-[10px] text-slate-400 block mb-0.5">Tax</label>
                                        <select x-model="item.sstRate" class="w-full text-xs rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 py-1.5 text-center font-bold">
                                            <option value="8">8%</option>
                                            <option value="6">6%</option>
                                            <option value="0">0%</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between pt-1 border-t border-slate-200/60 dark:border-slate-700/60 text-xs font-bold">
                                    <span class="text-slate-500">Row Total:</span>
                                    <span class="font-mono text-indigo-600 dark:text-indigo-400" x-text="'MYR ' + formatMoney((item.qty * item.price) + ((item.qty * item.price) * (item.sstRate / 100)))"></span>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Add Row Button -->
                    <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800">
                        <button type="button" @click="addItem()" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-indigo-50 dark:bg-indigo-950/60 hover:bg-indigo-100 dark:hover:bg-indigo-900/60 text-xs font-bold text-indigo-600 dark:text-indigo-400 transition-colors">
                            <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                            <span>Add Another Line Item</span>
                        </button>
                    </div>
                </x-card>
            </div>

            <!-- Right Sidebar: Financial Summary & Action Toolbar (Sticky on Desktop) -->
            <div class="w-full xl:w-80 2xl:w-96 flex-shrink-0 space-y-5 xl:sticky xl:top-20">
                
                <!-- Financial Calculation Card -->
                <x-card title="Invoice Summary" subtitle="Malaysian Ringgit (MYR)">
                    <div class="space-y-3 text-xs">
                        <div class="flex items-center justify-between text-slate-600 dark:text-slate-400">
                            <span>Subtotal (Excl. Tax):</span>
                            <span class="font-mono font-bold text-slate-900 dark:text-white" x-text="'MYR ' + formatMoney(subtotal)"></span>
                        </div>
                        <div class="flex items-center justify-between text-slate-600 dark:text-slate-400">
                            <span>Service Tax (8% Std):</span>
                            <span class="font-mono font-bold text-slate-900 dark:text-white" x-text="'MYR ' + formatMoney(sst8Total)"></span>
                        </div>
                        <div class="flex items-center justify-between text-slate-600 dark:text-slate-400" x-show="sst6Total > 0">
                            <span>Service Tax (6% Specific):</span>
                            <span class="font-mono font-bold text-slate-900 dark:text-white" x-text="'MYR ' + formatMoney(sst6Total)"></span>
                        </div>

                        <div class="border-t border-slate-200 dark:border-slate-800 pt-3 flex items-center justify-between">
                            <span class="text-xs font-extrabold text-slate-900 dark:text-white">TOTAL PAYABLE:</span>
                            <span class="text-base font-mono font-extrabold text-indigo-600 dark:text-indigo-400" x-text="'MYR ' + formatMoney(grandTotal)"></span>
                        </div>
                    </div>

                    <!-- Payment Mode QR Preview Box -->
                    <div class="mt-4 p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/80 flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-indigo-600 flex-shrink-0 shadow-xs">
                            <i data-lucide="qr-code" class="w-4 h-4"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-slate-900 dark:text-white truncate" x-text="eInvoiceMode === 'off' ? 'DuitNow Dynamic QR' : 'LHDN Validation QR'"></p>
                            <p class="text-[10px] text-slate-500 truncate" x-text="eInvoiceMode === 'off' ? 'Scan to pay via bank app' : 'Validation clearance embedded'"></p>
                        </div>
                    </div>
                </x-card>

                <!-- Actions Toolbar with Dedicated Preview PDF Button -->
                <x-card title="Actions & Delivery">
                    <div class="space-y-2.5">
                        
                        <!-- 1. Preview PDF Interactive Modal Button -->
                        <x-button @click="showPdfModal = true; $nextTick(() => { if (window.initLucideIcons) window.initLucideIcons(); })" variant="outline" icon="eye" class="w-full py-2.5 border-indigo-200 dark:border-indigo-800 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-950/50">
                            Preview PDF Invoice
                        </x-button>

                        <!-- 2. Share via WhatsApp -->
                        <x-button @click="openWhatsAppShare()" variant="success" icon="share-2" class="w-full py-2.5">
                            Share via WhatsApp
                        </x-button>

                        <!-- 3. Direct Print / Export PDF -->
                        <x-button variant="primary" icon="printer" class="w-full py-2.5" onclick="window.print()">
                            Print / Export PDF
                        </x-button>

                        <!-- 4. Save Draft -->
                        <x-button variant="secondary" icon="save" class="w-full py-2">
                            Save as Draft
                        </x-button>

                        <!-- 5. Transmit to LHDN (When in compliance mode) -->
                        <div x-show="eInvoiceMode !== 'off'" class="pt-1">
                            <x-button variant="success" icon="shield-check" class="w-full py-2.5">
                                Transmit to LHDN MyInvois
                            </x-button>
                        </div>
                    </div>
                </x-card>
            </div>
        </div>

        <!-- Interactive A4 Tax Invoice PDF Live Preview Modal -->
        <div x-show="showPdfModal" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/75 backdrop-blur-xs flex items-center justify-center p-3 sm:p-6"
             style="display: none;">
            
            <div @click.away="showPdfModal = false" 
                 class="w-full max-w-4xl bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-800 flex flex-col max-h-[90vh] overflow-hidden">
                
                <!-- Modal Top Navigation Bar -->
                <div class="h-14 px-5 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between bg-slate-50 dark:bg-slate-900/90 flex-shrink-0">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-950/80 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                            <i data-lucide="file-text" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-900 dark:text-white text-xs sm:text-sm">Official Tax Invoice PDF Preview</h3>
                            <p class="text-[10px] text-slate-500" x-text="invoiceNo + ' • Live Document Rendering'"></p>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="window.print()" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-xs transition-colors">
                            <i data-lucide="printer" class="w-3.5 h-3.5"></i>
                            <span>Print</span>
                        </button>
                        <button type="button" @click="showPdfModal = false" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-800 transition-colors">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>
                </div>

                <!-- Printable Document Body (A4 Style) -->
                <div class="flex-1 overflow-y-auto p-6 sm:p-10 bg-slate-100 dark:bg-slate-950/50">
                    <div class="max-w-3xl mx-auto bg-white dark:bg-slate-900 p-8 sm:p-12 rounded-xl shadow-lg border border-slate-200/80 dark:border-slate-800 text-xs text-slate-800 dark:text-slate-200 space-y-6">
                        
                        <!-- Invoice Top Header (Issuer & Invoice Info) -->
                        <div class="flex flex-col sm:flex-row justify-between items-start gap-4 pb-6 border-b border-slate-200 dark:border-slate-800">
                            <div>
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-lg bg-indigo-600 text-white flex items-center justify-center font-bold text-xs">
                                        ND
                                    </div>
                                    <h1 class="text-base font-extrabold text-slate-900 dark:text-white">Nexa Digital Sdn. Bhd.</h1>
                                </div>
                                <div class="mt-2 text-[11px] text-slate-500 dark:text-slate-400 space-y-0.5">
                                    <p>SSM BRN: 202101034567 (1434867-M)</p>
                                    <p>TIN: C25890123000 | SST No: W10-1808-32000045</p>
                                    <p>Bangsar South, 59200 Kuala Lumpur, Malaysia</p>
                                    <p>Email: billing@nexadigital.com.my | Phone: +603-2289-4500</p>
                                </div>
                            </div>

                            <div class="sm:text-right">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-indigo-50 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                                    TAX INVOICE
                                </span>
                                <h2 class="text-lg font-mono font-extrabold text-slate-900 dark:text-white mt-2" x-text="invoiceNo"></h2>
                                <div class="mt-2 text-[11px] text-slate-500 dark:text-slate-400 space-y-0.5 font-mono">
                                    <p>Date: <span class="font-bold text-slate-800 dark:text-slate-200" x-text="issueDate"></span></p>
                                    <p>Due Date: <span class="font-bold text-slate-800 dark:text-slate-200" x-text="dueDate"></span></p>
                                    <p x-show="poNumber">PO Ref: <span class="font-bold text-slate-800 dark:text-slate-200" x-text="poNumber"></span></p>
                                </div>
                            </div>
                        </div>

                        <!-- Bill To Section -->
                        <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-800">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">BILLED TO (BUYER):</span>
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white" x-text="selectedCustomer ? selectedCustomer.name : 'Unassigned Customer / Buyer'"></h3>
                            <div class="mt-1 text-[11px] text-slate-500 dark:text-slate-400 font-mono space-y-0.5">
                                <p x-show="selectedCustomer && selectedCustomer.ssm_brn" x-text="'ID / BRN: ' + selectedCustomer.ssm_brn"></p>
                                <p x-show="selectedCustomer && selectedCustomer.tin_number" x-text="'TIN: ' + selectedCustomer.tin_number + (selectedCustomer.sst_number ? ' | SST: ' + selectedCustomer.sst_number : '')"></p>
                                <p x-show="selectedCustomer && selectedCustomer.address_line1" x-text="selectedCustomer.address_line1 + ', ' + (selectedCustomer.city || '') + ' ' + (selectedCustomer.state || '')"></p>
                            </div>
                        </div>

                        <!-- Line Items Table -->
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs">
                                <thead>
                                    <tr class="border-b-2 border-slate-200 dark:border-slate-800 text-slate-500 uppercase text-[10px] font-bold">
                                        <th class="py-2 text-center w-8">#</th>
                                        <th class="py-2">Item Description</th>
                                        <th class="py-2 text-center w-12">Qty</th>
                                        <th class="py-2 text-right w-24">Price (MYR)</th>
                                        <th class="py-2 text-center w-16">Tax</th>
                                        <th class="py-2 text-right w-28">Amount (MYR)</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                    <template x-for="(item, index) in items" :key="index">
                                        <tr>
                                            <td class="py-2.5 text-center font-mono text-slate-400" x-text="index + 1"></td>
                                            <td class="py-2.5 font-medium text-slate-900 dark:text-white" x-text="item.description || 'Untitled line item'"></td>
                                            <td class="py-2.5 text-center font-mono" x-text="item.qty || 1"></td>
                                            <td class="py-2.5 text-right font-mono" x-text="formatMoney(item.price)"></td>
                                            <td class="py-2.5 text-center font-bold font-mono" x-text="item.sstRate + '%'"></td>
                                            <td class="py-2.5 text-right font-mono font-bold text-slate-900 dark:text-white" x-text="formatMoney((item.qty * item.price) + ((item.qty * item.price) * (item.sstRate / 100)))"></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>

                        <!-- Calculation Totals & Banking Footer -->
                        <div class="pt-4 border-t border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row justify-between gap-6">
                            <!-- Left: Banking Settlement & DuitNow QR -->
                            <div class="space-y-2 max-w-xs">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">PAYMENT INFORMATION:</span>
                                <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-800 text-[11px] space-y-1">
                                    <p class="font-bold text-slate-900 dark:text-white">Malayan Banking Berhad (Maybank)</p>
                                    <p class="font-mono text-indigo-600 dark:text-indigo-400 font-bold">5140-1234-8899</p>
                                    <p class="text-slate-500">Account Holder: Nexa Digital Sdn. Bhd.</p>
                                    <p class="text-slate-500">DuitNow ID: 202101034567</p>
                                </div>
                            </div>

                            <!-- Right: Tax Breakdown Summary -->
                            <div class="sm:w-64 space-y-2 text-xs">
                                <div class="flex justify-between text-slate-600 dark:text-slate-400">
                                    <span>Subtotal (Excl. Tax):</span>
                                    <span class="font-mono font-bold text-slate-900 dark:text-white" x-text="'MYR ' + formatMoney(subtotal)"></span>
                                </div>
                                <div class="flex justify-between text-slate-600 dark:text-slate-400">
                                    <span>Service Tax (8%):</span>
                                    <span class="font-mono font-bold text-slate-900 dark:text-white" x-text="'MYR ' + formatMoney(sst8Total)"></span>
                                </div>
                                <div class="flex justify-between text-slate-600 dark:text-slate-400" x-show="sst6Total > 0">
                                    <span>Service Tax (6%):</span>
                                    <span class="font-mono font-bold text-slate-900 dark:text-white" x-text="'MYR ' + formatMoney(sst6Total)"></span>
                                </div>
                                <div class="border-t-2 border-slate-900 dark:border-slate-200 pt-2 flex justify-between">
                                    <span class="font-extrabold text-slate-900 dark:text-white">TOTAL PAYABLE:</span>
                                    <span class="font-mono text-base font-extrabold text-indigo-600 dark:text-indigo-400" x-text="'MYR ' + formatMoney(grandTotal)"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Footer Legal Notice -->
                        <div class="pt-6 border-t border-slate-100 dark:border-slate-800 text-[10px] text-slate-400 text-center space-y-0.5">
                            <p>This is a computer-generated statutory Tax Invoice compliant with Royal Malaysian Customs Department (JKDM).</p>
                            <p>For electronic verification and inquiries, contact billing@nexadigital.com.my.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
