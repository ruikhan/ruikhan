<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    docRequest: Object, // The DocumentRequest model
    user: Object        // The user who requested it
});

// Helper to format database keys (e.g., "subject_last_name" -> "Subject Last Name")
const formatKey = (key) => {
    return key
        .replace(/_/g, ' ')
        .replace(/\b\w/g, l => l.toUpperCase());
};

const form = useForm({
    status: '',
    admin_remarks: '',
    appointment_date: ''
});

const submitDecision = (status) => {
    form.status = status;
    form.put(route('admin.requests.update', props.docRequest.id), {
        onSuccess: () => form.reset()
    });
};

const statusColor = computed(() => {
    switch(props.docRequest.status) {
        case 'approved': return 'text-green-400 bg-green-400/10 border-green-400/20';
        case 'rejected': return 'text-red-400 bg-red-400/10 border-red-400/20';
        default: return 'text-yellow-400 bg-yellow-400/10 border-yellow-400/20';
    }
});
</script>

<template>
    <Head :title="`${docRequest.tracking_code} - Review`" />

    <AdminLayout>
        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            
            <div class="bg-[#1e1e2d] border border-white/5 rounded-2xl p-6 mb-6 shadow-xl flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <div class="flex items-center gap-3 mb-1">
                        <h1 class="text-2xl font-bold text-white tracking-tight">{{ docRequest.tracking_code }}</h1>
                        <span :class="`px-3 py-1 rounded-full text-xs font-bold border ${statusColor}`">
                            {{ docRequest.status.toUpperCase() }}
                        </span>
                    </div>
                    <p class="text-slate-400 flex items-center gap-2 text-sm">
                        <span class="text-blue-400">{{ docRequest.department }}</span>
                        <span>•</span>
                        <span>{{ docRequest.service_type }}</span>
                        <span>•</span>
                        <span>{{ new Date(docRequest.created_at).toLocaleDateString() }}</span>
                    </p>
                </div>
                
                <div class="flex items-center gap-3">
                    <div class="text-right hidden md:block">
                        <p class="text-white font-medium">{{ user.name }}</p>
                        <p class="text-slate-500 text-xs">{{ user.email }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold">
                        {{ user.name.charAt(0) }}
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-[#1e1e2d] border border-white/5 rounded-2xl p-6 shadow-xl">
                        <h3 class="text-lg font-semibold text-white mb-6 border-b border-white/5 pb-4">Application Details</h3>
                        
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-6">
                            <div v-for="(value, key) in docRequest.data" :key="key" class="col-span-1">
                                <dt class="text-xs uppercase tracking-wider text-slate-500 font-semibold mb-1">
                                    {{ formatKey(key) }}
                                </dt>
                                <dd class="text-slate-200 text-sm font-medium bg-black/20 px-3 py-2 rounded-lg border border-white/5">
                                    {{ value || 'N/A' }}
                                </dd>
                            </div>
                        </dl>

                        <div v-if="docRequest.user_remarks" class="mt-6">
                            <dt class="text-xs uppercase tracking-wider text-slate-500 font-semibold mb-1">Applicant Remarks</dt>
                            <dd class="text-slate-300 italic text-sm p-4 bg-black/20 rounded-lg border border-white/5">
                                "{{ docRequest.user_remarks }}"
                            </dd>
                        </div>
                    </div>

                    <div class="bg-[#1e1e2d] border border-white/5 rounded-2xl p-6 shadow-xl">
                        <h3 class="text-lg font-semibold text-white mb-4">Attachments</h3>
                        <div v-if="docRequest.attachments" class="grid grid-cols-1 gap-4">
                            <a 
                                :href="`/storage/${docRequest.attachments.primary}`" 
                                target="_blank"
                                class="flex items-center p-4 rounded-xl bg-blue-500/10 border border-blue-500/20 hover:bg-blue-500/20 transition-all group"
                            >
                                <div class="p-3 bg-blue-500/20 rounded-lg text-blue-400 group-hover:scale-110 transition-transform">
                                    📎
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-blue-100">Primary Document</p>
                                    <p class="text-xs text-blue-300">Click to view or download</p>
                                </div>
                            </a>
                        </div>
                        <p v-else class="text-slate-500 text-sm italic">No attachments provided.</p>
                    </div>
                </div>

                <div class="lg:col-span-1">
                    <div class="bg-[#1e1e2d] border border-white/5 rounded-2xl p-6 shadow-xl sticky top-6">
                        <h3 class="text-lg font-semibold text-white mb-6">Action Panel</h3>

                        <div v-if="docRequest.status === 'pending'" class="space-y-4">
                            
                            <div>
                                <label class="block text-xs font-medium text-slate-400 mb-1">Set Appointment Date (Optional)</label>
                                <input 
                                    type="datetime-local" 
                                    v-model="form.appointment_date"
                                    class="w-full bg-black/40 border border-white/10 rounded-lg px-3 py-2 text-white text-sm focus:ring-2 focus:ring-blue-500"
                                >
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-slate-400 mb-1">Admin Remarks / Rejection Reason</label>
                                <textarea 
                                    v-model="form.admin_remarks"
                                    rows="3"
                                    class="w-full bg-black/40 border border-white/10 rounded-lg px-3 py-2 text-white text-sm focus:ring-2 focus:ring-blue-500 resize-none"
                                    placeholder="Add notes for the applicant..."
                                ></textarea>
                            </div>

                            <div class="grid grid-cols-2 gap-3 pt-2">
                                <button 
                                    @click="submitDecision('rejected')"
                                    :disabled="form.processing"
                                    class="w-full py-2 px-4 bg-red-500/10 hover:bg-red-500/20 text-red-400 border border-red-500/20 rounded-xl text-sm font-bold transition-all"
                                >
                                    Reject
                                </button>
                                <button 
                                    @click="submitDecision('approved')"
                                    :disabled="form.processing"
                                    class="w-full py-2 px-4 bg-green-500 hover:bg-green-400 text-white rounded-xl text-sm font-bold shadow-lg shadow-green-500/20 transition-all"
                                >
                                    Approve
                                </button>
                            </div>
                        </div>

                        <div v-else class="text-center py-8">
                            <div class="text-5xl mb-4">
                                {{ docRequest.status === 'approved' ? '✅' : '❌' }}
                            </div>
                            <h4 class="text-white font-bold text-lg">Request {{ docRequest.status }}</h4>
                            <p class="text-slate-400 text-sm mt-2">
                                Processed on {{ new Date(docRequest.updated_at).toLocaleDateString() }}
                            </p>
                            <div v-if="docRequest.admin_remarks" class="mt-4 text-left bg-black/20 p-3 rounded-lg border border-white/5">
                                <p class="text-xs text-slate-500 uppercase">Remarks:</p>
                                <p class="text-sm text-slate-300">{{ docRequest.admin_remarks }}</p>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </AdminLayout>
</template>