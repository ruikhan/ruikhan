<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ArcElement, BarElement, CategoryScale, Chart as ChartJS, Legend, LinearScale, Title, Tooltip } from 'chart.js';
import { Bar, Doughnut } from 'vue-chartjs';

// Register Chart.js components
ChartJS.register(ArcElement, Tooltip, Legend, CategoryScale, LinearScale, BarElement, Title);

const props = defineProps({ stats: Object, chartData: Object });

// --- CHART CONFIGURATION (Neon Theme) ---
const commonOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { labels: { color: '#94a3b8', font: { family: 'sans-serif', size: 11 } } }
    }
};

// 1. Doughnut Chart (Concerns)
const concernData = {
    labels: props.chartData.concerns.map(item => item.category),
    datasets: [{
        backgroundColor: ['#ef4444', '#f59e0b', '#3b82f6', '#10b981'], // Red, Yellow, Blue, Green
        borderColor: '#1e293b', // Match background color for spacing
        borderWidth: 4,
        data: props.chartData.concerns.map(item => item.total)
    }]
};

// 2. Bar Chart (Revenue) - Mocking labels for the months
const revenueData = {
    labels: props.chartData.revenue.map(item => `Month ${item.month}`),
    datasets: [{
        label: 'Revenue (PHP)',
        backgroundColor: '#10b981', // Emerald Green
        borderRadius: 6,
        data: props.chartData.revenue.map(item => item.total)
    }]
};

const barOptions = {
    ...commonOptions,
    scales: {
        y: { ticks: { color: '#64748b' }, grid: { color: '#334155' } },
        x: { ticks: { color: '#64748b' }, grid: { display: false } }
    }
};
</script>

