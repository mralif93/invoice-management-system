<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50 antialiased" x-data="{
    darkMode: localStorage.getItem('darkMode') === 'true',
    sidebarOpen: false,
    sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true',
    eInvoiceMode: localStorage.getItem('eInvoiceMode') || 'off', // 'off', 'sandbox', 'production'
    toggleDarkMode() {
        this.darkMode = !this.darkMode;
        localStorage.setItem('darkMode', this.darkMode);
        if (this.darkMode) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
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
<body class="h-full flex overflow-hidden font-sans text-slate-900 dark:text-slate-100 bg-slate-50 dark:bg-slate-950 transition-colors duration-200">

    <!-- Mobile Sidebar Backdrop -->
    <div x-show="sidebarOpen" 
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false"
         class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm z-40 lg:hidden"
         style="display: none;">
    </div>

    <!-- Sidebar Navigation -->
    <aside :class="sidebarCollapsed ? 'lg:w-20' : 'lg:w-64'"
           class="fixed inset-y-0 left-0 z-50 flex flex-col bg-white dark:bg-slate-900 border-r border-slate-200/80 dark:border-slate-800/80 shadow-sm transition-all duration-300 ease-in-out w-64 lg:static lg:flex-shrink-0"
           :class="{ 'translate-x-0': sidebarOpen, '-translate-x-full lg:translate-x-0': !sidebarOpen }">
        
        <!-- Sidebar Brand Header -->
        <div class="h-16 flex items-center justify-between px-4 border-b border-slate-200/80 dark:border-slate-800/80 bg-white dark:bg-slate-900">
            <a href="{{ url('/admin/dashboard') }}" class="flex items-center gap-3 overflow-hidden group">
                <div class="w-9 h-9 flex-shrink-0 rounded-xl bg-indigo-600 dark:bg-indigo-500 text-white flex items-center justify-center shadow-md shadow-indigo-500/20 group-hover:scale-105 transition-transform">
                    <i data-lucide="receipt" class="w-5 h-5"></i>
                </div>
                <div x-show="!sidebarCollapsed" class="flex flex-col truncate transition-opacity duration-200">
                    <span class="font-extrabold text-base tracking-tight bg-gradient-to-r from-slate-900 via-indigo-950 to-indigo-700 dark:from-white dark:via-slate-200 dark:to-indigo-300 bg-clip-text text-transparent truncate">
                        IMS Malaysia
                    </span>
                    <span class="text-[10px] -mt-0.5 font-semibold tracking-wider text-slate-500 dark:text-slate-400 uppercase truncate">
                        Billing & Tax Hub
                    </span>
                </div>
            </a>

            <!-- Collapse toggle for Desktop -->
            <button type="button" @click="toggleSidebarCollapse()" class="hidden lg:flex p-1.5 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                <i :data-lucide="sidebarCollapsed ? 'panel-left-open' : 'panel-left-close'" class="w-4 h-4"></i>
            </button>
            
            <!-- Close button for Mobile -->
            <button type="button" @click="sidebarOpen = false" class="lg:hidden p-1.5 rounded-lg text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <!-- Mode Indicator Banner inside Sidebar -->
        <div class="px-3 pt-3 pb-1" x-show="!sidebarCollapsed">
            <div class="p-2 rounded-xl border text-xs flex items-center justify-between"
                 :class="{
                    'bg-slate-100 dark:bg-slate-800/60 border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300': eInvoiceMode === 'off',
                    'bg-amber-50 dark:bg-amber-950/40 border-amber-200 dark:border-amber-800/60 text-amber-800 dark:text-amber-300': eInvoiceMode === 'sandbox',
                    'bg-emerald-50 dark:bg-emerald-950/40 border-emerald-200 dark:border-emerald-800/60 text-emerald-800 dark:text-emerald-300': eInvoiceMode === 'production'
                 }">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full"
                          :class="{
                            'bg-slate-400': eInvoiceMode === 'off',
                            'bg-amber-500 animate-pulse': eInvoiceMode === 'sandbox',
                            'bg-emerald-500 animate-pulse': eInvoiceMode === 'production'
                          }"></span>
                    <span class="font-bold uppercase tracking-wider text-[10px]" x-text="eInvoiceMode === 'off' ? 'Standard Mode' : 'e-Invois ' + eInvoiceMode"></span>
                </div>
                <a href="{{ url('/admin/settings') }}" class="text-[10px] font-semibold underline hover:opacity-80">Toggle</a>
            </div>
        </div>

        <!-- Navigation Links Menu -->
        <nav class="flex-1 px-3 py-3 space-y-1 overflow-y-auto">
            <!-- Section: Core -->
            <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 px-3 py-1.5" x-show="!sidebarCollapsed">
                Core Operations
            </div>

            <!-- Dashboard -->
            <a href="{{ url('/admin/dashboard') }}" 
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all group {{ request()->is('admin/dashboard') ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-500/20' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <i data-lucide="layout-dashboard" class="w-5 h-5 flex-shrink-0 {{ request()->is('admin/dashboard') ? 'text-white' : 'text-slate-500 dark:text-slate-400 group-hover:text-indigo-600 dark:group-hover:text-indigo-400' }}"></i>
                <span x-show="!sidebarCollapsed" class="truncate">Dashboard</span>
            </a>

            <!-- Invoices (AR) -->
            <a href="{{ url('/admin/invoices') }}" 
               class="flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-medium transition-all group {{ request()->is('admin/invoices*') ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-500/20' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <div class="flex items-center gap-3">
                    <i data-lucide="file-text" class="w-5 h-5 flex-shrink-0 {{ request()->is('admin/invoices*') ? 'text-white' : 'text-slate-500 dark:text-slate-400 group-hover:text-indigo-600 dark:group-hover:text-indigo-400' }}"></i>
                    <span x-show="!sidebarCollapsed" class="truncate">Customer Invoices (AR)</span>
                </div>
                <span x-show="!sidebarCollapsed" class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                    SST
                </span>
            </a>

            <!-- Supplier Bills (AP) -->
            <a href="{{ url('/admin/bills') }}" 
               class="flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-medium transition-all group {{ request()->is('admin/bills*') ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-500/20' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <div class="flex items-center gap-3">
                    <i data-lucide="receipt-text" class="w-5 h-5 flex-shrink-0 {{ request()->is('admin/bills*') ? 'text-white' : 'text-slate-500 dark:text-slate-400 group-hover:text-indigo-600 dark:group-hover:text-indigo-400' }}"></i>
                    <span x-show="!sidebarCollapsed" class="truncate">Supplier Bills (AP)</span>
                </div>
                <span x-show="!sidebarCollapsed" class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                    2-Way
                </span>
            </a>

            <!-- Customers & Vendors -->
            <div class="pt-3 text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 px-3 py-1.5" x-show="!sidebarCollapsed">
                Master Records
            </div>
            <a href="{{ url('/admin/customers') }}" 
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all group {{ request()->is('admin/customers*') ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-500/20' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <i data-lucide="users" class="w-5 h-5 flex-shrink-0 {{ request()->is('admin/customers*') ? 'text-white' : 'text-slate-500 dark:text-slate-400 group-hover:text-indigo-600 dark:group-hover:text-indigo-400' }}"></i>
                <span x-show="!sidebarCollapsed" class="truncate">Customers (Buyers)</span>
            </a>
            <a href="{{ url('/admin/vendors') }}" 
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all group {{ request()->is('admin/vendors*') ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-500/20' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <i data-lucide="building-2" class="w-5 h-5 flex-shrink-0 {{ request()->is('admin/vendors*') ? 'text-white' : 'text-slate-500 dark:text-slate-400 group-hover:text-indigo-600 dark:group-hover:text-indigo-400' }}"></i>
                <span x-show="!sidebarCollapsed" class="truncate">Vendors (Suppliers)</span>
            </a>

            <!-- Compliance & Reporting -->
            <div class="pt-3 text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 px-3 py-1.5" x-show="!sidebarCollapsed">
                Compliance & Finance
            </div>
            <a href="{{ url('/admin/reports/sst-02') }}" 
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all group {{ request()->is('admin/reports/sst-02*') ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-500/20' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <i data-lucide="file-spreadsheet" class="w-5 h-5 flex-shrink-0 {{ request()->is('admin/reports/sst-02*') ? 'text-white' : 'text-slate-500 dark:text-slate-400 group-hover:text-indigo-600 dark:group-hover:text-indigo-400' }}"></i>
                <span x-show="!sidebarCollapsed" class="truncate">SST-02 Tax Return</span>
            </a>
            <a href="{{ url('/admin/reports/aging') }}" 
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all group {{ request()->is('admin/reports/aging*') ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-500/20' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <i data-lucide="clock" class="w-5 h-5 flex-shrink-0 {{ request()->is('admin/reports/aging*') ? 'text-white' : 'text-slate-500 dark:text-slate-400 group-hover:text-indigo-600 dark:group-hover:text-indigo-400' }}"></i>
                <span x-show="!sidebarCollapsed" class="truncate">AR & AP Aging</span>
            </a>
            <a href="{{ url('/admin/banking/batch-payouts') }}" 
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all group {{ request()->is('admin/banking*') ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-500/20' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <i data-lucide="credit-card" class="w-5 h-5 flex-shrink-0 {{ request()->is('admin/banking*') ? 'text-white' : 'text-slate-500 dark:text-slate-400 group-hover:text-indigo-600 dark:group-hover:text-indigo-400' }}"></i>
                <span x-show="!sidebarCollapsed" class="truncate">Bank Batch Payouts</span>
            </a>

            <!-- System Settings -->
            <div class="pt-3 text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 px-3 py-1.5" x-show="!sidebarCollapsed">
                Configuration
            </div>
            <a href="{{ url('/admin/settings') }}" 
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all group {{ request()->is('admin/settings*') ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-500/20' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <i data-lucide="settings" class="w-5 h-5 flex-shrink-0 {{ request()->is('admin/settings*') ? 'text-white' : 'text-slate-500 dark:text-slate-400 group-hover:text-indigo-600 dark:group-hover:text-indigo-400' }}"></i>
                <span x-show="!sidebarCollapsed" class="truncate">Settings & e-Invois</span>
            </a>
        </nav>

        <!-- User Profile Card in Sidebar Footer -->
        <div class="p-3 border-t border-slate-200/80 dark:border-slate-800/80 bg-slate-50/50 dark:bg-slate-900/50">
            <div class="flex items-center gap-3 p-2 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer">
                <div class="w-8 h-8 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold text-xs">
                    {{ substr(Auth::user()->name ?? 'AD', 0, 2) }}
                </div>
                <div x-show="!sidebarCollapsed" class="flex flex-col truncate">
                    <span class="text-xs font-bold text-slate-900 dark:text-white truncate">
                        {{ Auth::user()->name ?? 'Administrator' }}
                    </span>
                    <span class="text-[10px] text-slate-500 dark:text-slate-400 truncate">
                        {{ Auth::user()->email ?? 'admin@ims-malaysia.com' }}
                    </span>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Application Wrapper -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        
        <!-- Top Application Header -->
        <header class="h-16 flex items-center justify-between px-4 sm:px-6 lg:px-8 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md border-b border-slate-200/80 dark:border-slate-800/80 sticky top-0 z-30 transition-colors">
            
            <!-- Left Side: Mobile Menu Button & Breadcrumb -->
            <div class="flex items-center gap-4">
                <button type="button" @click="sidebarOpen = true" class="lg:hidden p-2 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800">
                    <i data-lucide="menu" class="w-5 h-5"></i>
                </button>

                <!-- Page Header / Breadcrumb -->
                <div class="flex items-center gap-2 text-sm font-medium">
                    <span class="text-slate-400 dark:text-slate-500">IMS</span>
                    <i data-lucide="chevron-right" class="w-4 h-4 text-slate-400"></i>
                    <span class="font-bold text-slate-900 dark:text-white">{{ $header ?? 'Dashboard' }}</span>
                </div>
            </div>

            <!-- Right Side: Global Search, Mode Switcher, Notifications, Dark Mode -->
            <div class="flex items-center gap-3">
                
                <!-- Quick Mode Toggle Dropdown / Selector -->
                <div class="hidden sm:flex items-center bg-slate-100 dark:bg-slate-800 p-1 rounded-xl border border-slate-200 dark:border-slate-700 text-xs">
                    <button type="button" 
                            @click="setEInvoiceMode('off')"
                            :class="eInvoiceMode === 'off' ? 'bg-white dark:bg-slate-700 font-bold text-slate-900 dark:text-white shadow-sm' : 'text-slate-500 hover:text-slate-900 dark:text-slate-400'"
                            class="px-2.5 py-1 rounded-lg transition-all">
                        Standard
                    </button>
                    <button type="button" 
                            @click="setEInvoiceMode('sandbox')"
                            :class="eInvoiceMode === 'sandbox' ? 'bg-amber-500 font-bold text-white shadow-sm' : 'text-slate-500 hover:text-slate-900 dark:text-slate-400'"
                            class="px-2.5 py-1 rounded-lg transition-all">
                        Sandbox
                    </button>
                    <button type="button" 
                            @click="setEInvoiceMode('production')"
                            :class="eInvoiceMode === 'production' ? 'bg-emerald-600 font-bold text-white shadow-sm' : 'text-slate-500 hover:text-slate-900 dark:text-slate-400'"
                            class="px-2.5 py-1 rounded-lg transition-all">
                        LHDN Live
                    </button>
                </div>

                <!-- Dark Mode Toggle Button -->
                <button type="button" @click="toggleDarkMode()" class="p-2 rounded-xl text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors focus:outline-none" title="Toggle Theme">
                    <i x-show="!darkMode" data-lucide="moon" class="w-4 h-4"></i>
                    <i x-show="darkMode" data-lucide="sun" class="w-4 h-4" style="display: none;"></i>
                </button>

                <!-- Notifications -->
                <button type="button" class="relative p-2 rounded-xl text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                    <i data-lucide="bell" class="w-4 h-4"></i>
                    <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span>
                </button>

                <!-- New Invoice Quick CTA Button -->
                <a href="{{ url('/admin/invoices/create') }}" class="hidden sm:inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-sm hover:shadow-md transition-all transform active:scale-95">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    <span>New Invoice</span>
                </a>
            </div>
        </header>

        <!-- Main Dynamic Content Scroll View -->
        <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 bg-slate-50 dark:bg-slate-950 transition-colors">
            <div class="max-w-7xl mx-auto space-y-6">
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
