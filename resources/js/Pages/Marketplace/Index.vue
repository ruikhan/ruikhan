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
const viewMode = ref('grid'); // 'grid' or 'list'
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
        onFinish: () => {
            setTimeout(() => {
                isFiltering.value = false;
            }, 300);
        }
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
        'available': 'bg-emerald-500/20 text-emerald-300 border-emerald-500/40 shadow-emerald-500/20',
        'open': 'bg-blue-500/20 text-blue-300 border-blue-500/40 shadow-blue-500/20',
        'closed': 'bg-gray-500/20 text-gray-300 border-gray-500/40 shadow-gray-500/20',
        'full': 'bg-rose-500/20 text-rose-300 border-rose-500/40 shadow-rose-500/20'
    };
    return colors[status] || 'bg-gray-500/20 text-gray-300 border-gray-500/40';
};

const getTierBadge = (tier) => {
    const badges = {
        'premium': { 
            icon: '👑', 
            color: 'from-amber-500/30 via-yellow-500/20 to-amber-600/30',
            textColor: 'text-amber-200',
            borderColor: 'border-amber-500/50',
            glow: 'shadow-amber-500/30'
        },
        'standard': { 
            icon: '🚀', 
            color: 'from-purple-500/30 via-purple-600/20 to-purple-700/30',
            textColor: 'text-purple-200',
            borderColor: 'border-purple-500/50',
            glow: 'shadow-purple-500/30'
        },
        'basic': { 
            icon: '🎯', 
            color: 'from-blue-500/30 via-blue-600/20 to-blue-700/30',
            textColor: 'text-blue-200',
            borderColor: 'border-blue-500/50',
            glow: 'shadow-blue-500/30'
        }
    };
    return badges[tier] || badges.basic;
};

const getCategoryIcon = (category) => {
    const icons = {
        'retail': '🛍️',
        'food': '🍽️',
        'service': '⚙️',
        'health': '💊',
        'education': '📚',
        'technology': '💻',
        'entertainment': '🎭',
        'agriculture': '🌾',
    };
    return icons[category] || '🏪';
};

const hasActiveFilters = computed(() => {
    return props.filters.search || props.filters.category || props.filters.status;
});

const statsData = computed(() => {
    return {
        total: props.businesses.length,
        verified: props.businesses.filter(b => b.is_verified).length,
        premium: props.businesses.filter(b => b.tier === 'premium').length,
    };
});
</script>

