<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

// ✅ FIX: prop is a Laravel paginator object {data, links, meta}, NOT a plain array
const props = defineProps({
    requests: {
        type: Object,
        default: () => ({ data: [], links: {}, meta: {} })
    }
});

// ✅ FIX: always read from .data — handles both paginator object and plain array gracefully
const requestList = computed(() => {
    if (!props.requests) return [];
    // Laravel paginator → { data: [...], links: {}, meta: {} }
    if (Array.isArray(props.requests.data)) return props.requests.data;
    // Plain array fallback (in case controller changes)
    if (Array.isArray(props.requests)) return props.requests;
    return [];
});

// Pagination meta from Laravel paginator
const paginationLinks = computed(() => props.requests?.meta?.links ?? []);
const currentPage    = computed(() => props.requests?.meta?.current_page ?? 1);
const lastPage       = computed(() => props.requests?.meta?.last_page ?? 1);
const total          = computed(() => props.requests?.meta?.total ?? requestList.value.length);
const from           = computed(() => props.requests?.meta?.from ?? 1);
const to             = computed(() => props.requests?.meta?.to ?? requestList.value.length);

// ── Filters ──────────────────────────────────────────────────────
const search       = ref('');
const statusFilter = ref('all');

const filteredRequests = computed(() => {
    let list = requestList.value;
    const q = search.value.toLowerCase().trim();

    if (q) {
        list = list.filter(r =>
            (r.tracking_code ?? '').toLowerCase().includes(q) ||
            (r.user?.name  ?? '').toLowerCase().includes(q) ||
            (r.document_type ?? '').toLowerCase().includes(q) ||
            (r.department ?? '').toLowerCase().includes(q)
        );
    }
    if (statusFilter.value !== 'all') {
        list = list.filter(r => r.status === statusFilter.value);
    }
    return list;
});

// Debounce search → trigger Inertia reload with server-side filter
let searchTimeout = null;
watch(search, (val) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(route('admin.documents.index'), { search: val, status: statusFilter.value }, {
            preserveState: true, preserveScroll: true, replace: true
        });
    }, 400);
});

watch(statusFilter, (val) => {
    router.get(route('admin.documents.index'), { search: search.value, status: val }, {
        preserveState: true, preserveScroll: true, replace: true
    });
});

const goToPage = (url) => {
    if (!url) return;
    router.get(url, {}, { preserveScroll: true });
};

