<script setup>
import DashboardMap from '@/Components/DashboardMap.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed, onMounted, onUnmounted, ref } from 'vue';

const user = usePage().props.auth.user;

const props = defineProps({
    stats: {
        type: Object,
        default: () => ({ revenue: 0, citizens: 0 }) 
    },
    recentActivity: {
        type: Array,
        default: () => [] 
    },
    pendingTasks: {
        type: Array,
        default: () => [] 
    },
    departmentLoad: {
        type: Array,
        default: () => [] 
    }
});

const formatMoney = (value) => {
    return new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' }).format(value || 0);
};

// Animated counter for stats
const animatedRevenue = ref(0);
const animatedCitizens = ref(0);

const animateValue = (start, end, duration, callback) => {
    const range = end - start;
    const increment = range / (duration / 16);
    let current = start;
    
    const timer = setInterval(() => {
        current += increment;
        if ((increment > 0 && current >= end) || (increment < 0 && current <= end)) {
            current = end;
            clearInterval(timer);
        }
        callback(Math.floor(current));
    }, 16);
};

// Live clock
const time = ref('');
const date = ref('');
const updateTime = () => {
    const now = new Date();
    time.value = now.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', second: '2-digit' });
    date.value = now.toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' });
};

// System status
const systemStatus = computed(() => {
    const total = props.pendingTasks?.length || 0;
    if (total === 0) return { text: 'Optimal', color: 'text-green-400', bg: 'bg-green-500' };
    if (total < 5) return { text: 'Nominal', color: 'text-blue-400', bg: 'bg-blue-500' };
    if (total < 10) return { text: 'Active', color: 'text-yellow-400', bg: 'bg-yellow-500' };
    return { text: 'Critical', color: 'text-red-400', bg: 'bg-red-500' };
});

let clockTimer;
onMounted(() => {
    updateTime();
    clockTimer = setInterval(updateTime, 1000);
    
    // Animate stats on mount
    animateValue(0, props.stats?.revenue || 0, 1500, (val) => {
        animatedRevenue.value = val;
    });
    animateValue(0, props.stats?.citizens || 0, 1500, (val) => {
        animatedCitizens.value = val;
    });
});

onUnmounted(() => {
    clearInterval(clockTimer);
});

const getPriorityBadge = (status) => {
    const badges = {
        critical: { icon: '🔴', class: 'bg-gradient-to-r from-red-600 to-red-700', text: 'CRITICAL' },
        urgent: { icon: '🟡', class: 'bg-gradient-to-r from-yellow-600 to-orange-600', text: 'URGENT' },
        normal: { icon: '🔵', class: 'bg-gradient-to-r from-blue-600 to-blue-700', text: 'NORMAL' }
    };
    return badges[status] || badges.normal;
};
</script>

