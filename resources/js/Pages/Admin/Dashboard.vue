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
    if (total === 0) return { text: 'Optimal', color: 'text-emerald-400', bg: 'bg-emerald-500', glow: 'shadow-emerald-500/50' };
    if (total < 5) return { text: 'Nominal', color: 'text-blue-400', bg: 'bg-blue-500', glow: 'shadow-blue-500/50' };
    if (total < 10) return { text: 'Active', color: 'text-amber-400', bg: 'bg-amber-500', glow: 'shadow-amber-500/50' };
    return { text: 'Critical', color: 'text-red-400', bg: 'bg-red-500', glow: 'shadow-red-500/50' };
});

// Mouse parallax
const mouseX = ref(0);
const mouseY = ref(0);
let mouseRAF = null;
const handleMouseMove = (e) => {
    if (mouseRAF) return;
    mouseRAF = requestAnimationFrame(() => {
        mouseX.value = (e.clientX / window.innerWidth - 0.5) * 20;
        mouseY.value = (e.clientY / window.innerHeight - 0.5) * 20;
        mouseRAF = null;
    });
};

let clockTimer;
onMounted(() => {
    updateTime();
    clockTimer = setInterval(updateTime, 1000);
    
    // Animate stats on mount
    animateValue(0, props.stats?.revenue || 0, 2000, (val) => {
        animatedRevenue.value = val;
    });
    animateValue(0, props.stats?.citizens || 0, 2000, (val) => {
        animatedCitizens.value = val;
    });

    window.addEventListener('mousemove', handleMouseMove, { passive: true });
});

onUnmounted(() => {
    clearInterval(clockTimer);
    window.removeEventListener('mousemove', handleMouseMove);
    if (mouseRAF) cancelAnimationFrame(mouseRAF);
});

const getPriorityBadge = (status) => {
    const badges = {
        critical: { 
            icon: '🔴', 
            class: 'from-red-600 to-rose-700', 
            text: 'CRITICAL',
            border: 'border-red-500/40',
            glow: 'shadow-red-500/40'
        },
        urgent: { 
            icon: '🟡', 
            class: 'from-amber-600 to-orange-600', 
            text: 'URGENT',
            border: 'border-amber-500/40',
            glow: 'shadow-amber-500/40'
        },
        normal: { 
            icon: '🔵', 
            class: 'from-blue-600 to-indigo-700', 
            text: 'NORMAL',
            border: 'border-blue-500/40',
            glow: 'shadow-blue-500/40'
        }
    };
    return badges[status] || badges.normal;
};

const getAvatarGradient = (name) => {
    const gradients = [
        'from-blue-500 via-blue-600 to-indigo-600',
        'from-purple-500 via-purple-600 to-pink-600',
        'from-emerald-500 via-teal-600 to-cyan-600',
        'from-orange-500 via-red-600 to-pink-600',
        'from-amber-500 via-yellow-600 to-orange-600',
    ];
    const hash = (name?.split('').reduce((acc, char) => acc + char.charCodeAt(0), 0) || 0);
    return gradients[hash % gradients.length];
};
</script>

