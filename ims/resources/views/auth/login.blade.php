<x-layouts.public title="Admin Portal Sign In – IMS Malaysia">
    <div class="min-h-[calc(100vh-16rem)] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="w-full max-w-md space-y-6">
            
            <!-- Header & Badge -->
            <div class="text-center space-y-2">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-indigo-600 text-white shadow-md shadow-indigo-500/20 mb-2">
                    <i data-lucide="lock" class="w-6 h-6"></i>
                </div>
                <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Admin Portal Sign In</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400">Access your Malaysian Invoice & e-Invoicing Management System</p>
            </div>

            <!-- Login Card -->
            <x-card class="shadow-xl" x-data="{
                email: '{{ old('email', 'admin@ims-malaysia.com') }}',
                password: '',
                remember: true,
                showPassword: false,
                loading: false
            }">
                @if (session('status'))
                    <div class="mb-4 p-3 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 text-xs font-semibold text-emerald-700 dark:text-emerald-300 flex items-center gap-2">
                        <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-500"></i>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-4 p-3 rounded-xl bg-rose-50 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-800 text-xs font-semibold text-rose-700 dark:text-rose-300 space-y-1">
                        @foreach ($errors->all() as $error)
                            <div class="flex items-center gap-1.5">
                                <i data-lucide="alert-circle" class="w-4 h-4 text-rose-500 flex-shrink-0"></i>
                                <span>{{ $error }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.login.store') }}" @submit="loading = true" class="space-y-4">
                    @csrf

                    <!-- Email Address Field -->
                    <x-input 
                        label="Work Email Address" 
                        name="email" 
                        type="email" 
                        x-model="email"
                        placeholder="name@company.com.my" 
                        icon="mail" 
                        required 
                    />

                    <!-- Password Field with Toggle Visibility -->
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                                Password <span class="text-rose-500">*</span>
                            </label>
                            <a href="{{ route('admin.forgot-password') }}" class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                                Forgot password?
                            </a>
                        </div>
                        <div class="relative rounded-xl shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                <i data-lucide="key-round" class="w-4 h-4"></i>
                            </div>
                            <input 
                                :type="showPassword ? 'text' : 'password'"
                                name="password"
                                x-model="password"
                                placeholder="••••••••••••"
                                required
                                class="block w-full text-sm rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 dark:focus:border-indigo-500 transition-colors py-2 pl-9 pr-10 font-mono"
                            >
                            <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                                <i x-show="!showPassword" data-lucide="eye" class="w-4 h-4"></i>
                                <i x-show="showPassword" data-lucide="eye-off" class="w-4 h-4" style="display: none;"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me & Security Notice -->
                    <div class="flex items-center justify-between pt-1">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="remember" value="1" x-model="remember" class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                            <span class="text-xs text-slate-600 dark:text-slate-400 font-medium">Remember on this device</span>
                        </label>
                        <span class="text-[11px] text-slate-400 flex items-center gap-1">
                            <i data-lucide="shield" class="w-3 h-3 text-emerald-500"></i>
                            256-bit SSL
                        </span>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-2">
                        <x-button type="submit" variant="primary" class="w-full shadow-md">
                            <span x-show="!loading" class="flex items-center gap-2">
                                <span>Sign In to Dashboard</span>
                                <i data-lucide="arrow-right" class="w-4 h-4"></i>
                            </span>
                            <span x-show="loading" class="flex items-center gap-2" style="display: none;">
                                <i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i>
                                <span>Authenticating...</span>
                            </span>
                        </x-button>
                    </div>
                </form>

                <!-- Demo Credentials Quick Fill -->
                <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800 text-xs text-slate-500 dark:text-slate-400 space-y-2">
                    <p class="font-bold text-slate-700 dark:text-slate-300 text-[11px] uppercase tracking-wider">Default Seeded Accounts:</p>
                    <div class="flex items-center justify-between p-2 rounded-lg bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 font-mono text-[11px] cursor-pointer" @click="email = 'admin@ims-malaysia.com'; password = 'password123';">
                        <span>admin@ims-malaysia.com (pass: password123)</span>
                        <x-badge variant="indigo" size="sm">Admin</x-badge>
                    </div>
                    <div class="flex items-center justify-between p-2 rounded-lg bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 font-mono text-[11px] cursor-pointer" @click="email = 'accounts@ims-malaysia.com'; password = 'password123';">
                        <span>accounts@ims-malaysia.com (pass: password123)</span>
                        <x-badge variant="emerald" size="sm">Accounts</x-badge>
                    </div>
                </div>
            </x-card>

            <!-- Back to Home Link -->
            <div class="text-center">
                <a href="{{ url('/') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                    <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                    <span>Back to IMS Malaysia Landing Page</span>
                </a>
            </div>
        </div>
    </div>
</x-layouts.public>
