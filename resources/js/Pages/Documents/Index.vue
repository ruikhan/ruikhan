<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

// Receive the data from the Controller
const props = defineProps({
    requests: Array
});

// --- HELPER: FORMAT DATE ---
const formatDate = (dateString) => {
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('en-US', options);
};

// --- HELPER: STATUS COLORS (Enhanced) ---
const getStatusColor = (status) => {
    switch(status.toLowerCase()) {
        case 'pending': return 'bg-yellow-500/10 text-yellow-400 border-yellow-500/20';
        case 'processing': return 'bg-blue-500/10 text-blue-400 border-blue-500/20 animate-pulse';
        case 'completed': return 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20 shadow-[0_0_15px_rgba(16,185,129,0.2)]';
        case 'rejected': return 'bg-red-500/10 text-red-400 border-red-500/20';
        default: return 'bg-slate-500/10 text-slate-400 border-slate-500/20';
    }
};
</script>

<template>
    <Head title="My Requests" />

    <AuthenticatedLayout>
        <div class="fixed inset-0 z-0 pointer-events-none">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_var(--tw-gradient-stops))] from-[#0f172a] via-[#050505] to-black"></div>
            <div class="absolute inset-0 opacity-[0.03] bg-[url('https://grainy-gradients.vercel.app/noise.svg')] brightness-100 contrast-150"></div>
            <div class="absolute top-[20%] right-0 w-[50vw] h-[50vw] bg-blue-600/5 rounded-full blur-[120px]"></div>
        </div>

        <div class="relative z-10 py-12 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 min-h-screen">
            
            <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-12 gap-6">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/10 border border-blue-500/20 backdrop-blur-md mb-4">
                        <span class="w-1.5 h-1.5 bg-blue-400 rounded-full animate-pulse"></span>
                        <span class="text-xs font-bold uppercase tracking-[0.2em] text-blue-300">Document Center</span>
                    </div>
                    <h2 class="text-4xl font-black text-white tracking-tight">Request History</h2>
                    <p class="text-slate-400 mt-2 text-sm max-w-md">Track the real-time status of your clearances, permits, and certifications.</p>
                </div>
                
                <div class="flex flex-wrap gap-3">
                    <!-- <Link :href="route('dashboard')" 
                          class="px-5 py-2.5 rounded-full border border-white/10 text-slate-300 hover:text-white hover:bg-white/5 text-xs font-bold uppercase tracking-widest transition-all">
                        Dashboard
                    </Link> -->
                    <Link :href="route('documents.create')" 
                          class="group relative px-6 py-2.5 bg-white text-black rounded-full text-xs font-bold uppercase tracking-widest transition-all hover:scale-105 overflow-hidden shadow-[0_0_20px_rgba(255,255,255,0.3)]">
                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-blue-100 to-transparent -translate-x-[100%] group-hover:animate-shine"></div>
                        <span class="relative z-10 flex items-center gap-2">
                            <span>+ New Request</span>
                        </span>
                    </Link>
                </div>
            </div>

            <div v-if="requests.length === 0" class="flex flex-col items-center justify-center py-32 bg-[#0f1115]/40 backdrop-blur-xl border border-white/5 rounded-[2.5rem] text-center animate-fade-in-up">
                <div class="w-24 h-24 bg-white/5 rounded-full flex items-center justify-center text-5xl mb-6 shadow-inner border border-white/5">
                    📂
                </div>
                <h3 class="text-2xl font-bold text-white mb-2">No Requests Found</h3>
                <p class="text-slate-400 max-w-sm mx-auto mb-8 text-sm leading-relaxed">
                    You haven't submitted any document requests yet. Apply for a clearance or permit to get started with your digital record.
                </p>
                <Link :href="route('documents.create')" class="px-8 py-3 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-full transition-all shadow-lg shadow-blue-600/20 hover:-translate-y-1">
                    Create First Request
                </Link>
            </div>

            <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 animate-fade-in-up">
                
                <div v-for="req in requests" :key="req.id" 
                     class="group relative bg-[#0f1115]/60 backdrop-blur-xl border border-white/10 rounded-3xl p-6 hover:bg-white/5 hover:border-white/20 transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl flex flex-col justify-between min-h-[220px]">
                    
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
                            <div class="inline-block px-2 py-0.5 rounded bg-white/5 border border-white/5 text-[10px] font-mono text-blue-400 mb-2">
                                #{{ req.tracking_code }}
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
                            <div class="w-2 h-2 rounded-full bg-slate-600"></div>
                            <span class="text-[10px] text-slate-400 uppercase font-bold tracking-wider">{{ req.category }}</span>
                        </div>

                        <Link v-if="req.status === 'completed'" 
                              :href="route('documents.verify', req.id)"
                              class="flex items-center gap-2 text-emerald-400 text-xs font-bold uppercase tracking-wider hover:text-emerald-300 transition-colors bg-emerald-500/10 px-3 py-1.5 rounded-lg border border-emerald-500/20">
                            <span>Certificate</span>
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </Link>
                        
                        <span v-else class="text-[10px] text-slate-600 font-mono uppercase tracking-widest">
                            In Progress...
                        </span>
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
</style>