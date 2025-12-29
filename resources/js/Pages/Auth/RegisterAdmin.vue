<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    phone_number: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register.admin'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Create Admin Account" />

    <div class="min-h-screen w-full flex items-center justify-center p-4 sm:p-6 bg-navy-blue relative overflow-hidden font-sans selection:bg-red-500/30 selection:text-red-200">
        
        <!-- Enhanced Background -->
        <div class="fixed inset-0 z-0 pointer-events-none overflow-hidden">
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-[#0f172a] via-[#020617] to-black"></div>
            <div class="absolute inset-0 opacity-20 bg-[url('https://www.transparenttextures.com/patterns/stardust.png')]"></div>

            <!-- Animated Waves -->
            <div class="absolute top-0 left-0 w-full h-[60vh] overflow-hidden opacity-30 mix-blend-screen">
                <svg class="absolute w-[200%] h-full animate-wave-slow top-0 left-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1600 200" preserveAspectRatio="none">
                    <path d="M0,100 C400,150 800,50 1600,100 L1600,0 L0,0 Z" fill="url(#gradRed)" fill-opacity="0.4"></path>
                    <defs>
                        <linearGradient id="gradRed" x1="0%" y1="0%" x2="100%" y2="0%">
                            <stop offset="0%" style="stop-color:#ef4444;stop-opacity:0" />
                            <stop offset="50%" style="stop-color:#ef4444;stop-opacity:0.8" />
                            <stop offset="100%" style="stop-color:#ef4444;stop-opacity:0" />
                        </linearGradient>
                    </defs>
                </svg>
            </div>

            <!-- Floating Orbs -->
            <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-red-500/10 rounded-full blur-3xl animate-float"></div>
            <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-orange-500/10 rounded-full blur-3xl animate-float-delayed"></div>
        </div>

        <div class="relative z-10 w-full max-w-[1000px] bg-black/40 backdrop-blur-2xl border border-white/10 rounded-[2.5rem] shadow-2xl overflow-hidden flex flex-col md:flex-row animate-scale-in">
            
            <!-- Left Sidebar -->
            <div class="hidden md:flex md:w-4/12 relative p-10 flex-col justify-between overflow-hidden bg-white/5 border-r border-white/5">
                <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-10"></div>
                <div class="absolute bottom-[-20%] right-[-50%] w-[150%] h-[150%] bg-gradient-to-tl from-red-500/10 to-transparent rounded-full animate-pulse-slow blur-3xl"></div>

                <div class="relative z-10">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-red-500/10 border border-red-500/20 backdrop-blur-md mb-6 animate-slide-in-down">
                        <span class="relative flex h-1.5 w-1.5">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-red-500"></span>
                        </span>
                        <span class="text-red-300 text-[10px] font-bold tracking-[0.2em] uppercase">RESTRICTED ACCESS</span>
                    </div>
                    <h1 class="text-3xl font-bold text-white tracking-tight leading-tight animate-fade-in-left">
                        Admin <br />
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-red-400 to-orange-400">Registration</span>
                    </h1>
                </div>

                <div class="relative z-10 space-y-3 mt-8">
                    <div class="dashboard-info-card" style="animation-delay: 0.1s;">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-red-500/20 to-red-600/20 flex-shrink-0 flex items-center justify-center text-lg shadow-lg">
                            ⚠️
                        </div>
                        <div>
                            <h3 class="font-semibold text-white text-sm">Privileged Account</h3>
                            <p class="text-[11px] text-slate-400 mt-0.5">Full system access and control</p>
                        </div>
                    </div>
                    <div class="dashboard-info-card" style="animation-delay: 0.2s;">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-orange-500/20 to-orange-600/20 flex-shrink-0 flex items-center justify-center text-lg shadow-lg">
                            🔐
                        </div>
                        <div>
                            <h3 class="font-semibold text-white text-sm">Protected Setup</h3>
                            <p class="text-[11px] text-slate-400 mt-0.5">This page is for authorized personnel only</p>
                        </div>
                    </div>
                </div>

                <div class="relative z-10 mt-auto animate-fade-in-up" style="animation-delay: 0.3s;">
                    <p class="text-[10px] text-slate-500 border-t border-white/10 pt-4">
                        ⚡ Admin accounts have elevated privileges. Use responsibly.
                    </p>
                </div>
            </div>

            <!-- Right Form Section -->
            <div class="w-full md:w-8/12 p-6 sm:p-8 md:p-12 relative overflow-y-auto custom-scrollbar max-h-[90vh]">
                
                <div class="max-w-lg mx-auto">
                    <!-- Mobile Header -->
                    <div class="md:hidden text-center mb-6 animate-scale-in">
                        <div class="w-16 h-16 bg-gradient-to-br from-red-600 to-orange-600 rounded-2xl mx-auto flex items-center justify-center shadow-lg shadow-red-500/30 mb-4">
                            <span class="text-3xl">👨‍💼</span>
                        </div>
                        <h2 class="text-xl font-bold text-white tracking-wide">ADMIN REGISTRATION</h2>
                    </div>

                    <div class="mb-8 text-center md:text-left animate-fade-in-up">
                        <h2 class="text-3xl font-bold text-white tracking-tight mb-2">Create Admin Account</h2>
                        <p class="text-slate-400 text-sm">This account will have full administrative privileges.</p>
                    </div>

                    <form @submit.prevent="submit" class="space-y-5 animate-fade-in-up" style="animation-delay: 0.1s;">
                        
                        <!-- Full Name & Phone -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-5">
                            <div class="space-y-1.5">
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Full Name</label>
                                <div class="relative group">
                                    <span class="input-icon">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    </span>
                                    <input v-model="form.name" type="text" required placeholder="Admin Name" class="input-field" />
                                </div>
                            </div>

                            <div class="space-y-1.5">
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Phone Number</label>
                                <div class="relative group">
                                    <span class="input-icon">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                    </span>
                                    <input v-model="form.phone_number" type="text" required placeholder="0912..." class="input-field" />
                                </div>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Email Address</label>
                            <div class="relative group">
                                <span class="input-icon text-lg">📧</span>
                                <input v-model="form.email" type="email" required placeholder="admin@epili.gov" class="input-field" />
                            </div>
                            <div v-if="form.errors.email" class="text-red-400 text-xs ml-1 mt-1">{{ form.errors.email }}</div>
                        </div>

                        <!-- Password -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-5">
                            <div class="space-y-1.5">
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Password</label>
                                <input v-model="form.password" type="password" required class="input-field-simple" />
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Confirm Password</label>
                                <input v-model="form.password_confirmation" type="password" required class="input-field-simple" />
                            </div>
                        </div>
                        <div v-if="form.errors.password" class="text-red-400 text-xs ml-1">{{ form.errors.password }}</div>

                        <div class="pt-2">
                            <button :disabled="form.processing" class="btn-admin-submit group">
                                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                                <div class="flex items-center justify-center gap-2 relative z-10">
                                    <span v-if="!form.processing">Create Admin Account</span>
                                    <span v-else>Creating...</span>
                                    <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                    </svg>
                                </div>
                            </button>
                        </div>

                        <div class="text-center pb-4 md:pb-0">
                            <p class="text-xs text-slate-500">
                                Return to 
                                <Link :href="route('login')" class="text-red-400 font-bold hover:text-red-300 transition-colors hover:underline">
                                    Login Page
                                </Link>
                            </p>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');

* {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'SF Pro Display', sans-serif;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}

/* Copy animations and styles from Register.vue */
@keyframes waveSlow { 
    0% { transform: translateX(0); } 
    100% { transform: translateX(-50%); } 
}
.animate-wave-slow { animation: waveSlow 30s linear infinite; }

@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-20px); }
}
.animate-float { animation: float 6s ease-in-out infinite; }

