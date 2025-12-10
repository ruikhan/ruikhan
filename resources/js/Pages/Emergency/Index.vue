<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({ hotlines: Array });

// SOS Logic
const sosForm = useForm({});
const isSendingSOS = ref(false);
const showSOSModal = ref(false);

const triggerSOS = () => {
    isSendingSOS.value = true;
    // Simulate a 2-second transmission delay for realism
    setTimeout(() => {
        sosForm.post(route('emergency.store'), {
            onSuccess: () => {
                isSendingSOS.value = false;
                showSOSModal.value = true;
            }
        });
    }, 2000);
};
</script>

<template>
    <Head title="Emergency Command" />

    <AuthenticatedLayout>
        
        <div class="min-h-screen bg-slate-900 relative overflow-hidden font-sans pb-20">
            <div class="absolute inset-0 bg-red-900/10 animate-pulse-slow pointer-events-none"></div>
            <div class="absolute top-[-20%] right-[-20%] w-[800px] h-[800px] bg-red-600/20 rounded-full blur-[150px] animate-pulse-slow"></div>
            <div class="absolute inset-0 opacity-20" style="background-image: repeating-linear-gradient(45deg, #ef4444 0, #ef4444 1px, transparent 0, transparent 50%); background-size: 20px 20px;"></div>

            <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8">

                <div class="flex flex-col md:flex-row justify-between items-center mb-12 border-b border-red-500/30 pb-6 bg-slate-900/80 backdrop-blur-xl p-6 rounded-3xl shadow-2xl shadow-red-900/20">
                    <div class="flex items-center gap-4">
                        <div class="relative">
                            <div class="w-16 h-16 bg-red-600 rounded-2xl flex items-center justify-center text-3xl shadow-[0_0_30px_rgba(220,38,38,0.6)] animate-pulse">
                                🆘
                            </div>
                            <span class="absolute -top-1 -right-1 flex h-4 w-4">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-4 w-4 bg-red-500"></span>
                            </span>
                        </div>
                        <div>
                            <h1 class="text-3xl md:text-4xl font-black text-white tracking-tight uppercase">Emergency Command</h1>
                            <p class="text-red-400 font-mono text-sm tracking-widest">RAPID RESPONSE SYSTEM ACTIVE</p>
                        </div>
                    </div>
                    
                    <div class="mt-4 md:mt-0 px-6 py-2 bg-red-950/50 border border-red-500/50 rounded-xl flex items-center gap-3">
                        <div class="w-3 h-3 bg-green-500 rounded-full shadow-[0_0_10px_#22c55e]"></div>
                        <span class="text-red-200 text-xs font-bold uppercase tracking-wider">GPS Location Tracking: ON</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                    <div class="lg:col-span-1 order-2 lg:order-1">
                        <div class="bg-gradient-to-b from-slate-800 to-slate-900 border border-red-500/30 rounded-3xl p-8 text-center shadow-2xl relative overflow-hidden group">
                            
                            <h2 class="text-white font-bold text-xl mb-2">Request Immediate Rescue</h2>
                            <p class="text-slate-400 text-sm mb-8">Only use in life-threatening situations.</p>

                            <button 
                                @click="triggerSOS"
                                :disabled="isSendingSOS"
                                class="relative w-48 h-48 mx-auto rounded-full bg-gradient-to-br from-red-600 to-red-800 shadow-[0_0_50px_rgba(220,38,38,0.4)] border-4 border-red-500 flex items-center justify-center transition-all duration-300 active:scale-95 active:shadow-none hover:shadow-[0_0_80px_rgba(220,38,38,0.7)] group-hover:scale-105"
                            >
                                <span v-if="!isSendingSOS" class="text-white font-black text-3xl tracking-widest drop-shadow-md">SOS</span>
                                <span v-else class="text-white font-bold text-xl animate-pulse">SENDING...</span>
                                
                                <span v-if="!isSendingSOS" class="absolute inset-0 rounded-full border border-red-500 opacity-50 animate-ping-slow"></span>
                            </button>

                            <p class="mt-8 text-[10px] text-slate-500 uppercase tracking-widest">
                                Pressing this alerts Police & Medical units with your coordinates.
                            </p>
                        </div>

                        <div class="mt-6 bg-slate-800/80 backdrop-blur border border-white/10 rounded-3xl p-6">
                            <h3 class="text-slate-300 font-bold text-sm uppercase tracking-wider mb-4 border-b border-white/5 pb-2">Quick Guides</h3>
                            <div class="space-y-3">
                                <div class="flex items-center gap-3 p-3 bg-white/5 rounded-xl hover:bg-white/10 cursor-pointer transition">
                                    <span class="text-xl">🔥</span>
                                    <div>
                                        <p class="text-white font-bold text-sm">Fire Safety</p>
                                        <p class="text-slate-400 text-xs">Drop, Cover, and Roll protocols.</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3 p-3 bg-white/5 rounded-xl hover:bg-white/10 cursor-pointer transition">
                                    <span class="text-xl">🌊</span>
                                    <div>
                                        <p class="text-white font-bold text-sm">Flood Evaluation</p>
                                        <p class="text-slate-400 text-xs">Nearest high-ground centers.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-2 order-1 lg:order-2">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            
                            <div v-for="hotline in hotlines" :key="hotline.number" 
                                 class="group bg-slate-800/80 backdrop-blur hover:bg-slate-800 border border-white/10 rounded-3xl p-6 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl relative overflow-hidden">
                                
                                <div :class="`absolute inset-0 bg-gradient-to-br from-${hotline.color}-500/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500`"></div>

                                <div class="relative z-10 flex justify-between items-start">
                                    <div>
                                        <div class="text-4xl mb-4 grayscale group-hover:grayscale-0 transition-all duration-300 transform group-hover:scale-110 origin-left">{{ hotline.icon }}</div>
                                        <h3 class="text-lg font-bold text-slate-200 group-hover:text-white">{{ hotline.name }}</h3>
                                        <p class="text-slate-500 text-xs uppercase tracking-wider">Direct Line</p>
                                    </div>
                                    <a :href="`tel:${hotline.number}`" :class="`flex items-center gap-2 px-4 py-2 rounded-lg bg-${hotline.color}-600/20 text-${hotline.color}-400 border border-${hotline.color}-600/30 font-mono font-bold text-lg group-hover:bg-${hotline.color}-600 group-hover:text-white transition-all`">
                                        📞 {{ hotline.number }}
                                    </a>
                                </div>
                            </div>

                        </div>

                        <div class="mt-6 bg-slate-800 rounded-3xl overflow-hidden border border-white/10 h-64 relative group">
                            <div class="absolute inset-0 opacity-40 bg-[url('https://upload.wikimedia.org/wikipedia/commons/e/ec/World_map_blank_without_borders.svg')] bg-cover bg-center grayscale group-hover:grayscale-0 transition-all duration-700"></div>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div class="text-center">
                                    <div class="inline-block p-3 rounded-full bg-red-600/20 text-red-500 mb-2 border border-red-500/50 animate-pulse">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    </div>
                                    <p class="text-white font-bold">Nearest Evacuation Center</p>
                                    <p class="text-slate-400 text-sm">Pili Sports Complex (1.2km away)</p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>

        <div v-if="showSOSModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm">
            <div class="bg-slate-900 border border-white/10 rounded-2xl p-8 max-w-md w-full text-center shadow-2xl relative overflow-hidden">
                <div class="absolute inset-0 bg-green-500/10 pointer-events-none"></div>
                <div class="text-6xl mb-4">✅</div>
                <h3 class="text-2xl font-bold text-white mb-2">Signal Received</h3>
                <p class="text-slate-300 text-sm mb-6">Rescue units have been dispatched to your GPS coordinates. Stay calm and stay where you are.</p>
                <button @click="showSOSModal = false" class="w-full py-3 bg-green-600 hover:bg-green-500 text-white font-bold rounded-xl transition-colors">
                    Close
                </button>
            </div>
        </div>

    </AuthenticatedLayout>
</template>

<style scoped>
.animate-pulse-slow { animation: pulse 4s ease-in-out infinite; }
.animate-ping-slow { animation: ping 2s cubic-bezier(0, 0, 0.2, 1) infinite; }
</style>