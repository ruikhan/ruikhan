<script setup>
import { useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps(['doctors']);
const emit = defineEmits(['close']);

const step = ref(1);
const form = useForm({
    doctor_id: null,
    type: 'Consultation',
    date: '',
    time: '',
    symptoms: '',
});

// Mock Data
const timeSlots = ['09:00 AM', '10:00 AM', '11:30 AM', '02:00 PM', '03:30 PM', '04:00 PM'];
const dates = computed(() => {
    const arr = [];
    for (let i = 1; i <= 5; i++) {
        const d = new Date();
        d.setDate(d.getDate() + i);
        arr.push(d.toISOString().split('T')[0]);
    }
    return arr;
});

const selectedDoctor = computed(() => {
    return props.doctors.find(d => d.id === form.doctor_id);
});

const submitBooking = () => {
    form.post(route('health.store'), {
        onSuccess: () => {
            step.value = 4; // Success Screen
            setTimeout(() => emit('close'), 2500);
        }
    });
};
</script>

<template>
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        
        <div @click="$emit('close')" class="absolute inset-0 bg-black/80 backdrop-blur-sm transition-opacity animate-fade-in"></div>

        <div class="relative w-full max-w-2xl bg-[#0f1115] border border-white/10 rounded-[2rem] shadow-2xl overflow-hidden flex flex-col max-h-[90vh] animate-scale-up">
            
            <div class="px-8 py-6 border-b border-white/10 flex justify-between items-center bg-white/5">
                <div>
                    <h2 class="text-xl font-bold text-white tracking-tight">New Appointment</h2>
                    <div class="flex gap-1 mt-2">
                        <div class="h-1 w-8 rounded-full transition-colors" :class="step >= 1 ? 'bg-teal-500' : 'bg-white/10'"></div>
                        <div class="h-1 w-8 rounded-full transition-colors" :class="step >= 2 ? 'bg-teal-500' : 'bg-white/10'"></div>
                        <div class="h-1 w-8 rounded-full transition-colors" :class="step >= 3 ? 'bg-teal-500' : 'bg-white/10'"></div>
                    </div>
                </div>
                <button @click="$emit('close')" class="w-8 h-8 rounded-full bg-white/5 flex items-center justify-center text-slate-400 hover:bg-white/10 hover:text-white transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="p-8 overflow-y-auto custom-scrollbar">
                
                <div v-if="step === 1" class="space-y-8 animate-fade-in">
                    <div>
                        <h3 class="text-lg font-bold text-white mb-4">Select Specialist</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div v-for="doc in doctors" :key="doc.id" 
                                 @click="form.doctor_id = doc.id"
                                 class="cursor-pointer group relative p-4 rounded-2xl border transition-all"
                                 :class="form.doctor_id === doc.id ? 'bg-teal-500/10 border-teal-500/50 shadow-[0_0_20px_rgba(20,184,166,0.2)]' : 'bg-white/5 border-white/10 hover:border-white/30'">
                                
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 bg-teal-500/20 rounded-xl flex items-center justify-center text-2xl transition-transform group-hover:scale-110">👨‍⚕️</div>
                                    <div>
                                        <div class="font-bold text-white" :class="form.doctor_id === doc.id ? 'text-teal-400' : ''">{{ doc.name }}</div>
                                        <div class="text-xs text-slate-400">{{ doc.specialization }}</div>
                                    </div>
                                    <div v-if="form.doctor_id === doc.id" class="ml-auto text-teal-400">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2 block ml-1">Consultation Type</label>
                        <div class="relative">
                            <select v-model="form.type" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm appearance-none focus:ring-2 focus:ring-teal-500/50 focus:border-transparent transition-all cursor-pointer hover:bg-white/10">
                                <option class="bg-slate-900">General Consultation</option>
                                <option class="bg-slate-900">Annual Check-up</option>
                                <option class="bg-slate-900">Urgent Care</option>
                                <option class="bg-slate-900">Follow-up</option>
                            </select>
                            <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-slate-500"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg></div>
                        </div>
                    </div>
                </div>

                <div v-if="step === 2" class="space-y-8 animate-fade-in">
                    <div>
                        <h3 class="text-lg font-bold text-white mb-4">Select Date</h3>
                        <div class="flex gap-3 overflow-x-auto pb-4 custom-scrollbar">
                            <button v-for="date in dates" :key="date" 
                                    @click="form.date = date"
                                    class="flex flex-col items-center justify-center min-w-[80px] p-3 rounded-xl border transition-all"
                                    :class="form.date === date ? 'bg-teal-500 text-white border-teal-500 shadow-lg' : 'bg-white/5 text-slate-400 border-white/10 hover:bg-white/10 hover:text-white'">
                                <span class="text-xs uppercase font-bold">{{ new Date(date).toLocaleDateString('en-US', { weekday: 'short' }) }}</span>
                                <span class="text-xl font-black">{{ new Date(date).getDate() }}</span>
                            </button>
                        </div>
                    </div>

                    <div v-if="form.date">
                        <h3 class="text-sm font-bold text-slate-300 mb-3 uppercase tracking-wider">Available Slots</h3>
                        <div class="grid grid-cols-3 gap-3">
                            <button v-for="slot in timeSlots" :key="slot"
                                    @click="form.time = slot"
                                    class="py-2.5 rounded-lg text-sm font-medium border transition-all text-center"
                                    :class="form.time === slot ? 'bg-teal-500/20 text-teal-400 border-teal-500/50 shadow-[0_0_15px_rgba(20,184,166,0.2)]' : 'bg-white/5 text-slate-400 border-white/10 hover:bg-white/10 hover:text-white'">
                                {{ slot }}
                            </button>
                        </div>
                    </div>
                </div>

                <div v-if="step === 3" class="space-y-6 animate-fade-in">
                    <div class="bg-yellow-500/10 border border-yellow-500/20 rounded-xl p-4 flex gap-3">
                        <span class="text-yellow-500 text-xl">ℹ️</span>
                        <div class="text-xs text-yellow-200/80 leading-relaxed">
                            <strong class="text-yellow-400 block mb-1">Requirement Check</strong>
                            Please ensure you bring your <strong class="text-white">Government ID</strong> and active <strong class="text-white">PhilHealth Card</strong>.
                        </div>
                    </div>

                    <div>
                        <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2 block ml-1">Symptoms / Reason</label>
                        <textarea v-model="form.symptoms" rows="4" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:ring-2 focus:ring-teal-500/50 focus:border-transparent transition-all resize-none placeholder-slate-600" placeholder="Describe your symptoms..."></textarea>
                    </div>
                </div>

                <div v-if="step === 4" class="flex flex-col items-center justify-center py-12 text-center animate-fade-in">
                    <div class="w-24 h-24 bg-teal-500/20 rounded-full flex items-center justify-center mb-6 shadow-[0_0_30px_rgba(20,184,166,0.2)]">
                        <svg class="w-12 h-12 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-2">Booking Confirmed!</h3>
                    <p class="text-slate-400 text-sm max-w-xs mx-auto leading-relaxed">Your appointment has been scheduled. A confirmation has been sent to your dashboard.</p>
                </div>

            </div>

            <div v-if="step < 4" class="p-6 border-t border-white/10 bg-white/5 flex justify-between items-center">
                <button v-if="step > 1" @click="step--" class="text-slate-400 text-sm font-bold hover:text-white transition-colors px-4 py-2">
                    Back
                </button>
                <div v-else></div>

                <button v-if="step < 3" 
                        @click="step++" 
                        :disabled="(step === 1 && !form.doctor_id) || (step === 2 && (!form.date || !form.time))"
                        class="bg-teal-600 text-white px-8 py-3 rounded-xl font-bold text-sm hover:bg-teal-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all shadow-lg shadow-teal-500/20">
                    Next Step
                </button>
                
                <button v-if="step === 3" 
                        @click="submitBooking" 
                        :disabled="form.processing"
                        class="bg-emerald-600 text-white px-8 py-3 rounded-xl font-bold text-sm hover:bg-emerald-500 disabled:opacity-50 transition-all shadow-lg shadow-emerald-500/20 flex items-center gap-2">
                    <span v-if="form.processing" class="animate-spin h-4 w-4 border-2 border-white/30 border-t-white rounded-full"></span>
                    <span>Confirm Booking</span>
                </button>
            </div>

        </div>
    </div>
</template>

<style scoped>
/* Animations */
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
.animate-fade-in { animation: fadeIn 0.4s ease-out; }

@keyframes scaleUp { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
.animate-scale-up { animation: scaleUp 0.3s cubic-bezier(0.16, 1, 0.3, 1); }

/* Scrollbar */
.custom-scrollbar::-webkit-scrollbar { height: 4px; width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }
</style>