<template>
    <Head title="Command Center" />

    <AuthenticatedLayout>
        <!-- Enhanced Background Layer -->
        <div class="fixed inset-0 z-0 pointer-events-none overflow-hidden">
            <!-- Base gradient -->
            <div class="absolute inset-0 bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950"></div>
            
            <!-- Animated texture -->
            <div class="absolute inset-0 opacity-[0.03] bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxwYXRoIGQ9Ik0zNiAxOGMzLjMxNCAwIDYgMi42ODYgNiA2cy0yLjY4NiA2LTYgNi02LTIuNjg2LTYtNiAyLjY4Ni02IDYtNiIgc3Ryb2tlPSIjZmZmIi8+PC9nPjwvc3ZnPg==')]"></div>
            
            <!-- Dynamic color overlays -->
            <div class="absolute inset-0 bg-gradient-to-tr from-blue-950/30 via-transparent to-purple-950/30"></div>
            
            <!-- Animated glows -->
            <div 
                class="absolute top-0 right-1/4 w-[800px] h-[800px] bg-blue-500/15 rounded-full blur-[150px] animate-pulse-slow"
                :style="{ transform: `translate(${mouseX}px, ${mouseY}px)` }"
            ></div>
            <div 
                class="absolute bottom-0 left-1/4 w-[700px] h-[700px] bg-purple-500/15 rounded-full blur-[150px] animate-pulse-slow"
                style="animation-delay: 1.5s"
                :style="{ transform: `translate(${-mouseX}px, ${-mouseY}px)` }"
            ></div>
            <div 
                class="absolute top-1/2 left-1/2 w-[600px] h-[600px] bg-emerald-500/10 rounded-full blur-[150px] animate-pulse-slow"
                style="animation-delay: 3s"
                :style="{ transform: `translate(${mouseX * 0.5}px, ${mouseY * 0.5}px)` }"
            ></div>
            
            <!-- Grid overlay -->
            <div class="absolute inset-0 bg-[linear-gradient(to_right,rgba(255,255,255,0.02)_1px,transparent_1px),linear-gradient(to_bottom,rgba(255,255,255,0.02)_1px,transparent_1px)] bg-[size:4rem_4rem] [mask-image:radial-gradient(ellipse_80%_50%_at_50%_0%,#000_70%,transparent_110%)]"></div>
        </div>

        <div class="relative z-10 max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 space-y-6">
            
            <!-- Enhanced Premium Header -->
            <div class="glass-card group animate-fade-in-up">
                <div class="absolute inset-0 bg-gradient-to-r from-blue-600/5 via-purple-600/5 to-pink-600/5 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                
                <div class="relative p-4 sm:p-6 lg:p-8">
                    <div class="flex flex-col lg:flex-row justify-between gap-6">
                        <!-- Enhanced User Profile Section -->
                        <div class="flex items-center gap-4 sm:gap-6">
                            <div class="relative group/avatar">
                                <!-- Animated ring -->
                                <div class="absolute inset-0 rounded-2xl animate-spin-slow">
                                    <div class="absolute inset-0 bg-gradient-to-br from-blue-500 via-purple-500 to-pink-500 rounded-2xl blur-xl opacity-0 group-hover/avatar:opacity-75 transition-opacity duration-500"></div>
                                </div>
                                
                                <!-- Avatar -->
                                <div :class="`relative w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-gradient-to-br ${getAvatarGradient(user?.name)} flex items-center justify-center text-2xl sm:text-3xl font-bold text-white shadow-2xl transform group-hover/avatar:scale-105 transition-all duration-300`">
                                    {{ user?.name?.charAt(0) || 'A' }}
                                    
                                    <!-- Shine effect -->
                                    <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-white/20 to-transparent opacity-0 group-hover/avatar:opacity-100 transition-opacity"></div>
                                </div>
                                
                                <!-- Status indicator -->
                                <span class="absolute -bottom-1 -right-1 flex h-4 w-4 sm:h-5 sm:w-5">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-4 w-4 sm:h-5 sm:w-5 bg-emerald-500 border-2 sm:border-3 border-slate-900 shadow-lg shadow-emerald-500/50"></span>
                                </span>
                            </div>
                            
                            <div class="min-w-0">
                                <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold bg-gradient-to-r from-white via-blue-100 to-purple-100 bg-clip-text text-transparent mb-1 tracking-tight">
                                    Command Center
                                </h1>
                                <div class="flex flex-wrap items-center gap-2 sm:gap-3 text-xs sm:text-sm">
                                    <span :class="systemStatus.color" class="font-bold uppercase tracking-wider flex items-center gap-2">
                                        <span :class="`${systemStatus.bg} w-2 h-2 rounded-full animate-pulse ${systemStatus.glow} shadow-lg`"></span>
                                        System {{ systemStatus.text }}
                                    </span>
                                    <span class="text-slate-500 hidden sm:inline">•</span>
                                    <span class="text-slate-400 font-mono hidden sm:inline">{{ time }}</span>
                                </div>
                                <p class="text-xs text-slate-500 mt-1 hidden sm:block">{{ date }}</p>
                            </div>
                        </div>
                        
                        <!-- Enhanced Stats Grid -->
                        <div class="grid grid-cols-3 gap-3 sm:gap-4 lg:gap-6">
                            <!-- Revenue Card -->
                            <div class="stat-card group/stat">
                                <div class="absolute inset-0 bg-gradient-to-br from-emerald-600/20 to-green-600/20 rounded-2xl blur-lg opacity-0 group-hover/stat:opacity-100 transition-opacity duration-500"></div>
                                <div class="relative stat-card-content from-emerald-950/50 to-green-950/50 border-emerald-500/20">
                                    <div class="stat-header text-emerald-400">
                                        <span class="text-lg sm:text-xl">💰</span>
                                        <span class="hidden sm:inline">Revenue</span>
                                        <span class="sm:hidden">Rev</span>
                                    </div>
                                    <div class="stat-value from-emerald-400 to-green-400">
                                        {{ formatMoney(animatedRevenue).replace('₱', '₱').slice(0, -3) }}
                                    </div>
                                    <div class="stat-label text-emerald-500/60">Today's</div>
                                </div>
                            </div>

                            <!-- Citizens Card -->
                            <div class="stat-card group/stat">
                                <div class="absolute inset-0 bg-gradient-to-br from-blue-600/20 to-cyan-600/20 rounded-2xl blur-lg opacity-0 group-hover/stat:opacity-100 transition-opacity duration-500"></div>
                                <div class="relative stat-card-content from-blue-950/50 to-cyan-950/50 border-blue-500/20">
                                    <div class="stat-header text-blue-400">
                                        <span class="text-lg sm:text-xl">👥</span>
                                        <span class="hidden sm:inline">Citizens</span>
                                        <span class="sm:hidden">New</span>
                                    </div>
                                    <div class="stat-value from-blue-400 to-cyan-400">
                                        +{{ animatedCitizens }}
                                    </div>
                                    <div class="stat-label text-blue-500/60">Registered</div>
                                </div>
                            </div>

                            <!-- Pending Card -->
                            <div class="stat-card group/stat">
                                <div class="absolute inset-0 bg-gradient-to-br from-amber-600/20 to-orange-600/20 rounded-2xl blur-lg opacity-0 group-hover/stat:opacity-100 transition-opacity duration-500"></div>
                                <div class="relative stat-card-content from-amber-950/50 to-orange-950/50 border-amber-500/20">
                                    <div class="stat-header text-amber-400">
                                        <span class="text-lg sm:text-xl">⚡</span>
                                        <span class="hidden sm:inline">Pending</span>
                                        <span class="sm:hidden">Queue</span>
                                    </div>
                                    <div class="stat-value from-amber-400 to-orange-400">
                                        {{ pendingTasks?.length || 0 }}
                                    </div>
                                    <div class="stat-label text-amber-500/60">Tasks</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 sm:gap-6">
                
                <!-- Left Column -->
                <div class="lg:col-span-8 space-y-4 sm:space-y-6">
                    
                    <!-- Enhanced Priority Queue -->
                    <div class="glass-card animate-fade-in-up" style="animation-delay: 0.1s">
                        <div class="absolute inset-0 bg-gradient-to-br from-blue-600/5 to-purple-600/5 rounded-3xl"></div>
                        
                        <div class="relative px-4 sm:px-6 py-4 sm:py-5 border-b border-white/10 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 bg-gradient-to-r from-slate-900/50 to-transparent">
                            <h3 class="text-white font-bold text-base sm:text-lg flex items-center gap-2 sm:gap-3">
                                <span class="text-xl sm:text-2xl">⚡</span>
                                <span class="bg-gradient-to-r from-white to-slate-300 bg-clip-text text-transparent">Priority Queue</span>
                                <span class="px-2 sm:px-3 py-1 bg-amber-500/20 border border-amber-500/30 rounded-full text-xs text-amber-400 font-bold">
                                    {{ pendingTasks?.length || 0 }}
                                </span>
                            </h3>
                            <button class="w-full sm:w-auto px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-500 hover:to-purple-500 text-white text-sm font-bold rounded-xl transition-all transform hover:scale-105 shadow-lg shadow-blue-500/20 active:scale-95">
                                Batch Process
                            </button>
                        </div>
                        
                        <div class="relative divide-y divide-white/5 max-h-[500px] overflow-y-auto premium-scrollbar">
                            <div v-for="(task, index) in pendingTasks" :key="task.id" 
                                class="p-4 sm:p-5 hover:bg-white/5 transition-all group/task cursor-pointer backdrop-blur-sm"
                                :style="`animation: slideInRight 0.4s ease-out ${index * 0.05}s backwards`">
                                <div class="flex items-center justify-between gap-3 sm:gap-4">
                                    <div class="flex items-center gap-3 sm:gap-4 flex-1 min-w-0">
                                        <div :class="`w-10 h-10 sm:w-12 sm:h-12 rounded-xl flex items-center justify-center text-lg sm:text-xl font-bold shadow-lg transform group-hover/task:scale-110 group-hover/task:rotate-3 transition-all bg-gradient-to-br ${getPriorityBadge(task.status).class} ${getPriorityBadge(task.status).glow} border ${getPriorityBadge(task.status).border}`">
                                            {{ task.type ? task.type.charAt(0) : '?' }}
                                        </div>
                                        
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 mb-1 flex-wrap">
                                                <h4 class="text-white font-semibold text-sm truncate">{{ task.detail }}</h4>
                                                <span :class="`bg-gradient-to-r ${getPriorityBadge(task.status).class} px-2 py-0.5 rounded text-[10px] font-bold text-white shadow-sm border ${getPriorityBadge(task.status).border}`">
                                                    {{ getPriorityBadge(task.status).text }}
                                                </span>
                                            </div>
                                            <p class="text-slate-400 text-xs flex items-center gap-2">
                                                <span class="font-medium text-slate-300 truncate">{{ task.user }}</span>
                                                <span class="text-slate-600">•</span>
                                                <span class="text-slate-500">{{ task.time }}</span>
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <Link :href="route('admin.documents.show', task.id)" 
                                        class="hidden sm:flex px-4 sm:px-5 py-2 sm:py-2.5 bg-gradient-to-r from-emerald-600 to-green-600 hover:from-emerald-500 hover:to-green-500 text-white text-sm rounded-xl font-bold transition-all transform hover:scale-105 shadow-lg shadow-emerald-500/20 opacity-0 group-hover/task:opacity-100">
                                        Review
                                    </Link>
                                    <Link :href="route('admin.documents.show', task.id)" 
                                        class="sm:hidden flex-shrink-0 w-8 h-8 flex items-center justify-center bg-gradient-to-r from-emerald-600 to-green-600 text-white text-sm rounded-lg font-bold">
                                        →
                                    </Link>
                                </div>
                            </div>
                            
                            <div v-if="pendingTasks?.length === 0" class="p-12 sm:p-16 text-center">
                                <div class="text-5xl sm:text-6xl mb-4 opacity-20 animate-bounce-slow">✨</div>
                                <p class="text-slate-500 text-sm font-medium">All tasks completed</p>
                                <p class="text-slate-600 text-xs mt-1">System status: Optimal</p>
                            </div>
                        </div>
                    </div>

                    <!-- Enhanced Insights Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                        <!-- Department Load -->
                        <div class="glass-card p-4 sm:p-6 animate-fade-in-up" style="animation-delay: 0.2s">
                            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-blue-600/10 to-transparent rounded-full blur-3xl"></div>
                            
                            <h3 class="relative text-slate-400 text-xs font-bold uppercase tracking-widest mb-4 sm:mb-6 flex items-center gap-2">
                                <span class="text-base sm:text-lg">📊</span>
                                Department Load
                            </h3>
                            
                            <div class="relative space-y-4 sm:space-y-5">
                                <div v-for="dept in departmentLoad" :key="dept.name" class="group/dept">
                                    <div class="flex justify-between items-center text-sm mb-2">
                                        <span class="text-white font-medium truncate mr-2">{{ dept.name }}</span>
                                        <span :class="dept.count > dept.capacity * 0.9 ? 'text-red-400 font-bold' : 'text-slate-400'" class="font-mono text-xs flex-shrink-0">
                                            {{ dept.count }}/{{ dept.capacity }}
                                        </span>
                                    </div>
                                    <div class="relative w-full bg-slate-950/50 rounded-full h-3 overflow-hidden border border-white/5 group-hover/dept:border-white/10 transition-colors">
                                        <div :class="`h-full transition-all duration-1000 ease-out ${dept.color} shadow-lg relative overflow-hidden`" 
                                            :style="`width: ${(dept.count / dept.capacity) * 100}%`">
                                            <!-- Shine effect -->
                                            <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent animate-shimmer"></div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div v-if="departmentLoad?.length === 0" class="text-center text-slate-500 text-sm py-8">
                                    <div class="text-3xl mb-2 opacity-20">📊</div>
                                    No department data
                                </div>
                            </div>
                        </div>

                        <!-- Enhanced Quick Actions -->
                        <div class="glass-card p-4 sm:p-6 animate-fade-in-up" style="animation-delay: 0.3s">
                            <div class="absolute inset-0 bg-gradient-to-br from-purple-600/5 to-blue-600/5 rounded-3xl"></div>
                            
                            <h3 class="relative text-purple-300 text-xs font-bold uppercase tracking-widest mb-4 sm:mb-6 flex items-center gap-2">
                                <span class="text-base sm:text-lg">⚡</span>
                                Quick Actions
                            </h3>
                            
                            <div class="relative grid grid-cols-2 gap-3">
                                <Link :href="route('admin.documents.index')" 
                                    class="action-card from-indigo-600/30 to-purple-600/30 border-indigo-500/50">
                                    <div class="action-card-icon">📄</div>
                                    <span class="action-card-text">Documents</span>
                                </Link>
                                
                                <div class="action-card from-red-600/30 to-orange-600/30 border-red-500/50 cursor-pointer">
                                    <div class="action-card-icon">🚨</div>
                                    <span class="action-card-text">Emergency</span>
                                </div>

                                <div class="action-card from-emerald-600/30 to-green-600/30 border-emerald-500/50 cursor-pointer">
                                    <div class="action-card-icon">💹</div>
                                    <span class="action-card-text">Analytics</span>
                                </div>

                                <div class="action-card from-purple-600/30 to-pink-600/30 border-purple-500/50 cursor-pointer">
                                    <div class="action-card-icon">👔</div>
                                    <span class="action-card-text">HR Portal</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="lg:col-span-4 space-y-4 sm:space-y-6">
                    
                    <!-- Enhanced Live Map -->
                    <div class="glass-card p-2 h-64 sm:h-80 group/map animate-fade-in-up" style="animation-delay: 0.4s">
                        <div class="absolute top-4 left-4 z-10 bg-black/80 backdrop-blur-md px-3 py-2 rounded-xl text-xs text-emerald-400 font-bold border border-emerald-500/30 flex items-center gap-2 shadow-xl">
                            <span class="relative flex h-2 w-2">
                              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                              <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                            </span>
                            LIVE
                        </div>
                        <div class="w-full h-full rounded-2xl overflow-hidden opacity-90 group-hover/map:opacity-100 transition-all duration-500 transform group-hover/map:scale-[1.01]">
                            <DashboardMap />
                        </div>
                    </div>

                    <!-- Enhanced Activity Feed -->
                    <div class="glass-card animate-fade-in-up" style="animation-delay: 0.5s">
                        <div class="sticky top-0 z-10 px-4 sm:px-6 py-4 sm:py-5 border-b border-white/10 bg-gradient-to-r from-slate-900/95 to-transparent backdrop-blur-md rounded-t-3xl">
                            <h3 class="text-white text-sm font-bold uppercase tracking-widest flex items-center gap-2">
                                <span class="text-lg sm:text-xl">📋</span>
                                <span class="bg-gradient-to-r from-white to-slate-300 bg-clip-text text-transparent">Activity</span>
                            </h3>
                        </div>
                        
                        <div class="p-4 sm:p-6 max-h-[400px] sm:max-h-[450px] overflow-y-auto premium-scrollbar">
                            <div class="space-y-4">
                                <div v-for="(item, index) in recentActivity" :key="item.id" 
                                    class="relative pl-6 pb-4 border-l-2 border-slate-700/50 hover:border-blue-500/50 transition-all group/activity"
                                    :style="`animation: slideInLeft 0.4s ease-out ${index * 0.05}s backwards`">
                                    <div class="absolute -left-[9px] top-0 w-4 h-4 rounded-full bg-gradient-to-br from-blue-500 to-purple-500 border-2 border-slate-900 transform group-hover/activity:scale-125 transition-transform shadow-lg shadow-blue-500/30"></div>
                                    <div>
                                        <p class="text-sm text-slate-300">
                                            <span class="font-bold text-white">{{ item.user }}</span> 
                                            <span class="text-slate-400">{{ item.action }}</span>
                                        </p>
                                        <p class="text-xs text-slate-500 mt-1 font-mono">{{ item.time }}</p>
                                    </div>
                                </div>
                                
                                <div v-if="!recentActivity?.length" class="text-center py-12 sm:py-16">
                                    <div class="text-4xl sm:text-5xl mb-3 opacity-10 animate-pulse">📡</div>
                                    <p class="text-slate-500 text-sm">Monitoring activity...</p>
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
/* Glass Card Component */
.glass-card {
    position: relative;
    overflow: hidden;
    background: rgba(15, 23, 42, 0.7);
    backdrop-filter: blur(40px) saturate(180%);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 1.5rem;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7),
                0 0 0 1px rgba(255, 255, 255, 0.05) inset;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.glass-card:hover {
    border-color: rgba(255, 255, 255, 0.15);
    box-shadow: 0 30px 60px -12px rgba(0, 0, 0, 0.8),
                0 0 0 1px rgba(255, 255, 255, 0.1) inset;
}

