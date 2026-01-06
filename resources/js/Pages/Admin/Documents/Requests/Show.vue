<script setup>
import RequestTracker from '@/Components/RequestTracker.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({ 
    docRequest: Object, 
    user: Object 
});

const form = useForm({
    status: props.docRequest.status,
    admin_remarks: props.docRequest.admin_remarks || '',
    appointment_date: props.docRequest.appointment_date || ''
});

const isSubmitting = ref(false);

const submitUpdate = () => {
    isSubmitting.value = true;
    form.patch(route('admin.documents.update', props.docRequest.id), {
        preserveScroll: true,
        onSuccess: () => {
            isSubmitting.value = false;
        },
        onError: () => {
            isSubmitting.value = false;
        }
    });
};

const formatKey = (key) => {
    if (!key) return '';
    return key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
};

const getStatusConfig = (status) => {
    const configs = {
        pending: {
            color: 'text-amber-400',
            bg: 'bg-amber-500',
            glow: 'shadow-amber-500/50',
            gradient: 'from-amber-600 to-yellow-600',
            label: 'Pending Review'
        },
        processing: {
            color: 'text-blue-400',
            bg: 'bg-blue-500',
            glow: 'shadow-blue-500/50',
            gradient: 'from-blue-600 to-cyan-600',
            label: 'Processing'
        },
        ready_for_pickup: {
            color: 'text-emerald-400',
            bg: 'bg-emerald-500',
            glow: 'shadow-emerald-500/50',
            gradient: 'from-emerald-600 to-green-600',
            label: 'Ready for Pickup'
        },
        completed: {
            color: 'text-slate-400',
            bg: 'bg-slate-500',
            glow: 'shadow-slate-500/50',
            gradient: 'from-slate-600 to-slate-700',
            label: 'Completed'
        },
        rejected: {
            color: 'text-red-400',
            bg: 'bg-red-500',
            glow: 'shadow-red-500/50',
            gradient: 'from-red-600 to-rose-600',
            label: 'Rejected'
        }
    };
    return configs[status] || configs.pending;
};

const currentStatus = getStatusConfig(props.docRequest.status);
</script>

