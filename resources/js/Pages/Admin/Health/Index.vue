<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    applications: { type: Object, default: () => ({ data: [], meta: {} }) }
});

const list        = computed(() => props.applications?.data ?? []);
const total       = computed(() => props.applications?.meta?.total ?? list.value.length);
const lastPage    = computed(() => props.applications?.meta?.last_page ?? 1);
const currentPage = computed(() => props.applications?.meta?.current_page ?? 1);

const search       = ref('');
const statusFilter = ref('all');

const filtered = computed(() => {
    let data = list.value;
    const q = search.value.toLowerCase();
    if (q) data = data.filter(a =>
        (a.tracking_code ?? '').toLowerCase().includes(q) ||
        (a.user?.name ?? '').toLowerCase().includes(q) ||
        (a.application_type ?? '').toLowerCase().includes(q)
    );
    if (statusFilter.value !== 'all') data = data.filter(a => a.status === statusFilter.value);
    return data;
});

const statusCfg = {
    pending:     { label: 'Pending',     badge: 'bg-yellow-500/20 text-yellow-400 border-yellow-500/30' },
    under_review:{ label: 'Under Review',badge: 'bg-blue-500/20 text-blue-400 border-blue-500/30' },
    scheduled:   { label: 'Scheduled',   badge: 'bg-purple-500/20 text-purple-400 border-purple-500/30' },
    completed:   { label: 'Completed',   badge: 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30' },
    rejected:    { label: 'Rejected',    badge: 'bg-red-500/20 text-red-400 border-red-500/30' },
};
const getStatus = s => statusCfg[s] ?? { label: s, badge: 'bg-slate-500/20 text-slate-400 border-slate-500/30' };

const typeIcon = t => ({
    'Medical Assistance': '💊', 'Free Consultation': '🩺',
    'Medicine Request': '💉', 'Laboratory Request': '🔬', 'Mental Health Support': '🧠'
}[t] ?? '🏥');

const fmt = d => d ? new Date(d).toLocaleDateString('en-PH', { month: 'short', day: 'numeric', year: 'numeric' }) : '—';
const fmtTime = d => d ? new Date(d).toLocaleString('en-PH', { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' }) : '—';
const goToPage = url => { if (url) router.get(url, {}, { preserveScroll: true }); };
</script>

<template>
    <Head title="Health Applications" />
    <AuthenticatedLayout>
        <div class="min-h-screen bg-slate-950 text-white font-sans">
            <div class="fixed inset-0 pointer-events-none z-0">
                <div class="absolute inset-0 bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950"></div>
                <div class="absolute top-0 right-1/3 w-[500px] h-[400px] bg-emerald-500/6 rounded-full blur-[120px]"></div>
            </div>

            <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 py-8 space-y-6">

                <!-- Header -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <div class="flex items-center gap-2 mb-1 text-sm text-slate-500">
                            <Link :href="route('admin.dashboard')" class="hover:text-slate-300 transition-colors">← Dashboard</Link>
                            <span>/</span><span class="text-slate-400">Health</span>
                        </div>
                        <h1 class="text-2xl font-bold flex items-center gap-2">
                            🩺 Health Applications
                            <span class="text-sm px-2 py-0.5 bg-emerald-500/20 border border-emerald-500/30 text-emerald-400 rounded-full font-semibold">{{ total }} total</span>
                        </h1>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <button v-for="(cfg, key) in statusCfg" :key="key"
                            @click="statusFilter = key"
                            :class="['px-3 py-1.5 rounded-full text-xs font-bold border transition-all', statusFilter === key ? cfg.badge : 'bg-slate-800/50 text-slate-500 border-slate-700/50']">
                            {{ cfg.label }}
                        </button>
                        <button @click="statusFilter = 'all'"
                            :class="['px-3 py-1.5 rounded-full text-xs font-bold border transition-all', statusFilter === 'all' ? 'bg-white/10 text-white border-white/20' : 'bg-slate-800/50 text-slate-500 border-slate-700/50']">
                            All
                        </button>
                    </div>
                </div>

                <!-- Search -->
                <div class="relative">
                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input v-model="search" type="text" placeholder="Search patient, application type, tracking code..."
                        class="w-full pl-11 pr-4 py-3 bg-slate-900/60 backdrop-blur border border-slate-700/50 rounded-xl text-white placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/40 transition-all"/>
                </div>

                <!-- Empty -->
                <div v-if="filtered.length === 0" class="text-center py-20 bg-slate-900/40 backdrop-blur border border-slate-800/50 rounded-2xl">
                    <div class="text-6xl mb-4 opacity-20">🏥</div>
                    <p class="text-slate-400 font-semibold">No health applications found</p>
                </div>

                <!-- Table -->
                <div v-else class="bg-slate-900/60 backdrop-blur border border-slate-800/50 rounded-2xl overflow-hidden">
                    <div class="hidden lg:grid grid-cols-12 gap-4 px-6 py-3 border-b border-slate-800/50 bg-slate-900/80">
                        <div class="col-span-2 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Tracking #</div>
                        <div class="col-span-3 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Patient</div>
                        <div class="col-span-3 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Application Type</div>
                        <div class="col-span-2 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Appointment</div>
                        <div class="col-span-1 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Status</div>
                        <div class="col-span-1 text-[10px] font-bold text-slate-500 uppercase tracking-widest text-right">Action</div>
                    </div>

                    <div class="divide-y divide-slate-800/50">
                        <div v-for="app in filtered" :key="app.id" class="group hover:bg-white/[0.02] transition-colors">
                            <div class="hidden lg:grid grid-cols-12 gap-4 px-6 py-4 items-center">
                                <div class="col-span-2 font-mono text-xs text-emerald-400 font-bold">{{ app.tracking_code }}</div>
                                <div class="col-span-3 flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-sm font-bold flex-shrink-0">
                                        {{ app.user?.name?.charAt(0) ?? '?' }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-white truncate">{{ app.user?.name }}</p>
                                        <p class="text-xs text-slate-500 truncate">{{ app.user?.email }}</p>
                                    </div>
                                </div>
                                <div class="col-span-3 flex items-center gap-2">
                                    <span class="text-xl">{{ typeIcon(app.application_type) }}</span>
                                    <p class="text-sm text-slate-200">{{ app.application_type }}</p>
                                </div>
                                <div class="col-span-2">
                                    <p class="text-xs text-slate-400" v-if="app.scheduled_at">📅 {{ fmtTime(app.scheduled_at) }}</p>
                                    <p class="text-xs text-slate-600" v-else>Not scheduled</p>
                                    <p class="text-xs text-slate-500 mt-0.5" v-if="app.assigned_doctor">👨‍⚕️ {{ app.assigned_doctor }}</p>
                                </div>
                                <div class="col-span-1">
                                    <span :class="['inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold border', getStatus(app.status).badge]">
                                        {{ getStatus(app.status).label }}
                                    </span>
                                </div>
                                <div class="col-span-1 flex justify-end">
                                    <Link :href="route('admin.health.show', app.id)"
                                        class="px-3 py-1.5 bg-emerald-600/20 hover:bg-emerald-600/40 border border-emerald-500/30 rounded-lg text-emerald-400 text-xs font-bold transition-all opacity-0 group-hover:opacity-100">
                                        Review →
                                    </Link>
                                </div>
                            </div>
                            <!-- Mobile -->
                            <div class="lg:hidden p-4 flex items-start justify-between gap-3">
                                <div class="flex items-center gap-3 flex-1 min-w-0">
                                    <div class="text-2xl">{{ typeIcon(app.application_type) }}</div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-bold text-white">{{ app.user?.name }}</p>
                                        <p class="text-xs text-slate-400">{{ app.application_type }}</p>
                                        <p class="text-xs font-mono text-emerald-400 mt-0.5">{{ app.tracking_code }}</p>
                                    </div>
                                </div>
                                <div class="flex flex-col items-end gap-2">
                                    <span :class="['text-[10px] font-bold border px-2 py-1 rounded-full', getStatus(app.status).badge]">{{ getStatus(app.status).label }}</span>
                                    <Link :href="route('admin.health.show', app.id)" class="px-3 py-1.5 bg-emerald-600/20 border border-emerald-500/30 rounded-lg text-emerald-400 text-xs font-bold">Review →</Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="lastPage > 1" class="flex items-center justify-between">
                    <p class="text-xs text-slate-500">Page {{ currentPage }} of {{ lastPage }}</p>
                    <div class="flex gap-2">
                        <button @click="goToPage(applications?.links?.prev)" :disabled="!applications?.links?.prev"
                            class="px-4 py-2 bg-slate-800/60 border border-slate-700/50 rounded-xl text-sm text-slate-300 font-semibold disabled:opacity-30 hover:bg-slate-700/60 transition-colors">← Prev</button>
                        <button @click="goToPage(applications?.links?.next)" :disabled="!applications?.links?.next"
                            class="px-4 py-2 bg-slate-800/60 border border-slate-700/50 rounded-xl text-sm text-slate-300 font-semibold disabled:opacity-30 hover:bg-slate-700/60 transition-colors">Next →</button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>