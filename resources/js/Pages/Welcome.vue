<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { computed, onMounted, onUnmounted, ref } from 'vue';

const logoUrl = '/images/enotpili.png'; 
defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
});

// Cinematic Intro State
const showIntro = ref(true);
const introPhase = ref('initial'); // initial -> logo -> tagline -> features -> fadeout -> complete
const currentFeature = ref(0);
const skipIntro = ref(false);

// Mouse tracking
const mouseX = ref(0);
const mouseY = ref(0);
const scrollY = ref(0);

// Features for cinematic showcase
const cinematicFeatures = [
    {
        icon: '📄',
        title: 'E-Documents',
        subtitle: 'Instant Digital Services',
        description: 'Request barangay clearances and permits with QR verification',
        gradient: 'from-blue-500 to-cyan-500',
        glow: 'blue'
    },
    {
        icon: '🚨',
        title: 'Rapid Response',
        subtitle: 'Emergency Services',
        description: 'Direct line to emergency with real-time geo-tagging',
        gradient: 'from-red-500 to-orange-500',
        glow: 'red'
    },
    {
        icon: '💳',
        title: 'Secure Payments',
        subtitle: 'Encrypted Transactions',
        description: 'Pay utilities and taxes through secure virtual gateway',
        gradient: 'from-emerald-500 to-teal-500',
        glow: 'emerald'
    },
    {
        icon: '🔒',
        title: 'Data Protection',
        subtitle: 'Military-Grade Security',
        description: 'Your information protected with advanced encryption',
        gradient: 'from-purple-500 to-indigo-500',
        glow: 'purple'
    }
];

const handleMouseMove = (e) => {
    mouseX.value = e.clientX;
    mouseY.value = e.clientY;
};

const handleScroll = () => {
    scrollY.value = window.scrollY;
};

const handleSkipIntro = () => {
    skipIntro.value = true;
    introPhase.value = 'complete';
    setTimeout(() => {
        showIntro.value = false;
    }, 500);
};

// Cinematic sequence timing
const startCinematicSequence = () => {
    // Phase 1: Logo reveal (2s)
    setTimeout(() => {
        if (!skipIntro.value) introPhase.value = 'logo';
    }, 500);

    // Phase 2: Tagline (3.5s)
    setTimeout(() => {
        if (!skipIntro.value) introPhase.value = 'tagline';
    }, 2500);

    // Phase 3: Features showcase (12s total, 3s each)
    setTimeout(() => {
        if (!skipIntro.value) {
            introPhase.value = 'features';
            startFeatureLoop();
        }
    }, 6000);

    // Phase 4: Fade out and complete (18s)
    setTimeout(() => {
        if (!skipIntro.value) {
            introPhase.value = 'fadeout';
            setTimeout(() => {
                showIntro.value = false;
            }, 1000);
        }
    }, 18000);
};

const startFeatureLoop = () => {
    const interval = setInterval(() => {
        if (skipIntro.value || introPhase.value !== 'features') {
            clearInterval(interval);
            return;
        }
        currentFeature.value = (currentFeature.value + 1) % cinematicFeatures.length;
    }, 3000);
};

onMounted(() => {
    window.addEventListener('mousemove', handleMouseMove);
    window.addEventListener('scroll', handleScroll);
    
    // Start cinematic sequence
    startCinematicSequence();
});

onUnmounted(() => {
    window.removeEventListener('mousemove', handleMouseMove);
    window.removeEventListener('scroll', handleScroll);
});

const currentFeatureData = computed(() => cinematicFeatures[currentFeature.value]);
</script>