@keyframes float-delayed {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(20px); }
}
.animate-float-delayed { animation: float-delayed 8s ease-in-out infinite; }

@keyframes pulse-slow { 
    0%, 100% { opacity: 0.2; transform: scale(1); } 
    50% { opacity: 0.4; transform: scale(1.1); } 
}
.animate-pulse-slow { animation: pulse-slow 8s ease-in-out infinite; }

@keyframes scale-in { 
    from { opacity: 0; transform: scale(0.95); } 
    to { opacity: 1; transform: scale(1); } 
}
.animate-scale-in { animation: scale-in 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) forwards; }

@keyframes fade-in-up { 
    from { opacity: 0; transform: translateY(30px); } 
    to { opacity: 1; transform: translateY(0); } 
}
.animate-fade-in-up { animation: fade-in-up 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) forwards; }

@keyframes fade-in-left { 
    from { opacity: 0; transform: translateX(-30px); } 
    to { opacity: 1; transform: translateX(0); } 
}
.animate-fade-in-left { animation: fade-in-left 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) forwards; }

@keyframes slide-in-down { 
    from { opacity: 0; transform: translateY(-20px); } 
    to { opacity: 1; transform: translateY(0); } 
}
.animate-slide-in-down { animation: slide-in-down 0.6s cubic-bezier(0.2, 0.8, 0.2, 1) forwards; }

.dashboard-info-card {
    @apply flex items-start gap-4 p-4 rounded-2xl;
    @apply bg-black/40 backdrop-blur-xl border border-white/10;
    @apply hover:bg-white/5 transition-all duration-300 cursor-default;
    @apply animate-fade-in-up;
}

.input-icon {
    @apply absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none;
    @apply text-slate-500 group-focus-within:text-red-400 transition-colors;
}

.input-field {
    @apply w-full bg-white/5 border border-white/10 rounded-xl py-3 pl-10 pr-4;
    @apply text-sm text-white placeholder-slate-500;
    @apply focus:outline-none focus:ring-2 focus:ring-red-500/50 focus:border-transparent;
    @apply transition-all hover:bg-white/10 hover:border-white/20;
}

.input-field-simple {
    @apply w-full bg-white/5 border border-white/10 rounded-xl py-3 px-4;
    @apply text-sm text-white focus:outline-none focus:ring-2 focus:ring-red-500/50;
    @apply focus:border-transparent transition-all hover:bg-white/10 hover:border-white/20;
}

.btn-admin-submit {
    @apply relative w-full bg-gradient-to-r from-red-600 to-orange-600;
    @apply hover:from-red-500 hover:to-orange-500 text-white font-bold py-3.5 rounded-xl;
    @apply shadow-xl shadow-red-500/30 transition-all hover:scale-[1.02];
    @apply overflow-hidden;
}

.custom-scrollbar::-webkit-scrollbar { 
    width: 6px; 
}
.custom-scrollbar::-webkit-scrollbar-track { 
    background: transparent; 
}
.custom-scrollbar::-webkit-scrollbar-thumb { 
    background: rgba(255, 255, 255, 0.1); 
    border-radius: 10px; 
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.2);
}

html {
    scroll-behavior: smooth;
}
</style>