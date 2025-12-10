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
        <template #header>
            <div class="relative overflow-hidden rounded-xl bg-slate-900 border-b-4 border-red-600">
                <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-10"></div>
                <div class="absolute right-0 top-0 w-64 h-64 bg-red-600/20 blur-[80px]"></div>
                
                <div class="relative flex items-center justify-between py-6 px-4">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-red-600 to-red-800 rounded-xl flex items-center justify-center text-3xl shadow-lg shadow-red-900/50 animate-pulse-slow">
                            🚨
                        </div>
                        <div>
                            <h2 class="font-bold text-2xl text-white tracking-tight">Citizen Watch</h2>
                            <p class="text-red-400 text-xs uppercase tracking-[0.2em] font-bold">Incident Reporting System</p>
                        </div>
                    </div>
                    <div class="hidden md:block text-right">
                        <div class="text-xs text-slate-400 font-mono">SYSTEM STATUS</div>
                        <div class="text-green-400 font-bold text-sm flex items-center justify-end gap-2">
                            <span class="w-2 h-2 bg-green-500 rounded-full animate-ping"></span>
                            ONLINE / MONITORING
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <div class="flex flex-col lg:flex-row gap-8">
                    
                    <div class="w-full lg:w-2/3">
                        <form @submit.prevent="submit" enctype="multipart/form-data" class="bg-white rounded-2xl shadow-xl overflow-hidden border border-slate-100 relative animate-fade-in-up">
                            
                            <div class="bg-slate-50 p-6 border-b border-slate-100 flex justify-between items-center">
                                <h3 class="font-bold text-slate-800 flex items-center gap-2">
                                    <span>📝</span> Incident Details
                                </h3>
                                <span class="text-xs bg-red-100 text-red-700 px-2 py-1 rounded font-bold uppercase">High Priority</span>
                            </div>

                            <div class="p-8 space-y-6">
                                <div>
                                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">01. Select Category</label>
                                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                        <label class="cursor-pointer">
                                            <input type="radio" v-model="form.category" value="Infrastructure" class="peer sr-only">
                                            <div class="p-4 rounded-xl border-2 border-slate-100 bg-slate-50 hover:bg-white hover:border-red-200 peer-checked:border-red-500 peer-checked:bg-red-50 peer-checked:text-red-700 transition-all text-center group">
                                                <div class="text-2xl mb-1 group-hover:scale-110 transition-transform">🚧</div>
                                                <div class="text-xs font-bold">Infrastructure</div>
                                            </div>
                                        </label>
                                        <label class="cursor-pointer">
                                            <input type="radio" v-model="form.category" value="Garbage" class="peer sr-only">
                                            <div class="p-4 rounded-xl border-2 border-slate-100 bg-slate-50 hover:bg-white hover:border-red-200 peer-checked:border-red-500 peer-checked:bg-red-50 peer-checked:text-red-700 transition-all text-center group">
                                                <div class="text-2xl mb-1 group-hover:scale-110 transition-transform">🗑️</div>
                                                <div class="text-xs font-bold">Sanitation</div>
                                            </div>
                                        </label>
                                        <label class="cursor-pointer">
                                            <input type="radio" v-model="form.category" value="Public Disturbance" class="peer sr-only">
                                            <div class="p-4 rounded-xl border-2 border-slate-100 bg-slate-50 hover:bg-white hover:border-red-200 peer-checked:border-red-500 peer-checked:bg-red-50 peer-checked:text-red-700 transition-all text-center group">
                                                <div class="text-2xl mb-1 group-hover:scale-110 transition-transform">📢</div>
                                                <div class="text-xs font-bold">Disturbance</div>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="space-y-2">
                                        <label class="text-xs font-bold text-slate-500 uppercase">Subject</label>
                                        <input v-model="form.subject" type="text" placeholder="e.g. Broken Street Light" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all">
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-xs font-bold text-slate-500 uppercase">Location</label>
                                        <div class="relative">
                                            <input v-model="form.location" type="text" placeholder="Street / Barangay" class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-10 pr-4 py-3 focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all">
                                            <span class="absolute left-3 top-3.5 text-slate-400">📍</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <label class="text-xs font-bold text-slate-500 uppercase">Description</label>
                                    <textarea v-model="form.description" rows="3" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all resize-none"></textarea>
                                </div>

                                <div>
                                    <label class="text-xs font-bold text-slate-500 uppercase mb-2 block">Photo Evidence</label>
                                    <div class="relative border-2 border-dashed border-slate-300 rounded-2xl p-6 text-center hover:bg-slate-50 transition-colors group cursor-pointer overflow-hidden">
                                        <input type="file" @change="handleFileUpload" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20">
                                        
                                        <div v-if="!previewUrl" class="space-y-2">
                                            <div class="mx-auto w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center text-slate-400 group-hover:text-red-500 transition-colors">
                                                📸
                                            </div>
                                            <p class="text-sm text-slate-500 font-medium">Click to upload photo</p>
                                            <p class="text-xs text-slate-400">JPG, PNG (Max 2MB)</p>
                                        </div>

                                        <div v-else class="relative z-10">
                                            <img :src="previewUrl" class="h-40 mx-auto rounded-lg shadow-md object-cover">
                                            <p class="mt-2 text-xs text-green-600 font-bold flex items-center justify-center gap-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                Image Selected
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-slate-50 px-8 py-4 border-t border-slate-200 flex justify-end">
                                <button :disabled="form.processing" class="bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-xl shadow-lg shadow-red-500/30 transform hover:-translate-y-0.5 transition-all flex items-center gap-2">
                                    <span v-if="form.processing" class="animate-spin">⏳</span>
                                    <span>Submit Report</span>
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="w-full lg:w-1/3 space-y-6">
                        <div class="bg-slate-900 rounded-2xl shadow-xl overflow-hidden h-64 relative border border-slate-700 group">
                            <div class="absolute inset-0 bg-[url('https://api.mapbox.com/styles/v1/mapbox/dark-v10/static/123.18,13.62,13,0/600x400?access_token=YOUR_TOKEN')] bg-cover bg-center opacity-50 grayscale group-hover:grayscale-0 transition-all duration-700"></div>
                            <div class="absolute inset-0 bg-[linear-gradient(rgba(0,255,0,0.1)_1px,transparent_1px),linear-gradient(90deg,rgba(0,255,0,0.1)_1px,transparent_1px)] bg-[size:20px_20px]"></div>
                            
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div class="bg-black/50 backdrop-blur px-4 py-2 rounded-lg border border-red-500/50 text-red-400 text-xs font-mono">
                                    LOCATION TRACKING ACTIVE
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-2xl shadow-lg p-6 border-l-4 border-red-500">
                            <h4 class="font-bold text-slate-800 mb-3">Reporting Guidelines</h4>
                            <ul class="text-sm text-slate-600 space-y-3">
                                <li class="flex gap-2"><span class="text-red-500">•</span> Provide exact landmarks.</li>
                                <li class="flex gap-2"><span class="text-red-500">•</span> Photos help prioritize response.</li>
                                <li class="flex gap-2"><span class="text-red-500">•</span> False reports are penalized.</li>
                            </ul>
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