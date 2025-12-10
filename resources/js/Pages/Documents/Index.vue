<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';

// Receive the list of requests from the Controller
const props = defineProps({
    requests: Array
});

const user = usePage().props.auth.user;

// Setup the form handler with NEW fields
const form = useForm({
    document_type: '',
    // Personal Details (For the form appearance)
    civil_status: '',
    citizenship: 'Filipino',
    // Medical Specifics
    blood_type: '',
    weight: '',
    height: '',
    medical_condition: '',
    // General
    purpose_specific: '', // We use this for the text input
    purpose: '', // This is what we actually send to the backend
});

// Watch for changes and combine data into the main 'purpose' field for the backend
const submit = () => {
    let combinedPurpose = `Purpose: ${form.purpose_specific}.`;

    // Add extra details based on document type
    if (['Barangay Clearance', 'Certificate of Indigency'].includes(form.document_type)) {
        combinedPurpose += ` | Civil Status: ${form.civil_status} | Citizenship: ${form.citizenship}`;
    }
    
    if (form.document_type === 'Health Certificate') {
        combinedPurpose += ` | Blood Type: ${form.blood_type} | Wt: ${form.weight}kg | Ht: ${form.height}cm | Condition: ${form.medical_condition}`;
    }

    form.purpose = combinedPurpose;

    form.post(route('documents.store'), {
        onSuccess: () => {
            form.reset();
            // Reset the specific fields manually since reset() restores the data object
            form.purpose_specific = '';
            form.medical_condition = '';
        },
    });
};
</script>

