<script setup>
import ChatAssistant from '@/Components/ChatAssistant.vue';
import DashboardMap from '@/Components/DashboardMap.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { onMounted, onUnmounted, ref } from 'vue';

const user = usePage().props.auth.user;

// --- LIVE CLOCK LOGIC ---
const time = ref('');
const date = ref('');
const updateTime = () => {
    const now = new Date();
    time.value = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
    date.value = now.toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric' });
};

// --- CAROUSEL LOGIC ---
const currentSlide = ref(0);
const slides = [
    {
        id: 1,
        title: "Mabuhay! Thank You for Your Trust.",
        subtitle: "Together, we are building a safer, smarter, and digital-first province.",
        color: "from-yellow-600 to-orange-600",
        icon: "🤝",
        action: "View Profile",
        link: route('profile.edit')
    },
    {
        id: 2,
        title: "Typhoon Season Preparedness",
        subtitle: "Check the Environment module for flood warnings and evacuation centers.",
        color: "from-blue-600 to-cyan-600",
        icon: "⛈️",
        action: "Check Status",
        link: route('environment.index')
    },
    {
        id: 3,
        title: "Grand Job Fair 2025",
        subtitle: "Over 500+ local vacancies available. Apply online today!",
        color: "from-purple-600 to-pink-600",
        icon: "💼",
        action: "Find Jobs",
        link: route('jobs.index')
    }
];

const nextSlide = () => {
    currentSlide.value = (currentSlide.value + 1) % slides.length;
};

// --- TIMERS ---
let clockTimer;
let carouselTimer;

onMounted(() => {
    updateTime();
    clockTimer = setInterval(updateTime, 1000);
    carouselTimer = setInterval(nextSlide, 5000);
});

onUnmounted(() => {
    clearInterval(clockTimer);
    clearInterval(carouselTimer);
});
</script>

