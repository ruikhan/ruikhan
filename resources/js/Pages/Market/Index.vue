<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    marketPrices: Object,
    auth: Object
});

const form = useForm({
    item_name: '',
    category: 'Fish',
    price: '',
    unit: 'kg',
});

const isSubmitting = ref(false);
const selectedCategory = ref('all');
const searchQuery = ref('');
const viewMode = ref('grid'); // 'grid' or 'list'

const submit = () => {
    isSubmitting.value = true;
    form.post(route('market.store'), {
        onSuccess: () => {
            form.reset();
            isSubmitting.value = false;
        },
        onError: () => {
            isSubmitting.value = false;
        }
    });
};

// Enhanced Category Details with more gradients
const getCategoryDetails = (cat) => {
    const map = {
        'Fish': { 
            color: 'bg-cyan-500', 
            icon: '🐟', 
            gradient: 'from-cyan-500/20 via-blue-500/10 to-transparent',
            borderGlow: 'shadow-cyan-500/20',
            textGradient: 'from-cyan-400 to-blue-500'
        },
        'Meat': { 
            color: 'bg-rose-500', 
            icon: '🥩', 
            gradient: 'from-rose-500/20 via-red-500/10 to-transparent',
            borderGlow: 'shadow-rose-500/20',
            textGradient: 'from-rose-400 to-red-500'
        },
        'Vegetables': { 
            color: 'bg-emerald-500', 
            icon: '🥬', 
            gradient: 'from-emerald-500/20 via-green-500/10 to-transparent',
            borderGlow: 'shadow-emerald-500/20',
            textGradient: 'from-emerald-400 to-green-500'
        },
        'Fruits': { 
            color: 'bg-orange-500', 
            icon: '🍊', 
            gradient: 'from-orange-500/20 via-yellow-500/10 to-transparent',
            borderGlow: 'shadow-orange-500/20',
            textGradient: 'from-orange-400 to-yellow-500'
        },
        'Spices': { 
            color: 'bg-amber-500', 
            icon: '🌶️', 
            gradient: 'from-amber-500/20 via-orange-500/10 to-transparent',
            borderGlow: 'shadow-amber-500/20',
            textGradient: 'from-amber-400 to-orange-500'
        },
    };
    return map[cat] || { 
        color: 'bg-gray-500', 
        icon: '📦', 
        gradient: 'from-gray-500/20 to-transparent',
        borderGlow: 'shadow-gray-500/20',
        textGradient: 'from-gray-400 to-gray-500'
    };
};

const getTrendColor = (trend) => {
    if (trend === 'up') return 'text-rose-400 bg-rose-500/10 border-rose-500/30 shadow-rose-500/20';
    if (trend === 'down') return 'text-emerald-400 bg-emerald-500/10 border-emerald-500/30 shadow-emerald-500/20';
    return 'text-gray-400 bg-white/5 border-white/10';
};

// Computed filtered items
const filteredMarketPrices = computed(() => {
    let filtered = { ...props.marketPrices };
    
    if (selectedCategory.value !== 'all') {
        filtered = { [selectedCategory.value]: filtered[selectedCategory.value] };
    }
    
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        Object.keys(filtered).forEach(category => {
            filtered[category] = filtered[category].filter(item => 
                item.item_name.toLowerCase().includes(query)
            );
        });
    }
    
    return filtered;
});

// Get all categories
const categories = computed(() => {
    return ['all', ...Object.keys(props.marketPrices)];
});

// Total items count
const totalItems = computed(() => {
    return Object.values(props.marketPrices).reduce((sum, items) => sum + items.length, 0);
});
</script>