/* Stat Cards */
.stat-card {
    position: relative;
    cursor: pointer;
}

.stat-card-content {
    position: relative;
    background: linear-gradient(to bottom right, var(--tw-gradient-stops));
    border: 1px solid;
    border-radius: 1rem;
    padding: 0.875rem;
    backdrop-filter: blur(20px);
    transform: translateY(0);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.stat-card:hover .stat-card-content {
    transform: translateY(-2px) scale(1.02);
}

.stat-header {
    display: flex;
    align-items: center;
    gap: 0.375rem;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 0.5rem;
}

.stat-value {
    font-size: clamp(1.25rem, 3vw, 1.5rem);
    font-weight: 700;
    background: linear-gradient(to right, var(--tw-gradient-stops));
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
    font-family: ui-monospace, monospace;
    line-height: 1.2;
}

.stat-label {
    font-size: 0.75rem;
    margin-top: 0.25rem;
}

/* Action Cards */
.action-card {
    position: relative;
    overflow: hidden;
    padding: 1rem;
    background: linear-gradient(to bottom right, rgba(30, 41, 59, 0.6), rgba(15, 23, 42, 0.6));
    border: 1px solid;
    border-radius: 1rem;
    text-align: center;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    transform: translateY(0);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
}

.action-card::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(to bottom right, var(--tw-gradient-stops));
    opacity: 0;
    transition: opacity 0.3s;
}