<template>
    <Head :title="`Dossier #${docRequest.tracking_code}`" />

    <AuthenticatedLayout>
        <div class="min-h-screen bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 font-sans text-white relative overflow-hidden">
            
            <!-- Premium background effects -->
            <div class="fixed inset-0 pointer-events-none">
                <div class="absolute inset-0 opacity-[0.15] bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
                <div class="absolute inset-0 bg-gradient-to-tr from-blue-950/20 via-transparent to-purple-950/20"></div>
                <div class="absolute top-0 right-1/4 w-[600px] h-[600px] bg-blue-600/10 rounded-full blur-[120px] animate-pulse"></div>
                <div class="absolute bottom-0 left-1/4 w-[600px] h-[600px] bg-purple-600/10 rounded-full blur-[120px] animate-pulse" style="animation-delay: 1s"></div>
            </div>

            <div class="relative z-10 max-w-[1600px] mx-auto p-6 lg:p-12 space-y-8">
                
                <!-- Navigation & Header -->
                <div>
                    <Link :href="route('admin.documents.index')" 
                        class="inline-flex items-center gap-2 text-slate-400 hover:text-white transition-all mb-6 text-sm font-bold uppercase tracking-wider group">
                        <span class="transform group-hover:-translate-x-2 transition-transform">←</span>
                        <span>Return to Command Center</span>
                    </Link>
                    
                    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-end gap-6">
                        <div class="flex-1">
                            <div class="flex items-center gap-4 mb-3">
                                <h1 class="text-4xl lg:text-5xl font-bold bg-gradient-to-r from-white via-slate-200 to-slate-400 bg-clip-text text-transparent">
                                    Request Dossier
                                </h1>
                                <div class="px-4 py-2 bg-gradient-to-r from-blue-600/20 to-purple-600/20 border border-blue-500/30 rounded-xl">
                                    <span class="text-blue-300 font-mono text-lg font-bold">#{{ docRequest.tracking_code }}</span>
                                </div>
                            </div>
                            <p class="text-blue-400/80 text-xs font-mono uppercase tracking-[0.2em] flex items-center gap-2">
                                <span class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></span>
                                Classified • Secure Connection Active
                            </p>
                        </div>
                        
                        <!-- Status Badge -->
                        <div class="relative group">
                            <div :class="`absolute inset-0 ${currentStatus.glow} rounded-2xl blur-xl opacity-50 group-hover:opacity-75 transition-opacity`"></div>
                            <div :class="`relative px-6 py-4 rounded-2xl border bg-gradient-to-br ${currentStatus.gradient} border-white/20 backdrop-blur-md shadow-2xl`">
                                <span class="text-[10px] text-white/80 uppercase tracking-widest block mb-1 font-bold">Status</span>
                                <div :class="`${currentStatus.color} font-bold uppercase flex items-center gap-2 text-sm`">
                                    <span v-if="docRequest.status === 'pending'" class="relative flex h-3 w-3">
                                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-current opacity-75"></span>
                                      <span :class="`relative inline-flex rounded-full h-3 w-3 ${currentStatus.bg}`"></span>
                                    </span>
                                    <span v-else :class="`w-3 h-3 ${currentStatus.bg} rounded-full`"></span>
                                    {{ currentStatus.label }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content Grid -->
                <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
                    
                    <!-- Left Sidebar - User Info -->
                    <div class="xl:col-span-4 space-y-6">
                        
                        <!-- Applicant Card -->
                        <div class="relative overflow-hidden bg-gradient-to-br from-slate-900/90 to-slate-800/90 backdrop-blur-2xl border border-white/10 rounded-3xl shadow-2xl">
                            <div class="absolute inset-0 bg-gradient-to-br from-indigo-600/5 to-purple-600/5"></div>
                            
                            <div class="relative p-8">
                                <h3 class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-6 flex items-center gap-2">
                                    <span class="text-lg">👤</span>
                                    Applicant Identity
                                </h3>
                                
                                <div class="flex flex-col items-center text-center mb-8">
                                    <div class="relative group mb-4">
                                        <div class="absolute inset-0 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-3xl blur-2xl opacity-50 group-hover:opacity-75 transition-opacity"></div>
                                        <div class="relative w-24 h-24 rounded-3xl bg-gradient-to-br from-indigo-600 to-purple-600 flex items-center justify-center text-4xl font-bold shadow-2xl border-4 border-slate-900 transform group-hover:scale-105 transition-transform">
                                            {{ user.name.charAt(0) }}
                                        </div>
                                    </div>
                                    <h2 class="text-2xl font-bold text-white mb-1">{{ user.name }}</h2>
                                    <p class="text-sm text-slate-400 mb-1">{{ user.email }}</p>
                                    <div class="px-3 py-1 bg-blue-500/20 border border-blue-500/30 rounded-full text-xs text-blue-300 font-bold mt-2">
                                        Verified Citizen
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    <div class="p-4 bg-black/30 rounded-2xl border border-white/10 backdrop-blur-sm transform hover:scale-[1.02] transition-transform">
                                        <label class="block text-[10px] text-slate-500 uppercase font-bold mb-2 tracking-wider">Contact Number</label>
                                        <div class="text-base font-mono text-blue-200 font-semibold">{{ user.phone_number || 'Not provided' }}</div>
                                    </div>
                                    <div class="p-4 bg-black/30 rounded-2xl border border-white/10 backdrop-blur-sm transform hover:scale-[1.02] transition-transform">
                                        <label class="block text-[10px] text-slate-500 uppercase font-bold mb-2 tracking-wider">Registered Address</label>
                                        <div class="text-sm text-slate-300 leading-relaxed">{{ user.address || 'Address not registered' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Attachments Card -->
                        <div class="relative overflow-hidden bg-gradient-to-br from-slate-900/90 to-slate-800/90 backdrop-blur-2xl border border-white/10 rounded-3xl shadow-2xl">
                            <div class="absolute inset-0 bg-gradient-to-br from-blue-600/5 to-cyan-600/5"></div>
                            
                            <div class="relative p-8">
                                <h3 class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-6 flex items-center gap-2">
                                    <span class="text-lg">📎</span>
                                    Attachments
                                </h3>
                                
                                <div v-if="docRequest.attachments" class="space-y-3">
                                    <a :href="`/storage/${docRequest.attachments}`" target="_blank" 
                                        class="group relative block overflow-hidden p-5 rounded-2xl bg-gradient-to-br from-blue-600/20 to-cyan-600/20 border border-blue-500/30 hover:border-blue-400/50 transition-all transform hover:scale-[1.02] hover:-translate-y-1 shadow-lg hover:shadow-blue-500/30 cursor-pointer">
                                        <div class="flex items-center gap-4">
                                            <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-blue-500 to-cyan-500 flex items-center justify-center text-2xl shadow-lg transform group-hover:scale-110 group-hover:rotate-6 transition-all">
                                                📄
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-bold text-blue-100 truncate mb-1">Supporting Document</p>
                                                <p class="text-xs text-blue-300 uppercase tracking-wide">Click to view file</p>
                                            </div>
                                            <svg class="w-5 h-5 text-blue-300 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                            </svg>
                                        </div>
                                    </a>
                                </div>
                                <div v-else class="text-center py-12">
                                    <div class="text-5xl mb-3 opacity-10">📎</div>
                                    <p class="text-sm text-slate-500 italic">No attachments provided</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Content - Request Details & Actions -->
                    <div class="xl:col-span-8 space-y-6">
                        
                        <!-- Request Details Card -->
                        <div class="relative overflow-hidden bg-gradient-to-br from-slate-900/90 to-slate-800/90 backdrop-blur-2xl border border-white/10 rounded-3xl shadow-2xl">
                            <div class="absolute -top-20 -right-20 text-[200px] opacity-[0.02] pointer-events-none">
                                {{ docRequest.icon || '📄' }}
                            </div>
                            
                            <div class="relative p-8">
                                <div class="flex flex-col lg:flex-row justify-between items-start gap-6 pb-6 mb-8 border-b border-white/10">
                                    <div>
                                        <h3 class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-3">Document Request</h3>
                                        <div class="text-3xl font-bold bg-gradient-to-r from-white to-slate-300 bg-clip-text text-transparent">
                                            {{ docRequest.document_type }}
                                        </div>
                                    </div>
                                    <div class="px-5 py-3 bg-gradient-to-r from-blue-600/20 to-purple-600/20 border border-blue-500/30 rounded-2xl backdrop-blur-sm">
                                        <h3 class="text-[10px] text-slate-400 uppercase font-bold tracking-widest mb-1">Department</h3>
                                        <div class="text-sm font-bold text-blue-300">{{ docRequest.department }}</div>
                                    </div>
                                </div>

                                <!-- Request Data Grid -->
                                <div class="mb-8">
                                    <label class="block text-xs text-slate-400 uppercase font-bold mb-4 tracking-wider">Submitted Information</label>
                                    
                                    <div class="p-6 bg-black/30 rounded-2xl border border-white/10 backdrop-blur-sm">
                                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <div v-for="(value, key) in docRequest.data" :key="key" 
                                                class="group relative pl-4 border-l-2 border-slate-700 hover:border-blue-500 transition-colors">
                                                <dt class="text-[10px] uppercase tracking-wider text-slate-500 font-bold mb-2">
                                                    {{ formatKey(key) }}
                                                </dt>
                                                <dd class="text-sm font-medium text-slate-200 break-words leading-relaxed">
                                                    {{ value || '—' }}
                                                </dd>
                                            </div>
                                        </dl>
                                    </div>
                                </div>

                                <!-- User Remarks -->
                                <div v-if="docRequest.remarks" class="p-6 bg-gradient-to-br from-blue-950/30 to-purple-950/30 rounded-2xl border border-blue-500/20 backdrop-blur-sm">
                                    <label class="block text-xs text-blue-300 uppercase font-bold mb-3 tracking-wider flex items-center gap-2">
                                        <span class="text-base">💬</span>
                                        Applicant's Note
                                    </label>
                                    <p class="text-sm text-slate-300 italic leading-relaxed">"{{ docRequest.remarks }}"</p>
                                </div>
                            </div>
                        </div>

                        <!-- Request Tracker -->
<RequestTracker 
    :status="docRequest.status" 
    :department="docRequest.department"
    :documentType="docRequest.document_type"
    :requestData="docRequest.data || {}"
/>

                        <!-- Admin Action Panel -->
                        <div class="relative overflow-hidden bg-gradient-to-br from-slate-900/90 to-slate-800/90 backdrop-blur-2xl border border-white/10 rounded-3xl shadow-2xl">
                            <div class="absolute inset-0 bg-gradient-to-br from-blue-600/5 to-purple-600/5"></div>
                            
                            <div class="relative p-8">
                                <h3 class="text-slate-400 text-sm font-bold uppercase tracking-widest mb-6 flex items-center gap-3">
                                    <span class="w-3 h-3 bg-blue-500 rounded-full animate-pulse"></span>
                                    Administrative Control Panel
                                </h3>
                                
                                <form @submit.prevent="submitUpdate" class="space-y-6">
                                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                        
                                        <!-- Status Update -->
                                        <div class="space-y-6">
                                            <div>
                                                <label class="block text-xs font-bold text-slate-300 mb-3 uppercase tracking-wide">Update Status</label>
                                                <div class="relative">
                                                    <select 
                                                        v-model="form.status"
                                                        class="w-full bg-black/50 border-2 border-white/20 rounded-2xl px-5 py-4 text-white font-medium focus:ring-2 focus:ring-blue-500 focus:border-transparent appearance-none cursor-pointer hover:bg-black/60 hover:border-white/30 transition-all shadow-lg"
                                                    >
                                                        <option value="pending">⏳ Pending Review</option>
                                                        <option value="processing">🔄 Processing</option>
                                                        <option value="ready_for_pickup">✅ Ready for Pickup</option>
                                                        <option value="completed">🎉 Completed / Claimed</option>
                                                        <option value="rejected">❌ Rejected</option>
                                                    </select>
                                                    <div class="absolute right-4 top-1/2 transform -translate-y-1/2 pointer-events-none">
                                                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                        </svg>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Appointment Date (conditional) -->
                                            <div v-if="form.status === 'ready_for_pickup'" class="animate-slideIn">
                                                <label class="block text-xs font-bold text-emerald-300 mb-3 uppercase tracking-wide flex items-center gap-2">
                                                    <span class="text-base">📅</span>
                                                    Schedule Pickup
                                                </label>
                                                <input 
                                                    type="datetime-local"
                                                    v-model="form.appointment_date"
                                                    class="w-full bg-emerald-950/50 border-2 border-emerald-500/30 rounded-2xl px-5 py-4 text-emerald-100 font-medium focus:ring-2 focus:ring-emerald-500 focus:border-transparent shadow-lg hover:bg-emerald-950/60 transition-all"
                                                >
                                                <p class="text-xs text-emerald-400/60 mt-2 flex items-center gap-2">
                                                    <span class="w-1 h-1 bg-emerald-400 rounded-full"></span>
                                                    Applicant will be notified via email
                                                </p>
                                            </div>
                                        </div>

                                        <!-- Admin Remarks -->
                                        <div class="flex flex-col">
                                            <label class="block text-xs font-bold text-slate-300 mb-3 uppercase tracking-wide">Official Instructions</label>
                                            <textarea 
                                                v-model="form.admin_remarks"
                                                class="flex-1 w-full bg-black/50 border-2 border-white/20 rounded-2xl px-5 py-4 text-white font-medium focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none hover:bg-black/60 hover:border-white/30 transition-all shadow-lg"
                                                rows="5"
                                                placeholder="e.g., 'Bring valid government-issued ID and original birth certificate...'"
                                            ></textarea>
                                        </div>
                                    </div>

                                    <!-- Submit Button -->
                                    <div class="pt-4">
                                        <button 
                                            type="submit" 
                                            :disabled="form.processing || isSubmitting"
                                            :class="form.status === 'rejected' ? 
                                                'bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-500 hover:to-rose-500 shadow-red-500/30' : 
                                                'bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-500 hover:to-purple-500 shadow-blue-500/30'"
                                            class="relative w-full py-4 rounded-2xl font-bold text-base text-white transition-all shadow-2xl flex justify-center items-center gap-3 transform active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed group overflow-hidden"
                                        >
                                            <div class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/10 to-white/0 transform -skew-x-12 translate-x-[-200%] group-hover:translate-x-[200%] transition-transform duration-1000"></div>
                                            <span v-if="form.processing || isSubmitting" class="flex items-center gap-2">
                                                <svg class="animate-spin h-5 w-5" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                                Syncing Changes...
                                            </span>
                                            <span v-else class="flex items-center gap-2">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                Update Request Record
                                            </span>
                                        </button>
                                    </div>
                                </form>
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
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-slideIn {
    animation: slideIn 0.3s ease-out;
}
</style>