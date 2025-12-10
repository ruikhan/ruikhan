<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    company_name: '',
    job_title: '',
    description: '',
    salary_range: '',
    location: '',
});

const submit = () => {
    form.post(route('jobs.store'));
};
</script>

<template>
    <Head title="Post a Job" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <div class="p-2 bg-purple-100 rounded-xl text-purple-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <h2 class="font-bold text-xl text-gray-800 leading-tight">Recruitment Studio</h2>
                    <p class="text-xs text-slate-500 uppercase tracking-wide font-semibold">Post a new vacancy</p>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                
                <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-slate-100 flex flex-col md:flex-row animate-fade-in-up">
                    
                    <div class="w-full md:w-1/3 bg-slate-50 border-r border-slate-100 p-8">
                        <h3 class="font-bold text-slate-800 mb-6">Posting Tips</h3>
                        
                        <div class="space-y-6">
                            <div class="flex gap-4">
                                <div class="w-8 h-8 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center font-bold text-sm flex-shrink-0">1</div>
                                <div class="text-sm text-slate-600 leading-relaxed">
                                    <span class="font-bold text-slate-800 block mb-1">Clear Titles</span>
                                    Use standard job titles like "Cashier" or "Accountant" so people can find them.
                                </div>
                            </div>
                            <div class="flex gap-4">
                                <div class="w-8 h-8 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center font-bold text-sm flex-shrink-0">2</div>
                                <div class="text-sm text-slate-600 leading-relaxed">
                                    <span class="font-bold text-slate-800 block mb-1">Be Specific</span>
                                    Include skills required and daily responsibilities in the description.
                                </div>
                            </div>
                            <div class="flex gap-4">
                                <div class="w-8 h-8 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center font-bold text-sm flex-shrink-0">3</div>
                                <div class="text-sm text-slate-600 leading-relaxed">
                                    <span class="font-bold text-slate-800 block mb-1">Salary Matters</span>
                                    Jobs with clear salary ranges get 40% more applicants.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="w-full md:w-2/3 p-8 md:p-10">
                        <form @submit.prevent="submit" class="space-y-6">
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wide">Company Name</label>
                                    <input v-model="form.company_name" type="text" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:bg-white focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all font-semibold text-slate-700" placeholder="e.g. Acme Corp">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wide">Job Title</label>
                                    <input v-model="form.job_title" type="text" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:bg-white focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all font-semibold text-slate-700" placeholder="e.g. Sales Associate">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wide">Salary Range</label>
                                    <div class="relative">
                                        <input v-model="form.salary_range" type="text" class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-8 pr-4 py-3 focus:bg-white focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all font-semibold text-slate-700" placeholder="15,000 - 20,000">
                                        <span class="absolute left-3 top-3 text-slate-400 font-bold">₱</span>
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wide">Location</label>
                                    <div class="relative">
                                        <input v-model="form.location" type="text" class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-4 py-3 focus:bg-white focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all font-semibold text-slate-700" placeholder="City, Province">
                                        <span class="absolute left-3 top-3 text-slate-400">📍</span>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-400 uppercase tracking-wide">Job Description</label>
                                <textarea v-model="form.description" rows="6" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:bg-white focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all text-sm resize-none text-slate-600" placeholder="Describe the role, requirements, and benefits..."></textarea>
                            </div>

                            <div class="pt-4 border-t border-slate-100 flex justify-end items-center gap-4">
                                <button type="button" @click="$inertia.visit(route('jobs.index'))" class="text-slate-500 hover:text-slate-700 font-bold text-sm">Cancel</button>
                                
                                <button :disabled="form.processing" class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 px-8 rounded-xl shadow-lg shadow-purple-500/30 transform hover:-translate-y-0.5 transition-all flex items-center gap-2">
                                    <span v-if="form.processing" class="animate-spin">⏳</span>
                                    <span>Publish Job</span>
                                </button>
                            </div>

                        </form>
                    </div>

                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.animate-fade-in-up { animation: fadeUp 0.6s ease-out; }
@keyframes fadeUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>