<x-layouts.admin header="Supplier Bill Ingestion & 2-Way Match (AP)">
    <div x-data="{
        vendor: '1',
        poNumber: 'PO-2026-0412',
        billNumber: 'SUPP-INV-8834',
        billDate: '{{ date('Y-m-d') }}',
        poSubtotal: 4200.00,
        billSubtotal: 4200.00,
        toleranceLimit: 5.00,
        approved: false,
        fileUploaded: true,
        fileName: 'supplier_bill_inv8834.pdf',

        get variance() {
            return Math.abs((parseFloat(this.billSubtotal) || 0) - (parseFloat(this.poSubtotal) || 0));
        },

        get isMatch() {
            return this.variance <= this.toleranceLimit;
        },

        formatMoney(amount) {
            return (amount || 0).toLocaleString('en-MY', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },

        handleFileUpload(event) {
            const file = event.target.files[0];
            if (file) {
                this.fileName = file.name;
                this.fileUploaded = true;
            }
        }
    }">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <!-- Left Side: Interactive Upload & Split-Screen Bill PDF Viewer -->
            <div class="space-y-4">
                <x-card title="Uploaded Supplier Document" subtitle="Scanned image or digital PDF invoice">
                    
                    <!-- File Drag & Drop Dropzone -->
                    <div class="mb-4">
                        <label class="flex flex-col items-center justify-center w-full h-28 border-2 border-dashed border-slate-300 dark:border-slate-700 rounded-xl cursor-pointer bg-slate-50 dark:bg-slate-900/50 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors group">
                            <div class="flex flex-col items-center justify-center pt-3 pb-3">
                                <i data-lucide="upload-cloud" class="w-6 h-6 text-slate-400 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors"></i>
                                <p class="mt-1.5 text-xs text-slate-600 dark:text-slate-300"><span class="font-bold text-indigo-600 dark:text-indigo-400">Click to upload bill</span> or drag and drop</p>
                                <p class="text-[10px] text-slate-400">PDF, PNG, JPG or TIFF (Auto OCR Extraction)</p>
                            </div>
                            <input type="file" class="hidden" @change="handleFileUpload($event)" accept=".pdf,.png,.jpg,.jpeg">
                        </label>
                    </div>

                    <!-- PDF Viewer Container -->
                    <div class="rounded-xl bg-slate-900 border border-slate-800 flex flex-col overflow-hidden">
                        <div class="px-4 py-2 bg-slate-800 text-slate-300 text-xs flex items-center justify-between border-b border-slate-700">
                            <div class="flex items-center gap-2 min-w-0">
                                <i data-lucide="file-text" class="w-4 h-4 text-indigo-400 flex-shrink-0"></i>
                                <span class="font-mono font-semibold truncate" x-text="fileName"></span>
                            </div>
                            <div class="flex items-center gap-2 flex-shrink-0">
                                <span class="text-[10px] text-slate-400">Page 1/1</span>
                            </div>
                        </div>

                        <!-- Mock Scanned Document Representation -->
                        <div class="bg-slate-950 p-6 flex flex-col items-center justify-center text-center min-h-[380px]">
                            <div class="w-full max-w-sm p-5 bg-white text-slate-900 rounded-lg shadow-lg text-left text-xs font-mono space-y-3">
                                <div class="border-b pb-2 flex justify-between items-center">
                                    <span class="font-bold text-indigo-900">TEKNO LOGISTIK CLOUD SERVICES</span>
                                    <span class="text-[10px] px-1.5 py-0.5 bg-slate-100 rounded font-sans font-bold">TAX INVOICE</span>
                                </div>
                                <div class="space-y-0.5 text-[11px] text-slate-600">
                                    <p>SSM: 201501023456 | SST: W10-1808-11000077</p>
                                    <p>Bill To: Nexa Digital Sdn. Bhd.</p>
                                    <p>Date: <span x-text="billDate"></span></p>
                                    <p>Bill No: <span class="font-bold text-slate-900" x-text="billNumber"></span></p>
                                    <p>PO Ref: <span class="font-bold text-indigo-600" x-text="poNumber"></span></p>
                                </div>
                                <div class="border-t border-b py-2 space-y-1 text-[11px]">
                                    <div class="flex justify-between">
                                        <span>Cloud Logistics & Server Hosting</span>
                                        <span class="font-bold">MYR 4,200.00</span>
                                    </div>
                                    <div class="flex justify-between text-[10px] text-slate-500">
                                        <span>SST (8%)</span>
                                        <span>MYR 336.00</span>
                                    </div>
                                </div>
                                <div class="flex justify-between font-bold pt-1 text-sm text-slate-900">
                                    <span>TOTAL PAYABLE:</span>
                                    <span class="text-indigo-600">MYR 4,536.00</span>
                                </div>
                            </div>
                            <span class="text-[11px] text-slate-500 mt-3 flex items-center gap-1">
                                <i data-lucide="sparkles" class="w-3.5 h-3.5 text-indigo-400"></i>
                                Optical Character Recognition (OCR) verified & extracted
                            </span>
                        </div>
                    </div>
                </x-card>
            </div>

            <!-- Right Side: Extracted Data & 2-Way Match Verification -->
            <div class="space-y-4">
                
                <!-- Matching Status Banner -->
                <div class="p-4 rounded-xl border flex items-center justify-between shadow-xs"
                     :class="isMatch ? 'bg-emerald-50 dark:bg-emerald-950/50 border-emerald-200 dark:border-emerald-800' : 'bg-rose-50 dark:bg-rose-950/50 border-rose-200 dark:border-rose-800'">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg flex items-center justify-center font-bold text-white flex-shrink-0"
                             :class="isMatch ? 'bg-emerald-600' : 'bg-rose-600'">
                            <i :data-lucide="isMatch ? 'check-check' : 'alert-circle'" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-xs sm:text-sm" :class="isMatch ? 'text-emerald-950 dark:text-emerald-200' : 'text-rose-950 dark:text-rose-200'" x-text="isMatch ? '2-Way PO Match: Perfect Match' : '2-Way Match: Variance Detected'"></h3>
                            <p class="text-[11px] mt-0.5" :class="isMatch ? 'text-emerald-700 dark:text-emerald-400' : 'text-rose-700 dark:text-rose-400'" x-text="isMatch ? 'Variance is MYR 0.00 (within ± MYR 5.00 tolerance).' : 'Variance exceeds tolerance threshold. Requires manager approval.'"></p>
                        </div>
                    </div>
                    <span class="inline-flex items-center font-bold tracking-tight rounded-full border px-2.5 py-0.5 text-[11px] gap-1"
                          :class="isMatch ? 'bg-emerald-50 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800' : 'bg-rose-50 dark:bg-rose-950/80 text-rose-700 dark:text-rose-300 border-rose-200 dark:border-rose-800'">
                        <span class="w-1.5 h-1.5 rounded-full animate-pulse" :class="isMatch ? 'bg-emerald-500' : 'bg-rose-500'"></span>
                        <span x-text="isMatch ? 'Auto-Approvable' : 'Discrepancy'"></span>
                    </span>
                </div>

                <!-- Comparison Table Card -->
                <x-card title="2-Way Comparison" subtitle="Supplier Bill vs. Approved Purchase Order">
                    <div class="space-y-3.5 text-xs">
                        
                        <!-- Side-by-Side Comparison Box -->
                        <div class="grid grid-cols-2 gap-3 p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/80">
                            <div>
                                <span class="text-slate-400 uppercase font-bold text-[10px]">Purchase Order (PO)</span>
                                <p class="font-bold text-slate-900 dark:text-white mt-0.5" x-text="poNumber"></p>
                                <p class="text-slate-500 font-mono text-sm font-bold mt-1">MYR <span x-text="formatMoney(poSubtotal)"></span></p>
                            </div>
                            <div>
                                <span class="text-slate-400 uppercase font-bold text-[10px]">Supplier Bill Extracted</span>
                                <p class="font-bold text-slate-900 dark:text-white mt-0.5" x-text="billNumber"></p>
                                <p class="text-slate-500 font-mono text-sm font-bold mt-1">MYR <span x-text="formatMoney(billSubtotal)"></span></p>
                            </div>
                        </div>

                        <!-- Duplicate Check Indicator -->
                        <div class="p-2.5 rounded-lg bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 flex items-center justify-between text-[11px]">
                            <div class="flex items-center gap-2">
                                <i data-lucide="shield" class="w-4 h-4 text-emerald-500"></i>
                                <span class="font-semibold text-slate-700 dark:text-slate-300">Duplicate Check:</span>
                            </div>
                            <span class="text-emerald-600 dark:text-emerald-400 font-bold">No duplicate bill detected (Unique)</span>
                        </div>

                        <!-- Editable Extracted Metadata -->
                        <div class="grid grid-cols-2 gap-3 pt-2">
                            <x-input label="Supplier Bill No." x-model="billNumber" />
                            <x-input label="PO Reference" x-model="poNumber" />
                            <x-input label="Bill Amount (MYR)" type="number" step="0.01" x-model="billSubtotal" />
                            <x-input label="PO Amount (MYR)" type="number" step="0.01" x-model="poSubtotal" />
                        </div>

                        <!-- Approval Actions -->
                        <div class="pt-3 border-t border-slate-100 dark:border-slate-800 space-y-2.5">
                            <div class="flex items-center gap-2.5">
                                <x-button @click="approved = true" variant="success" icon="check" class="flex-1 py-2.5">
                                    Approve for Payout
                                </x-button>
                                <x-button variant="danger" icon="x" class="flex-1 py-2.5">
                                    Reject / Dispute
                                </x-button>
                            </div>

                            <div x-show="approved" x-transition class="p-3 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-xs font-semibold text-center">
                                ✓ Bill approved! Successfully queued for Multi-Bank Batch Payout export.
                            </div>
                        </div>
                    </div>
                </x-card>
            </div>
        </div>
    </div>
</x-layouts.admin>