<template>
    <Head title="Welcome to E-PILI" />

    <!-- Cinematic Introduction Overlay -->
    <Transition name="intro-fade">
        <div v-if="showIntro" class="fixed inset-0 z-[9999] bg-black">
            <!-- Cinematic Background -->
            <div class="absolute inset-0 overflow-hidden">
                <div class="absolute inset-0 bg-gradient-radial from-gray-900 via-black to-black"></div>
                
                <!-- Animated particles -->
                <div class="absolute inset-0 opacity-30">
                    <div v-for="i in 50" :key="i" 
                         class="absolute w-1 h-1 bg-white rounded-full cinematic-particle"
                         :style="{
                             left: `${Math.random() * 100}%`,
                             top: `${Math.random() * 100}%`,
                             animationDelay: `${Math.random() * 5}s`,
                             animationDuration: `${3 + Math.random() * 4}s`
                         }"></div>
                </div>

                <!-- Cinematic light rays -->
                <div class="absolute inset-0 opacity-20">
                    <div class="absolute top-0 left-1/4 w-1 h-full bg-gradient-to-b from-blue-500/50 to-transparent rotate-12 blur-xl"></div>
                    <div class="absolute top-0 right-1/4 w-1 h-full bg-gradient-to-b from-purple-500/50 to-transparent -rotate-12 blur-xl"></div>
                </div>
            </div>

            <!-- Skip Button -->
            <button 
                @click="handleSkipIntro"
                class="absolute top-8 right-8 z-50 px-6 py-3 bg-white/10 backdrop-blur-md border border-white/20 rounded-full text-white text-sm font-semibold hover:bg-white/20 transition-all duration-300 flex items-center gap-2 hover:scale-105"
            >
                <span>Skip</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path>
                </svg>
            </button>

            <!-- Content Container -->
            <div class="relative z-10 h-full flex items-center justify-center">
                
                <!-- Phase 1: Initial/Logo Reveal -->
                <Transition name="fade-scale">
                    <div v-if="introPhase === 'initial' || introPhase === 'logo'" 
                         class="text-center">
                        <div class="relative inline-block">
                            <div class="absolute inset-0 bg-blue-500/30 blur-[100px] rounded-full animate-pulse-glow"></div>
                            <img :src="logoUrl" 
                                 alt="E-PILI Logo" 
                                 class="h-48 md:h-64 relative z-10 drop-shadow-[0_0_50px_rgba(59,130,246,0.5)] animate-float-slow" />
                        </div>
                        <Transition name="slide-up-fade">
                            <h1 v-if="introPhase === 'logo'" 
                                class="mt-12 text-4xl md:text-6xl font-black text-white tracking-tight">
                                E-PILI
                            </h1>
                        </Transition>
                    </div>
                </Transition>

                <!-- Phase 2: Tagline -->
                <Transition name="fade-scale">
                    <div v-if="introPhase === 'tagline'" class="text-center max-w-4xl px-8">
                        <h2 class="text-5xl md:text-7xl font-black text-white mb-6 leading-tight">
                            The Future of
                            <span class="block mt-2 text-transparent bg-clip-text bg-gradient-to-r from-blue-400 via-cyan-400 to-indigo-400 animate-gradient-shift">
                                Digital Governance
                            </span>
                        </h2>
                        <p class="text-xl md:text-2xl text-gray-400 font-light animate-fade-in-delayed">
                            Transforming public service through innovation and technology
                        </p>
                    </div>
                </Transition>

                <!-- Phase 3: Features Showcase -->
                <Transition name="fade-scale">
                    <div v-if="introPhase === 'features'" class="text-center max-w-5xl px-8">
                        <TransitionGroup name="feature-transition">
                            <div :key="currentFeature" class="space-y-8">
                                <!-- Feature Icon -->
                                <div class="relative inline-block">
                                    <div :class="`absolute inset-0 bg-gradient-to-r ${currentFeatureData.gradient} blur-[80px] opacity-50 rounded-full animate-pulse-glow`"></div>
                                    <div :class="`relative text-8xl md:text-9xl filter drop-shadow-[0_0_30px_rgba(59,130,246,0.6)] animate-feature-float`">
                                        {{ currentFeatureData.icon }}
                                    </div>
                                </div>

                                <!-- Feature Title -->
                                <div>
                                    <p :class="`text-sm md:text-base font-bold uppercase tracking-[0.3em] text-transparent bg-clip-text bg-gradient-to-r ${currentFeatureData.gradient} mb-3`">
                                        {{ currentFeatureData.subtitle }}
                                    </p>
                                    <h3 class="text-5xl md:text-7xl font-black text-white mb-6">
                                        {{ currentFeatureData.title }}
                                    </h3>
                                    <p class="text-xl md:text-2xl text-gray-400 font-light max-w-2xl mx-auto">
                                        {{ currentFeatureData.description }}
                                    </p>
                                </div>

                                <!-- Progress Dots -->
                                <div class="flex justify-center gap-3 pt-8">
                                    <div v-for="(feature, index) in cinematicFeatures" 
                                         :key="index"
                                         :class="[
                                             'w-2 h-2 rounded-full transition-all duration-500',
                                             index === currentFeature 
                                                 ? `bg-gradient-to-r ${currentFeatureData.gradient} w-12` 
                                                 : 'bg-white/20'
                                         ]">
                                    </div>
                                </div>
                            </div>
                        </TransitionGroup>
                    </div>
                </Transition>

            </div>
        </div>
    </Transition>

    <!-- Main Portal (existing content) -->
    <div class="min-h-screen bg-black text-white font-sans selection:bg-cyan-500/30 selection:text-cyan-200 overflow-x-hidden relative">
        
        <!-- Enhanced Animated Background with Mouse Tracking -->
        <div class="fixed inset-0 z-0 pointer-events-none overflow-hidden">
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-[#0f172a] via-[#020617] to-black"></div>
            
            <!-- Dynamic Gradient Mesh -->
            <div class="absolute inset-0 opacity-30">
                <div class="absolute top-0 left-0 w-[500px] h-[500px] bg-blue-500/20 rounded-full blur-[120px] animate-float" 
                     :style="{ transform: `translate(${mouseX * 0.02}px, ${mouseY * 0.02}px)` }"></div>
                <div class="absolute top-1/4 right-1/4 w-[400px] h-[400px] bg-purple-500/20 rounded-full blur-[100px] animate-float-delayed" 
                     :style="{ transform: `translate(${-mouseX * 0.015}px, ${mouseY * 0.015}px)` }"></div>
                <div class="absolute bottom-1/4 left-1/3 w-[450px] h-[450px] bg-cyan-500/15 rounded-full blur-[110px] animate-pulse-slow" 
                     :style="{ transform: `translate(${mouseX * 0.01}px, ${-mouseY * 0.01}px)` }"></div>
            </div>
            
            <!-- Starfield Effect -->
            <div class="absolute inset-0 opacity-40 bg-[url('https://www.transparenttextures.com/patterns/stardust.png')] animate-starfield"></div>

            <!-- Floating Particles -->
            <div class="absolute inset-0">
                <div v-for="i in 20" :key="i" 
                     class="particle absolute w-1 h-1 bg-white/20 rounded-full"
                     :style="{
                         left: `${Math.random() * 100}%`,
                         top: `${Math.random() * 100}%`,
                         animationDelay: `${Math.random() * 5}s`,
                         animationDuration: `${5 + Math.random() * 10}s`
                     }"></div>
            </div>
        </div>

        <!-- Navigation with Glass Effect -->
        <nav class="relative z-50 px-6 py-6 max-w-7xl mx-auto">
            <div class="flex justify-between items-center backdrop-blur-xl bg-white/5 rounded-3xl px-6 py-4 border border-white/10 shadow-2xl nav-glow">
                <div class="flex items-center gap-4 group cursor-default">
                    <div class="leading-tight hidden sm:block">
                        <h5 class="text-[13px] font-bold tracking-tight text-white group-hover:text-blue-300 transition-all duration-300">Justine Villarosa</h5>
                        <p class="text-[9px] text-blue-400 uppercase tracking-[0.2em] font-bold">Developer</p>
                    </div>
                    <div class="relative">
                        <div class="absolute inset-0 bg-blue-500 blur-2xl opacity-0 group-hover:opacity-60 transition-all duration-700 rounded-full"></div>
                        <img :src="logoUrl" alt="ENOT PILI Logo" class="h-28 relative z-10 drop-shadow-2xl transform group-hover:scale-110 group-hover:rotate-3 transition-all duration-500"></img>
                    </div>
                </div>

                <div v-if="canLogin" class="flex gap-4">
                    <Link 
                        v-if="$page.props.auth.user" 
                        :href="route('dashboard')" 
                        class="premium-button group"
                    >
                        <span class="relative z-10">Enter Command Center</span>
                        <svg class="w-4 h-4 relative z-10 group-hover:translate-x-2 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                        <div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-indigo-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-full"></div>
                    </Link>

                    <template v-else>
                        <Link 
                            :href="route('login')" 
                            class="px-6 py-2.5 text-slate-300 hover:text-white font-medium text-sm transition-all duration-300 hover:scale-105 relative group"
                        >
                            <span class="relative z-10">Sign In</span>
                            <div class="absolute inset-0 bg-white/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-full"></div>
                        </Link>

                        <Link 
                            v-if="canRegister" 
                            :href="route('register')" 
                            class="premium-cta-button group"
                        >
                            <span class="relative z-10">Get Started</span>
                            <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-20 transition-opacity duration-300 rounded-full"></div>
                        </Link>
                    </template>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="relative z-10 flex flex-col items-center justify-center text-center px-4 mt-16 md:mt-24 mb-32">
            
            <!-- Status Badge with Enhanced Animation -->
            <div class="status-badge group">
                <span class="relative flex h-2 w-2">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-500 shadow-lg shadow-blue-500/50"></span>
                </span>
                <span class="text-blue-300 text-[10px] font-bold tracking-[0.25em] uppercase">Online Portal System</span>
                <div class="absolute inset-0 bg-blue-500/10 rounded-full blur-xl opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
            </div>

            <!-- Hero Title with Enhanced Typography -->
            <h1 class="hero-title">
                Governance <br class="hidden md:block" />
                <span class="hero-gradient">
                    E-Portal System
                </span>
            </h1>

            <!-- Description with Better Readability -->
            <p class="hero-description">
                The future of public service is digital. Request documents, pay bills, and participate in governance through a secure, unified command center.
            </p>

            <!-- CTA Buttons with Premium Effects -->
            <div class="flex flex-col sm:flex-row gap-4 animate-slide-up" style="animation-delay: 0.2s;">
                <Link 
                    :href="route('register')" 
                    class="cta-primary group"
                >
                    <div class="absolute inset-0 bg-gradient-to-r from-blue-600 via-blue-500 to-blue-600 opacity-100 group-hover:opacity-0 transition-opacity duration-300 rounded-full"></div>
                    <div class="absolute inset-0 bg-gradient-to-r from-cyan-500 via-blue-500 to-indigo-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-full"></div>
                    <span class="relative z-10">Create Citizen Account</span>
                    <svg class="w-5 h-5 relative z-10 group-hover:translate-x-2 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                </Link>
                <Link 
                    :href="route('login')" 
                    class="cta-secondary group"
                >
                    <div class="absolute inset-0 bg-white/5 group-hover:bg-white/10 transition-colors duration-300 rounded-full"></div>
                    <span class="relative z-10">Access Portal</span>
                </Link>
            </div>

            <!-- Feature Cards with Premium Design -->
            <div class="mt-32 grid grid-cols-1 md:grid-cols-3 gap-6 w-full max-w-6xl animate-fade-in-up" style="animation-delay: 0.4s;">
                
                <div class="feature-card group">
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-500/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 rounded-[2rem]"></div>
                    <div class="absolute -right-10 -top-10 w-40 h-40 bg-blue-500/20 rounded-full blur-[60px] group-hover:bg-blue-500/40 transition-all duration-500"></div>
                    
                    <div class="feature-icon bg-blue-500/20 text-blue-400 shadow-blue-500/30 group-hover:shadow-blue-500/50">
                        📄
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-white group-hover:text-blue-300 transition-colors duration-300">E-Documents</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">Instant request processing for Barangay Clearances and Permits with digital QR verification.</p>
                    
                    <div class="mt-6 flex items-center gap-2 text-blue-400 opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-y-2 group-hover:translate-y-0">
                        <span class="text-xs font-semibold">Learn More</span>
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </div>
                </div>

                <div class="feature-card group">
                    <div class="absolute inset-0 bg-gradient-to-br from-red-500/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 rounded-[2rem]"></div>
                    <div class="absolute -right-10 -top-10 w-40 h-40 bg-red-500/20 rounded-full blur-[60px] group-hover:bg-red-500/40 transition-all duration-500"></div>
                    
                    <div class="feature-icon bg-red-500/20 text-red-400 shadow-red-500/30 group-hover:shadow-red-500/50">
                        🚨
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-white group-hover:text-red-300 transition-colors duration-300">Rapid Response</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">Direct line to emergency services. Report community incidents with geo-tagging.</p>
                    
                    <div class="mt-6 flex items-center gap-2 text-red-400 opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-y-2 group-hover:translate-y-0">
                        <span class="text-xs font-semibold">Learn More</span>
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </div>
                </div>

                <div class="feature-card group">
                    <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 rounded-[2rem]"></div>
                    <div class="absolute -right-10 -top-10 w-40 h-40 bg-emerald-500/20 rounded-full blur-[60px] group-hover:bg-emerald-500/40 transition-all duration-500"></div>
                    
                    <div class="feature-icon bg-emerald-500/20 text-emerald-400 shadow-emerald-500/30 group-hover:shadow-emerald-500/50">
                        💳
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-white group-hover:text-emerald-300 transition-colors duration-300">Secure Payments</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">Hassle-free payment for utilities and taxes through our encrypted virtual gateway.</p>
                    
                    <div class="mt-6 flex items-center gap-2 text-emerald-400 opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-y-2 group-hover:translate-y-0">
                        <span class="text-xs font-semibold">Learn More</span>
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </div>
                </div>

            </div>

        </main>

        <!-- Enhanced Footer -->
        <footer class="relative z-10 border-t border-white/10 bg-black/80 backdrop-blur-xl">
            <div class="max-w-7xl mx-auto px-6 py-12 flex flex-col md:flex-row justify-between items-center gap-6">
                <div class="text-slate-500 text-sm font-medium">
                    &copy; 2025 Provincial Government of Camarines Sur.
                </div>
                <div class="flex gap-8 text-sm font-medium">
                    <a href="#" class="text-slate-500 hover:text-white transition-all duration-300 relative group">
                        <span class="relative z-10">Privacy</span>
                        <span class="absolute inset-x-0 bottom-0 h-0.5 bg-gradient-to-r from-blue-500 to-cyan-500 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300"></span>
                    </a>
                    <a href="#" class="text-slate-500 hover:text-white transition-all duration-300 relative group">
                        <span class="relative z-10">Terms</span>
                        <span class="absolute inset-x-0 bottom-0 h-0.5 bg-gradient-to-r from-blue-500 to-cyan-500 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300"></span>
                    </a>
                    <a href="#" class="text-slate-500 hover:text-white transition-all duration-300 relative group">
                        <span class="relative z-10">Support</span>
                        <span class="absolute inset-x-0 bottom-0 h-0.5 bg-gradient-to-r from-blue-500 to-cyan-500 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300"></span>
                    </a>
                </div>
            </div>
        </footer>

    </div>