<template>
    <Head title="Request Documents" />

    <AuthenticatedLayout>
        <template #header>
            <div class="relative overflow-hidden rounded-xl">
                <div class="absolute inset-0 bg-gradient-to-r from-slate-900 to-blue-900 opacity-90"></div>
                <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 20px 20px;"></div>
                
                <div class="relative flex items-center gap-4 py-4 px-2">
                    <div class="w-12 h-12 bg-white/10 backdrop-blur-md border border-white/20 rounded-xl flex items-center justify-center text-2xl shadow-lg">
                        🗂️
                    </div>
                    <div>
                        <h2 class="font-bold text-2xl text-white tracking-tight">Document Issuance</h2>
                        <p class="text-blue-200 text-xs uppercase tracking-widest font-semibold">Official Request Portal</p>
                    </div>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
                
                <div class="bg-white rounded-xl shadow-2xl overflow-hidden border border-slate-200 relative">
                    <div class="h-2 w-full bg-gradient-to-r from-blue-600 via-blue-500 to-cyan-400"></div>

                    <div class="flex flex-col md:flex-row">
                        
                        <div class="hidden md:block w-1/3 bg-slate-50 border-r border-slate-200 p-8 relative overflow-hidden">
                            <div class="absolute -bottom-20 -left-20 w-64 h-64 bg-slate-200 rounded-full opacity-50 blur-3xl"></div>
                            
                            <h3 class="font-bold text-slate-800 text-lg mb-4">Request Guidelines</h3>
                            <ul class="space-y-4 text-sm text-slate-600">
                                <li class="flex gap-3">
                                    <span class="flex-shrink-0 w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xs">1</span>
                                    <span>Ensure all personal information matches your valid ID.</span>
                                </li>
                                <li class="flex gap-3">
                                    <span class="flex-shrink-0 w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xs">2</span>
                                    <span>Medical certificates require accurate health data for emergency referencing.</span>
                                </li>
                                <li class="flex gap-3">
                                    <span class="flex-shrink-0 w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xs">3</span>
                                    <span>Processing takes <strong>1-2 business days</strong> depending on official availability.</span>
                                </li>
                            </ul>

                            <div class="mt-8 p-4 bg-blue-50/50 border border-blue-100 rounded-lg">
                                <p class="text-xs text-blue-600 font-semibold uppercase tracking-wide mb-1">Support</p>
                                <p class="text-xs text-slate-500">Need help? Call the Secretariat at <span class="font-bold text-slate-700">123-4567</span>.</p>
                            </div>
                        </div>

                        <div class="w-full md:w-2/3 p-8">
                            <form @submit.prevent="submit" class="space-y-8">
                                
                                <div>
                                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">01. Select Document</label>
                                    <div class="relative">
                                        <select v-model="form.document_type" class="w-full bg-slate-50 border border-slate-200 text-slate-700 font-bold rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all hover:bg-white cursor-pointer">
                                            <option disabled value="">Select Document Type...</option>
                                            <option value="Barangay Clearance">📋 Barangay Clearance</option>
                                            <option value="Certificate of Indigency">📜 Certificate of Indigency</option>
                                            <option value="Business Permit">🏪 Business Permit</option>
                                            <option value="Health Certificate">🩺 Health / Medical Certificate</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="animate-fade-in-up">
                                    <div class="flex items-center gap-4 mb-4">
                                        <div class="h-px bg-slate-200 flex-1"></div>
                                        <label class="text-xs font-bold text-slate-400 uppercase tracking-widest">02. Applicant Details</label>
                                        <div class="h-px bg-slate-200 flex-1"></div>
                                    </div>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="group">
                                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Full Name</label>
                                            <input type="text" :value="user.name" disabled class="w-full bg-slate-100 border border-slate-200 text-slate-500 font-bold rounded-lg px-3 py-2 cursor-not-allowed">
                                        </div>

                                        <Transition name="fade">
                                            <div v-if="['Barangay Clearance', 'Certificate of Indigency'].includes(form.document_type)" class="contents">
                                                <div>
                                                    <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Civil Status</label>
                                                    <select v-model="form.civil_status" class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                                                        <option>Single</option>
                                                        <option>Married</option>
                                                        <option>Widowed</option>
                                                    </select>
                                                </div>
                                                <div>
                                                    <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Citizenship</label>
                                                    <input v-model="form.citizenship" type="text" class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                                                </div>
                                            </div>
                                        </Transition>
                                    </div>
                                </div>

                                <Transition name="slide-fade">
                                    <div v-if="form.document_type === 'Health Certificate'" class="bg-red-50/50 border border-red-100 rounded-xl p-6">
                                        <div class="flex items-center gap-2 mb-4">
                                            <div class="p-1 bg-red-100 rounded text-red-600">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                                            </div>
                                            <label class="text-xs font-bold text-red-800 uppercase tracking-widest">03. Health Information</label>
                                        </div>

                                        <div class="grid grid-cols-3 gap-4 mb-4">
                                            <div>
                                                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Blood Type</label>
                                                <select v-model="form.blood_type" class="w-full bg-white border border-red-200 rounded-lg px-3 py-2 text-sm">
                                                    <option>A+</option><option>A-</option><option>B+</option><option>B-</option><option>O+</option><option>O-</option><option>AB+</option><option>AB-</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Weight (kg)</label>
                                                <input v-model="form.weight" type="number" class="w-full bg-white border border-red-200 rounded-lg px-3 py-2 text-sm">
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Height (cm)</label>
                                                <input v-model="form.height" type="number" class="w-full bg-white border border-red-200 rounded-lg px-3 py-2 text-sm">
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Existing Medical Conditions / Allergies</label>
                                            <input v-model="form.medical_condition" type="text" placeholder="e.g. Asthma, Penicillin Allergy" class="w-full bg-white border border-red-200 rounded-lg px-3 py-2 text-sm">
                                        </div>
                                    </div>
                                </Transition>

                                <div>
                                    <div class="flex items-center gap-4 mb-4">
                                        <div class="h-px bg-slate-200 flex-1"></div>
                                        <label class="text-xs font-bold text-slate-400 uppercase tracking-widest">04. Statement of Purpose</label>
                                        <div class="h-px bg-slate-200 flex-1"></div>
                                    </div>
                                    <textarea 
                                        v-model="form.purpose_specific" 
                                        rows="3" 
                                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all resize-none"
                                        placeholder="State the specific reason for this request (e.g. Employment requirement at XYZ Corp)..."
                                    ></textarea>
                                </div>

                                <div class="pt-4 border-t border-slate-100 flex justify-end">
                                    <button 
                                        :disabled="form.processing"
                                        class="group relative inline-flex items-center gap-2 px-8 py-3 bg-slate-900 text-white rounded-xl font-bold text-sm overflow-hidden shadow-xl hover:shadow-2xl hover:scale-105 transition-all duration-300 disabled:opacity-50"
                                    >
                                        <div class="absolute inset-0 w-full h-full bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-700"></div>
                                        <span>Submit Application</span>
                                        <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                    </button>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>

                <div v-if="requests.length > 0" class="mt-12">
                     <h3 class="font-bold text-slate-800 text-lg mb-4 ml-2 border-l-4 border-blue-500 pl-3 text-white">Recent Requests</h3>
                     <div class="grid gap-4">
                        <div v-for="req in requests" :key="req.id" class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex flex-col md:flex-row justify-between items-center gap-4 hover:border-blue-300 transition-colors">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-lg shadow-sm">📄</div>
                                <div>
                                    <h4 class="font-bold text-slate-800">{{ req.document_type }}</h4>
                                    <p class="text-xs text-slate-500">{{ req.purpose }}</p>
                                </div>
                            </div>
                            <div class="flex flex-col items-end gap-2">
                                <span v-if="req.status === 'pending'" class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-lg text-xs font-bold uppercase">Pending</span>
                                <span v-if="req.status === 'completed'" class="px-3 py-1 bg-green-100 text-green-700 rounded-lg text-xs font-bold uppercase">Ready</span>
                                
                                <a v-if="req.status === 'completed'" :href="route('documents.verify', req.id)" target="_blank" class="text-xs font-bold text-slate-900 underline decoration-2 decoration-blue-500 hover:text-blue-600">
                                    View Certificate ↗
                                </a>
                            </div>
                        </div>
                     </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
/* Smooth Transitions for the Form Fields */
.slide-fade-enter-active {
  transition: all 0.4s ease-out;
}
.slide-fade-leave-active {
  transition: all 0.3s cubic-bezier(1, 0.5, 0.8, 1);
}
.slide-fade-enter-from,
.slide-fade-leave-to {
  transform: translateY(-10px);
  opacity: 0;
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>