<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({ programs: Array });

// --- MOCK DATA FOR DEMO (If props are empty) ---
const demoPrograms = props.programs?.length ? props.programs : [
    { id: 1, type: 'Financial Aid', title: 'Indigent Support Fund', amount: '₱5,000', status: 'Open', description: 'One-time cash assistance for qualified residents.' },
    { id: 2, type: 'Education', title: 'City Scholarship', amount: '₱10,000', status: 'Closed', description: 'Tuition subsidy for college students.' },
    { id: 3, type: 'Medical', title: 'Senior Care Plus', amount: 'Free Meds', status: 'Open', description: 'Monthly maintenance medicine maintenance.' },
];

// Eligibility Logic
const showWizard = ref(false);
const step = ref(1);
const answers = ref({});
const result = ref(null);

const checkEligibility = () => {
    if (answers.value.income === 'below' && answers.value.resident === 'yes') {
        result.value = 'eligible';
    } else {
        result.value = 'not_eligible';
    }
    step.value = 3;
};

const resetWizard = () => {
    step.value = 1;
    answers.value = {};
    result.value = null;
    showWizard.value = false;
};
</script>

<template>
    <Head title="Social Care" />

    <AuthenticatedLayout>
        <div class="fixed inset-0 z-0 pointer-events-none">
            <div class="absolute inset-0 bg-[#000000]"></div>
            <div class="absolute top-[-20%] left-[-10%] w-[80vw] h-[80vw] bg-rose-600/20 rounded-full blur-[180px] opacity-60 mix-blend-screen animate-float"></div>
            <div class="absolute bottom-[-20%] right-[-10%] w-[80vw] h-[80vw] bg-blue-600/20 rounded-full blur-[180px] opacity-60 mix-blend-screen animate-float-delayed"></div>
            <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-[0.04] brightness-150"></div>
        </div>

        <div class="relative z-10 py-12 max-w-[1400px] mx-auto px-6 lg:px-12 font-sans selection:bg-rose-500/30 selection:text-rose-200">
            
            <div class="flex items-end justify-between mb-10 px-2">
                <div>
                    <h2 class="text-4xl font-semibold text-white tracking-tight">Social Care</h2>
                    <p class="text-white/50 text-lg mt-1 font-medium">Assistance & Welfare Programs</p>
                </div>
                <div class="flex gap-3">
                    <button class="w-10 h-10 rounded-full bg-[#1c1c1e]/50 backdrop-blur-xl border border-white/10 flex items-center justify-center text-white/70 hover:bg-white/20 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </button>
                    <div class="h-10 px-4 rounded-full bg-[#1c1c1e]/50 backdrop-blur-xl border border-white/10 flex items-center justify-center text-white/70 text-sm font-medium">
                        {{ new Date().toLocaleDateString('en-US', { month: 'short', day: 'numeric' }) }}
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                <div class="lg:col-span-8 relative group">
                    <div class="absolute inset-0 bg-gradient-to-br from-rose-500 to-orange-600 rounded-[2.5rem] blur-xl opacity-20 group-hover:opacity-30 transition-opacity duration-700"></div>
                    
                    <div class="relative h-full bg-[#1c1c1e]/60 backdrop-blur-2xl border border-white/10 rounded-[2.5rem] p-10 overflow-hidden flex flex-col justify-between">
                        
                        <transition name="fade" mode="out-in">
                            
                            <div v-if="!showWizard" class="h-full flex flex-col justify-between relative z-10">
                                <div class="max-w-xl">
                                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 border border-white/10 text-white/80 text-xs font-bold uppercase tracking-wider mb-6">
                                        <span class="w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span>
                                        AI Eligibility Checker
                                    </div>
                                    <h3 class="text-4xl md:text-5xl font-bold text-white leading-tight mb-4">Find programs tailored for you.</h3>
                                    <p class="text-white/60 text-lg leading-relaxed">Answer 3 simple questions to unlock financial aid, scholarships, and medical support instantly.</p>
                                </div>
                                <div class="mt-8">
                                    <button @click="showWizard = true" class="group relative inline-flex items-center gap-3 px-8 py-4 bg-white text-black rounded-full font-bold text-base transition-transform active:scale-95">
                                        <span>Start Check</span>
                                        <div class="w-6 h-6 rounded-full bg-black text-white flex items-center justify-center group-hover:translate-x-1 transition-transform">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path></svg>
                                        </div>
                                    </button>
                                </div>
                                <div class="absolute right-[-50px] bottom-[-50px] w-64 h-64 bg-gradient-to-br from-rose-400 to-orange-400 rounded-full blur-[60px] opacity-40 mix-blend-overlay pointer-events-none"></div>
                            </div>

                            <div v-else class="h-full flex flex-col relative z-10">
                                <div class="flex justify-between items-start mb-8">
                                    <h3 class="text-2xl font-bold text-white">Eligibility Check</h3>
                                    <button @click="resetWizard" class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center text-white/50 hover:bg-white/20 hover:text-white transition-all">✕</button>
                                </div>

                                <div class="flex gap-2 mb-8">
                                    <div class="h-1.5 flex-1 rounded-full transition-all duration-500" :class="step >= 1 ? 'bg-rose-500' : 'bg-white/10'"></div>
                                    <div class="h-1.5 flex-1 rounded-full transition-all duration-500" :class="step >= 2 ? 'bg-rose-500' : 'bg-white/10'"></div>
                                    <div class="h-1.5 flex-1 rounded-full transition-all duration-500" :class="step >= 3 ? 'bg-rose-500' : 'bg-white/10'"></div>
                                </div>

                                <div class="flex-1 flex flex-col justify-center items-center text-center">
                                    
                                    <div v-if="step === 1" class="animate-slide-up space-y-8 w-full max-w-md">
                                        <h4 class="text-3xl font-bold text-white">Are you a registered resident?</h4>
                                        <div class="grid grid-cols-2 gap-4">
                                            <button @click="answers.resident = 'yes'; step++" class="h-16 rounded-2xl bg-white/5 hover:bg-white text-white hover:text-black font-bold text-lg border border-white/10 transition-all duration-300">Yes</button>
                                            <button @click="answers.resident = 'no'; checkEligibility()" class="h-16 rounded-2xl bg-white/5 hover:bg-white/10 text-white/60 font-bold text-lg border border-white/10 transition-all duration-300">No</button>
                                        </div>
                                    </div>

                                    <div v-if="step === 2" class="animate-slide-up space-y-8 w-full max-w-md">
                                        <h4 class="text-3xl font-bold text-white">Is monthly income below ₱20k?</h4>
                                        <div class="grid grid-cols-2 gap-4">
                                            <button @click="answers.income = 'below'; checkEligibility()" class="h-16 rounded-2xl bg-white/5 hover:bg-white text-white hover:text-black font-bold text-lg border border-white/10 transition-all duration-300">Yes</button>
                                            <button @click="answers.income = 'above'; checkEligibility()" class="h-16 rounded-2xl bg-white/5 hover:bg-white/10 text-white/60 font-bold text-lg border border-white/10 transition-all duration-300">No</button>
                                        </div>
                                    </div>

                                    <div v-if="step === 3" class="animate-slide-up text-center space-y-6">
                                        <div class="w-20 h-20 rounded-full flex items-center justify-center text-4xl mx-auto mb-2 shadow-2xl" 
                                             :class="result === 'eligible' ? 'bg-emerald-500 text-white shadow-emerald-500/40' : 'bg-orange-500 text-white shadow-orange-500/40'">
                                            {{ result === 'eligible' ? '✓' : '!' }}
                                        </div>
                                        <div>
                                            <h4 class="text-3xl font-bold text-white mb-2">{{ result === 'eligible' ? 'You Qualified!' : 'Limited Access' }}</h4>
                                            <p class="text-white/60 text-lg max-w-sm mx-auto">
                                                {{ result === 'eligible' ? 'You are eligible for Financial Aid and Scholarship grants.' : 'Based on your inputs, you may qualify for general public services only.' }}
                                            </p>
                                        </div>
                                        <button @click="resetWizard" class="text-rose-400 font-bold hover:text-rose-300 transition-colors">Start Over ↺</button>
                                    </div>

                                </div>
                            </div>
                        </transition>
                    </div>
                </div>

                <div class="lg:col-span-4 flex flex-col gap-6">
                    
                    <!-- <div class="flex-1 bg-[#1c1c1e]/60 backdrop-blur-2xl border border-white/10 rounded-[2.5rem] p-8 flex flex-col justify-center relative overflow-hidden group">
                        <div class="absolute inset-0 bg-gradient-to-br from-blue-500/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                        <div class="relative z-10">
                            <div class="text-white/50 text-sm font-bold uppercase tracking-widest mb-1">Total Beneficiaries</div>
                            <div class="text-5xl font-semibold text-white tracking-tight">12,450</div>
                            <div class="mt-4 flex items-center gap-2 text-emerald-400 text-sm font-bold bg-emerald-500/10 w-fit px-3 py-1 rounded-full">
                                <span>▲ 12%</span>
                                <span>this month</span>
                            </div>
                        </div>
                    </div> -->

                    <!-- <div class="flex-1 bg-[#1c1c1e]/60 backdrop-blur-2xl border border-white/10 rounded-[2.5rem] p-8 flex flex-col justify-center relative overflow-hidden group">
                        <div class="absolute inset-0 bg-gradient-to-br from-purple-500/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                        <div class="relative z-10">
                            <div class="text-white/50 text-sm font-bold uppercase tracking-widest mb-1">Budget Disbursed</div>
                            <div class="text-5xl font-semibold text-white tracking-tight">₱8.2M</div>
                            <div class="mt-4 w-full bg-white/10 h-2 rounded-full overflow-hidden">
                                <div class="bg-purple-500 h-full w-[75%] rounded-full shadow-[0_0_10px_#a855f7]"></div>
                            </div>
                            <div class="text-right text-xs text-white/40 mt-2 font-mono">Q3 ALLOCATION</div>
                        </div>
                    </div> -->

                </div>

                <div class="lg:col-span-12 mt-4">
                    <h3 class="text-2xl font-semibold text-white mb-6 px-2">Available Programs</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div v-for="prog in demoPrograms" :key="prog.id" 
                             class="group relative bg-[#1c1c1e]/60 backdrop-blur-2xl border border-white/10 rounded-[2rem] p-6 hover:bg-[#2c2c2e]/80 transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl hover:shadow-black/50 cursor-pointer overflow-hidden">
                            
                            <div class="absolute top-6 right-6">
                                <div :class="[
                                    'px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider backdrop-blur-md border shadow-lg',
                                    prog.status === 'Open' 
                                        ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30 shadow-emerald-900/20' 
                                        : 'bg-rose-500/20 text-rose-300 border-rose-500/30'
                                ]">
                                    {{ prog.status }}
                                </div>
                            </div>

                            <div class="w-14 h-14 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-2xl mb-6 shadow-inner group-hover:scale-110 transition-transform duration-300">
                                {{ prog.type === 'Financial Aid' ? '💰' : prog.type === 'Medical' ? '⚕️' : '🎓' }}
                            </div>

                            <div class="mb-8">
                                <p class="text-white/40 text-xs font-bold uppercase tracking-widest mb-1">{{ prog.type }}</p>
                                <h4 class="text-2xl font-bold text-white mb-2 group-hover:text-rose-400 transition-colors">{{ prog.title }}</h4>
                                <p class="text-white/60 text-sm leading-relaxed line-clamp-2">{{ prog.description }}</p>
                            </div>

                            <div class="flex items-center justify-between border-t border-white/5 pt-4">
                                <div>
                                    <p class="text-[10px] text-white/30 uppercase font-bold tracking-wider">Benefit</p>
                                    <p class="text-lg font-mono font-medium text-white">{{ prog.amount }}</p>
                                </div>
                                <button class="w-10 h-10 rounded-full bg-white text-black flex items-center justify-center hover:scale-110 transition-transform active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed" :disabled="prog.status !== 'Open'">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </button>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
/* Animations */
@keyframes float { 0% { transform: translateY(0px); } 50% { transform: translateY(-20px); } 100% { transform: translateY(0px); } }
.animate-float { animation: float 10s ease-in-out infinite; }

@keyframes float-delayed { 0% { transform: translateY(0px); } 50% { transform: translateY(20px); } 100% { transform: translateY(0px); } }
.animate-float-delayed { animation: float-delayed 12s ease-in-out infinite; }

@keyframes slide-up { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
.animate-slide-up { animation: slide-up 0.5s cubic-bezier(0.2, 0.8, 0.2, 1); }

/* Fade Transition */
.fade-enter-active, .fade-leave-active { transition: opacity 0.4s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>