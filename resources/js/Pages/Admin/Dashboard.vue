<script setup>
import DashboardMap from '@/Components/DashboardMap.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed, onMounted, onUnmounted, ref, watch } from 'vue';

const user = usePage().props.auth.user;

const props = defineProps({
    stats:          { type: Object, default: () => ({ revenue: 0, citizens: 0 }) },
    recentActivity: { type: Array,  default: () => [] },
    pendingTasks:   { type: Array,  default: () => [] },
    departmentLoad: { type: Array,  default: () => [] },
    // ── Workflow sections ───────────────────────────────
    socialAid: {
        type: Object,
        default: () => ({ stats: {}, byProgram: [], recent: [] }),
    },
    health: {
        type: Object,
        default: () => ({ stats: {}, byType: [], recent: [] }),
    },
    environment: {
        type: Object,
        default: () => ({ stats: {}, bySeverity: [], recent: [] }),
    },
});

// ── Loading / Error / Toast ──────────────────────────────────────
const isLoading = ref(false);
const error     = ref(null);
const retryCount = ref(0);
const maxRetries  = 3;

const toast = ref({ show: false, message: '', type: 'info' });
const showToast = (message, type = 'info') => {
    toast.value = { show: true, message, type };
    setTimeout(() => { toast.value.show = false; }, 4000);
};

// ── Safe accessors ───────────────────────────────────────────────
const safeStats = computed(() => ({
    revenue:  Number(props.stats?.revenue)  || 0,
    citizens: Number(props.stats?.citizens) || 0,
}));

const safePendingTasks   = computed(() => (Array.isArray(props.pendingTasks)   ? props.pendingTasks.filter(t => t?.id)   : []));
const safeRecentActivity = computed(() => (Array.isArray(props.recentActivity) ? props.recentActivity.filter(i => i?.id) : []));
const safeDepartmentLoad = computed(() => (Array.isArray(props.departmentLoad) ? props.departmentLoad.filter(d => d?.name) : []));

// ── Workflow data ────────────────────────────────────────────────
const socialStats   = computed(() => props.socialAid?.stats     ?? {});
const socialRecent  = computed(() => props.socialAid?.recent    ?? []);
const socialPrograms= computed(() => props.socialAid?.byProgram ?? []);

const healthStats   = computed(() => props.health?.stats   ?? {});
const healthRecent  = computed(() => props.health?.recent  ?? []);
const healthTypes   = computed(() => props.health?.byType  ?? []);

const envStats      = computed(() => props.environment?.stats      ?? {});
const envRecent     = computed(() => props.environment?.recent     ?? []);
const envSeverities = computed(() => props.environment?.bySeverity ?? []);

// ── Refresh ──────────────────────────────────────────────────────
const refreshDashboard = async () => {
    if (isLoading.value) return;
    try {
        isLoading.value = true;
        error.value = null;
        router.reload({
            only: ['stats','recentActivity','pendingTasks','departmentLoad','socialAid','health','environment'],
            onError: (errors) => {
                error.value = 'Failed to refresh dashboard data';
                showToast('Failed to refresh dashboard', 'error');
                if (retryCount.value < maxRetries) {
                    retryCount.value++;
                    showToast(`Retrying… (${retryCount.value}/${maxRetries})`, 'warning');
                    setTimeout(refreshDashboard, 10000 * retryCount.value);
                }
            },
            onSuccess: () => { retryCount.value = 0; showToast('Dashboard refreshed', 'success'); },
            onFinish:  () => { isLoading.value = false; },
        });
    } catch (err) {
        error.value = 'An unexpected error occurred';
        showToast('An unexpected error occurred', 'error');
        isLoading.value = false;
    }
};

const formatMoney = (value) => {
    try {
        return new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' }).format(value || 0);
    } catch { return '₱0.00'; }
};

// ── Animated counters ────────────────────────────────────────────
const animatedRevenue  = ref(0);
const animatedCitizens = ref(0);

const animateValue = (start, end, duration, callback) => {
    const range     = end - start;
    const increment = range / (duration / 16);
    let current     = start;
    const timer = setInterval(() => {
        current += increment;
        if ((increment > 0 && current >= end) || (increment < 0 && current <= end)) {
            current = end;
            clearInterval(timer);
        }
        callback(Math.floor(current));
    }, 16);
    setTimeout(() => clearInterval(timer), duration + 100);
};

// ── Clock ────────────────────────────────────────────────────────
const time = ref('');
const date = ref('');
const updateTime = () => {
    const now = new Date();
    time.value = now.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', second: '2-digit' });
    date.value = now.toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' });
};

const systemStatus = computed(() => {
    const total = safePendingTasks.value.length;
    if (total === 0)  return { text: 'Optimal',  color: 'text-emerald-400', bg: 'bg-emerald-500', glow: 'shadow-emerald-500/50' };
    if (total < 5)   return { text: 'Nominal',  color: 'text-blue-400',    bg: 'bg-blue-500',    glow: 'shadow-blue-500/50' };
    if (total < 10)  return { text: 'Active',   color: 'text-amber-400',   bg: 'bg-amber-500',   glow: 'shadow-amber-500/50' };
    return { text: 'Critical', color: 'text-red-400', bg: 'bg-red-500', glow: 'shadow-red-500/50' };
});

// ── Mouse parallax ───────────────────────────────────────────────
const mouseX = ref(0);
const mouseY = ref(0);
let mouseRAF = null;
const handleMouseMove = (e) => {
    if (mouseRAF) return;
    mouseRAF = requestAnimationFrame(() => {
        mouseX.value = (e.clientX / window.innerWidth  - 0.5) * 20;
        mouseY.value = (e.clientY / window.innerHeight - 0.5) * 20;
        mouseRAF = null;
    });
};

let clockTimer, autoRefreshTimer;
onMounted(() => {
    updateTime();
    clockTimer = setInterval(updateTime, 1000);
    animateValue(0, safeStats.value.revenue,  2000, v => { animatedRevenue.value  = v; });
    animateValue(0, safeStats.value.citizens, 2000, v => { animatedCitizens.value = v; });
    window.addEventListener('mousemove', handleMouseMove, { passive: true });
    autoRefreshTimer = setInterval(refreshDashboard, 30000);
});

onUnmounted(() => {
    clearInterval(clockTimer);
    clearInterval(autoRefreshTimer);
    window.removeEventListener('mousemove', handleMouseMove);
    if (mouseRAF) cancelAnimationFrame(mouseRAF);
});