</template>

<style scoped>
/* ===== CINEMATIC INTRO STYLES ===== */

/* Intro Transitions */
.intro-fade-enter-active,
.intro-fade-leave-active {
    transition: opacity 1s ease;
}

.intro-fade-enter-from,
.intro-fade-leave-to {
    opacity: 0;
}

.fade-scale-enter-active {
    transition: all 1s cubic-bezier(0.16, 1, 0.3, 1);
}

.fade-scale-leave-active {
    transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1);
}

.fade-scale-enter-from {
    opacity: 0;
    transform: scale(0.8);
}

.fade-scale-leave-to {
    opacity: 0;
    transform: scale(1.2);
}

.slide-up-fade-enter-active {
    transition: all 1s cubic-bezier(0.16, 1, 0.3, 1);
    transition-delay: 0.5s;
}

.slide-up-fade-enter-from {
    opacity: 0;
    transform: translateY(40px);
}

.fade-in-delayed {
    animation: fadeIn 1s ease-out 1s both;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

/* Feature Transition */
.feature-transition-enter-active {
    transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1);
}

.feature-transition-leave-active {
    transition: all 0.6s cubic-bezier(0.16, 1, 0.3, 1);
}

.feature-transition-enter-from {
    opacity: 0;
    transform: translateY(50px) scale(0.9);
}

