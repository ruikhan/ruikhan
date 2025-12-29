<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    auth: Object,
    stats: {
        type: Object,
        default: () => ({
            totalRequests: 24,
            pending: 8,
            processing: 5,
            completed: 11
        })
    },
    recentRequests: {
        type: Array,
        default: () => [
            { id: 1, type: 'Certificate of Residency', tracking: 'BRGY-20251222-A1B2', status: 'processing', date: '2 hours ago' },
            { id: 2, type: 'Business Permit', tracking: 'BPLO-20251221-X7Y8', status: 'pending', date: '1 day ago' },
            { id: 3, type: 'Birth Certificate', tracking: 'MCR-20251220-M5N6', status: 'completed', date: '2 days ago' }
        ]
    },
    announcements: {
        type: Array,
        default: () => [
            { id: 1, title: 'Holiday Schedule: Municipal Offices Closed', date: 'Dec 25, 2024', category: 'Holiday' },
            { id: 2, title: 'New Online Services Available', date: 'Dec 20, 2024', category: 'Update' },
            { id: 3, title: 'System Maintenance: Dec 24, 11PM - 2AM', date: 'Dec 18, 2024', category: 'Maintenance' }
        ]
    }
});

const quickServices = [
    { name: 'Barangay Certifications', icon: '🏘️', route: 'services.create', param: 'Barangay Certifications', color: 'from-teal-500 to-cyan-500', count: 9 },
    { name: 'Civil Registry', icon: '📜', route: 'services.create', param: 'Municipal Civil Registrar', color: 'from-pink-500 to-rose-500', count: 4 },
    { name: 'Business Permits', icon: '💼', route: 'services.create', param: 'Business Permits (BPLO)', color: 'from-blue-500 to-indigo-500', count: 3 },
    { name: 'Health Services', icon: '🩺', route: 'services.create', param: 'Municipal Health Office', color: 'from-emerald-500 to-teal-500', count: 2 },
];

const statusColors = {
    pending: 'bg-yellow-500/20 text-yellow-400 border-yellow-500/30',
    processing: 'bg-blue-500/20 text-blue-400 border-blue-500/30',
    completed: 'bg-green-500/20 text-green-400 border-green-500/30',
    ready_for_pickup: 'bg-purple-500/20 text-purple-400 border-purple-500/30'
};