watch(() => props.stats, (newStats) => {
    if (!newStats) return;
    animateValue(animatedRevenue.value,  newStats.revenue  || 0, 1000, v => { animatedRevenue.value  = v; });
    animateValue(animatedCitizens.value, newStats.citizens || 0, 1000, v => { animatedCitizens.value = v; });
}, { deep: true });

// ── Helpers ──────────────────────────────────────────────────────
const getPriorityBadge = (status) => ({
    critical: { icon: '🔴', class: 'from-red-600 to-rose-700',    text: 'CRITICAL', border: 'border-red-500/40',   glow: 'shadow-red-500/40' },
    urgent:   { icon: '🟡', class: 'from-amber-600 to-orange-600', text: 'URGENT',   border: 'border-amber-500/40', glow: 'shadow-amber-500/40' },
    normal:   { icon: '🔵', class: 'from-blue-600 to-indigo-700',  text: 'NORMAL',   border: 'border-blue-500/40',  glow: 'shadow-blue-500/40' },
}[status] ?? { icon: '🔵', class: 'from-blue-600 to-indigo-700', text: 'NORMAL', border: 'border-blue-500/40', glow: 'shadow-blue-500/40' });

const getAvatarGradient = (name) => {
    const g = ['from-blue-500 via-blue-600 to-indigo-600','from-purple-500 via-purple-600 to-pink-600',
               'from-emerald-500 via-teal-600 to-cyan-600','from-orange-500 via-red-600 to-pink-600','from-amber-500 via-yellow-600 to-orange-600'];
    const h = name?.split('').reduce((a, c) => a + c.charCodeAt(0), 0) || 0;
    return g[h % g.length];
};

// ── Workflow-specific helpers ─────────────────────────────────────
const statusBadge = (status) => {
    const map = {
        pending:      'bg-amber-500/20 text-amber-300  border-amber-500/30',
        under_review: 'bg-blue-500/20  text-blue-300   border-blue-500/30',
        approved:     'bg-emerald-500/20 text-emerald-300 border-emerald-500/30',
        released:     'bg-cyan-500/20  text-cyan-300   border-cyan-500/30',
        scheduled:    'bg-violet-500/20 text-violet-300 border-violet-500/30',
        completed:    'bg-emerald-500/20 text-emerald-300 border-emerald-500/30',
        rejected:     'bg-red-500/20   text-red-300    border-red-500/30',
        acknowledged: 'bg-blue-500/20  text-blue-300   border-blue-500/30',
        investigating:'bg-orange-500/20 text-orange-300 border-orange-500/30',
        resolved:     'bg-emerald-500/20 text-emerald-300 border-emerald-500/30',
        dismissed:    'bg-slate-500/20 text-slate-300  border-slate-500/30',
    };
    return (map[status] ?? 'bg-slate-500/20 text-slate-300 border-slate-500/30') + ' px-2 py-0.5 rounded-full text-[10px] font-bold border';
};

const severityColor = (s) => ({
    low:      'text-blue-400 bg-blue-500/10 border-blue-500/20',
    moderate: 'text-amber-400 bg-amber-500/10 border-amber-500/20',
    high:     'text-orange-400 bg-orange-500/10 border-orange-500/20',
    critical: 'text-red-400 bg-red-500/10 border-red-500/20',
}[s] ?? 'text-slate-400 bg-slate-500/10 border-slate-500/20');

const programIcon = (p) => ({
    'student_assistance':   '🎓',
    'senior_citizen':       '👴',
    'pwd_assistance':       '♿',
    'solo_parent':          '👩‍👧',
    'calamity_aid':         '🆘',
}[p] ?? '🤝');

const healthIcon = (t) => ({
    'medical_assistance':   '💊',
    'free_consultation':    '🩺',
    'medicine_request':     '💉',
    'laboratory_request':   '🧪',
    'mental_health_support':'🧠',
}[t] ?? '🏥');

const envIcon = (t) => ({
    'illegal_dumping':      '🗑️',
    'air_pollution':        '🌫️',
    'water_contamination':  '💧',
    'flood_hazard':         '🌊',
    'deforestation':        '🌳',
    'noise_pollution':      '📢',
}[t] ?? '🌿');

const formatLabel = (str) => str?.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()) ?? '—';
</script>