<template>
    <Head title="Marketplace" />

    <AuthenticatedLayout>
        <!-- Enhanced Background -->
        <div class="fixed inset-0 z-0 bg-[#000000] pointer-events-none">
            <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-[0.03]"></div>
            <div class="absolute top-[-10%] left-[15%] w-[900px] h-[700px] bg-blue-600/15 rounded-[100%] blur-[150px] mix-blend-screen animate-drift"></div>
            <div class="absolute bottom-[-15%] right-[15%] w-[1000px] h-[700px] bg-purple-600/15 rounded-[100%] blur-[150px] mix-blend-screen animate-drift-delayed"></div>
            <div class="absolute top-[40%] right-[40%] w-[700px] h-[700px] bg-amber-600/10 rounded-[100%] blur-[130px] mix-blend-screen animate-drift-slow"></div>
        </div>

        <div class="relative z-10 max-w-[1800px] mx-auto space-y-8 py-8 px-4 lg:px-8">
            
            <!-- Premium Header -->
            <div class="relative group">
                <div class="absolute inset-0 bg-gradient-to-br from-blue-500/20 via-purple-500/20 to-amber-500/20 rounded-[2.5rem] blur-3xl opacity-60 group-hover:opacity-80 transition-opacity duration-700"></div>
                <div class="relative overflow-hidden rounded-[2.5rem] bg-[#1c1c1e]/90 backdrop-blur-3xl border border-white/10 shadow-2xl">
                    <!-- Animated Background Pattern -->
                    <div class="absolute inset-0 opacity-30">
                        <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-br from-blue-500/10 via-purple-500/10 to-transparent animate-gradient-slow"></div>
                        <div class="absolute bottom-0 right-0 w-2/3 h-2/3 bg-gradient-to-tl from-amber-500/10 via-transparent to-transparent animate-gradient-slower"></div>
                    </div>

                    <div class="relative p-8 md:p-12 text-center">
                        <!-- Icon -->
                        <div class="inline-flex items-center justify-center mb-6 group/icon">
                            <div class="absolute inset-0 bg-gradient-to-br from-blue-500/30 to-purple-500/30 rounded-3xl blur-2xl group-hover/icon:blur-3xl transition-all duration-500"></div>
                            <div class="relative w-20 h-20 rounded-3xl bg-gradient-to-br from-blue-500/20 via-purple-500/20 to-amber-500/20 border border-white/20 flex items-center justify-center transform group-hover/icon:scale-110 group-hover/icon:rotate-6 transition-all duration-500 shadow-2xl">
                                <span class="text-5xl">🏪</span>
                            </div>
                        </div>

                        <!-- Title -->
                        <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-4 animate-slide-up">
                            <span class="bg-gradient-to-br from-white via-gray-100 to-gray-400 bg-clip-text text-transparent">
                                Local Business
                            </span>
                            <br />
                            <span class="bg-gradient-to-r from-blue-400 via-purple-400 to-amber-400 bg-clip-text text-transparent">
                                Marketplace
                            </span>
                        </h1>
                        <p class="text-gray-400 text-lg max-w-2xl mx-auto mb-6 animate-slide-up" style="animation-delay: 0.1s">
                            Discover and support local businesses in your barangay
                        </p>

                        <!-- Quick Stats -->
                        <div class="flex flex-wrap items-center justify-center gap-4 animate-slide-up" style="animation-delay: 0.2s">
                            <div class="px-5 py-2.5 rounded-xl bg-white/5 backdrop-blur-md border border-white/10 hover:bg-white/10 transition-all duration-300 group/stat">
                                <div class="flex items-center gap-2">
                                    <span class="text-2xl">🏢</span>
                                    <div class="text-left">
                                        <p class="text-xs text-white/50 font-medium">Businesses</p>
                                        <p class="text-lg font-bold text-white">{{ statsData.total }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="px-5 py-2.5 rounded-xl bg-white/5 backdrop-blur-md border border-white/10 hover:bg-white/10 transition-all duration-300 group/stat">
                                <div class="flex items-center gap-2">
                                    <span class="text-2xl">✅</span>
                                    <div class="text-left">
                                        <p class="text-xs text-white/50 font-medium">Verified</p>
                                        <p class="text-lg font-bold text-white">{{ statsData.verified }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="px-5 py-2.5 rounded-xl bg-white/5 backdrop-blur-md border border-white/10 hover:bg-white/10 transition-all duration-300 group/stat">
                                <div class="flex items-center gap-2">
                                    <span class="text-2xl">👑</span>
                                    <div class="text-left">
                                        <p class="text-xs text-white/50 font-medium">Premium</p>
                                        <p class="text-lg font-bold text-white">{{ statsData.premium }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enhanced Filters Section -->
            <div class="relative group">
                <div class="absolute inset-0 bg-gradient-to-r from-blue-500/10 to-purple-500/10 rounded-[2rem] blur-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-700"></div>
                <div class="relative rounded-[2rem] bg-[#1c1c1e]/90 backdrop-blur-3xl border border-white/10 p-6 md:p-8 shadow-xl">
                    
                    <!-- Filter Header -->
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-xl font-bold text-white mb-1">Filters & Search</h3>
                            <p class="text-sm text-white/40">Find exactly what you're looking for</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <!-- View Toggle -->
                            <div class="flex gap-2 p-1 rounded-xl bg-white/5 border border-white/10">
                                <button 
                                    @click="viewMode = 'grid'"
                                    :class="[
                                        'p-2 rounded-lg transition-all duration-300',
                                        viewMode === 'grid' 
                                            ? 'bg-white/15 text-white shadow-lg' 
                                            : 'text-white/40 hover:text-white hover:bg-white/5'
                                    ]"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                                    </svg>
                                </button>
                                <button 
                                    @click="viewMode = 'list'"
                                    :class="[
                                        'p-2 rounded-lg transition-all duration-300',
                                        viewMode === 'list' 
                                            ? 'bg-white/15 text-white shadow-lg' 
                                            : 'text-white/40 hover:text-white hover:bg-white/5'
                                    ]"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                        <!-- Search -->
                        <div class="md:col-span-6 relative group/search">
                            <div class="absolute inset-0 bg-gradient-to-r from-blue-500/20 to-purple-500/20 rounded-2xl blur-xl opacity-0 group-hover/search:opacity-100 transition-all duration-500"></div>
                            <div class="relative flex items-center">
                                <svg class="absolute left-4 w-5 h-5 text-white/40 group-focus-within/search:text-blue-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                <input 
                                    v-model="searchQuery"
                                    @keyup.enter="applyFilters"
                                    type="text"
                                    placeholder="Search businesses, categories..."
                                    class="w-full h-14 pl-12 pr-4 bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl text-white placeholder-white/30 focus:bg-white/10 focus:border-blue-500/50 focus:ring-2 focus:ring-blue-500/20 transition-all duration-300"
                                />
                                <button 
                                    v-if="searchQuery"
                                    @click="searchQuery = ''; applyFilters();"
                                    class="absolute right-3 p-1.5 rounded-lg bg-white/10 hover:bg-white/20 text-white/60 hover:text-white transition-all"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Category Filter -->
                        <div class="md:col-span-3 relative group/cat">
                            <div class="absolute inset-0 bg-gradient-to-r from-purple-500/20 to-pink-500/20 rounded-2xl blur-xl opacity-0 group-hover/cat:opacity-100 transition-all duration-500"></div>
                            <div class="relative">
                                <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-white/40 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                                <select 
                                    v-model="selectedCategory"
                                    @change="applyFilters"
                                    class="w-full h-14 pl-12 pr-10 bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl text-white appearance-none cursor-pointer focus:bg-white/10 focus:border-purple-500/50 focus:ring-2 focus:ring-purple-500/20 transition-all duration-300"
                                >
                                    <option value="" class="bg-[#1c1c1e]">All Categories</option>
                                    <option v-for="cat in categories" :key="cat.value" :value="cat.value" class="bg-[#1c1c1e]">
                                        {{ cat.label }}
                                    </option>
                                </select>
                                <svg class="absolute right-4 top-1/2 -translate-y-1/2 w-5 h-5 text-white/40 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>

                        <!-- Status Filter -->
                        <div class="md:col-span-3 relative group/status">
                            <div class="absolute inset-0 bg-gradient-to-r from-emerald-500/20 to-cyan-500/20 rounded-2xl blur-xl opacity-0 group-hover/status:opacity-100 transition-all duration-500"></div>
                            <div class="relative">
                                <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-white/40 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <select 
                                    v-model="selectedStatus"
                                    @change="applyFilters"
                                    class="w-full h-14 pl-12 pr-10 bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl text-white appearance-none cursor-pointer focus:bg-white/10 focus:border-emerald-500/50 focus:ring-2 focus:ring-emerald-500/20 transition-all duration-300"
                                >
                                    <option value="" class="bg-[#1c1c1e]">All Status</option>
                                    <option v-for="status in statuses" :key="status.value" :value="status.value" class="bg-[#1c1c1e]">
                                        {{ status.label }}
                                    </option>
                                </select>
                                <svg class="absolute right-4 top-1/2 -translate-y-1/2 w-5 h-5 text-white/40 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Active Filters & Results -->
                    <div v-if="hasActiveFilters" class="flex flex-wrap items-center gap-3 mt-6 pt-6 border-t border-white/10">
                        <button 
                            @click="clearFilters"
                            class="px-4 py-2 rounded-xl bg-gradient-to-r from-rose-500/20 to-pink-500/20 text-rose-300 text-sm font-semibold border border-rose-500/30 hover:from-rose-500/30 hover:to-pink-500/30 transition-all duration-300 flex items-center gap-2 group/clear shadow-lg shadow-rose-500/10"
                        >
                            <svg class="w-4 h-4 group-hover/clear:rotate-90 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Clear All Filters
                        </button>
                        <div class="flex items-center gap-2 px-4 py-2 rounded-xl bg-white/5 border border-white/10">
                            <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <span class="text-sm font-medium text-white">{{ businesses.length }} businesses found</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Loading State -->
            <div v-if="isFiltering" class="flex items-center justify-center py-12">
                <div class="flex flex-col items-center gap-4">
                    <div class="relative w-16 h-16">
                        <div class="absolute inset-0 border-4 border-blue-500/30 rounded-full"></div>
                        <div class="absolute inset-0 border-4 border-blue-500 rounded-full border-t-transparent animate-spin"></div>
                    </div>
                    <p class="text-white/60 font-medium">Loading businesses...</p>
                </div>
            </div>

            <!-- Business Grid View -->
            <div v-else-if="businesses.length > 0 && viewMode === 'grid'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <a 
                    v-for="(business, index) in businesses" 
                    :key="business.id"
                    :href="route('marketplace.show', business.id)"
                    class="group relative overflow-hidden rounded-[2rem] bg-[#1c1c1e]/60 backdrop-blur-xl border border-white/10 transition-all duration-500 hover:scale-[1.02] hover:shadow-2xl cursor-pointer animate-fade-in-up"
                    :style="{ animationDelay: `${index * 0.05}s` }"
                >
                    <!-- Hover Gradient -->
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-500/10 via-purple-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-all duration-500"></div>

                    <!-- Shine Effect -->
                    <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none">
                        <div class="absolute inset-0 bg-gradient-to-tr from-transparent via-white/10 to-transparent transform -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                    </div>

                    <div class="relative p-6">
                        <!-- Badges Container -->
                        <div class="flex items-start justify-between mb-6">
                            <!-- Verified Badge -->
                            <div v-if="business.is_verified">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-gradient-to-r from-amber-500/20 via-yellow-500/20 to-amber-600/20 text-amber-200 border border-amber-500/40 shadow-lg shadow-amber-500/20 backdrop-blur-sm">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    VERIFIED
                                </span>
                            </div>

                            <!-- Tier Badge -->
                            <div class="ml-auto">
                                <span :class="`inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-gradient-to-r ${getTierBadge(business.tier).color} ${getTierBadge(business.tier).textColor} border ${getTierBadge(business.tier).borderColor} shadow-lg ${getTierBadge(business.tier).glow} backdrop-blur-sm`">
                                    <span class="text-base">{{ getTierBadge(business.tier).icon }}</span>
                                    {{ business.tier.toUpperCase() }}
                                </span>
                            </div>
                        </div>

                        <!-- Business Icon -->
                        <div class="relative mb-5 group/icon">
                            <div class="absolute inset-0 bg-gradient-to-br from-blue-500/30 to-purple-500/30 rounded-2xl blur-xl group-hover/icon:blur-2xl transition-all duration-500"></div>
                            <div class="relative w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-500/20 via-purple-500/15 to-transparent border border-white/10 flex items-center justify-center text-3xl shadow-xl transform group-hover/icon:scale-110 group-hover/icon:rotate-6 transition-all duration-500">
                                {{ getCategoryIcon(business.category) }}
                            </div>
                        </div>

                        <!-- Business Info -->
                        <h3 class="text-xl font-bold text-white mb-2 group-hover:text-transparent group-hover:bg-gradient-to-r group-hover:from-blue-400 group-hover:to-purple-400 group-hover:bg-clip-text transition-all duration-300 line-clamp-1">
                            {{ business.name }}
                        </h3>
                        <p class="text-sm text-gray-400 mb-5 line-clamp-2 leading-relaxed">
                            {{ business.description }}
                        </p>

                        <!-- Status & Stats -->
                        <div class="flex items-center justify-between mb-5 pb-5 border-b border-white/10">
                            <span :class="`inline-flex items-center px-3 py-1.5 text-xs font-bold rounded-xl border shadow-lg ${getStatusColor(business.status)}`">
                                <span class="w-1.5 h-1.5 rounded-full bg-current mr-2 animate-pulse"></span>
                                {{ business.status }}
                            </span>
                            <div class="flex items-center gap-4 text-sm">
                                <span class="flex items-center gap-1.5 text-amber-300">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                    {{ business.rating }}
                                </span>
                                <span class="flex items-center gap-1.5 text-rose-300">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ business.likes_count }}
                                </span>
                            </div>
                        </div>

                        <!-- Owner -->
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center text-white font-bold text-sm">
                                {{ business.owner_name.charAt(0) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs text-gray-500">Owned by</p>
                                <p class="text-sm text-gray-300 font-semibold truncate">{{ business.owner_name }}</p>
                            </div>
                            <svg class="w-5 h-5 text-white/20 group-hover:text-white/60 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Business List View -->
            <div v-else-if="businesses.length > 0 && viewMode === 'list'" class="space-y-4">
                <a 
                    v-for="(business, index) in businesses" 
                    :key="business.id"
                    :href="route('marketplace.show', business.id)"
                    class="group relative overflow-hidden rounded-[2rem] bg-[#1c1c1e]/60 backdrop-blur-xl border border-white/10 transition-all duration-300 hover:bg-[#2c2c2e] hover:shadow-xl cursor-pointer animate-fade-in-up"
                    :style="{ animationDelay: `${index * 0.03}s` }"
                >
                    <div class="absolute inset-0 bg-gradient-to-r from-blue-500/5 via-purple-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>

                    <div class="relative p-6 flex items-center gap-6">
                        <!-- Icon -->
                        <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-blue-500/20 to-purple-500/20 border border-white/10 flex items-center justify-center text-4xl flex-shrink-0 transform group-hover:scale-110 transition-transform shadow-xl">
                            {{ getCategoryIcon(business.category) }}
                        </div>

                        <!-- Content -->
                        <div class="flex-1 grid grid-cols-1 md:grid-cols-12 gap-6 items-center">
                            <!-- Info -->
                            <div class="md:col-span-5">
                                <div class="flex items-center gap-2 mb-2">
                                    <h3 class="text-xl font-bold text-white group-hover:text-transparent group-hover:bg-gradient-to-r group-hover:from-blue-400 group-hover:to-purple-400 group-hover:bg-clip-text transition-all">
                                        {{ business.name }}
                                    </h3>
                                    <span v-if="business.is_verified" class="text-amber-400">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                    </span>
                                </div>
                                <p class="text-sm text-gray-400 line-clamp-2">{{ business.description }}</p>
                            </div>

                            <!-- Stats -->
                            <div class="md:col-span-3 flex items-center gap-4">
                                <div class="flex items-center gap-2 text-sm text-amber-300">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                    {{ business.rating }}
                                </div>
                                <div class="flex items-center gap-2 text-sm text-rose-300">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ business.likes_count }}
                                </div>
                            </div>

                            <!-- Badges -->
                            <div class="md:col-span-4 flex items-center justify-end gap-3">
                                <span :class="`px-3 py-1.5 text-xs font-bold rounded-xl border ${getStatusColor(business.status)}`">
                                    {{ business.status }}
                                </span>
                                <span :class="`inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-gradient-to-r ${getTierBadge(business.tier).color} ${getTierBadge(business.tier).textColor} border ${getTierBadge(business.tier).borderColor}`">
                                    {{ getTierBadge(business.tier).icon }}
                                    {{ business.tier.toUpperCase() }}
                                </span>
                                <svg class="w-6 h-6 text-white/20 group-hover:text-white/60 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Enhanced Empty State -->
            <div v-else-if="!isFiltering" class="relative group">
                <div class="absolute inset-0 bg-gradient-to-br from-gray-500/10 to-gray-600/10 rounded-[2.5rem] blur-2xl"></div>
                <div class="relative rounded-[2.5rem] bg-[#1c1c1e]/60 backdrop-blur-xl border border-dashed border-white/10 p-16 text-center">
                    <!-- Icon -->
                    <div class="relative inline-flex items-center justify-center mb-6">
                        <div class="absolute inset-0 bg-gradient-to-br from-gray-500/20 to-gray-600/20 rounded-full blur-2xl"></div>
                        <div class="relative w-24 h-24 rounded-full bg-white/5 flex items-center justify-center">
                            <span class="text-6xl opacity-40">🔍</span>
                        </div>
                    </div>

                    <h3 class="text-2xl font-bold text-white mb-3">No Businesses Found</h3>
                    <p class="text-gray-400 mb-8 max-w-md mx-auto">
                        We couldn't find any businesses matching your criteria. Try adjusting your filters or search terms.
                    </p>
                    
                    <button 
                        @click="clearFilters"
                        class="relative group/btn inline-flex items-center gap-2 px-8 py-4 rounded-xl bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white font-semibold transition-all duration-300 active:scale-95 shadow-xl shadow-blue-500/20"
                    >
                        <svg class="w-5 h-5 group-hover/btn:rotate-90 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Clear All Filters
                    </button>
                </div>
            </div>

        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
