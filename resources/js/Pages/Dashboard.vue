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
const greeting = ref('Good Morning');

const updateTime = () => {
    const now = new Date();
    time.value = now.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' });
    date.value = now.toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric' });
    
    const hour = now.getHours();
    if (hour < 12) greeting.value = 'Good Morning';
    else if (hour < 18) greeting.value = 'Good Afternoon';
    else greeting.value = 'Good Evening';
};

// --- CAROUSEL LOGIC ---
const currentSlide = ref(0);
const slides = [
    {
        id: 1,
        title: "System Online",
        subtitle: "All governance modules are fully operational and secured.",
        color: "bg-blue-500",
        image: "https://images.unsplash.com/photo-1451187580459-43490279c0fa?q=80&w=2072&auto=format&fit=crop",
        link: route('profile.edit')
    },
    {
        id: 2,
        title: "Typhoon Watch",
        subtitle: "Monitor flood levels and evacuation centers in real-time.",
        color: "bg-indigo-500",
        image: "https://images.unsplash.com/photo-1516912481808-540c3f4fe1bc?q=80&w=2069&auto=format&fit-crop",
        link: route('environment.index')
    },
    {
        id: 3,
        title: "Job Fair 2025",
        subtitle: "500+ Local vacancies available. Apply via the portal.",
        color: "bg-purple-500",
        image: "https://images.unsplash.com/photo-1486312338219-ce68d2c6f44d?q=80&w=2072&auto=format&fit-crop",
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
    carouselTimer = setInterval(nextSlide, 6000);
});

onUnmounted(() => {
    clearInterval(clockTimer);
    clearInterval(carouselTimer);
});

// --- FEATURE DATA (NEW STRUCTURE) ---
const features = [
    // GROUP 1: CORE SERVICES (2x width)
    { 
        group: 'Core Services', 
        title: 'E-Documents', 
        subtitle: 'Clearances & Permits', 
        icon: '📄', 
        link: route('documents.index'),
        gradient: 'from-blue-600 to-indigo-700',
        size: 'col-span-1 md:col-span-2'
    },
    { 
        group: 'Core Services', 
        title: 'Bills & Fees', 
        subtitle: 'Secure Utility Payments', 
        icon: '💳', 
        link: route('bills.create'),
        gradient: 'from-teal-600 to-emerald-700',
        size: 'col-span-1 md:col-span-1'
    },
    { 
        group: 'Core Services', 
        title: 'Places', 
        subtitle: 'Business Directory', 
        icon: '🏨', 
        link: route('establishments.index'),
        gradient: 'from-orange-600 to-amber-700',
        size: 'col-span-1 md:col-span-1'
    },
    
    // GROUP 2: COMMUNITY & ACTION (Square Grid)
    { 
        group: 'Community & Action', 
        title: 'Issue Report', 
        subtitle: 'Rapid Response System', 
        icon: '🚨', 
        link: route('concerns.create'),
        gradient: 'from-red-600 to-pink-700',
        size: 'col-span-1 md:col-span-1'
    },
    { 
        group: 'Community & Action', 
        title: 'E-Health', 
        subtitle: 'Telemed & Booking', 
        icon: '🩺', 
        link: route('health.index'),
        gradient: 'from-sky-600 to-cyan-700',
        size: 'col-span-1 md:col-span-1'
    },
    { 
        group: 'Community & Action', 
        title: 'Social Care', 
        subtitle: 'Aid & Support', 
        icon: '🤝', 
        link: route('social.index'),
        gradient: 'from-pink-600 to-fuchsia-700',
        size: 'col-span-1 md:col-span-1'
    },
    { 
        group: 'Community & Action', 
        title: 'Polls & Vote', 
        subtitle: 'Citizen Referendum', 
        icon: '🗳️', 
        link: route('polls.index'),
        gradient: 'from-violet-600 to-purple-700',
        size: 'col-span-1 md:col-span-1'
    },
    
    // GROUP 3: MARKET & ENVIRONMENT (Mix)
    { 
        group: 'Market & Environment', 
        title: 'Market Prices', 
        subtitle: 'Live Commodity Rates', 
        icon: '📈', 
        link: route('market.index'),
        gradient: 'from-lime-600 to-green-700',
        size: 'col-span-1 md:col-span-1'
    },
    { 
        group: 'Market & Environment', 
        title: 'Green Guard', 
        subtitle: 'Waste & Air Quality', 
        icon: '♻️', 
        link: route('environment.index'),
        gradient: 'from-indigo-600 to-blue-700',
        size: 'col-span-1 md:col-span-1'
    },
    { 
        group: 'Market & Environment', 
        title: 'Local Jobs', 
        subtitle: 'Career Opportunities', 
        icon: '💼', 
        link: route('jobs.index'),
        gradient: 'from-yellow-600 to-red-700',
        size: 'col-span-2 md:col-span-2'
    },
];

