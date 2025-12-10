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
        <template #header>
            <div class="flex items-center gap-3">
                <div class="p-2 bg-teal-100 rounded-xl text-teal-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                </div>
                <h2 class="font-bold text-xl text-gray-800 leading-tight">Provincial Health Connect</h2>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <div class="flex gap-4 mb-8">
                    <button @click="activeTab = 'book'" :class="['px-6 py-3 rounded-xl font-bold text-sm transition-all', activeTab === 'book' ? 'bg-teal-600 text-white shadow-lg shadow-teal-500/30' : 'bg-white text-slate-500 hover:bg-teal-50']">
                        📅 Book Appointment
                    </button>
                    <button @click="activeTab = 'records'" :class="['px-6 py-3 rounded-xl font-bold text-sm transition-all', activeTab === 'records' ? 'bg-teal-600 text-white shadow-lg shadow-teal-500/30' : 'bg-white text-slate-500 hover:bg-teal-50']">
                        📂 Medical Records
                    </button>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    
                    <div v-if="activeTab === 'book'" class="lg:col-span-2 bg-white rounded-3xl shadow-xl p-8 border border-slate-100">
                        <h3 class="text-xl font-bold text-slate-800 mb-6">New Appointment Request</h3>
                        
                        <form @submit.prevent="submit" class="space-y-6">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-400 uppercase mb-2">Service Type</label>
                                    <select v-model="form.type" class="w-full border-slate-200 rounded-xl px-4 py-3 font-semibold text-slate-700 focus:ring-teal-500">
                                        <option>General Consultation</option>
                                        <option>Pediatrics (Child)</option>
                                        <option>Dental Checkup</option>
                                        <option>Vaccination</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-400 uppercase mb-2">Preferred Date</label>
                                    <input type="date" v-model="form.date" class="w-full border-slate-200 rounded-xl px-4 py-3 font-semibold text-slate-700 focus:ring-teal-500">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-400 uppercase mb-2">Primary Symptoms / Reason</label>
                                <textarea v-model="form.symptoms" rows="4" class="w-full border-slate-200 rounded-xl px-4 py-3 focus:ring-teal-500" placeholder="Describe what you are feeling..."></textarea>
                            </div>

                            <button class="w-full bg-teal-600 hover:bg-teal-500 text-white font-bold py-4 rounded-xl shadow-lg transition-all flex items-center justify-center gap-2">
                                <span>Confirm Booking</span>
                            </button>
                        </form>
                    </div>

                    <div v-if="activeTab === 'records'" class="lg:col-span-2 space-y-4">
                        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex justify-between items-center">
                            <div>
                                <h4 class="font-bold text-slate-800">COVID-19 Booster Shot</h4>
                                <p class="text-xs text-slate-500">Administered at Pili Sports Complex</p>
                            </div>
                            <span class="px-3 py-1 bg-green-100 text-green-700 rounded-lg text-xs font-bold">Vaccinated</span>
                        </div>
                        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex justify-between items-center">
                            <div>
                                <h4 class="font-bold text-slate-800">Annual Physical Exam</h4>
                                <p class="text-xs text-slate-500">Dr. Santos - Rural Health Unit 1</p>
                            </div>
                            <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-lg text-xs font-bold">Normal</span>
                        </div>
                    </div>

                    <div class="bg-slate-900 rounded-3xl p-6 text-white shadow-2xl relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-teal-500 rounded-full blur-[60px] opacity-20"></div>
                        
                        <h4 class="font-bold text-sm uppercase tracking-widest mb-6 border-b border-white/10 pb-4">
                            Live Queue Status
                        </h4>

                        <div class="space-y-6 relative z-10">
                            <div class="flex items-center gap-4">
                                <div class="relative">
                                    <div class="w-12 h-12 bg-white/10 rounded-full flex items-center justify-center text-xl">👨‍⚕️</div>
                                    <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-slate-900 rounded-full"></div>
                                </div>
                                <div>
                                    <p class="font-bold text-sm">Dr. A. Cruz (General)</p>
                                    <p class="text-xs text-teal-300">Serving Patient #42</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-4">
                                <div class="relative">
                                    <div class="w-12 h-12 bg-white/10 rounded-full flex items-center justify-center text-xl">👩‍⚕️</div>
                                    <div class="absolute bottom-0 right-0 w-3 h-3 bg-yellow-500 border-2 border-slate-900 rounded-full"></div>
                                </div>
                                <div>
                                    <p class="font-bold text-sm">Dr. B. Reyes (Dental)</p>
                                    <p class="text-xs text-yellow-300">On Break (Back 1:00 PM)</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 bg-white/10 rounded-xl p-4 text-center">
                            <p class="text-xs text-slate-400 uppercase">Estimated Wait Time</p>
                            <p class="text-2xl font-bold text-white">15 Mins</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>