<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { onMounted, onUnmounted, ref } from 'vue';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};

// Enhanced interaction states
const mouseX = ref(0);
const mouseY = ref(0);
const showPassword = ref(false);
const emailFocused = ref(false);
const passwordFocused = ref(false);

// Optimized mouse tracking with RAF
let mouseRAF = null;
const handleMouseMove = (e) => {
    if (mouseRAF) return;
    mouseRAF = requestAnimationFrame(() => {
        mouseX.value = e.clientX;
        mouseY.value = e.clientY;
        mouseRAF = null;
    });
};

onMounted(() => {
    window.addEventListener('mousemove', handleMouseMove, { passive: true });
});

onUnmounted(() => {
    window.removeEventListener('mousemove', handleMouseMove);
    if (mouseRAF) cancelAnimationFrame(mouseRAF);
});
</script>

<template>
    <Head title="Sign In - E-PILI Portal" />

    <div class="login-container">
        
        <!-- Enhanced Background Layer -->
        <div class="bg-layer">
            <!-- Base gradient -->
            <div class="bg-base"></div>
            
            <!-- Animated gradients -->
            <div class="bg-gradients">
                <div class="gradient gradient-1" 
                     :style="{ transform: `translate(${mouseX * 0.02}px, ${mouseY * 0.02}px)` }"></div>
                <div class="gradient gradient-2" 
                     :style="{ transform: `translate(${-mouseX * 0.015}px, ${-mouseY * 0.015}px)` }"></div>
                <div class="gradient gradient-3" 
                     :style="{ transform: `translate(${mouseX * 0.01}px, ${-mouseY * 0.01}px)` }"></div>
            </div>
            
            <!-- Grid overlay -->
            <div class="bg-grid"></div>
            
            <!-- Noise texture -->
            <div class="bg-noise"></div>
            
            <!-- Animated waves -->
            <div class="bg-waves">
                <svg class="wave" viewBox="0 0 1600 200" preserveAspectRatio="none">
                    <path d="M0,100 C400,150 800,50 1600,100 L1600,0 L0,0 Z" fill="url(#waveGrad1)">
                        <animate attributeName="d" dur="10s" repeatCount="indefinite"
                                 values="M0,100 C400,150 800,50 1600,100 L1600,0 L0,0 Z;
                                         M0,80 C400,120 800,80 1600,120 L1600,0 L0,0 Z;
                                         M0,100 C400,150 800,50 1600,100 L1600,0 L0,0 Z" />
                    </path>
                    <defs>
                        <linearGradient id="waveGrad1" x1="0%" y1="0%" x2="100%" y2="0%">
                            <stop offset="0%" style="stop-color:#3b82f6;stop-opacity:0" />
                            <stop offset="50%" style="stop-color:#3b82f6;stop-opacity:0.3" />
                            <stop offset="100%" style="stop-color:#3b82f6;stop-opacity:0" />
                        </linearGradient>
                    </defs>
                </svg>
            </div>
        </div>

        <!-- Main Content -->
        <div class="content-wrapper">
            
            <!-- Login Card -->
            <div class="login-card">
                
                <!-- Left Panel - Info Section -->
                <div class="info-panel">
                    <div class="panel-bg"></div>
                    <div class="panel-pattern"></div>
                    <div class="panel-glow"></div>
                    
                    <div class="panel-content">
                        <!-- Status Badge -->
                        <div class="status-badge">
                            <span class="status-pulse">
                                <span class="pulse-ring"></span>
                                <span class="pulse-dot"></span>
                            </span>
                            <span class="status-text">Secure Portal</span>
                        </div>

                        <!-- Title -->
                        <div class="panel-title">
                            <h1>
                                Welcome to
                                <span class="title-gradient">E-PILI Portal</span>
                            </h1>
                            <p>Your gateway to digital governance</p>
                        </div>

                        <!-- Feature Cards -->
                        <div class="feature-list">
                            <div class="feature-item" data-delay="0.1">
                                <div class="feature-icon blue">
                                    <span>🏛️</span>
                                    <div class="icon-ring"></div>
                                </div>
                                <div class="feature-text">
                                    <h3>Provincial Capital</h3>
                                    <p>Governance center of CamSur</p>
                                </div>
                            </div>

                            <div class="feature-item" data-delay="0.2">
                                <div class="feature-icon emerald">
                                    <span>🌾</span>
                                    <div class="icon-ring"></div>
                                </div>
                                <div class="feature-text">
                                    <h3>Agricultural Hub</h3>
                                    <p>Premier rice & pili production</p>
                                </div>
                            </div>

                            <div class="feature-item" data-delay="0.3">
                                <div class="feature-icon purple">
                                    <span>🔒</span>
                                    <div class="icon-ring"></div>
                                </div>
                                <div class="feature-text">
                                    <h3>Secure Platform</h3>
                                    <p>Military-grade encryption</p>
                                </div>
                            </div>
                        </div>

                        <!-- Promo Banner -->
                        <div class="promo-banner">
                            <div class="banner-shine"></div>
                            <div class="banner-content">
                                <h3>E-Governance Live</h3>
                                <p>Request documents online 24/7</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Panel - Form Section -->
                <div class="form-panel">
                    
                    <!-- Mobile Logo -->
                    <div class="mobile-header">
                        <div class="mobile-logo">
                            <span>🏛️</span>
                        </div>
                        <h2>E-PILI Portal</h2>
                    </div>

                    <!-- Form Header -->
                    <div class="form-header">
                        <h3>Welcome Back</h3>
                        <p>Enter your credentials to access your account</p>
                    </div>

                    <!-- Status Message -->
                    <div v-if="status" class="status-message">
                        <span class="status-icon">✓</span>
                        <span>{{ status }}</span>
                    </div>

                    <!-- Login Form -->
                    <form @submit.prevent="submit" class="login-form">
                        
                        <!-- Email Input -->
                        <div class="input-group">
                            <label class="input-label">Email Address</label>
                            <div class="input-wrapper" :class="{ focused: emailFocused }">
                                <div class="input-bg"></div>
                                <span class="input-icon">📧</span>
                                <input 
                                    type="email" 
                                    v-model="form.email" 
                                    @focus="emailFocused = true"
                                    @blur="emailFocused = false"
                                    placeholder="juan@example.com"
                                    required
                                    class="input-field" 
                                />
                                <div class="input-border"></div>
                            </div>
                        </div>

                        <!-- Password Input -->
                        <div class="input-group">
                            <label class="input-label">Password</label>
                            <div class="input-wrapper" :class="{ focused: passwordFocused }">
                                <div class="input-bg"></div>
                                <span class="input-icon">🔒</span>
                                <input 
                                    :type="showPassword ? 'text' : 'password'" 
                                    v-model="form.password" 
                                    @focus="passwordFocused = true"
                                    @blur="passwordFocused = false"
                                    placeholder="••••••••"
                                    required
                                    class="input-field" 
                                />
                                <button 
                                    type="button"
                                    @click="showPassword = !showPassword"
                                    class="toggle-password"
                                >
                                    <span v-if="!showPassword">👁️</span>
                                    <span v-else>🙈</span>
                                </button>
                                <div class="input-border"></div>
                            </div>
                        </div>

                        <!-- Remember & Forgot -->
                        <div class="form-options">
                            <label class="checkbox-label">
                                <input type="checkbox" v-model="form.remember" class="checkbox-input">
                                <span class="checkbox-box">
                                    <svg class="checkbox-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                </span>
                                <span class="checkbox-text">Remember me</span>
                            </label>

                            <Link 
                                v-if="canResetPassword" 
                                :href="route('password.request')" 
                                class="forgot-link"
                            >
                                Forgot Password?
                            </Link>
                        </div>

                        <!-- Submit Button -->
                        <button 
                            type="submit" 
                            :disabled="form.processing" 
                            class="submit-btn"
                        >
                            <div class="btn-bg"></div>
                            <div class="btn-shine"></div>
                            <div class="btn-content">
                                <span v-if="!form.processing">Sign In</span>
                                <span v-else class="btn-loading">
                                    <svg class="spinner" viewBox="0 0 24 24">
                                        <circle class="spinner-track" cx="12" cy="12" r="10" stroke-width="4"></circle>
                                        <path class="spinner-fill" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Signing In...
                                </span>
                                <svg class="btn-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                </svg>
                            </div>
                        </button>
                    </form>

                    <!-- Divider -->
                    <div class="divider">
                        <div class="divider-line"></div>
                        <span class="divider-text">Or continue with</span>
                        <div class="divider-line"></div>
                    </div>

                    <!-- Social Login -->
                    <div class="social-login">
                        <button class="social-btn">
                            <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google">
                            <span>Google</span>
                        </button>
                        <button class="social-btn">
                            <img src="https://www.svgrepo.com/show/475647/facebook-color.svg" alt="Facebook">
                            <span>Facebook</span>
                        </button>
                    </div>

                    <!-- Register Link -->
                    <div class="register-link">
                        <p>
                            Don't have an account? 
                            <Link :href="route('register')" class="link">
                                Create one now
                            </Link>
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>

