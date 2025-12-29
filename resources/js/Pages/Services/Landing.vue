<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const departments = [
        { 
        id: 'Barangay Certifications', 
        name: 'Barangay Certifications', 
        description: 'Residency, Indigency, Clearances.', 
        icon: '🏘️', 
        color: 'from-teal-500 to-cyan-500', 
        bg: 'bg-teal-500/10 border-teal-500/20', 
        text: 'text-teal-400',
        mobileBg: 'bg-gradient-to-br from-teal-400 to-cyan-500'
    },

    { 
        id: 'Municipal Civil Registrar', 
        name: 'Civil Registrar', 
        description: 'Birth, Marriage, Death certificates.', 
        icon: '💍', 
        color: 'from-pink-500 to-rose-500', 
        bg: 'bg-pink-500/10 border-pink-500/20', 
        text: 'text-pink-400',
        mobileBg: 'bg-gradient-to-br from-pink-400 to-rose-500'
    },
    { 
        id: 'Engineering Office', 
        name: 'Engineering', 
        description: 'Building & Occupancy permits.', 
        icon: '🏗️', 
        color: 'from-orange-500 to-amber-500', 
        bg: 'bg-orange-500/10 border-orange-500/20', 
        text: 'text-orange-400',
        mobileBg: 'bg-gradient-to-br from-orange-400 to-amber-500'
    },
    { 
        id: 'Municipal Health Office', 
        name: 'Health Office', 
        description: 'Sanitary permits & Health cards.', 
        icon: '🩺', 
        color: 'from-emerald-500 to-teal-500', 
        bg: 'bg-emerald-500/10 border-emerald-500/20', 
        text: 'text-emerald-400',
        mobileBg: 'bg-gradient-to-br from-emerald-400 to-teal-500'
    },
    { 
        id: 'Business Permits (BPLO)', 
        name: 'Business Licensing', 
        description: 'Business registration & renewals.', 
        icon: '💼', 
        color: 'from-blue-500 to-indigo-500', 
        bg: 'bg-blue-500/10 border-blue-500/20', 
        text: 'text-blue-400',
        mobileBg: 'bg-gradient-to-br from-blue-400 to-indigo-500'
    },
    { 
        id: 'Assessor’s Office', 
        name: 'Assessor’s Office', 
        description: 'Tax decs & Property assessment.', 
        icon: '🏡', 
        color: 'from-purple-500 to-violet-500', 
        bg: 'bg-purple-500/10 border-purple-500/20', 
        text: 'text-purple-400',
        mobileBg: 'bg-gradient-to-br from-purple-400 to-violet-500'
    },
    { 
        id: 'Social Welfare (MSWDO)', 
        name: 'Social Welfare', 
        description: 'Indigency & Financial Aid.', 
        icon: '🤝', 
        color: 'from-cyan-500 to-sky-500', 
        bg: 'bg-cyan-500/10 border-cyan-500/20', 
        text: 'text-cyan-400',
        mobileBg: 'bg-gradient-to-br from-cyan-400 to-sky-500'
    },
    { 
        id: 'MPDO', 
        name: 'Planning (MPDO)', 
        description: 'Zoning & Development plans.', 
        icon: '🗺️', 
        color: 'from-yellow-500 to-lime-500', 
        bg: 'bg-yellow-500/10 border-yellow-500/20', 
        text: 'text-yellow-400',
        mobileBg: 'bg-gradient-to-br from-yellow-400 to-lime-500'
    },
    { 
        id: 'Agriculture Office', 
        name: 'Agriculture', 
        description: 'Farming seeds & Livestock support.', 
        icon: '🌾', 
        color: 'from-green-600 to-lime-600', 
        bg: 'bg-green-600/10 border-green-600/20', 
        text: 'text-green-500',
        mobileBg: 'bg-gradient-to-br from-green-500 to-lime-600'
    },
    { 
        id: 'MDRRMO', 
        name: 'Disaster (MDRRMO)', 
        description: 'Incident reports & Emergency.', 
        icon: '🚨', 
        color: 'from-red-500 to-rose-600', 
        bg: 'bg-red-500/10 border-red-500/20', 
        text: 'text-red-400',
        mobileBg: 'bg-gradient-to-br from-red-400 to-rose-600'
    },
    { 
        id: 'Treasurer’s Office', 
        name: 'Treasurer', 
        description: 'Tax payments & Cedula.', 
        icon: '💰', 
        color: 'from-yellow-400 to-amber-400', 
        bg: 'bg-yellow-400/10 border-yellow-400/20', 
        text: 'text-yellow-300',
        mobileBg: 'bg-gradient-to-br from-yellow-400 to-amber-500'
    }
];
</script>

