<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    businesses: Array,
    filters: Object,
    categories: Array,
    statuses: Array,
});

const searchQuery = ref(props.filters.search || '');
const selectedCategory = ref(props.filters.category || '');
const selectedStatus = ref(props.filters.status || '');

const applyFilters = () => {
    router.get(route('marketplace.index'), {
        search: searchQuery.value,
        category: selectedCategory.value,
        status: selectedStatus.value,
    }, {
        preserveState: true,
        preserveScroll: true,
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
        'available': 'bg-green-500/20 text-green-300 border-green-500/30',
        'open': 'bg-blue-500/20 text-blue-300 border-blue-500/30',
        'closed': 'bg-gray-500/20 text-gray-300 border-gray-500/30',
        'full': 'bg-red-500/20 text-red-300 border-red-500/30'
    };
    return colors[status] || 'bg-gray-500/20 text-gray-300';
};

const getTierBadge = (tier) => {
    const badges = {
        'premium': { icon: '👑', color: 'from-amber-500/20 to-yellow-500/20 border-amber-500/30 text-amber-300' },
        'standard': { icon: '🚀', color: 'from-purple-500/20 to-purple-600/20 border-purple-500/30 text-purple-300' },
        'basic': { icon: '🎯', color: 'from-blue-500/20 to-blue-600/20 border-blue-500/30 text-blue-300' }
    };
    return badges[tier] || badges.basic;
};
</script>

<template>
    <Head title="Marketplace" />

    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto space-y-6">
            
            <!-- Header -->
            <div class="relative overflow-hidden rounded-3xl bg-[#121212]/70 backdrop-blur-2xl border border-white/10 shadow-xl">
                <div class="absolute inset-0 bg-gradient-to-br from-blue-500/10 via-purple-500/10 to-amber-500/10"></div>
                <div class="relative p-8 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-500/20 to-purple-500/20 border border-white/10 mb-4">
                        <span class="text-3xl">🏪</span>
                    </div>
                    <h1 class="text-4xl font-bold bg-gradient-to-br from-white via-gray-200 to-gray-400 bg-clip-text text-transparent mb-3">
                        Local Business Marketplace
                    </h1>
                    <p class="text-gray-400 max-w-2xl mx-auto">
                        Discover and support local businesses in your barangay
                    </p>
                </div>
            </div>

            <!-- Filters -->
            <div class="rounded-2xl bg-[#121212]/70 backdrop-blur-2xl border border-white/10 p-6 shadow-lg">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Search -->
                    <div class="md:col-span-2">
                        <input 
                            v-model="searchQuery"
                            @keyup.enter="applyFilters"
                            type="text"
                            placeholder="Search businesses..."
                            class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-gray-500 focus:bg-white/10 focus:border-blue-500/50 focus:ring-2 focus:ring-blue-500/20 transition-all"
                        />
                    </div>

                    <!-- Category Filter -->
                    <select 
                        v-model="selectedCategory"
                        @change="applyFilters"
                        class="px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white focus:bg-white/10 focus:border-blue-500/50 focus:ring-2 focus:ring-blue-500/20 transition-all"
                    >
                        <option value="">All Categories</option>
                        <option v-for="cat in categories" :key="cat.value" :value="cat.value">
                            {{ cat.label }}
                        </option>
                    </select>

                    <!-- Status Filter -->
                    <select 
                        v-model="selectedStatus"
                        @change="applyFilters"
                        class="px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white focus:bg-white/10 focus:border-blue-500/50 focus:ring-2 focus:ring-blue-500/20 transition-all"
                    >
                        <option value="">All Status</option>
                        <option v-for="status in statuses" :key="status.value" :value="status.value">
                            {{ status.label }}
                        </option>
                    </select>
                </div>

                <div v-if="filters.search || filters.category || filters.status" class="flex items-center gap-2 mt-4">
                    <button 
                        @click="clearFilters"
                        class="px-4 py-2 rounded-full bg-white/5 text-gray-300 text-sm border border-white/10 hover:bg-white/10 transition-all"
                    >
                        Clear Filters
                    </button>
                    <span class="text-sm text-gray-400">{{ businesses.length }} businesses found</span>
                </div>
            </div>

            <!-- Business Grid -->
            <div v-if="businesses.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <a 
                    v-for="business in businesses" 
                    :key="business.id"
                    :href="route('marketplace.show', business.id)"
                    class="group relative overflow-hidden rounded-3xl bg-[#121212]/70 backdrop-blur-2xl border border-white/10 transition-all duration-500 hover:scale-[1.02] hover:shadow-[0_0_40px_rgba(59,130,246,0.3)] cursor-pointer"
                >
                    <!-- Tier Badge -->
                    <div class="absolute top-4 right-4 z-10">
                        <span :class="`inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-gradient-to-r ${getTierBadge(business.tier).color} border shadow-lg`">
                            {{ getTierBadge(business.tier).icon }}
                            {{ business.tier.toUpperCase() }}
                        </span>
                    </div>

                    <!-- Verified Badge -->
                    <div v-if="business.is_verified" class="absolute top-4 left-4 z-10">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold bg-gradient-to-r from-amber-500/20 to-yellow-500/20 text-amber-300 border border-amber-500/30 shadow-lg">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            VERIFIED
                        </span>
                    </div>

                    <div class="p-6 pt-16">
                        <!-- Business Icon -->
                        <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-500/20 to-purple-500/20 border border-white/10 flex items-center justify-center text-3xl mb-4">
                            {{ business.category === 'retail' ? '🛍️' : '🏪' }}
                        </div>

                        <!-- Business Info -->
                        <h3 class="text-xl font-bold text-white mb-2 group-hover:text-blue-400 transition-colors">
                            {{ business.name }}
                        </h3>
                        <p class="text-sm text-gray-400 mb-4 line-clamp-2">
                            {{ business.description }}
                        </p>

                        <!-- Status & Stats -->
                        <div class="flex items-center justify-between mb-4">
                            <span :class="`inline-flex px-3 py-1 text-xs font-semibold rounded-full border ${getStatusColor(business.status)}`">
                                {{ business.status }}
                            </span>
                            <div class="flex items-center gap-3 text-xs text-gray-400">
                                <span class="flex items-center gap-1">
                                    ⭐ {{ business.rating }}
                                </span>
                                <span class="flex items-center gap-1">
                                    ❤️ {{ business.likes_count }}
                                </span>
                            </div>
                        </div>

                        <!-- Owner -->
                        <div class="pt-4 border-t border-white/10">
                            <p class="text-xs text-gray-500">Owned by <span class="text-gray-300">{{ business.owner_name }}</span></p>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Empty State -->
            <div v-else class="rounded-3xl bg-[#121212]/70 backdrop-blur-2xl border border-white/10 p-12 text-center">
                <div class="text-6xl mb-4">🔍</div>
                <h3 class="text-xl font-bold text-white mb-2">No Businesses Found</h3>
                <p class="text-gray-400 mb-6">Try adjusting your filters or search terms</p>
                <button 
                    @click="clearFilters"
                    class="px-6 py-3 rounded-full bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold transition-all duration-300 active:scale-95"
                >
                    Clear All Filters
                </button>
            </div>

        </div>
    </AuthenticatedLayout>
</template>