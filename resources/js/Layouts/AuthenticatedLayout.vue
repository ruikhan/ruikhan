<script setup>
import ChatAssistant from '@/Components/ChatAssistant.vue'; // Global Chatbot
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

const showingNavigationDropdown = ref(false);
const user = usePage().props.auth.user;

// Helper for Avatar Gradient
const getAvatarGradient = (name) => {
    const gradients = ['from-blue-500 to-indigo-500', 'from-purple-500 to-pink-500', 'from-emerald-500 to-teal-500'];
    return gradients[name.length % gradients.length];
};

// URL for the logo image (assuming it's in public/images)
const logoUrl = '/images/enotpili.png'; 
// --- LOADING STATE LOGIC ---
const isLoading = ref(false);

// Listen to Inertia navigation events
router.on('start', () => { 
    // Only show loader if it takes longer than 200ms (prevents flickering on fast networks)
    setTimeout(() => {
        if (router.remember) return; // Optional: logic to verify active request
        isLoading.value = true; 
    }, 200); 
});

router.on('finish', () => {
    isLoading.value = false;
});
</script>

<template>
    <div class="min-h-screen bg-slate-950 relative overflow-x-hidden font-sans">
        
        <nav class="sticky top-0 z-50 border-b border-white/10">
            <div class="absolute inset-0 backdrop-blur-xl bg-slate-900/70 bg-[url('https://www.transparenttextures.com/patterns/sound-wave.png')] bg-cover bg-center opacity-50"></div>
            
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    
                    <div class="flex items-center">
                        <div class="shrink-0 flex items-center">
                            <Link :href="route('dashboard')" class="group">
                                <img :src="logoUrl" alt="ENOT PILI Logo" class="h-20 w-auto object-contain group-hover:opacity-90 transition-opacity" />
                            </Link>
                        </div>

                        <div class="hidden space-x-6 sm:-my-px sm:ml-10 sm:flex items-center font-medium">
                            
                            <Link :href="route('dashboard')" :class="['px-3 py-2 rounded-lg text-sm transition-all', route().current('dashboard') ? 'bg-blue-600/20 text-blue-300 shadow-[0_0_15px_rgba(59,130,246,0.3)]' : 'text-slate-400 hover:text-white hover:bg-white/5']">
                                Dashboard
                            </Link>

                            <template v-if="user.role === 'admin'">
                                <Link :href="route('admin.analytics')" :class="['px-3 py-2 rounded-lg text-sm font-bold transition-all flex items-center gap-2', route().current('admin.analytics') ? 'bg-red-600/20 text-red-300 shadow-[0_0_15px_rgba(220,38,38,0.3)]' : 'text-red-400 hover:text-red-300 hover:bg-red-600/10']">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span>
                                    Command Center
                                </Link>
                                <Link :href="route('admin.documents.index')" :class="['px-3 py-2 rounded-lg text-sm transition-all', route().current('admin.documents.*') ? 'bg-blue-600/20 text-blue-300 shadow-[0_0_15px_rgba(59,130,246,0.3)]' : 'text-slate-400 hover:text-white hover:bg-white/5']">
                                    Approvals
                                </Link>
                            </template>

                            <template v-else>
                                <Link :href="route('documents.index')" :class="['px-3 py-2 rounded-lg text-sm transition-all', route().current('documents.*') ? 'bg-blue-600/20 text-blue-300 shadow-[0_0_15px_rgba(59,130,246,0.3)]' : 'text-slate-400 hover:text-white hover:bg-white/5']">
                                    Documents
                                </Link>
                                <Link :href="route('concerns.create')" :class="['px-3 py-2 rounded-lg text-sm transition-all', route().current('concerns.*') ? 'bg-blue-600/20 text-blue-300 shadow-[0_0_15px_rgba(59,130,246,0.3)]' : 'text-slate-400 hover:text-white hover:bg-white/5']">
                                    Report
                                </Link>
                                <Link :href="route('bills.create')" :class="['px-3 py-2 rounded-lg text-sm transition-all', route().current('bills.*') ? 'bg-blue-600/20 text-blue-300 shadow-[0_0_15px_rgba(59,130,246,0.3)]' : 'text-slate-400 hover:text-white hover:bg-white/5']">
                                    Bills
                                </Link>
                                <Link :href="route('jobs.index')" :class="['px-3 py-2 rounded-lg text-sm transition-all', route().current('jobs.*') ? 'bg-blue-600/20 text-blue-300 shadow-[0_0_15px_rgba(59,130,246,0.3)]' : 'text-slate-400 hover:text-white hover:bg-white/5']">
                                    Jobs
                                </Link>
                            </template>
                        </div>
                    </div>

                    <div class="hidden sm:flex sm:items-center sm:ml-6">
                        <div class="ml-3 relative">
                            <Dropdown align="right" width="48">
                                <template #trigger>
                                    <span class="inline-flex rounded-md">
                                        <button type="button" class="group inline-flex items-center gap-3 px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-full text-slate-300 hover:text-white hover:bg-white/5 focus:outline-none transition ease-in-out duration-150">
                                            <div :class="`w-9 h-9 rounded-full bg-gradient-to-br ${getAvatarGradient(user.name)} flex items-center justify-center text-white font-bold text-sm border border-white/20 shadow-lg group-hover:shadow-[0_0_15px_rgba(255,255,255,0.3)] transition-shadow`">
                                                {{ user.name.charAt(0) }}
                                            </div>
                                            <span class="hidden md:inline font-semibold">{{ user.name }}</span>
                                            <svg class="ml-2 -mr-0.5 h-4 w-4 group-hover:text-white transition-colors" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </span>
                                </template>
                                <template #content>
                                    <div class="bg-slate-800 border border-white/10 rounded-md overflow-hidden shadow-xl">
                                        <DropdownLink :href="route('profile.edit')" class="text-slate-300 hover:bg-slate-700 hover:text-white transition-colors"> Profile </DropdownLink>
                                        <DropdownLink :href="route('logout')" method="post" as="button" class="text-red-400 hover:bg-slate-700 hover:text-red-300 transition-colors"> Log Out </DropdownLink>
                                    </div>
                                </template>
                            </Dropdown>
                        </div>
                    </div>

                    <div class="-mr-2 flex items-center sm:hidden">
                        <button @click="showingNavigationDropdown = !showingNavigationDropdown" class="inline-flex items-center justify-center p-2 rounded-md text-slate-400 hover:text-white hover:bg-white/10 focus:outline-none transition duration-150 ease-in-out">
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path :class="{'hidden': showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                <path :class="{'hidden': !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <div :class="{'block': showingNavigationDropdown, 'hidden': !showingNavigationDropdown}" class="sm:hidden relative bg-slate-900/95 backdrop-blur-xl border-b border-white/10">
                <div class="pt-2 pb-3 space-y-1 px-2">
                    <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')" class="rounded-lg"> Dashboard </ResponsiveNavLink>
                    <template v-if="user.role === 'admin'">
                        <ResponsiveNavLink :href="route('admin.analytics')" :active="route().current('admin.analytics')" class="text-red-400 rounded-lg"> Command Center </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('admin.documents.index')" :active="route().current('admin.documents.*')" class="rounded-lg"> Approvals </ResponsiveNavLink>
                    </template>
                    <template v-else>
                        <ResponsiveNavLink :href="route('documents.index')" class="rounded-lg">Documents</ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('concerns.create')" class="rounded-lg">Report</ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('bills.create')" class="rounded-lg">Bills</ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('jobs.index')" class="rounded-lg">Jobs</ResponsiveNavLink>
                    </template>
                </div>
                <div class="pt-4 pb-1 border-t border-white/10 bg-black/20">
                    <div class="px-4 flex items-center gap-3">
                        <div :class="`w-10 h-10 rounded-full bg-gradient-to-br ${getAvatarGradient(user.name)} flex items-center justify-center text-white font-bold text-sm border border-white/20 shadow-lg`">
                            {{ user.name.charAt(0) }}
                        </div>
                        <div>
                            <div class="font-medium text-base text-white">{{ user.name }}</div>
                            <div class="font-medium text-sm text-slate-500">{{ user.email }}</div>
                        </div>
                    </div>
                    <div class="mt-3 space-y-1 px-2">
                        <ResponsiveNavLink :href="route('profile.edit')" class="rounded-lg"> Profile </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('logout')" method="post" as="button" class="text-red-400 rounded-lg"> Log Out </ResponsiveNavLink>
                    </div>
                </div>
            </div>
        </nav>

        <main class="relative z-0">
                    <Transition name="page" mode="out-in" appear>
                        <div :key="$page.url"> 
                            <slot />
                        </div>
                    </Transition>
                </main>
        <ChatAssistant />
                        <Transition name="fade">
            <div v-if="isLoading" class="fixed inset-0 z-[100] bg-slate-900/60 backdrop-blur-sm flex items-center justify-center pointer-events-none">
                <div class="relative">
                    <div class="w-16 h-16 rounded-full border-4 border-blue-500/30 border-t-blue-500 animate-spin"></div>
                    <div class="absolute top-2 left-2 w-12 h-12 rounded-full border-4 border-purple-500/30 border-b-purple-500 animate-spin-reverse"></div>
                    <div class="absolute top-1/2 left-1/2 w-2 h-2 bg-white rounded-full transform -translate-x-1/2 -translate-y-1/2 animate-pulse"></div>
                </div>
            </div>
        </Transition>
    </div>
    
</template>

<style scoped>
    /* PREMIUM SCALE TRANSITION (Nanotech Vibe) 
   Instead of just fading, the page zooms in/out slightly.
*/

/* Active state for entering/leaving */
.page-scale-enter-active,
.page-scale-leave-active {
    transition: all 0.5s cubic-bezier(0.22, 1, 0.36, 1); /* Custom Bezier for "High Tech" snap */
}

/* Starting state for entering (Zoomed out slightly, blurred) */
.page-scale-enter-from {
    opacity: 0;
    transform: scale(0.95) translateY(10px);
    filter: blur(8px);
}

/* Ending state for leaving (Zoomed in slightly, blurred) */
.page-scale-leave-to {
    opacity: 0;
    transform: scale(1.05);
    filter: blur(8px);
}

/* Normal state */
.page-scale-enter-to,
.page-scale-leave-from {
    opacity: 1;
    transform: scale(1);
    filter: blur(0);
}
    /* SPINNER ANIMATIONS */
@keyframes spin-reverse {
    from { transform: rotate(0deg); }
    to { transform: rotate(-360deg); }
}
.animate-spin-reverse {
    animation: spin-reverse 1s linear infinite;
}

/* OVERLAY TRANSITION */
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
/* PAGE TRANSITIONS (Fade + Slide Up) */
.page-enter-active,
.page-leave-active {
    transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
}

.page-enter-from {
    opacity: 0;
    transform: translateY(20px); /* Slides up from bottom */
    filter: blur(4px); /* Blur effect for glass feel */
}

.page-leave-to {
    opacity: 0;
    transform: translateY(-20px); /* Slides up to disappear */
    filter: blur(4px);
}
</style>