<template>
    <Head title="Select Service" />

    <AuthenticatedLayout>
        
        <!-- MOBILE LAYOUT (iOS Style) - < 1024px -->
        <div class="lg:hidden w-full max-w-md mx-auto px-4 py-6 pb-24">
            
            <!-- Header -->
            <div class="mb-6">
                <Link :href="route('dashboard')" class="inline-flex items-center gap-2 text-gray-600 text-sm mb-4 hover:text-gray-900">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back to Dashboard
                </Link>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Select Service</h1>
                <p class="text-gray-600 text-sm">Choose a department to start your application</p>
            </div>

            <!-- Department Cards Grid -->
            <div class="grid grid-cols-2 gap-3">
                <Link v-for="dept in departments" :key="dept.id"
                      :href="route('services.create', { department: dept.id })"
                      class="flex flex-col items-center justify-center p-4 bg-white rounded-2xl shadow-sm border border-gray-200 active:scale-95 transition-transform">
                    
                    <!-- Icon -->
                    <div :class="['w-14 h-14 rounded-2xl flex items-center justify-center text-2xl mb-3 shadow-md', dept.mobileBg]">
                        {{ dept.icon }}
                    </div>

                    <!-- Name -->
                    <h3 class="text-sm font-bold text-gray-900 text-center mb-1 leading-tight">
                        {{ dept.name }}
                    </h3>
                    
                    <!-- Description -->
                    <p class="text-[10px] text-gray-600 text-center leading-tight line-clamp-2">
                        {{ dept.description }}
                    </p>
                </Link>
            </div>

            <!-- View History Link -->
            <div class="mt-8 text-center">
                <Link :href="route('services.index')" 
                      class="inline-flex items-center gap-2 text-gray-600 text-sm font-medium hover:text-gray-900">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>View Application History</span>
                </Link>
            </div>
        </div>

        <!-- DESKTOP LAYOUT (Original Dark) - ≥ 1024px -->
        <div class="hidden lg:block">
            <div class="fixed inset-0 z-0 pointer-events-none">
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_var(--tw-gradient-stops))] from-[#0f172a] via-[#050505] to-black"></div>
                <div class="absolute inset-0 opacity-[0.03] bg-[url('https://grainy-gradients.vercel.app/noise.svg')] brightness-100 contrast-150"></div>
                <div class="absolute top-0 left-0 w-[40vw] h-[40vw] bg-blue-600/5 rounded-full blur-[100px]"></div>
                <div class="absolute bottom-0 right-0 w-[40vw] h-[40vw] bg-purple-600/5 rounded-full blur-[100px]"></div>
            </div>

            <div class="relative z-10 py-16 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 min-h-screen flex flex-col justify-center">
                
                <div class="text-center mb-16 animate-fade-in-up">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/5 border border-white/10 backdrop-blur-md mb-6">
                        <span class="text-xs font-bold uppercase tracking-[0.2em] text-slate-300">Smart Services Portal</span>
                    </div>
                    <h1 class="text-4xl md:text-6xl font-black text-white tracking-tight mb-4">
                        What do you need today?
                    </h1>
                    <p class="text-slate-400 text-lg max-w-2xl mx-auto">
                        Select the department below to begin your automated application process.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 animate-fade-in-up" style="animation-delay: 0.1s;">
                    
                    <Link v-for="dept in departments" :key="dept.id"
                          :href="route('services.create', { department: dept.id })"
                          class="group relative bg-[#0f1115]/60 backdrop-blur-xl border border-white/10 rounded-[2rem] p-8 hover:border-white/20 transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl overflow-hidden">
                        
                        <div :class="['absolute inset-0 bg-gradient-to-br opacity-0 group-hover:opacity-10 transition-opacity duration-500', dept.color]"></div>

                        <div class="relative z-10 flex flex-col h-full">
                            <div :class="['w-16 h-16 rounded-2xl flex items-center justify-center text-3xl mb-6 shadow-inner transition-transform group-hover:scale-110 duration-300', dept.bg]">
                                {{ dept.icon }}
                            </div>

                            <h3 class="text-2xl font-bold text-white mb-3 group-hover:text-white transition-colors">
                                {{ dept.name }}
                            </h3>
                            
                            <p class="text-slate-400 text-sm leading-relaxed mb-8 flex-grow">
                                {{ dept.description }}
                            </p>

                            <div class="flex items-center justify-between pt-6 border-t border-white/5">
                                <span :class="['text-xs font-bold uppercase tracking-widest', dept.text]">Start Application</span>
                                <div :class="['w-8 h-8 rounded-full flex items-center justify-center border transition-all duration-300 group-hover:bg-white group-hover:text-black', dept.bg, dept.text]">
                                    <svg class="w-4 h-4 transform -rotate-45 group-hover:rotate-0 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </div>
                            </div>
                        </div>
                    </Link>

                </div>

                <div class="mt-16 text-center animate-fade-in-up" style="animation-delay: 0.2s;">
                    <Link :href="route('services.index')" class="text-slate-500 hover:text-white text-sm font-medium transition-colors border-b border-transparent hover:border-white pb-0.5">
                        View my past application history &rarr;
                    </Link>
                </div>

            </div>
        </div>

    </AuthenticatedLayout>
</template>

<style scoped>
@keyframes fade-in-up {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in-up { animation: fade-in-up 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; opacity: 0; }

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>