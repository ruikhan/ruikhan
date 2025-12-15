<script setup>
import ChatAssistant from '@/Components/ChatAssistant.vue';
import DashboardMap from '@/Components/DashboardMap.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { defineProps, onMounted, onUnmounted, ref } from 'vue';

const user = usePage().props.auth.user;

// --- 1. RECEIVE REAL DATA ---
const props = defineProps({
    stats: Object,
    recentActivity: Array
});

// Helper: Format Money
const formatMoney = (value) => {
    return new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' }).format(value);
};

// --- LIVE CLOCK ---
const time = ref('');
const date = ref('');
const updateTime = () => {
    const now = new Date();
    time.value = now.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' });
    date.value = now.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric' });
};

// --- MOCK WORKFLOW DATA (Replace with Controller Data later) ---
const pendingTasks = ref([
    { id: 1, type: 'Permit', user: 'Juan Cruz', detail: 'Sari-Sari Store Renewal', status: 'urgent', time: '10m ago' },
    { id: 2, type: 'Report', user: 'Maria L.', detail: 'Flooding in Zone 4', status: 'critical', time: '25m ago' },
    { id: 3, type: 'Indigency', user: 'Pedro P.', detail: 'Scholarship Requirement', status: 'normal', time: '1h ago' },
    { id: 4, type: 'Health', user: 'Ana Marie', detail: 'Prenatal Booking Review', status: 'normal', time: '2h ago' },
]);

const departmentLoad = ref([
    { name: 'Civil Registry', count: 45, capacity: 80, color: 'bg-blue-500' },
    { name: 'Health Office', count: 92, capacity: 100, color: 'bg-red-500' }, // Overloaded
    { name: 'Engineering', count: 12, capacity: 50, color: 'bg-green-500' },
    { name: 'Social Welfare', count: 60, capacity: 70, color: 'bg-yellow-500' },
]);

let clockTimer;
onMounted(() => {
    updateTime();
    clockTimer = setInterval(updateTime, 1000);
});
onUnmounted(() => {
    clearInterval(clockTimer);
});
</script>