<style scoped>
/* ===== BASE SETUP ===== */
* {
    backface-visibility: hidden;
    -webkit-backface-visibility: hidden;
}

.login-container {
    position: relative;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1rem;
    background: #000;
    color: #f8fafc;
    font-family: -apple-system, BlinkMacSystemFont, 'SF Pro Display', 'Inter', 'Segoe UI', sans-serif;
    -webkit-font-smoothing: antialiased;
    overflow: hidden;
}

/* ===== BACKGROUND LAYER ===== */
.bg-layer {
    position: fixed;
    inset: 0;
    z-index: 1;
    overflow: hidden;
}

.bg-base {
    position: absolute;
    inset: 0;
    background: radial-gradient(ellipse at top, #0f172a 0%, #020617 50%, #000 100%);
}

.bg-gradients {
    position: absolute;
    inset: 0;
    opacity: 0.6;
}

.gradient {
    position: absolute;
    border-radius: 50%;
    filter: blur(140px);
    will-change: transform;
    animation: float-gradient 30s ease-in-out infinite;
}

.gradient-1 {
    top: 20%;
    left: 20%;
    width: 500px;
    height: 500px;
    background: radial-gradient(circle, rgba(59, 130, 246, 0.2), transparent 70%);
}

.gradient-2 {
    bottom: 20%;
    right: 20%;
    width: 600px;
    height: 600px;
    background: radial-gradient(circle, rgba(168, 85, 247, 0.15), transparent 70%);
    animation-delay: 5s;
}

.gradient-3 {
    top: 50%;
    left: 50%;
    width: 450px;
    height: 450px;
    background: radial-gradient(circle, rgba(6, 182, 212, 0.12), transparent 70%);
    animation-delay: 10s;
}

@keyframes float-gradient {
    0%, 100% { transform: translate(0, 0); }
    33% { transform: translate(40px, -40px); }
    66% { transform: translate(-40px, 40px); }
}

.bg-grid {
    position: absolute;
    inset: 0;
    background-image: 
        linear-gradient(rgba(59, 130, 246, 0.03) 1px, transparent 1px),
        linear-gradient(90deg, rgba(59, 130, 246, 0.03) 1px, transparent 1px);
    background-size: 50px 50px;
    mask-image: radial-gradient(ellipse at center, black 30%, transparent 80%);
}

.bg-noise {
    position: absolute;
    inset: 0;
    background-image: url('data:image/svg+xml,%3Csvg viewBox="0 0 400 400" xmlns="http://www.w3.org/2000/svg"%3E%3Cfilter id="n"%3E%3CfeTurbulence type="fractalNoise" baseFrequency="3.5" numOctaves="4"/%3E%3C/filter%3E%3Crect width="100%25" height="100%25" filter="url(%23n)" opacity="0.05"/%3E%3C/svg%3E');
    opacity: 0.03;
    mix-blend-mode: overlay;
}

.bg-waves {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 60vh;
    opacity: 0.25;
    overflow: hidden;
}

.wave {
    position: absolute;
    width: 200%;
    height: 100%;
    animation: wave-drift 30s linear infinite;
}

@keyframes wave-drift {
    0% { transform: translateX(0); }
    100% { transform: translateX(-50%); }
}

/* ===== CONTENT WRAPPER ===== */
.content-wrapper {
    position: relative;
    z-index: 10;
    width: 100%;
    max-width: 1100px;
}

/* ===== LOGIN CARD ===== */
.login-card {
    position: relative;
    display: grid;
    grid-template-columns: 1fr;
    background: rgba(10, 10, 15, 0.7);
    backdrop-filter: blur(40px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 2.5rem;
    overflow: hidden;
    box-shadow: 
        0 20px 80px rgba(0, 0, 0, 0.8),
        0 0 100px rgba(59, 130, 246, 0.1);
    animation: card-appear 0.8s cubic-bezier(0.16, 1, 0.3, 1);
}

@media (min-width: 768px) {
    .login-card {
        grid-template-columns: 5fr 7fr;
    }
}

@keyframes card-appear {
    from { opacity: 0; transform: scale(0.95) translateY(20px); }
    to { opacity: 1; transform: scale(1) translateY(0); }
}

/* ===== INFO PANEL ===== */
.info-panel {
    position: relative;
    display: none;
    padding: 3rem;
    overflow: hidden;
}

@media (min-width: 768px) {
    .info-panel {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
}

.panel-bg {
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.05), transparent);
    border-right: 1px solid rgba(255, 255, 255, 0.05);
}

.panel-pattern {
    position: absolute;
    inset: 0;
    background-image: url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="0.02"%3E%3Cpath d="M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');
    opacity: 0.1;
}

.panel-glow {
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(59, 130, 246, 0.1), transparent 70%);
    animation: glow-rotate 20s linear infinite;
    filter: blur(60px);
}