<template>
    <Head title="Market Watch" />

    <AuthenticatedLayout>
        <!-- Enhanced Background -->
        <div class="fixed inset-0 z-0 bg-[#000000]">
            <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-[0.03]"></div>
            <div class="absolute top-[-20%] left-[20%] w-[1000px] h-[600px] bg-blue-600/15 rounded-[100%] blur-[150px] mix-blend-screen animate-drift"></div>
            <div class="absolute bottom-[-10%] right-[10%] w-[800px] h-[600px] bg-purple-600/15 rounded-[100%] blur-[150px] mix-blend-screen animate-drift-delayed"></div>
            <div class="absolute top-[40%] left-[50%] w-[600px] h-[600px] bg-cyan-600/10 rounded-[100%] blur-[120px] mix-blend-screen animate-drift-slow"></div>
        </div>

        <!-- Premium Header -->
        <template #header>
            <div class="relative z-10 px-4 py-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                    <!-- Title Section -->
                    <div class="flex items-center gap-4">
                        <div class="relative group">
                            <div class="absolute inset-0 bg-gradient-to-br from-blue-500/30 to-purple-500/30 rounded-2xl blur-xl group-hover:blur-2xl transition-all duration-500"></div>
                            <div class="relative w-14 h-14 rounded-2xl bg-gradient-to-br from-gray-800 to-black border border-white/10 flex items-center justify-center shadow-2xl transform group-hover:scale-105 transition-transform duration-300">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <h2 class="text-3xl md:text-4xl font-bold bg-gradient-to-r from-white via-gray-100 to-gray-300 bg-clip-text text-transparent tracking-tight">
                                Market Watch
                            </h2>
                            <p class="text-white/40 text-sm font-medium tracking-wide mt-1">Real-time commodity prices</p>
                        </div>
                    </div>
                    
                    <!-- Stats Pills -->
                    <div class="flex flex-wrap gap-3">
                        <div class="px-4 py-2 rounded-xl bg-white/5 backdrop-blur-md border border-white/10 hover:bg-white/10 transition-all duration-300 group">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse shadow-lg shadow-emerald-500/50"></span>
                                <span class="text-xs text-white/60 font-medium">Live</span>
                            </div>
                        </div>
                        <div class="px-4 py-2 rounded-xl bg-white/5 backdrop-blur-md border border-white/10 hover:bg-white/10 transition-all duration-300">
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-white/40 font-medium">Items:</span>
                                <span class="text-sm text-white font-bold">{{ totalItems }}</span>
                            </div>
                        </div>
                        <div class="hidden md:flex px-4 py-2 rounded-xl bg-white/5 backdrop-blur-md border border-white/10 hover:bg-white/10 transition-all duration-300">
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-white/40 font-medium">Location:</span>
                                <span class="text-sm text-white font-semibold">Pili, CamSur</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Search & Filter Bar -->
                <div class="mt-6 grid grid-cols-1 md:grid-cols-12 gap-3">
                    <!-- Search -->
                    <div class="md:col-span-5 relative group">
                        <div class="absolute inset-0 bg-gradient-to-r from-blue-500/20 to-purple-500/20 rounded-2xl blur-xl opacity-0 group-hover:opacity-100 transition-all duration-500"></div>
                        <div class="relative flex items-center">
                            <svg class="absolute left-4 w-5 h-5 text-white/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <input 
                                v-model="searchQuery"
                                type="text" 
                                placeholder="Search items..." 
                                class="w-full h-12 pl-12 pr-4 bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl text-white placeholder-white/30 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50 transition-all duration-300 hover:bg-white/10"
                            >
                        </div>
                    </div>

                    <!-- Category Filter -->
                    <div class="md:col-span-5 flex gap-2 overflow-x-auto scrollbar-hide pb-2 md:pb-0">
                        <button 
                            v-for="cat in categories" 
                            :key="cat"
                            @click="selectedCategory = cat"
                            :class="[
                                'px-4 py-2 rounded-xl font-semibold text-sm whitespace-nowrap transition-all duration-300 flex-shrink-0',
                                selectedCategory === cat 
                                    ? 'bg-gradient-to-r from-blue-500 to-purple-600 text-white shadow-lg shadow-blue-500/30 scale-105' 
                                    : 'bg-white/5 text-white/60 hover:bg-white/10 hover:text-white border border-white/10'
                            ]"
                        >
                            <span v-if="cat !== 'all'" class="mr-1">{{ getCategoryDetails(cat).icon }}</span>
                            {{ cat === 'all' ? 'All' : cat }}
                        </button>
                    </div>

                    <!-- View Toggle -->
                    <div class="md:col-span-2 flex gap-2">
                        <button 
                            @click="viewMode = 'grid'"
                            :class="[
                                'flex-1 h-12 rounded-xl font-semibold transition-all duration-300',
                                viewMode === 'grid' 
                                    ? 'bg-white/10 text-white border border-white/20' 
                                    : 'bg-white/5 text-white/40 hover:bg-white/10 hover:text-white border border-white/10'
                            ]"
                        >
                            <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                            </svg>
                        </button>
                        <button 
                            @click="viewMode = 'list'"
                            :class="[
                                'flex-1 h-12 rounded-xl font-semibold transition-all duration-300',
                                viewMode === 'list' 
                                    ? 'bg-white/10 text-white border border-white/20' 
                                    : 'bg-white/5 text-white/40 hover:bg-white/10 hover:text-white border border-white/10'
                            ]"
                        >
                            <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </template>

        <!-- Main Content -->
        <div class="relative z-10 py-8 max-w-[1800px] mx-auto px-4 lg:px-8">
            <div class="grid grid-cols-1 xl:grid-cols-12 gap-8">
                
                <!-- Enhanced Sidebar Form -->
                <div class="xl:col-span-3">
                    <div class="sticky top-8 space-y-6">
                        <!-- New Entry Form -->
                        <div class="relative group">
                            <div class="absolute inset-0 bg-gradient-to-br from-blue-500/20 via-purple-500/20 to-pink-500/20 rounded-[2rem] blur-2xl opacity-0 group-hover:opacity-100 transition-all duration-700"></div>
                            <div class="relative bg-[#1c1c1e]/90 backdrop-blur-3xl border border-white/10 rounded-[2rem] p-6 shadow-2xl animate-slide-up hover:shadow-blue-500/10 transition-all duration-500">
                                <!-- Form Header -->
                                <div class="flex items-center justify-between mb-6">
                                    <div>
                                        <h3 class="text-lg font-bold text-white">New Entry</h3>
                                        <p class="text-xs text-white/40 mt-1">Update market prices</p>
                                    </div>
                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500/20 to-purple-500/20 flex items-center justify-center text-white/70 hover:scale-110 transition-transform duration-300">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                    </div>
                                </div>

                                <form @submit.prevent="submit" class="space-y-5">
                                    <!-- Item Name -->
                                    <div class="space-y-2 group">
                                        <label class="text-[10px] font-bold text-white/40 uppercase tracking-wider pl-1 group-focus-within:text-blue-400 transition-colors">
                                            Item Name
                                        </label>
                                        <div class="relative">
                                            <input 
                                                v-model="form.item_name" 
                                                type="text" 
                                                placeholder="e.g. Bangus, Tilapia..." 
                                                required
                                                class="w-full bg-black/30 border border-white/10 focus:border-blue-500/50 rounded-xl text-white placeholder-white/20 h-12 px-4 transition-all duration-300 hover:bg-black/40 focus:bg-black/40 focus:ring-2 focus:ring-blue-500/20"
                                            >
                                            <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-blue-500/0 via-blue-500/5 to-blue-500/0 opacity-0 group-focus-within:opacity-100 transition-opacity pointer-events-none"></div>
                                        </div>
                                    </div>

                                    <!-- Category & Unit -->
                                    <div class="grid grid-cols-2 gap-3">
                                        <div class="space-y-2">
                                            <label class="text-[10px] font-bold text-white/40 uppercase tracking-wider pl-1">Category</label>
                                            <div class="relative">
                                                <select 
                                                    v-model="form.category" 
                                                    class="w-full bg-black/30 border border-white/10 focus:border-blue-500/50 rounded-xl text-white h-12 px-3 pr-10 appearance-none cursor-pointer hover:bg-black/40 transition-all duration-300 focus:ring-2 focus:ring-blue-500/20"
                                                >
                                                    <option value="Fish">🐟 Fish</option>
                                                    <option value="Meat">🥩 Meat</option>
                                                    <option value="Vegetables">🥬 Vegetables</option>
                                                    <option value="Fruits">🍊 Fruits</option>
                                                    <option value="Spices">🌶️ Spices</option>
                                                </select>
                                                <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-white/40">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="space-y-2">
                                            <label class="text-[10px] font-bold text-white/40 uppercase tracking-wider pl-1">Unit</label>
                                            <select 
                                                v-model="form.unit" 
                                                class="w-full bg-black/30 border border-white/10 focus:border-blue-500/50 rounded-xl text-white h-12 px-3 text-sm appearance-none hover:bg-black/40 transition-all duration-300 focus:ring-2 focus:ring-blue-500/20"
                                            >
                                                <option value="kg">Per kg</option>
                                                <option value="pc">Per pc</option>
                                                <option value="bundle">Bundle</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Price Input -->
                                    <div class="space-y-2 group">
                                        <label class="text-[10px] font-bold text-white/40 uppercase tracking-wider pl-1 group-focus-within:text-emerald-400 transition-colors">
                                            Market Price
                                        </label>
                                        <div class="relative">
                                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-2xl font-bold text-white/30">₱</span>
                                            <input 
                                                v-model="form.price" 
                                                type="number" 
                                                step="0.01" 
                                                placeholder="0.00" 
                                                required
                                                class="w-full bg-black/30 border border-white/10 focus:border-emerald-500/50 rounded-xl text-white placeholder-white/20 h-16 pl-10 pr-4 text-3xl font-mono text-right transition-all duration-300 hover:bg-black/40 focus:bg-black/40 focus:ring-2 focus:ring-emerald-500/20"
                                            >
                                            <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-emerald-500/0 via-emerald-500/5 to-emerald-500/0 opacity-0 group-focus-within:opacity-100 transition-opacity pointer-events-none"></div>
                                        </div>
                                    </div>

                                    <!-- Submit Button -->
                                    <button 
                                        type="submit" 
                                        :disabled="isSubmitting"
                                        class="relative w-full h-14 mt-4 bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500 text-white font-bold rounded-xl overflow-hidden group disabled:opacity-50 disabled:cursor-not-allowed"
                                    >
                                        <div class="absolute inset-0 bg-gradient-to-r from-pink-500 via-purple-500 to-blue-500 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                                        <div class="absolute inset-0 bg-white/20 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-700"></div>
                                        <span class="relative flex items-center justify-center gap-2">
                                            <span v-if="!isSubmitting">Update Market</span>
                                            <span v-else>Updating...</span>
                                            <svg v-if="!isSubmitting" class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                            </svg>
                                            <svg v-else class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                        </span>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Market Insight Card -->
                        <div class="relative group">
                            <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/30 to-blue-600/30 rounded-[2rem] blur-2xl opacity-75 group-hover:opacity-100 transition-opacity duration-700"></div>
                            <div class="relative bg-gradient-to-br from-indigo-600/90 to-blue-700/90 backdrop-blur-xl rounded-[2rem] p-6 shadow-xl overflow-hidden">
                                <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20 mix-blend-overlay"></div>
                                <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full blur-3xl"></div>
                                <div class="relative z-10">
                                    <div class="flex items-center gap-2 mb-3">
                                        <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                            </svg>
                                        </div>
                                        <span class="text-blue-100 text-xs font-bold uppercase tracking-widest">Market Trend</span>
                                    </div>
                                    <h4 class="text-white text-2xl font-bold mb-2">Volatile</h4>
                                    <p class="text-blue-100/80 text-sm leading-relaxed">Prices for vegetables are fluctuating due to recent weather conditions.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Stats -->
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-4 hover:bg-white/10 transition-all duration-300">
                                <p class="text-white/40 text-xs font-medium mb-1">Avg Change</p>
                                <p class="text-white text-2xl font-bold">+2.3%</p>
                            </div>
                            <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-4 hover:bg-white/10 transition-all duration-300">
                                <p class="text-white/40 text-xs font-medium mb-1">Updated</p>
                                <p class="text-white text-2xl font-bold">Today</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Enhanced Content Area -->
                <div class="xl:col-span-9 space-y-10">
                    
                    <!-- Empty State -->
                    <div v-if="Object.keys(filteredMarketPrices).length === 0" class="flex flex-col items-center justify-center h-96 bg-white/5 backdrop-blur-xl rounded-[2.5rem] border border-dashed border-white/10">
                        <div class="relative mb-6">
                            <div class="absolute inset-0 bg-gradient-to-br from-blue-500/20 to-purple-500/20 rounded-full blur-2xl"></div>
                            <div class="relative w-24 h-24 rounded-full bg-white/5 flex items-center justify-center">
                                <svg class="w-12 h-12 text-white/20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                        </div>
                        <span class="text-white/30 font-medium text-lg mb-2">No items found</span>
                        <span class="text-white/20 text-sm">Try adjusting your filters</span>
                    </div>

                    <!-- Category Sections -->
                    <div v-for="(items, category) in filteredMarketPrices" :key="category" class="animate-slide-up" :style="{ animationDelay: `${Object.keys(filteredMarketPrices).indexOf(category) * 0.1}s` }">
                        
                        <!-- Enhanced Category Header -->
                        <div class="relative group mb-8">
                            <div class="flex items-center gap-4 sticky top-0 py-6 z-20 backdrop-blur-xl -mx-4 px-4 rounded-2xl">
                                <!-- Glow Effect -->
                                <div class="absolute inset-0 bg-gradient-to-r opacity-0 group-hover:opacity-100 transition-opacity duration-500 rounded-2xl blur-xl"
                                     :class="`${getCategoryDetails(category).gradient}`"></div>
                                
                                <!-- Icon Badge -->
                                <div class="relative">
                                    <div class="absolute inset-0 rounded-2xl blur-lg" 
                                         :class="`${getCategoryDetails(category).color} opacity-30`"></div>
                                    <div class="relative w-14 h-14 rounded-2xl bg-gradient-to-br border flex items-center justify-center text-2xl transform group-hover:scale-110 transition-transform duration-300"
                                         :class="`${getCategoryDetails(category).gradient} ${getCategoryDetails(category).color}/20 border-white/10`">
                                        {{ getCategoryDetails(category).icon }}
                                    </div>
                                </div>
                                
                                <!-- Title -->
                                <div class="flex-1">
                                    <h3 class="text-3xl font-bold bg-gradient-to-r bg-clip-text text-transparent"
                                        :class="`${getCategoryDetails(category).textGradient}`">
                                        {{ category }}
                                    </h3>
                                    <p class="text-white/40 text-sm mt-1">{{ items.length }} items available</p>
                                </div>
                                
                                <!-- Decorative Line -->
                                <div class="hidden md:block h-px flex-1 bg-gradient-to-r from-white/10 via-white/5 to-transparent"></div>
                            </div>
                        </div>

                        <!-- Grid View -->
                        <div v-if="viewMode === 'grid'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                            <div v-for="(item, index) in items" :key="item.id" 
                                class="group relative bg-[#1c1c1e]/60 backdrop-blur-xl border border-white/10 rounded-[1.75rem] p-5 hover:bg-[#2c2c2e] transition-all duration-500 hover:-translate-y-2 hover:shadow-2xl overflow-hidden"
                                :style="{ animationDelay: `${index * 0.05}s` }"
                                :class="`animate-fade-in-up hover:${getCategoryDetails(category).borderGlow}`">
                                
                                <!-- Hover Gradient Overlay -->
                                <div class="absolute inset-0 bg-gradient-to-br opacity-0 group-hover:opacity-100 transition-all duration-500 rounded-[1.75rem]"
                                     :class="getCategoryDetails(category).gradient"></div>

                                <!-- Card Content -->
                                <div class="relative z-10 flex flex-col h-full justify-between">
                                    
                                    <!-- Header -->
                                    <div class="flex justify-between items-start mb-4">
                                        <!-- Icon -->
                                        <div class="relative">
                                            <div class="absolute inset-0 bg-white/5 rounded-xl blur group-hover:bg-white/10 transition-colors"></div>
                                            <div class="relative w-12 h-12 rounded-xl bg-white/5 border border-white/5 flex items-center justify-center text-2xl shadow-inner transform group-hover:scale-110 group-hover:rotate-6 transition-all duration-300">
                                                {{ getCategoryDetails(category).icon }}
                                            </div>
                                        </div>
                                        
                                        <!-- Trend Badge -->
                                        <div class="px-3 py-1.5 rounded-lg border text-[10px] font-bold uppercase tracking-wide backdrop-blur-md flex items-center gap-1.5 shadow-lg transform group-hover:scale-105 transition-transform"
                                             :class="getTrendColor(item.trend)">
                                            <svg v-if="item.trend === 'up'" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                            </svg>
                                            <svg v-else-if="item.trend === 'down'" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                                            </svg>
                                            <span v-else class="text-lg leading-none">-</span>
                                            {{ item.trend === 'up' ? 'High' : item.trend === 'down' ? 'Low' : 'Stable' }}
                                        </div>
                                    </div>

                                    <!-- Item Details -->
                                    <div class="flex-1">
                                        <h4 class="text-lg font-bold text-white mb-2 truncate group-hover:text-transparent group-hover:bg-gradient-to-r group-hover:bg-clip-text transition-all duration-300"
                                            :class="`group-hover:${getCategoryDetails(category).textGradient}`">
                                            {{ item.item_name }}
                                        </h4>
                                        <div class="flex items-baseline gap-1.5">
                                            <span class="text-xs text-white/40 align-top mt-1.5">₱</span>
                                            <span class="text-4xl font-mono font-medium text-white tracking-tight">{{ item.price }}</span>
                                            <span class="text-sm text-white/40 font-medium ml-1">/{{ item.unit }}</span>
                                        </div>
                                    </div>

                                    <!-- Footer -->
                                    <div class="mt-6 pt-4 border-t border-white/5 flex justify-between items-center">
                                        <div class="flex items-center gap-2">
                                            <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></div>
                                            <span class="text-[10px] text-white/30 font-medium">Updated today</span>
                                        </div>
                                        <button class="opacity-0 group-hover:opacity-100 transition-opacity text-xs font-semibold text-transparent bg-gradient-to-r bg-clip-text flex items-center gap-1"
                                                :class="getCategoryDetails(category).textGradient">
                                            History
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <!-- Shine Effect -->
                                <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none">
                                    <div class="absolute inset-0 bg-gradient-to-tr from-transparent via-white/5 to-transparent transform -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                                </div>
                            </div>
                        </div>

                        <!-- List View -->
                        <div v-else class="space-y-3">
                            <div v-for="(item, index) in items" :key="item.id"
                                class="group relative bg-[#1c1c1e]/60 backdrop-blur-xl border border-white/10 rounded-2xl p-5 hover:bg-[#2c2c2e] transition-all duration-300 overflow-hidden"
                                :style="{ animationDelay: `${index * 0.03}s` }"
                                :class="`animate-fade-in-up hover:${getCategoryDetails(category).borderGlow}`">
                                
                                <div class="absolute inset-0 bg-gradient-to-r opacity-0 group-hover:opacity-100 transition-opacity duration-500"
                                     :class="getCategoryDetails(category).gradient"></div>

                                <div class="relative z-10 flex items-center gap-6">
                                    <!-- Icon -->
                                    <div class="w-14 h-14 rounded-xl bg-white/5 border border-white/5 flex items-center justify-center text-2xl flex-shrink-0 transform group-hover:scale-110 transition-transform">
                                        {{ getCategoryDetails(category).icon }}
                                    </div>

                                    <!-- Content -->
                                    <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
                                        <div>
                                            <h4 class="text-lg font-bold text-white group-hover:text-transparent group-hover:bg-gradient-to-r group-hover:bg-clip-text transition-all"
                                                :class="`group-hover:${getCategoryDetails(category).textGradient}`">
                                                {{ item.item_name }}
                                            </h4>
                                            <p class="text-xs text-white/40 mt-1">{{ category }}</p>
                                        </div>

                                        <div class="flex items-baseline gap-1">
                                            <span class="text-xs text-white/40">₱</span>
                                            <span class="text-3xl font-mono font-medium text-white">{{ item.price }}</span>
                                            <span class="text-sm text-white/40">/{{ item.unit }}</span>
                                        </div>

                                        <div class="flex items-center justify-between md:justify-end gap-4">
                                            <div class="px-3 py-1.5 rounded-lg border text-[10px] font-bold uppercase tracking-wide backdrop-blur-md flex items-center gap-1.5"
                                                 :class="getTrendColor(item.trend)">
                                                <svg v-if="item.trend === 'up'" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                                </svg>
                                                <svg v-else-if="item.trend === 'down'" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                                                </svg>
                                                {{ item.trend === 'up' ? 'High' : item.trend === 'down' ? 'Low' : 'Stable' }}
                                            </div>

                                            <button class="opacity-0 group-hover:opacity-100 transition-opacity px-4 py-2 rounded-lg bg-white/5 hover:bg-white/10 text-xs font-semibold text-white">
                                                View Details
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

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
    50% { transform: translate(10px, -10px) rotate(5deg); }
}