/* Enhanced Animations */
@keyframes drift {
    0%, 100% { transform: translate(0, 0) scale(1); }
    33% { transform: translate(30px, -30px) scale(1.1); }
    66% { transform: translate(-20px, 20px) scale(0.9); }
}

@keyframes drift-delayed {
    0%, 100% { transform: translate(0, 0) scale(1); }
    33% { transform: translate(-30px, 30px) scale(0.9); }
    66% { transform: translate(20px, -20px) scale(1.1); }
}

@keyframes drift-slow {
    0%, 100% { transform: translate(0, 0) rotate(0deg); }
    50% { transform: translate(15px, -15px) rotate(5deg); }
}

@keyframes gradient-slow {
    0%, 100% { transform: translate(0, 0) scale(1); opacity: 0.3; }
    50% { transform: translate(-10px, 10px) scale(1.1); opacity: 0.5; }
}

@keyframes gradient-slower {
    0%, 100% { transform: translate(0, 0) scale(1); opacity: 0.3; }
    50% { transform: translate(10px, -10px) scale(1.05); opacity: 0.4; }
}

.animate-drift { animation: drift 25s ease-in-out infinite; }
.animate-drift-delayed { animation: drift-delayed 30s ease-in-out infinite; }
.animate-drift-slow { animation: drift-slow 35s ease-in-out infinite; }
.animate-gradient-slow { animation: gradient-slow 20s ease-in-out infinite; }
.animate-gradient-slower { animation: gradient-slower 25s ease-in-out infinite; }

@keyframes slide-up {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}

.animate-slide-up { animation: slide-up 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; }

@keyframes fade-in-up {
    from { opacity: 0; transform: translateY(20px) scale(0.95); }
    to { opacity: 1; transform: translateY(0) scale(1); }
}

.animate-fade-in-up { animation: fade-in-up 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards; }

/* Reduce motion */
@media (prefers-reduced-motion: reduce) {
    *,
    *::before,
    *::after {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
}

/* Focus visible for accessibility */
button:focus-visible,
input:focus-visible,
select:focus-visible,
a:focus-visible {
    outline: 2px solid rgb(59, 130, 246);
    outline-offset: 2px;
}
</style>