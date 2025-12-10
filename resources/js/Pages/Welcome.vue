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

    <div class="min-h-screen bg-slate-900 text-white font-sans selection:bg-blue-500 selection:text-white overflow-x-hidden">
        
        <div class="fixed inset-0 z-0 pointer-events-none">
            <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(#4f46e5 1px, transparent 1px); background-size: 40px 40px;"></div>
            <div class="absolute top-0 left-1/4 w-[500px] h-[500px] bg-blue-600/30 rounded-full blur-[120px] animate-pulse-slow"></div>
            <div class="absolute bottom-0 right-1/4 w-[600px] h-[600px] bg-purple-600/20 rounded-full blur-[120px] animate-pulse-slow" style="animation-delay: 2s;"></div>
        </div>

        <nav class="relative z-50 px-6 py-6 flex justify-between items-center max-w-7xl mx-auto">
            <div class="flex items-center gap-3">
                <img :src="logoUrl" alt="ENOT PILI Logo" class="h-28"></img>
                <br>
                <div class="leading-tight">
                    <h1 class="font-bold text-xl tracking-tight">Justine Villarosa</h1>
                    <p class="text-[10px] text-blue-300 uppercase tracking-widest font-semibold">Developer</p>
                </div>
            </div>

            <div v-if="canLogin" class="flex gap-4">
                <Link 
                    v-if="$page.props.auth.user" 
                    :href="route('dashboard')" 
                    class="px-6 py-2.5 bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/10 rounded-xl font-bold text-sm transition-all"
                >
                    Go to Dashboard
                </Link>

                <template v-else>
                    <Link 
                        :href="route('login')" 
                        class="px-6 py-2.5 text-slate-300 hover:text-white font-bold text-sm transition-colors"
                    >
                        Log in
                    </Link>

                    <Link 
                        v-if="canRegister" 
                        :href="route('register')" 
                        class="px-6 py-2.5 bg-blue-600 hover:bg-blue-500 text-white rounded-xl font-bold text-sm shadow-lg shadow-blue-600/30 transition-all hover:scale-105"
                    >
                        Get Started
                    </Link>
                </template>
            </div>
        </nav>

        <main class="relative z-10 flex flex-col items-center justify-center text-center px-4 mt-10 md:mt-20 mb-32">
            
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-blue-500/10 border border-blue-500/20 backdrop-blur-md mb-8 animate-fade-in">
                <span class="relative flex h-2 w-2">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"></span>
                </span>
                <span class="text-blue-200 text-xs font-bold tracking-widest uppercase">System Online v2.0</span>
            </div>

            <h1 class="text-5xl md:text-7xl font-bold tracking-tight mb-6 max-w-4xl leading-tight animate-slide-up">
                Governance E-Portal System <br />
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 via-indigo-400 to-purple-400 animate-gradient-text">
                    For the Digital Age
                </span>
            </h1>

            <p class="text-lg md:text-xl text-slate-400 max-w-2xl mb-10 leading-relaxed animate-slide-up" style="animation-delay: 0.1s;">
                Experience a seamless connection between citizens and government. 
                Request documents, report issues, and pay bills—all from your device.
            </p>

            <div class="flex flex-col sm:flex-row gap-4 animate-slide-up" style="animation-delay: 0.2s;">
                <Link 
                    :href="route('register')" 
                    class="px-8 py-4 bg-white text-slate-900 rounded-2xl font-bold text-lg shadow-xl hover:shadow-2xl hover:bg-blue-50 transition-all hover:-translate-y-1 flex items-center justify-center gap-2"
                >
                    Create Citizen Account
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                </Link>
                <Link 
                    :href="route('login')" 
                    class="px-8 py-4 bg-white/5 backdrop-blur-md border border-white/10 text-white rounded-2xl font-bold text-lg hover:bg-white/10 transition-all hover:-translate-y-1"
                >
                    Sign In
                </Link>
            </div>

            <div class="mt-24 grid grid-cols-1 md:grid-cols-3 gap-6 w-full max-w-6xl animate-fade-in-up" style="animation-delay: 0.4s;">
                
                <div class="group relative bg-slate-800/50 backdrop-blur-xl border border-white/10 p-8 rounded-3xl hover:bg-slate-800/80 transition-all duration-500 hover:-translate-y-2">
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-500/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity rounded-3xl"></div>
                    <div class="text-4xl mb-4 bg-blue-500/20 w-16 h-16 rounded-2xl flex items-center justify-center text-blue-400 group-hover:scale-110 transition-transform duration-500">📄</div>
                    <h3 class="text-xl font-bold mb-2">E-Documents</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">Securely request Barangay Clearances and Permits. Includes QR Code verification for authenticity.</p>
                </div>

                <div class="group relative bg-slate-800/50 backdrop-blur-xl border border-white/10 p-8 rounded-3xl hover:bg-slate-800/80 transition-all duration-500 hover:-translate-y-2">
                    <div class="absolute inset-0 bg-gradient-to-br from-red-500/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity rounded-3xl"></div>
                    <div class="text-4xl mb-4 bg-red-500/20 w-16 h-16 rounded-2xl flex items-center justify-center text-red-400 group-hover:scale-110 transition-transform duration-500">🚨</div>
                    <h3 class="text-xl font-bold mb-2">Rapid Response</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">Report community issues instantly with geolocation tagging and photo evidence uploads.</p>
                </div>

                <div class="group relative bg-slate-800/50 backdrop-blur-xl border border-white/10 p-8 rounded-3xl hover:bg-slate-800/80 transition-all duration-500 hover:-translate-y-2">
                    <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity rounded-3xl"></div>
                    <div class="text-4xl mb-4 bg-emerald-500/20 w-16 h-16 rounded-2xl flex items-center justify-center text-emerald-400 group-hover:scale-110 transition-transform duration-500">💳</div>
                    <h3 class="text-xl font-bold mb-2">Secure Payments</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">Pay utility bills (Water, Electric, Internet) through our secure virtual gateway.</p>
                </div>

            </div>

        </main>

        <footer class="relative z-10 border-t border-white/10 bg-slate-900/50 backdrop-blur-md">
            <div class="max-w-7xl mx-auto px-6 py-12 flex flex-col md:flex-row justify-between items-center gap-6">
                <div class="text-slate-500 text-sm">
                    &copy; 2025 Provincial Government of Camarines Sur. All rights reserved.
                </div>
                <div class="flex gap-6">
                    <a href="#" class="text-slate-400 hover:text-white transition-colors">Privacy Policy</a>
                    <a href="#" class="text-slate-400 hover:text-white transition-colors">Terms of Service</a>
                    <a href="#" class="text-slate-400 hover:text-white transition-colors">Contact Support</a>
                </div>
            </div>
        </footer>

    </div>
</template>

<style scoped>
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