<template>
    <Head title="Command Center" />

    <AuthenticatedLayout>
        <!-- Toast -->
        <Transition name="toast">
            <div v-if="toast.show" :class="['toast', `toast-${toast.type}`]">
                <span class="toast-icon">
                    <span v-if="toast.type === 'success'">✓</span>
                    <span v-else-if="toast.type === 'error'">✕</span>
                    <span v-else-if="toast.type === 'warning'">⚠</span>
                    <span v-else>ℹ</span>
                </span>
                <span class="toast-message">{{ toast.message }}</span>
            </div>
        </Transition>

        <!-- Error Banner -->
        <Transition name="error-banner">
            <div v-if="error" class="error-banner">
                <div class="error-banner-content">
                    <span class="error-icon">⚠️</span>
                    <span class="error-text">{{ error }}</span>
                    <button @click="refreshDashboard" class="error-retry-btn">Retry</button>
                    <button @click="error = null"      class="error-close-btn">✕</button>
                </div>
            </div>
        </Transition>

        <!-- Loading Overlay -->
        <Transition name="loading-overlay">
            <div v-if="isLoading" class="loading-overlay">
                <div class="loading-spinner">
                    <div class="spinner-ring"></div>
                    <div class="spinner-ring"></div>
                    <div class="spinner-ring"></div>
                </div>
                <p class="loading-text">Refreshing dashboard…</p>
            </div>
        </Transition>

        <!-- Background -->
        <div class="fixed inset-0 z-0 pointer-events-none overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950"></div>
            <div class="absolute inset-0 opacity-[0.03] bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxwYXRoIGQ9Ik0zNiAxOGMzLjMxNCAwIDYgMi42ODYgNiA2cy0yLjY4NiA2LTYgNi02LTIuNjg2LTYtNiAyLjY4Ni02IDYtNiIgc3Ryb2tlPSIjZmZmIi8+PC9nPjwvc3ZnPg==')]"></div>
            <div class="absolute inset-0 bg-gradient-to-tr from-blue-950/30 via-transparent to-purple-950/30"></div>
            <div class="absolute top-0 right-1/4 w-[800px] h-[800px] bg-blue-500/15 rounded-full blur-[150px] animate-pulse-slow"
                 :style="{ transform: `translate(${mouseX}px, ${mouseY}px)` }"></div>
            <div class="absolute bottom-0 left-1/4 w-[700px] h-[700px] bg-purple-500/15 rounded-full blur-[150px] animate-pulse-slow"
                 style="animation-delay:1.5s"
                 :style="{ transform: `translate(${-mouseX}px, ${-mouseY}px)` }"></div>
            <div class="absolute top-1/2 left-1/2 w-[600px] h-[600px] bg-emerald-500/10 rounded-full blur-[150px] animate-pulse-slow"
                 style="animation-delay:3s"
                 :style="{ transform: `translate(${mouseX*.5}px, ${mouseY*.5}px)` }"></div>
            <div class="absolute inset-0 bg-[linear-gradient(to_right,rgba(255,255,255,0.02)_1px,transparent_1px),linear-gradient(to_bottom,rgba(255,255,255,0.02)_1px,transparent_1px)] bg-[size:4rem_4rem] [mask-image:radial-gradient(ellipse_80%_50%_at_50%_0%,#000_70%,transparent_110%)]"></div>
        </div>

        <div class="relative z-10 max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 space-y-6">

            <!-- ══ HEADER ══════════════════════════════════════════ -->
            <div class="glass-card group animate-fade-in-up">
                <div class="absolute inset-0 bg-gradient-to-r from-blue-600/5 via-purple-600/5 to-pink-600/5 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                <div class="relative p-4 sm:p-6 lg:p-8">
                    <div class="flex flex-col lg:flex-row justify-between gap-6">
                        <!-- User info -->
                        <div class="flex items-center gap-4 sm:gap-6">
                            <div class="relative group/avatar">
                                <div class="absolute inset-0 rounded-2xl animate-spin-slow">
                                    <div class="absolute inset-0 bg-gradient-to-br from-blue-500 via-purple-500 to-pink-500 rounded-2xl blur-xl opacity-0 group-hover/avatar:opacity-75 transition-opacity duration-500"></div>
                                </div>
                                <div :class="`relative w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-gradient-to-br ${getAvatarGradient(user?.name)} flex items-center justify-center text-2xl sm:text-3xl font-bold text-white shadow-2xl transform group-hover/avatar:scale-105 transition-all duration-300`">
                                    {{ user?.name?.charAt(0) || 'A' }}
                                    <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-white/20 to-transparent opacity-0 group-hover/avatar:opacity-100 transition-opacity"></div>
                                </div>
                                <span class="absolute -bottom-1 -right-1 flex h-4 w-4 sm:h-5 sm:w-5">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-4 w-4 sm:h-5 sm:w-5 bg-emerald-500 border-2 border-slate-900 shadow-lg shadow-emerald-500/50"></span>
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
                            <button @click="refreshDashboard" :disabled="isLoading"
                                class="ml-auto px-3 py-2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-500 hover:to-purple-500 text-white text-xs font-bold rounded-lg transition-all transform hover:scale-105 shadow-lg shadow-blue-500/20 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                                <svg class="w-4 h-4" :class="{ 'animate-spin': isLoading }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                <span class="hidden sm:inline">{{ isLoading ? 'Refreshing…' : 'Refresh' }}</span>
                            </button>
                        </div>

                        <!-- Stats -->
                        <div class="grid grid-cols-3 gap-3 sm:gap-4 lg:gap-6">
                            <div class="stat-card group/stat">
                                <div class="absolute inset-0 bg-gradient-to-br from-emerald-600/20 to-green-600/20 rounded-2xl blur-lg opacity-0 group-hover/stat:opacity-100 transition-opacity duration-500"></div>
                                <div class="relative stat-card-content from-emerald-950/50 to-green-950/50 border-emerald-500/20">
                                    <div class="stat-header text-emerald-400"><span class="text-lg sm:text-xl">💰</span><span class="hidden sm:inline">Revenue</span><span class="sm:hidden">Rev</span></div>
                                    <div class="stat-value from-emerald-400 to-green-400">{{ formatMoney(animatedRevenue).slice(0,-3) }}</div>
                                    <div class="stat-label text-emerald-500/60">Today's</div>
                                </div>
                            </div>
                            <div class="stat-card group/stat">
                                <div class="absolute inset-0 bg-gradient-to-br from-blue-600/20 to-cyan-600/20 rounded-2xl blur-lg opacity-0 group-hover/stat:opacity-100 transition-opacity duration-500"></div>
                                <div class="relative stat-card-content from-blue-950/50 to-cyan-950/50 border-blue-500/20">
                                    <div class="stat-header text-blue-400"><span class="text-lg sm:text-xl">👥</span><span class="hidden sm:inline">Citizens</span><span class="sm:hidden">New</span></div>
                                    <div class="stat-value from-blue-400 to-cyan-400">+{{ animatedCitizens }}</div>
                                    <div class="stat-label text-blue-500/60">Registered</div>
                                </div>
                            </div>
                            <div class="stat-card group/stat">
                                <div class="absolute inset-0 bg-gradient-to-br from-amber-600/20 to-orange-600/20 rounded-2xl blur-lg opacity-0 group-hover/stat:opacity-100 transition-opacity duration-500"></div>
                                <div class="relative stat-card-content from-amber-950/50 to-orange-950/50 border-amber-500/20">
                                    <div class="stat-header text-amber-400"><span class="text-lg sm:text-xl">⚡</span><span class="hidden sm:inline">Pending</span><span class="sm:hidden">Queue</span></div>
                                    <div class="stat-value from-amber-400 to-orange-400">{{ safePendingTasks.length }}</div>
                                    <div class="stat-label text-amber-500/60">Tasks</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ══ MAIN CONTENT GRID ══════════════════════════════ -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 sm:gap-6">

                <!-- Left Column -->
                <div class="lg:col-span-8 space-y-4 sm:space-y-6">

                    <!-- Priority Queue -->
                    <div class="glass-card animate-fade-in-up" style="animation-delay:0.1s">
                        <div class="absolute inset-0 bg-gradient-to-br from-blue-600/5 to-purple-600/5 rounded-3xl"></div>
                        <div class="relative px-4 sm:px-6 py-4 sm:py-5 border-b border-white/10 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 bg-gradient-to-r from-slate-900/50 to-transparent">
                            <h3 class="text-white font-bold text-base sm:text-lg flex items-center gap-2 sm:gap-3">
                                <span class="text-xl sm:text-2xl">⚡</span>
                                <span class="bg-gradient-to-r from-white to-slate-300 bg-clip-text text-transparent">Priority Queue</span>
                                <span class="px-2 sm:px-3 py-1 bg-amber-500/20 border border-amber-500/30 rounded-full text-xs text-amber-400 font-bold">{{ safePendingTasks.length }}</span>
                            </h3>
                            <button class="w-full sm:w-auto px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-500 hover:to-purple-500 text-white text-sm font-bold rounded-xl transition-all transform hover:scale-105 shadow-lg shadow-blue-500/20 active:scale-95">
                                Batch Process
                            </button>
                        </div>
                        <div class="relative divide-y divide-white/5 max-h-[500px] overflow-y-auto premium-scrollbar">
                            <div v-for="(task, index) in safePendingTasks" :key="task.id"
                                class="p-4 sm:p-5 hover:bg-white/5 transition-all group/task cursor-pointer"
                                :style="`animation: slideInRight 0.4s ease-out ${index * 0.05}s backwards`">
                                <div class="flex items-center justify-between gap-3 sm:gap-4">
                                    <div class="flex items-center gap-3 sm:gap-4 flex-1 min-w-0">
                                        <div :class="`w-10 h-10 sm:w-12 sm:h-12 rounded-xl flex items-center justify-center text-lg sm:text-xl font-bold shadow-lg transform group-hover/task:scale-110 group-hover/task:rotate-3 transition-all bg-gradient-to-br ${getPriorityBadge(task.status).class} ${getPriorityBadge(task.status).glow} border ${getPriorityBadge(task.status).border}`">
                                            {{ task.type?.charAt(0) ?? '?' }}
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 mb-1 flex-wrap">
                                                <h4 class="text-white font-semibold text-sm truncate">{{ task.detail }}</h4>
                                                <span :class="`bg-gradient-to-r ${getPriorityBadge(task.status).class} px-2 py-0.5 rounded text-[10px] font-bold text-white shadow-sm border ${getPriorityBadge(task.status).border}`">{{ getPriorityBadge(task.status).text }}</span>
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
                                        class="sm:hidden flex-shrink-0 w-8 h-8 flex items-center justify-center bg-gradient-to-r from-emerald-600 to-green-600 text-white text-sm rounded-lg font-bold">→</Link>
                                </div>
                            </div>
                            <div v-if="safePendingTasks.length === 0" class="p-12 sm:p-16 text-center">
                                <div class="text-5xl sm:text-6xl mb-4 opacity-20 animate-bounce-slow">✨</div>
                                <p class="text-slate-500 text-sm font-medium">All tasks completed</p>
                                <p class="text-slate-600 text-xs mt-1">System status: Optimal</p>
                            </div>
                        </div>
                    </div>

                    <!-- Insights Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                        <!-- Department Load -->
                        <div class="glass-card p-4 sm:p-6 animate-fade-in-up" style="animation-delay:0.2s">
                            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-blue-600/10 to-transparent rounded-full blur-3xl"></div>
                            <h3 class="relative text-slate-400 text-xs font-bold uppercase tracking-widest mb-4 sm:mb-6 flex items-center gap-2">
                                <span class="text-base sm:text-lg">📊</span> Department Load
                            </h3>
                            <div class="relative space-y-4 sm:space-y-5">
                                <div v-for="dept in safeDepartmentLoad" :key="dept.name" class="group/dept">
                                    <div class="flex justify-between items-center text-sm mb-2">
                                        <span class="text-white font-medium truncate mr-2">{{ dept.name }}</span>
                                        <span :class="dept.count > dept.capacity * 0.9 ? 'text-red-400 font-bold' : 'text-slate-400'" class="font-mono text-xs flex-shrink-0">{{ dept.count }}/{{ dept.capacity }}</span>
                                    </div>
                                    <div class="relative w-full bg-slate-950/50 rounded-full h-3 overflow-hidden border border-white/5">
                                        <div :class="`h-full transition-all duration-1000 ease-out ${dept.color} shadow-lg relative overflow-hidden`"
                                             :style="`width:${(dept.count/dept.capacity)*100}%`">
                                            <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent animate-shimmer"></div>
                                        </div>
                                    </div>
                                </div>
                                <div v-if="safeDepartmentLoad.length === 0" class="text-center text-slate-500 text-sm py-8">
                                    <div class="text-3xl mb-2 opacity-20">📊</div>No department data
                                </div>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="glass-card p-4 sm:p-6 animate-fade-in-up" style="animation-delay:0.3s">
                            <div class="absolute inset-0 bg-gradient-to-br from-purple-600/5 to-blue-600/5 rounded-3xl"></div>
                            <h3 class="relative text-purple-300 text-xs font-bold uppercase tracking-widest mb-4 sm:mb-6 flex items-center gap-2">
                                <span class="text-base sm:text-lg">⚡</span> Quick Actions
                            </h3>
                            <div class="relative grid grid-cols-2 gap-3">
                                <Link :href="route('admin.documents.index')" class="action-card from-indigo-600/30 to-purple-600/30 border-indigo-500/50">
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
                                <a :href="route('document.verify')" class="action-card from-cyan-600/30 to-blue-600/30 border-cyan-500/50 col-span-2">
                                    <div class="flex items-center justify-center gap-3">
                                        <span class="text-2xl">🔗</span>
                                        <div class="text-left">
                                            <span class="action-card-text block">Verify Document</span>
                                            <span class="text-[10px] text-cyan-400/70 font-normal normal-case tracking-normal">Blockchain verification portal</span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="lg:col-span-4 space-y-4 sm:space-y-6">
                    <!-- Live Map -->
                    <div class="glass-card p-2 h-64 sm:h-80 group/map animate-fade-in-up" style="animation-delay:0.4s">
                        <div class="absolute top-4 left-4 z-10 bg-black/80 backdrop-blur-md px-3 py-2 rounded-xl text-xs text-emerald-400 font-bold border border-emerald-500/30 flex items-center gap-2 shadow-xl">
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                            </span>LIVE
                        </div>
                        <div class="w-full h-full rounded-2xl overflow-hidden opacity-90 group-hover/map:opacity-100 transition-all duration-500 transform group-hover/map:scale-[1.01]">
                            <DashboardMap />
                        </div>
                    </div>

                    <!-- Activity Feed -->
                    <div class="glass-card animate-fade-in-up" style="animation-delay:0.5s">
                        <div class="sticky top-0 z-10 px-4 sm:px-6 py-4 sm:py-5 border-b border-white/10 bg-gradient-to-r from-slate-900/95 to-transparent backdrop-blur-md rounded-t-3xl">
                            <h3 class="text-white text-sm font-bold uppercase tracking-widest flex items-center gap-2">
                                <span class="text-lg sm:text-xl">📋</span>
                                <span class="bg-gradient-to-r from-white to-slate-300 bg-clip-text text-transparent">Activity</span>
                            </h3>
                        </div>
                        <div class="p-4 sm:p-6 max-h-[400px] sm:max-h-[450px] overflow-y-auto premium-scrollbar">
                            <div class="space-y-4">
                                <div v-for="(item, index) in safeRecentActivity" :key="item.id"
                                    class="relative pl-6 pb-4 border-l-2 border-slate-700/50 hover:border-blue-500/50 transition-all group/activity"
                                    :style="`animation: slideInLeft 0.4s ease-out ${index * 0.05}s backwards`">
                                    <div class="absolute -left-[9px] top-0 w-4 h-4 rounded-full bg-gradient-to-br from-blue-500 to-purple-500 border-2 border-slate-900 transform group-hover/activity:scale-125 transition-transform shadow-lg shadow-blue-500/30"></div>
                                    <div>
                                        <p class="text-sm text-slate-300">
                                            <span class="font-bold text-white">{{ item.user }}</span>
                                            <span class="text-slate-400"> {{ item.action }}</span>
                                        </p>
                                        <p class="text-xs text-slate-500 mt-1 font-mono">{{ item.time }}</p>
                                    </div>
                                </div>
                                <div v-if="safeRecentActivity.length === 0" class="text-center py-12 sm:py-16">
                                    <div class="text-4xl sm:text-5xl mb-3 opacity-10 animate-pulse">📡</div>
                                    <p class="text-slate-500 text-sm">Monitoring activity…</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ══════════════════════════════════════════════════
                 WORKFLOW MONITORING — Social Aid · Health · Environment
            ══════════════════════════════════════════════════════ -->

            <!-- Section title -->
            <div class="flex items-center gap-4 pt-2">
                <div class="h-px flex-1 bg-gradient-to-r from-transparent via-white/10 to-transparent"></div>
                <h2 class="text-xs font-bold text-slate-500 uppercase tracking-[0.2em]">Workflow Monitoring</h2>
                <div class="h-px flex-1 bg-gradient-to-r from-transparent via-white/10 to-transparent"></div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">

                <!-- ── SOCIAL AID ─────────────────────────────── -->
                <div class="glass-card animate-fade-in-up" style="animation-delay:0.1s">
                    <!-- Header -->
                    <div class="px-5 py-4 border-b border-white/8 bg-gradient-to-r from-violet-900/30 to-transparent flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-violet-600 to-purple-700 flex items-center justify-center text-lg shadow-lg shadow-violet-500/30">🤝</div>
                            <div>
                                <h3 class="text-white font-bold text-sm">Social Aid</h3>
                                <p class="text-violet-400/70 text-[11px]">Assistance programs</p>
                            </div>
                        </div>
                        <Link :href="route('admin.social-aid.index')" class="text-[11px] font-bold text-violet-400 hover:text-violet-300 transition-colors">
                            View All →
                        </Link>
                    </div>

                    <!-- Status pill row -->
                    <div class="px-5 py-4 grid grid-cols-3 gap-2 border-b border-white/5">
                        <div class="text-center p-2.5 rounded-xl bg-amber-500/10 border border-amber-500/20">
                            <div class="text-xl font-bold text-amber-300">{{ socialStats.pending ?? 0 }}</div>
                            <div class="text-[10px] text-amber-400/70 mt-0.5 uppercase tracking-wide">Pending</div>
                        </div>
                        <div class="text-center p-2.5 rounded-xl bg-blue-500/10 border border-blue-500/20">
                            <div class="text-xl font-bold text-blue-300">{{ socialStats.under_review ?? 0 }}</div>
                            <div class="text-[10px] text-blue-400/70 mt-0.5 uppercase tracking-wide">Review</div>
                        </div>
                        <div class="text-center p-2.5 rounded-xl bg-emerald-500/10 border border-emerald-500/20">
                            <div class="text-xl font-bold text-emerald-300">{{ (socialStats.approved ?? 0) + (socialStats.released ?? 0) }}</div>
                            <div class="text-[10px] text-emerald-400/70 mt-0.5 uppercase tracking-wide">Approved</div>
                        </div>
                    </div>

                    <!-- Program breakdown -->
                    <div class="px-5 py-3 space-y-2.5 border-b border-white/5">
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-3">By Program</p>
                        <div v-for="prog in socialPrograms" :key="prog.label" class="flex items-center gap-3">
                            <span class="text-base w-6 text-center">{{ programIcon(prog.label) }}</span>
                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between mb-1">
                                    <span class="text-xs text-slate-300 truncate">{{ formatLabel(prog.label) }}</span>
                                    <span class="text-xs font-bold text-violet-300 ml-2">{{ prog.count }}</span>
                                </div>
                                <div class="h-1.5 w-full bg-slate-800 rounded-full overflow-hidden">
                                    <div class="h-full bg-gradient-to-r from-violet-500 to-purple-500 rounded-full"
                                         :style="`width:${Math.min((prog.count / (socialStats.total || 1)) * 100, 100)}%`"></div>
                                </div>
                            </div>
                        </div>
                        <div v-if="!socialPrograms.length" class="text-center py-3 text-slate-600 text-xs">No data yet</div>
                    </div>

                    <!-- Recent -->
                    <div class="px-5 py-4 space-y-3 max-h-64 overflow-y-auto premium-scrollbar">
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-3">Recent Applications</p>
                        <div v-for="item in socialRecent" :key="item.id"
                            class="flex items-center gap-3 p-2.5 rounded-xl bg-white/[0.03] hover:bg-white/[0.06] border border-white/5 transition-colors group/row">
                            <span class="text-xl flex-shrink-0">{{ programIcon(item.program) }}</span>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="text-xs font-semibold text-white truncate">{{ item.name }}</span>
                                    <span :class="statusBadge(item.status)">{{ formatLabel(item.status) }}</span>
                                </div>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="text-[10px] text-slate-500 font-mono">{{ item.tracking }}</span>
                                    <span class="text-slate-700">·</span>
                                    <span class="text-[10px] text-slate-500">{{ item.time }}</span>
                                </div>
                            </div>
                            <Link :href="route('admin.social-aid.show', item.id)"
                                class="opacity-0 group-hover/row:opacity-100 transition-opacity flex-shrink-0 w-7 h-7 flex items-center justify-center bg-violet-600/40 hover:bg-violet-600/70 rounded-lg text-white text-xs font-bold">→</Link>
                        </div>
                        <div v-if="!socialRecent.length" class="text-center py-6 text-slate-600 text-xs">
                            <div class="text-3xl opacity-20 mb-2">🤝</div>No recent applications
                        </div>
                    </div>
                </div>

                <!-- ── HEALTH ──────────────────────────────────── -->
                <div class="glass-card animate-fade-in-up" style="animation-delay:0.2s">
                    <!-- Header -->
                    <div class="px-5 py-4 border-b border-white/8 bg-gradient-to-r from-rose-900/30 to-transparent flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-rose-600 to-pink-700 flex items-center justify-center text-lg shadow-lg shadow-rose-500/30">🏥</div>
                            <div>
                                <h3 class="text-white font-bold text-sm">Health Services</h3>
                                <p class="text-rose-400/70 text-[11px]">Medical assistance</p>
                            </div>
                        </div>
                        <Link :href="route('admin.health.index')" class="text-[11px] font-bold text-rose-400 hover:text-rose-300 transition-colors">
                            View All →
                        </Link>
                    </div>

                    <!-- Status pills -->
                    <div class="px-5 py-4 grid grid-cols-3 gap-2 border-b border-white/5">
                        <div class="text-center p-2.5 rounded-xl bg-amber-500/10 border border-amber-500/20">
                            <div class="text-xl font-bold text-amber-300">{{ healthStats.pending ?? 0 }}</div>
                            <div class="text-[10px] text-amber-400/70 mt-0.5 uppercase tracking-wide">Pending</div>
                        </div>
                        <div class="text-center p-2.5 rounded-xl bg-violet-500/10 border border-violet-500/20">
                            <div class="text-xl font-bold text-violet-300">{{ healthStats.scheduled ?? 0 }}</div>
                            <div class="text-[10px] text-violet-400/70 mt-0.5 uppercase tracking-wide">Scheduled</div>
                        </div>
                        <div class="text-center p-2.5 rounded-xl bg-emerald-500/10 border border-emerald-500/20">
                            <div class="text-xl font-bold text-emerald-300">{{ healthStats.completed ?? 0 }}</div>
                            <div class="text-[10px] text-emerald-400/70 mt-0.5 uppercase tracking-wide">Done</div>
                        </div>
                    </div>

                    <!-- By type -->
                    <div class="px-5 py-3 space-y-2.5 border-b border-white/5">
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-3">By Service Type</p>
                        <div v-for="ht in healthTypes" :key="ht.label" class="flex items-center gap-3">
                            <span class="text-base w-6 text-center">{{ healthIcon(ht.label) }}</span>
                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between mb-1">
                                    <span class="text-xs text-slate-300 truncate">{{ formatLabel(ht.label) }}</span>
                                    <span class="text-xs font-bold text-rose-300 ml-2">{{ ht.count }}</span>
                                </div>
                                <div class="h-1.5 w-full bg-slate-800 rounded-full overflow-hidden">
                                    <div class="h-full bg-gradient-to-r from-rose-500 to-pink-500 rounded-full"
                                         :style="`width:${Math.min((ht.count / (healthStats.total || 1)) * 100, 100)}%`"></div>
                                </div>
                            </div>
                        </div>
                        <div v-if="!healthTypes.length" class="text-center py-3 text-slate-600 text-xs">No data yet</div>
                    </div>

                    <!-- Recent -->
                    <div class="px-5 py-4 space-y-3 max-h-64 overflow-y-auto premium-scrollbar">
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-3">Recent Applications</p>
                        <div v-for="item in healthRecent" :key="item.id"
                            class="flex items-center gap-3 p-2.5 rounded-xl bg-white/[0.03] hover:bg-white/[0.06] border border-white/5 transition-colors group/row">
                            <span class="text-xl flex-shrink-0">{{ healthIcon(item.type) }}</span>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="text-xs font-semibold text-white truncate">{{ item.name }}</span>
                                    <span :class="statusBadge(item.status)">{{ formatLabel(item.status) }}</span>
                                </div>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="text-[10px] text-slate-500 font-mono">{{ item.tracking }}</span>
                                    <span class="text-slate-700">·</span>
                                    <span class="text-[10px] text-slate-500">{{ item.time }}</span>
                                </div>
                            </div>
                            <Link :href="route('admin.health.show', item.id)"
                                class="opacity-0 group-hover/row:opacity-100 transition-opacity flex-shrink-0 w-7 h-7 flex items-center justify-center bg-rose-600/40 hover:bg-rose-600/70 rounded-lg text-white text-xs font-bold">→</Link>
                        </div>
                        <div v-if="!healthRecent.length" class="text-center py-6 text-slate-600 text-xs">
                            <div class="text-3xl opacity-20 mb-2">🏥</div>No recent applications
                        </div>
                    </div>
                </div>

                <!-- ── ENVIRONMENT ─────────────────────────────── -->
                <div class="glass-card animate-fade-in-up" style="animation-delay:0.3s">
                    <!-- Header -->
                    <div class="px-5 py-4 border-b border-white/8 bg-gradient-to-r from-emerald-900/30 to-transparent flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-600 to-teal-700 flex items-center justify-center text-lg shadow-lg shadow-emerald-500/30">🌿</div>
                            <div>
                                <h3 class="text-white font-bold text-sm">Environment</h3>
                                <p class="text-emerald-400/70 text-[11px]">Incident reports</p>
                            </div>
                        </div>
                        <Link :href="route('admin.environment.index')" class="text-[11px] font-bold text-emerald-400 hover:text-emerald-300 transition-colors">
                            View All →
                        </Link>
                    </div>

                    <!-- Status pills — critical alert if any -->
                    <div class="px-5 py-4 space-y-3 border-b border-white/5">
                        <!-- Critical alert banner -->
                        <div v-if="envStats.critical > 0"
                            class="flex items-center gap-2 px-3 py-2 rounded-lg bg-red-500/15 border border-red-500/30 text-red-300 text-xs font-bold">
                            <span class="relative flex h-2 w-2 flex-shrink-0">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-red-400"></span>
                            </span>
                            {{ envStats.critical }} critical report{{ envStats.critical > 1 ? 's' : '' }} need attention
                        </div>
                        <div class="grid grid-cols-3 gap-2">
                            <div class="text-center p-2.5 rounded-xl bg-amber-500/10 border border-amber-500/20">
                                <div class="text-xl font-bold text-amber-300">{{ envStats.pending ?? 0 }}</div>
                                <div class="text-[10px] text-amber-400/70 mt-0.5 uppercase tracking-wide">Pending</div>
                            </div>
                            <div class="text-center p-2.5 rounded-xl bg-orange-500/10 border border-orange-500/20">
                                <div class="text-xl font-bold text-orange-300">{{ envStats.investigating ?? 0 }}</div>
                                <div class="text-[10px] text-orange-400/70 mt-0.5 uppercase tracking-wide">Active</div>
                            </div>
                            <div class="text-center p-2.5 rounded-xl bg-emerald-500/10 border border-emerald-500/20">
                                <div class="text-xl font-bold text-emerald-300">{{ envStats.resolved ?? 0 }}</div>
                                <div class="text-[10px] text-emerald-400/70 mt-0.5 uppercase tracking-wide">Resolved</div>
                            </div>
                        </div>
                    </div>

                    <!-- By severity -->
                    <div class="px-5 py-3 space-y-2 border-b border-white/5">
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-3">By Severity</p>
                        <div v-for="sv in envSeverities" :key="sv.label" class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span :class="`inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border ${severityColor(sv.label)}`">
                                    {{ formatLabel(sv.label) }}
                                </span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="h-1.5 w-16 bg-slate-800 rounded-full overflow-hidden">
                                    <div :class="`h-full rounded-full ${sv.label === 'critical' ? 'bg-red-500' : sv.label === 'high' ? 'bg-orange-500' : sv.label === 'moderate' ? 'bg-amber-500' : 'bg-blue-500'}`"
                                         :style="`width:${Math.min((sv.count / (envStats.total || 1)) * 100, 100)}%`"></div>
                                </div>
                                <span class="text-xs font-bold text-slate-300 w-4 text-right">{{ sv.count }}</span>
                            </div>
                        </div>
                        <div v-if="!envSeverities.length" class="text-center py-3 text-slate-600 text-xs">No data yet</div>
                    </div>

                    <!-- Recent -->
                    <div class="px-5 py-4 space-y-3 max-h-64 overflow-y-auto premium-scrollbar">
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-3">Recent Reports</p>
                        <div v-for="item in envRecent" :key="item.id"
                            class="flex items-center gap-3 p-2.5 rounded-xl bg-white/[0.03] hover:bg-white/[0.06] border border-white/5 transition-colors group/row">
                            <span class="text-xl flex-shrink-0">{{ envIcon(item.type) }}</span>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="text-xs font-semibold text-white truncate">{{ item.name }}</span>
                                    <span :class="statusBadge(item.status)">{{ formatLabel(item.status) }}</span>
                                    <span :class="`inline-flex items-center px-1.5 py-0 rounded text-[9px] font-bold border ${severityColor(item.severity)}`">{{ item.severity }}</span>
                                </div>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="text-[10px] text-slate-500 font-mono">{{ item.tracking }}</span>
                                    <span class="text-slate-700">·</span>
                                    <span class="text-[10px] text-slate-500">{{ item.time }}</span>
                                </div>
                            </div>
                            <Link :href="route('admin.environment.show', item.id)"
                                class="opacity-0 group-hover/row:opacity-100 transition-opacity flex-shrink-0 w-7 h-7 flex items-center justify-center bg-emerald-600/40 hover:bg-emerald-600/70 rounded-lg text-white text-xs font-bold">→</Link>
                        </div>
                        <div v-if="!envRecent.length" class="text-center py-6 text-slate-600 text-xs">
                            <div class="text-3xl opacity-20 mb-2">🌿</div>No recent reports
                        </div>
                    </div>
                </div>

            </div>
            <!-- END Workflow Monitoring -->

        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.glass-card {
    position: relative; overflow: hidden;
    background: rgba(15, 23, 42, 0.7);
    backdrop-filter: blur(40px) saturate(180%);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 1.5rem;
    box-shadow: 0 25px 50px -12px rgba(0,0,0,0.7), 0 0 0 1px rgba(255,255,255,0.05) inset;
    transition: all 0.3s cubic-bezier(0.4,0,0.2,1);
}
.glass-card:hover {
    border-color: rgba(255,255,255,0.15);
    box-shadow: 0 30px 60px -12px rgba(0,0,0,0.8), 0 0 0 1px rgba(255,255,255,0.1) inset;
}

