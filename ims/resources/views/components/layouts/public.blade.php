<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50 antialiased" x-data="{
    darkMode: localStorage.getItem('darkMode') === 'true',
    mobileMenuOpen: false,
    toggleDarkMode() {
        this.darkMode = !this.darkMode;
        localStorage.setItem('darkMode', this.darkMode);
        if (this.darkMode) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    }
}" x-init="if (darkMode) document.documentElement.classList.add('dark');">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'IMS Malaysia') }} - Simplified Invoice Management</title>
    <meta name="description" content="Malaysian SST-compliant Invoice Management System with optional LHDN MyInvois e-invoicing.">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles')
</head>
<body class="min-h-full flex flex-col font-sans text-slate-900 dark:text-slate-100 bg-slate-50 dark:bg-slate-950 transition-colors duration-200">

    <!-- Public Navigation Bar -->
    <header class="sticky top-0 z-40 w-full border-b border-slate-200/80 dark:border-slate-800/80 bg-white/90 dark:bg-slate-900/90 backdrop-blur-md transition-colors">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Brand Logo & Badge -->
                <div class="flex items-center gap-3">
                    <a href="{{ url('/') }}" class="flex items-center gap-2.5 group">
                        <div class="w-9 h-9 rounded-xl bg-indigo-600 dark:bg-indigo-500 text-white flex items-center justify-center shadow-md shadow-indigo-500/20 group-hover:scale-105 transition-transform">
                            <i data-lucide="receipt-text" class="w-5 h-5"></i>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-extrabold text-lg tracking-tight bg-gradient-to-r from-slate-900 via-indigo-950 to-indigo-700 dark:from-white dark:via-slate-200 dark:to-indigo-300 bg-clip-text text-transparent">
                                IMS Malaysia
                            </span>
                            <span class="text-[10px] -mt-1 font-semibold tracking-wider text-slate-500 dark:text-slate-400 uppercase">
                                Invoice & e-Invois
                            </span>
                        </div>
                    </a>

                    <span class="hidden sm:inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse mr-1.5"></span>
                        LHDN & SST Ready
                    </span>
                </div>

                <!-- Desktop Public Links -->
                <nav class="hidden md:flex items-center gap-6">
                    <a href="{{ url('/') }}" class="text-sm font-medium text-slate-600 hover:text-indigo-600 dark:text-slate-300 dark:hover:text-indigo-400 transition-colors">
                        Home
                    </a>
                    <a href="{{ url('/verify') }}" class="text-sm font-medium text-slate-600 hover:text-indigo-600 dark:text-slate-300 dark:hover:text-indigo-400 transition-colors flex items-center gap-1.5">
                        <i data-lucide="shield-check" class="w-4 h-4 text-emerald-500"></i>
                        Verify Invoice
                    </a>
                    <a href="{{ url('/faq') }}" class="text-sm font-medium text-slate-600 hover:text-indigo-600 dark:text-slate-300 dark:hover:text-indigo-400 transition-colors">
                        SST & LHDN FAQ
                    </a>
                </nav>

                <!-- Actions & Dark Mode Toggle -->
                <div class="flex items-center gap-3">
                    <!-- Dark Mode Toggle Button -->
                    <button type="button" @click="toggleDarkMode()" class="p-2 rounded-xl text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500/20" title="Toggle Light/Dark Theme">
                        <i x-show="!darkMode" data-lucide="moon" class="w-4 h-4"></i>
                        <i x-show="darkMode" data-lucide="sun" class="w-4 h-4" style="display: none;"></i>
                    </button>

                    <!-- Sign In / Admin Portal CTA -->
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/admin/dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-sm hover:shadow transition-all transform active:scale-95">
                                <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                                <span>Dashboard</span>
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="hidden sm:inline-flex items-center px-4 py-2 text-sm font-semibold text-slate-700 dark:text-slate-200 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                                Sign In
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-white bg-slate-900 hover:bg-slate-800 dark:bg-indigo-600 dark:hover:bg-indigo-500 rounded-xl shadow-sm transition-all transform active:scale-95">
                                    <span>Get Started</span>
                                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                                </a>
                            @endif
                        @endauth
                    @else
                        <a href="{{ url('/admin') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-sm hover:shadow transition-all transform active:scale-95">
                            <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                            <span>Admin Portal</span>
                        </a>
                    @endif

                    <!-- Mobile Menu Hamburger Button -->
                    <button type="button" @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden p-2 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 focus:outline-none">
                        <i x-show="!mobileMenuOpen" data-lucide="menu" class="w-5 h-5"></i>
                        <i x-show="mobileMenuOpen" data-lucide="x" class="w-5 h-5" style="display: none;"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Dropdown Menu -->
        <div x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2" class="md:hidden border-b border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 px-4 pt-2 pb-4 space-y-2 shadow-lg" style="display: none;">
            <a href="{{ url('/') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800">
                Home
            </a>
            <a href="{{ url('/verify') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-base font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800">
                <i data-lucide="shield-check" class="w-4 h-4 text-emerald-500"></i>
                Verify Invoice
            </a>
            <a href="{{ url('/faq') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800">
                SST & LHDN FAQ
            </a>
            <div class="pt-2 border-t border-slate-200 dark:border-slate-800">
                <a href="{{ url('/admin') }}" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 text-white font-medium shadow">
                    <i data-lucide="lock" class="w-4 h-4"></i>
                    <span>Access Admin Portal</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content Slot -->
    <main class="flex-1">
        {{ $slot }}
    </main>

    <!-- Public Footer -->
    <footer class="border-t border-slate-200/80 dark:border-slate-800/80 bg-white dark:bg-slate-900 py-10 transition-colors">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-slate-900 dark:bg-indigo-600 text-white flex items-center justify-center">
                        <i data-lucide="receipt" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-slate-900 dark:text-white">IMS Malaysia</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Compliant with Sales Tax & Service Tax Act 2018 & LHDN MyInvois</p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-6 text-xs text-slate-500 dark:text-slate-400">
                    <span>© {{ date('Y') }} Nexa Digital Sdn. Bhd.</span>
                    <a href="#" class="hover:text-indigo-600 dark:hover:text-indigo-400">Privacy Policy (PDPA)</a>
                    <a href="#" class="hover:text-indigo-600 dark:hover:text-indigo-400">Terms of Service</a>
                    <span class="inline-flex items-center gap-1 text-emerald-600 dark:text-emerald-400">
                        <i data-lucide="check-circle-2" class="w-3.5 h-3.5"></i>
                        7-Year Statutory Retention Enforced
                    </span>
                </div>
            </div>
        </div>
    </footer>

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
