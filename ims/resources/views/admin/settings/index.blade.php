<x-layouts.admin header="Settings & e-Invoicing Compliance">
    <div class="space-y-5" x-data="{
        mode: localStorage.getItem('eInvoiceMode') || 'off',
        saveSuccess: false,
        updateMode(newMode) {
            this.mode = newMode;
            this.setEInvoiceMode(newMode);
            this.saveSuccess = true;
            setTimeout(() => { this.saveSuccess = false; }, 3000);
        }
    }">
        <div x-show="saveSuccess" x-transition class="p-3.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-xs flex items-center gap-2.5 font-bold shadow-xs">
            <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600"></i>
            <span>System settings & operating mode updated successfully.</span>
        </div>

        <!-- 1. Operating Mode Configuration Card -->
        <x-card title="e-Invoicing Engine Mode" subtitle="Controls Malaysian LHDN MyInvois Clearance and QR code generation">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Mode 1: Standard -->
                <div @click="updateMode('off')" 
                     class="p-4 rounded-xl border-2 cursor-pointer transition-all space-y-2 relative"
                     :class="mode === 'off' ? 'border-indigo-600 bg-indigo-50/50 dark:bg-indigo-950/30' : 'border-slate-200 dark:border-slate-800 hover:border-slate-300'">
                    <div class="flex items-center justify-between">
                        <span class="font-extrabold text-xs text-slate-900 dark:text-white uppercase tracking-wider">Standard Mode</span>
                        <div class="w-4 h-4 rounded-full border-2 flex items-center justify-center" :class="mode === 'off' ? 'border-indigo-600 bg-indigo-600' : 'border-slate-300'">
                            <div x-show="mode === 'off'" class="w-1.5 h-1.5 rounded-full bg-white"></div>
                        </div>
                    </div>
                    <p class="text-[11px] text-slate-500">Instant offline invoicing. Generates DuitNow Dynamic QR codes with Maybank/CIMB details.</p>
                </div>

                <!-- Mode 2: Sandbox -->
                <div @click="updateMode('sandbox')" 
                     class="p-4 rounded-xl border-2 cursor-pointer transition-all space-y-2 relative"
                     :class="mode === 'sandbox' ? 'border-amber-500 bg-amber-50/50 dark:bg-amber-950/30' : 'border-slate-200 dark:border-slate-800 hover:border-slate-300'">
                    <div class="flex items-center justify-between">
                        <span class="font-extrabold text-xs text-amber-700 dark:text-amber-400 uppercase tracking-wider">LHDN Sandbox</span>
                        <div class="w-4 h-4 rounded-full border-2 flex items-center justify-center" :class="mode === 'sandbox' ? 'border-amber-500 bg-amber-500' : 'border-slate-300'">
                            <div x-show="mode === 'sandbox'" class="w-1.5 h-1.5 rounded-full bg-white"></div>
                        </div>
                    </div>
                    <p class="text-[11px] text-slate-500">Connects to LHDN Preprod Test environment for schema validation and mock clearances.</p>
                </div>

                <!-- Mode 3: Production -->
                <div @click="updateMode('production')" 
                     class="p-4 rounded-xl border-2 cursor-pointer transition-all space-y-2 relative"
                     :class="mode === 'production' ? 'border-emerald-600 bg-emerald-50/50 dark:bg-emerald-950/30' : 'border-slate-200 dark:border-slate-800 hover:border-slate-300'">
                    <div class="flex items-center justify-between">
                        <span class="font-extrabold text-xs text-emerald-700 dark:text-emerald-400 uppercase tracking-wider">LHDN Live (Official)</span>
                        <div class="w-4 h-4 rounded-full border-2 flex items-center justify-center" :class="mode === 'production' ? 'border-emerald-600 bg-emerald-600' : 'border-slate-300'">
                            <div x-show="mode === 'production'" class="w-1.5 h-1.5 rounded-full bg-white"></div>
                        </div>
                    </div>
                    <p class="text-[11px] text-slate-500">Official statutory compliance. Transmits real-time X.509 signed e-invoices with validation QR.</p>
                </div>
            </div>
        </x-card>

        <!-- 2. Company Profile Card -->
        <x-card title="Business Issuer Profile" subtitle="Your registered Malaysian legal entity details">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-input label="Company Name" value="Nexa Digital Sdn. Bhd." />
                <x-input label="SSM Registration Number" value="202101034567 (1434867-M)" />
                <x-input label="TIN (Tax Identification Number)" value="C25890123000" />
                <x-input label="SST Registration Number" value="W10-1808-32000045" />
                <x-input label="MSIC Industry Code" value="62010 - Computer programming, consultancy" />
                <x-input label="Billing Email" value="billing@nexadigital.com.my" />
                <x-input label="Bank Settlement Account" value="Maybank (5140-1234-8899)" />
                <x-input label="DuitNow Registered ID" value="202101034567" />
            </div>

            <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 flex justify-end">
                <x-button variant="primary" icon="save">
                    Save Profile Changes
                </x-button>
            </div>
        </x-card>
    </div>
</x-layouts.admin>