.stat-card { position: relative; cursor: pointer; }
.stat-card-content {
    position: relative; background: linear-gradient(to bottom right, var(--tw-gradient-stops));
    border: 1px solid; border-radius: 1rem; padding: 0.875rem;
    backdrop-filter: blur(20px);
    transition: all 0.3s cubic-bezier(0.4,0,0.2,1);
}
.stat-card:hover .stat-card-content { transform: translateY(-2px) scale(1.02); }
.stat-header { display: flex; align-items: center; gap: 0.375rem; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem; }
.stat-value { font-size: clamp(1.25rem, 3vw, 1.5rem); font-weight: 700; background: linear-gradient(to right, var(--tw-gradient-stops)); -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent; font-family: ui-monospace, monospace; line-height: 1.2; }
.stat-label { font-size: 0.75rem; margin-top: 0.25rem; }

.action-card {
    position: relative; overflow: hidden; padding: 1rem;
    background: linear-gradient(to bottom right, rgba(30,41,59,0.6), rgba(15,23,42,0.6));
    border: 1px solid; border-radius: 1rem; text-align: center;
    transition: all 0.3s cubic-bezier(0.4,0,0.2,1);
    box-shadow: 0 4px 20px rgba(0,0,0,0.3);
}
.action-card::before { content: ''; position: absolute; inset: 0; background: linear-gradient(to bottom right, var(--tw-gradient-stops)); opacity: 0; transition: opacity 0.3s; }
.action-card:hover { transform: translateY(-4px) scale(1.03); box-shadow: 0 8px 30px rgba(0,0,0,0.4); }
.action-card:hover::before { opacity: 0.15; }
.action-card:active { transform: translateY(-2px) scale(1.01); }
.action-card-icon { position: relative; font-size: 2rem; margin-bottom: 0.5rem; transition: transform 0.3s; }
.action-card:hover .action-card-icon { transform: scale(1.15) rotate(5deg); }
.action-card-text { position: relative; font-size: 0.75rem; color: white; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; }