<template>
    <Head title="Citizen Portal" />

    <AuthenticatedLayout>
        
        <div class="min-h-screen bg-slate-900 relative overflow-hidden font-sans pb-20">
            <div class="absolute top-[-10%] left-[-10%] w-[600px] h-[600px] bg-blue-600/20 rounded-full blur-[100px] animate-pulse-slow"></div>
            <div class="absolute bottom-[-10%] right-[-10%] w-[500px] h-[500px] bg-purple-600/20 rounded-full blur-[100px] animate-pulse-slow" style="animation-delay: 2s;"></div>
            <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(#64748b 1px, transparent 1px); background-size: 40px 40px;"></div>

            <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8">
                
                <div class="flex flex-col md:flex-row justify-between items-end mb-8 animate-slide-up">
                    <div>
                        <p class="text-blue-300 text-xs font-bold tracking-widest uppercase mb-1">System Online v2.0</p>
                        <h1 class="text-4xl md:text-5xl font-bold text-white tracking-tight">
                            Hello, <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-300 to-purple-300">{{ user.name.split(' ')[0] }}</span>
                        </h1>
                    </div>
                    <div class="mt-4 md:mt-0 text-right">
                        <div class="text-5xl font-thin text-white tracking-tighter">{{ time }}</div>
                        <div class="text-slate-400 font-medium uppercase tracking-wide text-xs">{{ date }}</div>
                    </div>
                </div>

                <div class="relative w-full h-48 rounded-3xl overflow-hidden shadow-2xl mb-10 border border-white/10 group animate-fade-in">
                    <div v-for="(slide, index) in slides" :key="slide.id" 
                         class="absolute inset-0 transition-opacity duration-1000 ease-in-out flex items-center px-8 md:px-12"
                         :class="index === currentSlide ? 'opacity-100 z-10' : 'opacity-0 z-0'">
                        
                        <div :class="`absolute inset-0 bg-gradient-to-r ${slide.color} opacity-90`"></div>
                        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-20"></div>
                        
                        <div class="relative z-20 w-full flex justify-between items-center">
                            <div class="max-w-xl text-white">
                                <div class="text-4xl mb-2 animate-bounce-subtle">{{ slide.icon }}</div>
                                <h2 class="text-2xl md:text-3xl font-bold mb-2 tracking-tight">{{ slide.title }}</h2>
                                <p class="text-white/80 text-sm md:text-base">{{ slide.subtitle }}</p>
                            </div>
                            <Link :href="slide.link" class="hidden sm:inline-flex items-center gap-2 bg-white/20 hover:bg-white/30 backdrop-blur-md border border-white/30 text-white px-6 py-3 rounded-xl font-bold transition-all hover:scale-105">
                                {{ slide.action }}
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </Link>
                        </div>
                    </div>
                    <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 z-30 flex gap-2">
                        <button v-for="(slide, index) in slides" :key="index" @click="currentSlide = index" :class="['w-2 h-2 rounded-full transition-all duration-300', index === currentSlide ? 'bg-white w-6' : 'bg-white/40']"></button>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    
                    <div class="lg:col-span-2 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-6 h-fit">
                        
                        <Link :href="route('documents.index')" class="h-44 bg-white/5 backdrop-blur-xl border border-white/10 p-6 rounded-3xl hover:bg-white/10 hover:border-blue-500/50 hover:shadow-[0_0_20px_rgba(59,130,246,0.2)] transition-all group flex flex-col justify-between animate-card-appear">
                            <div class="w-12 h-12 bg-blue-600 rounded-2xl flex items-center justify-center text-2xl shadow-lg group-hover:scale-110 transition-transform">📄</div>
                            <div>
                                <h3 class="font-bold text-white text-base">Documents</h3>
                                <p class="text-xs text-slate-400 mt-1">Permits & IDs</p>
                            </div>
                        </Link>

                        <Link :href="route('concerns.create')" class="h-44 bg-white/5 backdrop-blur-xl border border-white/10 p-6 rounded-3xl hover:bg-white/10 hover:border-red-500/50 hover:shadow-[0_0_20px_rgba(239,68,68,0.2)] transition-all group flex flex-col justify-between animate-card-appear" style="animation-delay: 0.05s;">
                            <div class="w-12 h-12 bg-red-600 rounded-2xl flex items-center justify-center text-2xl shadow-lg group-hover:scale-110 transition-transform">🚨</div>
                            <div>
                                <h3 class="font-bold text-white text-base">Report Issue</h3>
                                <p class="text-xs text-slate-400 mt-1">Incident Watch</p>
                            </div>
                        </Link>

                        <Link :href="route('bills.create')" class="h-44 bg-white/5 backdrop-blur-xl border border-white/10 p-6 rounded-3xl hover:bg-white/10 hover:border-emerald-500/50 hover:shadow-[0_0_20px_rgba(16,185,129,0.2)] transition-all group flex flex-col justify-between animate-card-appear" style="animation-delay: 0.1s;">
                            <div class="w-12 h-12 bg-emerald-600 rounded-2xl flex items-center justify-center text-2xl shadow-lg group-hover:scale-110 transition-transform">💳</div>
                            <div>
                                <h3 class="font-bold text-white text-base">Payments</h3>
                                <p class="text-xs text-slate-400 mt-1">Utilities & Tax</p>
                            </div>
                        </Link>

                        <Link :href="route('jobs.index')" class="h-44 bg-white/5 backdrop-blur-xl border border-white/10 p-6 rounded-3xl hover:bg-white/10 hover:border-purple-500/50 hover:shadow-[0_0_20px_rgba(168,85,247,0.2)] transition-all group flex flex-col justify-between animate-card-appear" style="animation-delay: 0.15s;">
                            <div class="w-12 h-12 bg-purple-600 rounded-2xl flex items-center justify-center text-2xl shadow-lg group-hover:scale-110 transition-transform">💼</div>
                            <div>
                                <h3 class="font-bold text-white text-base">Job Fair</h3>
                                <p class="text-xs text-slate-400 mt-1">Career Hub</p>
                            </div>
                        </Link>

                        <Link :href="route('health.index')" class="h-44 bg-white/5 backdrop-blur-xl border border-white/10 p-6 rounded-3xl hover:bg-white/10 hover:border-teal-500/50 hover:shadow-[0_0_20px_rgba(20,184,166,0.2)] transition-all group flex flex-col justify-between animate-card-appear" style="animation-delay: 0.2s;">
                            <div class="w-12 h-12 bg-teal-600 rounded-2xl flex items-center justify-center text-2xl shadow-lg group-hover:scale-110 transition-transform">🩺</div>
                            <div>
                                <h3 class="font-bold text-white text-base">E-Health</h3>
                                <p class="text-xs text-slate-400 mt-1">Telemed Booking</p>
                            </div>
                        </Link>

                        <Link :href="route('environment.index')" class="h-44 bg-white/5 backdrop-blur-xl border border-white/10 p-6 rounded-3xl hover:bg-white/10 hover:border-lime-500/50 hover:shadow-[0_0_20px_rgba(132,204,22,0.2)] transition-all group flex flex-col justify-between animate-card-appear" style="animation-delay: 0.25s;">
                            <div class="w-12 h-12 bg-lime-600 rounded-2xl flex items-center justify-center text-2xl shadow-lg group-hover:scale-110 transition-transform">♻️</div>
                            <div>
                                <h3 class="font-bold text-white text-base">Green Guard</h3>
                                <p class="text-xs text-slate-400 mt-1">Waste & Air</p>
                            </div>
                        </Link>

                        <Link :href="route('social.index')" class="h-44 bg-white/5 backdrop-blur-xl border border-white/10 p-6 rounded-3xl hover:bg-white/10 hover:border-pink-500/50 hover:shadow-[0_0_20px_rgba(236,72,153,0.2)] transition-all group flex flex-col justify-between animate-card-appear" style="animation-delay: 0.3s;">
                            <div class="w-12 h-12 bg-pink-600 rounded-2xl flex items-center justify-center text-2xl shadow-lg group-hover:scale-110 transition-transform">🤝</div>
                            <div>
                                <h3 class="font-bold text-white text-base">Social Care</h3>
                                <p class="text-xs text-slate-400 mt-1">Aid & Support</p>
                            </div>
                        </Link>

                        <Link :href="route('emergency.index')" class="h-44 bg-gradient-to-br from-red-600 to-orange-600 border border-white/20 p-6 rounded-3xl shadow-xl shadow-orange-600/30 hover:scale-[1.02] transition-all group flex flex-col justify-between animate-card-appear relative overflow-hidden" style="animation-delay: 0.35s;">
                            <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-20"></div>
                            
                            <div class="absolute -top-3 -right-3 w-8 h-8 bg-white/30 rounded-full animate-ping"></div>
                            
                            <div class="relative z-10 flex justify-between items-start">
                                <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center text-2xl shadow-lg">📞</div>
                                <span class="text-[10px] font-black bg-white text-red-600 px-2 py-1 rounded uppercase tracking-widest">SOS</span>
                            </div>
                            
                            <div class="relative z-10">
                                <h3 class="font-black text-white text-lg uppercase tracking-wide">Emergency</h3>
                                <p class="text-white/80 text-xs font-bold mt-1">Tap for Command Center</p>
                            </div>
                        </Link>

                    </div>

                    <div class="space-y-6 lg:mt-0 mt-8">
                        
                        <div class="group relative w-full aspect-[1.58/1] rounded-3xl overflow-hidden shadow-2xl transition-transform duration-500 hover:scale-105 border border-white/10">
                            <div class="absolute inset-0 bg-gradient-to-br from-slate-800 to-black"></div>
                            <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-30"></div>
                            <div class="absolute inset-0 p-6 flex flex-col justify-between">
                                <div class="flex justify-between items-start">
                                    <div class="w-12 h-12 bg-white/10 rounded-full flex items-center justify-center text-xl">🏛️</div>
                                    <div class="text-right">
                                        <div class="text-[9px] text-slate-400 uppercase tracking-widest">Resident ID</div>
                                        <div class="text-white font-mono font-bold text-base">{{ user.id.toString().padStart(8, '0') }}</div>
                                    </div>
                                </div>
                                <div>
                                    <div class="text-lg text-white font-bold tracking-wide uppercase">{{ user.name }}</div>
                                    <div class="text-[10px] text-blue-400 font-mono mt-1 flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 bg-blue-400 rounded-full animate-pulse"></span>
                                        Verified Citizen
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-slate-800/50 backdrop-blur-xl border border-white/5 rounded-3xl p-1 h-64 relative overflow-hidden shadow-lg">
                            <DashboardMap />
                        </div>

                    </div>

                </div>

            </div>
        </div>

        <ChatAssistant />
    </AuthenticatedLayout>
</template>

<style scoped>
.animate-pulse-slow { animation: pulse 6s ease-in-out infinite; }
.animate-slide-up { animation: slideUp 0.8s ease-out; }
@keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
.animate-fade-in { animation: fadeIn 1s ease-out; }
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
.animate-bounce-subtle { animation: bounce 3s infinite; }
.animate-card-appear { animation: card-appear 0.6s cubic-bezier(0.16, 1, 0.3, 1) both; }
@keyframes card-appear { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
</style>