// ── Status helpers ────────────────────────────────────────────────
const statusConfig = {
    pending:          { label: 'Pending',         badge: 'bg-yellow-500/20 text-yellow-400 border-yellow-500/30', dot: 'bg-yellow-400', ping: true  },
    processing:       { label: 'Processing',       badge: 'bg-blue-500/20 text-blue-400 border-blue-500/30',       dot: 'bg-blue-400',   ping: true  },
    ready_for_pickup: { label: 'Ready for Pickup', badge: 'bg-green-500/20 text-green-400 border-green-500/30',    dot: 'bg-green-400',  ping: false },
    completed:        { label: 'Completed',        badge: 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30', dot: 'bg-emerald-400', ping: false },
    rejected:         { label: 'Rejected',         badge: 'bg-red-500/20 text-red-400 border-red-500/30',          dot: 'bg-red-400',    ping: false },
};

const getStatus = (status) => statusConfig[status] ?? {
    label: status ?? 'Unknown',
    badge: 'bg-slate-500/20 text-slate-400 border-slate-500/30',
    dot:   'bg-slate-400',
    ping:  false,
};

const blockchainBadge = (r) => {
    if (r.blockchain_tx_hash) return { text: '⛓ Anchored', class: 'bg-cyan-500/15 text-cyan-400 border-cyan-500/30' };
    if (r.status === 'completed') return { text: '⏳ Anchoring', class: 'bg-amber-500/15 text-amber-400 border-amber-500/30' };
    return null;
};

const formatDate = (d) => {
    if (!d) return '—';
    return new Date(d).toLocaleDateString('en-PH', { month: 'short', day: 'numeric', year: 'numeric' });
};

// ── Summary counts from current page ─────────────────────────────
const counts = computed(() => {
    const list = requestList.value;
    return {
        all:        total.value,
        pending:    list.filter(r => r.status === 'pending').length,
        processing: list.filter(r => r.status === 'processing').length,
        completed:  list.filter(r => r.status === 'completed').length,
        rejected:   list.filter(r => r.status === 'rejected').length,
    };
});
</script>

<template>
    <Head title="Document Requests" />
    <AuthenticatedLayout>
        <div class="min-h-screen bg-slate-950 text-white font-sans">

            <!-- Background -->
            <div class="fixed inset-0 pointer-events-none z-0">
                <div class="absolute inset-0 bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950"></div>
                <div class="absolute top-0 right-1/4 w-[600px] h-[400px] bg-blue-500/8 rounded-full blur-[120px]"></div>
                <div class="absolute bottom-0 left-1/4 w-[500px] h-[400px] bg-purple-500/8 rounded-full blur-[120px]"></div>
                <div class="absolute inset-0 bg-[linear-gradient(to_right,rgba(255,255,255,0.015)_1px,transparent_1px),linear-gradient(to_bottom,rgba(255,255,255,0.015)_1px,transparent_1px)] bg-[size:4rem_4rem]"></div>
            </div>

            <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

                <!-- ── Header ─────────────────────────────────────────────── -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <div class="flex items-center gap-3 mb-1">
                            <Link :href="route('admin.dashboard')"
                                class="text-slate-500 hover:text-slate-300 text-sm transition-colors flex items-center gap-1 group">
                                <span class="group-hover:-translate-x-1 transition-transform inline-block">←</span>
                                Dashboard
                            </Link>
                            <span class="text-slate-700">/</span>
                            <span class="text-slate-400 text-sm">Documents</span>
                        </div>
                        <h1 class="text-2xl sm:text-3xl font-bold text-white tracking-tight">Document Requests</h1>
                        <p class="text-slate-400 text-sm mt-1">
                            Showing {{ from }}–{{ to }} of {{ total }} total requests
                        </p>
                    </div>

                    <!-- Summary chips -->
                    <div class="flex flex-wrap gap-2">
                        <button
                            v-for="(cfg, key) in statusConfig"
                            :key="key"
                            @click="statusFilter = key"
                            :class="[
                                'px-3 py-1.5 rounded-full text-xs font-bold border transition-all',
                                statusFilter === key ? cfg.badge : 'bg-slate-800/50 text-slate-500 border-slate-700/50 hover:border-slate-600'
                            ]"
                        >
                            {{ cfg.label }}
                        </button>
                        <button
                            @click="statusFilter = 'all'"
                            :class="[
                                'px-3 py-1.5 rounded-full text-xs font-bold border transition-all',
                                statusFilter === 'all'
                                    ? 'bg-white/10 text-white border-white/20'
                                    : 'bg-slate-800/50 text-slate-500 border-slate-700/50 hover:border-slate-600'
                            ]"
                        >
                            All ({{ counts.all }})
                        </button>
                    </div>
                </div>

                <!-- ── Search Bar ──────────────────────────────────────────── -->
                <div class="relative">
                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500 pointer-events-none"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Search by tracking code, name, document type..."
                        class="w-full pl-11 pr-4 py-3 bg-slate-900/60 backdrop-blur border border-slate-700/50 rounded-xl text-white placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50 transition-all"
                    />
                </div>

                <!-- ── Empty State ─────────────────────────────────────────── -->
                <div v-if="filteredRequests.length === 0"
                    class="text-center py-20 bg-slate-900/40 backdrop-blur border border-slate-800/50 rounded-2xl">
                    <div class="text-6xl mb-4 opacity-20">📭</div>
                    <p class="text-slate-400 font-semibold text-lg">
                        {{ search || statusFilter !== 'all' ? 'No matching requests found' : 'No document requests yet' }}
                    </p>
                    <p class="text-slate-600 text-sm mt-1">
                        {{ search ? 'Try adjusting your search terms' : 'Requests will appear here once submitted by residents' }}
                    </p>
                    <button v-if="search || statusFilter !== 'all'"
                        @click="search = ''; statusFilter = 'all'"
                        class="mt-4 px-4 py-2 bg-slate-800 hover:bg-slate-700 border border-slate-700 rounded-xl text-slate-300 text-sm font-semibold transition-colors">
                        Clear filters
                    </button>
                </div>

                <!-- ── Table ───────────────────────────────────────────────── -->
                <div v-else class="bg-slate-900/60 backdrop-blur border border-slate-800/50 rounded-2xl overflow-hidden">
                    <!-- Table header -->
                    <div class="hidden lg:grid grid-cols-12 gap-4 px-6 py-3 border-b border-slate-800/50 bg-slate-900/80">
                        <div class="col-span-2 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Tracking #</div>
                        <div class="col-span-3 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Applicant</div>
                        <div class="col-span-3 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Document</div>
                        <div class="col-span-2 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Status</div>
                        <div class="col-span-1 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Date</div>
                        <div class="col-span-1 text-[10px] font-bold text-slate-500 uppercase tracking-widest text-right">Action</div>
                    </div>

                    <!-- Rows -->
                    <div class="divide-y divide-slate-800/50">
                        <div
                            v-for="(req, index) in filteredRequests"
                            :key="req.id"
                            class="group hover:bg-white/[0.02] transition-colors"
                            :style="`animation: fadeInUp 0.3s ease-out ${index * 0.03}s backwards`"
                        >
                            <!-- Desktop row -->
                            <div class="hidden lg:grid grid-cols-12 gap-4 px-6 py-4 items-center">
                                <!-- Tracking code -->
                                <div class="col-span-2">
                                    <span class="font-mono text-xs text-blue-400 font-bold">
                                        {{ req.tracking_code ?? '—' }}
                                    </span>
                                </div>

                                <!-- Applicant -->
                                <div class="col-span-3 flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-sm font-bold text-white flex-shrink-0">
                                        {{ req.user?.name?.charAt(0) ?? '?' }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-white truncate">{{ req.user?.name ?? 'Unknown' }}</p>
                                        <p class="text-xs text-slate-500 truncate">{{ req.user?.email ?? '' }}</p>
                                    </div>
                                </div>

                                <!-- Document type + department -->
                                <div class="col-span-3">
                                    <p class="text-sm text-slate-200 truncate">{{ req.document_type ?? '—' }}</p>
                                    <p class="text-xs text-slate-500 truncate">{{ req.department ?? '—' }}</p>
                                </div>

                                <!-- Status -->
                                <div class="col-span-2 flex flex-col gap-1">
                                    <span :class="['inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold border w-fit', getStatus(req.status).badge]">
                                        <span :class="[getStatus(req.status).dot, 'w-1.5 h-1.5 rounded-full', getStatus(req.status).ping ? 'animate-pulse' : '']"></span>
                                        {{ getStatus(req.status).label }}
                                    </span>
                                    <!-- Blockchain badge -->
                                    <span v-if="blockchainBadge(req)"
                                        :class="['inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-bold border w-fit', blockchainBadge(req).class]">
                                        {{ blockchainBadge(req).text }}
                                    </span>
                                </div>

                                <!-- Date -->
                                <div class="col-span-1 text-xs text-slate-500 font-mono">
                                    {{ formatDate(req.created_at) }}
                                </div>

                                <!-- Action -->
                                <div class="col-span-1 flex justify-end">
                                    <Link :href="route('admin.documents.show', req.id)"
                                        class="px-3 py-1.5 bg-blue-600/20 hover:bg-blue-600/40 border border-blue-500/30 rounded-lg text-blue-400 text-xs font-bold transition-all opacity-0 group-hover:opacity-100 hover:scale-105">
                                        Review →
                                    </Link>
                                </div>
                            </div>

                            <!-- Mobile card -->
                            <div class="lg:hidden p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-center gap-3 flex-1 min-w-0">
                                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-sm font-bold text-white flex-shrink-0">
                                            {{ req.user?.name?.charAt(0) ?? '?' }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-bold text-white truncate">{{ req.user?.name ?? 'Unknown' }}</p>
                                            <p class="text-xs text-slate-400 truncate">{{ req.document_type ?? '—' }}</p>
                                            <p class="text-xs font-mono text-blue-400 mt-0.5">{{ req.tracking_code ?? '—' }}</p>
                                        </div>
                                    </div>
                                    <div class="flex flex-col items-end gap-2 flex-shrink-0">
                                        <span :class="['inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold border', getStatus(req.status).badge]">
                                            <span :class="[getStatus(req.status).dot, 'w-1.5 h-1.5 rounded-full']"></span>
                                            {{ getStatus(req.status).label }}
                                        </span>
                                        <Link :href="route('admin.documents.show', req.id)"
                                            class="px-3 py-1.5 bg-blue-600/20 border border-blue-500/30 rounded-lg text-blue-400 text-xs font-bold">
                                            Review →
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ── Pagination ──────────────────────────────────────────── -->
                <div v-if="lastPage > 1" class="flex items-center justify-between gap-4">
                    <p class="text-xs text-slate-500">
                        Page {{ currentPage }} of {{ lastPage }}
                    </p>
                    <div class="flex gap-2">
                        <button
                            @click="goToPage(requests?.links?.prev)"
                            :disabled="!requests?.links?.prev"
                            class="px-4 py-2 bg-slate-800/60 border border-slate-700/50 rounded-xl text-sm text-slate-300 font-semibold disabled:opacity-30 disabled:cursor-not-allowed hover:bg-slate-700/60 transition-colors"
                        >
                            ← Prev
                        </button>

                        <template v-for="link in paginationLinks" :key="link.label">
                            <button
                                v-if="link.url && !['&laquo; Previous', 'Next &raquo;'].includes(link.label)"
                                @click="goToPage(link.url)"
                                :class="[
                                    'px-4 py-2 rounded-xl text-sm font-semibold border transition-colors',
                                    link.active
                                        ? 'bg-blue-600 border-blue-500 text-white shadow-lg shadow-blue-500/20'
                                        : 'bg-slate-800/60 border-slate-700/50 text-slate-400 hover:bg-slate-700/60'
                                ]"
                            >
                                {{ link.label }}
                            </button>
                        </template>

                        <button
                            @click="goToPage(requests?.links?.next)"
                            :disabled="!requests?.links?.next"
                            class="px-4 py-2 bg-slate-800/60 border border-slate-700/50 rounded-xl text-sm text-slate-300 font-semibold disabled:opacity-30 disabled:cursor-not-allowed hover:bg-slate-700/60 transition-colors"
                        >
                            Next →
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(10px); }
    to   { opacity: 1; transform: translateY(0); }
}
</style>