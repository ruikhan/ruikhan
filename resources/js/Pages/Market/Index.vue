<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

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

const submit = () => {
    form.post(route('market.store'), {
        onSuccess: () => form.reset(),
    });
};

// macOS Style Helpers
const getCategoryDetails = (cat) => {
    const map = {
        'Fish': { color: 'bg-cyan-500', icon: '🐟', gradient: 'from-cyan-500/20 to-blue-500/5' },
        'Meat': { color: 'bg-rose-500', icon: '🥩', gradient: 'from-rose-500/20 to-red-500/5' },
        'Vegetables': { color: 'bg-emerald-500', icon: '🥬', gradient: 'from-emerald-500/20 to-green-500/5' },
        'Fruits': { color: 'bg-orange-500', icon: '🍊', gradient: 'from-orange-500/20 to-yellow-500/5' },
        'Spices': { color: 'bg-amber-500', icon: '🌶️', gradient: 'from-amber-500/20 to-orange-500/5' },
    };
    return map[cat] || { color: 'bg-gray-500', icon: '📦', gradient: 'from-gray-500/20 to-gray-500/5' };
};

// Trend Logic (Mocking visual logic if data isn't present)
const getTrendColor = (trend) => {
    if (trend === 'up') return 'text-rose-400 bg-rose-500/10 border-rose-500/20'; // Price up = "Hot" (Bad for buyer)
    if (trend === 'down') return 'text-emerald-400 bg-emerald-500/10 border-emerald-500/20'; // Price down = "Cool" (Good for buyer)
    return 'text-gray-400 bg-white/5 border-white/10';
};
</script>

