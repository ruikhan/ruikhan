<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    requests: Array
});

const formatDate = (dateString) => {
    const options = { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' };
    return new Date(dateString).toLocaleDateString('en-US', options);
};

const getStatusColor = (status) => {
    switch(status.toLowerCase()) {
        case 'pending': return 'bg-yellow-500/10 text-yellow-400 border-yellow-500/20';
        case 'processing': return 'bg-blue-500/10 text-blue-400 border-blue-500/20 animate-pulse';
        case 'completed': return 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20 shadow-[0_0_15px_rgba(16,185,129,0.2)]';
        case 'rejected': return 'bg-red-500/10 text-red-400 border-red-500/20';
        default: return 'bg-slate-500/10 text-slate-400 border-slate-500/20';
    }
};

const getMobileStatusColor = (status) => {
    switch(status.toLowerCase()) {
        case 'pending': return 'bg-yellow-100 text-yellow-800 border-yellow-200';
        case 'processing': return 'bg-blue-100 text-blue-800 border-blue-200';
        case 'completed': return 'bg-green-100 text-green-800 border-green-200';
        case 'rejected': return 'bg-red-100 text-red-800 border-red-200';
        default: return 'bg-gray-100 text-gray-800 border-gray-200';
    }
};

const getDeptIcon = (dept) => {
    const icons = {
        'MCR': '💍',
        'ENG': '🏗️',
        'HEALTH': '🩺',
        'BUS': '💼',
        'ASSESS': '🏡',
        'DSWD': '🤝'
    };
    return icons[dept] || '🏛️';
};
</script>

<template>
    <Head title="My Applications" />

    <AuthenticatedLayout>
        
        <!-- MOBILE LAYOUT (iOS Style) - < 1024px -->
        <div class="lg:hidden w-full max-w-md mx-auto px-4 py-6 pb-24">
            
            <!-- Header -->
            <div class="mb-6">
                <Link :href="route('dashboard')" class="inline-flex items-center gap-2 text-gray-600 text-sm mb-4 hover:text-gray-900">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back
                </Link>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">My Applications</h1>
                <p class="text-gray-600 text-sm">Track your service requests</p>
            </div>

            <!-- New Application Button -->
            <Link :href="route('services.landing')" 
                  class="w-full flex items-center justify-center gap-2 py-3 bg-gradient-to-r from-blue-500 to-indigo-500 text-white rounded-2xl font-semibold mb-6 active:scale-95 transition-transform shadow-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>New Application</span>
            </Link>

            <!-- Empty State -->
            <div v-if="requests.length === 0" class="text-center py-16">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center text-4xl mx-auto mb-4">
                    📂
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">No Applications Yet</h3>
                <p class="text-gray-600 text-sm mb-6 max-w-xs mx-auto">
                    You haven't submitted any service requests. Start by browsing available services.
                </p>
                <Link :href="route('services.landing')" 
                      class="inline-block px-6 py-3 bg-blue-500 text-white rounded-full font-semibold active:scale-95 transition-transform">
                    Browse Services
                </Link>
            </div>

            <!-- Applications List -->
            <div v-else class="space-y-4">
                <div v-for="req in requests" :key="req.id"
                     class="bg-white rounded-2xl p-4 shadow-sm border border-gray-200 active:scale-[0.98] transition-transform">
                    
                    <!-- Status & Date -->
                    <div class="flex items-center justify-between mb-3">
                        <span :class="['px-2.5 py-1 rounded-full text-[10px] font-bold uppercase border', getMobileStatusColor(req.status)]">
                            {{ req.status }}
                        </span>
                        <span class="text-xs text-gray-500">{{ formatDate(req.created_at) }}</span>
                    </div>

                    <!-- Tracking Code & Category -->
                    <div class="flex items-center gap-2 mb-2">
                        <span class="px-2 py-0.5 bg-blue-50 text-blue-700 rounded text-[10px] font-mono font-semibold">
                            #{{ req.tracking_code }}
                        </span>
                        <span class="px-2 py-0.5 bg-purple-50 text-purple-700 rounded text-[10px] font-semibold">
                            {{ getDeptIcon(req.category) }} {{ req.category }}
                        </span>
                    </div>

                    <!-- Title -->
                    <h3 class="text-base font-bold text-gray-900 mb-1">
                        {{ req.document_type }}
                    </h3>
                    <p class="text-sm text-gray-600 mb-4 line-clamp-2">
                        {{ req.purpose }}
                    </p>

                    <!-- Progress Dots -->
                    <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                        <div class="flex items-center gap-2">
                            <div class="flex gap-1">
                                <div class="w-2 h-2 rounded-full bg-blue-500"></div>
                                <div :class="['w-2 h-2 rounded-full', ['processing','completed'].includes(req.status.toLowerCase()) ? 'bg-blue-500' : 'bg-gray-300']"></div>
                                <div :class="['w-2 h-2 rounded-full', req.status.toLowerCase() === 'completed' ? 'bg-green-500' : 'bg-gray-300']"></div>
                            </div>
                            <span class="text-[10px] text-gray-500 font-semibold uppercase">
                                {{ req.status === 'completed' ? 'Ready' : 'Tracking' }}
                            </span>
                        </div>

                        <!-- View Button -->
                        <Link v-if="req.status === 'completed'" 
                              :href="route('documents.index')"
                              class="px-3 py-1.5 bg-green-50 text-green-700 rounded-lg text-xs font-semibold flex items-center gap-1 active:scale-95 transition-transform">
                            <span>View</span>
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <!-- DESKTOP LAYOUT (Original Dark) - ≥ 1024px -->
        <div class="hidden lg:block">
            <div class="fixed inset-0 z-0 pointer-events-none">
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_var(--tw-gradient-stops))] from-[#0f172a] via-[#050505] to-black"></div>
                <div class="absolute inset-0 opacity-[0.03] bg-[url('https://grainy-gradients.vercel.app/noise.svg')] brightness-100 contrast-150"></div>
                <div class="absolute top-[20%] right-0 w-[50vw] h-[50vw] bg-indigo-600/5 rounded-full blur-[120px]"></div>
            </div>

            <div class="relative z-10 py-12 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 min-h-screen">
                
                <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-12 gap-6">
                    <div>
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/10 border border-blue-500/20 backdrop-blur-md mb-4">
                            <span class="w-1.5 h-1.5 bg-blue-400 rounded-full animate-pulse"></span>
                            <span class="text-xs font-bold uppercase tracking-[0.2em] text-blue-300">Service Portal</span>
                        </div>
                        <h2 class="text-4xl font-black text-white tracking-tight">Application History</h2>
                        <p class="text-slate-400 mt-2 text-sm max-w-md">Track the real-time status of your permits, clearances, and service requests.</p>
                    </div>
                    
                    <div class="flex flex-wrap gap-3">
                        <Link :href="route('services.landing')" 
                              class="group relative px-6 py-2.5 bg-white text-black rounded-full text-xs font-bold uppercase tracking-widest transition-all hover:scale-105 overflow-hidden shadow-[0_0_20px_rgba(255,255,255,0.3)]">
                            <div class="absolute inset-0 bg-gradient-to-r from-transparent via-blue-100 to-transparent -translate-x-[100%] group-hover:animate-shine"></div>
                            <span class="relative z-10 flex items-center gap-2">
                                <span>+ Browse Services</span>
                            </span>
                        </Link>
                    </div>
                </div>

                <div v-if="requests.length === 0" class="flex flex-col items-center justify-center py-32 bg-[#0f1115]/40 backdrop-blur-xl border border-white/5 rounded-[2.5rem] text-center animate-fade-in-up">
                    <div class="w-24 h-24 bg-white/5 rounded-full flex items-center justify-center text-5xl mb-6 shadow-inner border border-white/5">
                        📂
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-2">No History Found</h3>
                    <p class="text-slate-400 max-w-sm mx-auto mb-8 text-sm leading-relaxed">
                        You haven't submitted any service requests yet. Browse the catalog to apply for permits or certificates.
                    </p>
                    <Link :href="route('services.landing')" class="px-8 py-3 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-full transition-all shadow-lg shadow-blue-600/20 hover:-translate-y-1">
                        Browse Catalog
                    </Link>
                </div>

                <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 animate-fade-in-up">
                    
                    <div v-for="req in requests" :key="req.id" 
                         class="group relative bg-[#0f1115]/60 backdrop-blur-xl border border-white/10 rounded-3xl p-6 hover:bg-white/5 hover:border-white/20 transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl flex flex-col justify-between min-h-[240px]">
                        
                        <div class="absolute inset-0 bg-gradient-to-br from-blue-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity rounded-3xl pointer-events-none"></div>

                        <div>
                            <div class="flex justify-between items-start mb-6">
                                <div class="flex flex-col">
                                    <span class="text-[10px] text-slate-500 font-bold uppercase tracking-wider mb-1">Filed On</span>
                                    <span class="text-sm font-mono text-slate-300">{{ formatDate(req.created_at) }}</span>
                                </div>
                                <span :class="['px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest border', getStatusColor(req.status)]">
                                    {{ req.status }}
                                </span>
                            </div>

                            <div class="mb-6 relative z-10">
                                <div class="flex items-center gap-2 mb-2">
                                    <div class="inline-block px-2 py-0.5 rounded bg-white/5 border border-white/5 text-[10px] font-mono text-blue-400">
                                        #{{ req.tracking_code }}
                                    </div>
                                    <div class="inline-block px-2 py-0.5 rounded bg-purple-500/10 border border-purple-500/20 text-[10px] font-bold text-purple-300 uppercase">
                                        {{ getDeptIcon(req.category) }} {{ req.category }}
                                    </div>
                                </div>
                                
                                <h3 class="text-xl font-bold text-white leading-tight group-hover:text-blue-200 transition-colors line-clamp-2">
                                    {{ req.document_type }}
                                </h3>
                                <p class="text-slate-400 text-xs mt-2 line-clamp-2 leading-relaxed">
                                    {{ req.purpose }}
                                </p>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-white/5 flex justify-between items-center relative z-10">
                            <div class="flex items-center gap-2">
                                <div class="flex gap-1">
                                    <div class="w-1.5 h-1.5 rounded-full bg-blue-500"></div>
                                    <div :class="['w-1.5 h-1.5 rounded-full', ['processing','completed'].includes(req.status.toLowerCase()) ? 'bg-blue-500' : 'bg-slate-700']"></div>
                                    <div :class="['w-1.5 h-1.5 rounded-full', req.status.toLowerCase() === 'completed' ? 'bg-emerald-500' : 'bg-slate-700']"></div>
                                </div>
                                <span class="text-[10px] text-slate-500 uppercase font-bold tracking-wider ml-1">
                                    {{ req.status === 'completed' ? 'Ready' : 'Tracking' }}
                                </span>
                            </div>

                            <Link v-if="req.status === 'completed'" 
                                  :href="route('documents.index')"
                                  class="flex items-center gap-2 text-emerald-400 text-xs font-bold uppercase tracking-wider hover:text-emerald-300 transition-colors bg-emerald-500/10 px-3 py-1.5 rounded-lg border border-emerald-500/20">
                                <span>View Result</span>
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </Link>
                            
                            <span v-else class="text-[10px] text-slate-600 font-mono uppercase tracking-widest flex items-center gap-1">
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
@keyframes shine { 100% { transform: translateX(100%); } }
.animate-shine { animation: shine 1.5s infinite; }

@keyframes fade-in-up {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in-up { animation: fade-in-up 0.6s ease-out forwards; }

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>