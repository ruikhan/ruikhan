<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    businesses: Array,
    filters: Object,
    categories: Array,
    statuses: Array,
});

const searchQuery = ref(props.filters.search || '');
const selectedCategory = ref(props.filters.category || '');
const selectedStatus = ref(props.filters.status || '');
const viewMode = ref('grid');
const isFiltering = ref(false);

const applyFilters = () => {
    isFiltering.value = true;
    router.get(route('marketplace.index'), {
        search: searchQuery.value,
        category: selectedCategory.value,
        status: selectedStatus.value,
    }, {
        preserveState: true,
        preserveScroll: true,
        onFinish: () => { setTimeout(() => { isFiltering.value = false; }, 300); }
    });
};

const clearFilters = () => {
    searchQuery.value = '';
    selectedCategory.value = '';
    selectedStatus.value = '';
    applyFilters();
};

const getStatusColor = (status) => {
    const colors = {
        'available': 'bg-emerald-500/20 text-emerald-300 border-emerald-500/40',
        'open':      'bg-blue-500/20 text-blue-300 border-blue-500/40',
        'closed':    'bg-gray-500/20 text-gray-300 border-gray-500/40',
        'full':      'bg-rose-500/20 text-rose-300 border-rose-500/40',
    };
    return colors[status] || 'bg-gray-500/20 text-gray-300 border-gray-500/40';
};

const getTierBadge = (tier) => {
    const badges = {
        'premium':  { icon: '👑', color: 'from-amber-500/30 via-yellow-500/20 to-amber-600/30',   textColor: 'text-amber-200',  borderColor: 'border-amber-500/50' },
        'standard': { icon: '🚀', color: 'from-purple-500/30 via-purple-600/20 to-purple-700/30', textColor: 'text-purple-200', borderColor: 'border-purple-500/50' },
        'basic':    { icon: '🎯', color: 'from-blue-500/30 via-blue-600/20 to-blue-700/30',       textColor: 'text-blue-200',   borderColor: 'border-blue-500/50' },
    };
    return badges[tier] || badges.basic;
};

const getCategoryIcon = (category) => {
    const icons = {
        'retail': '🛍️', 'food': '🍽️', 'service': '⚙️', 'health': '💊',
        'education': '📚', 'technology': '💻', 'entertainment': '🎭', 'agriculture': '🌾',
    };
    return icons[category] || '🏪';
};

const hasActiveFilters = computed(() => props.filters.search || props.filters.category || props.filters.status);
const statsData = computed(() => ({
    total:    props.businesses.length,
    verified: props.businesses.filter(b => b.is_verified).length,
    premium:  props.businesses.filter(b => b.tier === 'premium').length,
}));
</script>

