<x-layouts.public title="IMS Malaysia – Modern SST Invoicing & LHDN e-Invoicing Platform">
    <!-- Hero Section -->
    <section class="relative overflow-hidden pt-12 pb-20 sm:pt-20 sm:pb-28">
        <!-- Ambient Glowing Background Accents -->
        <div class="absolute top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[650px] h-[380px] bg-gradient-to-tr from-indigo-500/20 via-sky-500/10 to-emerald-500/20 blur-3xl rounded-full -z-10 pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            
            <!-- Statutory Compliance Pill -->
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-indigo-50 dark:bg-indigo-950/80 border border-indigo-200 dark:border-indigo-800 text-indigo-700 dark:text-indigo-300 text-xs font-bold uppercase tracking-wider mb-6 animate__animated animate__fadeInDown shadow-sm">
                <i data-lucide="shield-check" class="w-4 h-4 text-emerald-500"></i>
                <span>Malaysian SST & LHDN MyInvois Compliant</span>
            </div>

            <!-- Headline -->
            <h1 class="text-4xl sm:text-6xl lg:text-7xl font-extrabold tracking-tight text-slate-900 dark:text-white max-w-4xl mx-auto leading-[1.15] animate__animated animate__fadeIn">
                Fast Invoicing in <span class="bg-gradient-to-r from-indigo-600 via-indigo-500 to-emerald-500 bg-clip-text text-transparent">MYR</span> with Zero Friction
            </h1>

            <p class="mt-6 text-base sm:text-xl text-slate-600 dark:text-slate-300 max-w-2xl mx-auto leading-relaxed animate__animated animate__fadeIn animate__delay-1s">
                Create SST-compliant tax invoices in 60 seconds, match supplier bills via OCR 2-way PO matching, and switch between Standard Mode and live LHDN e-Invoicing at the flick of a switch.
            </p>

            <!-- Call to Actions -->
            <div class="mt-10 flex flex-wrap items-center justify-center gap-4 animate__animated animate__fadeInUp animate__delay-1s">
                <a href="{{ route('admin.login') }}" class="inline-flex items-center gap-2 px-7 py-3.5 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-base shadow-lg shadow-indigo-500/25 hover:shadow-indigo-500/40 transition-all transform hover:-translate-y-0.5 active:scale-95">
                    <i data-lucide="lock" class="w-5 h-5"></i>
                    <span>Admin Portal Sign In</span>
                </a>
                <a href="{{ url('/verify') }}" class="inline-flex items-center gap-2 px-7 py-3.5 rounded-2xl bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-900 dark:text-white border border-slate-200 dark:border-slate-700 font-bold text-base shadow-sm transition-all transform hover:-translate-y-0.5">
                    <i data-lucide="shield-check" class="w-5 h-5 text-emerald-500"></i>
                    <span>Verify an Invoice</span>
                </a>
            </div>

            <!-- Trust Bar / Key Badges -->
            <div class="mt-14 pt-8 border-t border-slate-200/80 dark:border-slate-800/80 flex flex-wrap items-center justify-center gap-6 sm:gap-12 text-xs font-semibold text-slate-500 dark:text-slate-400">
                <div class="flex items-center gap-2">
                    <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-500"></i>
                    <span>Sales & Service Tax Act 2018</span>
                </div>
                <div class="flex items-center gap-2">
                    <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-500"></i>
                    <span>LHDN MyInvois (UBL 2.1 JSON)</span>
                </div>
                <div class="flex items-center gap-2">
                    <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-500"></i>
                    <span>7-Year Statutory Document Retention</span>
                </div>
                <div class="flex items-center gap-2">
                    <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-500"></i>
                    <span>DuitNow Dynamic QR & FPX Ready</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Key Pillars Section -->
    <section class="py-16 bg-white dark:bg-slate-900 border-y border-slate-200/80 dark:border-slate-800/80 transition-colors">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-xs font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400 mb-2">Designed for Malaysian Business Operations</h2>
                <p class="text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight">Everything you need to run AR, AP, and Tax Compliance</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Pillar 1: AR Billing -->
                <div class="p-7 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/80 dark:border-slate-700/80 hover:shadow-lg transition-all group">
                    <div class="w-12 h-12 rounded-xl bg-indigo-500 text-white flex items-center justify-center mb-5 shadow-md shadow-indigo-500/20 group-hover:scale-110 transition-transform">
                        <i data-lucide="zap" class="w-6 h-6"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white">Fast 60s AR Invoicing</h3>
                    <p class="mt-3 text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                        Instant line-item calculations for 0%, 6%, and 8% SST. Automatically embeds DuitNow dynamic QR codes and provides 1-click WhatsApp customer delivery.
                    </p>
                </div>

                <!-- Pillar 2: AP Bill Tracking -->
                <div class="p-7 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/80 dark:border-slate-700/80 hover:shadow-lg transition-all group">
                    <div class="w-12 h-12 rounded-xl bg-amber-500 text-white flex items-center justify-center mb-5 shadow-md shadow-amber-500/20 group-hover:scale-110 transition-transform">
                        <i data-lucide="receipt" class="w-6 h-6"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white">2-Way PO Matching (AP)</h3>
                    <p class="mt-3 text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                        Split-screen PDF bill viewer with OCR auto-fill, duplicate bill prevention, $\pm\text{MYR }5.00$ tolerance variance detection, and domestic bank batch payout exports.
                    </p>
                </div>

                <!-- Pillar 3: Modular e-Invoicing -->
                <div class="p-7 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/80 dark:border-slate-700/80 hover:shadow-lg transition-all group">
                    <div class="w-12 h-12 rounded-xl bg-emerald-500 text-white flex items-center justify-center mb-5 shadow-md shadow-emerald-500/20 group-hover:scale-110 transition-transform">
                        <i data-lucide="toggle-right" class="w-6 h-6"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white">Modular e-Invoicing Toggle</h3>
                    <p class="mt-3 text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                        Run uninterrupted in Standard Mode (`OFF`) for daily operations, or toggle to Compliance Mode (`ON`) for cryptographic X.509 signing and live LHDN validation.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- SST-02 & Banking Highlights -->
    <section class="py-16 bg-slate-50 dark:bg-slate-950 transition-colors">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="p-8 sm:p-12 rounded-3xl bg-gradient-to-br from-slate-900 to-indigo-950 text-white shadow-xl flex flex-col md:flex-row items-center justify-between gap-8">
                <div class="space-y-3 max-w-xl">
                    <x-badge variant="indigo" class="bg-indigo-500/20 text-indigo-300 border-indigo-400/30">Tax & Banking Integration</x-badge>
                    <h3 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Ready for Bi-monthly JKDM SST-02 Filing & Multi-Bank Payouts</h3>
                    <p class="text-sm text-slate-300">
                        Export SST-02 tax return reports directly into Excel (.xlsx) and generate standard IBG/DuitNow batch payment files for Maybank, CIMB, Public Bank, and RHB.
                    </p>
                </div>
                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('admin.login') }}" class="px-6 py-3.5 rounded-xl bg-white hover:bg-slate-100 text-slate-900 font-bold text-sm shadow transition-all text-center">
                        Launch Admin Portal
                    </a>
                </div>
            </div>
        </div>
    </section>
</x-layouts.public>
