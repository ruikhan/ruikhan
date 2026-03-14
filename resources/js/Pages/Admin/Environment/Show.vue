<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({ report: Object, user: Object });

const actionForm = useForm({
    status:          '',
    admin_note:      '',
    action_taken:    '',
    resolution_date: '',
});
const showActionModal = ref(false);
const selectedAction  = ref('');

const openAction = (newStatus) => {
    selectedAction.value  = newStatus;
    actionForm.status     = newStatus;
    actionForm.admin_note = '';
    actionForm.action_taken = '';
    actionForm.resolution_date = '';
    showActionModal.value = true;
};

const submitAction = () => {
    actionForm.post(route('admin.environment.update-status', props.report.id), {
        preserveScroll: true,
        onSuccess: () => { showActionModal.value = false; },
    });
};

const status = computed(() => props.report?.status);

const statusCfg = {
    pending:      { label: 'Pending',       badge: 'bg-yellow-500/20 text-yellow-400 border-yellow-500/30', dot: 'bg-yellow-400' },
    acknowledged: { label: 'Acknowledged',  badge: 'bg-blue-500/20 text-blue-400 border-blue-500/30',       dot: 'bg-blue-400' },
    investigating:{ label: 'Investigating', badge: 'bg-purple-500/20 text-purple-400 border-purple-500/30', dot: 'bg-purple-400' },
    resolved:     { label: 'Resolved',      badge: 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30', dot: 'bg-emerald-400' },
    dismissed:    { label: 'Dismissed',     badge: 'bg-slate-500/20 text-slate-400 border-slate-500/30',    dot: 'bg-slate-400' },
};
const getStatus = s => statusCfg[s] ?? { label: s, badge: 'bg-slate-500/20 text-slate-400 border-slate-500/30', dot: 'bg-slate-400' };

const severityColor = {
    low: 'text-emerald-400', moderate: 'text-amber-400', high: 'text-orange-400', critical: 'text-red-400'
};

const fmtKey = k => k.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
const fmt    = d => d ? new Date(d).toLocaleDateString('en-PH', { month: 'long', day: 'numeric', year: 'numeric' }) : '—';

const typeIcon = t => ({
    'Illegal Dumping':'🗑','Air Pollution':'🌫','Water Contamination':'💧',
    'Flood Hazard':'🌊','Deforestation':'🌲','Noise Pollution':'📢','Other':'⚠️'
}[t] ?? '🌿');

const workflowSteps = [
    { key: 'pending',       label: 'Reported',      icon: '📝' },
    { key: 'acknowledged',  label: 'Acknowledged',  icon: '👁'  },
    { key: 'investigating', label: 'Investigating', icon: '🔍' },
    { key: 'resolved',      label: 'Resolved',      icon: '✅' },
];
const stepOrder   = { pending: 0, acknowledged: 1, investigating: 2, resolved: 3, dismissed: -1 };
const currentStep = computed(() => stepOrder[status.value] ?? 0);

const availableActions = computed(() => {
    const actions = {
        pending:      [{ status: 'acknowledged',  label: '👁 Acknowledge',   color: 'blue' }],
        acknowledged: [{ status: 'investigating', label: '🔍 Start Investigation', color: 'purple' },
                       { status: 'dismissed',     label: '🚫 Dismiss',        color: 'slate' }],
        investigating:[{ status: 'resolved',      label: '✅ Mark Resolved',   color: 'emerald' },
                       { status: 'dismissed',     label: '🚫 Dismiss',        color: 'slate' }],
        resolved:     [],
        dismissed:    [],
    };
    return actions[status.value] ?? [];
});

const actionColor = {
    blue:    'bg-blue-600/20 hover:bg-blue-600/40 border-blue-500/30 text-blue-400',
    purple:  'bg-purple-600/20 hover:bg-purple-600/40 border-purple-500/30 text-purple-400',
    emerald: 'bg-emerald-600/20 hover:bg-emerald-600/40 border-emerald-500/30 text-emerald-400',
    slate:   'bg-slate-700/40 hover:bg-slate-700/60 border-slate-600/30 text-slate-400',
};

const needsActionTaken   = computed(() => ['resolved'].includes(actionForm.status));
const needsResolutionDate = computed(() => actionForm.status === 'resolved');
const modalTitle = computed(() => ({
    acknowledged:  '👁 Acknowledge Report',
    investigating: '🔍 Start Investigation',
    resolved:      '✅ Mark as Resolved',
    dismissed:     '🚫 Dismiss Report',
}[selectedAction.value] ?? 'Update Status'));
</script>

<template>
    <Head :title="`Environment — ${report?.tracking_code}`" />
    <AuthenticatedLayout>
        <div class="min-h-screen bg-slate-900 text-white font-sans p-6 lg:p-12">
            <div class="absolute inset-0 opacity-5 pointer-events-none" style="background-image: radial-gradient(#64748b 1px, transparent 1px); background-size: 32px 32px;"></div>

            <!-- Header -->
            <div class="max-w-6xl mx-auto mb-8 relative z-10">
                <Link :href="route('admin.environment.index')" class="flex items-center gap-2 text-slate-400 hover:text-white text-sm font-bold uppercase tracking-wider group mb-4">
                    <span class="group-hover:-translate-x-1 transition-transform">←</span> Back to Environment
                </Link>
                <div class="flex flex-col md:flex-row justify-between items-start gap-4">
                    <div>
                        <h1 class="text-2xl font-bold flex items-center gap-3">
                            {{ typeIcon(report.report_type) }} {{ report.report_type }}
                            <span class="font-mono text-slate-500 text-lg">#{{ report.tracking_code }}</span>
                        </h1>
                        <p :class="['text-sm font-bold uppercase mt-1', severityColor[report.severity] ?? 'text-slate-400']">
                            ⚠ {{ report.severity?.toUpperCase() }} SEVERITY
                        </p>
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
                    <!-- Reporter card -->
                    <div class="bg-slate-800/50 backdrop-blur border border-white/10 rounded-2xl p-6">
                        <h3 class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-4 pb-2 border-b border-white/5">Reporter</h3>
                        <div class="flex flex-col items-center text-center mb-4">
                            <div class="w-16 h-16 rounded-full bg-gradient-to-br from-green-500 to-teal-600 flex items-center justify-center text-2xl font-bold mb-3 border-4 border-slate-800 shadow-xl">
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
                        </div>
                    </div>

                    <!-- Workflow Progress -->
                    <div class="bg-slate-800/50 backdrop-blur border border-white/10 rounded-2xl p-6">
                        <h3 class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-5 pb-2 border-b border-white/5">Progress</h3>
                        <div class="space-y-0">
                            <div v-for="(step, i) in workflowSteps" :key="step.key" class="flex items-start gap-3">
                                <div class="flex flex-col items-center">
                                    <div :class="['w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold transition-all',
                                        status === 'dismissed' ? 'bg-slate-700 text-slate-500 border border-slate-600' :
                                        i <= currentStep ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' :
                                        'bg-slate-800 text-slate-600 border border-slate-700']">
                                        {{ i <= currentStep && status !== 'dismissed' ? '✓' : step.icon }}
                                    </div>
                                    <div v-if="i < workflowSteps.length - 1" :class="['w-0.5 h-8 my-1', i < currentStep ? 'bg-emerald-500/40' : 'bg-slate-700']"></div>
                                </div>
                                <div class="pt-1 pb-6">
                                    <p :class="['text-sm font-semibold', i <= currentStep && status !== 'dismissed' ? 'text-white' : 'text-slate-500']">{{ step.label }}</p>
                                </div>
                            </div>
                            <div v-if="status === 'dismissed'" class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-slate-700 text-slate-400 border border-slate-600 flex items-center justify-center text-sm font-bold">🚫</div>
                                <p class="text-sm font-semibold text-slate-400">Dismissed</p>
                            </div>
                        </div>
                    </div>

                    <!-- Resolution Info -->
                    <div v-if="status === 'resolved'" class="bg-emerald-950/30 border border-emerald-500/30 rounded-2xl p-5">
                        <h3 class="text-emerald-400 text-xs font-bold uppercase tracking-widest mb-3">Resolution</h3>
                        <div v-if="report.action_taken" class="mb-3">
                            <label class="text-[10px] text-slate-500 uppercase font-bold block">Action Taken</label>
                            <p class="text-sm text-slate-200 mt-1">{{ report.action_taken }}</p>
                        </div>
                        <div v-if="report.resolution_date">
                            <label class="text-[10px] text-slate-500 uppercase font-bold block">Resolution Date</label>
                            <p class="text-sm text-white font-semibold">{{ fmt(report.resolution_date) }}</p>
                        </div>
                    </div>
                </div>

                <!-- RIGHT -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Report Details -->
                    <div class="bg-slate-800/50 backdrop-blur border border-white/10 rounded-2xl p-8">
                        <div class="flex justify-between items-start border-b border-white/5 pb-4 mb-6">
                            <div>
                                <h3 class="text-slate-400 text-xs uppercase tracking-widest font-bold">Environmental Report</h3>
                                <p class="text-2xl font-bold text-white mt-1">{{ report.report_type }}</p>
                                <div class="flex items-center gap-3 mt-2">
                                    <span class="text-xs text-slate-400">📍 {{ report.location }}</span>
                                    <span :class="['text-[10px] font-bold uppercase px-2 py-0.5 rounded-full border', {
                                        low:      'bg-emerald-500/20 text-emerald-400 border-emerald-500/30',
                                        moderate: 'bg-amber-500/20 text-amber-400 border-amber-500/30',
                                        high:     'bg-orange-500/20 text-orange-400 border-orange-500/30',
                                        critical: 'bg-red-500/20 text-red-400 border-red-500/30',
                                    }[report.severity]]">{{ report.severity }}</span>
                                </div>
                            </div>
                            <span class="text-4xl">{{ typeIcon(report.report_type) }}</span>
                        </div>

                        <div class="bg-black/20 rounded-xl p-6 border border-white/5">
                            <label class="text-[10px] text-slate-500 uppercase font-bold block mb-4">Report Details</label>
                            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-5">
                                <div v-for="(value, key) in report.report_data" :key="key" class="border-l-2 border-slate-700 pl-3">
                                    <dt class="text-[10px] uppercase tracking-wider text-slate-500 font-bold mb-1">{{ fmtKey(String(key)) }}</dt>
                                    <dd class="text-sm font-medium text-slate-200 break-words">{{ value || '—' }}</dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Photo Evidence -->
                        <div v-if="report.attachments?.length" class="mt-4 pt-4 border-t border-white/5">
                            <label class="text-[10px] text-slate-500 uppercase font-bold block mb-3">Photo Evidence</label>
                            <div class="grid grid-cols-3 gap-3">
                                <a v-for="(att, i) in report.attachments" :key="i" :href="`/storage/${att}`" target="_blank"
                                    class="aspect-square bg-slate-800 border border-white/10 rounded-xl overflow-hidden hover:border-green-500/40 transition-colors flex items-center justify-center text-slate-500 text-sm">
                                    📷 Photo {{ i + 1 }}
                                </a>
                            </div>
                        </div>

                        <!-- Admin note -->
                        <div v-if="report.admin_note" class="mt-4 pt-4 border-t border-white/5">
                            <label class="text-[10px] text-slate-500 uppercase font-bold block mb-2">Admin Notes</label>
                            <p class="text-sm text-slate-300">{{ report.admin_note }}</p>
                        </div>
                    </div>

                    <!-- Workflow History -->
                    <div v-if="report.workflow_history?.length" class="bg-slate-800/50 backdrop-blur border border-white/10 rounded-2xl p-6">
                        <h3 class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-4">Activity Log</h3>
                        <div class="space-y-3">
                            <div v-for="(entry, i) in [...report.workflow_history].reverse()" :key="i"
                                class="flex items-start gap-3 pb-3 border-b border-slate-800/50 last:border-0">
                                <div class="w-2 h-2 rounded-full bg-green-400 mt-1.5 flex-shrink-0"></div>
                                <div>
                                    <p class="text-sm font-semibold text-white capitalize">{{ entry.status?.replace(/_/g, ' ') }}</p>
                                    <p class="text-xs text-slate-400">{{ entry.admin_name }} · {{ new Date(entry.timestamp).toLocaleString('en-PH') }}</p>
                                    <p v-if="entry.note" class="text-xs text-slate-500 italic mt-1">"{{ entry.note }}"</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Console -->
                    <div v-if="availableActions.length" class="bg-slate-800/80 backdrop-blur border border-white/10 rounded-2xl p-6">
                        <h3 class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-4 flex items-center gap-2">
                            <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                            Action Console
                        </h3>
                        <div class="flex flex-wrap gap-3">
                            <button v-for="action in availableActions" :key="action.status"
                                @click="openAction(action.status)"
                                :class="['py-3 px-6 border rounded-xl font-bold text-sm transition-all hover:scale-[1.02] active:scale-[0.98]', actionColor[action.color]]">
                                {{ action.label }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Modal -->
        <Transition enter-active-class="transition duration-200" enter-from-class="opacity-0">
            <div v-if="showActionModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4">
                <div class="bg-slate-900 border border-white/10 rounded-2xl max-w-md w-full p-6 shadow-2xl">
                    <h3 class="text-lg font-bold text-white mb-5">{{ modalTitle }}</h3>
                    <form @submit.prevent="submitAction" class="space-y-4">

                        <div v-if="needsActionTaken">
                            <label class="text-xs font-bold text-slate-400 uppercase block mb-2">Action Taken <span class="text-red-400">*</span></label>
                            <textarea v-model="actionForm.action_taken" rows="3" required
                                class="w-full bg-black/40 border border-white/20 rounded-xl px-4 py-3 text-white text-sm focus:ring-2 focus:ring-green-500 resize-none"
                                placeholder="Describe what was done to resolve the issue..."></textarea>
                        </div>

                        <div v-if="needsResolutionDate">
                            <label class="text-xs font-bold text-slate-400 uppercase block mb-2">Resolution Date</label>
                            <input type="date" v-model="actionForm.resolution_date"
                                class="w-full bg-black/40 border border-white/20 rounded-xl px-4 py-3 text-white text-sm focus:ring-2 focus:ring-green-500"/>
                        </div>

                        <div>
                            <label class="text-xs font-bold text-slate-400 uppercase block mb-2">Note to Reporter</label>
                            <textarea v-model="actionForm.admin_note" rows="3"
                                class="w-full bg-black/40 border border-white/20 rounded-xl px-4 py-3 text-white text-sm focus:ring-2 focus:ring-green-500 resize-none"
                                :placeholder="selectedAction === 'acknowledged' ? 'Your report has been received...' :
                                              selectedAction === 'investigating' ? 'Our team is now investigating...' :
                                              selectedAction === 'resolved' ? 'The issue has been addressed...' :
                                              'The report was reviewed but...'"></textarea>
                        </div>

                        <div class="flex gap-3 pt-2">
                            <button type="button" @click="showActionModal = false" class="flex-1 py-2.5 bg-slate-700 rounded-xl text-slate-300 text-sm font-bold hover:bg-slate-600 transition-colors">Cancel</button>
                            <button type="submit" :disabled="actionForm.processing"
                                class="flex-1 py-2.5 bg-green-600 hover:bg-green-500 rounded-xl text-white text-sm font-bold disabled:opacity-50 transition-colors">
                                {{ actionForm.processing ? 'Saving...' : 'Confirm' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </Transition>
    </AuthenticatedLayout>
</template>