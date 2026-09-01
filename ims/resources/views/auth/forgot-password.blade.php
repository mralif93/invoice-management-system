<x-layouts.public title="Reset Password – IMS Malaysia Admin">
    <div class="min-h-[calc(100vh-16rem)] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="w-full max-w-md space-y-6">
            
            <!-- Header & Badge -->
            <div class="text-center space-y-2">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-amber-500 text-white shadow-md shadow-amber-500/20 mb-2">
                    <i data-lucide="key" class="w-6 h-6"></i>
                </div>
                <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Forgot Password</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400">Enter your registered work email to receive password reset instructions</p>
            </div>

            <!-- Password Reset Card -->
            <x-card class="shadow-xl" x-data="{
                email: '',
                sent: false,
                loading: false,
                submitReset() {
                    if (!this.email) return;
                    this.loading = true;
                    setTimeout(() => {
                        this.loading = false;
                        this.sent = true;
                    }, 500);
                }
            }">
                <!-- Success Message when Sent -->
                <div x-show="sent" x-transition class="space-y-4 text-center py-4" style="display: none;">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mx-auto">
                        <i data-lucide="check-circle" class="w-7 h-7"></i>
                    </div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">Reset Link Dispatched</h3>
                    <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                        We have sent password reset instructions to <span class="font-mono font-bold text-slate-900 dark:text-white" x-text="email"></span>. Please check your inbox or spam folder.
                    </p>
                    <div class="pt-2">
                        <a href="{{ route('admin.login') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white font-bold text-xs hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                            <i data-lucide="arrow-left" class="w-4 h-4"></i>
                            <span>Return to Sign In</span>
                        </a>
                    </div>
                </div>

                <!-- Reset Request Form -->
                <form x-show="!sent" @submit.prevent="submitReset()" class="space-y-4">
                    <x-input 
                        label="Registered Work Email" 
                        name="email" 
                        type="email" 
                        x-model="email"
                        placeholder="admin@ims-malaysia.com" 
                        icon="mail" 
                        required 
                    />

                    <div class="pt-2">
                        <x-button type="submit" variant="primary" class="w-full shadow-md">
                            <span x-show="!loading" class="flex items-center gap-2">
                                <span>Send Reset Instructions</span>
                                <i data-lucide="send" class="w-4 h-4"></i>
                            </span>
                            <span x-show="loading" class="flex items-center gap-2" style="display: none;">
                                <i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i>
                                <span>Sending...</span>
                            </span>
                        </x-button>
                    </div>
                </form>

                <!-- Back to Sign In Link in Footer -->
                <div x-show="!sent" class="mt-5 pt-4 border-t border-slate-100 dark:border-slate-800 text-center">
                    <a href="{{ route('admin.login') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                        <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                        <span>Remember password? Back to Sign In</span>
                    </a>
                </div>
            </x-card>

            <!-- Need Help Notice -->
            <div class="p-3.5 rounded-xl bg-slate-100/70 dark:bg-slate-800/50 border border-slate-200/80 dark:border-slate-700/80 text-center text-[11px] text-slate-500 dark:text-slate-400">
                <span>Account locked? Contact your organization IT lead or System Admin.</span>
            </div>
        </div>
    </div>
</x-layouts.public>
