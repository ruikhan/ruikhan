<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({ programs: Array });

// Simple Eligibility Logic
const showWizard = ref(false);
const step = ref(1);
const answers = ref({});
const result = ref(null);

const checkEligibility = () => {
    // Mock Logic
    if (answers.value.income === 'below' && answers.value.resident === 'yes') {
        result.value = 'eligible';
    } else {
        result.value = 'not_eligible';
    }
    step.value = 3;
};
</script>

<template>
    <Head title="Social Care" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <div class="p-2 bg-rose-100 rounded-xl text-rose-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h2 class="font-bold text-xl text-gray-800 leading-tight">Social Care Portal</h2>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <div class="bg-gradient-to-r from-rose-500 to-pink-600 rounded-3xl p-8 text-white shadow-2xl mb-12 relative overflow-hidden">
                    <div class="absolute right-0 top-0 w-64 h-64 bg-white opacity-10 rounded-full blur-[80px] -mr-10 -mt-10"></div>
                    
                    <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-8">
                        <div>
                            <h3 class="text-2xl font-bold mb-2">Not sure what you qualify for?</h3>
                            <p class="text-rose-100 max-w-lg">Use our AI-powered eligibility checker to find scholarships, financial aid, and medical assistance programs suited for you.</p>
                        </div>
                        <button @click="showWizard = !showWizard" class="bg-white text-rose-600 px-8 py-3 rounded-xl font-bold shadow-lg hover:scale-105 transition-transform">
                            {{ showWizard ? 'Close Checker' : 'Check Eligibility' }}
                        </button>
                    </div>

                    <transition name="fade">
                        <div v-if="showWizard" class="mt-8 bg-white text-slate-800 rounded-2xl p-6 shadow-xl max-w-2xl mx-auto">
                            
                            <div v-if="step === 1" class="text-center space-y-4">
                                <h4 class="font-bold text-lg">Are you a registered resident of this province?</h4>
                                <div class="flex justify-center gap-4">
                                    <button @click="answers.resident = 'yes'; step++" class="px-6 py-2 bg-slate-100 hover:bg-rose-100 hover:text-rose-700 rounded-lg font-bold transition">Yes</button>
                                    <button @click="answers.resident = 'no'; checkEligibility()" class="px-6 py-2 bg-slate-100 hover:bg-slate-200 rounded-lg font-bold transition">No</button>
                                </div>
                            </div>

                            <div v-if="step === 2" class="text-center space-y-4">
                                <h4 class="font-bold text-lg">Is your monthly household income below ₱20,000?</h4>
                                <div class="flex justify-center gap-4">
                                    <button @click="answers.income = 'below'; checkEligibility()" class="px-6 py-2 bg-slate-100 hover:bg-rose-100 hover:text-rose-700 rounded-lg font-bold transition">Yes</button>
                                    <button @click="answers.income = 'above'; checkEligibility()" class="px-6 py-2 bg-slate-100 hover:bg-slate-200 rounded-lg font-bold transition">No</button>
                                </div>
                            </div>

                            <div v-if="step === 3" class="text-center">
                                <div v-if="result === 'eligible'">
                                    <div class="text-4xl mb-2">🎉</div>
                                    <h4 class="font-bold text-xl text-green-600">You are eligible!</h4>
                                    <p class="text-sm text-slate-500 mb-4">You qualify for the Indigent Financial Aid and Academic Scholarship.</p>
                                    <button @click="showWizard = false" class="text-rose-600 font-bold underline">View Programs Below</button>
                                </div>
                                <div v-else>
                                    <div class="text-4xl mb-2">🤔</div>
                                    <h4 class="font-bold text-xl text-slate-700">Limited Eligibility</h4>
                                    <p class="text-sm text-slate-500">Based on your answers, you may only qualify for general public services.</p>
                                </div>
                            </div>

                        </div>
                    </transition>
                </div>

                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    <div v-for="prog in programs" :key="prog.id" class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 hover:shadow-md transition-shadow relative overflow-hidden">
                        
                        <div class="absolute top-4 right-4">
                            <span v-if="prog.status === 'Open'" class="px-2 py-1 bg-green-100 text-green-700 text-[10px] font-bold uppercase rounded">Open</span>
                            <span v-else class="px-2 py-1 bg-slate-100 text-slate-500 text-[10px] font-bold uppercase rounded">Closed</span>
                        </div>

                        <div class="mb-4">
                            <span class="text-xs font-bold text-rose-500 uppercase tracking-wide">{{ prog.type }}</span>
                            <h3 class="text-lg font-bold text-slate-800 mt-1">{{ prog.title }}</h3>
                        </div>

                        <div class="p-3 bg-slate-50 rounded-xl mb-4">
                            <p class="text-xs text-slate-500 uppercase font-bold">Benefit Amount</p>
                            <p class="text-lg font-bold text-slate-900">{{ prog.amount }}</p>
                        </div>

                        <button :disabled="prog.status !== 'Open'" class="w-full py-2 rounded-lg font-bold text-sm transition-colors" :class="prog.status === 'Open' ? 'bg-slate-900 text-white hover:bg-rose-600' : 'bg-slate-100 text-slate-400 cursor-not-allowed'">
                            {{ prog.status === 'Open' ? 'Apply Now' : 'Application Closed' }}
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.5s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>