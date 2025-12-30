<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { onMounted, onUnmounted, ref } from 'vue';

const props = defineProps({
    requests: Array
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

onMounted(() => {
    window.addEventListener('mousemove', handleMouseMove, { passive: true });
});

onUnmounted(() => {
    window.removeEventListener('mousemove', handleMouseMove);
    if (mouseRAF) cancelAnimationFrame(mouseRAF);
});

const formatDate = (dateString) => {
    const options = { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' };
    return new Date(dateString).toLocaleDateString('en-US', options);
};

const getStatusConfig = (status) => {
    const configs = {
        pending: {
            gradient: 'from-amber-600 to-yellow-600',
            glow: 'shadow-amber-500/40',
            border: 'border-amber-500/30',
            bg: 'bg-amber-500/10',
            text: 'text-amber-400',
            icon: '⏳',
            pulse: false
        },
        processing: {
            gradient: 'from-blue-600 to-cyan-600',
            glow: 'shadow-blue-500/40',
            border: 'border-blue-500/30',
            bg: 'bg-blue-500/10',
            text: 'text-blue-400',
            icon: '⚡',
            pulse: true
        },
        completed: {
            gradient: 'from-emerald-600 to-green-600',
            glow: 'shadow-emerald-500/40',
            border: 'border-emerald-500/30',
            bg: 'bg-emerald-500/10',
            text: 'text-emerald-400',
            icon: '✓',
            pulse: false
        },
        rejected: {
            gradient: 'from-red-600 to-rose-700',
            glow: 'shadow-red-500/40',
            border: 'border-red-500/30',
            bg: 'bg-red-500/10',
            text: 'text-red-400',
            icon: '✕',
            pulse: false
        }
    };
    return configs[status.toLowerCase()] || configs.pending;
};

const getMobileStatusColor = (status) => {
    const colors = {
        pending: 'bg-amber-100 text-amber-800 border-amber-200',
        processing: 'bg-blue-100 text-blue-800 border-blue-200',
        completed: 'bg-emerald-100 text-emerald-800 border-emerald-200',
        rejected: 'bg-red-100 text-red-800 border-red-200'
    };
    return colors[status.toLowerCase()] || 'bg-gray-100 text-gray-800 border-gray-200';
};

const getDeptConfig = (dept) => {
    const configs = {
        'MCR': { icon: '💍', name: 'MCR', gradient: 'from-pink-500 to-rose-600' },
        'ENG': { icon: '🏗️', name: 'Engineering', gradient: 'from-orange-500 to-amber-600' },
        'HEALTH': { icon: '🩺', name: 'Health', gradient: 'from-emerald-500 to-teal-600' },
        'BUS': { icon: '💼', name: 'Business', gradient: 'from-blue-500 to-indigo-600' },
        'ASSESS': { icon: '🏡', name: 'Assessment', gradient: 'from-purple-500 to-violet-600' },
        'DSWD': { icon: '🤝', name: 'DSWD', gradient: 'from-cyan-500 to-blue-600' }
    };
    return configs[dept] || { icon: '🏛️', name: dept, gradient: 'from-slate-500 to-gray-600' };
};

const getProgressSteps = (status) => {
    const statusLower = status.toLowerCase();
    return [
        { active: true, color: 'bg-blue-500' },
        { active: ['processing', 'completed'].includes(statusLower), color: statusLower === 'completed' ? 'bg-blue-500' : 'bg-blue-500' },
        { active: statusLower === 'completed', color: 'bg-emerald-500' }
    ];
};
</script>

<template>
    <Head title="My Applications" />

    <AuthenticatedLayout>
        
        <!-- ENHANCED MOBILE LAYOUT - < 1024px -->
        <div class="lg:hidden w-full max-w-md mx-auto px-4 py-6 pb-28">
            
            <!-- Mobile Header with Glass Effect -->
            <div class="mb-6 animate-fade-in-down">
                <Link :href="route('dashboard')" 
                      class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-white/80 backdrop-blur-md text-gray-700 text-sm mb-4 hover:bg-white active:scale-95 transition-all shadow-sm border border-gray-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    <span class="font-semibold">Back</span>
                </Link>
                
                <div class="mb-3">
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-gradient-to-r from-blue-500/10 to-purple-500/10 border border-blue-500/20 backdrop-blur-sm mb-2">
                        <span class="w-1.5 h-1.5 bg-blue-500 rounded-full animate-pulse"></span>
                        <span class="text-xs font-bold uppercase tracking-wider text-blue-600">Service Portal</span>
                    </div>
                </div>
                
                <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 bg-clip-text text-transparent mb-2">
                    My Applications
                </h1>
                <p class="text-gray-600 text-sm">Track your service requests in real-time</p>
            </div>

            <!-- New Application Button -->
            <Link :href="route('services.landing')" 
                  class="mobile-action-btn group mb-6">
                <div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl opacity-0 group-active:opacity-100 transition-opacity blur-xl"></div>
                <div class="relative flex items-center justify-center gap-2 py-3.5 bg-gradient-to-r from-blue-500 to-indigo-500 text-white rounded-2xl font-semibold shadow-lg shadow-blue-500/30">
                    <svg class="w-5 h-5 group-active:rotate-90 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>New Application</span>
                </div>
            </Link>

            <!-- Enhanced Empty State -->
            <div v-if="requests.length === 0" class="mobile-empty-state">
                <div class="relative">
                    <div class="w-24 h-24 bg-gradient-to-br from-blue-100 to-purple-100 rounded-3xl flex items-center justify-center text-5xl mx-auto mb-5 shadow-lg">
                        <div class="animate-bounce-slow">📂</div>
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-2">No Applications Yet</h3>
                <p class="text-gray-600 text-sm mb-8 max-w-xs mx-auto leading-relaxed">
                    You haven't submitted any service requests. Start by browsing available services.
                </p>
                <Link :href="route('services.landing')" 
                      class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-500 to-indigo-500 text-white rounded-2xl font-semibold active:scale-95 transition-transform shadow-lg shadow-blue-500/30">
                    <span>Browse Services</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </Link>
            </div>

            <!-- Enhanced Applications List -->
            <div v-else class="space-y-4">
                <div v-for="(req, index) in requests" :key="req.id"
                     class="mobile-card group"
                     :style="`animation-delay: ${index * 0.05}s`">
                    
                    <!-- Card Glow Effect -->
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-500/5 to-purple-500/5 rounded-2xl opacity-0 group-active:opacity-100 transition-opacity"></div>
                    
                    <!-- Status Badge & Date -->
                    <div class="relative flex items-center justify-between mb-3">
                        <span :class="['mobile-status-badge', getMobileStatusColor(req.status)]">
                            {{ getStatusConfig(req.status).icon }} {{ req.status }}
                        </span>
                        <span class="text-xs text-gray-500 font-medium">{{ formatDate(req.created_at) }}</span>
                    </div>

                    <!-- Tracking & Category -->
                    <div class="relative flex items-center gap-2 mb-3">
                        <span class="mobile-tag bg-blue-50 text-blue-700 border-blue-200">
                            #{{ req.tracking_code }}
                        </span>
                        <span :class="`mobile-tag bg-gradient-to-r ${getDeptConfig(req.category).gradient} text-white border-transparent`">
                            {{ getDeptConfig(req.category).icon }} {{ req.category }}
                        </span>
                    </div>

                    <!-- Document Info -->
                    <div class="relative">
                        <h3 class="text-base font-bold text-gray-900 mb-1.5 group-active:text-blue-600 transition-colors">
                            {{ req.document_type }}
                        </h3>
                        <p class="text-sm text-gray-600 mb-4 line-clamp-2 leading-relaxed">
                            {{ req.purpose }}
                        </p>
                    </div>

                    <!-- Footer with Progress -->
                    <div class="relative flex items-center justify-between pt-3 border-t border-gray-100">
                        <div class="flex items-center gap-3">
                            <!-- Progress Dots -->
                            <div class="flex gap-1.5">
                                <div v-for="(step, i) in getProgressSteps(req.status)" :key="i"
                                     :class="['w-2 h-2 rounded-full transition-all', step.active ? step.color : 'bg-gray-300']">
                                </div>
                            </div>
                            <span class="text-[10px] text-gray-500 font-bold uppercase tracking-wider">
                                {{ req.status === 'completed' ? 'Ready' : 'Tracking' }}
                            </span>
                        </div>

                        <!-- Action Button -->
                        <Link v-if="req.status === 'completed'" 
                              :href="route('documents.index')"
                              class="mobile-view-btn">
                            <span>View</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <!-- ENHANCED DESKTOP LAYOUT - ≥ 1024px -->
        <div class="hidden lg:block min-h-screen">
            <!-- Premium Background -->
            <div class="fixed inset-0 z-0 pointer-events-none overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950"></div>
                <div class="absolute inset-0 opacity-[0.03] bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxwYXRoIGQ9Ik0zNiAxOGMzLjMxNCAwIDYgMi42ODYgNiA2cy0yLjY4NiA2LTYgNi02LTIuNjg2LTYtNiAyLjY4Ni02IDYtNiIgc3Ryb2tlPSIjZmZmIi8+PC9nPjwvc3ZnPg==')]"></div>
                <div class="absolute inset-0 bg-gradient-to-tr from-blue-950/30 via-transparent to-purple-950/30"></div>
                
                <!-- Animated glows -->
                <div 
                    class="absolute top-[20%] right-[10%] w-[600px] h-[600px] bg-blue-500/10 rounded-full blur-[150px] animate-pulse-slow"
                    :style="{ transform: `translate(${mouseX}px, ${mouseY}px)` }"
                ></div>
                <div 
                    class="absolute bottom-[10%] left-[15%] w-[500px] h-[500px] bg-purple-500/10 rounded-full blur-[150px] animate-pulse-slow"
                    style="animation-delay: 2s"
                    :style="{ transform: `translate(${-mouseX}px, ${-mouseY}px)` }"
                ></div>
                
                <!-- Grid overlay -->
                <div class="absolute inset-0 bg-[linear-gradient(to_right,rgba(255,255,255,0.02)_1px,transparent_1px),linear-gradient(to_bottom,rgba(255,255,255,0.02)_1px,transparent_1px)] bg-[size:4rem_4rem] [mask-image:radial-gradient(ellipse_80%_50%_at_50%_0%,#000_70%,transparent_110%)]"></div>
            </div>

            <div class="relative z-10 py-12 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <!-- Enhanced Header -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-12 gap-6 animate-fade-in-up">
                    <div>
                        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-blue-500/10 border border-blue-500/20 backdrop-blur-md mb-4 shadow-lg shadow-blue-500/10">
                            <span class="w-2 h-2 bg-blue-400 rounded-full animate-pulse shadow-lg shadow-blue-400/50"></span>
                            <span class="text-xs font-bold uppercase tracking-[0.2em] text-blue-300">Service Portal</span>
                        </div>
                        <h2 class="text-5xl font-black bg-gradient-to-r from-white via-blue-100 to-purple-100 bg-clip-text text-transparent tracking-tight mb-2">
                            Application History
                        </h2>
                        <p class="text-slate-400 mt-2 text-sm max-w-lg leading-relaxed">
                            Track the real-time status of your permits, clearances, and service requests with live updates.
                        </p>
                    </div>
                    
                    <Link :href="route('services.landing')" 
                          class="desktop-action-btn group">
                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-blue-400/20 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                        <span class="relative flex items-center gap-2">
                            <svg class="w-4 h-4 group-hover:rotate-90 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span>Browse Services</span>
                        </span>
                    </Link>
                </div>

                <!-- Enhanced Empty State -->
                <div v-if="requests.length === 0" class="desktop-empty-state">
                    <div class="relative mb-8">
                        <div class="absolute inset-0 bg-gradient-to-r from-blue-500/20 to-purple-500/20 rounded-full blur-3xl"></div>
                        <div class="relative w-32 h-32 bg-gradient-to-br from-white/10 to-white/5 rounded-3xl flex items-center justify-center text-6xl mx-auto border border-white/10 shadow-2xl">
                            <div class="animate-float">📂</div>
                        </div>
                    </div>
                    <h3 class="text-3xl font-bold text-white mb-3">No History Found</h3>
                    <p class="text-slate-400 max-w-md mx-auto mb-10 text-sm leading-relaxed">
                        You haven't submitted any service requests yet. Browse the catalog to apply for permits or certificates.
                    </p>
                    <Link :href="route('services.landing')" 
                          class="desktop-cta-btn group">
                        <div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-purple-600 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity blur-xl"></div>
                        <span class="relative flex items-center gap-2">
                            <span>Browse Catalog</span>
                            <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                        </span>
                    </Link>
                </div>

                <!-- Enhanced Applications Grid -->
                <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="(req, index) in requests" :key="req.id" 
                         class="desktop-card group"
                         :style="`animation-delay: ${index * 0.05}s`">
                        
                        <!-- Card glow on hover -->
                        <div :class="`absolute inset-0 bg-gradient-to-br ${getStatusConfig(req.status).gradient} opacity-0 group-hover:opacity-5 transition-opacity rounded-3xl pointer-events-none blur-xl`"></div>
                        
                        <!-- Card content -->
                        <div class="relative z-10">
                            <!-- Header -->
                            <div class="flex justify-between items-start mb-6">
                                <div>
                                    <span class="desktop-label">Filed On</span>
                                    <span class="desktop-date">{{ formatDate(req.created_at) }}</span>
                                </div>
                                <span :class="`desktop-status-badge ${getStatusConfig(req.status).bg} ${getStatusConfig(req.status).text} ${getStatusConfig(req.status).border}`">
                                    <span v-if="getStatusConfig(req.status).pulse" class="w-1.5 h-1.5 rounded-full bg-current animate-pulse"></span>
                                    {{ req.status }}
                                </span>
                            </div>

                            <!-- Document info -->
                            <div class="mb-6">
                                <div class="flex items-center gap-2 mb-3">
                                    <div class="desktop-tag">
                                        #{{ req.tracking_code }}
                                    </div>
                                    <div :class="`desktop-dept-tag bg-gradient-to-r ${getDeptConfig(req.category).gradient}`">
                                        {{ getDeptConfig(req.category).icon }} {{ req.category }}
                                    </div>
                                </div>
                                
                                <h3 class="desktop-card-title">
                                    {{ req.document_type }}
                                </h3>
                                <p class="desktop-card-desc">
                                    {{ req.purpose }}
                                </p>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="relative z-10 pt-4 border-t border-white/5 flex justify-between items-center">
                            <div class="flex items-center gap-3">
                                <!-- Progress dots -->
                                <div class="flex gap-1.5">
                                    <div v-for="(step, i) in getProgressSteps(req.status)" :key="i"
                                         :class="['w-2 h-2 rounded-full transition-all', step.active ? step.color : 'bg-slate-700']">
                                    </div>
                                </div>
                                <span class="desktop-progress-label">
                                    {{ req.status === 'completed' ? 'Ready' : 'Tracking' }}
                                </span>
                            </div>

                            <!-- Action button -->
                            <Link v-if="req.status === 'completed'" 
                                  :href="route('documents.index')"
                                  class="desktop-view-btn group/btn">
                                <span>View Result</span>
                                <svg class="w-3.5 h-3.5 group-hover/btn:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </Link>
                            
                            <span v-else class="desktop-processing">
                                <span class="animate-pulse">●</span> Processing
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </AuthenticatedLayout>
</template>

<style scoped>
/* Mobile Styles */
.mobile-action-btn {
    @apply relative w-full block active:scale-[0.98] transition-transform;
}

.mobile-empty-state {
    @apply text-center py-16 animate-fade-in-up;
}

.mobile-card {
    @apply relative bg-white rounded-2xl p-4 shadow-lg border border-gray-200 
           active:scale-[0.99] transition-all animate-slide-up;
    backdrop-filter: blur(20px);
}

.mobile-status-badge {
    @apply px-2.5 py-1.5 rounded-full text-[10px] font-bold uppercase border 
           inline-flex items-center gap-1.5 shadow-sm;
}

.mobile-tag {
    @apply px-2.5 py-1 rounded-lg text-[10px] font-bold border shadow-sm;
}

.mobile-view-btn {
    @apply px-3.5 py-2 bg-gradient-to-r from-emerald-500 to-green-500 text-white 
           rounded-xl text-xs font-bold flex items-center gap-1.5 
           active:scale-95 transition-all shadow-lg shadow-emerald-500/30;
}

/* Desktop Styles */
.desktop-action-btn {
    @apply relative px-8 py-3 bg-white text-black rounded-2xl text-xs font-bold 
           uppercase tracking-widest transition-all hover:scale-105 overflow-hidden 
           shadow-[0_0_30px_rgba(255,255,255,0.3)] hover:shadow-[0_0_40px_rgba(255,255,255,0.4)];
}

.desktop-empty-state {
    @apply flex flex-col items-center justify-center py-32 
           bg-slate-900/40 backdrop-blur-2xl border border-white/10 
           rounded-[2.5rem] text-center animate-fade-in-up;
}

.desktop-cta-btn {
    @apply relative inline-flex items-center gap-2 px-10 py-4 
           bg-gradient-to-r from-blue-600 to-purple-600 text-white font-bold 
           rounded-2xl transition-all hover:scale-105 
           shadow-2xl shadow-blue-600/30 hover:shadow-blue-600/40;
}

.desktop-card {
    @apply relative bg-slate-900/60 backdrop-blur-2xl border border-white/10 
           rounded-3xl p-6 hover:bg-white/5 hover:border-white/20 
           transition-all duration-500 hover:-translate-y-2 hover:shadow-2xl 
           flex flex-col justify-between min-h-[280px] animate-slide-up;
}

.desktop-label {
    @apply block text-[10px] text-slate-500 font-bold uppercase tracking-wider mb-1;
}

.desktop-date {
    @apply block text-sm font-mono text-slate-300;
}

.desktop-status-badge {
    @apply px-3 py-1.5 rounded-full text-[10px] font-bold uppercase 
           tracking-widest border flex items-center gap-1.5 shadow-lg;
}

.desktop-tag {
    @apply inline-block px-2.5 py-1 rounded-lg bg-white/5 border border-white/10 
           text-[10px] font-mono text-blue-400 shadow-sm;
}

.desktop-dept-tag {
    @apply inline-block px-2.5 py-1 rounded-lg text-[10px] font-bold 
           text-white shadow-lg border border-white/20;
}

.desktop-card-title {
    @apply text-xl font-bold text-white leading-tight 
           group-hover:text-blue-200 transition-colors line-clamp-2 mb-2;
}

.desktop-card-desc {
    @apply text-slate-400 text-xs line-clamp-2 leading-relaxed;
}

.desktop-progress-label {
    @apply text-[10px] text-slate-500 uppercase font-bold tracking-wider;
}

.desktop-view-btn {
    @apply flex items-center gap-2 text-emerald-400 text-xs font-bold 
           uppercase tracking-wider hover:text-emerald-300 transition-all 
           bg-emerald-500/10 px-3 py-2 rounded-xl border border-emerald-500/20 
           hover:bg-emerald-500/15 hover:border-emerald-500/30 
           shadow-lg shadow-emerald-500/10;
}

.desktop-processing {
    @apply text-[10px] text-slate-600 font-mono uppercase tracking-widest 
           flex items-center gap-1.5;
}

/* Line clamp utility */
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Animations */
@keyframes fadeInDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
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

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes bounceSlow {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-15px); }
}

@keyframes float {
    0%, 100% { transform: translateY(0) rotate(0deg); }
    50% { transform: translateY(-10px) rotate(5deg); }
}

@keyframes pulseSlow {
    0%, 100% { opacity: 0.8; }
    50% { opacity: 1; }
}

.animate-fade-in-down {
    animation: fadeInDown 0.6s ease-out backwards;
}

.animate-fade-in-up {
    animation: fadeInUp 0.6s ease-out backwards;
}

.animate-slide-up {
    animation: slideUp 0.6s ease-out backwards;
}

.animate-bounce-slow {
    animation: bounceSlow 3s ease-in-out infinite;
}

.animate-float {
    animation: float 4s ease-in-out infinite;
}

.animate-pulse-slow {
    animation: pulseSlow 4s ease-in-out infinite;
}

/* Responsive optimizations */
@media (max-width: 640px) {
    .mobile-card {
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