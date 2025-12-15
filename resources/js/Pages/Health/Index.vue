<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const form = useForm({
    type: 'Consultation',
    date: '',
    symptoms: ''
});

const activeTab = ref('book'); // 'book' or 'records'

const submit = () => {
    alert("Appointment Request Sent to Health Center!"); 
    // In real app: form.post(route('health.store'));
};
</script>

<template>
    <Head title="E-Health" />

    <AuthenticatedLayout>
        <div class="fixed inset-0 z-0 pointer-events-none">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,_var(--tw-gradient-stops))] from-[#0f172a] via-[#050505] to-black"></div>
            <div class="absolute inset-0 opacity-[0.03] bg-[url('https://grainy-gradients.vercel.app/noise.svg')] brightness-100 contrast-150"></div>
            <div class="absolute top-[20%] left-[-10%] w-[50vw] h-[50vw] bg-teal-600/10 rounded-full blur-[120px]"></div>
        </div>

        <div class="relative z-10 py-12 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-6">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-teal-500/10 rounded-2xl border border-teal-500/20 text-teal-400">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                    </div>
                    <div>
                        <h2 class="font-black text-3xl text-white tracking-tight">Health Connect</h2>
                        <p class="text-slate-400 text-sm">Provincial Rural Health Unit System</p>
                    </div>
                </div>

                <div class="bg-black/40 p-1 rounded-xl border border-white/10 flex relative">
                    <div class="absolute top-1 bottom-1 w-[calc(50%-4px)] bg-teal-600 rounded-lg transition-all duration-300 shadow-lg shadow-teal-500/20"
                         :class="activeTab === 'book' ? 'left-1' : 'left-[calc(50%+4px)]'"></div>
                    
                    <button @click="activeTab = 'book'" class="relative z-10 px-6 py-2 text-xs font-bold uppercase tracking-widest transition-colors w-32"
                            :class="activeTab === 'book' ? 'text-white' : 'text-slate-400 hover:text-white'">
                        Book
                    </button>
                    <button @click="activeTab = 'records'" class="relative z-10 px-6 py-2 text-xs font-bold uppercase tracking-widest transition-colors w-32"
                            :class="activeTab === 'records' ? 'text-white' : 'text-slate-400 hover:text-white'">
                        Records
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <div class="lg:col-span-2">
                    
                    <Transition name="fade" mode="out-in">
                        <div v-if="activeTab === 'book'" class="bg-[#0f1115]/60 backdrop-blur-xl rounded-[2rem] border border-white/10 p-8 shadow-2xl">
                            <h3 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
                                <span class="text-teal-400">📅</span> New Appointment
                            </h3>
                            
                            <form @submit.prevent="submit" class="space-y-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="space-y-1.5">
                                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Service Type</label>
                                        <div class="relative">
                                            <select v-model="form.type" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white appearance-none focus:ring-2 focus:ring-teal-500/50 focus:border-transparent transition-all cursor-pointer hover:bg-white/10">
                                                <option class="bg-slate-900">General Consultation</option>
                                                <option class="bg-slate-900">Pediatrics (Child)</option>
                                                <option class="bg-slate-900">Dental Checkup</option>
                                                <option class="bg-slate-900">Vaccination</option>
                                            </select>
                                            <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-slate-500">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="space-y-1.5">
                                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Preferred Date</label>
                                        <input type="date" v-model="form.date" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-teal-500/50 focus:border-transparent transition-all hover:bg-white/10 [color-scheme:dark]">
                                    </div>
                                </div>

                                <div class="space-y-1.5">
                                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Symptoms / Reason</label>
                                    <textarea v-model="form.symptoms" rows="4" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:ring-2 focus:ring-teal-500/50 focus:border-transparent transition-all resize-none hover:bg-white/10 placeholder-slate-600" placeholder="Describe what you are feeling..."></textarea>
                                </div>

                                <div class="pt-4 flex justify-end">
                                    <button class="bg-teal-600 hover:bg-teal-500 text-white font-bold py-3.5 px-8 rounded-xl shadow-lg shadow-teal-500/20 transform hover:-translate-y-0.5 transition-all flex items-center gap-2">
                                        <span>Confirm Booking</span>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                    </button>
                                </div>
                            </form>
                        </div>

                        <div v-else class="space-y-4">
                            <div class="group bg-[#0f1115]/60 backdrop-blur-xl p-6 rounded-2xl border border-white/10 hover:border-teal-500/30 transition-all cursor-pointer">
                                <div class="flex justify-between items-start">
                                    <div class="flex gap-4">
                                        <div class="w-12 h-12 rounded-xl bg-teal-500/10 flex items-center justify-center text-teal-400 text-2xl group-hover:scale-110 transition-transform">💉</div>
                                        <div>
                                            <h4 class="font-bold text-white text-lg group-hover:text-teal-300 transition-colors">COVID-19 Booster Shot</h4>
                                            <p class="text-xs text-slate-400 mt-1">Pili Sports Complex • Jan 15, 2024</p>
                                        </div>
                                    </div>
                                    <span class="px-3 py-1 bg-teal-500/10 text-teal-400 rounded-lg text-[10px] font-bold uppercase border border-teal-500/20">Vaccinated</span>
                                </div>
                            </div>

                            <div class="group bg-[#0f1115]/60 backdrop-blur-xl p-6 rounded-2xl border border-white/10 hover:border-blue-500/30 transition-all cursor-pointer">
                                <div class="flex justify-between items-start">
                                    <div class="flex gap-4">
                                        <div class="w-12 h-12 rounded-xl bg-blue-500/10 flex items-center justify-center text-blue-400 text-2xl group-hover:scale-110 transition-transform">🩺</div>
                                        <div>
                                            <h4 class="font-bold text-white text-lg group-hover:text-blue-300 transition-colors">Annual Physical Exam</h4>
                                            <p class="text-xs text-slate-400 mt-1">Dr. Santos - RHU 1 • Dec 10, 2023</p>
                                        </div>
                                    </div>
                                    <span class="px-3 py-1 bg-blue-500/10 text-blue-400 rounded-lg text-[10px] font-bold uppercase border border-blue-500/20">Normal</span>
                                </div>
                            </div>
                        </div>
                    </Transition>
                </div>

                <div class="bg-black/40 backdrop-blur-xl rounded-[2rem] p-6 border border-white/10 relative overflow-hidden h-fit">
                    
                    <div class="absolute top-0 right-0 w-32 h-32 bg-teal-500/20 rounded-full blur-[60px]"></div>

                    <div class="flex items-center justify-between mb-6 border-b border-white/10 pb-4 relative z-10">
                        <h4 class="font-bold text-xs uppercase tracking-widest text-slate-300">Live Queue</h4>
                        <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse shadow-[0_0_10px_#22c55e]"></span>
                    </div>

                    <div class="space-y-6 relative z-10">
                        <div class="flex items-center gap-4">
                            <div class="relative">
                                <div class="w-14 h-14 bg-white/5 rounded-2xl flex items-center justify-center text-2xl border border-white/5">👨‍⚕️</div>
                                <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-500 border-2 border-black rounded-full"></div>
                            </div>
                            <div>
                                <p class="font-bold text-white text-sm">Dr. A. Cruz</p>
                                <p class="text-[10px] text-slate-500 uppercase tracking-wider mb-1">General Medicine</p>
                                <div class="inline-block bg-teal-500/20 text-teal-300 text-[10px] font-mono px-2 py-0.5 rounded border border-teal-500/30">
                                    Serving #42
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-4 opacity-70">
                            <div class="relative">
                                <div class="w-14 h-14 bg-white/5 rounded-2xl flex items-center justify-center text-2xl border border-white/5">👩‍⚕️</div>
                                <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-yellow-500 border-2 border-black rounded-full"></div>
                            </div>
                            <div>
                                <p class="font-bold text-white text-sm">Dr. B. Reyes</p>
                                <p class="text-[10px] text-slate-500 uppercase tracking-wider mb-1">Dental</p>
                                <div class="inline-block bg-yellow-500/20 text-yellow-300 text-[10px] font-mono px-2 py-0.5 rounded border border-yellow-500/30">
                                    On Break
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 bg-white/5 rounded-2xl p-5 text-center border border-white/5 relative z-10">
                        <p class="text-[10px] text-slate-400 uppercase tracking-widest font-bold mb-1">Estimated Wait</p>
                        <p class="text-3xl font-black text-white tracking-tight">15 <span class="text-sm font-medium text-slate-500">mins</span></p>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
/* Smooth Fade Transition */
.fade-enter-active, .fade-leave-active { transition: opacity 0.3s ease, transform 0.3s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; transform: translateY(10px); }
</style>