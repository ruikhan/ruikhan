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
const introPhase = ref('initial');
const currentFeature = ref(0);
const skipIntro = ref(false);

// Enhanced Mouse & Scroll Tracking
const mouseX = ref(0);
const mouseY = ref(0);
const scrollY = ref(0);
const scrollProgress = ref(0);

// Features with enhanced data
const cinematicFeatures = [
    {
        icon: '📄',
        title: 'E-Documents',
        subtitle: 'Instant Digital Services',
        description: 'Request barangay clearances and permits with QR verification',
        gradient: 'from-blue-500 via-blue-400 to-cyan-500',
        glow: 'blue',
        particles: '💫'
    },
    {
        icon: '🚨',
        title: 'Rapid Response',
        subtitle: 'Emergency Services',
        description: 'Direct line to emergency with real-time geo-tagging',
        gradient: 'from-red-500 via-orange-500 to-red-400',
        glow: 'red',
        particles: '⚡'
    },
    {
        icon: '💳',
        title: 'Secure Payments',
        subtitle: 'Encrypted Transactions',
        description: 'Pay utilities and taxes through secure virtual gateway',
        gradient: 'from-emerald-500 via-teal-400 to-emerald-500',
        glow: 'emerald',
        particles: '✨'
    },
    {
        icon: '🔒',
        title: 'Data Protection',
        subtitle: 'Military-Grade Security',
        description: 'Your information protected with advanced encryption',
        gradient: 'from-purple-500 via-indigo-400 to-purple-500',
        glow: 'purple',
        particles: '🛡️'
    }
];

// Optimized event handlers with RAF
let mouseRAF = null;
let scrollRAF = null;

const handleMouseMove = (e) => {
    if (mouseRAF) return;
    mouseRAF = requestAnimationFrame(() => {
        mouseX.value = e.clientX;
        mouseY.value = e.clientY;
        mouseRAF = null;
    });
};

const handleScroll = () => {
    if (scrollRAF) return;
    scrollRAF = requestAnimationFrame(() => {
        scrollY.value = window.scrollY;
        const winScroll = document.documentElement.scrollTop;
        const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
        scrollProgress.value = height > 0 ? (winScroll / height) * 100 : 0;
        scrollRAF = null;
    });
};

const handleSkipIntro = () => {
    skipIntro.value = true;
    introPhase.value = 'complete';
    setTimeout(() => {
        showIntro.value = false;
    }, 500);
};

// Enhanced cinematic sequence
const startCinematicSequence = () => {
    setTimeout(() => {
        if (!skipIntro.value) introPhase.value = 'logo';
    }, 500);

    setTimeout(() => {
        if (!skipIntro.value) introPhase.value = 'tagline';
    }, 2500);

    setTimeout(() => {
        if (!skipIntro.value) {
            introPhase.value = 'features';
            startFeatureLoop();
        }
    }, 6000);

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
    window.addEventListener('mousemove', handleMouseMove, { passive: true });
    window.addEventListener('scroll', handleScroll, { passive: true });
    startCinematicSequence();
});

onUnmounted(() => {
    window.removeEventListener('mousemove', handleMouseMove);
    window.removeEventListener('scroll', handleScroll);
    if (mouseRAF) cancelAnimationFrame(mouseRAF);
    if (scrollRAF) cancelAnimationFrame(scrollRAF);
});

const currentFeatureData = computed(() => cinematicFeatures[currentFeature.value]);
const parallaxOffset = computed(() => scrollY.value * 0.5);
</script>

