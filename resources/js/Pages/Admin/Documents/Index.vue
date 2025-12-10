<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({ requests: Array });

const form = useForm({
    status: '',
    admin_note: ''
});

const updateStatus = (id, newStatus) => {
    if (!confirm(`Confirm Action: Mark request as ${newStatus.toUpperCase()}?`)) return;
    
    form.status = newStatus;
    form.patch(route('admin.documents.update', id), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <Head title="Document Control" />

    <AuthenticatedLayout>
        <div class="min-h-screen bg-slate-900 relative overflow-hidden font-sans">
            
            <div class="absolute inset-0 pointer-events-none">
                <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(#64748b 1px, transparent 1px); background-size: 32px 32px;"></div>
                <div class="absolute top-0 left-1/4 w-[500px] h-[500px] bg-blue-600/20 rounded-full blur-[100px] animate-pulse-slow"></div>
                <div class="absolute bottom-0 right-1/4 w-[500px] h-[500px] bg-cyan-600/10 rounded-full blur-[100px] animate-pulse-slow" style="animation-delay: 2s;"></div>
            </div>

            <div class="relative z-10 pt-10 pb-6 px-6 lg:px-8 border-b border-white/5 bg-slate-900/80 backdrop-blur-md">
                <div class="max-w-7xl mx-auto flex justify-between items-end animate-fade-in">
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
                            <span class="text-[10px] font-bold uppercase tracking-widest text-red-400">Restricted Access</span>
                        </div>
                        <h1 class="text-3xl md:text-4xl font-bold text-white tracking-tight">Document Control</h1>
                        <p class="text-slate-400 text-sm mt-1">Review and process resident applications.</p>
                    </div>
                    
                    <div class="hidden md:flex gap-4">
                        <div class="px-4 py-2 rounded-lg bg-white/5 border border-white/10 backdrop-blur-sm">
                            <span class="block text-2xl font-bold text-yellow-400 leading-none">{{ requests.filter(r => r.status === 'pending').length }}</span>
                            <span class="text-[10px] text-slate-400 uppercase tracking-wider">Pending</span>
                        </div>
                        <div class="px-4 py-2 rounded-lg bg-white/5 border border-white/10 backdrop-blur-sm">
                            <span class="block text-2xl font-bold text-green-400 leading-none">{{ requests.filter(r => r.status === 'ready_for_pickup').length }}</span>
                            <span class="text-[10px] text-slate-400 uppercase tracking-wider">Ready</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="py-10 relative z-10">
                <div class="max-w-7xl mx-auto px-6 lg:px-8">
                    
                    <div class="bg-slate-800/40 backdrop-blur-xl border border-white/10 rounded-2xl overflow-hidden shadow-2xl animate-slide-up">
                        
                        <div class="p-4 border-b border-white/5 bg-white/5 flex justify-between items-center">
                            <div class="flex items-center gap-2 text-sm text-slate-300">
                                <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path></svg>
                                <span>Filter View:</span>
                                <span class="text-white font-bold">All Requests</span>
                            </div>
                            <div class="text-[10px] font-mono text-emerald-500 flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                LIVE FEED ACTIVE
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-white/5">
                                <thead class="bg-black/20">
                                    <tr>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Timestamp</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Applicant</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Request Details</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-4 text-right text-xs font-bold text-slate-400 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-white/5 text-sm">
                                    <tr v-for="req in requests" :key="req.id" class="hover:bg-white/5 transition-colors group">
                                        
                                        <td class="px-6 py-4 whitespace-nowrap text-slate-300 font-mono text-xs">
                                            {{ new Date(req.created_at).toLocaleString() }}
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="h-8 w-8 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-xs mr-3 shadow-lg shadow-indigo-500/20">
                                                    {{ req.user.name.charAt(0) }}
                                                </div>
                                                <div>
                                                    <div class="font-bold text-white">{{ req.user.name }}</div>
                                                    <div class="text-xs text-slate-500">{{ req.user.email }}</div>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="px-6 py-4">
                                            <div class="text-blue-300 font-bold mb-1">{{ req.document_type }}</div>
                                            <div class="text-slate-400 text-xs italic opacity-70 truncate max-w-xs">
                                                {{ req.purpose }}
                                            </div>
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span v-if="req.status === 'pending'" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-yellow-500/10 border border-yellow-500/20 text-yellow-400 text-xs font-bold shadow-[0_0_10px_rgba(234,179,8,0.2)]">
                                                <span class="w-1.5 h-1.5 rounded-full bg-yellow-400 animate-pulse"></span> Pending
                                            </span>
                                            <span v-if="req.status === 'processing'" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-blue-500/10 border border-blue-500/20 text-blue-400 text-xs font-bold shadow-[0_0_10px_rgba(59,130,246,0.2)]">
                                                <span class="w-1.5 h-1.5 rounded-full bg-blue-400 animate-spin-slow"></span> Processing
                                            </span>
                                            <span v-if="req.status === 'ready_for_pickup'" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-green-500/10 border border-green-500/20 text-green-400 text-xs font-bold shadow-[0_0_10px_rgba(34,197,94,0.2)]">
                                                <span class="w-1.5 h-1.5 rounded-full bg-green-400"></span> Ready
                                            </span>
                                            <span v-if="req.status === 'completed'" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-slate-700/50 border border-slate-600 text-slate-400 text-xs font-bold">
                                                Claimed
                                            </span>
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <Link 
                                            :href="route('admin.documents.show', req.id)" 
                                            class="inline-flex items-center gap-2 px-3 py-1.5 bg-white/5 hover:bg-white/10 border border-white/10 rounded-lg text-xs font-bold text-blue-300 transition-all hover:text-blue-200 group/btn"
                                        >
                                            <span>View Dossier</span>
                                            <svg class="w-3 h-3 group-hover/btn:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </Link>
                                    </td>
                                    </tr>
                                </tbody>
                            </table>
                            
                            <div v-if="requests.length === 0" class="p-12 text-center text-slate-500">
                                <div class="text-4xl mb-2 opacity-30">📂</div>
                                <p>No active requests in queue.</p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.animate-pulse-slow { animation: pulse 4s ease-in-out infinite; }
.animate-spin-slow { animation: spin 3s linear infinite; }
@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
.animate-slide-up { animation: slideUp 0.8s ease-out; }
@keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
.animate-fade-in { animation: fadeIn 1s ease-out; }
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
</style>