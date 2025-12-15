<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const form = useForm({
    category: '',
    subject: '',
    description: '',
    location: '',
    evidence: null,
});

// Image Preview Logic
const previewUrl = ref(null);

const handleFileUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.evidence = file;
        previewUrl.value = URL.createObjectURL(file);
    }
};

const submit = () => {
    form.post(route('concerns.store'), {
        forceFormData: true,
    });
};
</script>

<template>
    <Head title="Report Incident" />

    <AuthenticatedLayout>
        <div class="fixed inset-0 z-0 pointer-events-none">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,_var(--tw-gradient-stops))] from-[#1a0505] via-[#050505] to-black"></div>
            <div class="absolute inset-0 opacity-[0.03] bg-[url('https://grainy-gradients.vercel.app/noise.svg')] brightness-100 contrast-150"></div>
            <div class="absolute top-0 right-0 w-[60vw] h-[60vw] bg-red-900/10 rounded-full blur-[120px]"></div>
        </div>

        <div class="relative z-10 py-12 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-8 gap-4">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-red-500/10 border border-red-500/20 backdrop-blur-md mb-2">
                        <span class="w-1.5 h-1.5 bg-red-500 rounded-full animate-pulse"></span>
                        <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-red-400">Citizen Watch</span>
                    </div>
                    <h2 class="font-black text-3xl md:text-4xl text-white tracking-tight">Report Incident</h2>
                    <p class="text-slate-400 text-sm mt-1">Submit real-time reports to the command center.</p>
                </div>
                
                <div class="hidden md:block text-right">
                    <div class="text-[10px] text-slate-500 font-mono uppercase tracking-widest mb-1">System Status</div>
                    <div class="flex items-center justify-end gap-2 text-emerald-400 text-xs font-bold uppercase tracking-wider">
                        <span class="relative flex h-2 w-2">
                          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                          <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        </span>
                        Monitoring Active
                    </div>
                </div>
            </div>

            <div class="flex flex-col lg:flex-row gap-8">
                
                <div class="w-full lg:w-2/3">
                    <form @submit.prevent="submit" enctype="multipart/form-data" class="bg-[#0f1115]/60 backdrop-blur-xl border border-white/10 rounded-[2rem] shadow-2xl overflow-hidden animate-fade-in-up">
                        
                        <div class="p-6 border-b border-white/5 flex justify-between items-center bg-white/5">
                            <h3 class="font-bold text-white flex items-center gap-2 text-sm">
                                <span>📝</span> Incident Details
                            </h3>
                            <span class="text-[10px] bg-red-500/10 border border-red-500/20 text-red-400 px-2 py-1 rounded-lg font-bold uppercase tracking-wider">Priority Handling</span>
                        </div>

                        <div class="p-8 space-y-8">
                            
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-4">01. Select Category</label>
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                    
                                    <label class="cursor-pointer group">
                                        <input type="radio" v-model="form.category" value="Infrastructure" class="peer sr-only">
                                        <div class="p-4 rounded-2xl border border-white/10 bg-white/5 peer-checked:bg-red-500/10 peer-checked:border-red-500 peer-checked:text-red-400 hover:bg-white/10 transition-all text-center h-full flex flex-col items-center justify-center gap-2">
                                            <div class="text-2xl grayscale group-hover:grayscale-0 peer-checked:grayscale-0 transition-all">🚧</div>
                                            <div class="text-xs font-bold text-slate-300 peer-checked:text-red-400">Infrastructure</div>
                                        </div>
                                    </label>

                                    <label class="cursor-pointer group">
                                        <input type="radio" v-model="form.category" value="Garbage" class="peer sr-only">
                                        <div class="p-4 rounded-2xl border border-white/10 bg-white/5 peer-checked:bg-red-500/10 peer-checked:border-red-500 peer-checked:text-red-400 hover:bg-white/10 transition-all text-center h-full flex flex-col items-center justify-center gap-2">
                                            <div class="text-2xl grayscale group-hover:grayscale-0 peer-checked:grayscale-0 transition-all">🗑️</div>
                                            <div class="text-xs font-bold text-slate-300 peer-checked:text-red-400">Sanitation</div>
                                        </div>
                                    </label>

                                    <label class="cursor-pointer group">
                                        <input type="radio" v-model="form.category" value="Public Disturbance" class="peer sr-only">
                                        <div class="p-4 rounded-2xl border border-white/10 bg-white/5 peer-checked:bg-red-500/10 peer-checked:border-red-500 peer-checked:text-red-400 hover:bg-white/10 transition-all text-center h-full flex flex-col items-center justify-center gap-2">
                                            <div class="text-2xl grayscale group-hover:grayscale-0 peer-checked:grayscale-0 transition-all">📢</div>
                                            <div class="text-xs font-bold text-slate-300 peer-checked:text-red-400">Disturbance</div>
                                        </div>
                                    </label>

                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-1.5">
                                    <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest ml-1">Subject</label>
                                    <input v-model="form.subject" type="text" placeholder="e.g. Broken Street Light" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:ring-2 focus:ring-red-500/50 focus:border-transparent transition-all placeholder-slate-600">
                                </div>
                                <div class="space-y-1.5">
                                    <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest ml-1">Location</label>
                                    <div class="relative group">
                                        <span class="absolute left-3 top-3.5 text-slate-500 group-focus-within:text-red-400 transition-colors">📍</span>
                                        <input v-model="form.location" type="text" placeholder="Street / Barangay" class="w-full bg-white/5 border border-white/10 rounded-xl pl-10 pr-4 py-3 text-white text-sm focus:ring-2 focus:ring-red-500/50 focus:border-transparent transition-all placeholder-slate-600">
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-1.5">
                                <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest ml-1">Description</label>
                                <textarea v-model="form.description" rows="3" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:ring-2 focus:ring-red-500/50 focus:border-transparent transition-all resize-none placeholder-slate-600" placeholder="Provide details..."></textarea>
                            </div>

                            <div>
                                <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2 block ml-1">Photo Evidence</label>
                                <div class="relative border-2 border-dashed border-white/10 rounded-2xl p-8 text-center hover:bg-white/5 hover:border-red-500/30 transition-all group cursor-pointer overflow-hidden bg-black/20">
                                    <input type="file" @change="handleFileUpload" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20">
                                    
                                    <div v-if="!previewUrl" class="space-y-3 relative z-10 pointer-events-none">
                                        <div class="mx-auto w-14 h-14 bg-white/5 rounded-full flex items-center justify-center text-slate-400 group-hover:text-red-400 group-hover:scale-110 transition-all shadow-lg">
                                            📸
                                        </div>
                                        <div>
                                            <p class="text-sm text-slate-300 font-bold group-hover:text-white transition-colors">Click or Drop Photo</p>
                                            <p class="text-xs text-slate-600">JPG, PNG (Max 2MB)</p>
                                        </div>
                                    </div>

                                    <div v-else class="relative z-10">
                                        <img :src="previewUrl" class="h-48 mx-auto rounded-xl shadow-2xl border border-white/10 object-cover">
                                        <div class="mt-4 inline-flex items-center gap-2 px-3 py-1 bg-green-500/20 text-green-400 rounded-full text-[10px] font-bold uppercase tracking-wider border border-green-500/30">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            Image Attached
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-black/20 px-8 py-5 border-t border-white/5 flex justify-end">
                            <button :disabled="form.processing" class="bg-red-600 hover:bg-red-500 text-white font-bold py-3 px-8 rounded-xl shadow-[0_0_20px_rgba(220,38,38,0.4)] transform hover:-translate-y-0.5 transition-all flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                                <span v-if="form.processing" class="animate-spin h-4 w-4 border-2 border-white/30 border-t-white rounded-full"></span>
                                <span v-else>Submit Report</span>
                            </button>
                        </div>
                    </form>
                </div>

                <div class="w-full lg:w-1/3 space-y-6">
                    
                    <div class="bg-[#0f1115]/60 backdrop-blur-xl rounded-[2rem] border border-white/10 overflow-hidden h-64 relative group">
                        <div class="absolute inset-0 opacity-40 bg-[url('https://www.transparenttextures.com/patterns/diagmonds-light.png')]"></div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent"></div>
                        
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="bg-black/60 backdrop-blur px-4 py-2 rounded-xl border border-red-500/30 text-red-400 text-[10px] font-mono tracking-widest flex items-center gap-2 shadow-lg">
                                <span class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
                                GPS TRACKING ACTIVE
                            </div>
                        </div>
                    </div>

                    <div class="bg-[#0f1115]/60 backdrop-blur-xl rounded-[2rem] p-8 border border-white/10 relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-20 h-20 bg-white/5 rounded-bl-[4rem]"></div>
                        
                        <h4 class="font-bold text-white mb-4 text-sm uppercase tracking-wider flex items-center gap-2">
                            <span class="text-red-500">⚠️</span> Protocols
                        </h4>
                        <ul class="text-sm text-slate-400 space-y-3">
                            <li class="flex gap-3 items-start">
                                <span class="text-red-500 mt-1">●</span> 
                                <span>Provide exact landmarks for faster dispatch.</span>
                            </li>
                            <li class="flex gap-3 items-start">
                                <span class="text-red-500 mt-1">●</span> 
                                <span>Photos significantly increase priority level.</span>
                            </li>
                            <li class="flex gap-3 items-start">
                                <span class="text-red-500 mt-1">●</span> 
                                <span>False reports are tracked and penalized under Ordinance 2024.</span>
                            </li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.animate-fade-in-up { animation: fadeInUp 0.6s ease-out forwards; }
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>