.feature-transition-leave-to {
    opacity: 0;
    transform: translateY(-50px) scale(1.1);
}

/* Cinematic Animations */
@keyframes float-slow {
    0%, 100% { transform: translateY(0) rotate(0deg); }
    50% { transform: translateY(-30px) rotate(5deg); }
}

.animate-float-slow {
    animation: float-slow 6s ease-in-out infinite;
}

@keyframes feature-float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-20px); }
}

.animate-feature-float {
    animation: feature-float 3s ease-in-out infinite;
}

@keyframes pulse-glow {
    0%, 100% { opacity: 0.3; transform: scale(1); }
    50% { opacity: 0.6; transform: scale(1.2); }
}

.animate-pulse-glow {
    animation: pulse-glow 3s ease-in-out infinite;
}

.cinematic-particle {
    animation: particle-drift 7s ease-in-out infinite;
}

@keyframes particle-drift {
    0%, 100% { 
        transform: translate(0, 0); 
        opacity: 0.1;
    }
    50% { 
        transform: translate(50px, -100px); 
        opacity: 0.6;
    }
}

@keyframes gradient-shift {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

.animate-gradient-shift {
    background-size: 200% auto;
    animation: gradient-shift 5s ease infinite;
}

/* ===== EXISTING PORTAL STYLES ===== */

/* Premium Navigation Glow */
.nav-glow {
    box-shadow: 0 0 40px rgba(59, 130, 246, 0.1);
    transition: all 0.3s ease;
}

.nav-glow:hover {
    box-shadow: 0 0 60px rgba(59, 130, 246, 0.2);
}

/* Status Badge */
.status-badge {
    @apply inline-flex items-center gap-2 px-5 py-2 rounded-full;
    @apply bg-blue-500/10 border border-blue-500/30 backdrop-blur-md mb-8;
    @apply animate-fade-in shadow-lg shadow-blue-500/20;
    @apply relative transition-all duration-300 hover:scale-105;
}

/* Hero Title */
.hero-title {
    @apply text-5xl md:text-8xl font-black tracking-tighter mb-6 max-w-5xl leading-tight;
    @apply animate-slide-up drop-shadow-2xl;
}

.hero-gradient {
    @apply text-transparent bg-clip-text bg-gradient-to-r from-blue-400 via-indigo-400 to-cyan-400;
    background-size: 200% auto;
    animation: gradient-shift 8s ease infinite;
}

/* Hero Description */
.hero-description {
    @apply text-lg md:text-xl text-slate-400 max-w-2xl mb-12 leading-relaxed;
    @apply animate-slide-up font-light;
    animation-delay: 0.1s;
}

/* Premium Buttons */
.premium-button {
    @apply relative px-6 py-2.5 bg-white/10 backdrop-blur-md border border-white/10;
    @apply rounded-full font-bold text-sm transition-all flex items-center gap-2;
    @apply hover:scale-105 hover:shadow-xl hover:shadow-blue-500/20 overflow-hidden;
}

.premium-cta-button {
    @apply relative px-6 py-2.5 bg-white text-black rounded-full font-bold text-sm;
    @apply shadow-[0_0_30px_rgba(255,255,255,0.3)] transition-all;
    @apply hover:scale-110 hover:shadow-[0_0_40px_rgba(255,255,255,0.5)] overflow-hidden;
}

/* CTA Buttons */
.cta-primary {
    @apply relative px-8 py-4 text-white rounded-full font-bold text-lg;
    @apply shadow-[0_0_40px_rgba(37,99,235,0.5)] transition-all;
    @apply hover:-translate-y-2 hover:shadow-[0_0_60px_rgba(37,99,235,0.7)];
    @apply flex items-center justify-center gap-2 overflow-hidden;
}

.cta-secondary {
    @apply relative px-8 py-4 backdrop-blur-xl border border-white/10 text-white rounded-full;
    @apply font-bold text-lg transition-all overflow-hidden;
    @apply hover:-translate-y-2 hover:shadow-[0_0_30px_rgba(255,255,255,0.2)];
}

/* Feature Cards */
.feature-card {
    @apply relative bg-white/5 backdrop-blur-xl border border-white/10;
    @apply p-8 rounded-[2rem] transition-all duration-500;
    @apply hover:bg-white/10 hover:-translate-y-3 hover:shadow-2xl overflow-hidden;
}

.feature-icon {
    @apply text-4xl mb-6 w-16 h-16 rounded-2xl flex items-center justify-center;
    @apply group-hover:scale-110 transition-all duration-500 shadow-lg;
}

/* Floating Animations */
@keyframes float {
    0%, 100% { transform: translateY(0) rotate(0deg); }
    50% { transform: translateY(-20px) rotate(5deg); }
}
.animate-float { animation: float 8s ease-in-out infinite; }

@keyframes float-delayed {
    0%, 100% { transform: translateY(0) rotate(0deg); }
    50% { transform: translateY(-30px) rotate(-5deg); }
}
.animate-float-delayed { animation: float-delayed 10s ease-in-out infinite; }

/* Pulse Animation */
@keyframes pulse-slow {
    0%, 100% { opacity: 0.3; transform: scale(1); }
    50% { opacity: 0.6; transform: scale(1.1); }
}
.animate-pulse-slow { animation: pulse-slow 8s ease-in-out infinite; }

/* Fade In Animations */
@keyframes slide-up {
    from { opacity: 0; transform: translateY(40px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-slide-up { animation: slide-up 1s cubic-bezier(0.16, 1, 0.3, 1) both; }

.animate-fade-in { animation: slide-up 1s ease-out; }
.animate-fade-in-up { animation: slide-up 1s ease-out both; }

/* Starfield Animation */
@keyframes starfield {
    0% { transform: translateY(0); }
    100% { transform: translateY(-100px); }
}
.animate-starfield { animation: starfield 200s linear infinite; }

/* Particle Animation */
.particle {
    animation: particle-float 15s ease-in-out infinite;
}

@keyframes particle-float {
    0%, 100% { 
        transform: translate(0, 0) scale(1); 
        opacity: 0.2;
    }
    50% { 
        transform: translate(50px, -50px) scale(1.5); 
        opacity: 0.8;
    }
}

/* Smooth Scrolling */
html {
    scroll-behavior: smooth;
}
</style>