@keyframes glow-rotate {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.panel-content {
    position: relative;
    z-index: 10;
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

/* Status Badge */
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 1.25rem;
    background: rgba(59, 130, 246, 0.1);
    border: 1px solid rgba(59, 130, 246, 0.2);
    border-radius: 9999px;
    backdrop-filter: blur(20px);
    width: fit-content;
    animation: badge-float 3s ease-in-out infinite;
}

@keyframes badge-float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-8px); }
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
    font-size: 0.6875rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.15em;
    color: #93c5fd;
}

/* Panel Title */
.panel-title {
    animation: slide-in-left 0.8s cubic-bezier(0.16, 1, 0.3, 1);
}

@keyframes slide-in-left {
    from { opacity: 0; transform: translateX(-30px); }
    to { opacity: 1; transform: translateX(0); }
}

.panel-title h1 {
    font-size: 2rem;
    font-weight: 800;
    line-height: 1.2;
    margin-bottom: 0.75rem;
    color: white;
}

.title-gradient {
    display: block;
    background: linear-gradient(135deg, #60a5fa, #a78bfa);
    background-size: 200% auto;
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
    animation: gradient-flow 5s linear infinite;
}

@keyframes gradient-flow {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

.panel-title p {
    font-size: 0.875rem;
    color: rgb(148, 163, 184);
    font-weight: 400;
}

/* Feature List */
.feature-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.feature-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: rgba(0, 0, 0, 0.3);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 1.25rem;
    backdrop-filter: blur(20px);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    animation: slide-in-up 0.8s cubic-bezier(0.16, 1, 0.3, 1);
    animation-fill-mode: both;
}

.feature-item[data-delay="0.1"] { animation-delay: 0.1s; }
.feature-item[data-delay="0.2"] { animation-delay: 0.2s; }
.feature-item[data-delay="0.3"] { animation-delay: 0.3s; }

@keyframes slide-in-up {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.feature-item:hover {
    background: rgba(255, 255, 255, 0.05);
    border-color: rgba(255, 255, 255, 0.15);
    transform: translateX(4px);
}

.feature-icon {
    position: relative;
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 0.875rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    flex-shrink: 0;
    transition: transform 0.3s;
}

.feature-item:hover .feature-icon {
    transform: scale(1.1) rotate(5deg);
}

.feature-icon.blue {
    background: rgba(59, 130, 246, 0.15);
    box-shadow: 0 8px 30px rgba(59, 130, 246, 0.2);
}

.feature-icon.emerald {
    background: rgba(16, 185, 129, 0.15);
    box-shadow: 0 8px 30px rgba(16, 185, 129, 0.2);
}

.feature-icon.purple {
    background: rgba(168, 85, 247, 0.15);
    box-shadow: 0 8px 30px rgba(168, 85, 247, 0.2);
}

.icon-ring {
    position: absolute;
    inset: -0.375rem;
    border: 2px solid currentColor;
    border-radius: 1.125rem;
    opacity: 0;
    animation: ring-pulse 2s ease-out infinite;
}

@keyframes ring-pulse {
    0% { transform: scale(0.9); opacity: 0.8; }
    100% { transform: scale(1.3); opacity: 0; }
}

.feature-item:hover .icon-ring {
    opacity: 0.5;
}

.feature-text h3 {
    font-size: 0.9375rem;
    font-weight: 600;
    color: white;
    margin-bottom: 0.25rem;
}

.feature-text p {
    font-size: 0.75rem;
    color: rgb(148, 163, 184);
}

/* Promo Banner */
.promo-banner {
    position: relative;
    padding: 1.5rem;
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.15), rgba(99, 102, 241, 0.15));
    border: 1px solid rgba(59, 130, 246, 0.3);
    border-radius: 1.5rem;
    overflow: hidden;
    animation: slide-in-up 0.8s cubic-bezier(0.16, 1, 0.3, 1) 0.4s both;
    transition: transform 0.3s;
}