<template>
    <Head title="Welcome to E-PILI" />

    <!-- Enhanced Cinematic Intro -->
    <Transition name="intro-fade">
        <div v-if="showIntro" class="intro-overlay">
            <!-- Dynamic Background -->
            <div class="intro-bg">
                <div class="intro-gradient"></div>
                
                <!-- Enhanced Particles -->
                <div class="intro-particles">
                    <div v-for="i in 60" :key="i" 
                         class="particle"
                         :style="{
                             left: `${Math.random() * 100}%`,
                             top: `${Math.random() * 100}%`,
                             animationDelay: `${Math.random() * 5}s`,
                             animationDuration: `${3 + Math.random() * 4}s`,
                             width: `${1 + Math.random() * 2}px`,
                             height: `${1 + Math.random() * 2}px`
                         }"></div>
                </div>

                <!-- Light Rays -->
                <div class="light-rays">
                    <div v-for="i in 4" :key="i" 
                         class="ray"
                         :style="{
                             left: `${i * 25}%`,
                             animationDelay: `${i * 0.5}s`
                         }"></div>
                </div>
            </div>

            <!-- Skip Button -->
            <button @click="handleSkipIntro" class="skip-btn">
                <span>Skip Intro</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path>
                </svg>
            </button>

            <!-- Content -->
            <div class="intro-content">
                
                <!-- Logo Phase -->
                <Transition name="fade-scale">
                    <div v-if="introPhase === 'initial' || introPhase === 'logo'" class="intro-logo-phase">
                        <div class="logo-container">
                            <div class="logo-glow"></div>
                            <img :src="logoUrl" alt="E-PILI Logo" class="logo-img" />
                            <div class="logo-rings">
                                <div v-for="i in 3" :key="i" class="ring" :style="{ animationDelay: `${i * 0.2}s` }"></div>
                            </div>
                        </div>
                        <Transition name="slide-up">
                            <h1 v-if="introPhase === 'logo'" class="logo-title">E-PILI</h1>
                        </Transition>
                    </div>
                </Transition>

                <!-- Tagline Phase -->
                <Transition name="fade-scale">
                    <div v-if="introPhase === 'tagline'" class="tagline-phase">
                        <h2 class="tagline-main">
                            The Future of
                            <span class="tagline-gradient">
                                Digital Governance
                            </span>
                        </h2>
                        <p class="tagline-sub">
                            Transforming public service through innovation and technology
                        </p>
                        <div class="tagline-line"></div>
                    </div>
                </Transition>

                <!-- Features Phase -->
                <Transition name="fade-scale">
                    <div v-if="introPhase === 'features'" class="features-phase">
                        <TransitionGroup name="feature">
                            <div :key="currentFeature" class="feature-showcase">
                                <!-- Icon with particles -->
                                <div class="feature-icon-wrapper">
                                    <div :class="`feature-glow bg-gradient-to-r ${currentFeatureData.gradient}`"></div>
                                    <div class="feature-icon">
                                        {{ currentFeatureData.icon }}
                                    </div>
                                    <div class="feature-particles">
                                        <span v-for="i in 8" :key="i" 
                                              class="feature-particle"
                                              :style="{ animationDelay: `${i * 0.1}s` }">
                                            {{ currentFeatureData.particles }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Content -->
                                <div class="feature-content">
                                    <p :class="`feature-subtitle text-transparent bg-clip-text bg-gradient-to-r ${currentFeatureData.gradient}`">
                                        {{ currentFeatureData.subtitle }}
                                    </p>
                                    <h3 class="feature-title">
                                        {{ currentFeatureData.title }}
                                    </h3>
                                    <p class="feature-desc">
                                        {{ currentFeatureData.description }}
                                    </p>
                                </div>

                                <!-- Progress -->
                                <div class="feature-progress">
                                    <div v-for="(feature, index) in cinematicFeatures" 
                                         :key="index"
                                         :class="[
                                             'progress-dot',
                                             index === currentFeature && 'active',
                                             `bg-gradient-to-r ${currentFeatureData.gradient}`
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

    <!-- Main Portal -->
    <div class="portal-container">
        
        <!-- Enhanced Background -->
        <div class="portal-bg">
            <div class="bg-base"></div>
            
            <!-- Dynamic Gradients -->
            <div class="bg-gradients">
                <div class="gradient gradient-1" 
                     :style="{ transform: `translate(${mouseX * 0.02}px, ${mouseY * 0.02 - parallaxOffset}px)` }"></div>
                <div class="gradient gradient-2" 
                     :style="{ transform: `translate(${-mouseX * 0.015}px, ${mouseY * 0.015 - parallaxOffset}px)` }"></div>
                <div class="gradient gradient-3" 
                     :style="{ transform: `translate(${mouseX * 0.01}px, ${-mouseY * 0.01 - parallaxOffset}px)` }"></div>
            </div>
            
            <!-- Grid Overlay -->
            <div class="bg-grid"></div>
            
            <!-- Noise Texture -->
            <div class="bg-noise"></div>
        </div>

        <!-- Scroll Progress -->
        <div class="scroll-indicator">
            <div class="scroll-bar" :style="{ width: scrollProgress + '%' }"></div>
        </div>

        <!-- Navigation -->
        <nav class="portal-nav">
            <div class="nav-wrapper">
                <div class="nav-glass"></div>
                <div class="nav-border"></div>
                
                <!-- Logo -->
                <Link :href="route('dashboard')" class="nav-logo-link">
                    <div class="nav-logo-wrapper">
                        <img :src="logoUrl" alt="ENOT PILI Logo" class="nav-logo-img">
                        <div class="nav-logo-pulse"></div>
                    </div>
                    <div class="nav-logo-text">
                        <h5>Justine Villarosa</h5>
                        <p>Developer</p>
                    </div>
                </Link>

                <!-- Auth Buttons -->
                <div v-if="canLogin" class="nav-actions">
                    <Link 
                        v-if="$page.props.auth.user" 
                        :href="route('dashboard')" 
                        class="btn-dashboard"
                    >
                        <span>Dashboard</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </Link>

                    <template v-else>
                        <Link :href="route('login')" class="btn-login">
                            Sign In
                        </Link>
                        <Link v-if="canRegister" :href="route('register')" class="btn-register">
                            Get Started
                        </Link>
                    </template>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <main class="hero-section">
            
            <!-- Status Badge -->
            <div class="status-badge">
                <span class="status-pulse">
                    <span class="pulse-ring"></span>
                    <span class="pulse-dot"></span>
                </span>
                <span class="status-text">System Online</span>
                <span class="status-indicator">●</span>
            </div>

            <!-- Hero Title -->
            <h1 class="hero-title">
                <span class="title-line">Governance</span>
                <span class="title-gradient">E-Portal System</span>
                <div class="title-underline"></div>
            </h1>

            <!-- Description -->
            <p class="hero-desc">
                The future of public service is digital. Request documents, pay bills, 
                and participate in governance through a secure, unified command center.
            </p>

            <!-- CTA Buttons -->
            <div class="hero-cta">
                <Link :href="route('register')" class="cta-primary">
                    <div class="cta-bg"></div>
                    <span>Create Citizen Account</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                    </svg>
                    <div class="cta-shine"></div>
                </Link>
                <Link :href="route('login')" class="cta-secondary">
                    <div class="cta-glass"></div>
                    <span>Access Portal</span>
                    <div class="cta-border"></div>
                </Link>
            </div>

            <!-- Feature Cards -->
            <div class="feature-cards">
                
                <div class="card" data-color="blue">
                    <div class="card-bg"></div>
                    <div class="card-glow"></div>
                    
                    <div class="card-icon">
                        <span>📄</span>
                        <div class="icon-ring"></div>
                    </div>
                    
                    <h3 class="card-title">E-Documents</h3>
                    <p class="card-desc">
                        Instant request processing for Barangay Clearances and Permits with digital QR verification.
                    </p>
                    
                    <div class="card-link">
                        <span>Learn More</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                </div>

                <div class="card" data-color="red">
                    <div class="card-bg"></div>
                    <div class="card-glow"></div>
                    
                    <div class="card-icon">
                        <span>🚨</span>
                        <div class="icon-ring"></div>
                    </div>
                    
                    <h3 class="card-title">Rapid Response</h3>
                    <p class="card-desc">
                        Direct line to emergency services. Report community incidents with geo-tagging.
                    </p>
                    
                    <div class="card-link">
                        <span>Learn More</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                </div>

                <div class="card" data-color="emerald">
                    <div class="card-bg"></div>
                    <div class="card-glow"></div>
                    
                    <div class="card-icon">
                        <span>💳</span>
                        <div class="icon-ring"></div>
                    </div>
                    
                    <h3 class="card-title">Secure Payments</h3>
                    <p class="card-desc">
                        Hassle-free payment for utilities and taxes through our encrypted virtual gateway.
                    </p>
                    
                    <div class="card-link">
                        <span>Learn More</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                </div>

            </div>

        </main>

        <!-- Footer -->
        <footer class="portal-footer">
            <div class="footer-content">
                <div class="footer-copyright">
                    <span>&copy; 2025 Provincial Government of Camarines Sur.</span>
                </div>
                <div class="footer-links">
                    <a href="#" class="footer-link">Privacy</a>
                    <a href="#" class="footer-link">Terms</a>
                    <a href="#" class="footer-link">Support</a>
                </div>
            </div>
        </footer>

    </div>
</template>

<style scoped>
/* ===== PERFORMANCE OPTIMIZATIONS ===== */
* {
    backface-visibility: hidden;
    -webkit-backface-visibility: hidden;
}

/* ===== CINEMATIC INTRO ===== */
.intro-overlay {
    position: fixed;
    inset: 0;
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #000;
    will-change: opacity;
}

.intro-bg {
    position: absolute;
    inset: 0;
    overflow: hidden;
}

.intro-gradient {
    position: absolute;
    inset: 0;
    background: radial-gradient(ellipse at center, #1a1a2e 0%, #0f0f1e 50%, #000 100%);
}

.intro-particles {
    position: absolute;
    inset: 0;
    opacity: 0.4;
}

.particle {
    position: absolute;
    background: white;
    border-radius: 50%;
    animation: particle-float 7s ease-in-out infinite;
    will-change: transform, opacity;
}

@keyframes particle-float {
    0%, 100% { 
        transform: translate(0, 0) scale(1); 
        opacity: 0.2;
    }
    50% { 
        transform: translate(50px, -100px) scale(1.5); 
        opacity: 0.8;
    }
}

.light-rays {
    position: absolute;
    inset: 0;
    opacity: 0.15;
}

.ray {
    position: absolute;
    top: 0;
    width: 2px;
    height: 100%;
    background: linear-gradient(to bottom, rgba(59, 130, 246, 0.6), transparent);
    filter: blur(20px);
    animation: ray-pulse 4s ease-in-out infinite;
}

@keyframes ray-pulse {
    0%, 100% { opacity: 0.3; transform: translateY(0); }
    50% { opacity: 0.8; transform: translateY(20px); }
}

.skip-btn {
    position: absolute;
    top: 2rem;
    right: 2rem;
    z-index: 10;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    background: rgba(255, 255, 255, 0.08);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 9999px;
    color: white;
    font-size: 0.875rem;
    font-weight: 600;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.skip-btn:hover {
    background: rgba(255, 255, 255, 0.15);
    transform: translateX(4px);
    box-shadow: 0 10px 40px rgba(59, 130, 246, 0.3);
}

.intro-content {
    position: relative;
    z-index: 10;
    width: 100%;
    max-width: 1200px;
    padding: 0 2rem;
}

/* Logo Phase */
.intro-logo-phase {
    text-align: center;
}

.logo-container {
    position: relative;
    display: inline-block;
    margin-bottom: 3rem;
}

.logo-glow {
    position: absolute;
    inset: -3rem;
    background: radial-gradient(circle, rgba(59, 130, 246, 0.4), transparent 70%);
    animation: glow-pulse 3s ease-in-out infinite;
    filter: blur(60px);
}

@keyframes glow-pulse {
    0%, 100% { opacity: 0.4; transform: scale(1); }
    50% { opacity: 0.8; transform: scale(1.2); }
}

.logo-img {
    position: relative;
    height: 16rem;
    width: auto;
    filter: drop-shadow(0 20px 60px rgba(59, 130, 246, 0.6));
    animation: logo-float 4s ease-in-out infinite;
}

@keyframes logo-float {
    0%, 100% { transform: translateY(0) rotate(0deg); }
    50% { transform: translateY(-20px) rotate(2deg); }
}

.logo-rings {
    position: absolute;
    inset: -2rem;
}

.ring {
    position: absolute;
    inset: 0;
    border: 2px solid rgba(59, 130, 246, 0.3);
    border-radius: 50%;
    animation: ring-expand 3s ease-out infinite;
}

@keyframes ring-expand {
    0% { transform: scale(0.8); opacity: 0.8; }
    100% { transform: scale(1.5); opacity: 0; }
}

.logo-title {
    font-size: clamp(3rem, 8vw, 6rem);
    font-weight: 900;
    color: white;
    letter-spacing: -0.02em;
    text-shadow: 0 0 60px rgba(59, 130, 246, 0.8);
}

/* Tagline Phase */
.tagline-phase {
    text-align: center;
}

.tagline-main {
    font-size: clamp(2.5rem, 6vw, 5rem);
    font-weight: 900;
    color: white;
    line-height: 1.2;
    margin-bottom: 1.5rem;
}

.tagline-gradient {
    display: block;
    margin-top: 0.5rem;
    background: linear-gradient(135deg, #60a5fa, #a78bfa, #06b6d4);
    background-size: 200% auto;
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
    animation: gradient-flow 8s linear infinite;
}

@keyframes gradient-flow {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

.tagline-sub {
    font-size: clamp(1.125rem, 2vw, 1.5rem);
    color: rgb(156, 163, 175);
    font-weight: 300;
    margin-bottom: 2rem;
}

.tagline-line {
    width: 8rem;
    height: 3px;
    margin: 0 auto;
    background: linear-gradient(to right, transparent, #60a5fa, transparent);
    animation: line-glow 2s ease-in-out infinite;
}

@keyframes line-glow {
    0%, 100% { opacity: 0.5; transform: scaleX(1); }
    50% { opacity: 1; transform: scaleX(1.2); }
}

/* Features Phase */
.features-phase {
    text-align: center;
}

.feature-showcase {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 2rem;
}

.feature-icon-wrapper {
    position: relative;
    width: 12rem;
    height: 12rem;
}

.feature-glow {
    position: absolute;
    inset: -3rem;
    border-radius: 50%;
    opacity: 0.4;
    filter: blur(80px);
    animation: feature-glow 3s ease-in-out infinite;
}

@keyframes feature-glow {
    0%, 100% { opacity: 0.4; transform: scale(1); }
    50% { opacity: 0.7; transform: scale(1.3); }
}

.feature-icon {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 6rem;
    filter: drop-shadow(0 10px 40px rgba(59, 130, 246, 0.6));
    animation: icon-bounce 2s ease-in-out infinite;
}

@keyframes icon-bounce {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-20px); }
}

.feature-particles {
    position: absolute;
    inset: 0;
}

.feature-particle {
    position: absolute;
    top: 50%;
    left: 50%;
    font-size: 1.5rem;
    opacity: 0;
    animation: particle-burst 2s ease-out infinite;
}

.feature-particle:nth-child(1) { transform: rotate(0deg); }
.feature-particle:nth-child(2) { transform: rotate(45deg); }
.feature-particle:nth-child(3) { transform: rotate(90deg); }
.feature-particle:nth-child(4) { transform: rotate(135deg); }
.feature-particle:nth-child(5) { transform: rotate(180deg); }
.feature-particle:nth-child(6) { transform: rotate(225deg); }
.feature-particle:nth-child(7) { transform: rotate(270deg); }
.feature-particle:nth-child(8) { transform: rotate(315deg); }

@keyframes particle-burst {
    0% { 
        transform: translate(-50%, -50%) translateY(0);
        opacity: 0;
    }
    50% { 
        opacity: 1;
    }
    100% { 
        transform: translate(-50%, -50%) translateY(-100px);
        opacity: 0;
    }
}

.feature-content {
    max-width: 48rem;
}

.feature-subtitle {
    font-size: 0.875rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.2em;
    margin-bottom: 1rem;
}

.feature-title {
    font-size: clamp(2.5rem, 5vw, 4rem);
    font-weight: 900;
    color: white;
    margin-bottom: 1rem;
}

.feature-desc {
    font-size: clamp(1.125rem, 2vw, 1.5rem);
    color: rgb(156, 163, 175);
    font-weight: 300;
}

.feature-progress {
    display: flex;
    gap: 0.75rem;
    padding-top: 2rem;
}

.progress-dot {
    width: 0.5rem;
    height: 0.5rem;
    border-radius: 9999px;
    background: rgba(255, 255, 255, 0.2);
    transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}

.progress-dot.active {
    width: 3rem;
}

/* Intro Transitions */
.intro-fade-enter-active,
.intro-fade-leave-active {
    transition: opacity 1s ease;
}

.intro-fade-enter-from,
.intro-fade-leave-to {
    opacity: 0;
}

.fade-scale-enter-active,
.fade-scale-leave-active {
    transition: all 1s cubic-bezier(0.16, 1, 0.3, 1);
}

.fade-scale-enter-from {
    opacity: 0;
    transform: scale(0.8);
}

.fade-scale-leave-to {
    opacity: 0;
    transform: scale(1.2);
}

.slide-up-enter-active {
    transition: all 1s cubic-bezier(0.16, 1, 0.3, 1);
    transition-delay: 0.5s;
}

.slide-up-enter-from {
    opacity: 0;
    transform: translateY(40px);
}

.feature-enter-active,
.feature-leave-active {
    transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1);
}

.feature-enter-from {
    opacity: 0;
    transform: translateY(50px) scale(0.9);
}

.feature-leave-to {
    opacity: 0;
    transform: translateY(-50px) scale(1.1);
}

/* ===== PORTAL CONTAINER ===== */
.portal-container {
    position: relative;
    min-height: 100vh;
    background: #000;
    color: #f8fafc;
    font-family: -apple-system, BlinkMacSystemFont, 'SF Pro Display', 'Segoe UI', sans-serif;
    -webkit-font-smoothing: antialiased;
    overflow-x: hidden;
}

/* Enhanced Background */
.portal-bg {
    position: fixed;
    inset: 0;
    z-index: 1;
    overflow: hidden;
}

.bg-base {
    position: absolute;
    inset: 0;
    background: radial-gradient(ellipse at top, #0a0a0f 0%, #050507 50%, #000 100%);
}

.bg-gradients {
    position: absolute;
    inset: 0;
}

.gradient {
    position: absolute;
    border-radius: 50%;
    filter: blur(140px);
    will-change: transform;
    animation: gradient-float 30s ease-in-out infinite;
}

.gradient-1 {
    top: 10%;
    left: 15%;
    width: 600px;
    height: 600px;
    background: radial-gradient(circle, rgba(59, 130, 246, 0.15), transparent 70%);
}

.gradient-2 {
    bottom: 10%;
    right: 15%;
    width: 700px;
    height: 700px;
    background: radial-gradient(circle, rgba(168, 85, 247, 0.12), transparent 70%);
    animation-delay: 5s;
}

.gradient-3 {
    top: 50%;
    left: 50%;
    width: 500px;
    height: 500px;
    background: radial-gradient(circle, rgba(6, 182, 212, 0.1), transparent 70%);
    animation-delay: 10s;
}

@keyframes gradient-float {
    0%, 100% { transform: translate(0, 0); }
    33% { transform: translate(50px, -50px); }
    66% { transform: translate(-50px, 50px); }
}

.bg-grid {
    position: absolute;
    inset: 0;
    background-image: 
        linear-gradient(rgba(59, 130, 246, 0.03) 1px, transparent 1px),
        linear-gradient(90deg, rgba(59, 130, 246, 0.03) 1px, transparent 1px);
    background-size: 50px 50px;
    mask-image: radial-gradient(ellipse at center, black 20%, transparent 80%);
}

.bg-noise {
    position: absolute;
    inset: 0;
    background-image: url('data:image/svg+xml,%3Csvg viewBox="0 0 400 400" xmlns="http://www.w3.org/2000/svg"%3E%3Cfilter id="n"%3E%3CfeTurbulence type="fractalNoise" baseFrequency="3.5" numOctaves="4"/%3E%3C/filter%3E%3Crect width="100%25" height="100%25" filter="url(%23n)" opacity="0.05"/%3E%3C/svg%3E');
    opacity: 0.03;
    mix-blend-mode: overlay;
}

/* Scroll Indicator */
.scroll-indicator {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    z-index: 100;
    background: rgba(0, 0, 0, 0.3);
    backdrop-filter: blur(10px);
}

.scroll-bar {
    height: 100%;
    background: linear-gradient(to right, #3b82f6, #8b5cf6, #ec4899);
    box-shadow: 0 0 20px rgba(59, 130, 246, 0.6);
    transition: width 0.1s ease-out;
}

/* Navigation */
.portal-nav {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 50;
    padding: 1.5rem;
}

.nav-wrapper {
    position: relative;
    max-width: 1280px;
    margin: 0 auto;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 1.5rem;
    border-radius: 9999px;
    background: rgba(10, 10, 15, 0.8);
    border: 1px solid rgba(255, 255, 255, 0.08);
    backdrop-filter: blur(40px);
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.8);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.nav-wrapper:hover {
    border-color: rgba(255, 255, 255, 0.15);
    box-shadow: 0 24px 80px rgba(0, 0, 0, 0.9);
}

.nav-glass {
    position: absolute;
    inset: 0;
    border-radius: 9999px;
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.08), rgba(255, 255, 255, 0.02));
}

.nav-border {
    position: absolute;
    inset: -1px;
    border-radius: 9999px;
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.3), rgba(168, 85, 247, 0.2));
    filter: blur(8px);
    opacity: 0;
    transition: opacity 0.3s;
    z-index: -1;
}

.nav-wrapper:hover .nav-border {
    opacity: 1;
}

.nav-logo-link {
    position: relative;
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: transform 0.3s;
}

.nav-logo-link:hover {
    transform: scale(1.05);
}

.nav-logo-wrapper {
    position: relative;
    width: 4.5rem;
    height: 4.5rem;
}

.nav-logo-img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    filter: drop-shadow(0 10px 30px rgba(59, 130, 246, 0.4));
    transition: filter 0.3s;
}

