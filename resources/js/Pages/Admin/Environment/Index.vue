<script setup>
// ════════ Admin/Environment/Index.vue ════════
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    reports: { type: Object, default: () => ({ data: [], meta: {} }) }
});

const list        = computed(() => props.reports?.data ?? []);
const total       = computed(() => props.reports?.meta?.total ?? list.value.length);
const lastPage    = computed(() => props.reports?.meta?.last_page ?? 1);
const currentPage = computed(() => props.reports?.meta?.current_page ?? 1);

const search       = ref('');
const statusFilter = ref('all');

const filtered = computed(() => {
    let data = list.value;
    const q = search.value.toLowerCase();
    if (q) data = data.filter(r =>
        (r.tracking_code ?? '').toLowerCase().includes(q) ||
        (r.user?.name ?? '').toLowerCase().includes(q) ||
        (r.report_type ?? '').toLowerCase().includes(q) ||
        (r.location ?? '').toLowerCase().includes(q)
    );
    if (statusFilter.value !== 'all') data = data.filter(r => r.status === statusFilter.value);
    return data;
});

const statusCfg = {
    pending:      { label: 'Pending',       badge: 'bg-yellow-500/20 text-yellow-400 border-yellow-500/30' },
    acknowledged: { label: 'Acknowledged',  badge: 'bg-blue-500/20 text-blue-400 border-blue-500/30' },
    investigating:{ label: 'Investigating', badge: 'bg-purple-500/20 text-purple-400 border-purple-500/30' },
    resolved:     { label: 'Resolved',      badge: 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30' },
    dismissed:    { label: 'Dismissed',     badge: 'bg-slate-500/20 text-slate-400 border-slate-500/30' },
};
const getStatus = s => statusCfg[s] ?? { label: s, badge: 'bg-slate-500/20 text-slate-400 border-slate-500/30' };

const severityCfg = {
    low:      'bg-emerald-500/20 text-emerald-400 border-emerald-500/30',
    moderate: 'bg-amber-500/20 text-amber-400 border-amber-500/30',
    high:     'bg-orange-500/20 text-orange-400 border-orange-500/30',
    critical: 'bg-red-500/20 text-red-400 border-red-500/30',
};

const typeIcon = t => ({
    'Illegal Dumping':'🗑','Air Pollution':'🌫','Water Contamination':'💧',
    'Flood Hazard':'🌊','Deforestation':'🌲','Noise Pollution':'📢','Other':'⚠️'
}[t] ?? '🌿');

const fmt = d => d ? new Date(d).toLocaleDateString('en-PH', { month: 'short', day: 'numeric', year: 'numeric' }) : '—';
const goToPage = url => { if (url) router.get(url, {}, { preserveScroll: true }); };
</script>

<template>
    <Head title="Environment Reports" />
    <AuthenticatedLayout>
        <div class="min-h-screen bg-slate-950 text-white font-sans">
            <div class="fixed inset-0 pointer-events-none z-0">
                <div class="absolute inset-0 bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950"></div>
                <div class="absolute bottom-0 left-1/4 w-[500px] h-[400px] bg-green-500/6 rounded-full blur-[120px]"></div>
            </div>

            <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 py-8 space-y-6">

                <!-- Header -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <div class="flex items-center gap-2 mb-1 text-sm text-slate-500">
                            <Link :href="route('admin.dashboard')" class="hover:text-slate-300 transition-colors">← Dashboard</Link>
                            <span>/</span><span class="text-slate-400">Environment</span>
                        </div>
                        <h1 class="text-2xl font-bold flex items-center gap-2">
                            🌿 Environment Reports
                            <span class="text-sm px-2 py-0.5 bg-green-500/20 border border-green-500/30 text-green-400 rounded-full font-semibold">{{ total }} total</span>
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
                    <input v-model="search" type="text" placeholder="Search reporter, type, location, tracking code..."
                        class="w-full pl-11 pr-4 py-3 bg-slate-900/60 backdrop-blur border border-slate-700/50 rounded-xl text-white placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-green-500/40 transition-all"/>
                </div>

                <!-- Empty -->
                <div v-if="filtered.length === 0" class="text-center py-20 bg-slate-900/40 backdrop-blur border border-slate-800/50 rounded-2xl">
                    <div class="text-6xl mb-4 opacity-20">🌿</div>
                    <p class="text-slate-400 font-semibold">No environment reports found</p>
                </div>

                <!-- Table -->
                <div v-else class="bg-slate-900/60 backdrop-blur border border-slate-800/50 rounded-2xl overflow-hidden">
                    <div class="hidden lg:grid grid-cols-12 gap-4 px-6 py-3 border-b border-slate-800/50 bg-slate-900/80">
                        <div class="col-span-2 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Tracking #</div>
                        <div class="col-span-2 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Reporter</div>
                        <div class="col-span-3 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Report Type</div>
                        <div class="col-span-2 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Location</div>
                        <div class="col-span-1 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Severity</div>
                        <div class="col-span-1 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Status</div>
                        <div class="col-span-1 text-[10px] font-bold text-slate-500 uppercase tracking-widest text-right">Action</div>
                    </div>

                    <div class="divide-y divide-slate-800/50">
                        <div v-for="rep in filtered" :key="rep.id" class="group hover:bg-white/[0.02] transition-colors">
                            <div class="hidden lg:grid grid-cols-12 gap-4 px-6 py-4 items-center">
                                <div class="col-span-2 font-mono text-xs text-green-400 font-bold">{{ rep.tracking_code }}</div>
                                <div class="col-span-2">
                                    <p class="text-sm font-semibold text-white truncate">{{ rep.user?.name }}</p>
                                    <p class="text-xs text-slate-500 truncate">{{ rep.user?.email }}</p>
                                </div>
                                <div class="col-span-3 flex items-center gap-2">
                                    <span class="text-xl">{{ typeIcon(rep.report_type) }}</span>
                                    <p class="text-sm text-slate-200">{{ rep.report_type }}</p>
                                </div>
                                <div class="col-span-2 text-xs text-slate-400 truncate">📍 {{ rep.location }}</div>
                                <div class="col-span-1">
                                    <span :class="['text-[10px] font-bold border px-2 py-0.5 rounded-full capitalize', severityCfg[rep.severity] ?? 'bg-slate-500/20 text-slate-400 border-slate-500/30']">{{ rep.severity }}</span>
                                </div>
                                <div class="col-span-1">
                                    <span :class="['text-[10px] font-bold border px-2 py-0.5 rounded-full', getStatus(rep.status).badge]">{{ getStatus(rep.status).label }}</span>
                                </div>
                                <div class="col-span-1 flex justify-end">
                                    <Link :href="route('admin.environment.show', rep.id)"
                                        class="px-3 py-1.5 bg-green-600/20 hover:bg-green-600/40 border border-green-500/30 rounded-lg text-green-400 text-xs font-bold transition-all opacity-0 group-hover:opacity-100">
                                        View →
                                    </Link>
                                </div>
                            </div>
                            <!-- Mobile -->
                            <div class="lg:hidden p-4 flex items-start justify-between gap-3">
                                <div class="flex items-center gap-3 flex-1 min-w-0">
                                    <div class="text-2xl">{{ typeIcon(rep.report_type) }}</div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-bold text-white">{{ rep.user?.name }}</p>
                                        <p class="text-xs text-slate-400">{{ rep.report_type }} · {{ rep.location }}</p>
                                        <p class="text-xs font-mono text-green-400 mt-0.5">{{ rep.tracking_code }}</p>
                                    </div>
                                </div>
                                <div class="flex flex-col items-end gap-2">
                                    <span :class="['text-[10px] font-bold border px-2 py-1 rounded-full', getStatus(rep.status).badge]">{{ getStatus(rep.status).label }}</span>
                                    <Link :href="route('admin.environment.show', rep.id)" class="px-3 py-1.5 bg-green-600/20 border border-green-500/30 rounded-lg text-green-400 text-xs font-bold">View →</Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="lastPage > 1" class="flex items-center justify-between">
                    <p class="text-xs text-slate-500">Page {{ currentPage }} of {{ lastPage }}</p>
                    <div class="flex gap-2">
                        <button @click="goToPage(reports?.links?.prev)" :disabled="!reports?.links?.prev" class="px-4 py-2 bg-slate-800/60 border border-slate-700/50 rounded-xl text-sm text-slate-300 font-semibold disabled:opacity-30 hover:bg-slate-700/60 transition-colors">← Prev</button>
                        <button @click="goToPage(reports?.links?.next)" :disabled="!reports?.links?.next" class="px-4 py-2 bg-slate-800/60 border border-slate-700/50 rounded-xl text-sm text-slate-300 font-semibold disabled:opacity-30 hover:bg-slate-700/60 transition-colors">Next →</button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>