<template>
    <Head title="Market Watch" />

    <AuthenticatedLayout>
        <div class="fixed inset-0 z-0 bg-[#000000]">
            <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-[0.04]"></div>
            <div class="absolute top-[-20%] left-[20%] w-[1000px] h-[600px] bg-blue-600/20 rounded-[100%] blur-[120px] mix-blend-screen animate-drift"></div>
            <div class="absolute bottom-[-10%] right-[10%] w-[800px] h-[600px] bg-purple-600/20 rounded-[100%] blur-[120px] mix-blend-screen animate-drift-delayed"></div>
        </div>

        <template #header>
            <div class="relative z-10 flex items-center justify-between px-2 py-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-gray-700 to-black border border-white/10 flex items-center justify-center shadow-2xl">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    </div>
                    <div>
                        <h2 class="text-3xl font-bold text-white tracking-tight">Market Watch</h2>
                        <p class="text-white/40 text-sm font-medium tracking-wide">Commodity Tracker</p>
                    </div>
                </div>
                
                <div class="hidden md:flex flex-col items-end">
                    <span class="text-xs font-bold text-white/30 uppercase tracking-widest">Pili, CamSur</span>
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span class="text-white font-mono text-sm">{{ new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) }}</span>
                    </div>
                </div>
            </div>
        </template>

        <div class="relative z-10 py-8 max-w-[1600px] mx-auto px-4 lg:px-8">
            <div class="grid grid-cols-1 xl:grid-cols-12 gap-8">
                
                <div class="xl:col-span-3">
                    <div class="sticky top-8 space-y-6">
                        <div class="bg-[#1c1c1e]/80 backdrop-blur-3xl border border-white/10 rounded-[2rem] p-6 shadow-2xl animate-slide-up">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-lg font-bold text-white">New Entry</h3>
                                <button class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center text-white/50 hover:bg-white hover:text-black transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                </button>
                            </div>

                            <form @submit.prevent="submit" class="space-y-4">
                                <div class="space-y-1">
                                    <label class="text-[10px] font-bold text-white/30 uppercase tracking-wider pl-1">Item Details</label>
                                    <input v-model="form.item_name" type="text" placeholder="e.g. Bangus" 
                                        class="w-full bg-black/20 border-none focus:ring-1 focus:ring-blue-500/50 rounded-xl text-white placeholder-white/20 h-12 px-4 transition-all hover:bg-black/30">
                                </div>

                                <div class="grid grid-cols-2 gap-3">
                                    <div class="space-y-1">
                                        <label class="text-[10px] font-bold text-white/30 uppercase tracking-wider pl-1">Category</label>
                                        <div class="relative">
                                            <select v-model="form.category" 
                                                class="w-full bg-black/20 border-none focus:ring-1 focus:ring-blue-500/50 rounded-xl text-white h-12 px-4 appearance-none cursor-pointer hover:bg-black/30 transition-all">
                                                <option>Fish</option>
                                                <option>Meat</option>
                                                <option>Vegetables</option>
                                                <option>Fruits</option>
                                                <option>Spices</option>
                                            </select>
                                            <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-white/40">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="space-y-1">
                                        <label class="text-[10px] font-bold text-white/30 uppercase tracking-wider pl-1">Unit</label>
                                        <select v-model="form.unit" class="w-full bg-black/20 border-none focus:ring-1 focus:ring-blue-500/50 rounded-xl text-white h-12 px-3 text-sm appearance-none hover:bg-black/30">
                                            <option value="kg">Per kg</option>
                                            <option value="pc">Per pc</option>
                                            <option value="bundle">Bundle</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="space-y-1">
                                    <label class="text-[10px] font-bold text-white/30 uppercase tracking-wider pl-1">Market Price (₱)</label>
                                    <input v-model="form.price" type="number" step="0.01" placeholder="0.00" 
                                        class="w-full bg-black/20 border-none focus:ring-1 focus:ring-blue-500/50 rounded-xl text-white placeholder-white/20 h-14 px-4 text-2xl font-mono text-right transition-all hover:bg-black/30">
                                </div>

                                <button type="submit" class="w-full h-12 mt-2 bg-white text-black font-bold rounded-xl hover:scale-[1.02] active:scale-[0.98] transition-all shadow-lg shadow-white/10 flex items-center justify-center gap-2">
                                    <span>Update Market</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                </button>
                            </form>
                        </div>

                        <div class="bg-gradient-to-br from-indigo-600 to-blue-700 rounded-[2rem] p-6 shadow-xl relative overflow-hidden group">
                            <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20 mix-blend-overlay"></div>
                            <div class="relative z-10">
                                <p class="text-blue-100 text-xs font-bold uppercase tracking-widest mb-1">Market Trend</p>
                                <h4 class="text-white text-2xl font-bold">Volatile</h4>
                                <p class="text-blue-200/60 text-sm mt-2">Prices for vegetables are fluctuating due to recent storms.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="xl:col-span-9 space-y-10">
                    
                    <div v-if="Object.keys(marketPrices).length === 0" class="flex flex-col items-center justify-center h-96 bg-[#1c1c1e]/40 rounded-[2.5rem] border border-dashed border-white/10 animate-pulse">
                        <div class="w-20 h-20 rounded-full bg-white/5 flex items-center justify-center mb-4">
                            <svg class="w-10 h-10 text-white/20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        </div>
                        <span class="text-white/30 font-medium">Waiting for market data...</span>
                    </div>

                    <div v-for="(items, category) in marketPrices" :key="category" class="animate-slide-up">
                        
                        <div class="flex items-center gap-3 mb-6 sticky top-0 py-4 z-20 backdrop-blur-md -mx-4 px-4 rounded-xl">
                            <div class="w-3 h-3 rounded-full shadow-[0_0_12px_currentColor]" :class="getCategoryDetails(category).color.replace('bg-', 'text-') + ' bg-current'"></div>
                            <h3 class="text-2xl font-bold text-white tracking-tight">{{ category }}</h3>
                            <div class="h-px flex-1 bg-gradient-to-r from-white/10 to-transparent ml-4"></div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                            <div v-for="item in items" :key="item.id" 
                                class="group relative bg-[#1c1c1e]/60 backdrop-blur-xl border border-white/10 rounded-[1.75rem] p-5 hover:bg-[#2c2c2e] transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl hover:shadow-black/50 overflow-hidden">
                                
                                <div class="absolute inset-0 bg-gradient-to-br opacity-0 group-hover:opacity-100 transition-opacity duration-500"
                                     :class="getCategoryDetails(category).gradient"></div>

                                <div class="relative z-10 flex flex-col h-full justify-between">
                                    
                                    <div class="flex justify-between items-start mb-4">
                                        <div class="w-10 h-10 rounded-xl bg-white/5 border border-white/5 flex items-center justify-center text-xl shadow-inner">
                                            {{ getCategoryDetails(category).icon }}
                                        </div>
                                        
                                        <div class="px-2.5 py-1 rounded-lg border text-[10px] font-bold uppercase tracking-wide backdrop-blur-md flex items-center gap-1"
                                             :class="getTrendColor(item.trend)">
                                            <svg v-if="item.trend === 'up'" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                                            <svg v-else-if="item.trend === 'down'" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path></svg>
                                            <span v-else class="text-lg leading-none">-</span>
                                            {{ item.trend === 'up' ? 'High' : item.trend === 'down' ? 'Low' : 'Stable' }}
                                        </div>
                                    </div>

                                    <div>
                                        <h4 class="text-lg font-bold text-white mb-1 truncate group-hover:text-blue-200 transition-colors">{{ item.item_name }}</h4>
                                        <div class="flex items-baseline gap-1">
                                            <span class="text-xs text-white/40 align-top mt-1">₱</span>
                                            <span class="text-3xl font-mono font-medium text-white tracking-tight">{{ item.price }}</span>
                                            <span class="text-xs text-white/40 font-medium">/{{ item.unit }}</span>
                                        </div>
                                    </div>

                                    <div class="mt-4 pt-3 border-t border-white/5 flex justify-between items-center text-[10px] text-white/30 font-medium">
                                        <span>Updated today</span>
                                        <span class="opacity-0 group-hover:opacity-100 transition-opacity text-blue-400">View History →</span>
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
@keyframes drift {
    0% { transform: translate(0, 0); }
    50% { transform: translate(-20px, 20px); }
    100% { transform: translate(0, 0); }
}
.animate-drift { animation: drift 20s ease-in-out infinite; }
.animate-drift-delayed { animation: drift 25s ease-in-out infinite reverse; }

@keyframes slide-up {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-slide-up { animation: slide-up 0.6s cubic-bezier(0.2, 0.8, 0.2, 1) forwards; }
</style>