// --- GROUPING LOGIC ---
const groupedFeatures = features.reduce((acc, feature) => {
    (acc[feature.group] = acc[feature.group] || []).push(feature);
    return acc;
}, {});
</script>

<template>
    <Head title="Command Center" />

    <AuthenticatedLayout>
        
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

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 pb-24 font-sans">
            
            <div class="flex flex-col md:flex-row justify-between items-end mb-10 animate-slide-up">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                        <p class="text-blue-300/80 text-xs font-bold tracking-widest uppercase">System Operational</p>
                    </div>
                    <h1 class="text-4xl md:text-5xl font-extrabold text-white tracking-tight drop-shadow-sm">
                        {{ greeting }}, <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-300 to-indigo-300">{{ user.name.split(' ')[0] }}</span>
                    </h1>
                </div>
                <div class="mt-4 md:mt-0 text-right">
                    <div class="text-6xl font-thin text-white tracking-tighter leading-none">{{ time }}</div>
                    <div class="text-white/50 font-medium text-sm uppercase tracking-widest mt-1">{{ date }}</div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                
                <div class="lg:col-span-8 space-y-6">
                    
                    <div class="relative h-64 rounded-[2.5rem] overflow-hidden shadow-2xl border border-white/10 group animate-fade-in">
                        <div v-for="(slide, index) in slides" :key="slide.id" 
                             class="absolute inset-0 transition-all duration-1000 ease-in-out"
                             :class="index === currentSlide ? 'opacity-100 scale-100 z-10' : 'opacity-0 scale-105 z-0'">
                            
                            <img :src="slide.image" class="absolute inset-0 w-full h-full object-cover opacity-60">
                            <div class="absolute inset-0 bg-gradient-to-r from-black via-black/60 to-transparent"></div>
                            
                            <div class="relative z-20 h-full flex flex-col justify-center px-10">
                                <span class="px-3 py-1 bg-white/10 backdrop-blur-md border border-white/20 w-fit rounded-full text-[10px] font-bold text-white uppercase tracking-widest mb-4">
                                    Featured News
                                </span>
                                <h2 class="text-4xl font-bold text-white mb-2 leading-tight max-w-lg">{{ slide.title }}</h2>
                                <p class="text-white/70 text-lg mb-6 max-w-md">{{ slide.subtitle }}</p>
                                
                                <Link :href="slide.link" class="flex items-center gap-2 text-white font-bold group-hover:gap-4 transition-all">
                                    <span>Open Module</span>
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                                </Link>
                            </div>
                        </div>
                        
                        <div class="absolute bottom-6 right-8 z-30 flex gap-2">
                            <button v-for="(slide, index) in slides" :key="index" @click="currentSlide = index" 
                                :class="['h-1.5 rounded-full transition-all duration-300', index === currentSlide ? 'bg-white w-8' : 'bg-white/30 w-2']">
                            </button>
                        </div>
                    </div>

                    <div v-for="(group, groupName, groupIndex) in groupedFeatures" :key="groupName" 
                         :style="{ 'animation-delay': `${0.2 + groupIndex * 0.15}s` }"
                         class="animate-slide-up space-y-4">
                        
                        <h2 class="text-sm font-bold uppercase tracking-[0.2em] text-white/50 pt-6 border-t border-white/10">{{ groupName }}</h2>

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <Link v-for="(feature, featureIndex) in group" :key="feature.title" 
                                  :href="feature.link" 
                                  :class="[
                                    'group relative overflow-hidden flex flex-col justify-between p-5 rounded-[1.75rem] shadow-xl border border-white/10 transition-all duration-300 hover:scale-[1.03] hover:shadow-2xl hover:border-white/20',
                                    `bg-gradient-to-br ${feature.gradient}`,
                                    feature.size,
                                    (groupName === 'Core Services' && feature.title === 'E-Documents') ? 'h-52' : 'h-36' 
                                  ]">
                                  
                                <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-20 mix-blend-overlay"></div>
                                
                                <div class="relative z-10 flex flex-col justify-start">
                                    <div class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center text-xl shadow-lg mb-2 group-hover:scale-110 transition-transform duration-300">
                                        {{ feature.icon }}
                                    </div>
                                    <p class="text-white/70 text-xs font-semibold uppercase tracking-widest mt-2">{{ feature.subtitle }}</p>
                                </div>

                                <div class="relative z-10">
                                    <h3 class="font-black text-white text-xl leading-tight">{{ feature.title }}</h3>
                                </div>
                            </Link>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-4 space-y-6">
                    
                    <div class="group relative aspect-[1.58/1] rounded-[2rem] overflow-hidden shadow-2xl transition-transform duration-500 hover:scale-105 border border-white/10 animate-slide-up" style="animation-delay: 0.2s;">
                        <div class="absolute inset-0 bg-gradient-to-br from-slate-800 via-slate-900 to-black"></div>
                        <div class="absolute inset-0 opacity-30 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-blue-400 via-purple-500 to-transparent"></div>
                        
                        <div class="absolute inset-0 p-6 flex flex-col justify-between">
                            <div class="flex justify-between items-start">
                                <div class="w-10 h-10 bg-white/10 backdrop-blur-md rounded-full flex items-center justify-center text-lg border border-white/10">🏛️</div>
                                <div class="text-right">
                                    <div class="text-[9px] text-white/60 uppercase tracking-widest font-bold">Universal ID</div>
                                    <div class="text-white font-mono font-bold text-sm tracking-widest opacity-80">{{ user.id.toString().padStart(8, '0') }}</div>
                                </div>
                            </div>
                            <div>
                                <div class="text-2xl text-white font-black tracking-wide uppercase">{{ user.name }}</div>
                                <div class="flex items-center gap-2 mt-2">
                                    <span class="px-2 py-0.5 bg-blue-500/20 border border-blue-500/30 rounded-full text-[9px] font-bold text-blue-300 uppercase tracking-wider">Citizen</span>
                                    <span class="px-2 py-0.5 bg-green-500/20 border border-green-500/30 rounded-full text-[9px] font-bold text-green-300 uppercase tracking-wider">Active Status</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-black/40 backdrop-blur-xl border border-white/10 rounded-[2rem] p-1 h-80 shadow-2xl animate-slide-up relative group" style="animation-delay: 0.3s;">
                        <div class="absolute top-4 left-4 z-10 bg-black/60 backdrop-blur-md px-3 py-1 rounded-full text-xs font-bold text-white border border-white/10 shadow-lg">
                            📍 Live Map & Alerts
                        </div>
                        <div class="w-full h-full rounded-[1.8rem] overflow-hidden opacity-90 group-hover:opacity-100 transition-opacity">
                            <DashboardMap />
                        </div>
                    </div>
                    
                    <Link :href="route('emergency.index')" class="bg-gradient-to-br from-red-600 to-orange-600 border border-white/20 p-6 rounded-[2rem] shadow-xl shadow-orange-600/30 hover:scale-[1.02] transition-all group flex flex-col justify-between animate-card-appear relative overflow-hidden h-40" style="animation-delay: 0.4s;">
                        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-20"></div>
                        
                        <div class="absolute -top-3 -right-3 w-8 h-8 bg-white/30 rounded-full animate-ping"></div>
                        
                        <div class="relative z-10 flex justify-between items-start">
                            <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center text-2xl shadow-lg">📞</div>
                            <span class="text-[10px] font-black bg-white text-red-600 px-2 py-1 rounded uppercase tracking-widest">SOS</span>
                        </div>
                        
                        <div class="relative z-10">
                            <h3 class="font-black text-white text-2xl uppercase tracking-wide">Emergency Call</h3>
                            <p class="text-white/80 text-xs font-bold mt-1">Tap for Command Center</p>
                        </div>
                    </Link>

                </div>

            </div>

        </div>

        <ChatAssistant />
    </AuthenticatedLayout>
</template>

<style scoped>
/* 🌊 WAVE ANIMATION (Copied from Welcome.vue) */
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

/* Slide up entrance (Used for sections/groups) */
@keyframes slideUp { 
    from { opacity: 0; transform: translateY(20px); filter: blur(5px); } 
    to { opacity: 1; transform: translateY(0); filter: blur(0); } 
}
.animate-slide-up { animation: slideUp 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) forwards; }

/* Fade in (Used for the Carousel) */
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
.animate-fade-in { animation: fadeIn 1s ease-out; }

/* Card Appear (Used for the Emergency button on the right) */
@keyframes cardAppear {
    from { opacity: 0; transform: translateY(50px) scale(0.9); }
    to { opacity: 1; transform: translateY(0) scale(1); }
}
.animate-card-appear { animation: cardAppear 1s cubic-bezier(0.2, 0.8, 0.2, 1) forwards; }
</style>