.animate-drift { 
    animation: drift 25s ease-in-out infinite; 
}

.animate-drift-delayed { 
    animation: drift-delayed 30s ease-in-out infinite; 
}

.animate-drift-slow { 
    animation: drift-slow 35s ease-in-out infinite; 
}

@keyframes slide-up {
    from { 
        opacity: 0; 
        transform: translateY(30px); 
    }
    to { 
        opacity: 1; 
        transform: translateY(0); 
    }
}

.animate-slide-up { 
    animation: slide-up 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; 
}

@keyframes fade-in-up {
    from {
        opacity: 0;
        transform: translateY(20px) scale(0.95);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

.animate-fade-in-up {
    animation: fade-in-up 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

/* Scrollbar Styling */
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}

.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

/* Custom Scrollbar for containers */
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 10px;
}

::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.15);
    border-radius: 10px;
    transition: background 0.3s;
}

::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.25);
}

/* Reduce motion for accessibility */
@media (prefers-reduced-motion: reduce) {
    *,
    *::before,
    *::after {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
}

/* Print styles */
@media print {
    .sticky {
        position: static;
    }
}

/* High contrast mode support */
@media (prefers-contrast: high) {
    .border-white\/10 {
        border-color: rgba(255, 255, 255, 0.3);
    }
}

/* Focus visible for accessibility */
button:focus-visible,
input:focus-visible,
select:focus-visible {
    outline: 2px solid rgb(59, 130, 246);
    outline-offset: 2px;
}
</style>