.nav-logo-link:hover .nav-logo-img {
    filter: drop-shadow(0 15px 40px rgba(59, 130, 246, 0.6));
}

.nav-logo-pulse {
    position: absolute;
    inset: -0.5rem;
    border: 2px solid rgba(59, 130, 246, 0.3);
    border-radius: 9999px;
    opacity: 0;
    animation: pulse-ring 2s ease-out infinite;
}

.nav-logo-link:hover .nav-logo-pulse {
    opacity: 1;
}

@keyframes pulse-ring {
    0% { transform: scale(0.8); opacity: 0.8; }
    100% { transform: scale(1.3); opacity: 0; }
}

.nav-logo-text {
    display: none;
}

@media (min-width: 640px) {
    .nav-logo-text {
        display: block;
    }
    
    .nav-logo-text h5 {
        font-size: 0.875rem;
        font-weight: 700;
        color: white;
        transition: color 0.3s;
    }
    
    .nav-logo-text p {
        font-size: 0.75rem;
        color: #60a5fa;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        font-weight: 700;
    }
    
    .nav-logo-link:hover .nav-logo-text h5 {
        color: #60a5fa;
    }
}

.nav-actions {
    position: relative;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.btn-dashboard,
.btn-login,
.btn-register {
    position: relative;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.625rem 1.25rem;
    border-radius: 9999px;
    font-size: 0.875rem;
    font-weight: 600;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    overflow: hidden;
}

.btn-dashboard {
    background: linear-gradient(135deg, #3b82f6, #8b5cf6);
    color: white;
    box-shadow: 0 10px 30px rgba(59, 130, 246, 0.4);
}

.btn-dashboard:hover {
    transform: translateY(-2px);
    box-shadow: 0 15px 40px rgba(59, 130, 246, 0.6);
}

.btn-login {
    color: rgb(203, 213, 225);
    background: rgba(255, 255, 255, 0.05);
}

.btn-login:hover {
    color: white;
    background: rgba(255, 255, 255, 0.1);
    transform: translateY(-2px);
}

.btn-register {
    background: white;
    color: black;
    box-shadow: 0 10px 30px rgba(255, 255, 255, 0.2);
}

.btn-register:hover {
    transform: translateY(-2px);
    box-shadow: 0 15px 40px rgba(255, 255, 255, 0.3);
}

/* Hero Section */
.hero-section {
    position: relative;
    z-index: 10;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 8rem 1.5rem 4rem;
    text-align: center;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 1.5rem;
    margin-bottom: 2rem;
    background: rgba(59, 130, 246, 0.08);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(59, 130, 246, 0.2);
    border-radius: 9999px;
    box-shadow: 0 10px 40px rgba(59, 130, 246, 0.2);
    animation: badge-float 3s ease-in-out infinite;
}

@keyframes badge-float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}

.status-pulse {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
}

.pulse-ring {
    position: absolute;
    width: 1rem;
    height: 1rem;
    border-radius: 50%;
    background: #60a5fa;
    animation: ping 2s cubic-bezier(0, 0, 0.2, 1) infinite;
}

@keyframes ping {
    0% { transform: scale(0.8); opacity: 1; }
    80%, 100% { transform: scale(2.5); opacity: 0; }
}

.pulse-dot {
    position: relative;
    width: 0.5rem;
    height: 0.5rem;
    border-radius: 50%;
    background: #60a5fa;
    box-shadow: 0 0 15px rgba(96, 165, 250, 0.8);
}

.status-text {
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.15em;
    color: #93c5fd;
}

.status-indicator {
    color: #22c55e;
    font-size: 0.875rem;
}

.hero-title {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    margin-bottom: 2rem;
}

.title-line {
    font-size: clamp(3rem, 8vw, 7rem);
    font-weight: 900;
    color: white;
    letter-spacing: -0.03em;
    line-height: 1;
}

.title-gradient {
    font-size: clamp(3rem, 8vw, 7rem);
    font-weight: 900;
    background: linear-gradient(135deg, #60a5fa, #a78bfa, #06b6d4);
    background-size: 200% auto;
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
    letter-spacing: -0.03em;
    line-height: 1;
    animation: gradient-flow 8s linear infinite;
}

.title-underline {
    width: 8rem;
    height: 4px;
    margin: 1rem auto 0;
    background: linear-gradient(to right, transparent, #60a5fa, transparent);
    border-radius: 9999px;
    animation: underline-glow 2s ease-in-out infinite;
}

@keyframes underline-glow {
    0%, 100% { opacity: 0.5; transform: scaleX(1); }
    50% { opacity: 1; transform: scaleX(1.2); }
}

.hero-desc {
    max-width: 48rem;
    font-size: clamp(1rem, 2vw, 1.25rem);
    color: rgb(156, 163, 175);
    font-weight: 300;
    line-height: 1.7;
    margin-bottom: 3rem;
}

.hero-cta {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    justify-content: center;
    margin-bottom: 5rem;
}

.cta-primary,
.cta-secondary {
    position: relative;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem 2rem;
    border-radius: 9999px;
    font-size: 1.125rem;
    font-weight: 700;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    overflow: hidden;
}

.cta-primary {
    background: linear-gradient(135deg, #3b82f6, #8b5cf6);
    color: white;
    box-shadow: 0 20px 60px rgba(59, 130, 246, 0.4);
}

.cta-primary:hover {
    transform: translateY(-4px);
    box-shadow: 0 25px 80px rgba(59, 130, 246, 0.6);
}

.cta-bg {
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, #6366f1, #ec4899);
    opacity: 0;
    transition: opacity 0.3s;
}

.cta-primary:hover .cta-bg {
    opacity: 1;
}

.cta-shine {
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    transition: left 0.6s;
}

.cta-primary:hover .cta-shine {
    left: 100%;
}

.cta-secondary {
    background: rgba(255, 255, 255, 0.05);
    color: white;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.cta-glass {
    position: absolute;
    inset: 0;
    backdrop-filter: blur(20px);
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.05));
    opacity: 0;
    transition: opacity 0.3s;
}

.cta-secondary:hover .cta-glass {
    opacity: 1;
}

.cta-border {
    position: absolute;
    inset: -2px;
    border-radius: 9999px;
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.5), rgba(168, 85, 247, 0.5));
    filter: blur(8px);
    opacity: 0;
    transition: opacity 0.3s;
    z-index: -1;
}

.cta-secondary:hover {
    transform: translateY(-4px);
    border-color: rgba(255, 255, 255, 0.2);
}

.cta-secondary:hover .cta-border {
    opacity: 1;
}

/* Feature Cards */
.feature-cards {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1.5rem;
    max-width: 1200px;
    width: 100%;
}

@media (min-width: 768px) {
    .feature-cards {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (min-width: 1024px) {
    .feature-cards {
        grid-template-columns: repeat(3, 1fr);
    }
}

.card {
    position: relative;
    padding: 2rem;
    border-radius: 2rem;
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.08);
    backdrop-filter: blur(20px);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    overflow: hidden;
}

.card:hover {
    transform: translateY(-8px);
    border-color: rgba(255, 255, 255, 0.15);
}

.card-bg {
    position: absolute;
    inset: 0;
    opacity: 0;
    transition: opacity 0.4s;
}

.card[data-color="blue"] .card-bg {
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), transparent);
}