<template>
    <Head title="Marketplace" />
    <AuthenticatedLayout>

        <!-- Background -->
        <div class="fixed inset-0 z-0 bg-black pointer-events-none">
            <div class="absolute top-[-10%] left-[15%] w-[900px] h-[700px] bg-blue-600/15 rounded-[100%] blur-[150px] animate-drift"></div>
            <div class="absolute bottom-[-15%] right-[15%] w-[1000px] h-[700px] bg-purple-600/15 rounded-[100%] blur-[150px] animate-drift-delayed"></div>
            <div class="absolute top-[40%] right-[40%] w-[700px] h-[700px] bg-amber-600/10 rounded-[100%] blur-[130px] animate-drift-slow"></div>
        </div>

        <div class="relative z-10 max-w-[1800px] mx-auto py-6 px-4 lg:px-8 space-y-6">

            <!-- ══════════════════════════════════════
                 MOBILE HEADER — banking-app style
            ══════════════════════════════════════ -->
            <div class="block lg:hidden space-y-4">

                <!-- Page title row -->
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-700 text-white/40 uppercase tracking-widest mb-1">Discover</p>
                        <h1 class="text-2xl font-900 text-white leading-tight tracking-tight">
                            Marketplace
                            <span class="ml-2 text-base">🏪</span>
                        </h1>
                    </div>
                    <!-- View toggle pill -->
                    <div class="flex gap-1 p-1 rounded-xl bg-white/6 border border-white/10">
                        <button @click="viewMode = 'grid'" :class="['p-2 rounded-lg transition-all', viewMode==='grid' ? 'bg-white/15 text-white' : 'text-white/40']">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                        </button>
                        <button @click="viewMode = 'list'" :class="['p-2 rounded-lg transition-all', viewMode==='list' ? 'bg-white/15 text-white' : 'text-white/40']">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        </button>
                    </div>
                </div>

                <!-- Stats row — like the hero card stats strip -->
                <div class="grid grid-cols-3 gap-3">
                    <div class="m-stat-pill">
                        <span class="m-stat-pill-icon">🏢</span>
                        <p class="m-stat-pill-val">{{ statsData.total }}</p>
                        <p class="m-stat-pill-lbl">Listed</p>
                    </div>
                    <div class="m-stat-pill">
                        <span class="m-stat-pill-icon">✅</span>
                        <p class="m-stat-pill-val">{{ statsData.verified }}</p>
                        <p class="m-stat-pill-lbl">Verified</p>
                    </div>
                    <div class="m-stat-pill">
                        <span class="m-stat-pill-icon">👑</span>
                        <p class="m-stat-pill-val">{{ statsData.premium }}</p>
                        <p class="m-stat-pill-lbl">Premium</p>
                    </div>
                </div>

                <!-- Search bar — full width, same style as Dashboard -->
                <div class="m-search-bar">
                    <svg class="m-search-bar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input v-model="searchQuery" @keyup.enter="applyFilters" type="text"
                        placeholder="Search businesses…"
                        class="m-search-bar-input"/>
                    <button v-if="searchQuery" @click="searchQuery=''; applyFilters();" class="m-search-clear">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Filter chips — horizontal scroll -->
                <div class="m-filter-scroll">
                    <!-- Category pills -->
                    <button @click="selectedCategory=''; applyFilters()"
                        :class="['m-chip', !selectedCategory ? 'm-chip-active' : '']">All</button>
                    <button v-for="cat in categories" :key="cat.value"
                        @click="selectedCategory=cat.value; applyFilters()"
                        :class="['m-chip', selectedCategory===cat.value ? 'm-chip-active' : '']">
                        {{ cat.label }}
                    </button>
                </div>

                <!-- Status filter strip -->
                <div class="m-filter-scroll">
                    <button @click="selectedStatus=''; applyFilters()"
                        :class="['m-chip', !selectedStatus ? 'm-chip-active-green' : '']">Any Status</button>
                    <button v-for="status in statuses" :key="status.value"
                        @click="selectedStatus=status.value; applyFilters()"
                        :class="['m-chip', selectedStatus===status.value ? 'm-chip-active-green' : '']">
                        {{ status.label }}
                    </button>
                </div>

                <!-- Clear filters notice -->
                <div v-if="hasActiveFilters" class="flex items-center justify-between px-1">
                    <p class="text-xs text-white/50 font-600">{{ businesses.length }} results found</p>
                    <button @click="clearFilters" class="text-xs font-700 text-rose-400 hover:text-rose-300 transition-colors">Clear all ×</button>
                </div>
            </div>

            <!-- ══════════════════════════════════════
                 DESKTOP HEADER (unchanged)
            ══════════════════════════════════════ -->
            <div class="hidden lg:block relative group">
                <div class="absolute inset-0 bg-gradient-to-br from-blue-500/20 via-purple-500/20 to-amber-500/20 rounded-[2.5rem] blur-3xl opacity-60 group-hover:opacity-80 transition-opacity duration-700"></div>
                <div class="relative overflow-hidden rounded-[2.5rem] bg-[#1c1c1e]/90 backdrop-blur-3xl border border-white/10 shadow-2xl">
                    <div class="relative p-8 md:p-12 text-center">
                        <div class="inline-flex items-center justify-center mb-6">
                            <div class="relative w-20 h-20 rounded-3xl bg-gradient-to-br from-blue-500/20 via-purple-500/20 to-amber-500/20 border border-white/20 flex items-center justify-center">
                                <span class="text-5xl">🏪</span>
                            </div>
                        </div>
                        <h1 class="text-4xl md:text-6xl font-bold mb-4">
                            <span class="bg-gradient-to-br from-white via-gray-100 to-gray-400 bg-clip-text text-transparent">Local Business</span>
                            <br />
                            <span class="bg-gradient-to-r from-blue-400 via-purple-400 to-amber-400 bg-clip-text text-transparent">Marketplace</span>
                        </h1>
                        <p class="text-gray-400 text-lg max-w-2xl mx-auto mb-6">Discover and support local businesses in your barangay</p>
                        <div class="flex flex-wrap items-center justify-center gap-4">
                            <div class="px-5 py-2.5 rounded-xl bg-white/5 border border-white/10">
                                <div class="flex items-center gap-2"><span class="text-2xl">🏢</span><div class="text-left"><p class="text-xs text-white/50">Businesses</p><p class="text-lg font-bold text-white">{{ statsData.total }}</p></div></div>
                            </div>
                            <div class="px-5 py-2.5 rounded-xl bg-white/5 border border-white/10">
                                <div class="flex items-center gap-2"><span class="text-2xl">✅</span><div class="text-left"><p class="text-xs text-white/50">Verified</p><p class="text-lg font-bold text-white">{{ statsData.verified }}</p></div></div>
                            </div>
                            <div class="px-5 py-2.5 rounded-xl bg-white/5 border border-white/10">
                                <div class="flex items-center gap-2"><span class="text-2xl">👑</span><div class="text-left"><p class="text-xs text-white/50">Premium</p><p class="text-lg font-bold text-white">{{ statsData.premium }}</p></div></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Desktop Filters -->
            <div class="hidden lg:block relative group">
                <div class="relative rounded-[2rem] bg-[#1c1c1e]/90 backdrop-blur-3xl border border-white/10 p-6 md:p-8 shadow-xl">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-xl font-bold text-white mb-1">Filters & Search</h3>
                            <p class="text-sm text-white/40">Find exactly what you're looking for</p>
                        </div>
                        <div class="flex gap-2 p-1 rounded-xl bg-white/5 border border-white/10">
                            <button @click="viewMode='grid'" :class="['p-2 rounded-lg transition-all', viewMode==='grid' ? 'bg-white/15 text-white' : 'text-white/40 hover:text-white']">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                            </button>
                            <button @click="viewMode='list'" :class="['p-2 rounded-lg transition-all', viewMode==='list' ? 'bg-white/15 text-white' : 'text-white/40 hover:text-white']">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                            </button>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                        <div class="md:col-span-6 relative">
                            <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <input v-model="searchQuery" @keyup.enter="applyFilters" type="text" placeholder="Search businesses, categories..."
                                class="w-full h-14 pl-12 pr-4 bg-white/5 border border-white/10 rounded-2xl text-white placeholder-white/30 focus:bg-white/10 focus:border-blue-500/50 transition-all"/>
                        </div>
                        <div class="md:col-span-3 relative">
                            <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-white/40 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                            <select v-model="selectedCategory" @change="applyFilters" class="w-full h-14 pl-12 pr-10 bg-white/5 border border-white/10 rounded-2xl text-white appearance-none cursor-pointer focus:bg-white/10 transition-all">
                                <option value="" class="bg-[#1c1c1e]">All Categories</option>
                                <option v-for="cat in categories" :key="cat.value" :value="cat.value" class="bg-[#1c1c1e]">{{ cat.label }}</option>
                            </select>
                            <svg class="absolute right-4 top-1/2 -translate-y-1/2 w-5 h-5 text-white/40 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                        <div class="md:col-span-3 relative">
                            <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-white/40 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <select v-model="selectedStatus" @change="applyFilters" class="w-full h-14 pl-12 pr-10 bg-white/5 border border-white/10 rounded-2xl text-white appearance-none cursor-pointer focus:bg-white/10 transition-all">
                                <option value="" class="bg-[#1c1c1e]">All Status</option>
                                <option v-for="status in statuses" :key="status.value" :value="status.value" class="bg-[#1c1c1e]">{{ status.label }}</option>
                            </select>
                            <svg class="absolute right-4 top-1/2 -translate-y-1/2 w-5 h-5 text-white/40 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </div>
                    <div v-if="hasActiveFilters" class="flex flex-wrap items-center gap-3 mt-6 pt-6 border-t border-white/10">
                        <button @click="clearFilters" class="px-4 py-2 rounded-xl bg-rose-500/20 text-rose-300 text-sm font-semibold border border-rose-500/30 hover:bg-rose-500/30 transition-all flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            Clear All Filters
                        </button>
                        <span class="text-sm text-white px-4 py-2 rounded-xl bg-white/5 border border-white/10">{{ businesses.length }} businesses found</span>
                    </div>
                </div>
            </div>

            <!-- Loading -->
            <div v-if="isFiltering" class="flex items-center justify-center py-12">
                <div class="flex flex-col items-center gap-4">
                    <div class="relative w-16 h-16">
                        <div class="absolute inset-0 border-4 border-blue-500/30 rounded-full"></div>
                        <div class="absolute inset-0 border-4 border-blue-500 rounded-full border-t-transparent animate-spin"></div>
                    </div>
                    <p class="text-white/60 font-medium">Loading businesses...</p>
                </div>
            </div>

            <!-- ══════════════════════════════════════
                 MOBILE GRID VIEW — card list style
            ══════════════════════════════════════ -->
            <div v-else-if="businesses.length > 0 && viewMode === 'grid'">

                <!-- Mobile: full-width stacked cards -->
                <div class="grid grid-cols-1 gap-4 lg:hidden">
                    <a v-for="(business, index) in businesses" :key="business.id"
                        :href="route('marketplace.show', business.id)"
                        class="m-biz-card animate-fade-in-up"
                        :style="{ animationDelay: `${index * 0.04}s` }">

                        <div class="m-biz-card-shine"></div>

                        <!-- Left: icon -->
                        <div class="m-biz-icon-wrap">
                            <span class="text-2xl">{{ getCategoryIcon(business.category) }}</span>
                            <div class="m-biz-icon-glow"></div>
                        </div>

                        <!-- Center: info -->
                        <div class="m-biz-info">
                            <div class="flex items-center gap-1.5 mb-0.5">
                                <h3 class="m-biz-name">{{ business.name }}</h3>
                                <span v-if="business.is_verified" class="text-amber-400 flex-shrink-0">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                </span>
                            </div>
                            <p class="m-biz-owner">{{ business.owner_name }}</p>
                            <div class="flex items-center gap-2 mt-1.5">
                                <span :class="`m-biz-status-badge ${getStatusColor(business.status)}`">
                                    <span class="w-1.5 h-1.5 rounded-full bg-current mr-1 inline-block"></span>
                                    {{ business.status }}
                                </span>
                                <span :class="`m-biz-tier-badge bg-gradient-to-r ${getTierBadge(business.tier).color} ${getTierBadge(business.tier).textColor} border ${getTierBadge(business.tier).borderColor}`">
                                    {{ getTierBadge(business.tier).icon }} {{ business.tier }}
                                </span>
                            </div>
                        </div>

                        <!-- Right: stats + arrow -->
                        <div class="m-biz-right">
                            <div class="flex items-center gap-1 text-amber-300 text-xs font-700 mb-1">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                {{ business.rating }}
                            </div>
                            <svg class="w-4 h-4 text-white/25" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </div>
                    </a>
                </div>

                <!-- Desktop: original grid -->
                <div class="hidden lg:grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    <a v-for="(business, index) in businesses" :key="business.id"
                        :href="route('marketplace.show', business.id)"
                        class="group relative overflow-hidden rounded-[2rem] bg-[#1c1c1e]/60 backdrop-blur-xl border border-white/10 transition-all duration-500 hover:scale-[1.02] hover:shadow-2xl cursor-pointer animate-fade-in-up"
                        :style="{ animationDelay: `${index * 0.05}s` }">
                        <div class="absolute inset-0 bg-gradient-to-br from-blue-500/10 via-purple-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-all duration-500"></div>
                        <div class="relative p-6">
                            <div class="flex items-start justify-between mb-6">
                                <div v-if="business.is_verified">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-gradient-to-r from-amber-500/20 via-yellow-500/20 to-amber-600/20 text-amber-200 border border-amber-500/40">
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                        VERIFIED
                                    </span>
                                </div>
                                <div class="ml-auto">
                                    <span :class="`inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-gradient-to-r ${getTierBadge(business.tier).color} ${getTierBadge(business.tier).textColor} border ${getTierBadge(business.tier).borderColor}`">
                                        <span class="text-base">{{ getTierBadge(business.tier).icon }}</span>{{ business.tier.toUpperCase() }}
                                    </span>
                                </div>
                            </div>
                            <div class="relative mb-5">
                                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-500/20 via-purple-500/15 to-transparent border border-white/10 flex items-center justify-center text-3xl shadow-xl transform group-hover:scale-110 group-hover:rotate-6 transition-all duration-500">
                                    {{ getCategoryIcon(business.category) }}
                                </div>
                            </div>
                            <h3 class="text-xl font-bold text-white mb-2 line-clamp-1">{{ business.name }}</h3>
                            <p class="text-sm text-gray-400 mb-5 line-clamp-2 leading-relaxed">{{ business.description }}</p>
                            <div class="flex items-center justify-between mb-5 pb-5 border-b border-white/10">
                                <span :class="`inline-flex items-center px-3 py-1.5 text-xs font-bold rounded-xl border ${getStatusColor(business.status)}`">
                                    <span class="w-1.5 h-1.5 rounded-full bg-current mr-2 animate-pulse"></span>{{ business.status }}
                                </span>
                                <div class="flex items-center gap-4 text-sm">
                                    <span class="flex items-center gap-1.5 text-amber-300">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        {{ business.rating }}
                                    </span>
                                    <span class="flex items-center gap-1.5 text-rose-300">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/></svg>
                                        {{ business.likes_count }}
                                    </span>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center text-white font-bold text-sm">{{ business.owner_name.charAt(0) }}</div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs text-gray-500">Owned by</p>
                                    <p class="text-sm text-gray-300 font-semibold truncate">{{ business.owner_name }}</p>
                                </div>
                                <svg class="w-5 h-5 text-white/20 group-hover:text-white/60 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- List View (same for both) -->
            <div v-else-if="businesses.length > 0 && viewMode === 'list'" class="space-y-4">
                <a v-for="(business, index) in businesses" :key="business.id"
                    :href="route('marketplace.show', business.id)"
                    class="group relative overflow-hidden rounded-[2rem] bg-[#1c1c1e]/60 backdrop-blur-xl border border-white/10 transition-all duration-300 hover:bg-[#2c2c2e] hover:shadow-xl cursor-pointer animate-fade-in-up block"
                    :style="{ animationDelay: `${index * 0.03}s` }">
                    <div class="relative p-5 flex items-center gap-4">
                        <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-500/20 to-purple-500/20 border border-white/10 flex items-center justify-center text-3xl flex-shrink-0 transform group-hover:scale-110 transition-transform shadow-xl">
                            {{ getCategoryIcon(business.category) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-1.5 mb-1">
                                <h3 class="text-base font-bold text-white truncate">{{ business.name }}</h3>
                                <span v-if="business.is_verified" class="text-amber-400 flex-shrink-0">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                </span>
                            </div>
                            <p class="text-xs text-gray-400 line-clamp-1 mb-2">{{ business.description }}</p>
                            <div class="flex items-center gap-2 flex-wrap">
                                <span :class="`px-2 py-0.5 text-xs font-bold rounded-lg border ${getStatusColor(business.status)}`">{{ business.status }}</span>
                                <span :class="`inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-xs font-bold bg-gradient-to-r ${getTierBadge(business.tier).color} ${getTierBadge(business.tier).textColor} border ${getTierBadge(business.tier).borderColor}`">
                                    {{ getTierBadge(business.tier).icon }} {{ business.tier }}
                                </span>
                                <span class="flex items-center gap-1 text-xs text-amber-300">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    {{ business.rating }}
                                </span>
                            </div>
                        </div>
                        <svg class="w-5 h-5 text-white/20 group-hover:text-white/60 flex-shrink-0 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </div>
                </a>
            </div>

            <!-- Empty State -->
            <div v-else-if="!isFiltering" class="relative">
                <div class="rounded-[2.5rem] bg-[#1c1c1e]/60 backdrop-blur-xl border border-dashed border-white/10 p-16 text-center">
                    <div class="w-24 h-24 rounded-full bg-white/5 flex items-center justify-center mx-auto mb-6"><span class="text-6xl opacity-40">🔍</span></div>
                    <h3 class="text-2xl font-bold text-white mb-3">No Businesses Found</h3>
                    <p class="text-gray-400 mb-8 max-w-md mx-auto">Try adjusting your filters or search terms.</p>
                    <button @click="clearFilters" class="inline-flex items-center gap-2 px-8 py-4 rounded-xl bg-gradient-to-r from-blue-500 to-purple-600 text-white font-semibold shadow-xl">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        Clear All Filters
                    </button>
                </div>
            </div>

        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
/* ── Animations ── */
@keyframes drift         { 0%,100%{transform:translate(0,0)scale(1);}33%{transform:translate(30px,-30px)scale(1.1);}66%{transform:translate(-20px,20px)scale(0.9);} }
@keyframes drift-delayed { 0%,100%{transform:translate(0,0)scale(1);}33%{transform:translate(-30px,30px)scale(0.9);}66%{transform:translate(20px,-20px)scale(1.1);} }
@keyframes drift-slow    { 0%,100%{transform:translate(0,0)rotate(0);}50%{transform:translate(15px,-15px)rotate(5deg);} }
.animate-drift         { animation: drift 25s ease-in-out infinite; }
.animate-drift-delayed { animation: drift-delayed 30s ease-in-out infinite; }
.animate-drift-slow    { animation: drift-slow 35s ease-in-out infinite; }

@keyframes fade-in-up { from{opacity:0;transform:translateY(20px) scale(0.95);}to{opacity:1;transform:none;} }
.animate-fade-in-up { animation: fade-in-up 0.5s cubic-bezier(0.16,1,0.3,1) forwards; }

/* ── Mobile stat pills ── */
.m-stat-pill {
    display: flex; flex-direction: column; align-items: center; gap: 0.25rem;
    padding: 0.875rem 0.5rem;
    border-radius: 1.25rem;
    background: rgba(255,255,255,0.06);
    border: 1px solid rgba(255,255,255,0.09);
    backdrop-filter: blur(20px);
}
.m-stat-pill-icon { font-size: 1.25rem; }
.m-stat-pill-val  { font-size: 1.375rem; font-weight: 800; color: white; line-height: 1; letter-spacing: -0.02em; }
.m-stat-pill-lbl  { font-size: 0.5625rem; font-weight: 700; color: rgba(255,255,255,0.45); text-transform: uppercase; letter-spacing: 0.06em; }

/* ── Mobile search bar ── */
.m-search-bar {
    display: flex; align-items: center; gap: 0.75rem;
    padding: 0.75rem 1rem;
    border-radius: 1.25rem;
    background: rgba(255,255,255,0.07);
    border: 1px solid rgba(255,255,255,0.1);
    backdrop-filter: blur(20px);
}
.m-search-bar:focus-within { border-color: rgba(255,255,255,0.2); box-shadow: 0 0 0 3px rgba(255,255,255,0.05); }
.m-search-bar-icon  { width: 1.125rem; height: 1.125rem; color: rgba(255,255,255,0.35); flex-shrink: 0; }
.m-search-bar-input { flex: 1; background: transparent; border: none; outline: none; color: white; font-size: 0.9375rem; font-weight: 500; }
.m-search-bar-input::placeholder { color: rgba(255,255,255,0.3); }
.m-search-clear { width: 1.75rem; height: 1.75rem; display: flex; align-items: center; justify-content: center; border-radius: 0.5rem; background: rgba(255,255,255,0.1); color: rgba(255,255,255,0.5); }

/* ── Filter chip scroll ── */
.m-filter-scroll {
    display: flex; gap: 0.5rem;
    overflow-x: auto; padding-bottom: 0.25rem;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
}
.m-filter-scroll::-webkit-scrollbar { display: none; }

.m-chip {
    flex-shrink: 0;
    padding: 0.5rem 1rem;
    border-radius: 9999px;
    font-size: 0.75rem; font-weight: 700;
    color: rgba(255,255,255,0.5);
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.09);
    transition: all 0.2s;
    white-space: nowrap;
}
.m-chip:active { transform: scale(0.95); }
.m-chip-active       { color: white; background: rgba(59,130,246,0.25); border-color: rgba(59,130,246,0.4); }
.m-chip-active-green { color: white; background: rgba(16,185,129,0.2); border-color: rgba(16,185,129,0.35); }

/* ── Mobile biz card ── */
.m-biz-card {
    position: relative;
    display: flex; align-items: center; gap: 1rem;
    padding: 1rem;
    border-radius: 1.25rem;
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.08);
    backdrop-filter: blur(20px);
    overflow: hidden;
    transition: background 0.2s, border-color 0.2s;
}
.m-biz-card:active { transform: scale(0.97); }
.m-biz-card:hover  { background: rgba(255,255,255,0.09); border-color: rgba(255,255,255,0.15); }

