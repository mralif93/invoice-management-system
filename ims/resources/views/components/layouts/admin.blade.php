<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50 antialiased" x-data="{
    darkMode: localStorage.getItem('darkMode') === 'true',
    sidebarOpen: false,
    sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true',
    eInvoiceMode: localStorage.getItem('eInvoiceMode') || 'off',
    
    // Determine active module from current route on page load
    activeModule: '{{ request()->is("admin/invoices*", "admin/customers*") ? "ar" : (request()->is("admin/bills*", "admin/vendors*") ? "ap" : (request()->is("admin/banking*") ? "banking" : (request()->is("admin/reports*") ? "tax" : "ar"))) }}',

    toggleModule(name) {
        this.activeModule = this.activeModule === name ? null : name;
    },

    toggleDarkMode() {
        this.darkMode = !this.darkMode;
        localStorage.setItem('darkMode', this.darkMode);
        if (this.darkMode) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
        this.$nextTick(() => {
            if (window.initLucideIcons) window.initLucideIcons();
        });
    },
    toggleSidebarCollapse() {
        this.sidebarCollapsed = !this.sidebarCollapsed;
        localStorage.setItem('sidebarCollapsed', this.sidebarCollapsed);
    },
    setEInvoiceMode(mode) {
        this.eInvoiceMode = mode;
        localStorage.setItem('eInvoiceMode', mode);
    }
}" x-init="if (darkMode) document.documentElement.classList.add('dark');">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Dashboard' }} | IMS Malaysia Admin</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles')
</head>
<body class="h-full flex overflow-hidden font-sans text-slate-800 dark:text-slate-200 bg-slate-50 dark:bg-slate-950 transition-colors duration-200 text-xs">

    <!-- Mobile Sidebar Backdrop Overlay -->
    <div x-show="sidebarOpen" 
         x-transition:enter="transition-opacity ease-linear duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false"
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-40 lg:hidden"
         style="display: none;">
    </div>

    <!-- Sidebar Navigation with Accordion Modules -->
    <aside class="fixed inset-y-0 left-0 z-50 flex flex-col bg-white dark:bg-slate-900 border-r border-slate-200/80 dark:border-slate-800/80 shadow-sm transition-all duration-200 ease-in-out lg:static lg:flex-shrink-0"
           :class="{
               'w-64 lg:w-64': !sidebarCollapsed,
               'w-16 lg:w-16': sidebarCollapsed,
               'translate-x-0': sidebarOpen,
               '-translate-x-full lg:translate-x-0': !sidebarOpen
           }">
        
        <!-- Sidebar Brand Header -->
        <div class="h-14 flex items-center border-b border-slate-200/80 dark:border-slate-800/80 bg-white dark:bg-slate-900 px-3 relative"
             :class="sidebarCollapsed ? 'justify-center' : 'justify-between'">
            
            <!-- Expanded Brand Content -->
            <div x-show="!sidebarCollapsed" class="flex items-center gap-2.5 flex-1 min-w-0 pr-2">
                <a href="{{ url('/admin/dashboard') }}" class="flex items-center gap-2.5 min-w-0 group">
                    <div class="w-8 h-8 flex-shrink-0 rounded-xl bg-indigo-600 dark:bg-indigo-500 text-white flex items-center justify-center shadow-xs group-hover:scale-105 transition-transform">
                        <i data-lucide="receipt" class="w-4 h-4"></i>
                    </div>
                    <div class="flex flex-col min-w-0">
                        <span class="font-extrabold text-sm tracking-tight text-slate-900 dark:text-white truncate">
                            IMS Malaysia
                        </span>
                        <span class="text-[9px] -mt-0.5 font-bold tracking-wider text-slate-400 dark:text-slate-500 uppercase truncate">
                            Billing & Tax Hub
                        </span>
                    </div>
                </a>
            </div>

            <!-- Collapsed Centered Brand Icon -->
            <div x-show="sidebarCollapsed" class="w-full flex items-center justify-center" style="display: none;">
                <a href="{{ url('/admin/dashboard') }}" class="w-8 h-8 rounded-xl bg-indigo-600 text-white flex items-center justify-center shadow-xs" title="IMS Malaysia">
                    <i data-lucide="receipt" class="w-4 h-4"></i>
                </a>
            </div>

            <!-- Mobile Close Button -->
            <button type="button" @click="sidebarOpen = false" class="lg:hidden p-1 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 ml-auto">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <!-- Mode Indicator Banner inside Sidebar -->
        <div class="px-3 pt-2 pb-1" x-show="!sidebarCollapsed">
            <div class="p-1.5 rounded-lg border text-[10px] flex items-center justify-between"
                 :class="{
                    'bg-slate-100 dark:bg-slate-800/60 border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300': eInvoiceMode === 'off',
                    'bg-amber-50 dark:bg-amber-950/40 border-amber-200 dark:border-amber-800/60 text-amber-800 dark:text-amber-300': eInvoiceMode === 'sandbox',
                    'bg-emerald-50 dark:bg-emerald-950/40 border-emerald-200 dark:border-emerald-800/60 text-emerald-800 dark:text-emerald-300': eInvoiceMode === 'production'
                 }">
                <div class="flex items-center gap-1.5 min-w-0">
                    <span class="w-1.5 h-1.5 rounded-full flex-shrink-0"
                          :class="{
                            'bg-slate-400': eInvoiceMode === 'off',
                            'bg-amber-500 animate-pulse': eInvoiceMode === 'sandbox',
                            'bg-emerald-500 animate-pulse': eInvoiceMode === 'production'
                          }"></span>
                    <span class="font-bold uppercase tracking-wider text-[9px] truncate" x-text="eInvoiceMode === 'off' ? 'Standard Mode' : 'e-Invois ' + eInvoiceMode"></span>
                </div>
                <a href="{{ url('/admin/settings') }}" class="text-[9px] font-semibold text-indigo-600 dark:text-indigo-400 underline hover:opacity-80 flex-shrink-0">Toggle</a>
            </div>
        </div>

        <!-- Accordion Navigation Menu -->
        <nav class="flex-1 px-2 py-2 space-y-1.5 overflow-y-auto">
            
            <!-- 1. OVERVIEW (Direct Item) -->
            <a href="{{ url('/admin/dashboard') }}" 
               :title="sidebarCollapsed ? 'Dashboard' : ''"
               class="flex items-center rounded-lg text-xs font-medium transition-all group {{ request()->is('admin/dashboard') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}"
               :class="sidebarCollapsed ? 'justify-center p-2.5' : 'gap-2.5 px-2.5 py-1.5'">
                <i data-lucide="layout-dashboard" class="w-4 h-4 flex-shrink-0 {{ request()->is('admin/dashboard') ? 'text-white' : 'text-slate-400 group-hover:text-indigo-600 dark:group-hover:text-indigo-400' }}"></i>
                <span x-show="!sidebarCollapsed" class="truncate font-semibold">Dashboard</span>
            </a>

            <!-- 2. ACCOUNTS RECEIVABLE (Accordion Module) -->
            <div class="space-y-0.5">
                <button type="button" 
                        @click="toggleModule('ar')"
                        :title="sidebarCollapsed ? 'Accounts Receivable (AR)' : ''"
                        class="w-full flex items-center rounded-lg text-xs font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                        :class="sidebarCollapsed ? 'justify-center p-2' : 'justify-between px-2.5 py-1.5'">
                    <div class="flex items-center gap-2 min-w-0">
                        <i data-lucide="file-text" class="w-4 h-4 text-indigo-600 dark:text-indigo-400 flex-shrink-0"></i>
                        <span x-show="!sidebarCollapsed" class="truncate uppercase tracking-wider text-[10px] text-slate-500 dark:text-slate-400">Accounts Receivable</span>
                    </div>
                    <i x-show="!sidebarCollapsed" data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" :class="activeModule === 'ar' ? 'rotate-180' : ''"></i>
                </button>

                <!-- Children Sub-links -->
                <div x-show="activeModule === 'ar' && !sidebarCollapsed" x-transition class="pl-4 space-y-0.5 border-l border-slate-100 dark:border-slate-800 ml-3.5 mt-0.5">
                    <a href="{{ url('/admin/invoices/create') }}" 
                       class="flex flex-col px-2.5 py-1.5 rounded-lg transition-colors group {{ request()->is('admin/invoices/create') ? 'bg-indigo-600 text-white font-bold shadow-xs' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                        <span class="text-xs truncate {{ request()->is('admin/invoices/create') ? 'text-white font-bold' : 'text-slate-800 dark:text-slate-200' }}">Create Tax Invoice</span>
                        <span class="text-[10px] font-normal {{ request()->is('admin/invoices/create') ? 'text-indigo-100' : 'text-slate-500 dark:text-slate-400' }}">Fast 60s AR Invoicing</span>
                    </a>

                    <a href="{{ url('/admin/invoices') }}" 
                       class="flex flex-col px-2.5 py-1.5 rounded-lg transition-colors group {{ request()->is('admin/invoices') ? 'bg-indigo-600 text-white font-bold shadow-xs' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                        <span class="text-xs truncate {{ request()->is('admin/invoices') ? 'text-white font-bold' : 'text-slate-800 dark:text-slate-200' }}">Invoices List</span>
                        <span class="text-[10px] font-normal {{ request()->is('admin/invoices') ? 'text-indigo-100' : 'text-slate-500 dark:text-slate-400' }}">Draft, Issued, Paid, Overdue</span>
                    </a>

                    <a href="{{ url('/admin/customers') }}" 
                       class="flex flex-col px-2.5 py-1.5 rounded-lg transition-colors group {{ request()->is('admin/customers*') ? 'bg-indigo-600 text-white font-bold shadow-xs' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                        <span class="text-xs truncate {{ request()->is('admin/customers*') ? 'text-white font-bold' : 'text-slate-800 dark:text-slate-200' }}">Customers & Patients</span>
                        <span class="text-[10px] font-normal {{ request()->is('admin/customers*') ? 'text-indigo-100' : 'text-slate-500 dark:text-slate-400' }}">B2B Companies & B2C Walk-ins</span>
                    </a>
                </div>
            </div>

            <!-- 3. ACCOUNTS PAYABLE (Accordion Module) -->
            <div class="space-y-0.5">
                <button type="button" 
                        @click="toggleModule('ap')"
                        :title="sidebarCollapsed ? 'Accounts Payable (AP)' : ''"
                        class="w-full flex items-center rounded-lg text-xs font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                        :class="sidebarCollapsed ? 'justify-center p-2' : 'justify-between px-2.5 py-1.5'">
                    <div class="flex items-center gap-2 min-w-0">
                        <i data-lucide="receipt" class="w-4 h-4 text-amber-600 dark:text-amber-400 flex-shrink-0"></i>
                        <span x-show="!sidebarCollapsed" class="truncate uppercase tracking-wider text-[10px] text-slate-600 dark:text-slate-300 font-bold">Accounts Payable</span>
                    </div>
                    <i x-show="!sidebarCollapsed" data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" :class="activeModule === 'ap' ? 'rotate-180' : ''"></i>
                </button>

                <!-- Children Sub-links -->
                <div x-show="activeModule === 'ap' && !sidebarCollapsed" x-transition class="pl-4 space-y-0.5 border-l border-slate-100 dark:border-slate-800 ml-3.5 mt-0.5">
                    <a href="{{ url('/admin/bills/upload') }}" 
                       class="flex flex-col px-2.5 py-1.5 rounded-lg transition-colors group {{ request()->is('admin/bills/upload') ? 'bg-indigo-600 text-white font-bold shadow-xs' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                        <span class="text-xs truncate {{ request()->is('admin/bills/upload') ? 'text-white font-bold' : 'text-slate-800 dark:text-slate-200' }}">Upload Supplier Bill</span>
                        <span class="text-[10px] font-normal {{ request()->is('admin/bills/upload') ? 'text-indigo-100' : 'text-slate-500 dark:text-slate-400' }}">OCR Extraction & 2-Way Match</span>
                    </a>

                    <a href="{{ url('/admin/bills') }}" 
                       class="flex flex-col px-2.5 py-1.5 rounded-lg transition-colors group {{ request()->is('admin/bills') ? 'bg-indigo-600 text-white font-bold shadow-xs' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                        <span class="text-xs truncate {{ request()->is('admin/bills') ? 'text-white font-bold' : 'text-slate-800 dark:text-slate-200' }}">Supplier Bills List</span>
                        <span class="text-[10px] font-normal {{ request()->is('admin/bills') ? 'text-indigo-100' : 'text-slate-500 dark:text-slate-400' }}">Matched & Variance Flagged</span>
                    </a>

                    <a href="{{ url('/admin/vendors') }}" 
                       class="flex flex-col px-2.5 py-1.5 rounded-lg transition-colors group {{ request()->is('admin/vendors*') ? 'bg-indigo-600 text-white font-bold shadow-xs' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                        <span class="text-xs truncate {{ request()->is('admin/vendors*') ? 'text-white font-bold' : 'text-slate-800 dark:text-slate-200' }}">Vendors & Suppliers</span>
                        <span class="text-[10px] font-normal {{ request()->is('admin/vendors*') ? 'text-indigo-100' : 'text-slate-500 dark:text-slate-400' }}">Bank Details & SST Numbers</span>
                    </a>
                </div>
            </div>

            <!-- 4. BANKING & PAYMENTS (Accordion Module) -->
            <div class="space-y-0.5">
                <button type="button" 
                        @click="toggleModule('banking')"
                        :title="sidebarCollapsed ? 'Banking & Payments' : ''"
                        class="w-full flex items-center rounded-lg text-xs font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                        :class="sidebarCollapsed ? 'justify-center p-2' : 'justify-between px-2.5 py-1.5'">
                    <div class="flex items-center gap-2 min-w-0">
                        <i data-lucide="credit-card" class="w-4 h-4 text-emerald-600 dark:text-emerald-400 flex-shrink-0"></i>
                        <span x-show="!sidebarCollapsed" class="truncate uppercase tracking-wider text-[10px] text-slate-600 dark:text-slate-300 font-bold">Banking & Payouts</span>
                    </div>
                    <i x-show="!sidebarCollapsed" data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" :class="activeModule === 'banking' ? 'rotate-180' : ''"></i>
                </button>

                <div x-show="activeModule === 'banking' && !sidebarCollapsed" x-transition class="pl-4 space-y-0.5 border-l border-slate-100 dark:border-slate-800 ml-3.5 mt-0.5">
                    <a href="{{ url('/admin/banking/batch-payouts') }}" 
                       class="flex flex-col px-2.5 py-1.5 rounded-lg transition-colors group {{ request()->is('admin/banking*') ? 'bg-indigo-600 text-white font-bold shadow-xs' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                        <span class="text-xs truncate {{ request()->is('admin/banking*') ? 'text-white font-bold' : 'text-slate-800 dark:text-slate-200' }}">Bank Batch Payouts</span>
                        <span class="text-[10px] font-normal {{ request()->is('admin/banking*') ? 'text-indigo-100' : 'text-slate-500 dark:text-slate-400' }}">Maybank / CIMB IBG CSV</span>
                    </a>
                </div>
            </div>

            <!-- 5. TAX & COMPLIANCE (Accordion Module) -->
            <div class="space-y-0.5">
                <button type="button" 
                        @click="toggleModule('tax')"
                        :title="sidebarCollapsed ? 'Tax & Statutory Compliance' : ''"
                        class="w-full flex items-center rounded-lg text-xs font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                        :class="sidebarCollapsed ? 'justify-center p-2' : 'justify-between px-2.5 py-1.5'">
                    <div class="flex items-center gap-2 min-w-0">
                        <i data-lucide="shield-check" class="w-4 h-4 text-indigo-600 dark:text-indigo-400 flex-shrink-0"></i>
                        <span x-show="!sidebarCollapsed" class="truncate uppercase tracking-wider text-[10px] text-slate-600 dark:text-slate-300 font-bold">Tax & Compliance</span>
                    </div>
                    <i x-show="!sidebarCollapsed" data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" :class="activeModule === 'tax' ? 'rotate-180' : ''"></i>
                </button>

                <div x-show="activeModule === 'tax' && !sidebarCollapsed" x-transition class="pl-4 space-y-0.5 border-l border-slate-100 dark:border-slate-800 ml-3.5 mt-0.5">
                    <a href="{{ url('/admin/reports/sst-02') }}" 
                       class="flex flex-col px-2.5 py-1.5 rounded-lg transition-colors group {{ request()->is('admin/reports/sst-02*') ? 'bg-indigo-600 text-white font-bold shadow-xs' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                        <span class="text-xs truncate {{ request()->is('admin/reports/sst-02*') ? 'text-white font-bold' : 'text-slate-800 dark:text-slate-200' }}">SST-02 Tax Return</span>
                        <span class="text-[10px] font-normal {{ request()->is('admin/reports/sst-02*') ? 'text-indigo-100' : 'text-slate-500 dark:text-slate-400' }}">JKDM Bi-Monthly 8% & 6%</span>
                    </a>

                    <a href="{{ url('/admin/reports/aging') }}" 
                       class="flex flex-col px-2.5 py-1.5 rounded-lg transition-colors group {{ request()->is('admin/reports/aging*') ? 'bg-indigo-600 text-white font-bold shadow-xs' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                        <span class="text-xs truncate {{ request()->is('admin/reports/aging*') ? 'text-white font-bold' : 'text-slate-800 dark:text-slate-200' }}">AR & AP Aging</span>
                        <span class="text-[10px] font-normal {{ request()->is('admin/reports/aging*') ? 'text-indigo-100' : 'text-slate-500 dark:text-slate-400' }}">0-30, 31-60, 61-90, 90+ Days</span>
                    </a>
                </div>
            </div>

            <!-- 6. CONFIGURATION -->
            <div class="space-y-0.5">
                <a href="{{ url('/admin/settings') }}" 
                   :title="sidebarCollapsed ? 'Settings & e-Invois' : ''"
                   class="flex items-center rounded-lg text-xs font-medium transition-all group {{ request()->is('admin/settings*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}"
                   :class="sidebarCollapsed ? 'justify-center p-2.5' : 'gap-2.5 px-2.5 py-1.5'">
                    <i data-lucide="settings" class="w-4 h-4 flex-shrink-0 {{ request()->is('admin/settings*') ? 'text-white' : 'text-slate-400 group-hover:text-indigo-600 dark:group-hover:text-indigo-400' }}"></i>
                    <div x-show="!sidebarCollapsed" class="flex flex-col min-w-0">
                        <span class="truncate font-semibold">Settings & e-Invois</span>
                        <span class="text-[9px] text-slate-400 dark:text-slate-500 font-normal">Company Profile & LHDN Modes</span>
                    </div>
                </a>
            </div>
        </nav>

        <!-- User Profile Card in Sidebar Footer -->
        <div class="p-2 border-t border-slate-200/80 dark:border-slate-800/80 bg-slate-50/50 dark:bg-slate-900/50">
            <div class="flex items-center rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                 :class="sidebarCollapsed ? 'justify-center p-1.5' : 'justify-between p-1.5'">
                
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 rounded-lg bg-indigo-600 text-white flex items-center justify-center font-bold text-[10px] flex-shrink-0 shadow-xs">
                        {{ substr(Auth::user()->name ?? 'AD', 0, 2) }}
                    </div>
                    <div x-show="!sidebarCollapsed" class="flex flex-col min-w-0">
                        <span class="text-[11px] font-bold text-slate-800 dark:text-slate-200 truncate">
                            {{ Auth::user()->name ?? 'Administrator' }}
                        </span>
                        <span class="text-[9px] text-slate-400 dark:text-slate-500 truncate">
                            {{ Auth::user()->email ?? 'admin@ims-malaysia.com' }}
                        </span>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.logout') }}" x-show="!sidebarCollapsed">
                    @csrf
                    <button type="submit" class="p-1 rounded-md text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/50 transition-colors flex-shrink-0" title="Sign Out">
                        <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Application Container (100% Full Width & Flex-1) -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        
        <!-- Top Application Header (Fixed Clean Alignment with Sidebar Toggle) -->
        <header class="h-14 flex items-center justify-between px-3 sm:px-5 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md border-b border-slate-200/80 dark:border-slate-800/80 sticky top-0 z-30 transition-colors">
            
            <!-- Left Side: Desktop Sidebar Collapse Toggle + Mobile Hamburger + Breadcrumb -->
            <div class="flex items-center gap-2.5">
                <!-- Desktop Sidebar Toggle Button -->
                <button type="button" 
                        @click="toggleSidebarCollapse()" 
                        class="hidden lg:flex p-1.5 rounded-lg text-slate-500 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors focus:outline-none" 
                        :title="sidebarCollapsed ? 'Expand Sidebar' : 'Collapse Sidebar'">
                    <i :data-lucide="sidebarCollapsed ? 'panel-left-open' : 'panel-left-close'" class="w-4 h-4"></i>
                </button>

                <!-- Mobile Hamburger Button -->
                <button type="button" @click="sidebarOpen = true" class="lg:hidden p-1.5 rounded-lg text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800">
                    <i data-lucide="menu" class="w-4 h-4"></i>
                </button>

                <!-- Page Breadcrumb -->
                <div class="flex items-center gap-1.5 text-xs font-medium pl-1 border-l border-slate-200 dark:border-slate-700">
                    <span class="text-slate-400 dark:text-slate-500 font-semibold">IMS</span>
                    <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-slate-400"></i>
                    <span class="font-bold text-slate-800 dark:text-slate-100">{{ $header ?? 'Dashboard' }}</span>
                </div>
            </div>

            <!-- Right Side: Mode Switcher, Notifications, Dark Mode, Quick Invoice CTA -->
            <div class="flex items-center gap-2">
                
                <!-- Quick Mode Selector -->
                <div class="hidden sm:flex items-center bg-slate-100 dark:bg-slate-800 p-0.5 rounded-lg border border-slate-200 dark:border-slate-700 text-[10px]">
                    <button type="button" 
                            @click="setEInvoiceMode('off')"
                            :class="eInvoiceMode === 'off' ? 'bg-white dark:bg-slate-700 font-bold text-slate-900 dark:text-white shadow-xs' : 'text-slate-500 hover:text-slate-900 dark:text-slate-400'"
                            class="px-2 py-0.5 rounded-md transition-all">
                        Standard
                    </button>
                    <button type="button" 
                            @click="setEInvoiceMode('sandbox')"
                            :class="eInvoiceMode === 'sandbox' ? 'bg-amber-500 font-bold text-white shadow-xs' : 'text-slate-500 hover:text-slate-900 dark:text-slate-400'"
                            class="px-2 py-0.5 rounded-md transition-all">
                        Sandbox
                    </button>
                    <button type="button" 
                            @click="setEInvoiceMode('production')"
                            :class="eInvoiceMode === 'production' ? 'bg-emerald-600 font-bold text-white shadow-xs' : 'text-slate-500 hover:text-slate-900 dark:text-slate-400'"
                            class="px-2 py-0.5 rounded-md transition-all">
                        LHDN Live
                    </button>
                </div>

                <!-- Dark Mode Toggle Button -->
                <button type="button" @click="toggleDarkMode()" class="p-1.5 rounded-lg text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors focus:outline-none" title="Toggle Theme">
                    <i x-show="!darkMode" data-lucide="moon" class="w-3.5 h-3.5"></i>
                    <i x-show="darkMode" data-lucide="sun" class="w-3.5 h-3.5" style="display: none;"></i>
                </button>

                <!-- Notifications -->
                <button type="button" class="relative p-1.5 rounded-lg text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                    <i data-lucide="bell" class="w-3.5 h-3.5"></i>
                    <span class="absolute top-1 right-1 w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse"></span>
                </button>

                <!-- New Invoice Quick CTA Button -->
                <a href="{{ url('/admin/invoices/create') }}" class="hidden sm:inline-flex items-center gap-1 px-3 py-1.5 text-[11px] font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-xs transition-all transform active:scale-95">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                    <span>New Invoice</span>
                </a>
            </div>
        </header>

        <!-- Main Dynamic Content Scroll View (100% Full Width) -->
        <main class="flex-1 overflow-y-auto p-3 sm:p-5 bg-slate-50 dark:bg-slate-950 transition-colors">
            <div class="w-full space-y-4">
                {{ $slot }}
            </div>
        </main>
    </div>

    @livewireScripts
    @stack('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (window.initLucideIcons) window.initLucideIcons();
        });
        document.addEventListener('livewire:navigated', () => {
            if (window.initLucideIcons) window.initLucideIcons();
        });
    </script>
</body>
</html>
