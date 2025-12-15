<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';

// Chart.js Imports
import { CategoryScale, Chart as ChartJS, Filler, Legend, LinearScale, LineElement, PointElement, Title, Tooltip } from 'chart.js';
import { Line } from 'vue-chartjs';

// Leaflet Map Imports
import { LCircle, LMap, LMarker, LPopup, LTileLayer } from "@vue-leaflet/vue-leaflet";
import 'leaflet/dist/leaflet.css';

ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, Title, Tooltip, Legend, Filler);

const props = defineProps({ 
    current: Object, 
    chart_data: Object, 
    map_data: Object,
    active_alert: Object 
});

// -- SIMULATION TOGGLE --
const isDisasterMode = ref(!!props.active_alert);
const toggleSimulation = () => isDisasterMode.value = !isDisasterMode.value;

// -- CHART CONFIG (Dark Mode Optimized) --
const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    scales: {
        y: { 
            border: { display: false },
            grid: { color: 'rgba(255,255,255,0.05)' }, 
            ticks: { color: '#64748b', font: { size: 10, family: 'sans-serif' } } 
        },
        x: { 
            border: { display: false },
            grid: { display: false }, 
            ticks: { color: '#64748b', font: { size: 10 } } 
        }
    },
    plugins: { 
        legend: { display: false },
        tooltip: {
            backgroundColor: 'rgba(15, 23, 42, 0.9)',
            titleColor: '#fff',
            bodyColor: '#cbd5e1',
            borderColor: 'rgba(255,255,255,0.1)',
            borderWidth: 1,
            padding: 10,
            cornerRadius: 8,
            displayColors: false
        }
    },
    interaction: {
        intersect: false,
        mode: 'index',
    },
};

const chartData = {
    labels: props.chart_data.labels,
    datasets: [{
        label: 'AQI Level',
        backgroundColor: (ctx) => {
            const canvas = ctx.chart.ctx;
            const gradient = canvas.createLinearGradient(0, 0, 0, 300);
            gradient.addColorStop(0, 'rgba(34, 197, 94, 0.4)'); // Green top
            gradient.addColorStop(1, 'rgba(34, 197, 94, 0)');   // Transparent bottom
            return gradient;
        },
        borderColor: '#22c55e',
        borderWidth: 2,
        pointBackgroundColor: '#050505',
        pointBorderColor: '#22c55e',
        pointBorderWidth: 2,
        pointHoverBackgroundColor: '#22c55e',
        pointHoverBorderColor: '#fff',
        fill: true,
        data: props.chart_data.aqi,
        tension: 0.4
    }]
};

// -- MAP CONFIG --
const zoom = ref(13);
const center = ref([13.58, 123.28]); // Pili Coordinates
</script>