.action-card:hover {
    transform: translateY(-4px) scale(1.03);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.4);
}

.action-card:hover::before {
    opacity: 0.15;
}

.action-card:active {
    transform: translateY(-2px) scale(1.01);
}

.action-card-icon {
    position: relative;
    font-size: 2rem;
    margin-bottom: 0.5rem;
    transition: transform 0.3s;
}

.action-card:hover .action-card-icon {
    transform: scale(1.15) rotate(5deg);
}

.action-card-text {
    position: relative;
    font-size: 0.75rem;
    color: white;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

/* Animations */
@keyframes slideInRight {
    from {
        opacity: 0;
        transform: translateX(-30px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes slideInLeft {
    from {
        opacity: 0;
        transform: translateX(20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in-up {
    animation: fadeInUp 0.6s ease-out backwards;
}

@keyframes pulseSlow {
    0%, 100% { opacity: 0.8; }
    50% { opacity: 1; }
}

.animate-pulse-slow {
    animation: pulseSlow 4s ease-in-out infinite;
}

@keyframes bounceSlow {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}

.animate-bounce-slow {
    animation: bounceSlow 3s ease-in-out infinite;
}

@keyframes shimmer {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}

.animate-shimmer {
    animation: shimmer 2s infinite;
}

@keyframes spinSlow {
    to { transform: rotate(360deg); }
}

.animate-spin-slow {
    animation: spinSlow 20s linear infinite;
}

/* Scrollbar */
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

/* Responsive optimizations */
@media (max-width: 640px) {
    .glass-card {
        border-radius: 1.25rem;
    }
}

/* Reduced motion */
@media (prefers-reduced-motion: reduce) {
    *,
    *::before,
    *::after {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
}
</style>