<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({ application: Object, user: Object });

const scheduleForm = useForm({
    scheduled_at:      '',
    assigned_doctor:   '',
    clinic_location:   'Pili Municipal Health Center, Pili, Camarines Sur',
    assistance_amount: '',
    admin_note:        '',
});
const rejectForm = useForm({ rejection_reason: '' });
const showScheduleModal = ref(false);
const showRejectModal   = ref(false);

const submitSchedule = () => {
    scheduleForm.post(route('admin.health.approve', props.application.id), {
        preserveScroll: true,
        onSuccess: () => { showScheduleModal.value = false; },
    });
};
const submitReject = () => {
    rejectForm.post(route('admin.health.reject', props.application.id), {
        preserveScroll: true,
        onSuccess: () => { showRejectModal.value = false; },
    });
};
const completeForm = useForm({});
const submitComplete = () => {
    if (!confirm('Mark appointment as completed?')) return;
    completeForm.post(route('admin.health.complete', props.application.id), { preserveScroll: true });
};

const status     = computed(() => props.application?.status);
const canSchedule = computed(() => ['pending', 'under_review'].includes(status.value));
const canReject   = computed(() => ['pending', 'under_review'].includes(status.value));
const canComplete = computed(() => status.value === 'scheduled');

const statusCfg = {
    pending:      { label: 'Pending',      badge: 'bg-yellow-500/20 text-yellow-400 border-yellow-500/30', dot: 'bg-yellow-400' },
    under_review: { label: 'Under Review', badge: 'bg-blue-500/20 text-blue-400 border-blue-500/30',       dot: 'bg-blue-400' },
    scheduled:    { label: 'Scheduled',    badge: 'bg-purple-500/20 text-purple-400 border-purple-500/30', dot: 'bg-purple-400' },
    completed:    { label: 'Completed',    badge: 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30', dot: 'bg-emerald-400' },
    rejected:     { label: 'Rejected',     badge: 'bg-red-500/20 text-red-400 border-red-500/30',           dot: 'bg-red-400' },
};
const getStatus = s => statusCfg[s] ?? { label: s, badge: 'bg-slate-500/20 text-slate-400 border-slate-500/30', dot: 'bg-slate-400' };
const fmtKey = k => k.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
const fmtDt  = d => d ? new Date(d).toLocaleString('en-PH', { month: 'long', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit' }) : '—';

const typeIcon = t => ({
    'Medical Assistance':'💊','Free Consultation':'🩺','Medicine Request':'💉','Laboratory Request':'🔬','Mental Health Support':'🧠'
}[t] ?? '🏥');

const workflowSteps = [
    { key: 'pending',      label: 'Submitted',    icon: '📝' },
    { key: 'under_review', label: 'Under Review', icon: '🔍' },
    { key: 'scheduled',    label: 'Scheduled',    icon: '📅' },
    { key: 'completed',    label: 'Completed',    icon: '✅' },
];
const stepOrder = { pending: 0, under_review: 1, scheduled: 2, completed: 3, rejected: -1 };
const currentStep = computed(() => stepOrder[status.value] ?? 0);

const minScheduleDate = computed(() => {
    const d = new Date(); d.setDate(d.getDate() + 1);
    return d.toISOString().slice(0, 16);
});
</script>

<template>
    <Head :title="`Health — ${application?.tracking_code}`" />
    <AuthenticatedLayout>
        <div class="min-h-screen bg-slate-900 text-white font-sans p-6 lg:p-12">
            <div class="absolute inset-0 opacity-5 pointer-events-none" style="background-image: radial-gradient(#64748b 1px, transparent 1px); background-size: 32px 32px;"></div>

            <!-- Header -->
            <div class="max-w-6xl mx-auto mb-8 relative z-10">
                <Link :href="route('admin.health.index')" class="flex items-center gap-2 text-slate-400 hover:text-white text-sm font-bold uppercase tracking-wider group mb-4">
                    <span class="group-hover:-translate-x-1 transition-transform">←</span> Back to Health
                </Link>
                <div class="flex flex-col md:flex-row justify-between items-start gap-4">
                    <div>
                        <h1 class="text-2xl font-bold flex items-center gap-3">
                            {{ typeIcon(application.application_type) }} {{ application.application_type }}
                            <span class="font-mono text-slate-500 text-lg">#{{ application.tracking_code }}</span>
                        </h1>
                    </div>
                    <span :class="['px-4 py-2 rounded-xl border text-sm font-bold flex items-center gap-2', getStatus(status).badge]">
                        <span :class="[getStatus(status).dot, 'w-2 h-2 rounded-full animate-pulse']"></span>
                        {{ getStatus(status).label }}
                    </span>
                </div>
            </div>

            <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-8 relative z-10">
                <!-- LEFT -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Patient card -->
                    <div class="bg-slate-800/50 backdrop-blur border border-white/10 rounded-2xl p-6">
                        <h3 class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-4 pb-2 border-b border-white/5">Patient</h3>
                        <div class="flex flex-col items-center text-center mb-4">
                            <div class="w-16 h-16 rounded-full bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-2xl font-bold mb-3 border-4 border-slate-800 shadow-xl">
                                {{ user.name?.charAt(0) ?? '?' }}
                            </div>
                            <h2 class="text-lg font-bold">{{ user.name }}</h2>
                            <p class="text-sm text-slate-400">{{ user.email }}</p>
                        </div>
                        <div class="space-y-2">
                            <div class="p-3 bg-black/20 rounded-lg border border-white/5">
                                <label class="text-[10px] text-slate-500 uppercase font-bold block">Phone</label>
                                <div class="text-sm font-mono text-blue-200">{{ user.phone_number ?? 'N/A' }}</div>
                            </div>
                            <div class="p-3 bg-black/20 rounded-lg border border-white/5">
                                <label class="text-[10px] text-slate-500 uppercase font-bold block">Address</label>
                                <div class="text-sm text-slate-300">{{ user.address ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Progress -->
                    <div class="bg-slate-800/50 backdrop-blur border border-white/10 rounded-2xl p-6">
                        <h3 class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-5 pb-2 border-b border-white/5">Progress</h3>
                        <div class="space-y-0">
                            <div v-for="(step, i) in workflowSteps" :key="step.key" class="flex items-start gap-3">
                                <div class="flex flex-col items-center">
                                    <div :class="['w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold transition-all',
                                        status === 'rejected' ? 'bg-red-500/20 text-red-400 border border-red-500/30' :
                                        i <= currentStep ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' :
                                        'bg-slate-800 text-slate-600 border border-slate-700']">
                                        {{ i <= currentStep && status !== 'rejected' ? '✓' : step.icon }}
                                    </div>
                                    <div v-if="i < workflowSteps.length - 1" :class="['w-0.5 h-8 my-1', i < currentStep ? 'bg-emerald-500/40' : 'bg-slate-700']"></div>
                                </div>
                                <div class="pt-1 pb-6">
                                    <p :class="['text-sm font-semibold', i <= currentStep && status !== 'rejected' ? 'text-white' : 'text-slate-500']">{{ step.label }}</p>
                                </div>
                            </div>
                            <div v-if="status === 'rejected'" class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-red-500/20 text-red-400 border border-red-500/30 flex items-center justify-center text-sm font-bold">✕</div>
                                <p class="text-sm font-semibold text-red-400">Rejected</p>
                            </div>
                        </div>
                    </div>

                    <!-- Appointment info (when scheduled) -->
                    <div v-if="['scheduled','completed'].includes(status)" class="bg-purple-950/30 border border-purple-500/30 rounded-2xl p-5">
                        <h3 class="text-purple-400 text-xs font-bold uppercase tracking-widest mb-4">Appointment Details</h3>
                        <div class="space-y-3">
                            <div>
                                <label class="text-[10px] text-slate-500 uppercase font-bold block">Schedule</label>
                                <p class="text-sm text-white font-semibold">📅 {{ fmtDt(application.scheduled_at) }}</p>
                            </div>
                            <div>
                                <label class="text-[10px] text-slate-500 uppercase font-bold block">Doctor</label>
                                <p class="text-sm text-white">👨‍⚕️ {{ application.assigned_doctor }}</p>
                            </div>
                            <div>
                                <label class="text-[10px] text-slate-500 uppercase font-bold block">Location</label>
                                <p class="text-sm text-white">📍 {{ application.clinic_location }}</p>
                            </div>
                            <div v-if="application.assistance_amount">
                                <label class="text-[10px] text-slate-500 uppercase font-bold block">Assistance Amount</label>
                                <p class="text-lg font-bold text-emerald-400">₱{{ Number(application.assistance_amount).toLocaleString() }}</p>
                            </div>
                        </div>
                        <button v-if="canComplete" @click="submitComplete" :disabled="completeForm.processing"
                            class="w-full mt-4 py-2.5 bg-emerald-600/20 hover:bg-emerald-600/40 border border-emerald-500/30 rounded-xl text-emerald-400 text-sm font-bold transition-all">
                            ✅ Mark as Completed
                        </button>
                    </div>
                </div>

                <!-- RIGHT -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Application Data -->
                    <div class="bg-slate-800/50 backdrop-blur border border-white/10 rounded-2xl p-8">
                        <div class="flex justify-between items-start border-b border-white/5 pb-4 mb-6">
                            <div>
                                <h3 class="text-slate-400 text-xs uppercase tracking-widest font-bold">Health Application</h3>
                                <p class="text-2xl font-bold text-white mt-1">{{ application.application_type }}</p>
                            </div>
                            <span class="text-4xl">{{ typeIcon(application.application_type) }}</span>
                        </div>

                        <div class="bg-black/20 rounded-xl p-6 border border-white/5">
                            <label class="text-[10px] text-slate-500 uppercase font-bold block mb-4">Patient Information</label>
                            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-5">
                                <div v-for="(value, key) in application.application_data" :key="key" class="border-l-2 border-slate-700 pl-3">
                                    <dt class="text-[10px] uppercase tracking-wider text-slate-500 font-bold mb-1">{{ fmtKey(String(key)) }}</dt>
                                    <dd class="text-sm font-medium text-slate-200 break-words">{{ value || '—' }}</dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Attachments -->
                        <div v-if="application.attachments?.length" class="mt-4 pt-4 border-t border-white/5">
                            <label class="text-[10px] text-slate-500 uppercase font-bold block mb-3">Submitted Documents</label>
                            <div class="flex flex-wrap gap-2">
                                <a v-for="(att, i) in application.attachments" :key="i" :href="`/storage/${att}`" target="_blank"
                                    class="flex items-center gap-2 px-3 py-2 bg-blue-600/10 border border-blue-500/20 rounded-lg text-blue-300 text-xs hover:bg-blue-600/20 transition-all">
                                    📎 Document {{ i + 1 }}
                                </a>
                            </div>
                        </div>

                        <!-- Rejection -->
                        <div v-if="application.rejection_reason" class="mt-4 bg-red-950/30 rounded-xl p-4 border border-red-500/20">
                            <label class="text-[10px] text-red-400 uppercase font-bold block mb-2">Rejection Reason</label>
                            <p class="text-sm text-red-300">{{ application.rejection_reason }}</p>
                        </div>
                    </div>

                    <!-- Workflow History -->
                    <div v-if="application.workflow_history?.length" class="bg-slate-800/50 backdrop-blur border border-white/10 rounded-2xl p-6">
                        <h3 class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-4">Workflow History</h3>
                        <div class="space-y-3">
                            <div v-for="(entry, i) in [...application.workflow_history].reverse()" :key="i"
                                class="flex items-start gap-3 pb-3 border-b border-slate-800/50 last:border-0">
                                <div class="w-2 h-2 rounded-full bg-emerald-400 mt-1.5 flex-shrink-0"></div>
                                <div>
                                    <p class="text-sm font-semibold text-white capitalize">{{ entry.status?.replace(/_/g, ' ') }}</p>
                                    <p class="text-xs text-slate-400">{{ entry.admin_name }} · {{ new Date(entry.timestamp).toLocaleString('en-PH') }}</p>
                                    <p v-if="entry.note" class="text-xs text-slate-500 italic mt-1">"{{ entry.note }}"</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Console -->
                    <div v-if="canSchedule || canReject" class="bg-slate-800/80 backdrop-blur border border-white/10 rounded-2xl p-6">
                        <h3 class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-4 flex items-center gap-2">
                            <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                            Action Console
                        </h3>
                        <div class="flex flex-col sm:flex-row gap-3">
                            <button @click="showScheduleModal = true"
                                class="flex-1 py-3 bg-emerald-600/20 hover:bg-emerald-600/40 border border-emerald-500/30 rounded-xl text-emerald-400 font-bold text-sm transition-all hover:scale-[1.02]">
                                📅 Schedule Appointment
                            </button>
                            <button @click="showRejectModal = true"
                                class="flex-1 py-3 bg-red-600/20 hover:bg-red-600/40 border border-red-500/30 rounded-xl text-red-400 font-bold text-sm transition-all hover:scale-[1.02]">
                                ❌ Reject Application
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Schedule Modal -->
        <Transition enter-active-class="transition duration-200" enter-from-class="opacity-0">
            <div v-if="showScheduleModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4">
                <div class="bg-slate-900 border border-emerald-500/30 rounded-2xl max-w-md w-full p-6 shadow-2xl">
                    <h3 class="text-lg font-bold text-emerald-400 mb-5">📅 Schedule Appointment</h3>
                    <form @submit.prevent="submitSchedule" class="space-y-4">
                        <div>
                            <label class="text-xs font-bold text-slate-400 uppercase block mb-2">Appointment Date & Time <span class="text-red-400">*</span></label>
                            <input type="datetime-local" v-model="scheduleForm.scheduled_at" :min="minScheduleDate" required
                                class="w-full bg-black/40 border border-white/20 rounded-xl px-4 py-3 text-white text-sm focus:ring-2 focus:ring-emerald-500"/>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-400 uppercase block mb-2">Assigned Doctor / Health Worker <span class="text-red-400">*</span></label>
                            <input type="text" v-model="scheduleForm.assigned_doctor" required placeholder="e.g. Dr. Maria Santos"
                                class="w-full bg-black/40 border border-white/20 rounded-xl px-4 py-3 text-white text-sm focus:ring-2 focus:ring-emerald-500"/>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-400 uppercase block mb-2">Clinic / Location <span class="text-red-400">*</span></label>
                            <input type="text" v-model="scheduleForm.clinic_location" required
                                class="w-full bg-black/40 border border-white/20 rounded-xl px-4 py-3 text-white text-sm focus:ring-2 focus:ring-emerald-500"/>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-400 uppercase block mb-2">Medical Assistance Amount (₱) <span class="text-slate-600">optional</span></label>
                            <input type="number" v-model="scheduleForm.assistance_amount" min="0" placeholder="0"
                                class="w-full bg-black/40 border border-white/20 rounded-xl px-4 py-3 text-white text-sm focus:ring-2 focus:ring-emerald-500"/>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-400 uppercase block mb-2">Notes for Patient</label>
                            <textarea v-model="scheduleForm.admin_note" rows="3" placeholder="e.g. Please bring your valid ID and referral slip..."
                                class="w-full bg-black/40 border border-white/20 rounded-xl px-4 py-3 text-white text-sm focus:ring-2 focus:ring-emerald-500 resize-none"></textarea>
                        </div>
                        <div class="flex gap-3 pt-2">
                            <button type="button" @click="showScheduleModal = false" class="flex-1 py-2.5 bg-slate-700 rounded-xl text-slate-300 text-sm font-bold hover:bg-slate-600 transition-colors">Cancel</button>
                            <button type="submit" :disabled="scheduleForm.processing"
                                class="flex-1 py-2.5 bg-emerald-600 hover:bg-emerald-500 rounded-xl text-white text-sm font-bold disabled:opacity-50 transition-colors">
                                {{ scheduleForm.processing ? 'Scheduling...' : '✅ Confirm Schedule' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </Transition>

        <!-- Reject Modal -->
        <Transition enter-active-class="transition duration-200" enter-from-class="opacity-0">
            <div v-if="showRejectModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4">
                <div class="bg-slate-900 border border-red-500/30 rounded-2xl max-w-md w-full p-6 shadow-2xl">
                    <h3 class="text-lg font-bold text-red-400 mb-5">❌ Reject Health Application</h3>
                    <form @submit.prevent="submitReject" class="space-y-4">
                        <div>
                            <label class="text-xs font-bold text-slate-400 uppercase block mb-2">Rejection Reason <span class="text-red-400">*</span></label>
                            <textarea v-model="rejectForm.rejection_reason" rows="4" required
                                class="w-full bg-black/40 border border-red-500/20 rounded-xl px-4 py-3 text-white text-sm focus:ring-2 focus:ring-red-500 resize-none"
                                placeholder="Explain why this application cannot be processed..."></textarea>
                        </div>
                        <div class="flex gap-3 pt-2">
                            <button type="button" @click="showRejectModal = false" class="flex-1 py-2.5 bg-slate-700 rounded-xl text-slate-300 text-sm font-bold hover:bg-slate-600 transition-colors">Cancel</button>
                            <button type="submit" :disabled="rejectForm.processing"
                                class="flex-1 py-2.5 bg-red-600 hover:bg-red-500 rounded-xl text-white text-sm font-bold disabled:opacity-50 transition-colors">
                                {{ rejectForm.processing ? 'Rejecting...' : '❌ Confirm Reject' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </Transition>
    </AuthenticatedLayout>
</template>