@keyframes slideInRight { from { opacity: 0; transform: translateX(-30px); } to { opacity: 1; transform: translateX(0); } }
@keyframes slideInLeft  { from { opacity: 0; transform: translateX(20px); }  to { opacity: 1; transform: translateX(0); } }
@keyframes fadeInUp     { from { opacity: 0; transform: translateY(20px); }  to { opacity: 1; transform: translateY(0); } }
.animate-fade-in-up { animation: fadeInUp 0.6s ease-out backwards; }

@keyframes pulseSlow    { 0%, 100% { opacity: 0.8; } 50% { opacity: 1; } }
.animate-pulse-slow     { animation: pulseSlow 4s ease-in-out infinite; }
@keyframes bounceSlow   { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }
.animate-bounce-slow    { animation: bounceSlow 3s ease-in-out infinite; }
@keyframes shimmer      { 0% { transform: translateX(-100%); } 100% { transform: translateX(100%); } }
.animate-shimmer        { animation: shimmer 2s infinite; }
@keyframes spinSlow     { to { transform: rotate(360deg); } }
.animate-spin-slow      { animation: spinSlow 20s linear infinite; }

.premium-scrollbar::-webkit-scrollbar { width: 4px; }
.premium-scrollbar::-webkit-scrollbar-track { background: rgba(15,23,42,0.5); border-radius: 10px; }
.premium-scrollbar::-webkit-scrollbar-thumb { background: linear-gradient(to bottom, rgba(99,102,241,0.4), rgba(139,92,246,0.4)); border-radius: 10px; }
.premium-scrollbar::-webkit-scrollbar-thumb:hover { background: linear-gradient(to bottom, rgba(99,102,241,0.7), rgba(139,92,246,0.7)); }

