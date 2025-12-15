<script setup>
import { Head, Link } from '@inertiajs/vue3';

const logoUrl = '/images/enotpili.png'; 
defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
});
</script>

<template>
    <Head title="Welcome to E-PILI" />

    <div class="min-h-screen bg-black text-white font-sans selection:bg-cyan-500/30 selection:text-cyan-200 overflow-x-hidden relative">
        
        <div class="fixed inset-0 z-0 pointer-events-none">
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-[#0f172a] via-[#020617] to-black"></div>
            
            <div class="absolute inset-0 opacity-20 bg-[url('https://www.transparenttextures.com/patterns/stardust.png')]"></div>

            <div class="absolute top-0 left-0 w-full h-[60vh] overflow-hidden opacity-40 mix-blend-screen pointer-events-none">
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

                <svg class="absolute w-[200%] h-full animate-wave-medium top-10 left-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1600 200" preserveAspectRatio="none">
                    <path d="M0,100 C300,60 900,140 1600,100 L1600,0 L0,0 Z" fill="url(#gradPurple)" fill-opacity="0.3"></path>
                    <defs>
                        <linearGradient id="gradPurple" x1="0%" y1="0%" x2="100%" y2="0%">
                            <stop offset="0%" style="stop-color:#8b5cf6;stop-opacity:0" />
                            <stop offset="50%" style="stop-color:#a855f7;stop-opacity:0.7" />
                            <stop offset="100%" style="stop-color:#8b5cf6;stop-opacity:0" />
                        </linearGradient>
                    </defs>
                </svg>

                <svg class="absolute w-[200%] h-full animate-wave-fast top-5 left-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1600 200" preserveAspectRatio="none">
                    <path d="M0,80 C600,130 1200,30 1600,80 L1600,0 L0,0 Z" fill="url(#gradCyan)" fill-opacity="0.2"></path>
                    <defs>
                        <linearGradient id="gradCyan" x1="0%" y1="0%" x2="100%" y2="0%">
                            <stop offset="0%" style="stop-color:#06b6d4;stop-opacity:0" />
                            <stop offset="50%" style="stop-color:#22d3ee;stop-opacity:0.6" />
                            <stop offset="100%" style="stop-color:#06b6d4;stop-opacity:0" />
                        </linearGradient>
                    </defs>
                </svg>
            </div>
        </div>

        <nav class="relative z-50 px-6 py-6 flex justify-between items-center max-w-7xl mx-auto">
            <div class="flex items-center gap-4 group cursor-default">
                <div class="leading-tight hidden sm:block">
                    <h5 class="text-[13px] font-bold tracking-tight text-white group-hover:text-blue-200 transition-colors">Justine Villarosa</h5>
                    <p class="text-[9px] text-blue-400 uppercase tracking-[0.2em] font-bold">Developer</p>
                </div>
                <div class="relative">
                    <div class="absolute inset-0 bg-blue-500 blur-xl opacity-0 group-hover:opacity-40 transition-opacity duration-500 rounded-full"></div>
                    <img :src="logoUrl" alt="ENOT PILI Logo" class="h-28 relative z-10 drop-shadow-2xl"></img>
                </div>
            </div>

            <div v-if="canLogin" class="flex gap-4">
                <Link 
                    v-if="$page.props.auth.user" 
                    :href="route('dashboard')" 
                    class="px-6 py-2.5 bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/10 rounded-full font-bold text-sm transition-all flex items-center gap-2 group"
                >
                    <span>Enter Command Center</span>
                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                </Link>

                <template v-else>
                    <Link 
                        :href="route('login')" 
                        class="px-6 py-2.5 text-slate-300 hover:text-white font-medium text-sm transition-colors"
                    >
                        Sign In
                    </Link>

                    <Link 
                        v-if="canRegister" 
                        :href="route('register')" 
                        class="px-6 py-2.5 bg-white text-black hover:bg-gray-100 rounded-full font-bold text-sm shadow-[0_0_20px_rgba(255,255,255,0.3)] transition-all hover:scale-105"
                    >
                        Get Started
                    </Link>
                </template>
            </div>
        </nav>

        <main class="relative z-10 flex flex-col items-center justify-center text-center px-4 mt-16 md:mt-24 mb-32">
            
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-blue-500/10 border border-blue-500/20 backdrop-blur-md mb-8 animate-fade-in shadow-[0_0_15px_rgba(59,130,246,0.2)]">
                <span class="relative flex h-2 w-2">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"></span>
                </span>
                <span class="text-blue-300 text-[10px] font-bold tracking-[0.2em] uppercase">Online Portal System</span>
            </div>

            <h1 class="text-5xl md:text-8xl font-black tracking-tighter mb-6 max-w-5xl leading-tight animate-slide-up drop-shadow-2xl">
                Governance <br class="hidden md:block" />
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 via-indigo-400 to-cyan-400 animate-gradient-text">
                    E-Portal System
                </span>
            </h1>

            <p class="text-lg md:text-xl text-slate-400 max-w-2xl mb-12 leading-relaxed animate-slide-up font-light" style="animation-delay: 0.1s;">
                The future of public service is digital. Request documents, pay bills, and participate in governance through a secure, unified command center.
            </p>

            <div class="flex flex-col sm:flex-row gap-4 animate-slide-up" style="animation-delay: 0.2s;">
                <Link 
                    :href="route('register')" 
                    class="px-8 py-4 bg-blue-600 hover:bg-blue-500 text-white rounded-full font-bold text-lg shadow-[0_0_30px_rgba(37,99,235,0.4)] transition-all hover:-translate-y-1 flex items-center justify-center gap-2"
                >
                    Create Citizen Account
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                </Link>
                <Link 
                    :href="route('login')" 
                    class="px-8 py-4 bg-white/5 backdrop-blur-xl border border-white/10 text-white rounded-full font-bold text-lg hover:bg-white/10 transition-all hover:-translate-y-1"
                >
                    Access Portal
                </Link>
            </div>

            <div class="mt-32 grid grid-cols-1 md:grid-cols-3 gap-6 w-full max-w-6xl animate-fade-in-up" style="animation-delay: 0.4s;">
                
                <div class="group relative bg-white/5 backdrop-blur-xl border border-white/10 p-8 rounded-[2rem] hover:bg-white/10 transition-all duration-500 hover:-translate-y-2 hover:shadow-2xl hover:shadow-blue-500/10 overflow-hidden">
                    <div class="absolute -right-10 -top-10 w-32 h-32 bg-blue-500/20 rounded-full blur-[50px] group-hover:bg-blue-500/40 transition-colors"></div>
                    <div class="text-4xl mb-6 bg-blue-500/20 w-14 h-14 rounded-2xl flex items-center justify-center text-blue-400 group-hover:scale-110 transition-transform duration-500 shadow-lg shadow-blue-500/20">📄</div>
                    <h3 class="text-xl font-bold mb-2 text-white">E-Documents</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">Instant request processing for Barangay Clearances and Permits with digital QR verification.</p>
                </div>

                <div class="group relative bg-white/5 backdrop-blur-xl border border-white/10 p-8 rounded-[2rem] hover:bg-white/10 transition-all duration-500 hover:-translate-y-2 hover:shadow-2xl hover:shadow-red-500/10 overflow-hidden">
                    <div class="absolute -right-10 -top-10 w-32 h-32 bg-red-500/20 rounded-full blur-[50px] group-hover:bg-red-500/40 transition-colors"></div>
                    <div class="text-4xl mb-6 bg-red-500/20 w-14 h-14 rounded-2xl flex items-center justify-center text-red-400 group-hover:scale-110 transition-transform duration-500 shadow-lg shadow-red-500/20">🚨</div>
                    <h3 class="text-xl font-bold mb-2 text-white">Rapid Response</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">Direct line to emergency services. Report community incidents with geo-tagging.</p>
                </div>

                <div class="group relative bg-white/5 backdrop-blur-xl border border-white/10 p-8 rounded-[2rem] hover:bg-white/10 transition-all duration-500 hover:-translate-y-2 hover:shadow-2xl hover:shadow-emerald-500/10 overflow-hidden">
                    <div class="absolute -right-10 -top-10 w-32 h-32 bg-emerald-500/20 rounded-full blur-[50px] group-hover:bg-emerald-500/40 transition-colors"></div>
                    <div class="text-4xl mb-6 bg-emerald-500/20 w-14 h-14 rounded-2xl flex items-center justify-center text-emerald-400 group-hover:scale-110 transition-transform duration-500 shadow-lg shadow-emerald-500/20">💳</div>
                    <h3 class="text-xl font-bold mb-2 text-white">Secure Payments</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">Hassle-free payment for utilities and taxes through our encrypted virtual gateway.</p>
                </div>

            </div>

        </main>

        <footer class="relative z-10 border-t border-white/10 bg-black/60 backdrop-blur-md">
            <div class="max-w-7xl mx-auto px-6 py-12 flex flex-col md:flex-row justify-between items-center gap-6">
                <div class="text-slate-500 text-sm font-medium">
                    &copy; 2025 Provincial Government of Camarines Sur.
                </div>
                <div class="flex gap-8 text-sm font-medium">
                    <a href="#" class="text-slate-500 hover:text-white transition-colors">Privacy</a>
                    <a href="#" class="text-slate-500 hover:text-white transition-colors">Terms</a>
                    <a href="#" class="text-slate-500 hover:text-white transition-colors">Support</a>
                </div>
            </div>
        </footer>

    </div>
</template>

<style scoped>
/* 🌊 WAVE ANIMATION (Same as Dashboard) */
@keyframes waveSlow {
    0% { transform: translateX(0); }
    100% { transform: translateX(-50%); }
}
.animate-wave-slow { animation: waveSlow 30s linear infinite; }

@keyframes waveMedium {
    0% { transform: translateX(-25%); }
    100% { transform: translateX(-75%); }
}
.animate-wave-medium { animation: waveMedium 20s linear infinite; }

@keyframes waveFast {
    0% { transform: translateX(0); }
    100% { transform: translateX(-50%); }
}
.animate-wave-fast { animation: waveFast 15s linear infinite; }


/* ✨ OTHER ANIMATIONS */
@keyframes pulse-slow {
    0%, 100% { opacity: 0.3; transform: scale(1); }
    50% { opacity: 0.5; transform: scale(1.1); }
}
.animate-pulse-slow { animation: pulse-slow 8s ease-in-out infinite; }

@keyframes gradient-text {
    0% { background-position: 0% 50%; }
    100% { background-position: 100% 50%; }
}
.animate-gradient-text {
    background-size: 200% auto;
    animation: gradient-text 5s ease infinite alternate;
}

@keyframes slide-up {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-slide-up { animation: slide-up 1s cubic-bezier(0.16, 1, 0.3, 1) both; }

.animate-fade-in { animation: slide-up 1s ease-out; }
.animate-fade-in-up { animation: slide-up 1s ease-out both; }
</style>