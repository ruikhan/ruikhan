<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const emit = defineEmits(['close']);

// State Management
const step = ref(1); // 1: Triage, 2: Details, 3: Success
const selection = ref(null); // 'civil' (Barangay) or 'public' (Municipal)

// The Form Data
const form = useForm({
    type: '', // 'civil' or 'public'
    // Public/Municipal Fields
    department: '',
    location: '',
    image: null,
    // Civil/Barangay Fields
    respondent_name: '',
    nature_of_dispute: '',
    // Common
    description: '',
});

// Triage Selection Handler
const selectType = (type) => {
    selection.value = type;
    form.type = type;
    step.value = 2;
};

// Submission Handler
const submitIssue = () => {
    form.post(route('issues.store'), {
        preserveScroll: true,
        onSuccess: () => {
            step.value = 3;
            setTimeout(() => emit('close'), 2000);
        },
    });
};
</script>

<template>
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        
        <div @click="$emit('close')" class="absolute inset-0 bg-black/80 backdrop-blur-sm transition-opacity animate-fade-in"></div>

        <div class="relative w-full max-w-2xl bg-[#0f1115] border border-white/10 rounded-3xl shadow-2xl overflow-hidden transform transition-all animate-scale-up flex flex-col max-h-[90vh]">
            
            <div class="px-6 py-5 border-b border-white/10 flex justify-between items-center bg-white/5">
                <div>
                    <h2 class="text-lg font-bold text-white tracking-tight">
                        <span v-if="step === 1">Select Issue Type</span>
                        <span v-if="step === 2 && selection === 'civil'">File Barangay Case</span>
                        <span v-if="step === 2 && selection === 'public'">Report Public Concern</span>
                        <span v-if="step === 3">Submission Complete</span>
                    </h2>
                    <p v-if="step === 1" class="text-xs text-slate-400 mt-0.5">Choose the category that best describes your concern.</p>
                </div>
                <button @click="$emit('close')" class="w-8 h-8 rounded-full bg-white/5 flex items-center justify-center text-slate-400 hover:bg-white/10 hover:text-white transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="p-6 md:p-8 overflow-y-auto custom-scrollbar">
                
                <div v-if="step === 1" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <button @click="selectType('civil')" class="group relative p-6 rounded-2xl border border-white/10 bg-white/5 hover:bg-blue-600/10 hover:border-blue-500/50 transition-all text-left">
                        <div class="w-12 h-12 rounded-xl bg-blue-500/20 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                            <span class="text-2xl">⚖️</span>
                        </div>
                        <h3 class="font-bold text-white mb-1 group-hover:text-blue-400 transition-colors">Personal Dispute</h3>
                        <p class="text-xs text-slate-400 leading-relaxed">Conflict with a neighbor, debt collection, slander, or property boundary issues.</p>
                        <span class="absolute top-4 right-4 text-[10px] font-bold uppercase tracking-wider text-blue-400 bg-blue-500/10 px-2 py-1 rounded-lg border border-blue-500/20">Barangay</span>
                    </button>

                    <button @click="selectType('public')" class="group relative p-6 rounded-2xl border border-white/10 bg-white/5 hover:bg-emerald-600/10 hover:border-emerald-500/50 transition-all text-left">
                        <div class="w-12 h-12 rounded-xl bg-emerald-500/20 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                            <span class="text-2xl">🚧</span>
                        </div>
                        <h3 class="font-bold text-white mb-1 group-hover:text-emerald-400 transition-colors">Public Concern</h3>
                        <p class="text-xs text-slate-400 leading-relaxed">Potholes, uncollected trash, broken streetlights, or community hazards.</p>
                        <span class="absolute top-4 right-4 text-[10px] font-bold uppercase tracking-wider text-emerald-400 bg-emerald-500/10 px-2 py-1 rounded-lg border border-emerald-500/20">Municipal</span>
                    </button>
                </div>

                <div v-if="step === 2 && selection === 'civil'" class="space-y-5 animate-fade-in">
                    <div class="bg-yellow-500/10 border border-yellow-500/20 rounded-xl p-4 flex gap-3">
                        <span class="text-yellow-500 text-xl">ℹ️</span>
                        <p class="text-xs text-yellow-200/80 leading-relaxed">
                            <strong class="text-yellow-400 block mb-1">Lupon Tagapamayapa Notice</strong>
                            This will formally file a complaint for mediation. False accusations are punishable by law.
                        </p>
                    </div>
                    
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Respondent Name</label>
                        <input v-model="form.respondent_name" type="text" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:ring-2 focus:ring-blue-500/50 focus:border-transparent transition-all placeholder-slate-600" placeholder="Who are you complaining against?">
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Nature of Dispute</label>
                        <div class="relative">
                            <select v-model="form.nature_of_dispute" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm appearance-none focus:ring-2 focus:ring-blue-500/50 focus:border-transparent transition-all cursor-pointer hover:bg-white/10">
                                <option class="bg-slate-900">Collection of Debt</option>
                                <option class="bg-slate-900">Property Boundary</option>
                                <option class="bg-slate-900">Slander / Gossip</option>
                                <option class="bg-slate-900">Physical Altercation</option>
                                <option class="bg-slate-900">Other</option>
                            </select>
                            <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-slate-500"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg></div>
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Narrative</label>
                        <textarea v-model="form.description" rows="4" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:ring-2 focus:ring-blue-500/50 focus:border-transparent transition-all resize-none placeholder-slate-600" placeholder="State the facts clearly..."></textarea>
                    </div>
                </div>

                <div v-if="step === 2 && selection === 'public'" class="space-y-5 animate-fade-in">
                    <div class="bg-emerald-500/10 border border-emerald-500/20 rounded-xl p-4 flex gap-3">
                        <span class="text-emerald-500 text-xl">🎫</span>
                        <p class="text-xs text-emerald-200/80 leading-relaxed">
                            <strong class="text-emerald-400 block mb-1">Ticket Generation</strong>
                            This report will create a dispatch ticket for the relevant municipal department.
                        </p>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Department</label>
                        <div class="relative">
                            <select v-model="form.department" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm appearance-none focus:ring-2 focus:ring-emerald-500/50 focus:border-transparent transition-all cursor-pointer hover:bg-white/10">
                                <option class="bg-slate-900" value="engineering">Engineering (Roads, Lights)</option>
                                <option class="bg-slate-900" value="menro">MENRO (Trash, Trees)</option>
                                <option class="bg-slate-900" value="health">Health Office (Sanitation)</option>
                                <option class="bg-slate-900" value="agriculture">Agriculture (Stray Animals)</option>
                            </select>
                            <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-slate-500"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg></div>
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Exact Location</label>
                        <div class="relative group">
                            <span class="absolute left-3 top-3.5 text-slate-500 group-focus-within:text-emerald-400 transition-colors">📍</span>
                            <input v-model="form.location" type="text" class="w-full bg-white/5 border border-white/10 rounded-xl pl-10 pr-4 py-3 text-white text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-transparent transition-all placeholder-slate-600" placeholder="e.g. Corner of Rizal St.">
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Description</label>
                        <textarea v-model="form.description" rows="3" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-transparent transition-all resize-none placeholder-slate-600" placeholder="Describe the concern..."></textarea>
                    </div>
                </div>

                <div v-if="step === 3" class="flex flex-col items-center justify-center py-8 text-center animate-fade-in">
                    <div class="w-20 h-20 bg-green-500/20 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2">Report Submitted</h3>
                    <p class="text-slate-400 text-sm">Your ticket has been generated. Redirecting...</p>
                </div>

            </div>

            <div v-if="step === 2" class="p-6 border-t border-white/10 bg-white/5 flex justify-end gap-3">
                <button @click="step = 1" type="button" class="px-5 py-2.5 rounded-xl border border-white/10 text-slate-300 text-xs font-bold uppercase tracking-widest hover:bg-white/5 hover:text-white transition-all">
                    Back
                </button>
                <button @click="submitIssue" :disabled="form.processing" class="px-6 py-2.5 rounded-xl bg-blue-600 text-white text-xs font-bold uppercase tracking-widest hover:bg-blue-500 shadow-lg shadow-blue-500/20 transition-all flex items-center gap-2 disabled:opacity-50">
                    <span v-if="form.processing" class="animate-spin h-3 w-3 border-2 border-white/30 border-t-white rounded-full"></span>
                    <span>Submit Report</span>
                </button>
            </div>

        </div>
    </div>
</template>

<style scoped>
/* Animations */
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
.animate-fade-in { animation: fadeIn 0.3s ease-out; }

@keyframes scaleUp { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
.animate-scale-up { animation: scaleUp 0.3s cubic-bezier(0.16, 1, 0.3, 1); }

/* Scrollbar */
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }
</style>