<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    application: Object,
    user:        Object,
});

// Forms
const approveForm = useForm({
    approved_amount: '',
    release_date:    '',
    release_venue:   'Municipal Social Welfare Office, Pili Municipal Hall',
    admin_note:      '',
});

const rejectForm = useForm({
    rejection_reason: '',
});

const showApproveModal = ref(false);
const showRejectModal  = ref(false);

const submitApprove = () => {
    approveForm.post(route('admin.social-aid.approve', props.application.id), {
        preserveScroll: true,
        onSuccess: () => { showApproveModal.value = false; },
    });
};

const submitReject = () => {
    rejectForm.post(route('admin.social-aid.reject', props.application.id), {
        preserveScroll: true,
        onSuccess: () => { showRejectModal.value = false; },
    });
};

const releaseForm = useForm({});
const submitRelease = () => {
    if (!confirm('Mark this assistance as physically released to the applicant?')) return;
    releaseForm.post(route('admin.social-aid.release', props.application.id), { preserveScroll: true });
};

// Helpers
const statusCfg = {
    pending:      { label: 'Pending',      badge: 'bg-yellow-500/20 text-yellow-400 border-yellow-500/30', dot: 'bg-yellow-400' },
    under_review: { label: 'Under Review', badge: 'bg-blue-500/20 text-blue-400 border-blue-500/30',       dot: 'bg-blue-400' },
    approved:     { label: 'Approved',     badge: 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30', dot: 'bg-emerald-400' },
    rejected:     { label: 'Rejected',     badge: 'bg-red-500/20 text-red-400 border-red-500/30',           dot: 'bg-red-400' },
    released:     { label: 'Released 🎉',  badge: 'bg-cyan-500/20 text-cyan-400 border-cyan-500/30',        dot: 'bg-cyan-400' },
};
const getStatus = (s) => statusCfg[s] ?? { label: s, badge: 'bg-slate-500/20 text-slate-400 border-slate-500/30', dot: 'bg-slate-400' };
const fmt = (d) => d ? new Date(d).toLocaleDateString('en-PH', { month: 'long', day: 'numeric', year: 'numeric' }) : '—';
const fmtKey = (k) => k.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());

const status       = computed(() => props.application?.status);
const canApprove   = computed(() => ['pending', 'under_review'].includes(status.value));
const canReject    = computed(() => ['pending', 'under_review'].includes(status.value));
const canRelease   = computed(() => status.value === 'approved');

// Min date for release (tomorrow)
const minReleaseDate = computed(() => {
    const d = new Date(); d.setDate(d.getDate() + 1);
    return d.toISOString().split('T')[0];
});

const workflowSteps = [
    { key: 'pending',      label: 'Submitted',    icon: '📝' },
    { key: 'under_review', label: 'Under Review', icon: '🔍' },
    { key: 'approved',     label: 'Approved',     icon: '✅' },
    { key: 'released',     label: 'Released',     icon: '🎉' },
];
const stepOrder = { pending: 0, under_review: 1, approved: 2, released: 3, rejected: -1 };
const currentStep = computed(() => stepOrder[status.value] ?? 0);
</script>