.card[data-color="red"] .card-bg {
    background: linear-gradient(135deg, rgba(239, 68, 68, 0.1), transparent);
}

.card[data-color="emerald"] .card-bg {
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), transparent);
}

.card:hover .card-bg {
    opacity: 1;
}

.card-glow {
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    border-radius: 50%;
    opacity: 0;
    filter: blur(80px);
    transition: opacity 0.4s;
}

.card[data-color="blue"] .card-glow {
    background: radial-gradient(circle, rgba(59, 130, 246, 0.3), transparent 70%);
}

.card[data-color="red"] .card-glow {
    background: radial-gradient(circle, rgba(239, 68, 68, 0.3), transparent 70%);
}

.card[data-color="emerald"] .card-glow {
    background: radial-gradient(circle, rgba(16, 185, 129, 0.3), transparent 70%);
}

.card:hover .card-glow {
    opacity: 1;
}

.card-icon {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 4rem;
    height: 4rem;
    margin-bottom: 1.5rem;
    border-radius: 1.25rem;
    font-size: 2rem;
    transition: transform 0.4s;
}

.card[data-color="blue"] .card-icon {
    background: rgba(59, 130, 246, 0.15);
    box-shadow: 0 10px 40px rgba(59, 130, 246, 0.2);
}

.card[data-color="red"] .card-icon {
    background: rgba(239, 68, 68, 0.15);
    box-shadow: 0 10px 40px rgba(239, 68, 68, 0.2);
}