<template>
    <Head title="Executive Analytics" />

    <AuthenticatedLayout>
        <div class="min-h-screen bg-slate-900 text-white font-sans relative overflow-hidden pb-20">
            
            <div class="absolute inset-0 pointer-events-none">
                <div class="absolute inset-0 opacity-10" style="background-image: linear-gradient(0deg, transparent 24%, #ffffff 25%, #ffffff 26%, transparent 27%, transparent 74%, #ffffff 75%, #ffffff 76%, transparent 77%, transparent), linear-gradient(90deg, transparent 24%, #ffffff 25%, #ffffff 26%, transparent 27%, transparent 74%, #ffffff 75%, #ffffff 76%, transparent 77%, transparent); background-size: 50px 50px;"></div>
                <div class="absolute top-[-10%] right-[-10%] w-[600px] h-[600px] bg-purple-600/20 rounded-full blur-[120px] animate-pulse-slow"></div>
                <div class="absolute bottom-[-10%] left-[-10%] w-[600px] h-[600px] bg-blue-600/20 rounded-full blur-[120px] animate-pulse-slow" style="animation-delay: 2s;"></div>
            </div>

            <div class="relative z-10 pt-10 px-6 lg:px-8 max-w-7xl mx-auto mb-10">
                <div class="flex justify-between items-end animate-fade-in">
                    <div>
                        <p class="text-blue-400 text-xs font-bold uppercase tracking-widest mb-1">Business Intelligence</p>
                        <h1 class="text-4xl font-bold tracking-tight">Executive Dashboard</h1>
                    </div>
                    <button class="bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 rounded-lg text-sm font-bold shadow-lg shadow-blue-600/30 transition-all flex items-center gap-2">
                        <span>⬇️</span> Export Report
                    </button>
                </div>
            </div>

            <div class="relative z-10 max-w-7xl mx-auto px-6 lg:px-8 space-y-8">
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 animate-slide-up">
                    
                    <div class="bg-slate-800/50 backdrop-blur-xl border border-white/10 p-6 rounded-2xl relative overflow-hidden group hover:border-green-500/50 transition-all duration-300">
                        <div class="absolute top-0 right-0 p-4 opacity-10 text-5xl group-hover:scale-110 transition-transform">💰</div>
                        <div class="text-slate-400 text-xs font-bold uppercase tracking-wider">Total Revenue</div>
                        <div class="text-3xl font-bold text-white mt-2">₱{{ stats.total_revenue.toLocaleString() }}</div>
                        <div class="text-green-400 text-xs mt-1 flex items-center gap-1">
                            <span class="text-lg leading-none">↑</span> 12% vs last month
                        </div>
                        <div class="absolute bottom-0 left-0 w-full h-1 bg-green-500/50"></div>
                    </div>

                    <div class="bg-slate-800/50 backdrop-blur-xl border border-white/10 p-6 rounded-2xl relative overflow-hidden group hover:border-blue-500/50 transition-all duration-300">
                        <div class="absolute top-0 right-0 p-4 opacity-10 text-5xl group-hover:scale-110 transition-transform">👥</div>
                        <div class="text-slate-400 text-xs font-bold uppercase tracking-wider">Total Residents</div>
                        <div class="text-3xl font-bold text-white mt-2">{{ stats.total_residents }}</div>
                        <div class="text-blue-400 text-xs mt-1">+ {{ stats.total_businesses }} Businesses</div>
                        <div class="absolute bottom-0 left-0 w-full h-1 bg-blue-500/50"></div>
                    </div>

                    <div class="bg-slate-800/50 backdrop-blur-xl border border-white/10 p-6 rounded-2xl relative overflow-hidden group hover:border-yellow-500/50 transition-all duration-300">
                        <div class="absolute top-0 right-0 p-4 opacity-10 text-5xl group-hover:scale-110 transition-transform">📄</div>
                        <div class="text-slate-400 text-xs font-bold uppercase tracking-wider">Pending Docs</div>
                        <div class="text-3xl font-bold text-white mt-2">{{ stats.pending_docs }}</div>
                        <div class="text-yellow-400 text-xs mt-1">Needs Attention</div>
                        <div class="absolute bottom-0 left-0 w-full h-1 bg-yellow-500/50"></div>
                    </div>

                    <div class="bg-slate-800/50 backdrop-blur-xl border border-white/10 p-6 rounded-2xl relative overflow-hidden group hover:border-red-500/50 transition-all duration-300">
                        <div class="absolute top-0 right-0 p-4 opacity-10 text-5xl group-hover:scale-110 transition-transform">🚨</div>
                        <div class="text-slate-400 text-xs font-bold uppercase tracking-wider">Active Issues</div>
                        <div class="text-3xl font-bold text-white mt-2">{{ stats.open_concerns }}</div>
                        <div class="text-red-400 text-xs mt-1">Critical Priority</div>
                        <div class="absolute bottom-0 left-0 w-full h-1 bg-red-500/50"></div>
                    </div>

                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 animate-slide-up" style="animation-delay: 0.2s;">
                    
                    <div class="bg-slate-800/50 backdrop-blur-xl border border-white/10 p-6 rounded-2xl shadow-xl flex flex-col">
                        <h3 class="font-bold text-lg mb-6 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                            Concerns Distribution
                        </h3>
                        <div class="flex-1 relative min-h-[250px]">
                            <Doughnut :data="concernData" :options="commonOptions" />
                        </div>
                    </div>

                    <div class="lg:col-span-2 bg-slate-800/50 backdrop-blur-xl border border-white/10 p-6 rounded-2xl shadow-xl flex flex-col">
                        <h3 class="font-bold text-lg mb-6 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-green-500"></span>
                            Revenue Trend
                        </h3>
                        <div class="flex-1 relative min-h-[250px]">
                            <Bar :data="revenueData" :options="barOptions" />
                        </div>
                    </div>

                </div>

                <div class="bg-gradient-to-r from-slate-800 to-slate-900 border border-white/10 rounded-2xl p-8 animate-slide-up" style="animation-delay: 0.4s;">
                    <h3 class="font-bold text-xl mb-4">📢 System Broadcast</h3>
                    <div class="flex gap-4">
                        <input type="text" placeholder="Type emergency alert message..." class="flex-1 bg-black/20 border border-white/10 rounded-xl px-4 text-white focus:ring-2 focus:ring-blue-500 transition-all">
                        <button class="bg-red-600 hover:bg-red-500 text-white px-6 py-3 rounded-xl font-bold shadow-lg shadow-red-600/20 transition-all">
                            Send Alert
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.animate-pulse-slow { animation: pulse 6s ease-in-out infinite; }
.animate-slide-up { animation: slideUp 0.8s cubic-bezier(0.16, 1, 0.3, 1); }
@keyframes slideUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
.animate-fade-in { animation: fadeIn 1s ease-out; }
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
</style>