@media (max-width: 640px) { .glass-card { border-radius: 1.25rem; } }
@media (prefers-reduced-motion: reduce) {
    *, *::before, *::after { animation-duration: 0.01ms !important; animation-iteration-count: 1 !important; transition-duration: 0.01ms !important; }
}

/* ── Toast ────────────────────────────────────────────────────── */
.toast { position: fixed; top: 6rem; right: 1.5rem; z-index: 100; display: flex; align-items: center; gap: 0.75rem; padding: 1rem 1.5rem; border-radius: 0.75rem; background: rgba(18,18,20,0.95); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.1); box-shadow: 0 10px 30px rgba(0,0,0,0.5); max-width: 24rem; }
.toast-success { border-color: rgba(34,197,94,0.3); background: rgba(34,197,94,0.1); }
.toast-error   { border-color: rgba(239,68,68,0.3);  background: rgba(239,68,68,0.1); }
.toast-warning { border-color: rgba(245,158,11,0.3); background: rgba(245,158,11,0.1); }
.toast-info    { border-color: rgba(59,130,246,0.3);  background: rgba(59,130,246,0.1); }
.toast-icon { display: flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; border-radius: 50%; font-size: 1rem; font-weight: 700; flex-shrink: 0; }
.toast-success .toast-icon { background: rgba(34,197,94,0.2);  color: rgb(134,239,172); }
.toast-error   .toast-icon { background: rgba(239,68,68,0.2);  color: rgb(252,165,165); }
.toast-warning .toast-icon { background: rgba(245,158,11,0.2); color: rgb(251,191,36); }
.toast-info    .toast-icon { background: rgba(59,130,246,0.2); color: rgb(147,197,253); }
.toast-message { flex: 1; font-size: 0.875rem; font-weight: 500; color: white; }
.toast-enter-active { animation: toast-in  0.3s ease; }
.toast-leave-active { animation: toast-out 0.3s ease; }
@keyframes toast-in  { from { opacity: 0; transform: translateX(100%); } to { opacity: 1; transform: translateX(0); } }
@keyframes toast-out { to   { opacity: 0; transform: translateX(100%); } }