<template>
    <Head title="Green Guard Dashboard" />

    <AuthenticatedLayout>
        
        <Transition name="fade">
            <div v-if="isDisasterMode" class="fixed inset-0 z-[100] flex items-center justify-center p-4 pointer-events-none">
                <div class="absolute inset-0 bg-red-950/40 backdrop-blur-sm animate-pulse-slow"></div>
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_center,transparent_0%,rgba(0,0,0,0.8)_100%)]"></div>
                
                <div class="absolute top-0 inset-x-0 h-2 bg-[repeating-linear-gradient(45deg,#ef4444,#ef4444_20px,transparent_20px,transparent_40px)] animate-slide-bg"></div>
                <div class="absolute bottom-0 inset-x-0 h-2 bg-[repeating-linear-gradient(45deg,#ef4444,#ef4444_20px,transparent_20px,transparent_40px)] animate-slide-bg"></div>

                <div class="pointer-events-auto relative max-w-2xl w-full bg-black/80 backdrop-blur-2xl border border-red-500/50 rounded-3xl p-1 shadow-[0_0_100px_rgba(220,38,38,0.5)] overflow-hidden m-4">
                    <div class="absolute top-0 right-0 p-4 opacity-50">
                        <svg class="w-32 h-32 text-red-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L1 21h22L12 2zm0 3.99L19.53 19H4.47L12 5.99zM11 16h2v2h-2zm0-6h2v4h-2z"/></svg>
                    </div>
                    
                    <div class="relative bg-[#0f1115]/90 rounded-[1.4rem] p-6 md:p-12 text-center border border-white/5">
                        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-red-500/20 border border-red-500/50 text-red-400 font-bold uppercase tracking-widest text-[10px] md:text-xs mb-4 md:mb-6 animate-pulse">
                            <span class="w-2 h-2 rounded-full bg-red-500"></span> Critical Alert Active
                        </div>
                        
                        <h2 class="text-3xl md:text-6xl font-black text-white mb-4 tracking-tighter leading-tight">
                            TYPHOON <span class="text-red-500 block md:inline">SIGNAL #3</span>
                        </h2>
                        <p class="text-slate-300 text-sm md:text-lg mb-8 max-w-md mx-auto leading-relaxed">
                            Heavy rainfall and potential flooding detected in Zone 4. Immediate evacuation protocols are in effect.
                        </p>

                        <div class="flex flex-col sm:flex-row justify-center gap-4">
                            <button class="px-6 md:px-8 py-3 md:py-4 bg-red-600 hover:bg-red-500 text-white font-bold rounded-xl shadow-[0_0_30px_rgba(220,38,38,0.4)] transition-all transform hover:scale-105 flex items-center justify-center gap-2 text-sm md:text-base">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                Request Rescue
                            </button>
                            <button @click="toggleSimulation" class="px-6 md:px-8 py-3 md:py-4 bg-white/10 hover:bg-white/20 border border-white/10 text-white font-bold rounded-xl transition-all text-sm md:text-base">
                                Mark as Safe
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </Transition>

        <div class="min-h-screen relative overflow-hidden font-sans pb-12">
            
            <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8 pt-4 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-white tracking-tight">Environmental Command</h1>
                    <p class="text-slate-400 text-xs md:text-sm mt-1 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        Live Sensor Feed • Pili, Camarines Sur
                    </p>
                </div>
                
                <div class="flex items-center gap-3 w-full md:w-auto">
                    <button @click="toggleSimulation" 
                        class="w-full md:w-auto group relative px-5 py-3 md:py-2.5 rounded-xl md:rounded-full font-bold text-xs uppercase tracking-widest transition-all overflow-hidden border border-white/10 bg-white/5 hover:bg-white/10 text-center"
                    >
                        <span :class="['relative z-10', isDisasterMode ? 'text-red-400' : 'text-slate-300 group-hover:text-white']">
                            {{ isDisasterMode ? 'End Simulation' : 'Simulate Disaster' }}
                        </span>
                    </button>
                </div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-6 relative z-10">
                
                <div class="bg-[#0f1115]/60 backdrop-blur-xl border border-white/10 rounded-2xl md:rounded-3xl p-5 md:p-6 group hover:border-emerald-500/30 transition-all duration-500">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="text-[10px] font-bold text-emerald-400 uppercase tracking-widest">Air Quality</p>
                            <h3 class="text-2xl md:text-3xl font-bold text-white mt-1">{{ current.aqi }}</h3>
                        </div>
                        <div class="w-10 h-10 rounded-2xl bg-emerald-500/10 flex items-center justify-center text-emerald-400 text-lg group-hover:scale-110 transition-transform">🍃</div>
                    </div>
                    <div class="w-full bg-white/10 h-1.5 rounded-full overflow-hidden">
                        <div class="bg-emerald-500 h-full rounded-full shadow-[0_0_10px_#10b981]" style="width: 42%"></div>
                    </div>
                    <p class="text-[10px] text-slate-500 mt-2 flex justify-between">
                        <span>PM2.5 Levels</span>
                        <span class="text-emerald-400">Good</span>
                    </p>
                </div>

                <div class="bg-[#0f1115]/60 backdrop-blur-xl border border-white/10 rounded-2xl md:rounded-3xl p-5 md:p-6 group hover:border-orange-500/30 transition-all duration-500">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="text-[10px] font-bold text-orange-400 uppercase tracking-widest">Heat Index</p>
                            <h3 class="text-2xl md:text-3xl font-bold text-white mt-1">{{ current.temp }}°<span class="text-lg text-slate-500">C</span></h3>
                        </div>
                        <div class="w-10 h-10 rounded-2xl bg-orange-500/10 flex items-center justify-center text-orange-400 text-lg group-hover:scale-110 transition-transform">☀️</div>
                    </div>
                    <div class="flex items-center gap-2 text-[10px] md:text-xs text-slate-400 mt-4 bg-white/5 p-2 rounded-lg border border-white/5">
                        <span class="text-blue-400">💧</span> Humidity: {{ current.humidity }}%
                    </div>
                </div>

                <div class="bg-[#0f1115]/60 backdrop-blur-xl border border-white/10 rounded-2xl md:rounded-3xl p-5 md:p-6 group hover:border-blue-500/30 transition-all duration-500">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="text-[10px] font-bold text-blue-400 uppercase tracking-widest">River Level</p>
                            <h3 class="text-2xl md:text-3xl font-bold text-white mt-1">{{ current.water_level }}<span class="text-lg text-slate-500">m</span></h3>
                        </div>
                        <div class="w-10 h-10 rounded-2xl bg-blue-500/10 flex items-center justify-center text-blue-400 text-lg group-hover:scale-110 transition-transform">🌊</div>
                    </div>
                    <div class="relative w-full h-8 overflow-hidden rounded-lg bg-blue-900/20 border border-blue-500/20">
                        <div class="absolute bottom-0 inset-x-0 bg-blue-500/60 h-[40%] animate-pulse-slow"></div>
                        <div class="absolute bottom-0 inset-x-0 bg-blue-400/40 h-[45%] wave-clip"></div>
                    </div>
                </div>

                <div :class="['rounded-2xl md:rounded-3xl p-6 flex flex-col justify-center items-center text-center border transition-all duration-500 relative overflow-hidden', isDisasterMode ? 'bg-red-500/10 border-red-500/50' : 'bg-emerald-500/10 border-emerald-500/30']">
                    <div v-if="isDisasterMode" class="absolute inset-0 bg-red-500/10 animate-pulse"></div>
                    <div class="relative z-10">
                        <span class="text-4xl md:text-5xl mb-2 block filter drop-shadow-lg">{{ isDisasterMode ? '⚠️' : '🛡️' }}</span>
                        <h3 :class="['font-black text-base md:text-lg uppercase tracking-widest', isDisasterMode ? 'text-red-400' : 'text-emerald-400']">
                            {{ isDisasterMode ? 'CRITICAL' : 'SECURE' }}
                        </h3>
                        <p class="text-white/60 text-[10px] mt-1">{{ isDisasterMode ? 'Evacuation Required' : 'Systems Normal' }}</p>
                    </div>
                </div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-3 gap-6 relative z-10">
                
                <div class="lg:col-span-2 bg-[#0f1115] rounded-3xl overflow-hidden border border-white/10 relative shadow-2xl group h-[400px] lg:h-[600px]">
                    <l-map ref="map" v-model:zoom="zoom" :center="center" :use-global-leaflet="false" class="z-0 h-full w-full">
                        <l-tile-layer
                            url="https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png"
                            layer-type="base"
                            name="Dark Map"
                        ></l-tile-layer>

                        <l-marker v-for="center in map_data.evacuation_centers" :key="center.id" :lat-lng="[center.lat, center.lng]">
                            <l-popup class="custom-popup">
                                <div class="text-slate-900 text-xs">
                                    <strong class="text-emerald-600 block mb-1">Evacuation Center</strong>
                                    {{ center.name }}<br>
                                    <span class="text-slate-500">Cap: {{ center.capacity }}</span>
                                </div>
                            </l-popup>
                        </l-marker>

                        <l-circle 
                            v-for="(zone, index) in map_data.flood_zones" :key="index"
                            :lat-lng="[zone.lat, zone.lng]" 
                            :radius="zone.radius" 
                            color="#ef4444" 
                            :fill="true" :fillOpacity="0.2"
                            class="animate-pulse-slow"
                        />
                    </l-map>

                    <div class="absolute top-4 right-4 z-[400] flex flex-col gap-2 pointer-events-none">
                        <div class="bg-black/80 backdrop-blur-md text-white px-3 py-2 rounded-xl border border-white/10 text-[10px] uppercase font-bold tracking-wider shadow-lg pointer-events-auto">
                            <div class="flex items-center gap-2 mb-1"><span class="w-2 h-2 bg-emerald-500 rounded-full shadow-[0_0_5px_#10b981]"></span> Safe Zone</div>
                            <div class="flex items-center gap-2"><span class="w-2 h-2 bg-red-500 rounded-full shadow-[0_0_5px_#ef4444]"></span> Flood Risk</div>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-6 h-auto lg:h-[600px]">
                    
                    <div class="bg-[#0f1115]/60 backdrop-blur-xl border border-white/10 rounded-3xl p-6 flex-1 flex flex-col min-h-[250px]">
                        <h4 class="text-white font-bold text-xs uppercase tracking-widest mb-4 flex items-center gap-2 opacity-80">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Sensor Trends
                        </h4>
                        <div class="flex-1 relative w-full h-full">
                            <Line :data="chartData" :options="chartOptions" />
                        </div>
                    </div>

                    <div class="bg-[#0f1115]/60 backdrop-blur-xl border border-white/10 rounded-3xl p-6 h-[300px] lg:h-2/5 overflow-hidden flex flex-col">
                        <h4 class="text-white font-bold text-xs uppercase tracking-widest mb-3 flex items-center gap-2 opacity-80">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span> Reports
                        </h4>
                        <div class="space-y-2 overflow-y-auto pr-2 custom-scrollbar flex-1">
                            <div class="flex items-start gap-3 p-3 bg-white/5 hover:bg-white/10 rounded-xl border border-white/5 transition-colors cursor-pointer group">
                                <div class="w-8 h-8 rounded-lg bg-red-500/20 flex items-center justify-center text-red-400 text-xs flex-shrink-0">📸</div>
                                <div>
                                    <p class="text-slate-200 text-xs font-bold group-hover:text-white">Uncollected Trash</p>
                                    <p class="text-[10px] text-slate-500">Zone 4 • 10m ago</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3 p-3 bg-white/5 hover:bg-white/10 rounded-xl border border-white/5 transition-colors cursor-pointer group">
                                <div class="w-8 h-8 rounded-lg bg-yellow-500/20 flex items-center justify-center text-yellow-400 text-xs flex-shrink-0">⚠️</div>
                                <div>
                                    <p class="text-slate-200 text-xs font-bold group-hover:text-white">Clogged Drainage</p>
                                    <p class="text-[10px] text-slate-500">San Jose • 1h ago</p>
                                </div>
                            </div>
                             <div class="flex items-start gap-3 p-3 bg-white/5 hover:bg-white/10 rounded-xl border border-white/5 transition-colors cursor-pointer group">
                                <div class="w-8 h-8 rounded-lg bg-blue-500/20 flex items-center justify-center text-blue-400 text-xs flex-shrink-0">💧</div>
                                <div>
                                    <p class="text-slate-200 text-xs font-bold group-hover:text-white">Water Leak</p>
                                    <p class="text-[10px] text-slate-500">Market • 2h ago</p>
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
/* 🔴 ANIMATIONS */
@keyframes slide-bg {
    0% { background-position: 0 0; }
    100% { background-position: 40px 0; }
}
.animate-slide-bg {
    animation: slide-bg 1s linear infinite;
}

@keyframes pulse-slow {
    0%, 100% { opacity: 0.3; }
    50% { opacity: 0.6; }
}
.animate-pulse-slow { animation: pulse-slow 3s ease-in-out infinite; }

/* Custom Scrollbar for Feed */
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.2); }

/* Fade Transition */
.fade-enter-active, .fade-leave-active { transition: opacity 0.5s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>