.promo-banner:hover {
    transform: scale(1.02);
}

.banner-shine {
    position: absolute;
    inset: 0;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
    animation: shine-sweep 3s ease-in-out infinite;
}

@keyframes shine-sweep {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}

.banner-content h3 {
    font-size: 0.9375rem;
    font-weight: 700;
    color: white;
    margin-bottom: 0.375rem;
}

.banner-content p {
    font-size: 0.75rem;
    color: rgb(147, 197, 253);
}

/* ===== FORM PANEL ===== */
.form-panel {
    padding: 2rem;
    position: relative;
}

@media (min-width: 640px) {
    .form-panel {
        padding: 3rem;
    }
}

@media (min-width: 768px) {
    .form-panel {
        padding: 3.5rem;
    }
}

/* Mobile Header */
.mobile-header {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
    margin-bottom: 2rem;
    animation: fade-in 0.8s cubic-bezier(0.16, 1, 0.3, 1);
}

@media (min-width: 768px) {
    .mobile-header {
        display: none;
    }
}

.mobile-logo {
    width: 4rem;
    height: 4rem;
    border-radius: 1.5rem;
    background: linear-gradient(135deg, #3b82f6, #6366f1);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    box-shadow: 0 10px 40px rgba(59, 130, 246, 0.4);
    transition: transform 0.3s;
}

.mobile-logo:hover {
    transform: scale(1.1);
}

.mobile-header h2 {
    font-size: 1.5rem;
    font-weight: 700;
    color: white;
}

/* Form Header */
.form-header {
    margin-bottom: 2rem;
    text-align: center;
    animation: fade-in 0.8s cubic-bezier(0.16, 1, 0.3, 1) 0.1s both;
}

@media (min-width: 768px) {
    .form-header {
        text-align: left;
    }
}

.form-header h3 {
    font-size: 2rem;
    font-weight: 700;
    color: white;
    margin-bottom: 0.5rem;
}

.form-header p {
    font-size: 0.9375rem;
    color: rgb(148, 163, 184);
}

@keyframes fade-in {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Status Message */
.status-message {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem;
    margin-bottom: 1.5rem;
    background: rgba(34, 197, 94, 0.1);
    border: 1px solid rgba(34, 197, 94, 0.3);
    border-radius: 1rem;
    color: rgb(134, 239, 172);
    font-size: 0.875rem;
    animation: slide-in-down 0.5s cubic-bezier(0.16, 1, 0.3, 1);
}

@keyframes slide-in-down {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.status-icon {
    width: 1.25rem;
    height: 1.25rem;
    border-radius: 50%;
    background: rgb(34, 197, 94);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.75rem;
    flex-shrink: 0;
}

/* Login Form */
.login-form {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
    animation: fade-in 0.8s cubic-bezier(0.16, 1, 0.3, 1) 0.2s both;
}

/* Input Group */
.input-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.input-label {
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: rgb(148, 163, 184);
    margin-left: 0.25rem;
}

.input-wrapper {
    position: relative;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.input-bg {
    position: absolute;
    inset: 0;
    background: rgba(255, 255, 255, 0.03);
    border-radius: 1rem;
    transition: all 0.3s;
}

.input-wrapper:hover .input-bg {
    background: rgba(255, 255, 255, 0.05);
}

.input-wrapper.focused .input-bg {
    background: rgba(59, 130, 246, 0.05);
}

.input-icon {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    font-size: 1.125rem;
    color: rgb(100, 116, 139);
    transition: all 0.3s;
    pointer-events: none;
    z-index: 10;
}

.input-wrapper.focused .input-icon {
    color: #60a5fa;
    transform: translateY(-50%) scale(1.1);
}

.input-field {
    position: relative;
    z-index: 5;
    width: 100%;
    padding: 1rem 1rem 1rem 3rem;
    background: transparent;
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 1rem;
    color: white;
    font-size: 0.9375rem;
    outline: none;
    transition: all 0.3s;
}

.input-field::placeholder {
    color: rgb(100, 116, 139);
}

.input-field:hover {
    border-color: rgba(255, 255, 255, 0.2);
}

.input-field:focus {
    border-color: transparent;
}

.input-border {
    position: absolute;
    inset: -1px;
    border-radius: 1rem;
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.5), rgba(99, 102, 241, 0.5));
    opacity: 0;
    transition: opacity 0.3s;
    pointer-events: none;
    z-index: 1;
}

.input-wrapper.focused .input-border {
    opacity: 1;
}

.toggle-password {
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translateY(-50%);
    z-index: 10;
    font-size: 1.125rem;
    color: rgb(100, 116, 139);
    transition: all 0.3s;
}

.toggle-password:hover {
    color: #60a5fa;
    transform: translateY(-50%) scale(1.1);
}

/* Form Options */
.form-options {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 0.625rem;
    cursor: pointer;
}

.checkbox-input {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}

.checkbox-box {
    position: relative;
    width: 1.125rem;
    height: 1.125rem;
    border: 2px solid rgb(71, 85, 105);
    border-radius: 0.375rem;
    background: transparent;
    transition: all 0.3s;
}

.checkbox-input:checked + .checkbox-box {
    background: linear-gradient(135deg, #3b82f6, #6366f1);
    border-color: #3b82f6;
}

.checkbox-label:hover .checkbox-box {
    border-color: #60a5fa;
}

.checkbox-check {
    position: absolute;
    inset: 0;
    width: 1.125rem;
    height: 1.125rem;
    color: white;
    opacity: 0;
    transition: opacity 0.3s;
    pointer-events: none;
}

.checkbox-input:checked + .checkbox-box .checkbox-check {
    opacity: 1;
}

.checkbox-text {
    font-size: 0.875rem;
    color: rgb(203, 213, 225);
    transition: color 0.3s;
}

.checkbox-label:hover .checkbox-text {
    color: white;
}

.forgot-link {
    font-size: 0.875rem;
    font-weight: 600;
    color: #60a5fa;
    transition: all 0.3s;
}

.forgot-link:hover {
    color: #93c5fd;
    text-decoration: underline;
}

/* Submit Button */
.submit-btn {
    position: relative;
    width: 100%;
    padding: 1rem;
    border-radius: 1rem;
    overflow: hidden;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.submit-btn:hover:not(:disabled) {
    transform: translateY(-2px);
}

.submit-btn:active:not(:disabled) {
    transform: translateY(0);
}

.submit-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.btn-bg {
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, #3b82f6, #6366f1);
    transition: transform 0.3s;
}

.submit-btn:hover:not(:disabled) .btn-bg {
    transform: scale(1.05);
}

.btn-shine {
    position: absolute;
    inset: 0;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transform: translateX(-100%);
    transition: transform 0.6s;
}

.submit-btn:hover:not(:disabled) .btn-shine {
    transform: translateX(100%);
}

.btn-content {
    position: relative;
    z-index: 10;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    color: white;
    font-size: 1rem;
    font-weight: 600;
}

.btn-loading {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.spinner {
    width: 1.125rem;
    height: 1.125rem;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

.spinner-track {
    fill: none;
    stroke: currentColor;
    opacity: 0.25;
}

.spinner-fill {
    opacity: 0.75;
}

.btn-arrow {
    width: 1.125rem;
    height: 1.125rem;
    transition: transform 0.3s;
}

.submit-btn:hover:not(:disabled) .btn-arrow {
    transform: translateX(4px);
}

/* Divider */
.divider {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin: 2rem 0;
}

.divider-line {
    flex: 1;
    height: 1px;
    background: linear-gradient(to right, transparent, rgba(255, 255, 255, 0.1), transparent);
}

.divider-text {
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: rgb(100, 116, 139);
}

/* Social Login */
.social-login {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
    animation: fade-in 0.8s cubic-bezier(0.16, 1, 0.3, 1) 0.3s both;
}

.social-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.625rem;
    padding: 0.875rem;
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 1rem;
    color: rgb(203, 213, 225);
    font-size: 0.875rem;
    font-weight: 600;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.social-btn:hover {
    background: rgba(255, 255, 255, 0.08);
    border-color: rgba(255, 255, 255, 0.2);
    transform: translateY(-2px);
    color: white;
}

.social-btn img {
    width: 1.25rem;
    height: 1.25rem;
    opacity: 0.9;
    transition: all 0.3s;
}

.social-btn:hover img {
    opacity: 1;
    transform: scale(1.1);
}

/* Register Link */
.register-link {
    margin-top: 2rem;
    text-align: center;
    animation: fade-in 0.8s cubic-bezier(0.16, 1, 0.3, 1) 0.4s both;
}

.register-link p {
    font-size: 0.875rem;
    color: rgb(148, 163, 184);
}

.register-link .link {
    font-weight: 700;
    color: #60a5fa;
    transition: all 0.3s;
}

.register-link .link:hover {
    color: #93c5fd;
    text-decoration: underline;
}

/* ===== RESPONSIVE ADJUSTMENTS ===== */
@media (max-width: 640px) {
    .login-card {
        border-radius: 2rem;
    }
    
    .form-panel {
        padding: 1.5rem;
    }
    
    .form-header h3 {
        font-size: 1.75rem;
    }
}

/* ===== ACCESSIBILITY ===== */
@media (prefers-reduced-motion: reduce) {
    *,
    *::before,
    *::after {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
}

/* ===== PRINT STYLES ===== */
@media print {
    .bg-layer {
        display: none !important;
    }
    
    .login-container {
        background: white;
        color: black;
    }
}
</style>