<template>
    <Head title="Command Center" />

    <AuthenticatedLayout>
        <div class="fixed inset-0 z-0 pointer-events-none bg-[#0f172a]">
            <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
            <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-blue-900/20 rounded-full blur-[100px]"></div>
            <div class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-indigo-900/20 rounded-full blur-[100px]"></div>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 font-sans">
            
            <div class="flex flex-col md:flex-row justify-between items-center mb-8 bg-slate-800/50 backdrop-blur-md border border-slate-700 p-4 rounded-2xl shadow-xl">
                <div class="flex items-center gap-4">
                    <div class="relative">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-600 to-indigo-600 flex items-center justify-center text-xl font-bold text-white shadow-lg">
                            {{ user.name.charAt(0) }}
                        </div>
                        <span class="absolute -bottom-1 -right-1 flex h-3 w-3">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                        </span>
                    </div>
                    <div>
                        <h1 class="text-white font-bold text-lg">Admin Overview</h1>
                        <p class="text-slate-400 text-xs uppercase tracking-widest">System Operational • {{ time }}</p>
                    </div>
                </div>
                
                <div class="flex gap-6 mt-4 md:mt-0 overflow-x-auto">
                    <div class="text-right">
                        <p class="text-[10px] text-slate-400 uppercase font-bold">Today's Revenue</p>
                        <p class="text-green-400 font-mono font-bold">{{ formatMoney(stats?.revenue || 24500) }}</p>
                    </div>
                    <div class="text-right border-l border-slate-700 pl-6">
                        <p class="text-[10px] text-slate-400 uppercase font-bold">New Citizens</p>
                        <p class="text-blue-400 font-mono font-bold">+{{ stats?.citizens || 12 }}</p>
                    </div>
                    <div class="text-right border-l border-slate-700 pl-6">
                        <p class="text-[10px] text-slate-400 uppercase font-bold">Pending Actions</p>
                        <p class="text-yellow-400 font-mono font-bold">{{ pendingTasks.length }}</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 h-full">
                
                <div class="lg:col-span-8 space-y-6">
                    
                    <div class="bg-slate-800/80 backdrop-blur-xl border border-slate-700 rounded-2xl overflow-hidden shadow-2xl">
                        <div class="px-6 py-4 border-b border-slate-700 flex justify-between items-center bg-slate-900/50">
                            <h3 class="text-white font-bold flex items-center gap-2">
                                <span class="text-yellow-500 text-lg">⚡</span> Priority Queue
                            </h3>
                            <button class="text-xs text-blue-400 hover:text-white transition">Batch Process All</button>
                        </div>
                        
                        <div class="divide-y divide-slate-700/50">
                            <div v-for="task in pendingTasks" :key="task.id" 
                                class="p-4 hover:bg-slate-700/30 transition flex items-center justify-between group cursor-pointer">
                                <div class="flex items-center gap-4">
                                    <div :class="{
                                        'bg-red-500/20 text-red-400': task.status === 'critical',
                                        'bg-yellow-500/20 text-yellow-400': task.status === 'urgent',
                                        'bg-blue-500/20 text-blue-400': task.status === 'normal',
                                    }" class="w-10 h-10 rounded-lg flex items-center justify-center text-lg font-bold">
                                        {{ task.type.charAt(0) }}
                                    </div>
                                    <div>
                                        <h4 class="text-white font-medium text-sm">{{ task.detail }}</h4>
                                        <p class="text-slate-400 text-xs">{{ task.user }} • <span class="text-slate-500">{{ task.time }}</span></p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button class="px-3 py-1 bg-green-600 hover:bg-green-500 text-white text-xs rounded font-bold">Approve</button>
                                    <button class="px-3 py-1 bg-slate-700 hover:bg-slate-600 text-white text-xs rounded">View</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-slate-800/80 backdrop-blur-xl border border-slate-700 rounded-2xl p-6 shadow-2xl">
                             <h3 class="text-white text-xs font-bold uppercase tracking-widest mb-4 opacity-70">Department Load</h3>
                             <div class="space-y-4">
                                <div v-for="dept in departmentLoad" :key="dept.name">
                                    <div class="flex justify-between text-xs mb-1">
                                        <span class="text-white">{{ dept.name }}</span>
                                        <span :class="dept.count > dept.capacity * 0.9 ? 'text-red-400' : 'text-slate-400'">
                                            {{ dept.count }}/{{ dept.capacity }}
                                        </span>
                                    </div>
                                    <div class="w-full bg-slate-900 rounded-full h-2 overflow-hidden">
                                        <div :class="`h-full ${dept.color}`" :style="`width: ${(dept.count / dept.capacity) * 100}%`"></div>
                                    </div>
                                </div>
                             </div>
                        </div>

                        <div class="bg-gradient-to-br from-indigo-900/50 to-slate-900/50 border border-indigo-500/30 rounded-2xl p-6 shadow-2xl">
                             <h3 class="text-white text-xs font-bold uppercase tracking-widest mb-4 opacity-70">Quick Actions</h3>
                             <div class="grid grid-cols-2 gap-3">
                                <Link :href="route('documents.index')" class="p-3 bg-slate-800 hover:bg-indigo-600 transition rounded-lg text-center border border-slate-600 group">
                                    <div class="text-xl mb-1 group-hover:scale-110 transition">📄</div>
                                    <span class="text-xs text-white font-bold">Verify Doc</span>
                                </Link>
                                <Link :href="route('emergency.index')" class="p-3 bg-slate-800 hover:bg-red-600 transition rounded-lg text-center border border-slate-600 group">
                                    <div class="text-xl mb-1 group-hover:scale-110 transition">🚨</div>
                                    <span class="text-xs text-white font-bold">Dispatch</span>
                                </Link>
                                <Link :href="route('market.index')" class="p-3 bg-slate-800 hover:bg-green-600 transition rounded-lg text-center border border-slate-600 group">
                                    <div class="text-xl mb-1 group-hover:scale-110 transition">📈</div>
                                    <span class="text-xs text-white font-bold">Prices</span>
                                </Link>
                                <Link :href="route('jobs.index')" class="p-3 bg-slate-800 hover:bg-purple-600 transition rounded-lg text-center border border-slate-600 group">
                                    <div class="text-xl mb-1 group-hover:scale-110 transition">💼</div>
                                    <span class="text-xs text-white font-bold">Post Job</span>
                                </Link>
                             </div>
                         </div>
                    </div>
                </div>

                <div class="lg:col-span-4 space-y-6">
                    
                    <div class="bg-slate-800/80 border border-slate-700 rounded-2xl p-1 h-64 relative overflow-hidden group">
                        <div class="absolute top-3 left-3 z-10 bg-black/70 px-2 py-1 rounded text-[10px] text-green-400 font-mono border border-green-500/30 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                            LIVE TRACKING
                        </div>
                        <div class="w-full h-full rounded-xl opacity-80 hover:opacity-100 transition duration-500">
                             <DashboardMap />
                        </div>
                    </div>

                    <div class="bg-slate-800/80 backdrop-blur-xl border border-slate-700 rounded-2xl p-5 shadow-xl h-[400px] overflow-y-auto custom-scrollbar">
                        <h3 class="text-white text-xs font-bold uppercase tracking-widest mb-4 sticky top-0 bg-slate-800/90 pb-2 border-b border-slate-700">Audit Log</h3>
                        
                        <div class="space-y-4">
                            <div v-for="item in recentActivity" :key="item.id" class="flex gap-3 relative pl-4 border-l border-slate-700">
                                <div class="absolute -left-[5px] top-1 w-2 h-2 rounded-full bg-slate-500"></div>
                                <div>
                                    <p class="text-xs text-slate-300">
                                        <span class="font-bold text-white">{{ item.user }}</span> {{ item.action }}
                                    </p>
                                    <p class="text-[10px] text-slate-500 mt-1">{{ item.time }}</p>
                                </div>
                            </div>
                            <div v-if="!recentActivity?.length" class="text-center text-slate-500 text-xs py-10">
                                System Waiting for input...
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <ChatAssistant />
    </AuthenticatedLayout>
</template>

<style scoped>
/* Custom Scrollbar for the feed */
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: rgba(30, 41, 59, 0.5); 
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(71, 85, 105, 0.8); 
    border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgba(99, 102, 241, 0.8); 
}
</style>