/* ── Error Banner ─────────────────────────────────────────────── */
.error-banner { position: fixed; top: 5rem; left: 50%; transform: translateX(-50%); z-index: 100; width: calc(100% - 2rem); max-width: 48rem; }
.error-banner-content { display: flex; align-items: center; gap: 1rem; padding: 1rem 1.5rem; border-radius: 0.75rem; background: rgba(239,68,68,0.15); backdrop-filter: blur(20px); border: 1px solid rgba(239,68,68,0.3); box-shadow: 0 10px 30px rgba(239,68,68,0.2); }
.error-icon { font-size: 1.25rem; flex-shrink: 0; }
.error-text { flex: 1; font-size: 0.875rem; font-weight: 600; color: rgb(252,165,165); }
.error-retry-btn { padding: 0.5rem 1rem; border-radius: 0.5rem; background: rgba(239,68,68,0.2); color: white; font-size: 0.75rem; font-weight: 700; transition: all 0.2s; flex-shrink: 0; }
.error-retry-btn:hover { background: rgba(239,68,68,0.3); transform: scale(1.05); }
.error-close-btn { display: flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; border-radius: 0.5rem; color: white; background: rgba(255,255,255,0.05); transition: all 0.2s; flex-shrink: 0; }
.error-close-btn:hover { background: rgba(255,255,255,0.1); }
.error-banner-enter-active { animation: slide-down 0.3s ease; }
.error-banner-leave-active { animation: slide-up   0.3s ease; }
@keyframes slide-down { from { opacity: 0; transform: translateX(-50%) translateY(-100%); } to { opacity: 1; transform: translateX(-50%) translateY(0); } }
@keyframes slide-up   { to   { opacity: 0; transform: translateX(-50%) translateY(-100%); } }