.card[data-color="emerald"] .card-icon {
    background: rgba(16, 185, 129, 0.15);
    box-shadow: 0 10px 40px rgba(16, 185, 129, 0.2);
}

.card:hover .card-icon {
    transform: scale(1.1) rotate(5deg);
}

.icon-ring {
    position: absolute;
    inset: -0.5rem;
    border: 2px solid currentColor;
    border-radius: 1.5rem;
    opacity: 0;
    animation: ring-pulse 2s ease-out infinite;
}

.card[data-color="blue"] .icon-ring {
    border-color: rgba(59, 130, 246, 0.5);
}

.card[data-color="red"] .icon-ring {
    border-color: rgba(239, 68, 68, 0.5);
}

.card[data-color="emerald"] .icon-ring {
    border-color: rgba(16, 185, 129, 0.5);
}

.card:hover .icon-ring {
    opacity: 1;
}

@keyframes ring-pulse {
    0% { transform: scale(0.9); opacity: 0.8; }
    100% { transform: scale(1.3); opacity: 0; }
}

.card-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: white;
    margin-bottom: 0.75rem;
    transition: color 0.3s;
}

.card[data-color="blue"]:hover .card-title {
    color: #60a5fa;
}

.card[data-color="red"]:hover .card-title {
    color: #f87171;
}

