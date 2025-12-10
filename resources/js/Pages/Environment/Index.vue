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
    active_alert: Object // Can be null
});

// -- SIMULATION TOGGLE (For Demo Purposes) --
const isDisasterMode = ref(!!props.active_alert);
const toggleSimulation = () => isDisasterMode.value = !isDisasterMode.value;

// -- CHART CONFIG --
const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    scales: {
        y: { grid: { color: 'rgba(255,255,255,0.1)' }, ticks: { color: '#94a3b8' } },
        x: { grid: { display: false }, ticks: { color: '#94a3b8' } }
    },
    plugins: { legend: { display: false } }
};

const chartData = {
    labels: props.chart_data.labels,
    datasets: [{
        label: 'Air Quality (AQI)',
        backgroundColor: (ctx) => {
            const canvas = ctx.chart.ctx;
            const gradient = canvas.createLinearGradient(0, 0, 0, 300);
            gradient.addColorStop(0, 'rgba(74, 222, 128, 0.5)'); // Green top
            gradient.addColorStop(1, 'rgba(74, 222, 128, 0)');   // Transparent bottom
            return gradient;
        },
        borderColor: '#4ade80',
        borderWidth: 2,
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
    <Head title="Green Guard" />

    <AuthenticatedLayout>
        <div v-if="isDisasterMode" class="fixed inset-0 z-[100] bg-red-900/90 backdrop-blur-md flex items-center justify-center p-4 animate-pulse-slow pointer-events-none">
            </div>
        
        <div v-if="isDisasterMode" class="fixed inset-x-0 top-20 z-[101] max-w-4xl mx-auto px-4">
            <div class="bg-black/80 border-2 border-red-500 rounded-3xl p-8 shadow-[0_0_50px_rgba(239,68,68,0.6)] text-center relative overflow-hidden">
                <div class="absolute inset-0 opacity-10 bg-[repeating-linear-gradient(45deg,#ef4444,#ef4444_10px,transparent_10px,transparent_20px)]"></div>
                
                <div class="relative z-10">
                    <div class="inline-flex items-center gap-3 bg-red-600 text-white px-6 py-2 rounded-full font-bold text-xl uppercase tracking-widest mb-6 animate-bounce">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        Emergency Alert: Typhoon Signal #3
                    </div>
                    <h2 class="text-4xl md:text-6xl font-black text-white mb-4">EVACUATE IMMEDIATELY</h2>
                    <p class="text-xl text-red-200 mb-8">Heavy rainfall and potential flooding detected in Zone 4. Proceed to the nearest evacuation center.</p>
                    
                    <div class="flex justify-center gap-4">
                        <button @click="toggleSimulation" class="px-8 py-3 bg-white text-red-700 font-bold rounded-xl hover:bg-gray-200 transition">
                            I am Safe
                        </button>
                        <button class="px-8 py-3 bg-red-600 text-white font-bold rounded-xl hover:bg-red-500 transition animate-pulse">
                            Request Rescue
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <template #header>
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-lime-100 rounded-xl text-lime-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h2 class="font-bold text-xl text-gray-800 leading-tight">Environment Command</h2>
                </div>
                
                <button @click="toggleSimulation" :class="['px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-widest border transition-all', isDisasterMode ? 'bg-red-600 text-white border-red-500' : 'bg-slate-200 text-slate-600 border-slate-300 hover:bg-red-100 hover:text-red-600']">
                    {{ isDisasterMode ? 'End Simulation' : 'Simulate Disaster' }}
                </button>
            </div>
        </template>

        <div class="py-8 bg-slate-900 min-h-screen relative overflow-hidden">
            
            <div class="absolute inset-0 opacity-20 pointer-events-none" style="background-image: radial-gradient(#84cc16 1px, transparent 1px); background-size: 40px 40px;"></div>

            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 relative z-10 space-y-6">
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="bg-slate-800/80 backdrop-blur border border-white/10 rounded-2xl p-6">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-xs text-lime-400 font-bold uppercase tracking-wider">Air Quality</p>
                                <h3 class="text-4xl font-bold text-white mt-1">{{ current.aqi }}</h3>
                            </div>
                            <div class="w-10 h-10 rounded-full bg-lime-500/20 flex items-center justify-center text-lime-400">🍃</div>
                        </div>
                        <div class="mt-4">
                            <div class="w-full bg-slate-700 h-1.5 rounded-full overflow-hidden">
                                <div class="bg-lime-500 h-full" style="width: 42%"></div>
                            </div>
                            <p class="text-[10px] text-slate-400 mt-2">PM2.5 Levels: Good</p>
                        </div>
                    </div>

                    <div class="bg-slate-800/80 backdrop-blur border border-white/10 rounded-2xl p-6">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-xs text-orange-400 font-bold uppercase tracking-wider">Heat Index</p>
                                <h3 class="text-4xl font-bold text-white mt-1">{{ current.temp }}°C</h3>
                            </div>
                            <div class="w-10 h-10 rounded-full bg-orange-500/20 flex items-center justify-center text-orange-400">☀️</div>
                        </div>
                        <p class="text-[10px] text-slate-400 mt-6">Humidity: {{ current.humidity }}%</p>
                    </div>

                    <div class="bg-slate-800/80 backdrop-blur border border-white/10 rounded-2xl p-6">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-xs text-blue-400 font-bold uppercase tracking-wider">River Level</p>
                                <h3 class="text-2xl font-bold text-white mt-2">{{ current.water_level }}</h3>
                            </div>
                            <div class="w-10 h-10 rounded-full bg-blue-500/20 flex items-center justify-center text-blue-400 animate-pulse">🌊</div>
                        </div>
                        <p class="text-[10px] text-slate-400 mt-6">Sensor: Bicol River Bridge</p>
                    </div>

                    <div :class="['rounded-2xl p-6 flex flex-col justify-center items-center text-center border', isDisasterMode ? 'bg-red-600 border-red-400 animate-pulse' : 'bg-green-600 border-green-400']">
                        <span class="text-6xl mb-2">{{ isDisasterMode ? '⚠️' : '🛡️' }}</span>
                        <h3 class="font-bold text-white text-lg uppercase">{{ isDisasterMode ? 'DANGER' : 'SAFE' }}</h3>
                        <p class="text-white/80 text-xs">{{ isDisasterMode ? 'Evacuation Protocols Active' : 'All systems normal' }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 h-[500px]">
                    
                    <div class="lg:col-span-2 bg-slate-800 rounded-3xl overflow-hidden border border-white/10 relative shadow-2xl">
                        <l-map ref="map" v-model:zoom="zoom" :center="center" :use-global-leaflet="false">
                            <l-tile-layer
                                url="https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png"
                                layer-type="base"
                                name="Dark Map"
                            ></l-tile-layer>

                            <l-marker v-for="center in map_data.evacuation_centers" :key="center.id" :lat-lng="[center.lat, center.lng]">
                                <l-popup>
                                    <div class="text-slate-800">
                                        <strong class="text-green-700">Evacuation Center</strong><br>
                                        {{ center.name }}<br>
                                        Capacity: {{ center.capacity }}
                                    </div>
                                </l-popup>
                            </l-marker>

                            <l-circle 
                                v-for="(zone, index) in map_data.flood_zones" 
                                :key="index"
                                :lat-lng="[zone.lat, zone.lng]" 
                                :radius="zone.radius" 
                                color="red" 
                                :fill="true"
                                :fillOpacity="0.3"
                            >
                                <l-popup class="text-red-600 font-bold">High Risk Flood Zone</l-popup>
                            </l-circle>
                        </l-map>

                        <div class="absolute top-4 right-4 z-[400] flex flex-col gap-2">
                            <div class="bg-slate-900/90 backdrop-blur text-white px-3 py-2 rounded-lg border border-white/10 text-xs shadow-lg">
                                <div class="flex items-center gap-2 mb-1"><span class="w-3 h-3 bg-green-500 rounded-full"></span> Safe Zone</div>
                                <div class="flex items-center gap-2"><span class="w-3 h-3 bg-red-500/50 border border-red-500 rounded-full"></span> Flood Risk</div>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col gap-6">
                        
                        <div class="bg-slate-800/80 backdrop-blur border border-white/10 rounded-3xl p-6 flex-1 flex flex-col">
                            <h4 class="text-white font-bold text-sm mb-4 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-lime-400"></span> Live Sensor Feed
                            </h4>
                            <div class="flex-1 relative">
                                <Line :data="chartData" :options="chartOptions" />
                            </div>
                        </div>

                        <div class="bg-slate-800/80 backdrop-blur border border-white/10 rounded-3xl p-6 h-1/3 overflow-y-auto">
                            <h4 class="text-white font-bold text-sm mb-3">Community Reports</h4>
                            <div class="space-y-3">
                                <div class="flex items-start gap-3 p-2 hover:bg-white/5 rounded-lg transition">
                                    <div class="mt-1 text-xs">🔴</div>
                                    <div>
                                        <p class="text-slate-200 text-xs font-bold">Uncollected Trash</p>
                                        <p class="text-[10px] text-slate-500">Zone 4 • 10 mins ago</p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3 p-2 hover:bg-white/5 rounded-lg transition">
                                    <div class="mt-1 text-xs">🟡</div>
                                    <div>
                                        <p class="text-slate-200 text-xs font-bold">Clogged Drainage</p>
                                        <p class="text-[10px] text-slate-500">San Jose St. • 1 hr ago</p>
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
.animate-pulse-slow { animation: pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite; }
</style>