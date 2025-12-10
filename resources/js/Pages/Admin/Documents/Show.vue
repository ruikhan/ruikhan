<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({ docRequest: Object });

const form = useForm({
    status: props.docRequest.status,
    admin_note: props.docRequest.admin_note || ''
});

const updateStatus = (newStatus) => {
    if (!confirm(`Confirm Action: Mark as ${newStatus.toUpperCase()}?`)) return;
    form.status = newStatus;
    form.patch(route('admin.documents.update', props.docRequest.id));
};

// Helper to format the long purpose string into readable lines
const formattedPurpose = props.docRequest.purpose.split('|').map(item => item.trim());
</script>

<template>
    <Head title="Request Details" />

    <AuthenticatedLayout>
        <div class="min-h-screen bg-slate-900 font-sans text-white p-6 lg:p-12 relative overflow-hidden">
            
            <div class="absolute inset-0 opacity-10 pointer-events-none" style="background-image: radial-gradient(#64748b 1px, transparent 1px); background-size: 32px 32px;"></div>

            <div class="max-w-5xl mx-auto mb-8 relative z-10">
                <Link :href="route('admin.documents.index')" class="flex items-center gap-2 text-slate-400 hover:text-white transition-colors mb-4 text-sm font-bold uppercase tracking-wider">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Back to Command Center
                </Link>
                
                <div class="flex justify-between items-end">
                    <div>
                        <h1 class="text-3xl font-bold text-white tracking-tight">Request Dossier <span class="text-slate-600">#{{ docRequest.id }}</span></h1>
                        <p class="text-blue-400 text-xs font-mono uppercase mt-1">SECURE CONNECTION // OFFICIAL USE ONLY</p>
                    </div>
                    
                    <div class="px-4 py-2 rounded-lg border border-white/10 bg-white/5 backdrop-blur-md">
                        <span class="text-[10px] text-slate-400 uppercase tracking-widest block mb-1">Current Status</span>
                        <span v-if="docRequest.status === 'pending'" class="text-yellow-400 font-bold uppercase flex items-center gap-2"><span class="w-2 h-2 bg-yellow-400 rounded-full animate-pulse"></span> Pending Review</span>
                        <span v-if="docRequest.status === 'processing'" class="text-blue-400 font-bold uppercase flex items-center gap-2"><span class="w-2 h-2 bg-blue-400 rounded-full animate-spin"></span> Processing</span>
                        <span v-if="docRequest.status === 'ready_for_pickup'" class="text-green-400 font-bold uppercase flex items-center gap-2"><span class="w-2 h-2 bg-green-400 rounded-full"></span> Ready</span>
                        <span v-if="docRequest.status === 'completed'" class="text-slate-400 font-bold uppercase">Archived / Claimed</span>
                    </div>
                </div>
            </div>

            <div class="max-w-5xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-8 relative z-10">
                
                <div class="bg-slate-800/50 backdrop-blur-xl border border-white/10 rounded-2xl p-6 h-fit">
                    <h3 class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-6 border-b border-white/5 pb-2">Applicant Identity</h3>
                    
                    <div class="flex flex-col items-center text-center mb-6">
                        <div class="w-24 h-24 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-4xl shadow-2xl shadow-indigo-500/20 mb-4">
                            {{ docRequest.user.name.charAt(0) }}
                        </div>
                        <h2 class="text-xl font-bold text-white">{{ docRequest.user.name }}</h2>
                        <p class="text-sm text-slate-400">{{ docRequest.user.email }}</p>
                    </div>

                    <div class="space-y-4">
                        <div class="p-3 bg-white/5 rounded-lg border border-white/5">
                            <label class="block text-[10px] text-slate-500 uppercase font-bold">Contact Number</label>
                            <div class="text-sm font-mono text-blue-200">{{ docRequest.user.phone_number || 'N/A' }}</div>
                        </div>
                        <div class="p-3 bg-white/5 rounded-lg border border-white/5">
                            <label class="block text-[10px] text-slate-500 uppercase font-bold">Address</label>
                            <div class="text-sm text-slate-300">{{ docRequest.user.address || 'Address not registered' }}</div>
                        </div>
                        <div class="p-3 bg-white/5 rounded-lg border border-white/5">
                            <label class="block text-[10px] text-slate-500 uppercase font-bold">Resident Status</label>
                            <div class="text-sm text-green-400 font-bold flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Verified Citizen
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-2 space-y-6">
                    
                    <div class="bg-slate-800/50 backdrop-blur-xl border border-white/10 rounded-2xl p-8 relative overflow-hidden">
                        <div class="absolute top-0 right-0 p-6 opacity-10 text-9xl pointer-events-none">📄</div>
                        
                        <h3 class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-6 border-b border-white/5 pb-2">Request Specification</h3>

                        <div class="grid grid-cols-2 gap-8 mb-8">
                            <div>
                                <label class="block text-[10px] text-slate-500 uppercase font-bold mb-1">Document Type</label>
                                <div class="text-2xl font-bold text-white">{{ docRequest.document_type }}</div>
                            </div>
                            <div>
                                <label class="block text-[10px] text-slate-500 uppercase font-bold mb-1">Filed On</label>
                                <div class="text-lg font-mono text-slate-300">{{ new Date(docRequest.created_at).toLocaleString() }}</div>
                            </div>
                        </div>

                        <div class="bg-black/20 rounded-xl p-6 border border-white/5">
                            <label class="block text-[10px] text-slate-500 uppercase font-bold mb-3">Statement of Purpose / Data</label>
                            
                            <ul class="space-y-2">
                                <li v-for="(line, index) in formattedPurpose" :key="index" class="flex items-start gap-3 text-sm text-slate-200">
                                    <span class="text-blue-500 mt-1">➤</span>
                                    <span>{{ line }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="bg-slate-800/80 backdrop-blur-xl border border-white/10 rounded-2xl p-6">
                        <h3 class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-4">Official Actions</h3>
                        
                        <div class="flex flex-wrap gap-4">
                            
                            <button 
                                @click="updateStatus('processing')" 
                                :disabled="docRequest.status !== 'pending'"
                                class="flex-1 bg-blue-600 hover:bg-blue-500 disabled:opacity-50 disabled:bg-slate-700 text-white py-4 rounded-xl font-bold text-sm shadow-lg shadow-blue-900/50 transition-all flex items-center justify-center gap-2"
                            >
                                <span>⚙️</span> Process Request
                            </button>

                            <button 
                                @click="updateStatus('ready_for_pickup')"
                                :disabled="!['pending', 'processing'].includes(docRequest.status)"
                                class="flex-1 bg-green-600 hover:bg-green-500 disabled:opacity-50 disabled:bg-slate-700 text-white py-4 rounded-xl font-bold text-sm shadow-lg shadow-green-900/50 transition-all flex items-center justify-center gap-2"
                            >
                                <span>✅</span> Mark Ready
                            </button>

                            <button 
                                @click="updateStatus('rejected')"
                                :disabled="['completed', 'rejected'].includes(docRequest.status)"
                                class="px-6 border border-red-500/50 text-red-400 hover:bg-red-500/10 disabled:opacity-30 rounded-xl font-bold text-sm transition-all"
                            >
                                Reject
                            </button>

                        </div>

                        <div class="mt-6 pt-6 border-t border-white/5">
                            <label class="block text-[10px] text-slate-500 uppercase font-bold mb-2">Internal Admin Notes (Optional)</label>
                            <div class="flex gap-2">
                                <input v-model="form.admin_note" type="text" placeholder="Add remarks for records..." class="flex-1 bg-black/20 border border-white/10 rounded-lg px-4 py-2 text-sm text-white focus:ring-1 focus:ring-blue-500">
                                <button @click="form.patch(route('admin.documents.update', docRequest.id))" class="text-xs bg-slate-700 hover:bg-slate-600 px-4 rounded-lg text-white">Save</button>
                            </div>
                        </div>

                    </div>

                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>