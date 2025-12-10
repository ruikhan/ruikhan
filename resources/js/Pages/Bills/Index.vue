<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({ payments: Array });

const searchQuery = ref('');
const filterStatus = ref('all');

// Biller emoji mapping
const billerEmoji = {
    'Meralco': '⚡',
    'Maynilad': '💧',
    'PLDT': '📡',
    'Globe': '🌐',
};

const getBillerEmoji = (biller) => billerEmoji[biller] || '💳';

// Status color mapping
const getStatusColor = (status) => {
    const colors = {
        'paid': 'bg-green-100 text-green-800 border-green-300',
        'completed': 'bg-green-100 text-green-800 border-green-300',
        'pending': 'bg-yellow-100 text-yellow-800 border-yellow-300',
        'processing': 'bg-blue-100 text-blue-800 border-blue-300',
        'failed': 'bg-red-100 text-red-800 border-red-300',
    };
    return colors[status.toLowerCase()] || 'bg-gray-100 text-gray-800 border-gray-300';
};
</script>

<template>
    <Head title="Payment History" />

    <AuthenticatedLayout>
        <template #header>
            <div class="relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-r from-emerald-600 to-blue-600 opacity-10"></div>
                <div class="relative flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-blue-600 rounded-xl flex items-center justify-center text-white shadow-lg animate-pulse-slow">
                            📜
                        </div>
                        <div>
                            <h2 class="font-bold text-2xl text-gray-800 leading-tight">Payment History</h2>
                            <p class="text-xs text-gray-500 mt-0.5">View all your transactions</p>
                        </div>
                    </div>
                    <Link 
                        :href="route('bills.create')" 
                        class="group relative px-6 py-3 bg-gradient-to-r from-emerald-600 to-green-600 text-white rounded-xl text-sm font-bold shadow-lg hover:shadow-xl hover:from-emerald-700 hover:to-green-700 transition-all duration-300 overflow-hidden hover:scale-105 transform flex items-center gap-2"
                    >
                        <span class="absolute inset-0 w-full h-full bg-gradient-to-r from-transparent via-white to-transparent opacity-0 group-hover:opacity-20 transform -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></span>
                        <svg class="w-5 h-5 relative group-hover:rotate-90 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span class="relative">Pay New Bill</span>
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                
                <!-- Stats Overview -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 animate-fade-in-up">
                    <div class="bg-white p-6 rounded-2xl shadow-lg border border-slate-100 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                        <div class="flex items-center justify-between mb-3">
                            <div class="text-sm font-bold text-gray-500 uppercase tracking-wide">Total Payments</div>
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-100 to-blue-200 rounded-xl flex items-center justify-center">
                                💳
                            </div>
                        </div>
                        <div class="text-3xl font-black text-gray-800">{{ payments.length }}</div>
                        <div class="text-xs text-gray-500 mt-1">All time transactions</div>
                    </div>

                    <div class="bg-white p-6 rounded-2xl shadow-lg border border-slate-100 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                        <div class="flex items-center justify-between mb-3">
                            <div class="text-sm font-bold text-gray-500 uppercase tracking-wide">Total Amount</div>
                            <div class="w-10 h-10 bg-gradient-to-br from-emerald-100 to-emerald-200 rounded-xl flex items-center justify-center">
                                💰
                            </div>
                        </div>
                        <div class="text-3xl font-black text-emerald-700">
                            ₱{{ payments.reduce((sum, p) => sum + parseFloat(p.amount), 0).toLocaleString('en-PH', { minimumFractionDigits: 2 }) }}
                        </div>
                        <div class="text-xs text-gray-500 mt-1">Lifetime spending</div>
                    </div>

                    <div class="bg-white p-6 rounded-2xl shadow-lg border border-slate-100 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                        <div class="flex items-center justify-between mb-3">
                            <div class="text-sm font-bold text-gray-500 uppercase tracking-wide">Success Rate</div>
                            <div class="w-10 h-10 bg-gradient-to-br from-green-100 to-green-200 rounded-xl flex items-center justify-center">
                                ✅
                            </div>
                        </div>
                        <div class="text-3xl font-black text-green-700">
                            {{ payments.length > 0 ? Math.round((payments.filter(p => ['paid', 'completed'].includes(p.status.toLowerCase())).length / payments.length) * 100) : 0 }}%
                        </div>
                        <div class="text-xs text-gray-500 mt-1">Successful transactions</div>
                    </div>
                </div>

                <!-- Filters and Search -->
                <div class="bg-white p-6 rounded-2xl shadow-lg border border-slate-100 animate-fade-in-up" style="animation-delay: 0.1s;">
                    <div class="flex flex-col md:flex-row gap-4 items-center justify-between">
                        <div class="flex-1 w-full md:w-auto">
                            <div class="relative">
                                <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                <input 
                                    v-model="searchQuery"
                                    type="text" 
                                    placeholder="Search by biller or reference..." 
                                    class="w-full pl-12 pr-4 py-3 border-gray-300 rounded-xl focus:border-blue-500 focus:ring-blue-500 transition-all"
                                >
                            </div>
                        </div>
                        <div class="flex gap-3 w-full md:w-auto">
                            <select 
                                v-model="filterStatus"
                                class="px-4 py-3 border-gray-300 rounded-xl focus:border-blue-500 focus:ring-blue-500 font-medium"
                            >
                                <option value="all">All Status</option>
                                <option value="paid">Paid</option>
                                <option value="pending">Pending</option>
                                <option value="failed">Failed</option>
                            </select>
                            <button class="px-4 py-3 bg-gray-100 hover:bg-gray-200 rounded-xl font-semibold text-gray-700 transition-colors flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                                Export
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-if="payments.length === 0" class="bg-white p-16 rounded-2xl shadow-lg border border-slate-100 text-center animate-fade-in-up" style="animation-delay: 0.2s;">
                    <div class="inline-flex items-center justify-center w-24 h-24 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full mb-6 animate-bounce-subtle">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">No Payment History</h3>
                    <p class="text-gray-500 max-w-md mx-auto mb-6">You haven't made any payments yet. Start by paying your first bill!</p>
                    <Link 
                        :href="route('bills.create')" 
                        class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-emerald-600 to-green-600 text-white rounded-xl font-bold shadow-lg hover:shadow-xl hover:from-emerald-700 hover:to-green-700 transition-all duration-300 hover:scale-105 transform"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Make Your First Payment
                    </Link>
                </div>

                <!-- Payment Table -->
                <div v-else class="bg-white overflow-hidden shadow-xl rounded-2xl border border-slate-100 animate-fade-in-up" style="animation-delay: 0.2s;">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gradient-to-r from-slate-50 to-blue-50 border-b-2 border-slate-200">
                                    <th class="p-5 font-bold text-sm text-gray-700 uppercase tracking-wider">Date & Time</th>
                                    <th class="p-5 font-bold text-sm text-gray-700 uppercase tracking-wider">Biller</th>
                                    <th class="p-5 font-bold text-sm text-gray-700 uppercase tracking-wider">Reference Code</th>
                                    <th class="p-5 font-bold text-sm text-gray-700 uppercase tracking-wider">Amount</th>
                                    <th class="p-5 font-bold text-sm text-gray-700 uppercase tracking-wider">Status</th>
                                    <th class="p-5 font-bold text-sm text-gray-700 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr 
                                    v-for="(pay, index) in payments" 
                                    :key="pay.id" 
                                    class="border-b border-slate-100 hover:bg-gradient-to-r hover:from-blue-50/30 hover:to-transparent transition-all duration-300 group animate-slide-in"
                                    :style="`animation-delay: ${index * 0.05}s;`"
                                >
                                    <td class="p-5">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 bg-gradient-to-br from-blue-100 to-blue-200 rounded-xl flex items-center justify-center text-sm font-bold text-blue-700 group-hover:scale-110 transition-transform">
                                                {{ new Date(pay.created_at).getDate() }}
                                            </div>
                                            <div>
                                                <div class="text-sm font-semibold text-gray-900">
                                                    {{ new Date(pay.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) }}
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    {{ new Date(pay.created_at).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' }) }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-5">
                                        <div class="flex items-center gap-3">
                                            <div class="text-2xl">{{ getBillerEmoji(pay.biller_name) }}</div>
                                            <div>
                                                <div class="font-bold text-gray-900">{{ pay.biller_name }}</div>
                                                <div class="text-xs text-gray-500">Utility Payment</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-5">
                                        <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-gray-100 rounded-lg group-hover:bg-gray-200 transition-colors">
                                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                                            </svg>
                                            <span class="font-mono text-sm font-semibold text-gray-700">{{ pay.reference_code }}</span>
                                        </div>
                                    </td>
                                    <td class="p-5">
                                        <div class="text-xl font-black text-emerald-700">
                                            ₱{{ parseFloat(pay.amount).toLocaleString('en-PH', { minimumFractionDigits: 2 }) }}
                                        </div>
                                    </td>
                                    <td class="p-5">
                                        <span 
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold uppercase tracking-wide border-2 transition-all group-hover:scale-105"
                                            :class="getStatusColor(pay.status)"
                                        >
                                            <span v-if="['paid', 'completed'].includes(pay.status.toLowerCase())" class="w-2 h-2 bg-green-500 rounded-full"></span>
                                            <span v-else-if="pay.status.toLowerCase() === 'pending'" class="relative flex h-2 w-2">
                                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-yellow-400 opacity-75"></span>
                                                <span class="relative inline-flex rounded-full h-2 w-2 bg-yellow-500"></span>
                                            </span>
                                            <span v-else class="w-2 h-2 bg-red-500 rounded-full"></span>
                                            {{ pay.status }}
                                        </span>
                                    </td>
                                    <td class="p-5">
                                        <div class="flex items-center gap-2">
                                            <button class="p-2 hover:bg-blue-50 rounded-lg transition-colors group/btn" title="View Details">
                                                <svg class="w-5 h-5 text-gray-400 group-hover/btn:text-blue-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </button>
                                            <button class="p-2 hover:bg-emerald-50 rounded-lg transition-colors group/btn" title="Download Receipt">
                                                <svg class="w-5 h-5 text-gray-400 group-hover/btn:text-emerald-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
@keyframes fade-in-up {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slide-in {
    from {
        opacity: 0;
        transform: translateX(-20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes bounce-subtle {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-10px);
    }
}

@keyframes pulse-slow {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.7;
    }
}

.animate-fade-in-up {
    animation: fade-in-up 0.6s ease-out both;
}

.animate-slide-in {
    animation: slide-in 0.5s ease-out both;
}

.animate-bounce-subtle {
    animation: bounce-subtle 2s ease-in-out infinite;
}

.animate-pulse-slow {
    animation: pulse-slow 3s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>