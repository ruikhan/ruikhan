<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Log in" />

    <div class="min-h-screen w-full flex items-center justify-center p-4 sm:p-6 bg-navy-blue relative overflow-hidden font-sans selection:bg-blue-500/30 selection:text-blue-200">
        
        <!-- Enhanced Background - Matching Dashboard -->
        <div class="fixed inset-0 z-0 pointer-events-none overflow-hidden">
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-[#0f172a] via-[#020617] to-black"></div>
            <div class="absolute inset-0 opacity-20 bg-[url('https://www.transparenttextures.com/patterns/stardust.png')]"></div>

            <!-- Animated Waves -->
            <div class="absolute top-0 left-0 w-full h-[60vh] overflow-hidden opacity-30 mix-blend-screen">
                <svg class="absolute w-[200%] h-full animate-wave-slow top-0 left-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1600 200" preserveAspectRatio="none">
                    <path d="M0,100 C400,150 800,50 1600,100 L1600,0 L0,0 Z" fill="url(#gradBlue)" fill-opacity="0.4"></path>
                    <defs>
                        <linearGradient id="gradBlue" x1="0%" y1="0%" x2="100%" y2="0%">
                            <stop offset="0%" style="stop-color:#3b82f6;stop-opacity:0" />
                            <stop offset="50%" style="stop-color:#3b82f6;stop-opacity:0.8" />
                            <stop offset="100%" style="stop-color:#3b82f6;stop-opacity:0" />
                        </linearGradient>
                    </defs>
                </svg>
            </div>

            <!-- Floating Orbs -->
            <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl animate-float"></div>
            <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-purple-500/10 rounded-full blur-3xl animate-float-delayed"></div>
        </div>

        <div class="relative z-10 w-full max-w-[1100px] bg-black/40 backdrop-blur-2xl border border-white/10 rounded-[2.5rem] shadow-2xl overflow-hidden flex flex-col md:flex-row animate-scale-in">
            
            <!-- Left Sidebar -->
            <div class="hidden md:flex md:w-5/12 relative p-10 flex-col justify-between overflow-hidden bg-white/5 border-r border-white/5">
                <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-10"></div>
                <div class="absolute top-[-50%] left-[-50%] w-[200%] h-[200%] bg-gradient-to-tr from-blue-500/10 to-transparent rounded-full animate-slow-spin blur-3xl"></div>

                <div class="relative z-10">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/10 border border-blue-500/20 backdrop-blur-md mb-6 animate-slide-in-down">
                        <span class="relative flex h-1.5 w-1.5">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-blue-500"></span>
                        </span>
                        <span class="text-blue-300 text-[10px] font-bold tracking-[0.2em] uppercase">Secure Access</span>
                    </div>
                    <h1 class="text-3xl font-bold text-white tracking-tight leading-tight animate-fade-in-left">
                        Welcome to <br />
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-indigo-400">E-PILI Portal</span>
                    </h1>
                </div>

                <div class="relative z-10 space-y-3 mt-8">
                    <div class="dashboard-info-card group" style="animation-delay: 0.1s;">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500/20 to-blue-600/20 flex items-center justify-center text-lg shadow-lg group-hover:scale-110 transition-transform">
                            🏛️
                        </div>
                        <div>
                            <h3 class="font-semibold text-white text-sm">Provincial Capital</h3>
                            <p class="text-[11px] text-slate-400">Governance center of CamSur</p>
                        </div>
                    </div>
                    <div class="dashboard-info-card group" style="animation-delay: 0.2s;">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500/20 to-emerald-600/20 flex items-center justify-center text-lg shadow-lg group-hover:scale-110 transition-transform">
                            🌾
                        </div>
                        <div>
                            <h3 class="font-semibold text-white text-sm">Agricultural Hub</h3>
                            <p class="text-[11px] text-slate-400">Premiere rice & pili production</p>
                        </div>
                    </div>
                </div>

                <div class="relative z-10 mt-auto pt-6 animate-fade-in-up" style="animation-delay: 0.3s;">
                    <div class="relative bg-gradient-to-r from-blue-600/20 to-indigo-600/20 border border-blue-500/30 rounded-2xl p-5 overflow-hidden hover:scale-[1.02] transition-transform">
                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/5 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                        <h3 class="text-white font-bold text-sm mb-1 relative z-10">E-GOVERNANCE LIVE</h3>
                        <p class="text-blue-200/70 text-[10px] relative z-10">Request documents online 24/7.</p>
                    </div>
                </div>
            </div>

            <!-- Right Form Section -->
            <div class="w-full md:w-7/12 p-6 sm:p-8 md:p-12 flex flex-col justify-center relative">
                
                <div class="max-w-sm mx-auto w-full">
                    
                    <!-- Mobile Header -->
                    <div class="md:hidden text-center mb-8 animate-scale-in">
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-2xl mx-auto flex items-center justify-center shadow-lg shadow-blue-500/30 mb-4">
                            <span class="text-3xl">🏛️</span>
                        </div>
                        <h2 class="text-xl font-bold text-white tracking-wide">E-PILI PORTAL</h2>
                    </div>

                    <div class="mb-8 text-center md:text-left animate-fade-in-up">
                        <h3 class="text-3xl font-bold text-white tracking-tight mb-2">Welcome Back</h3>
                        <p class="text-slate-400 text-sm">Please enter your credentials to continue.</p>
                    </div>

                    <form @submit.prevent="submit" class="space-y-5 animate-fade-in-up" style="animation-delay: 0.1s;">
                        
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Email Address</label>
                            <div class="relative group">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500 group-focus-within:text-blue-400 transition-colors text-lg">
                                    📧
                                </span>
                                <input id="email" 
                                       type="email" 
                                       v-model="form.email" 
                                       required 
                                       placeholder="juan@example.com"
                                       class="input-field" />
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Password</label>
                            <div class="relative group">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500 group-focus-within:text-blue-400 transition-colors text-lg">
                                    🔒
                                </span>
                                <input id="password" 
                                       type="password" 
                                       v-model="form.password" 
                                       required 
                                       placeholder="••••••••"
                                       class="input-field" />
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-1">
                            <label class="flex items-center cursor-pointer group">
                                <div class="relative">
                                    <input type="checkbox" v-model="form.remember" class="peer sr-only">
                                    <div class="w-4 h-4 border-2 border-slate-600 rounded bg-transparent peer-checked:bg-blue-500 peer-checked:border-blue-500 transition-all"></div>
                                    <svg class="absolute inset-0 w-4 h-4 text-white opacity-0 peer-checked:opacity-100 pointer-events-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                </div>
                                <span class="ml-2 text-xs text-slate-400 group-hover:text-slate-300 transition-colors">Remember me</span>
                            </label>

                            <Link v-if="canResetPassword" :href="route('password.request')" class="text-xs text-blue-400 hover:text-blue-300 font-medium transition-colors hover:underline">
                                Forgot Password?
                            </Link>
                        </div>

                        <button :disabled="form.processing" class="btn-primary-submit group">
                            <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                            <div class="flex items-center justify-center gap-2 relative z-10">
                                <span v-if="!form.processing">Sign In</span>
                                <span v-else>Signing In...</span>
                                <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                </svg>
                            </div>
                        </button>
                    </form>

                    <div class="mt-8 text-center animate-fade-in-up" style="animation-delay: 0.2s;">
                        <div class="relative flex py-2 items-center mb-6">
                            <div class="flex-grow border-t border-white/10"></div>
                            <span class="flex-shrink-0 mx-4 text-slate-500 text-[10px] uppercase tracking-widest">Or access with</span>
                            <div class="flex-grow border-t border-white/10"></div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-8">
                            <button class="social-btn group">
                                <img src="https://www.svgrepo.com/show/475656/google-color.svg" class="w-5 h-5 opacity-80 group-hover:opacity-100 transition-all" alt="Google">
                                <span class="font-medium text-slate-300 text-xs group-hover:text-white transition-colors">Google</span>
                            </button>
                            <button class="social-btn group">
                                <img src="https://www.svgrepo.com/show/475647/facebook-color.svg" class="w-5 h-5 opacity-80 group-hover:opacity-100 transition-all" alt="Facebook">
                                <span class="font-medium text-slate-300 text-xs group-hover:text-white transition-colors">Facebook</span>
                            </button>
                        </div>

                        <p class="text-xs text-slate-500">
                            No account yet? 
                            <Link :href="route('register')" class="text-blue-400 font-bold hover:text-blue-300 transition-colors hover:underline">
                                Create an account
                            </Link>
                        </p>
                    </div>
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

/* Animations */
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

@keyframes slow-spin { 
    from { transform: rotate(0deg); } 
    to { transform: rotate(360deg); } 
}
.animate-slow-spin { animation: slow-spin 20s linear infinite; }

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

/* Dashboard Style Components */
.dashboard-info-card {
    @apply flex items-center gap-4 p-4 rounded-2xl;
    @apply bg-black/40 backdrop-blur-xl border border-white/10;
    @apply hover:bg-white/5 transition-all duration-300 cursor-default;
    @apply animate-fade-in-up;
}

.input-field {
    @apply w-full bg-white/5 border border-white/10 rounded-xl py-3 pl-10 pr-4;
    @apply text-sm text-white placeholder-slate-500;
    @apply focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-transparent;
    @apply transition-all hover:bg-white/10 hover:border-white/20;
}

.btn-primary-submit {
    @apply relative w-full bg-gradient-to-r from-blue-600 to-indigo-600;
    @apply hover:from-blue-500 hover:to-indigo-500 text-white font-bold py-3.5 rounded-xl;
    @apply shadow-xl shadow-blue-500/30 transition-all hover:scale-[1.02];
    @apply overflow-hidden;
}

.social-btn {
    @apply bg-white/5 border border-white/10 rounded-xl py-3 hover:bg-white/10;
    @apply hover:border-white/20 transition-all flex items-center justify-center gap-2;
    @apply hover:scale-[1.02];
}

html {
    scroll-behavior: smooth;
}
</style>