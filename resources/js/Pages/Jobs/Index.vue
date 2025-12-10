<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({ jobs: Array });
const user = usePage().props.auth.user;

// Simple Search Filter Logic (Client-side for smoothness)
const search = ref('');
const filteredJobs = computed(() => {
    if (!search.value) return props.jobs;
    return props.jobs.filter(job => 
        job.job_title.toLowerCase().includes(search.value.toLowerCase()) || 
        job.company_name.toLowerCase().includes(search.value.toLowerCase())
    );
});

// Helper for random gradient avatars
const getAvatarGradient = (name) => {
    const gradients = [
        'from-purple-500 to-indigo-500',
        'from-pink-500 to-rose-500',
        'from-blue-500 to-cyan-500',
        'from-emerald-500 to-teal-500'
    ];
    return gradients[name.length % gradients.length];
};
</script>

<template>
    <Head title="Career Hub" />

    <AuthenticatedLayout>
        <template #header>
            <div class="relative overflow-hidden rounded-xl bg-slate-900 border-b-4 border-purple-500">
                <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10"></div>
                <div class="absolute right-0 bottom-0 w-96 h-96 bg-purple-600/30 rounded-full blur-[100px] animate-pulse-slow"></div>
                
                <div class="relative py-8 px-4 flex flex-col md:flex-row justify-between items-center gap-6">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <span class="px-3 py-1 rounded-full bg-purple-500/20 border border-purple-500/30 text-purple-300 text-[10px] font-bold uppercase tracking-widest">
                                Provincial Job Fair 2025
                            </span>
                        </div>
                        <h2 class="font-bold text-3xl text-white tracking-tight">Find Your <span class="text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-pink-400">Dream Career</span></h2>
                        <p class="text-slate-400 text-sm mt-2 max-w-lg">Connect with top local businesses and build your future in the province.</p>
                    </div>

                    <Link v-if="user.role === 'business_owner'" :href="route('jobs.create')" class="group relative flex items-center gap-3 px-6 py-3 bg-white text-purple-900 rounded-xl font-bold shadow-xl shadow-purple-900/20 hover:scale-105 transition-all duration-300 overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-r from-purple-100 to-pink-100 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        <span class="relative z-10 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Post a Vacancy
                        </span>
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <div class="relative -mt-20 mb-12 mx-4 sm:mx-0 z-20 animate-fade-in-up">
                    <div class="bg-white/90 backdrop-blur-xl p-2 rounded-2xl shadow-2xl border border-white/50 flex items-center">
                        <div class="pl-4 text-slate-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input v-model="search" type="text" placeholder="Search job titles, companies, or keywords..." class="w-full bg-transparent border-none focus:ring-0 text-slate-800 placeholder-slate-400 text-lg h-12">
                        <button class="bg-slate-900 text-white px-6 py-3 rounded-xl font-bold text-sm hover:bg-purple-700 transition-colors shadow-lg">
                            Search
                        </button>
                    </div>
                </div>

                <div v-if="filteredJobs.length === 0" class="text-center py-20 animate-fade-in">
                    <div class="inline-flex items-center justify-center w-24 h-24 bg-slate-100 rounded-full mb-6">
                        <span class="text-4xl">💼</span>
                    </div>
                    <h3 class="text-xl font-bold text-slate-700">No jobs found</h3>
                    <p class="text-slate-500 mt-2">Try adjusting your search or check back later.</p>
                </div>

                <div v-else class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    <div 
                        v-for="(job, index) in filteredJobs" 
                        :key="job.id" 
                        class="group relative bg-white rounded-3xl p-6 shadow-xl border border-slate-100 hover:border-purple-200 hover:shadow-2xl hover:shadow-purple-500/10 transition-all duration-500 hover:-translate-y-2 animate-card-appear"
                        :style="`animation-delay: ${index * 0.1}s;`"
                    >
                        <div class="flex items-start justify-between mb-6">
                            <div class="flex items-center gap-4">
                                <div :class="`w-12 h-12 rounded-xl bg-gradient-to-br ${getAvatarGradient(job.company_name)} flex items-center justify-center text-white font-bold text-xl shadow-lg`">
                                    {{ job.company_name.charAt(0) }}
                                </div>
                                <div>
                                    <h3 class="font-bold text-slate-800 text-sm opacity-90">{{ job.company_name }}</h3>
                                    <p class="text-xs text-slate-400 mt-0.5 flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        {{ new Date(job.created_at).toLocaleDateString() }}
                                    </p>
                                </div>
                            </div>
                            <button class="text-slate-300 hover:text-purple-600 transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path></svg>
                            </button>
                        </div>

                        <div class="mb-6">
                            <h2 class="text-xl font-bold text-slate-900 group-hover:text-purple-700 transition-colors mb-2">{{ job.job_title }}</h2>
                            <div class="flex flex-wrap gap-2 mb-4">
                                <span class="px-3 py-1 bg-green-50 text-green-700 rounded-lg text-xs font-bold border border-green-100 flex items-center gap-1">
                                    <span class="text-lg leading-none">₱</span> {{ job.salary_range }}
                                </span>
                                <span class="px-3 py-1 bg-blue-50 text-blue-700 rounded-lg text-xs font-bold border border-blue-100">Full Time</span>
                            </div>
                            <p class="text-slate-500 text-sm line-clamp-3 leading-relaxed">
                                {{ job.description }}
                            </p>
                        </div>

                        <div class="pt-6 border-t border-slate-100 flex items-center justify-between">
                            <div class="flex items-center gap-1 text-xs text-slate-400 font-medium">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                {{ job.location }}
                            </div>
                            
                            <button class="px-4 py-2 bg-slate-900 text-white text-xs font-bold rounded-lg group-hover:bg-purple-600 transition-colors shadow-lg shadow-slate-900/10 group-hover:shadow-purple-600/30">
                                Apply Now
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
@keyframes pulse-slow {
    0%, 100% { opacity: 0.3; transform: scale(1); }
    50% { opacity: 0.5; transform: scale(1.1); }
}
.animate-pulse-slow { animation: pulse-slow 8s ease-in-out infinite; }

@keyframes fade-in-up {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in-up { animation: fade-in-up 0.8s ease-out both; }

@keyframes card-appear {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-card-appear { animation: card-appear 0.6s cubic-bezier(0.2, 0.8, 0.2, 1) both; }
</style>