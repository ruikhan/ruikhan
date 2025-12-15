<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

// --- FORM LOGIC ---
const form = useForm({
    type: '',
    category: 'personal',
    purpose: '',
    civil_status: 'Single',
    years_of_residency: '',
    contact_number: '',
    business_name: '',
    tin_number: '',
    valid_id: null,
});

// --- FILE PREVIEW LOGIC ---
const idPreview = ref(null);

const handleFileUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.valid_id = file;
        idPreview.value = URL.createObjectURL(file);
    }
};

// --- SUBMIT LOGIC ---
const submit = () => {
    form.post(route('documents.store'), {
        forceFormData: true,
        onSuccess: () => {
            form.reset();
            idPreview.value = null;
        },
        preserveScroll: true,
    });
};

const page = usePage();
const flashSuccess = computed(() => page.props.flash?.success);
</script>

<template>
    <Head title="Request Document" />

    <AuthenticatedLayout>
        <div class="fixed inset-0 z-0 pointer-events-none">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_var(--tw-gradient-stops))] from-[#0f172a] via-[#050505] to-black"></div>
            <div class="absolute inset-0 opacity-[0.03] bg-[url('https://grainy-gradients.vercel.app/noise.svg')] brightness-100 contrast-150"></div>
            <div class="absolute top-0 left-0 w-[60vw] h-[60vw] bg-blue-600/5 rounded-full blur-[120px]"></div>
        </div>

        <div class="relative z-10 py-8 md:py-12 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-8 gap-4">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/10 border border-blue-500/20 backdrop-blur-md mb-2">
                        <span class="w-1.5 h-1.5 bg-blue-400 rounded-full animate-pulse"></span>
                        <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-blue-300">Official Portal</span>
                    </div>
                    <h2 class="font-black text-3xl md:text-4xl text-white tracking-tight">New Request</h2>
                    <p class="text-slate-400 text-sm mt-1">Submit your application for barangay and municipal documents.</p>
                </div>

                <Link :href="route('dashboard')" 
                      class="flex items-center gap-2 px-5 py-2.5 bg-white/5 border border-white/10 rounded-full text-slate-300 text-xs font-bold uppercase tracking-widest hover:bg-white/10 hover:text-white transition-all group">
                    <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Cancel
                </Link>
            </div>

            <div v-if="flashSuccess" class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl text-emerald-400 flex items-center gap-3 animate-fade-in-up">
                <div class="p-2 bg-emerald-500/20 rounded-full"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
                <span class="font-bold text-sm">{{ flashSuccess }}</span>
            </div>

            <div class="bg-[#0f1115]/60 backdrop-blur-xl border border-white/10 rounded-[2.5rem] shadow-2xl overflow-hidden">
                
                <form @submit.prevent="submit" class="p-6 md:p-10 grid grid-cols-1 md:grid-cols-12 gap-8 md:gap-12">
                    
                    <div class="md:col-span-7 space-y-6">
                        
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Document Type</label>
                            <div class="relative group">
                                <select v-model="form.type" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white appearance-none focus:ring-2 focus:ring-blue-500/50 focus:border-transparent transition-all cursor-pointer hover:bg-white/10">
                                    <option value="" disabled class="text-gray-500">Select Document...</option>
                                    <option value="Barangay Clearance" class="bg-slate-900">Barangay Clearance</option>
                                    <option value="Certificate of Indigency" class="bg-slate-900">Certificate of Indigency</option>
                                    <option value="Business Permit" class="bg-slate-900">Business Permit</option>
                                    <option value="Residency Certificate" class="bg-slate-900">Residency Certificate</option>
                                </select>
                                <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-slate-500 group-hover:text-white transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>
                            <p v-if="form.errors.type" class="text-red-400 text-xs ml-1">{{ form.errors.type }}</p>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Category</label>
                            <div class="grid grid-cols-2 gap-4">
                                <label class="cursor-pointer group">
                                    <input type="radio" value="personal" v-model="form.category" class="peer sr-only">
                                    <div class="flex items-center justify-center gap-2 py-3 rounded-xl border border-white/10 bg-white/5 peer-checked:bg-blue-600 peer-checked:border-blue-500 transition-all hover:bg-white/10 text-slate-400 peer-checked:text-white peer-checked:shadow-lg">
                                        <span class="text-sm font-bold">Personal</span>
                                    </div>
                                </label>
                                <label class="cursor-pointer group">
                                    <input type="radio" value="business" v-model="form.category" class="peer sr-only">
                                    <div class="flex items-center justify-center gap-2 py-3 rounded-xl border border-white/10 bg-white/5 peer-checked:bg-purple-600 peer-checked:border-purple-500 transition-all hover:bg-white/10 text-slate-400 peer-checked:text-white peer-checked:shadow-lg">
                                        <span class="text-sm font-bold">Business</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div v-if="form.category === 'business'" class="animate-slide-down p-5 bg-purple-500/10 rounded-2xl border border-purple-500/20 space-y-4">
                            <div class="space-y-1.5">
                                <label class="text-[10px] font-bold text-purple-300 uppercase tracking-widest ml-1">Business Name</label>
                                <input type="text" v-model="form.business_name" class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-2.5 text-white text-sm focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition-all" placeholder="Registered Name">
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-[10px] font-bold text-purple-300 uppercase tracking-widest ml-1">TIN Number</label>
                                <input type="text" v-model="form.tin_number" class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-2.5 text-white text-sm focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition-all" placeholder="000-000-000">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Civil Status</label>
                                <select v-model="form.civil_status" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm appearance-none focus:ring-2 focus:ring-blue-500/50 focus:border-transparent transition-all hover:bg-white/10">
                                    <option class="bg-slate-900">Single</option>
                                    <option class="bg-slate-900">Married</option>
                                    <option class="bg-slate-900">Widowed</option>
                                </select>
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Years Residency</label>
                                <input type="number" min="0" v-model="form.years_of_residency" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:ring-2 focus:ring-blue-500/50 focus:border-transparent transition-all hover:bg-white/10" placeholder="e.g. 5">
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Contact Number</label>
                            <div class="relative group">
                                <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-500 group-focus-within:text-blue-400 transition-colors">📞</span>
                                <input type="tel" v-model="form.contact_number" class="w-full bg-white/5 border border-white/10 rounded-xl py-3 pl-10 pr-4 text-white text-sm focus:ring-2 focus:ring-blue-500/50 focus:border-transparent transition-all hover:bg-white/10" placeholder="0912 345 6789">
                            </div>
                        </div>
                    </div>

                    <div class="md:col-span-5 flex flex-col h-full space-y-6">
                        
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Purpose</label>
                            <textarea v-model="form.purpose" rows="4" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:ring-2 focus:ring-blue-500/50 focus:border-transparent transition-all resize-none hover:bg-white/10" placeholder="Briefly describe why you need this document..."></textarea>
                        </div>

                        <div class="space-y-1.5 flex-grow flex flex-col">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Valid ID Verification</label>
                            
                            <div class="relative flex-grow min-h-[220px] border-2 border-dashed border-white/10 rounded-2xl bg-white/5 hover:bg-white/10 hover:border-blue-500/50 transition-all group overflow-hidden cursor-pointer">
                                <input type="file" @change="handleFileUpload" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20" accept="image/*">
                                
                                <div v-if="!idPreview" class="absolute inset-0 flex flex-col items-center justify-center text-center p-6 transition-opacity duration-300">
                                    <div class="w-14 h-14 rounded-full bg-blue-500/10 flex items-center justify-center mb-3 group-hover:scale-110 group-hover:bg-blue-500/20 transition-all">
                                        <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    </div>
                                    <p class="text-white font-bold text-sm">Click or Drop ID</p>
                                    <p class="text-slate-500 text-xs mt-1">PNG, JPG (Max 2MB)</p>
                                </div>

                                <img v-if="idPreview" :src="idPreview" class="absolute inset-0 w-full h-full object-cover opacity-90 group-hover:opacity-100 transition-opacity z-10 animate-fade-in">
                                
                                <div v-if="idPreview" class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity z-20 flex items-center justify-center text-white text-xs font-bold tracking-widest uppercase">Change Image</div>
                            </div>
                            <p v-if="form.errors.valid_id" class="text-red-400 text-xs ml-1">{{ form.errors.valid_id }}</p>
                        </div>

                        <button type="submit" :disabled="form.processing" class="w-full py-4 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl text-white font-bold uppercase tracking-widest text-xs hover:shadow-[0_0_25px_rgba(37,99,235,0.4)] hover:scale-[1.02] active:scale-[0.98] transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                            <span v-if="form.processing" class="animate-spin h-4 w-4 border-2 border-white/30 border-t-white rounded-full"></span>
                            <span v-else>Submit Application</span>
                        </button>

                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
@keyframes slideDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
.animate-slide-down { animation: slideDown 0.3s ease-out forwards; }
.animate-fade-in { animation: fadeIn 0.5s ease-out forwards; }
.animate-fade-in-up { animation: slideDown 0.5s ease-out forwards; }
</style>