/* ── Loading Overlay ──────────────────────────────────────────── */
.loading-overlay { position: fixed; inset: 0; z-index: 90; display: flex; flex-direction: column; align-items: center; justify-content: center; background: rgba(0,0,0,0.6); backdrop-filter: blur(8px); }
.loading-spinner { position: relative; width: 60px; height: 60px; }
.spinner-ring { position: absolute; inset: 0; border-radius: 50%; border: 3px solid transparent; animation: spinner-rotate 1.5s cubic-bezier(0.5,0,0.5,1) infinite; }
.spinner-ring:nth-child(1) { border-top-color: #3b82f6;   animation-delay: -0.45s; }
.spinner-ring:nth-child(2) { border-right-color: #a855f7; animation-delay: -0.3s; }
.spinner-ring:nth-child(3) { border-bottom-color: #ec4899;animation-delay: -0.15s; }
@keyframes spinner-rotate { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
.loading-text { margin-top: 1.5rem; font-size: 0.875rem; font-weight: 600; color: rgba(255,255,255,0.8); }
.loading-overlay-enter-active { animation: fade-in  0.2s ease; }
.loading-overlay-leave-active { animation: fade-out 0.3s ease; }
@keyframes fade-in  { from { opacity: 0; } to { opacity: 1; } }
@keyframes fade-out { to   { opacity: 0; } }
</style>