.m-biz-card-shine {
    position: absolute; inset: 0;
    background: linear-gradient(135deg, rgba(255,255,255,0.04), transparent);
    pointer-events: none;
}

.m-biz-icon-wrap {
    position: relative;
    width: 3.25rem; height: 3.25rem; flex-shrink: 0;
    border-radius: 1rem;
    background: linear-gradient(135deg, rgba(59,130,246,0.2), rgba(139,92,246,0.2));
    border: 1px solid rgba(255,255,255,0.1);
    display: flex; align-items: center; justify-content: center;
}
.m-biz-icon-glow {
    position: absolute; inset: -3px; border-radius: 1rem;
    background: linear-gradient(135deg, rgba(59,130,246,0.3), rgba(139,92,246,0.3));
    filter: blur(10px); opacity: 0.5; z-index: -1;
}

.m-biz-info { flex: 1; min-width: 0; }
.m-biz-name  { font-size: 0.9375rem; font-weight: 800; color: white; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.m-biz-owner { font-size: 0.6875rem; font-weight: 600; color: rgba(255,255,255,0.4); margin-top: 0.125rem; }

.m-biz-status-badge {
    display: inline-flex; align-items: center;
    padding: 0.2rem 0.5rem; border-radius: 9999px;
    font-size: 0.625rem; font-weight: 700;
    border-width: 1px; border-style: solid;
    text-transform: capitalize;
}
.m-biz-tier-badge {
    display: inline-flex; align-items: center; gap: 0.25rem;
    padding: 0.2rem 0.5rem; border-radius: 9999px;
    font-size: 0.625rem; font-weight: 700;
    border-width: 1px; border-style: solid;
    text-transform: capitalize;
}

.m-biz-right { display: flex; flex-direction: column; align-items: flex-end; gap: 0.5rem; flex-shrink: 0; }

@media (prefers-reduced-motion: reduce) { *,*::before,*::after { animation-duration: 0.01ms !important; transition-duration: 0.01ms !important; } }
button:focus-visible,input:focus-visible,select:focus-visible,a:focus-visible { outline: 2px solid rgb(59,130,246); outline-offset: 2px; }
</style>