.card[data-color="emerald"]:hover .card-title {
    color: #34d399;
}

.card-desc {
    font-size: 0.9375rem;
    color: rgb(156, 163, 175);
    line-height: 1.6;
    margin-bottom: 1.5rem;
}

.card-link {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    font-weight: 600;
    opacity: 0;
    transform: translateY(10px);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.card[data-color="blue"] .card-link {
    color: #60a5fa;
}

.card[data-color="red"] .card-link {
    color: #f87171;
}

.card[data-color="emerald"] .card-link {
    color: #34d399;
}

.card:hover .card-link {
    opacity: 1;
    transform: translateY(0);
}

.card-link svg {
    transition: transform 0.3s;
}

.card-link:hover svg {
    transform: translateX(4px);
}

/* Footer */
.portal-footer {
    position: relative;
    z-index: 10;
    padding: 3rem 1.5rem;
    border-top: 1px solid rgba(255, 255, 255, 0.08);
    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(20px);
}

.footer-content {
    max-width: 1200px;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
    align-items: center;
}

@media (min-width: 768px) {
    .footer-content {
        flex-direction: row;
        justify-content: space-between;
    }
}

.footer-copyright {
    font-size: 0.875rem;
    color: rgb(100, 116, 139);
}

.footer-links {
    display: flex;
    gap: 2rem;
}

.footer-link {
    position: relative;
    font-size: 0.875rem;
    font-weight: 600;
    color: rgb(148, 163, 184);
    transition: color 0.3s;
}

.footer-link::after {
    content: '';
    position: absolute;
    bottom: -4px;
    left: 0;
    width: 0;
    height: 2px;
    background: linear-gradient(to right, #60a5fa, #a78bfa);
    transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.footer-link:hover {
    color: white;
}

.footer-link:hover::after {
    width: 100%;
}

/* Responsive Adjustments */
@media (max-width: 640px) {
    .skip-btn {
        top: 1rem;
        right: 1rem;
        padding: 0.5rem 1rem;
        font-size: 0.75rem;
    }
    
    .nav-wrapper {
        padding: 0.75rem 1rem;
    }
    
    .nav-logo-wrapper {
        width: 3.5rem;
        height: 3.5rem;
    }
    
    .btn-dashboard,
    .btn-login,
    .btn-register {
        padding: 0.5rem 1rem;
        font-size: 0.8125rem;
    }
    
    .hero-section {
        padding: 6rem 1rem 3rem;
    }
    
    .hero-cta {
        flex-direction: column;
        width: 100%;
    }
    
    .cta-primary,
    .cta-secondary {
        width: 100%;
        justify-content: center;
        padding: 0.875rem 1.5rem;
        font-size: 1rem;
    }
}

/* Performance: Reduce motion for accessibility */
@media (prefers-reduced-motion: reduce) {
    *,
    *::before,
    *::after {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
}

/* Print styles */
@media print {
    .intro-overlay,
    .portal-nav,
    .scroll-indicator {
        display: none !important;
    }
    
    .portal-container {
        background: white;
        color: black;
    }
}
</style>