const categoryColors = {
    Holiday: 'bg-red-500/10 text-red-400',
    Update: 'bg-blue-500/10 text-blue-400',
    Maintenance: 'bg-orange-500/10 text-orange-400'
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <!-- Background Effects -->
        <div class="fixed inset-0 z-0 pointer-events-none">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_var(--tw-gradient-stops))] from-[#0f172a] via-[#050505] to-black"></div>
            <div class="absolute inset-0 opacity-[0.03] bg-[url('https://grainy-gradients.vercel.app/noise.svg')] brightness-100 contrast-150"></div>
            <div class="absolute top-0 left-1/4 w-[40vw] h-[40vw] bg-blue-600/5 rounded-full blur-[120px]"></div>
            <div class="absolute bottom-0 right-1/4 w-[40vw] h-[40vw] bg-purple-600/5 rounded-full blur-[120px]"></div>
        </div>

        <div class="relative z-10 py-12 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Welcome Header -->
            <div class="mb-10 animate-fade-in-up">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h1 class="text-4xl font-black text-white tracking-tight mb-2">
                            Welcome back, {{ auth.user.name }}!
                        </h1>
                        <p class="text-slate-400 text-lg">Here's what's happening with your government services today.</p>
                    </div>
                    <div class="hidden md:flex items-center gap-3">
                        <Link :href="route('simulation')" 
                              class="group flex items-center gap-2 px-4 py-2 bg-purple-600 hover:bg-purple-700 rounded-xl text-white font-semibold transition-all hover:shadow-lg">
                            <span class="text-xl">🎬</span>
                            <span>Workflow Demo</span>
                            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </Link>
                        <div class="text-right">
                            <div class="text-xs text-slate-500 uppercase tracking-wider">Current Time</div>
                            <div class="text-white font-mono font-bold text-lg">{{ new Date().toLocaleTimeString() }}</div>
                        </div>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="group bg-gradient-to-br from-blue-500/10 to-blue-600/5 border border-blue-500/20 rounded-2xl p-6 hover:border-blue-500/40 transition-all hover:scale-105 cursor-pointer">
                        <div class="flex items-start justify-between mb-4">
                            <div class="w-12 h-12 bg-blue-500/20 rounded-xl flex items-center justify-center text-2xl">
                                📊
                            </div>
                            <span class="text-blue-400 text-xs font-bold uppercase tracking-wider">Total</span>
                        </div>
                        <div class="text-4xl font-black text-white mb-1">{{ stats.totalRequests }}</div>
                        <div class="text-slate-400 text-sm">Total Requests</div>
                    </div>

                    <div class="group bg-gradient-to-br from-yellow-500/10 to-yellow-600/5 border border-yellow-500/20 rounded-2xl p-6 hover:border-yellow-500/40 transition-all hover:scale-105 cursor-pointer">
                        <div class="flex items-start justify-between mb-4">
                            <div class="w-12 h-12 bg-yellow-500/20 rounded-xl flex items-center justify-center text-2xl">
                                ⏳
                            </div>
                            <span class="text-yellow-400 text-xs font-bold uppercase tracking-wider">Pending</span>
                        </div>
                        <div class="text-4xl font-black text-white mb-1">{{ stats.pending }}</div>
                        <div class="text-slate-400 text-sm">Awaiting Review</div>
                    </div>

                    <div class="group bg-gradient-to-br from-purple-500/10 to-purple-600/5 border border-purple-500/20 rounded-2xl p-6 hover:border-purple-500/40 transition-all hover:scale-105 cursor-pointer">
                        <div class="flex items-start justify-between mb-4">
                            <div class="w-12 h-12 bg-purple-500/20 rounded-xl flex items-center justify-center text-2xl">
                                ⚙️
                            </div>
                            <span class="text-purple-400 text-xs font-bold uppercase tracking-wider">Processing</span>
                        </div>
                        <div class="text-4xl font-black text-white mb-1">{{ stats.processing }}</div>
                        <div class="text-slate-400 text-sm">In Progress</div>
                    </div>

                    <div class="group bg-gradient-to-br from-green-500/10 to-green-600/5 border border-green-500/20 rounded-2xl p-6 hover:border-green-500/40 transition-all hover:scale-105 cursor-pointer">
                        <div class="flex items-start justify-between mb-4">
                            <div class="w-12 h-12 bg-green-500/20 rounded-xl flex items-center justify-center text-2xl">
                                ✅
                            </div>
                            <span class="text-green-400 text-xs font-bold uppercase tracking-wider">Completed</span>
                        </div>
                        <div class="text-4xl font-black text-white mb-1">{{ stats.completed }}</div>
                        <div class="text-slate-400 text-sm">Ready to Claim</div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- Main Content (Left Side) -->
                <div class="lg:col-span-2 space-y-8">
                    
                    <!-- Featured: Workflow Simulator Banner -->
                    <div class="animate-fade-in-up">
                        <Link :href="route('simulation')"
                              class="group block relative bg-gradient-to-r from-purple-500/20 via-pink-500/20 to-blue-500/20 backdrop-blur-xl border border-purple-500/30 rounded-2xl p-8 hover:border-purple-500/50 transition-all overflow-hidden">
                            <div class="absolute top-0 right-0 w-64 h-64 bg-purple-500/10 rounded-full blur-3xl -mr-32 -mt-32 group-hover:bg-purple-500/20 transition-all"></div>
                            
                            <div class="relative z-10 flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="inline-flex items-center gap-2 px-3 py-1 bg-purple-500/20 text-purple-300 rounded-full text-xs font-bold mb-3">
                                        <span class="animate-pulse">●</span>
                                        INTERACTIVE DEMO
                                    </div>
                                    <h3 class="text-2xl font-black text-white mb-2">Workflow Process Simulator</h3>
                                    <p class="text-slate-300 text-sm mb-4">See how government services work step-by-step with real scenarios and characters.</p>
                                    <div class="flex items-center gap-4 text-sm text-slate-400">
                                        <span class="flex items-center gap-1">
                                            <span>🎬</span>
                                            6 Scenarios
                                        </span>
                                        <span class="flex items-center gap-1">
                                            <span>⚡</span>
                                            Interactive
                                        </span>
                                        <span class="flex items-center gap-1">
                                            <span>📊</span>
                                            Visual
                                        </span>
                                    </div>
                                </div>
                                <div class="hidden md:block">
                                    <div class="w-20 h-20 bg-purple-500/20 rounded-2xl flex items-center justify-center text-4xl border border-purple-500/30 group-hover:scale-110 transition-transform">
                                        🎭
                                    </div>
                                </div>
                            </div>
                        </Link>
                    </div>
                    
                    <!-- Quick Actions -->
                    <div class="animate-fade-in-up" style="animation-delay: 0.15s;">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-2xl font-bold text-white flex items-center gap-3">
                                <span class="w-1 h-8 bg-blue-500 rounded-full"></span>
                                Quick Services
                            </h2>
                            <Link :href="route('services.landing')" class="text-blue-400 hover:text-blue-300 text-sm font-medium flex items-center gap-1 group">
                                View All
                                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </Link>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <Link v-for="service in quickServices" :key="service.name"
                                  :href="route(service.route, { department: service.param })"
                                  class="group relative bg-[#0f1115]/60 backdrop-blur-xl border border-white/10 rounded-2xl p-6 hover:border-white/20 transition-all hover:-translate-y-1 overflow-hidden">
                                <div :class="['absolute inset-0 bg-gradient-to-br opacity-0 group-hover:opacity-10 transition-opacity', service.color]"></div>
                                
                                <div class="relative z-10 flex items-center justify-between">
                                    <div class="flex items-center gap-4">
                                        <div class="w-14 h-14 bg-white/5 rounded-xl flex items-center justify-center text-3xl border border-white/10">
                                            {{ service.icon }}
                                        </div>
                                        <div>
                                            <h3 class="text-white font-bold text-lg">{{ service.name }}</h3>
                                            <p class="text-slate-400 text-sm">{{ service.count }} types available</p>
                                        </div>
                                    </div>
                                    <svg class="w-6 h-6 text-slate-500 group-hover:text-white group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </div>
                            </Link>
                        </div>
                    </div>

                    <!-- Recent Requests -->
                    <div class="animate-fade-in-up" style="animation-delay: 0.3s;">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-2xl font-bold text-white flex items-center gap-3">
                                <span class="w-1 h-8 bg-purple-500 rounded-full"></span>
                                Recent Requests
                            </h2>
                            <Link :href="route('services.index')" class="text-purple-400 hover:text-purple-300 text-sm font-medium flex items-center gap-1 group">
                                View History
                                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </Link>
                        </div>

                        <div class="space-y-4">
                            <div v-for="request in recentRequests" :key="request.id"
                                 class="group bg-[#0f1115]/60 backdrop-blur-xl border border-white/10 rounded-2xl p-6 hover:border-white/20 transition-all hover:-translate-y-1">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex-1">
                                        <h3 class="text-white font-bold text-lg mb-1">{{ request.type }}</h3>
                                        <p class="text-slate-500 text-sm font-mono">{{ request.tracking }}</p>
                                    </div>
                                    <span :class="['px-3 py-1 rounded-full text-xs font-bold border', statusColors[request.status]]">
                                        {{ request.status.toUpperCase() }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-400 text-sm">{{ request.date }}</span>
                                    <button class="text-blue-400 hover:text-blue-300 text-sm font-medium flex items-center gap-1 group">
                                        Track Status
                                        <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <div v-if="recentRequests.length === 0" class="bg-[#0f1115]/60 backdrop-blur-xl border border-white/10 rounded-2xl p-12 text-center">
                                <div class="text-6xl mb-4">📄</div>
                                <h3 class="text-white font-bold text-lg mb-2">No Requests Yet</h3>
                                <p class="text-slate-400 text-sm mb-6">Start by requesting a government service</p>
                                <Link :href="route('services.landing')" class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl transition-all">
                                    Browse Services
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar (Right Side) -->
                <div class="space-y-8">
                    
                    <!-- Profile Card -->
                    <div class="animate-fade-in-up" style="animation-delay: 0.4s;">
                        <div class="bg-gradient-to-br from-blue-500/10 to-purple-500/10 backdrop-blur-xl border border-white/10 rounded-2xl p-6 relative overflow-hidden">
                            <div class="absolute top-0 right-0 w-32 h-32 bg-blue-500/10 rounded-full blur-3xl -mr-16 -mt-16"></div>
                            
                            <div class="relative z-10">
                                <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-purple-500 rounded-2xl flex items-center justify-center text-3xl font-black text-white mb-4">
                                    {{ auth.user.name.charAt(0).toUpperCase() }}
                                </div>
                                <h3 class="text-white font-bold text-xl mb-1">{{ auth.user.name }}</h3>
                                <p class="text-slate-400 text-sm mb-1">{{ auth.user.email }}</p>
                                <p class="text-slate-500 text-xs mb-4">{{ auth.user.phone_number || 'No phone number' }}</p>
                                
                                <div class="flex items-center gap-2 mb-4">
                                    <span class="px-3 py-1 bg-blue-500/20 text-blue-400 rounded-full text-xs font-bold border border-blue-500/30">
                                        {{ auth.user.role.toUpperCase() }}
                                    </span>
                                    <span class="px-3 py-1 bg-green-500/20 text-green-400 rounded-full text-xs font-bold border border-green-500/30 flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 bg-green-400 rounded-full animate-pulse"></span>
                                        VERIFIED
                                    </span>
                                </div>

                                <Link href="/profile" class="block w-full text-center py-3 bg-white/5 hover:bg-white/10 border border-white/10 hover:border-white/20 text-white font-bold rounded-xl transition-all">
                                    Edit Profile
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- Announcements -->
                    <div class="animate-fade-in-up" style="animation-delay: 0.5s;">
                        <h2 class="text-xl font-bold text-white flex items-center gap-3 mb-4">
                            <span class="w-1 h-6 bg-yellow-500 rounded-full"></span>
                            Announcements
                        </h2>
                        
                        <div class="space-y-3">
                            <div v-for="announcement in announcements" :key="announcement.id"
                                 class="bg-[#0f1115]/60 backdrop-blur-xl border border-white/10 rounded-xl p-4 hover:border-white/20 transition-all">
                                <div class="flex items-start gap-3">
                                    <div class="w-2 h-2 bg-yellow-400 rounded-full mt-2 flex-shrink-0"></div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-white font-semibold text-sm mb-1 line-clamp-2">{{ announcement.title }}</h4>
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="text-slate-500 text-xs">{{ announcement.date }}</span>
                                            <span :class="['px-2 py-0.5 rounded text-[10px] font-bold', categoryColors[announcement.category]]">
                                                {{ announcement.category }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Help & Support -->
                    <div class="animate-fade-in-up" style="animation-delay: 0.6s;">
                        <div class="bg-gradient-to-br from-orange-500/10 to-red-500/10 backdrop-blur-xl border border-orange-500/20 rounded-2xl p-6">
                            <div class="text-4xl mb-3">💬</div>
                            <h3 class="text-white font-bold text-lg mb-2">Need Help?</h3>
                            <p class="text-slate-400 text-sm mb-4">Our support team is available Monday to Friday, 8:00 AM - 5:00 PM</p>
                            <div class="space-y-2">
                                <a href="tel:+639123456789" class="flex items-center gap-3 text-orange-400 hover:text-orange-300 text-sm font-medium transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                    (052) 123-4567
                                </a>
                                <a href="mailto:help@pili.gov.ph" class="flex items-center gap-3 text-orange-400 hover:text-orange-300 text-sm font-medium transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                    help@pili.gov.ph
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