<template>
    <Head :title="`Social Aid — ${application?.tracking_code}`" />
    <AuthenticatedLayout>
        <div class="min-h-screen bg-slate-900 text-white font-sans p-6 lg:p-12">
            <div class="absolute inset-0 opacity-5 pointer-events-none" style="background-image: radial-gradient(#64748b 1px, transparent 1px); background-size: 32px 32px;"></div>

            <!-- Back + Header -->
            <div class="max-w-6xl mx-auto mb-8 relative z-10">
                <Link :href="route('admin.social-aid.index')" class="flex items-center gap-2 text-slate-400 hover:text-white text-sm font-bold uppercase tracking-wider group mb-4">
                    <span class="group-hover:-translate-x-1 transition-transform">←</span> Back to Social Aid
                </Link>
                <div class="flex flex-col md:flex-row justify-between items-start gap-4">
                    <div>
                        <h1 class="text-2xl font-bold flex items-center gap-3">
                            🤝 Social Aid Application
                            <span class="font-mono text-slate-500 text-lg">#{{ application.tracking_code }}</span>
                        </h1>
                        <p class="text-rose-400 text-xs font-mono uppercase mt-1 tracking-widest">{{ application.program_type }}</p>
                    </div>
                    <span :class="['px-4 py-2 rounded-xl border text-sm font-bold flex items-center gap-2', getStatus(status).badge]">
                        <span :class="[getStatus(status).dot, 'w-2 h-2 rounded-full animate-pulse']"></span>
                        {{ getStatus(status).label }}
                    </span>
                </div>
            </div>

            <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-8 relative z-10">

                <!-- LEFT: Applicant Info -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Profile card -->
                    <div class="bg-slate-800/50 backdrop-blur border border-white/10 rounded-2xl p-6">
                        <h3 class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-4 pb-2 border-b border-white/5">Applicant</h3>
                        <div class="flex flex-col items-center text-center mb-4">
                            <div class="w-16 h-16 rounded-full bg-gradient-to-br from-rose-500 to-pink-600 flex items-center justify-center text-2xl font-bold mb-3 border-4 border-slate-800 shadow-xl">
                                {{ user.name?.charAt(0) ?? '?' }}
                            </div>
                            <h2 class="text-lg font-bold">{{ user.name }}</h2>
                            <p class="text-sm text-slate-400">{{ user.email }}</p>
                        </div>
                        <div class="space-y-3">
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

                    <!-- Workflow Progress -->
                    <div class="bg-slate-800/50 backdrop-blur border border-white/10 rounded-2xl p-6">
                        <h3 class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-5 pb-2 border-b border-white/5">Progress</h3>
                        <div class="space-y-0">
                            <div v-for="(step, i) in workflowSteps" :key="step.key" class="flex items-start gap-3">
                                <div class="flex flex-col items-center">
                                    <div :class="[
                                        'w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold transition-all',
                                        status === 'rejected' ? 'bg-red-500/20 text-red-400 border border-red-500/30' :
                                        i <= currentStep ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' :
                                        'bg-slate-800 text-slate-600 border border-slate-700'
                                    ]">{{ i <= currentStep && status !== 'rejected' ? '✓' : step.icon }}</div>
                                    <div v-if="i < workflowSteps.length - 1" :class="['w-0.5 h-8 my-1', i < currentStep ? 'bg-emerald-500/40' : 'bg-slate-700']"></div>
                                </div>
                                <div class="pt-1 pb-6">
                                    <p :class="['text-sm font-semibold', i <= currentStep && status !== 'rejected' ? 'text-white' : 'text-slate-500']">{{ step.label }}</p>
                                </div>
                            </div>
                            <!-- Rejected indicator -->
                            <div v-if="status === 'rejected'" class="flex items-center gap-3 mt-2">
                                <div class="w-8 h-8 rounded-full bg-red-500/20 text-red-400 border border-red-500/30 flex items-center justify-center text-sm font-bold">✕</div>
                                <p class="text-sm font-semibold text-red-400">Rejected</p>
                            </div>
                        </div>
                    </div>

                    <!-- Release Info (if approved) -->
                    <div v-if="status === 'approved' || status === 'released'" class="bg-emerald-950/40 border border-emerald-500/30 rounded-2xl p-5">
                        <h3 class="text-emerald-400 text-xs font-bold uppercase tracking-widest mb-4">Release Schedule</h3>
                        <div class="space-y-3">
                            <div>
                                <label class="text-[10px] text-slate-500 uppercase font-bold block">Amount</label>
                                <p class="text-xl font-bold text-emerald-400">₱{{ Number(application.approved_amount).toLocaleString('en-PH') }}</p>
                            </div>
                            <div>
                                <label class="text-[10px] text-slate-500 uppercase font-bold block">Release Date</label>
                                <p class="text-sm text-white font-semibold">📅 {{ fmt(application.release_date) }}</p>
                            </div>
                            <div>
                                <label class="text-[10px] text-slate-500 uppercase font-bold block">Venue</label>
                                <p class="text-sm text-white">📍 {{ application.release_venue }}</p>
                            </div>
                        </div>
                        <button v-if="canRelease" @click="submitRelease" :disabled="releaseForm.processing"
                            class="w-full mt-4 py-2.5 bg-cyan-600/20 hover:bg-cyan-600/40 border border-cyan-500/30 rounded-xl text-cyan-400 text-sm font-bold transition-all disabled:opacity-50">
                            ✅ Mark as Released
                        </button>
                    </div>
                </div>

                <!-- RIGHT: Application Data + Actions -->
                <div class="lg:col-span-2 space-y-6">

                    <!-- Application Details -->
                    <div class="bg-slate-800/50 backdrop-blur border border-white/10 rounded-2xl p-8">
                        <div class="flex justify-between items-start border-b border-white/5 pb-4 mb-6">
                            <div>
                                <h3 class="text-slate-400 text-xs uppercase tracking-widest font-bold">Application</h3>
                                <p class="text-2xl font-bold text-white mt-1">{{ application.program_title }}</p>
                            </div>
                            <span class="text-3xl">{{ { 'Student Assistance':'🎓', 'Senior Citizen':'👴', 'PWD Assistance':'♿', 'Solo Parent':'👩‍👧', 'Calamity Aid':'🆘' }[application.program_type] ?? '🤝' }}</span>
                        </div>

                        <!-- Form data -->
                        <div class="bg-black/20 rounded-xl p-6 border border-white/5">
                            <label class="text-[10px] text-slate-500 uppercase font-bold block mb-4">Submitted Information</label>
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
                                <a v-for="(att, i) in application.attachments" :key="i"
                                    :href="`/storage/${att}`" target="_blank"
                                    class="flex items-center gap-2 px-3 py-2 bg-blue-600/10 border border-blue-500/20 rounded-lg text-blue-300 text-xs hover:bg-blue-600/20 transition-all">
                                    📎 Attachment {{ i + 1 }}
                                </a>
                            </div>
                        </div>

                        <!-- Rejection reason -->
                        <div v-if="application.rejection_reason" class="mt-4 pt-4 border-t border-white/5 bg-red-950/30 rounded-xl p-4">
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
                                <div class="w-2 h-2 rounded-full bg-rose-400 mt-1.5 flex-shrink-0"></div>
                                <div>
                                    <p class="text-sm font-semibold text-white capitalize">{{ entry.status?.replace(/_/g, ' ') }}</p>
                                    <p class="text-xs text-slate-400">{{ entry.admin_name }} · {{ new Date(entry.timestamp).toLocaleString('en-PH') }}</p>
                                    <p v-if="entry.note" class="text-xs text-slate-500 italic mt-1">"{{ entry.note }}"</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Console -->
                    <div v-if="canApprove || canReject" class="bg-slate-800/80 backdrop-blur border border-white/10 rounded-2xl p-6">
                        <h3 class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-4 flex items-center gap-2">
                            <span class="w-2 h-2 bg-rose-500 rounded-full animate-pulse"></span>
                            Action Console
                        </h3>
                        <div class="flex flex-col sm:flex-row gap-3">
                            <button @click="showApproveModal = true"
                                class="flex-1 py-3 bg-emerald-600/20 hover:bg-emerald-600/40 border border-emerald-500/30 rounded-xl text-emerald-400 font-bold text-sm transition-all hover:scale-[1.02] active:scale-[0.98]">
                                ✅ Approve Application
                            </button>
                            <button @click="showRejectModal = true"
                                class="flex-1 py-3 bg-red-600/20 hover:bg-red-600/40 border border-red-500/30 rounded-xl text-red-400 font-bold text-sm transition-all hover:scale-[1.02] active:scale-[0.98]">
                                ❌ Reject Application
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- ── APPROVE MODAL ────────────────────────────────────── -->
        <Transition enter-active-class="transition duration-200" enter-from-class="opacity-0" enter-to-class="opacity-100">
            <div v-if="showApproveModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4">
                <div class="bg-slate-900 border border-emerald-500/30 rounded-2xl max-w-md w-full p-6 shadow-2xl">
                    <h3 class="text-lg font-bold text-emerald-400 mb-5">✅ Approve Application</h3>
                    <form @submit.prevent="submitApprove" class="space-y-4">
                        <div>
                            <label class="text-xs font-bold text-slate-400 uppercase block mb-2">Approved Amount (₱)</label>
                            <input type="number" v-model="approveForm.approved_amount" required min="0"
                                class="w-full bg-black/40 border border-white/20 rounded-xl px-4 py-3 text-white text-sm focus:ring-2 focus:ring-emerald-500"
                                placeholder="e.g. 10000" />
                            <p v-if="approveForm.errors.approved_amount" class="text-red-400 text-xs mt-1">{{ approveForm.errors.approved_amount }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-400 uppercase block mb-2">📅 Release Date</label>
                            <input type="date" v-model="approveForm.release_date" :min="minReleaseDate" required
                                class="w-full bg-black/40 border border-white/20 rounded-xl px-4 py-3 text-white text-sm focus:ring-2 focus:ring-emerald-500" />
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-400 uppercase block mb-2">📍 Release Venue</label>
                            <input type="text" v-model="approveForm.release_venue" required
                                class="w-full bg-black/40 border border-white/20 rounded-xl px-4 py-3 text-white text-sm focus:ring-2 focus:ring-emerald-500" />
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-400 uppercase block mb-2">Notes for Applicant (optional)</label>
                            <textarea v-model="approveForm.admin_note" rows="3"
                                class="w-full bg-black/40 border border-white/20 rounded-xl px-4 py-3 text-white text-sm focus:ring-2 focus:ring-emerald-500 resize-none"
                                placeholder="e.g. Bring valid ID and original documents..."></textarea>
                        </div>
                        <div class="flex gap-3 pt-2">
                            <button type="button" @click="showApproveModal = false" class="flex-1 py-2.5 bg-slate-700 rounded-xl text-slate-300 text-sm font-bold hover:bg-slate-600 transition-colors">Cancel</button>
                            <button type="submit" :disabled="approveForm.processing"
                                class="flex-1 py-2.5 bg-emerald-600 hover:bg-emerald-500 rounded-xl text-white text-sm font-bold disabled:opacity-50 transition-colors">
                                {{ approveForm.processing ? 'Approving...' : '✅ Confirm Approve' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </Transition>

        <!-- ── REJECT MODAL ─────────────────────────────────────── -->
        <Transition enter-active-class="transition duration-200" enter-from-class="opacity-0" enter-to-class="opacity-100">
            <div v-if="showRejectModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4">
                <div class="bg-slate-900 border border-red-500/30 rounded-2xl max-w-md w-full p-6 shadow-2xl">
                    <h3 class="text-lg font-bold text-red-400 mb-5">❌ Reject Application</h3>
                    <form @submit.prevent="submitReject" class="space-y-4">
                        <div>
                            <label class="text-xs font-bold text-slate-400 uppercase block mb-2">Rejection Reason <span class="text-red-400">*</span></label>
                            <textarea v-model="rejectForm.rejection_reason" rows="4" required
                                class="w-full bg-black/40 border border-red-500/20 rounded-xl px-4 py-3 text-white text-sm focus:ring-2 focus:ring-red-500 resize-none"
                                placeholder="Explain why this application cannot be approved..."></textarea>
                            <p v-if="rejectForm.errors.rejection_reason" class="text-red-400 text-xs mt-1">{{ rejectForm.errors.rejection_reason }}</p>
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