<template>
    <Head title="Command Center" />

    <AuthenticatedLayout>
        <!-- Premium background with animated gradient -->
        <div class="fixed inset-0 z-0 pointer-events-none bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950">
            <div class="absolute inset-0 opacity-[0.15] bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
            <div class="absolute inset-0 bg-gradient-to-tr from-blue-950/20 via-transparent to-purple-950/20"></div>
            <div class="absolute top-0 right-1/4 w-[600px] h-[600px] bg-blue-600/10 rounded-full blur-[120px] animate-pulse"></div>
            <div class="absolute bottom-0 left-1/4 w-[600px] h-[600px] bg-purple-600/10 rounded-full blur-[120px] animate-pulse" style="animation-delay: 1s"></div>
        </div>

        <div class="relative z-10 max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
            
            <!-- Premium Header -->
            <div class="relative overflow-hidden bg-gradient-to-br from-slate-900/90 via-slate-800/90 to-slate-900/90 backdrop-blur-2xl border border-white/10 rounded-3xl shadow-2xl shadow-black/50">
                <div class="absolute inset-0 bg-gradient-to-r from-blue-600/5 via-purple-600/5 to-blue-600/5"></div>
                
                <div class="relative p-6 lg:p-8">
                    <div class="flex flex-col lg:flex-row justify-between gap-6">
                        <!-- User Profile Section -->
                        <div class="flex items-center gap-6">
                            <div class="relative group">
                                <div class="absolute inset-0 bg-gradient-to-br from-blue-500 to-purple-600 rounded-2xl blur-xl opacity-50 group-hover:opacity-75 transition-opacity"></div>
                                <div class="relative w-20 h-20 rounded-2xl bg-gradient-to-br from-blue-600 to-purple-600 flex items-center justify-center text-3xl font-bold text-white shadow-2xl transform group-hover:scale-105 transition-transform">
                                    {{ user?.name?.charAt(0) || 'A' }}
                                </div>
                                <span class="absolute -bottom-1 -right-1 flex h-4 w-4">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-4 w-4 bg-emerald-500 border-2 border-slate-900"></span>
                                </span>
                            </div>
                            
                            <div>
                                <h1 class="text-2xl lg:text-3xl font-bold bg-gradient-to-r from-white to-slate-300 bg-clip-text text-transparent mb-1">
                                    Command Center
                                </h1>
                                <div class="flex items-center gap-3 text-sm">
                                    <span :class="systemStatus.color" class="font-bold uppercase tracking-wider flex items-center gap-2">
                                        <span :class="systemStatus.bg" class="w-2 h-2 rounded-full animate-pulse"></span>
                                        System {{ systemStatus.text }}
                                    </span>
                                    <span class="text-slate-500">•</span>
                                    <span class="text-slate-400 font-mono">{{ time }}</span>
                                </div>
                                <p class="text-xs text-slate-500 mt-1">{{ date }}</p>
                            </div>
                        </div>
                        
                        <!-- Stats Grid -->
                        <div class="grid grid-cols-3 gap-4 lg:gap-6">
                            <!-- Revenue Card -->
                            <div class="relative group cursor-pointer">
                                <div class="absolute inset-0 bg-gradient-to-br from-emerald-600/20 to-green-600/20 rounded-2xl blur-lg opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                <div class="relative bg-gradient-to-br from-emerald-950/50 to-green-950/50 border border-emerald-500/20 rounded-2xl p-4 backdrop-blur-sm transform group-hover:scale-105 transition-transform">
                                    <div class="text-xs text-emerald-400 uppercase font-bold tracking-wider mb-2 flex items-center gap-2">
                                        <span class="text-lg">💰</span>
                                        Revenue
                                    </div>
                                    <div class="text-2xl font-bold bg-gradient-to-r from-emerald-400 to-green-400 bg-clip-text text-transparent font-mono">
                                        {{ formatMoney(animatedRevenue) }}
                                    </div>
                                    <div class="text-xs text-emerald-500/60 mt-1">Today's collection</div>
                                </div>
                            </div>

                            <!-- Citizens Card -->
                            <div class="relative group cursor-pointer">
                                <div class="absolute inset-0 bg-gradient-to-br from-blue-600/20 to-cyan-600/20 rounded-2xl blur-lg opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                <div class="relative bg-gradient-to-br from-blue-950/50 to-cyan-950/50 border border-blue-500/20 rounded-2xl p-4 backdrop-blur-sm transform group-hover:scale-105 transition-transform">
                                    <div class="text-xs text-blue-400 uppercase font-bold tracking-wider mb-2 flex items-center gap-2">
                                        <span class="text-lg">👥</span>
                                        Citizens
                                    </div>
                                    <div class="text-2xl font-bold bg-gradient-to-r from-blue-400 to-cyan-400 bg-clip-text text-transparent font-mono">
                                        +{{ animatedCitizens }}
                                    </div>
                                    <div class="text-xs text-blue-500/60 mt-1">New registrations</div>
                                </div>
                            </div>

                            <!-- Pending Card -->
                            <div class="relative group cursor-pointer">
                                <div class="absolute inset-0 bg-gradient-to-br from-amber-600/20 to-yellow-600/20 rounded-2xl blur-lg opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                <div class="relative bg-gradient-to-br from-amber-950/50 to-yellow-950/50 border border-amber-500/20 rounded-2xl p-4 backdrop-blur-sm transform group-hover:scale-105 transition-transform">
                                    <div class="text-xs text-amber-400 uppercase font-bold tracking-wider mb-2 flex items-center gap-2">
                                        <span class="text-lg">⚡</span>
                                        Pending
                                    </div>
                                    <div class="text-2xl font-bold bg-gradient-to-r from-amber-400 to-yellow-400 bg-clip-text text-transparent font-mono">
                                        {{ pendingTasks?.length || 0 }}
                                    </div>
                                    <div class="text-xs text-amber-500/60 mt-1">Awaiting action</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                
                <!-- Left Column - Priority Queue & Insights -->
                <div class="lg:col-span-8 space-y-6">
                    
                    <!-- Priority Queue -->
                    <div class="relative overflow-hidden bg-gradient-to-br from-slate-900/90 via-slate-800/90 to-slate-900/90 backdrop-blur-2xl border border-white/10 rounded-3xl shadow-2xl">
                        <div class="absolute inset-0 bg-gradient-to-br from-blue-600/5 to-purple-600/5"></div>
                        
                        <div class="relative px-6 py-5 border-b border-white/10 flex justify-between items-center bg-gradient-to-r from-slate-900/50 to-transparent">
                            <h3 class="text-white font-bold text-lg flex items-center gap-3">
                                <span class="text-2xl">⚡</span>
                                <span class="bg-gradient-to-r from-white to-slate-300 bg-clip-text text-transparent">Priority Queue</span>
                                <span class="px-3 py-1 bg-amber-500/20 border border-amber-500/30 rounded-full text-xs text-amber-400 font-bold">
                                    {{ pendingTasks?.length || 0 }} Active
                                </span>
                            </h3>
                            <button class="px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-500 hover:to-purple-500 text-white text-sm font-bold rounded-xl transition-all transform hover:scale-105 shadow-lg shadow-blue-500/20">
                                Batch Process
                            </button>
                        </div>
                        
                        <div class="relative divide-y divide-white/5 max-h-[500px] overflow-y-auto premium-scrollbar">
                            <div v-for="(task, index) in pendingTasks" :key="task.id" 
                                class="p-5 hover:bg-white/5 transition-all group cursor-pointer backdrop-blur-sm"
                                :style="`animation: slideIn 0.3s ease-out ${index * 0.05}s backwards`">
                                <div class="flex items-center justify-between gap-4">
                                    <div class="flex items-center gap-4 flex-1">
                                        <div :class="getPriorityBadge(task.status).class" 
                                            class="w-12 h-12 rounded-xl flex items-center justify-center text-xl font-bold shadow-lg transform group-hover:scale-110 group-hover:rotate-3 transition-all">
                                            {{ task.type ? task.type.charAt(0) : '?' }}
                                        </div>
                                        
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 mb-1">
                                                <h4 class="text-white font-semibold text-sm truncate">{{ task.detail }}</h4>
                                                <span :class="`${getPriorityBadge(task.status).class} px-2 py-0.5 rounded text-[10px] font-bold text-white shadow-sm`">
                                                    {{ getPriorityBadge(task.status).text }}
                                                </span>
                                            </div>
                                            <p class="text-slate-400 text-xs flex items-center gap-2">
                                                <span class="font-medium text-slate-300">{{ task.user }}</span>
                                                <span class="text-slate-600">•</span>
                                                <span class="text-slate-500">{{ task.time }}</span>
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <Link :href="route('admin.documents.show', task.id)" 
                                        class="px-5 py-2.5 bg-gradient-to-r from-emerald-600 to-green-600 hover:from-emerald-500 hover:to-green-500 text-white text-sm rounded-xl font-bold transition-all transform hover:scale-105 shadow-lg shadow-emerald-500/20 opacity-0 group-hover:opacity-100">
                                        Review
                                    </Link>
                                </div>
                            </div>
                            
                            <div v-if="pendingTasks?.length === 0" class="p-16 text-center">
                                <div class="text-6xl mb-4 opacity-20">✨</div>
                                <p class="text-slate-500 text-sm font-medium">All tasks completed</p>
                                <p class="text-slate-600 text-xs mt-1">System status: Optimal</p>
                            </div>
                        </div>
                    </div>

                    <!-- Insights Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Department Load -->
                        <div class="relative overflow-hidden bg-gradient-to-br from-slate-900/90 to-slate-800/90 backdrop-blur-2xl border border-white/10 rounded-3xl shadow-2xl p-6">
                            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-blue-600/10 to-transparent rounded-full blur-2xl"></div>
                            
                            <h3 class="relative text-slate-400 text-xs font-bold uppercase tracking-widest mb-6 flex items-center gap-2">
                                <span class="text-lg">📊</span>
                                Department Load
                            </h3>
                            
                            <div class="relative space-y-5">
                                <div v-for="dept in departmentLoad" :key="dept.name" class="group">
                                    <div class="flex justify-between items-center text-sm mb-2">
                                        <span class="text-white font-medium">{{ dept.name }}</span>
                                        <span :class="dept.count > dept.capacity * 0.9 ? 'text-red-400 font-bold' : 'text-slate-400'" class="font-mono text-xs">
                                            {{ dept.count }}/{{ dept.capacity }}
                                        </span>
                                    </div>
                                    <div class="relative w-full bg-slate-950/50 rounded-full h-3 overflow-hidden border border-white/5">
                                        <div :class="`h-full transition-all duration-1000 ease-out ${dept.color} shadow-lg`" 
                                            :style="`width: ${(dept.count / dept.capacity) * 100}%`">
                                        </div>
                                    </div>
                                </div>
                                
                                <div v-if="departmentLoad?.length === 0" class="text-center text-slate-500 text-sm py-8">
                                    <div class="text-3xl mb-2 opacity-20">📊</div>
                                    No department data
                                </div>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="relative overflow-hidden bg-gradient-to-br from-purple-950/50 via-slate-900/90 to-blue-950/50 backdrop-blur-2xl border border-purple-500/20 rounded-3xl shadow-2xl p-6">
                            <div class="absolute inset-0 bg-gradient-to-br from-purple-600/5 to-blue-600/5"></div>
                            
                            <h3 class="relative text-purple-300 text-xs font-bold uppercase tracking-widest mb-6 flex items-center gap-2">
                                <span class="text-lg">⚡</span>
                                Quick Actions
                            </h3>
                            
                            <div class="relative grid grid-cols-2 gap-3">
                                <Link :href="route('admin.documents.index')" 
                                    class="group relative overflow-hidden p-5 bg-gradient-to-br from-slate-800/80 to-slate-900/80 hover:from-indigo-600/30 hover:to-purple-600/30 transition-all rounded-2xl border border-white/10 hover:border-indigo-500/50 transform hover:scale-105 hover:-translate-y-1 shadow-lg">
                                    <div class="absolute inset-0 bg-gradient-to-br from-indigo-600/0 to-purple-600/0 group-hover:from-indigo-600/10 group-hover:to-purple-600/10 transition-all"></div>
                                    <div class="relative text-center">
                                        <div class="text-3xl mb-2 transform group-hover:scale-110 group-hover:rotate-3 transition-transform">📄</div>
                                        <span class="text-xs text-white font-bold uppercase tracking-wide">Documents</span>
                                    </div>
                                </Link>
                                
                                <div class="group relative overflow-hidden p-5 bg-gradient-to-br from-slate-800/80 to-slate-900/80 hover:from-red-600/30 hover:to-orange-600/30 transition-all rounded-2xl border border-white/10 hover:border-red-500/50 transform hover:scale-105 hover:-translate-y-1 shadow-lg cursor-pointer">
                                    <div class="absolute inset-0 bg-gradient-to-br from-red-600/0 to-orange-600/0 group-hover:from-red-600/10 group-hover:to-orange-600/10 transition-all"></div>
                                    <div class="relative text-center">
                                        <div class="text-3xl mb-2 transform group-hover:scale-110 group-hover:rotate-3 transition-transform">🚨</div>
                                        <span class="text-xs text-white font-bold uppercase tracking-wide">Emergency</span>
                                    </div>
                                </div>

                                <div class="group relative overflow-hidden p-5 bg-gradient-to-br from-slate-800/80 to-slate-900/80 hover:from-emerald-600/30 hover:to-green-600/30 transition-all rounded-2xl border border-white/10 hover:border-emerald-500/50 transform hover:scale-105 hover:-translate-y-1 shadow-lg cursor-pointer">
                                    <div class="absolute inset-0 bg-gradient-to-br from-emerald-600/0 to-green-600/0 group-hover:from-emerald-600/10 group-hover:to-green-600/10 transition-all"></div>
                                    <div class="relative text-center">
                                        <div class="text-3xl mb-2 transform group-hover:scale-110 group-hover:rotate-3 transition-transform">💹</div>
                                        <span class="text-xs text-white font-bold uppercase tracking-wide">Analytics</span>
                                    </div>
                                </div>

                                <div class="group relative overflow-hidden p-5 bg-gradient-to-br from-slate-800/80 to-slate-900/80 hover:from-purple-600/30 hover:to-pink-600/30 transition-all rounded-2xl border border-white/10 hover:border-purple-500/50 transform hover:scale-105 hover:-translate-y-1 shadow-lg cursor-pointer">
                                    <div class="absolute inset-0 bg-gradient-to-br from-purple-600/0 to-pink-600/0 group-hover:from-purple-600/10 group-hover:to-pink-600/10 transition-all"></div>
                                    <div class="relative text-center">
                                        <div class="text-3xl mb-2 transform group-hover:scale-110 group-hover:rotate-3 transition-transform">👔</div>
                                        <span class="text-xs text-white font-bold uppercase tracking-wide">HR Portal</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Map & Activity -->
                <div class="lg:col-span-4 space-y-6">
                    
                    <!-- Live Map -->
                    <div class="relative overflow-hidden bg-gradient-to-br from-slate-900/90 to-slate-800/90 backdrop-blur-2xl border border-white/10 rounded-3xl shadow-2xl p-2 h-80 group">
                        <div class="absolute top-4 left-4 z-10 bg-black/80 backdrop-blur-md px-3 py-2 rounded-xl text-xs text-emerald-400 font-bold border border-emerald-500/30 flex items-center gap-2 shadow-xl">
                            <span class="relative flex h-2 w-2">
                              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                              <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                            </span>
                            LIVE TRACKING
                        </div>
                        <div class="w-full h-full rounded-2xl overflow-hidden opacity-90 hover:opacity-100 transition-all duration-500 transform group-hover:scale-[1.02]">
                             <DashboardMap />
                        </div>
                    </div>

                    <!-- Activity Feed -->
                    <div class="relative overflow-hidden bg-gradient-to-br from-slate-900/90 to-slate-800/90 backdrop-blur-2xl border border-white/10 rounded-3xl shadow-2xl">
                        <div class="sticky top-0 z-10 px-6 py-5 border-b border-white/10 bg-gradient-to-r from-slate-900/95 to-transparent backdrop-blur-md">
                            <h3 class="text-white text-sm font-bold uppercase tracking-widest flex items-center gap-2">
                                <span class="text-xl">📋</span>
                                <span class="bg-gradient-to-r from-white to-slate-300 bg-clip-text text-transparent">Activity Stream</span>
                            </h3>
                        </div>
                        
                        <div class="p-6 max-h-[450px] overflow-y-auto premium-scrollbar">
                            <div class="space-y-4">
                                <div v-for="(item, index) in recentActivity" :key="item.id" 
                                    class="relative pl-6 pb-4 border-l-2 border-slate-700/50 hover:border-blue-500/50 transition-all group"
                                    :style="`animation: slideIn 0.3s ease-out ${index * 0.05}s backwards`">
                                    <div class="absolute -left-[9px] top-0 w-4 h-4 rounded-full bg-gradient-to-br from-blue-500 to-purple-500 border-2 border-slate-900 transform group-hover:scale-125 transition-transform shadow-lg shadow-blue-500/30"></div>
                                    <div>
                                        <p class="text-sm text-slate-300">
                                            <span class="font-bold text-white">{{ item.user }}</span> 
                                            <span class="text-slate-400">{{ item.action }}</span>
                                        </p>
                                        <p class="text-xs text-slate-500 mt-1 font-mono">{{ item.time }}</p>
                                    </div>
                                </div>
                                
                                <div v-if="!recentActivity?.length" class="text-center py-16">
                                    <div class="text-5xl mb-3 opacity-10">📡</div>
                                    <p class="text-slate-500 text-sm">Monitoring system activity...</p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateX(-20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.premium-scrollbar::-webkit-scrollbar {
    width: 6px;
}
.premium-scrollbar::-webkit-scrollbar-track {
    background: rgba(15, 23, 42, 0.5); 
    border-radius: 10px;
}
.premium-scrollbar::-webkit-scrollbar-thumb {
    background: linear-gradient(to bottom, rgba(99, 102, 241, 0.5), rgba(139, 92, 246, 0.5));
    border-radius: 10px;
    border: 2px solid rgba(15, 23, 42, 0.5);
}
.premium-scrollbar::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(to bottom, rgba(99, 102, 241, 0.8), rgba(139, 92, 246, 0.8));
}
</style>