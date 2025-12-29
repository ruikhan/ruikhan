<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
// ✅ IMPORT THE NEW COMPONENT
import RequestTracker from '@/Components/RequestTracker.vue';

const props = defineProps({ 
    docRequest: Object, 
    user: Object 
});

// Initialize form with current values
const form = useForm({
    status: props.docRequest.status,
    admin_remarks: props.docRequest.admin_remarks || '',
    appointment_date: props.docRequest.appointment_date || ''
});

const submitUpdate = () => {
    form.patch(route('admin.documents.update', props.docRequest.id), {
        preserveScroll: true,
        onSuccess: () => {
            // Optional: Add toast notification trigger here
        }
    });
};

// Helper to clean up database keys
const formatKey = (key) => {
    if (!key) return '';
    return key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
};
</script>

<template>
    <Head :title="`Dossier #${docRequest.tracking_code}`" />

    <AuthenticatedLayout>
        <div class="min-h-screen bg-slate-900 font-sans text-white p-6 lg:p-12 relative overflow-hidden">
            
            <div class="absolute inset-0 opacity-10 pointer-events-none" style="background-image: radial-gradient(#64748b 1px, transparent 1px); background-size: 32px 32px;"></div>

            <div class="max-w-6xl mx-auto mb-8 relative z-10">
                <Link :href="route('admin.documents.index')" class="flex items-center gap-2 text-slate-400 hover:text-white transition-colors mb-4 text-sm font-bold uppercase tracking-wider group">
                    <span class="group-hover:-translate-x-1 transition-transform">←</span>
                    Back to Command Center
                </Link>
                
                <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
                    <div>
                        <h1 class="text-3xl font-bold text-white tracking-tight flex items-center gap-3">
                            Request Dossier 
                            <span class="text-slate-600 font-mono">#{{ docRequest.tracking_code }}</span>
                        </h1>
                        <p class="text-blue-400 text-xs font-mono uppercase mt-1 tracking-widest">SECURE CONNECTION // OFFICIAL USE ONLY</p>
                    </div>
                    
                    <div class="px-5 py-3 rounded-xl border border-white/10 bg-white/5 backdrop-blur-md shadow-lg">
                        <span class="text-[10px] text-slate-400 uppercase tracking-widest block mb-1">Current Status</span>
                        
                        <span v-if="docRequest.status === 'pending'" class="text-yellow-400 font-bold uppercase flex items-center gap-2">
                            <span class="relative flex h-3 w-3">
                              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-yellow-400 opacity-75"></span>
                              <span class="relative inline-flex rounded-full h-3 w-3 bg-yellow-500"></span>
                            </span>
                            Pending Review
                        </span>

                        <span v-else-if="docRequest.status === 'processing'" class="text-blue-400 font-bold uppercase flex items-center gap-2">
                            <span class="w-2 h-2 bg-blue-400 rounded-full animate-pulse"></span> Processing
                        </span>

                        <span v-else-if="docRequest.status === 'ready_for_pickup'" class="text-green-400 font-bold uppercase flex items-center gap-2">
                            <span class="w-2 h-2 bg-green-400 rounded-full"></span> Ready for Pickup
                        </span>

                        <span v-else-if="docRequest.status === 'completed'" class="text-slate-400 font-bold uppercase flex items-center gap-2">
                            <span class="w-2 h-2 bg-slate-400 rounded-full"></span> Completed
                        </span>

                        <span v-else class="text-red-400 font-bold uppercase flex items-center gap-2">
                            <span class="w-2 h-2 bg-red-400 rounded-full"></span> Rejected
                        </span>
                    </div>
                </div>
            </div>

            <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-8 relative z-10">
                
                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-slate-800/50 backdrop-blur-xl border border-white/10 rounded-2xl p-6">
                        <h3 class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-6 border-b border-white/5 pb-2">Applicant Identity</h3>
                        
                        <div class="flex flex-col items-center text-center mb-6">
                            <div class="w-20 h-20 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-3xl font-bold shadow-2xl shadow-indigo-500/20 mb-4 border-4 border-slate-800">
                                {{ user.name.charAt(0) }}
                            </div>
                            <h2 class="text-xl font-bold text-white">{{ user.name }}</h2>
                            <p class="text-sm text-slate-400">{{ user.email }}</p>
                        </div>

                        <div class="space-y-4">
                            <div class="p-3 bg-black/20 rounded-lg border border-white/5">
                                <label class="block text-[10px] text-slate-500 uppercase font-bold">Contact Number</label>
                                <div class="text-sm font-mono text-blue-200">{{ user.phone_number || 'N/A' }}</div>
                            </div>
                            <div class="p-3 bg-black/20 rounded-lg border border-white/5">
                                <label class="block text-[10px] text-slate-500 uppercase font-bold">Address</label>
                                <div class="text-sm text-slate-300">{{ user.address || 'Address not registered' }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-slate-800/50 backdrop-blur-xl border border-white/10 rounded-2xl p-6">
                         <h3 class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-4 border-b border-white/5 pb-2">Evidence / Attachments</h3>
                         
                         <div v-if="docRequest.attachments" class="space-y-3">
                            <a :href="`/storage/${docRequest.attachments}`" target="_blank" class="flex items-center gap-3 p-3 rounded-lg bg-blue-600/10 border border-blue-500/20 hover:bg-blue-600/20 transition-all group cursor-pointer">
                                <div class="w-10 h-10 rounded bg-blue-500/20 flex items-center justify-center text-lg">📄</div>
                                <div class="overflow-hidden">
                                    <p class="text-sm font-bold text-blue-100 truncate">View Document</p>
                                    <p class="text-[10px] text-blue-300 uppercase">Click to Open</p>
                                </div>
                            </a>
                         </div>
                         <div v-else class="text-sm text-slate-500 italic text-center py-4">
                            No attachments provided.
                         </div>
                    </div>
                </div>

                <div class="lg:col-span-2 space-y-6">
                    
                    <div class="bg-slate-800/50 backdrop-blur-xl border border-white/10 rounded-2xl p-8 relative overflow-hidden group">
                        <div class="absolute -top-6 -right-6 p-6 opacity-[0.03] text-9xl pointer-events-none grayscale group-hover:grayscale-0 transition-all duration-1000">
                             {{ docRequest.icon || '📄' }}
                        </div>
                        
                        <div class="flex justify-between items-start border-b border-white/5 pb-4 mb-6">
                            <div>
                                <h3 class="text-slate-400 text-xs font-bold uppercase tracking-widest">Request Specification</h3>
                                <div class="text-2xl font-bold text-white mt-1">{{ docRequest.document_type }}</div>
                            </div>
                            <div class="text-right">
                                <h3 class="text-slate-400 text-xs font-bold uppercase tracking-widest">Department</h3>
                                <div class="text-sm font-bold text-blue-400 mt-1">{{ docRequest.department }}</div>
                            </div>
                        </div>

                        <div class="bg-black/20 rounded-xl p-6 border border-white/5">
                            <label class="block text-[10px] text-slate-500 uppercase font-bold mb-4">Submitted Information</label>
                            
                            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-6">
                                <div v-for="(value, key) in docRequest.data" :key="key" class="border-l-2 border-slate-700 pl-3">
                                    <dt class="text-[10px] uppercase tracking-wider text-slate-500 font-bold mb-1">
                                        {{ formatKey(key) }}
                                    </dt>
                                    <dd class="text-sm font-medium text-slate-200 break-words">
                                        {{ value || '—' }}
                                    </dd>
                                </div>
                            </dl>
                        </div>

                        <div v-if="docRequest.remarks" class="mt-6 pt-4 border-t border-white/5">
                            <label class="block text-[10px] text-slate-500 uppercase font-bold mb-2">User Remarks</label>
                            <p class="text-sm text-slate-300 italic">"{{ docRequest.remarks }}"</p>
                        </div>
                    </div>

                    <RequestTracker :status="docRequest.status" />

                    <div class="bg-slate-800/80 backdrop-blur-xl border border-white/10 rounded-2xl p-6 shadow-2xl">
                        <h3 class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-4 flex items-center gap-2">
                            <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                            Official Action Console
                        </h3>
                        
                        <form @submit.prevent="submitUpdate" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-400 mb-2 uppercase">Update Status</label>
                                    <select 
                                        v-model="form.status"
                                        class="w-full bg-black/40 border border-white/20 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-blue-500 appearance-none cursor-pointer hover:bg-white/5 transition-colors"
                                    >
                                        <option value="pending">Pending Review</option>
                                        <option value="processing">Processing</option>
                                        <option value="ready_for_pickup">Ready for Pickup</option>
                                        <option value="completed">Completed / Claimed</option>
                                        <option value="rejected">Rejected</option>
                                    </select>
                                </div>

                                <div v-if="form.status === 'ready_for_pickup'" class="animate-fade-in">
                                    <label class="block text-xs font-bold text-green-400 mb-2 uppercase">Pickup Schedule</label>
                                    <input 
                                        type="datetime-local"
                                        v-model="form.appointment_date"
                                        class="w-full bg-green-500/10 border border-green-500/30 rounded-xl px-4 py-3 text-green-100 focus:ring-2 focus:ring-green-500"
                                    >
                                    <p class="text-[10px] text-slate-500 mt-1">*User will be notified of this date.</p>
                                </div>
                            </div>

                            <div class="flex flex-col h-full">
                                <label class="block text-xs font-bold text-slate-400 mb-2 uppercase">Admin Remarks / Instructions</label>
                                <textarea 
                                    v-model="form.admin_remarks"
                                    class="flex-1 w-full bg-black/40 border border-white/20 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-blue-500 resize-none text-sm mb-4"
                                    placeholder="Enter instructions for the applicant (e.g., 'Bring Valid ID')..."
                                ></textarea>
                                
                                <button 
                                    type="submit" 
                                    :disabled="form.processing"
                                    class="w-full py-3 rounded-xl font-bold text-sm text-white transition-all shadow-lg flex justify-center items-center gap-2 transform active:scale-95"
                                    :class="{
                                        'bg-blue-600 hover:bg-blue-500 shadow-blue-500/20': form.status !== 'rejected',
                                        'bg-red-600 hover:bg-red-500 shadow-red-500/20': form.status === 'rejected'
                                    }"
                                >
                                    <span v-if="form.processing">Syncing...</span>
                                    <span v-else>Update Request Record</span>
                                </button>
                            </div>

                        </form>
                    </div>

                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.animate-fade-in {
    animation: fadeIn 0.3s ease-in-out;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-5px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>