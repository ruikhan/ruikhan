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

// ── CSRF helpers ──────────────────────────────────────────────────────────────

/**
 * Get the ENCRYPTED cookie value (correct input for X-XSRF-TOKEN).
 * Do NOT use this for X-CSRF-TOKEN — different format.
 */
const getXsrfCookie = () => {
    try {
        const row = document.cookie.split('; ').find(r => r.startsWith('XSRF-TOKEN='));
        return row ? decodeURIComponent(row.split('=')[1]) : null;
    } catch { return null; }
};

/**
 * Refresh the session. After this, the XSRF-TOKEN cookie is fresh.
 * Inertia will pick it up via the X-XSRF-TOKEN header set in router.on('before').
 * Do NOT update the <meta> tag from the cookie — they hold different values.
 */
const refreshCsrf = async () => {
    // Use the global refreshCsrf if available (from app.js), otherwise call directly
    if (window.__refreshCsrf) {
        await window.__refreshCsrf();
    } else {
        await fetch('/sanctum/csrf-cookie', {
            credentials: 'same-origin',
            headers: { Accept: 'application/json' },
        });
    }
};

// ── Form submit ───────────────────────────────────────────────────────────────

let _retried = false;

const submit = async () => {
    // Ensure a fresh cookie before every login attempt
    await refreshCsrf();
    _retried = false;

    form.post(route('login'), {
        onFinish: () => {
            form.reset('password');
            _retried = false;
        },
        onError: async (errors) => {
            // Detect a 419 surface from Inertia (shows as empty error object
            // or a message containing "419" or "CSRF")
            const keys = Object.keys(errors);
            const is419 = keys.length === 0
                || errors?.message?.includes('419')
                || errors?.message?.toLowerCase().includes('csrf');

            if (is419 && !_retried) {
                _retried = true;
                console.warn('⚠️ 419 on login — refreshing and retrying once…');
                await refreshCsrf();

                // Small tick to ensure the browser has the new cookie before posting
                await new Promise(r => setTimeout(r, 50));
                submit();
            }
        },
    });
};

// ── Visibility-change (user returns after leaving tab open) ───────────────────
let lastVisible = Date.now();

const handleVisibilityChange = async () => {
    if (document.hidden) { lastVisible = Date.now(); return; }
    if (Date.now() - lastVisible > 2 * 60 * 1000) {
        await refreshCsrf();
    }
};

// ── Visual / interaction state (unchanged from original) ─────────────────────

const mouseX           = ref(0);
const mouseY           = ref(0);
const showPassword     = ref(false);
const emailFocused     = ref(false);
const passwordFocused  = ref(false);
const isInitialLoad    = ref(true);

let mouseRAF = null;
const handleMouseMove = (e) => {
    if (mouseRAF) return;
    mouseRAF = requestAnimationFrame(() => {
        mouseX.value = e.clientX;
        mouseY.value = e.clientY;
        mouseRAF = null;
    });
};

const audioContext = ref(null);
const playSound = (frequency, duration = 0.08) => {
    try {
        if (!audioContext.value)
            audioContext.value = new (window.AudioContext || window.webkitAudioContext)();
        const ctx        = audioContext.value;
        const oscillator = ctx.createOscillator();
        const gainNode   = ctx.createGain();
        oscillator.connect(gainNode);
        gainNode.connect(ctx.destination);
        oscillator.frequency.value = frequency;
        oscillator.type = 'sine';
        gainNode.gain.setValueAtTime(0.02, ctx.currentTime);
        gainNode.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + duration);
        oscillator.start(ctx.currentTime);
        oscillator.stop(ctx.currentTime + duration);
    } catch { /* silent fail */ }
};

onMounted(async () => {
    window.addEventListener('mousemove', handleMouseMove, { passive: true });
    document.addEventListener('visibilitychange', handleVisibilityChange);
    setTimeout(() => { isInitialLoad.value = false; }, 100);

    // Pre-warm a fresh cookie on page load
    await refreshCsrf();
    console.log('✅ CSRF token initialized');
});

onUnmounted(() => {
    window.removeEventListener('mousemove', handleMouseMove);
    document.removeEventListener('visibilitychange', handleVisibilityChange);
    if (mouseRAF) cancelAnimationFrame(mouseRAF);
});
</script>

<template>
    <Head title="Sign In - E-PILI Portal" />

    <div class="login-container" :class="{ 'initial-load': isInitialLoad }">
        
        <!-- Enhanced Background Layer -->
        <div class="bg-layer">
            <!-- Base gradient -->
            <div class="bg-base"></div>
            
            <!-- Animated mesh gradients -->
            <div class="bg-mesh">
                <div class="mesh-orb mesh-1" 
                     :style="{ transform: `translate(${mouseX * 0.025}px, ${mouseY * 0.025}px)` }"></div>
                <div class="mesh-orb mesh-2" 
                     :style="{ transform: `translate(${-mouseX * 0.02}px, ${-mouseY * 0.02}px)` }"></div>
                <div class="mesh-orb mesh-3" 
                     :style="{ transform: `translate(${mouseX * 0.015}px, ${-mouseY * 0.015}px)` }"></div>
            </div>
            
            <!-- Grid overlay -->
            <div class="bg-grid"></div>
            
            <!-- Noise texture -->
            <div class="bg-noise"></div>
            
            <!-- Floating particles -->
            <div class="bg-particles">
                <div v-for="i in 30" :key="i" class="particle" 
                     :style="{
                         left: `${Math.random() * 100}%`,
                         animationDelay: `${Math.random() * 5}s`,
                         animationDuration: `${5 + Math.random() * 10}s`
                     }"></div>
            </div>
            
            <!-- Animated waves -->
            <div class="bg-waves">
                <svg class="wave wave-1" viewBox="0 0 1600 200" preserveAspectRatio="none">
                    <path d="M0,100 C400,150 800,50 1600,100 L1600,0 L0,0 Z" fill="url(#waveGrad1)">
                        <animate attributeName="d" dur="12s" repeatCount="indefinite"
                                 values="M0,100 C400,150 800,50 1600,100 L1600,0 L0,0 Z;
                                         M0,80 C400,120 800,80 1600,120 L1600,0 L0,0 Z;
                                         M0,100 C400,150 800,50 1600,100 L1600,0 L0,0 Z" />
                    </path>
                    <defs>
                        <linearGradient id="waveGrad1" x1="0%" y1="0%" x2="100%" y2="0%">
                            <stop offset="0%" style="stop-color:#3b82f6;stop-opacity:0" />
                            <stop offset="50%" style="stop-color:#3b82f6;stop-opacity:0.15" />
                            <stop offset="100%" style="stop-color:#3b82f6;stop-opacity:0" />
                        </linearGradient>
                    </defs>
                </svg>
                <svg class="wave wave-2" viewBox="0 0 1600 200" preserveAspectRatio="none">
                    <path d="M0,120 C400,80 800,140 1600,100 L1600,0 L0,0 Z" fill="url(#waveGrad2)">
                        <animate attributeName="d" dur="15s" repeatCount="indefinite"
                                 values="M0,120 C400,80 800,140 1600,100 L1600,0 L0,0 Z;
                                         M0,100 C400,130 800,90 1600,110 L1600,0 L0,0 Z;
                                         M0,120 C400,80 800,140 1600,100 L1600,0 L0,0 Z" />
                    </path>
                    <defs>
                        <linearGradient id="waveGrad2" x1="0%" y1="0%" x2="100%" y2="0%">
                            <stop offset="0%" style="stop-color:#a855f7;stop-opacity:0" />
                            <stop offset="50%" style="stop-color:#a855f7;stop-opacity:0.1" />
                            <stop offset="100%" style="stop-color:#a855f7;stop-opacity:0" />
                        </linearGradient>
                    </defs>
                </svg>
            </div>
        </div>

        <!-- Main Content -->
        <div class="content-wrapper">
            
            <!-- Login Card -->
            <div class="login-card">
                
                <!-- Card Glass Effects -->
                <div class="card-glass"></div>
                <div class="card-border-glow"></div>
                <div class="card-shine"></div>
                
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
                            <div class="feature-item" style="animation-delay: 0.1s">
                                <div class="feature-icon-wrapper">
                                    <div class="feature-icon blue">
                                        <div class="icon-glow"></div>
                                        <span>🏛️</span>
                                    </div>
                                    <div class="icon-ring"></div>
                                </div>
                                <div class="feature-text">
                                    <h3>Provincial Capital</h3>
                                    <p>Governance center of CamSur</p>
                                </div>
                            </div>

                            <div class="feature-item" style="animation-delay: 0.2s">
                                <div class="feature-icon-wrapper">
                                    <div class="feature-icon emerald">
                                        <div class="icon-glow"></div>
                                        <span>🌾</span>
                                    </div>
                                    <div class="icon-ring"></div>
                                </div>
                                <div class="feature-text">
                                    <h3>Agricultural Hub</h3>
                                    <p>Premier rice & pili production</p>
                                </div>
                            </div>

                            <div class="feature-item" style="animation-delay: 0.3s">
                                <div class="feature-icon-wrapper">
                                    <div class="feature-icon purple">
                                        <div class="icon-glow"></div>
                                        <span>🔒</span>
                                    </div>
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
                            <div class="banner-bg"></div>
                            <div class="banner-shine"></div>
                            <div class="banner-sparkles">
                                <span class="sparkle">✨</span>
                                <span class="sparkle">✨</span>
                                <span class="sparkle">✨</span>
                            </div>
                            <div class="banner-content">
                                <h3>🚀 E-Governance Live</h3>
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
                            <div class="logo-rings">
                                <div class="logo-ring ring-1"></div>
                                <div class="logo-ring ring-2"></div>
                            </div>
                            <div class="logo-glow"></div>
                            <span>🏛️</span>
                        </div>
                        <h2>E-PILI Portal</h2>
                        <p class="mobile-subtitle">Digital Governance Platform</p>
                    </div>

                    <!-- Form Header -->
                    <div class="form-header">
                        <h3>Welcome Back</h3>
                        <p>Enter your credentials to access your account</p>
                    </div>

                    <!-- Status Message -->
                    <Transition name="status">
                        <div v-if="status" class="status-message">
                            <div class="status-message-bg"></div>
                            <span class="status-icon">✓</span>
                            <span>{{ status }}</span>
                        </div>
                    </Transition>

                    <!-- Login Form -->
                    <form @submit.prevent="submit" class="login-form">
                        
                        <!-- Email Input -->
                        <div class="input-group">
                            <label class="input-label">Email Address</label>
                            <div class="input-wrapper" :class="{ focused: emailFocused }">
                                <div class="input-bg"></div>
                                <div class="input-glow"></div>
                                <span class="input-icon">📧</span>
                                <input 
                                    type="email" 
                                    v-model="form.email" 
                                    @focus="emailFocused = true; playSound(600)"
                                    @blur="emailFocused = false"
                                    placeholder="juan@example.com"
                                    required
                                    autocomplete="email"
                                    class="input-field" 
                                />
                                <div class="input-border"></div>
                            </div>
                            <Transition name="error">
                                <div v-if="form.errors.email" class="input-error">
                                    <span>⚠️</span>
                                    <span>{{ form.errors.email }}</span>
                                </div>
                            </Transition>
                        </div>

                        <!-- Password Input -->
                        <div class="input-group">
                            <label class="input-label">Password</label>
                            <div class="input-wrapper" :class="{ focused: passwordFocused }">
                                <div class="input-bg"></div>
                                <div class="input-glow"></div>
                                <span class="input-icon">🔒</span>
                                <input 
                                    :type="showPassword ? 'text' : 'password'" 
                                    v-model="form.password" 
                                    @focus="passwordFocused = true; playSound(600)"
                                    @blur="passwordFocused = false"
                                    placeholder="••••••••"
                                    required
                                    autocomplete="current-password"
                                    class="input-field" 
                                />
                                <button 
                                    type="button"
                                    @click="showPassword = !showPassword; playSound(700)"
                                    class="toggle-password"
                                >
                                    <div class="toggle-bg"></div>
                                    <span v-if="!showPassword">👁️</span>
                                    <span v-else>🙈</span>
                                </button>
                                <div class="input-border"></div>
                            </div>
                            <Transition name="error">
                                <div v-if="form.errors.password" class="input-error">
                                    <span>⚠️</span>
                                    <span>{{ form.errors.password }}</span>
                                </div>
                            </Transition>
                        </div>

                        <!-- Remember & Forgot -->
                        <div class="form-options">
                            <label class="checkbox-label" @click="playSound(650, 0.06)">
                                <input type="checkbox" v-model="form.remember" class="checkbox-input">
                                <span class="checkbox-box">
                                    <div class="checkbox-glow"></div>
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
                                @click="playSound(700)"
                            >
                                Forgot Password?
                            </Link>
                        </div>

                        <!-- Submit Button -->
                        <button 
                            type="submit" 
                            :disabled="form.processing" 
                            class="submit-btn"
                            @click="playSound(800)"
                        >
                            <div class="btn-bg"></div>
                            <div class="btn-glow"></div>
                            <div class="btn-shine"></div>
                            <div class="btn-content">
                                <span v-if="!form.processing">Sign In</span>
                                <span v-else class="btn-loading">
                                    <svg class="spinner" viewBox="0 0 24 24">
                                        <circle class="spinner-track" cx="12" cy="12" r="10" stroke-width="4"></circle>
                                        <circle class="spinner-fill" cx="12" cy="12" r="10" stroke-width="4"></circle>
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
                        <button class="social-btn" @click="playSound(750)">
                            <div class="social-bg"></div>
                            <div class="social-shine"></div>
                            <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google">
                            <span>Google</span>
                        </button>
                        <button class="social-btn" @click="playSound(750)">
                            <div class="social-bg"></div>
                            <div class="social-shine"></div>
                            <img src="https://www.svgrepo.com/show/475647/facebook-color.svg" alt="Facebook">
                            <span>Facebook</span>
                        </button>
                    </div>

                    <!-- Register Link -->
                    <div class="register-link">
                        <p>
                            Don't have an account? 
                            <Link :href="route('register')" class="link" @click="playSound(700)">
                                Create one now
                            </Link>
                        </p>
                    </div>
                </div>

            </div>

            <!-- Footer -->
            <div class="page-footer">
                <p>© 2026 E-PILI Portal. Secure & Encrypted.</p>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* ===== BASE SETUP ===== */
* {
    backface-visibility: hidden;
    -webkit-backface-visibility: hidden;
    transform-style: preserve-3d;
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

.login-container.initial-load {
    opacity: 0;
    animation: container-fade-in 0.6s ease-out 0.1s forwards;
}

@keyframes container-fade-in {
    to { opacity: 1; }
}

/* ===== ENHANCED BACKGROUND LAYER ===== */
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

.bg-mesh {
    position: absolute;
    inset: 0;
    opacity: 0.7;
}

.mesh-orb {
    position: absolute;
    border-radius: 50%;
    filter: blur(160px);
    will-change: transform;
    animation: mesh-float 35s ease-in-out infinite;
}

.mesh-1 {
    top: 15%;
    left: 20%;
    width: 600px;
    height: 600px;
    background: radial-gradient(circle, rgba(59, 130, 246, 0.25), transparent 70%);
}

.mesh-2 {
    bottom: 15%;
    right: 20%;
    width: 700px;
    height: 700px;
    background: radial-gradient(circle, rgba(168, 85, 247, 0.2), transparent 70%);
    animation-delay: 8s;
}

.mesh-3 {
    top: 50%;
    left: 50%;
    width: 500px;
    height: 500px;
    background: radial-gradient(circle, rgba(6, 182, 212, 0.15), transparent 70%);
    animation-delay: 16s;
}

@keyframes mesh-float {
    0%, 100% { transform: translate(0, 0) scale(1); }
    33% { transform: translate(50px, -50px) scale(1.1); }
    66% { transform: translate(-40px, 40px) scale(0.9); }
}

.bg-grid {
    position: absolute;
    inset: 0;
    background-image: 
        linear-gradient(rgba(59, 130, 246, 0.04) 1px, transparent 1px),
        linear-gradient(90deg, rgba(59, 130, 246, 0.04) 1px, transparent 1px);
    background-size: 60px 60px;
    mask-image: radial-gradient(ellipse at center, black 20%, transparent 75%);
    animation: grid-pulse 20s ease-in-out infinite;
}

@keyframes grid-pulse {
    0%, 100% { opacity: 0.3; }
    50% { opacity: 0.6; }
}

.bg-noise {
    position: absolute;
    inset: 0;
    background-image: url('data:image/svg+xml,%3Csvg viewBox="0 0 400 400" xmlns="http://www.w3.org/2000/svg"%3E%3Cfilter id="n"%3E%3CfeTurbulence type="fractalNoise" baseFrequency="3.5" numOctaves="4"/%3E%3C/filter%3E%3Crect width="100%25" height="100%25" filter="url(%23n)" opacity="0.05"/%3E%3C/svg%3E');
    opacity: 0.04;
    mix-blend-mode: overlay;
    animation: noise-shift 8s steps(10) infinite;
}

@keyframes noise-shift {
    0%, 100% { transform: translate(0, 0); }
    10% { transform: translate(-5%, -10%); }
    20% { transform: translate(-15%, 5%); }
    30% { transform: translate(7%, -25%); }
    40% { transform: translate(-5%, 25%); }
    50% { transform: translate(-15%, 10%); }
    60% { transform: translate(15%, 0%); }
    70% { transform: translate(0%, 15%); }
    80% { transform: translate(3%, 25%); }
    90% { transform: translate(-10%, 10%); }
}

.bg-particles {
    position: absolute;
    inset: 0;
    overflow: hidden;
}

.particle {
    position: absolute;
    width: 2px;
    height: 2px;
    background: rgba(59, 130, 246, 0.4);
    border-radius: 50%;
    animation: particle-rise linear infinite;
    box-shadow: 0 0 8px rgba(59, 130, 246, 0.6);
}

@keyframes particle-rise {
    0% {
        bottom: -10%;
        opacity: 0;
    }
    10% {
        opacity: 1;
    }
    90% {
        opacity: 1;
    }
    100% {
        bottom: 110%;
        opacity: 0;
    }
}

.bg-waves {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 70vh;
    opacity: 0.3;
    overflow: hidden;
}

.wave {
    position: absolute;
    width: 200%;
    height: 100%;
}

.wave-1 {
    animation: wave-drift-1 35s linear infinite;
}

.wave-2 {
    animation: wave-drift-2 40s linear infinite;
    opacity: 0.7;
}

@keyframes wave-drift-1 {
    0% { transform: translateX(0); }
    100% { transform: translateX(-50%); }
}

@keyframes wave-drift-2 {
    0% { transform: translateX(-25%); }
    100% { transform: translateX(-75%); }
}

/* ===== CONTENT WRAPPER ===== */
.content-wrapper {
    position: relative;
    z-index: 10;
    width: 100%;
    max-width: 1100px;
}

/* ===== ENHANCED LOGIN CARD ===== */
.login-card {
    position: relative;
    display: grid;
    grid-template-columns: 1fr;
    background: rgba(8, 8, 12, 0.75);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 2.5rem;
    overflow: hidden;
    box-shadow: 
        0 30px 90px rgba(0, 0, 0, 0.9),
        0 0 120px rgba(59, 130, 246, 0.15);
    animation: card-appear 0.9s cubic-bezier(0.16, 1, 0.3, 1);
}

@media (min-width: 768px) {
    .login-card {
        grid-template-columns: 5fr 7fr;
    }
}

@keyframes card-appear {
    from { opacity: 0; transform: scale(0.94) translateY(30px); }
    to { opacity: 1; transform: scale(1) translateY(0); }
}

.card-glass {
    position: absolute;
    inset: 0;
    backdrop-filter: blur(80px) saturate(180%);
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.03), rgba(255, 255, 255, 0.01));
    pointer-events: none;
}

.card-border-glow {
    position: absolute;
    inset: -2px;
    border-radius: 2.5rem;
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.3), rgba(168, 85, 247, 0.2), rgba(6, 182, 212, 0.3));
    filter: blur(15px);
    opacity: 0.4;
    z-index: -1;
    animation: border-pulse 4s ease-in-out infinite;
}

@keyframes border-pulse {
    0%, 100% { opacity: 0.3; }
    50% { opacity: 0.6; }
}

.card-shine {
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(to right, transparent, rgba(255, 255, 255, 0.03), transparent);
    animation: card-shine 10s ease-in-out infinite;
    pointer-events: none;
}

@keyframes card-shine {
    to { left: 200%; }
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
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.04), transparent);
    border-right: 1px solid rgba(255, 255, 255, 0.06);
}

.panel-pattern {
    position: absolute;
    inset: 0;
    background-image: url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="0.02"%3E%3Cpath d="M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');
    opacity: 0.08;
    animation: pattern-shift 40s linear infinite;
}

@keyframes pattern-shift {
    to { transform: translate(60px, 60px); }
}

.panel-glow {
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(59, 130, 246, 0.12), transparent 70%);
    animation: glow-rotate 25s linear infinite;
    filter: blur(80px);
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

/* Status Badge - Enhanced */
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 1.25rem;
    background: rgba(59, 130, 246, 0.12);
    border: 1px solid rgba(59, 130, 246, 0.25);
    border-radius: 9999px;
    backdrop-filter: blur(30px);
    width: fit-content;
    animation: badge-float 3s ease-in-out infinite;
    box-shadow: 0 8px 32px rgba(59, 130, 246, 0.2);
}

@keyframes badge-float {
    0%, 100% { transform: translateY(0) scale(1); }
    50% { transform: translateY(-10px) scale(1.02); }
}

.status-pulse {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 1rem;
    height: 1rem;
}

.pulse-ring {
    position: absolute;
    inset: 0;
    border-radius: 50%;
    background: #60a5fa;
    animation: ping-smooth 2.5s cubic-bezier(0, 0, 0.2, 1) infinite;
}

@keyframes ping-smooth {
    0% { transform: scale(0.8); opacity: 1; }
    80%, 100% { transform: scale(2.8); opacity: 0; }
}

.pulse-dot {
    position: relative;
    width: 0.5rem;
    height: 0.5rem;
    border-radius: 50%;
    background: #60a5fa;
    box-shadow: 0 0 20px rgba(96, 165, 250, 1);
    animation: dot-glow 2s ease-in-out infinite;
}

@keyframes dot-glow {
    0%, 100% { box-shadow: 0 0 15px rgba(96, 165, 250, 0.8); }
    50% { box-shadow: 0 0 25px rgba(96, 165, 250, 1); }
}

.status-text {
    font-size: 0.6875rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.15em;
    color: #93c5fd;
}

/* Panel Title - Enhanced */
.panel-title {
    animation: slide-in-left 0.9s cubic-bezier(0.16, 1, 0.3, 1);
}

@keyframes slide-in-left {
    from { opacity: 0; transform: translateX(-40px); }
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
    background: linear-gradient(135deg, #60a5fa, #a78bfa, #60a5fa);
    background-size: 200% auto;
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
    animation: gradient-flow 6s linear infinite;
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

/* Feature List - Enhanced */
.feature-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.feature-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.125rem;
    background: rgba(0, 0, 0, 0.35);
    border: 1px solid rgba(255, 255, 255, 0.06);
    border-radius: 1.25rem;
    backdrop-filter: blur(30px);
    transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    animation: slide-in-up 0.9s cubic-bezier(0.16, 1, 0.3, 1) both;
    cursor: pointer;
}

@keyframes slide-in-up {
    from { opacity: 0; transform: translateY(30px) scale(0.95); }
    to { opacity: 1; transform: translateY(0) scale(1); }
}

.feature-item:hover {
    background: rgba(255, 255, 255, 0.06);
    border-color: rgba(255, 255, 255, 0.12);
    transform: translateX(6px) scale(1.02);
}

.feature-icon-wrapper {
    position: relative;
}

.feature-icon {
    position: relative;
    width: 2.75rem;
    height: 2.75rem;
    border-radius: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.375rem;
    flex-shrink: 0;
    transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    overflow: hidden;
}

.icon-glow {
    position: absolute;
    inset: -50%;
    border-radius: 50%;
    filter: blur(20px);
    opacity: 0.6;
    animation: icon-glow-pulse 3s ease-in-out infinite;
}

@keyframes icon-glow-pulse {
    0%, 100% { opacity: 0.4; transform: scale(1); }
    50% { opacity: 0.7; transform: scale(1.2); }
}

.feature-item:hover .feature-icon {
    transform: scale(1.15) rotate(8deg);
}

.feature-icon.blue {
    background: rgba(59, 130, 246, 0.18);
    box-shadow: 0 10px 40px rgba(59, 130, 246, 0.25);
}

.feature-icon.blue .icon-glow {
    background: radial-gradient(circle, rgba(59, 130, 246, 0.8), transparent);
}

.feature-icon.emerald {
    background: rgba(16, 185, 129, 0.18);
    box-shadow: 0 10px 40px rgba(16, 185, 129, 0.25);
}

.feature-icon.emerald .icon-glow {
    background: radial-gradient(circle, rgba(16, 185, 129, 0.8), transparent);
}

.feature-icon.purple {
    background: rgba(168, 85, 247, 0.18);
    box-shadow: 0 10px 40px rgba(168, 85, 247, 0.25);
}

.feature-icon.purple .icon-glow {
    background: radial-gradient(circle, rgba(168, 85, 247, 0.8), transparent);
}

.icon-ring {
    position: absolute;
    inset: -0.5rem;
    border: 2px solid currentColor;
    border-radius: 1.25rem;
    opacity: 0;
    animation: ring-pulse-slow 3s ease-out infinite;
}

@keyframes ring-pulse-slow {
    0% { transform: scale(0.9); opacity: 0.7; }
    100% { transform: scale(1.4); opacity: 0; }
}

.feature-item:hover .icon-ring {
    opacity: 0.6;
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

/* Promo Banner - Enhanced */
.promo-banner {
    position: relative;
    padding: 1.75rem;
    border: 1px solid rgba(59, 130, 246, 0.3);
    border-radius: 1.75rem;
    overflow: hidden;
    animation: slide-in-up 0.9s cubic-bezier(0.16, 1, 0.3, 1) 0.5s both;
    transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    cursor: pointer;
}

.promo-banner:hover {
    transform: scale(1.03);
}

.banner-bg {
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.18), rgba(99, 102, 241, 0.18));
    backdrop-filter: blur(20px);
}

.banner-shine {
    position: absolute;
    inset: 0;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.15), transparent);
    animation: shine-sweep 4s ease-in-out infinite;
}

@keyframes shine-sweep {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}

.banner-sparkles {
    position: absolute;
    inset: 0;
    pointer-events: none;
}

.sparkle {
    position: absolute;
    font-size: 1rem;
    animation: sparkle-twinkle 2.5s ease-in-out infinite;
}

.sparkle:nth-child(1) {
    top: 15%;
    right: 20%;
    animation-delay: 0s;
}

.sparkle:nth-child(2) {
    bottom: 25%;
    right: 15%;
    animation-delay: 0.8s;
}

.sparkle:nth-child(3) {
    top: 60%;
    left: 15%;
    animation-delay: 1.6s;
}

@keyframes sparkle-twinkle {
    0%, 100% { opacity: 0.2; transform: scale(0.7) rotate(0deg); }
    50% { opacity: 1; transform: scale(1.3) rotate(180deg); }
}

.banner-content {
    position: relative;
    z-index: 10;
}

.banner-content h3 {
    font-size: 1rem;
    font-weight: 700;
    color: white;
    margin-bottom: 0.5rem;
}

.banner-content p {
    font-size: 0.8125rem;
    color: rgb(147, 197, 253);
}

/* ===== FORM PANEL ===== */
.form-panel {
    padding: 2rem;
    position: relative;
}

@media (min-width: 640px) {
    .form-panel {
        padding: 2.5rem 3rem;
    }
}

@media (min-width: 768px) {
    .form-panel {
        padding: 3.5rem;
    }
}

/* Mobile Header - Enhanced */
.mobile-header {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
    margin-bottom: 2.5rem;
    animation: fade-in-scale 0.9s cubic-bezier(0.16, 1, 0.3, 1);
}

@media (min-width: 768px) {
    .mobile-header {
        display: none;
    }
}

@keyframes fade-in-scale {
    from { opacity: 0; transform: scale(0.9); }
    to { opacity: 1; transform: scale(1); }
}

.mobile-logo {
    position: relative;
    width: 4.5rem;
    height: 4.5rem;
    border-radius: 1.75rem;
    background: linear-gradient(135deg, #3b82f6, #6366f1);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.25rem;
    box-shadow: 0 15px 50px rgba(59, 130, 246, 0.5);
    transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    cursor: pointer;
    overflow: hidden;
}

.mobile-logo:hover {
    transform: scale(1.1) rotate(-5deg);
}

.logo-rings {
    position: absolute;
    inset: -8px;
}

.logo-ring {
    position: absolute;
    inset: 0;
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-radius: 2rem;
    animation: logo-ring-pulse 2.5s ease-out infinite;
}

.ring-1 {
    animation-delay: 0s;
}

.ring-2 {
    animation-delay: 0.5s;
}

@keyframes logo-ring-pulse {
    0% { transform: scale(1); opacity: 0.6; }
    100% { transform: scale(1.5); opacity: 0; }
}

.logo-glow {
    position: absolute;
    inset: -20%;
    background: radial-gradient(circle, rgba(59, 130, 246, 0.6), transparent);
    filter: blur(30px);
    animation: logo-glow-pulse 2s ease-in-out infinite;
}

@keyframes logo-glow-pulse {
    0%, 100% { opacity: 0.5; }
    50% { opacity: 1; }
}

.mobile-header h2 {
    font-size: 1.625rem;
    font-weight: 700;
    color: white;
}

.mobile-subtitle {
    font-size: 0.8125rem;
    color: rgb(148, 163, 184);
    font-weight: 500;
}

/* Form Header - Enhanced */
.form-header {
    margin-bottom: 2.5rem;
    text-align: center;
    animation: fade-in 0.9s cubic-bezier(0.16, 1, 0.3, 1) 0.15s both;
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
    margin-bottom: 0.625rem;
}

.form-header p {
    font-size: 0.9375rem;
    color: rgb(148, 163, 184);
}

@keyframes fade-in {
    from { opacity: 0; transform: translateY(15px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Status Message - Enhanced */
.status-message {
    position: relative;
    display: flex;
    align-items: center;
    gap: 0.875rem;
    padding: 1.125rem 1.25rem;
    margin-bottom: 2rem;
    border: 1px solid rgba(34, 197, 94, 0.4);
    border-radius: 1.25rem;
    color: rgb(134, 239, 172);
    font-size: 0.875rem;
    font-weight: 500;
    overflow: hidden;
}

.status-message-bg {
    position: absolute;
    inset: 0;
    background: rgba(34, 197, 94, 0.12);
    backdrop-filter: blur(20px);
}

.status-icon {
    position: relative;
    z-index: 10;
    width: 1.5rem;
    height: 1.5rem;
    border-radius: 50%;
    background: rgb(34, 197, 94);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.875rem;
    flex-shrink: 0;
    box-shadow: 0 0 20px rgba(34, 197, 94, 0.6);
}

.status-enter-active {
    animation: slide-in-down 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.status-leave-active {
    animation: slide-out-up 0.4s cubic-bezier(0.4, 0, 1, 1);
}

@keyframes slide-in-down {
    from { opacity: 0; transform: translateY(-20px) scale(0.95); }
    to { opacity: 1; transform: translateY(0) scale(1); }
}

@keyframes slide-out-up {
    to { opacity: 0; transform: translateY(-20px) scale(0.95); }
}

/* Login Form - Enhanced */
.login-form {
    display: flex;
    flex-direction: column;
    gap: 1.75rem;
    animation: fade-in 0.9s cubic-bezier(0.16, 1, 0.3, 1) 0.3s both;
}

/* Input Group - Enhanced */
.input-group {
    display: flex;
    flex-direction: column;
    gap: 0.625rem;
}

.input-label {
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: rgb(148, 163, 184);
    margin-left: 0.375rem;
}

.input-wrapper {
    position: relative;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.input-bg {
    position: absolute;
    inset: 0;
    background: rgba(255, 255, 255, 0.02);
    border-radius: 1.125rem;
    transition: all 0.4s;
}

.input-wrapper:hover .input-bg {
    background: rgba(255, 255, 255, 0.04);
}

.input-wrapper.focused .input-bg {
    background: rgba(59, 130, 246, 0.06);
}

.input-glow {
    position: absolute;
    inset: -8px;
    border-radius: 1.25rem;
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.3), rgba(99, 102, 241, 0.3));
    filter: blur(20px);
    opacity: 0;
    transition: opacity 0.4s;
}

.input-wrapper.focused .input-glow {
    opacity: 1;
}

.input-icon {
    position: absolute;
    left: 1.125rem;
    top: 50%;
    transform: translateY(-50%);
    font-size: 1.25rem;
    color: rgb(100, 116, 139);
    transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    pointer-events: none;
    z-index: 10;
}

.input-wrapper.focused .input-icon {
    color: #60a5fa;
    transform: translateY(-50%) scale(1.15) rotate(-10deg);
}

.input-field {
    position: relative;
    z-index: 5;
    width: 100%;
    padding: 1.125rem 1.125rem 1.125rem 3.25rem;
    background: transparent;
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 1.125rem;
    color: white;
    font-size: 0.9375rem;
    outline: none;
    transition: all 0.4s;
}

.input-field::placeholder {
    color: rgb(100, 116, 139);
}

.input-field:hover {
    border-color: rgba(255, 255, 255, 0.15);
}

.input-field:focus {
    border-color: transparent;
}

.input-border {
    position: absolute;
    inset: -1px;
    border-radius: 1.125rem;
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.6), rgba(99, 102, 241, 0.6));
    opacity: 0;
    transition: opacity 0.4s;
    pointer-events: none;
    z-index: 1;
}

.input-wrapper.focused .input-border {
    opacity: 1;
    animation: border-shimmer 2s linear infinite;
}

@keyframes border-shimmer {
    0% { background-position: 0% 50%; }
    100% { background-position: 200% 50%; }
}

.toggle-password {
    position: absolute;
    right: 1.125rem;
    top: 50%;
    transform: translateY(-50%);
    z-index: 10;
    font-size: 1.25rem;
    color: rgb(100, 116, 139);
    transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    padding: 0.5rem;
    border-radius: 0.5rem;
    overflow: hidden;
}

.toggle-bg {
    position: absolute;
    inset: 0;
    background: rgba(255, 255, 255, 0.05);
    opacity: 0;
    transition: opacity 0.3s;
}

.toggle-password:hover .toggle-bg {
    opacity: 1;
}

.toggle-password:hover {
    color: #60a5fa;
    transform: translateY(-50%) scale(1.15);
}

.toggle-password:active {
    transform: translateY(-50%) scale(0.95);
}

/* Input Error - New */
.input-error {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1rem;
    background: rgba(239, 68, 68, 0.1);
    border: 1px solid rgba(239, 68, 68, 0.3);
    border-radius: 0.875rem;
    color: rgb(252, 165, 165);
    font-size: 0.8125rem;
    font-weight: 500;
}

.error-enter-active {
    animation: shake 0.5s cubic-bezier(0.36, 0.07, 0.19, 0.97);
}

.error-leave-active {
    animation: fade-out 0.3s ease-out;
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
    20%, 40%, 60%, 80% { transform: translateX(5px); }
}

@keyframes fade-out {
    to { opacity: 0; transform: translateY(-10px); }
}

/* Form Options - Enhanced */
.form-options {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    cursor: pointer;
    transition: transform 0.2s;
}

.checkbox-label:active {
    transform: scale(0.98);
}

.checkbox-input {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}

.checkbox-box {
    position: relative;
    width: 1.25rem;
    height: 1.25rem;
    border: 2px solid rgb(71, 85, 105);
    border-radius: 0.5rem;
    background: transparent;
    transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    overflow: hidden;
}

.checkbox-glow {
    position: absolute;
    inset: -50%;
    background: radial-gradient(circle, rgba(59, 130, 246, 0.8), transparent);
    filter: blur(15px);
    opacity: 0;
    transition: opacity 0.3s;
}

.checkbox-input:checked + .checkbox-box .checkbox-glow {
    opacity: 1;
}

.checkbox-input:checked + .checkbox-box {
    background: linear-gradient(135deg, #3b82f6, #6366f1);
    border-color: #3b82f6;
    transform: scale(1.1);
}

.checkbox-label:hover .checkbox-box {
    border-color: #60a5fa;
    transform: scale(1.05);
}

.checkbox-check {
    position: absolute;
    inset: 0;
    width: 1.25rem;
    height: 1.25rem;
    color: white;
    opacity: 0;
    transition: opacity 0.3s;
    pointer-events: none;
}

.checkbox-input:checked + .checkbox-box .checkbox-check {
    opacity: 1;
    animation: check-pop 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
}

@keyframes check-pop {
    0% { transform: scale(0.3); }
    50% { transform: scale(1.2); }
    100% { transform: scale(1); }
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
    white-space: nowrap;
}

.forgot-link:hover {
    color: #93c5fd;
    text-decoration: underline;
    transform: translateX(2px);
}

/* Submit Button - Enhanced */
.submit-btn {
    position: relative;
    width: 100%;
    padding: 1.125rem 1.5rem;
    border-radius: 1.125rem;
    overflow: hidden;
    transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.submit-btn:hover:not(:disabled) {
    transform: translateY(-3px) scale(1.02);
}

.submit-btn:active:not(:disabled) {
    transform: translateY(-1px) scale(0.98);
}

.submit-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.btn-bg {
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, #3b82f6, #6366f1);
    transition: transform 0.4s;
}

.submit-btn:hover:not(:disabled) .btn-bg {
    transform: scale(1.08);
}

.btn-glow {
    position: absolute;
    inset: -20%;
    background: radial-gradient(circle, rgba(59, 130, 246, 0.6), transparent);
    filter: blur(40px);
    opacity: 0;
    transition: opacity 0.4s;
}

.submit-btn:hover:not(:disabled) .btn-glow {
    opacity: 1;
}

.btn-shine {
    position: absolute;
    inset: 0;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.25), transparent);
    transform: translateX(-100%);
    transition: transform 0.8s;
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
    gap: 0.625rem;
    color: white;
    font-size: 1rem;
    font-weight: 600;
}

.btn-loading {
    display: flex;
    align-items: center;
    gap: 0.625rem;
}

.spinner {
    width: 1.25rem;
    height: 1.25rem;
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
    fill: none;
    stroke: currentColor;
    stroke-dasharray: 63;
    stroke-dashoffset: 47;
    animation: spinner-dash 1.5s ease-in-out infinite;
}

@keyframes spinner-dash {
    0% {
        stroke-dashoffset: 63;
    }
    50% {
        stroke-dashoffset: 16;
        transform: rotate(135deg);
    }
    100% {
        stroke-dashoffset: 63;
        transform: rotate(450deg);
    }
}

.btn-arrow {
    width: 1.25rem;
    height: 1.25rem;
    transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.submit-btn:hover:not(:disabled) .btn-arrow {
    transform: translateX(6px);
}

/* Divider - Enhanced */
.divider {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin: 2.5rem 0 2rem;
}

.divider-line {
    flex: 1;
    height: 1px;
    background: linear-gradient(to right, transparent, rgba(255, 255, 255, 0.15), transparent);
}

.divider-text {
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: rgb(100, 116, 139);
}

/* Social Login - Enhanced */
.social-login {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
    animation: fade-in 0.9s cubic-bezier(0.16, 1, 0.3, 1) 0.45s both;
}

.social-btn {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    padding: 1rem;
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 1.125rem;
    color: rgb(203, 213, 225);
    font-size: 0.875rem;
    font-weight: 600;
    transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    overflow: hidden;
}

.social-bg {
    position: absolute;
    inset: 0;
    background: rgba(255, 255, 255, 0.02);
    transition: background 0.3s;
}

.social-btn:hover .social-bg {
    background: rgba(255, 255, 255, 0.06);
}

.social-shine {
    position: absolute;
    inset: 0;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
    transform: translateX(-100%);
    transition: transform 0.6s;
}

.social-btn:hover .social-shine {
    transform: translateX(100%);
}

.social-btn:hover {
    border-color: rgba(255, 255, 255, 0.15);
    transform: translateY(-3px) scale(1.02);
    color: white;
}

.social-btn:active {
    transform: translateY(-1px) scale(0.98);
}

.social-btn img {
    position: relative;
    z-index: 10;
    width: 1.375rem;
    height: 1.375rem;
    opacity: 0.9;
    transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.social-btn:hover img {
    opacity: 1;
    transform: scale(1.15) rotate(-5deg);
}

/* Register Link - Enhanced */
.register-link {
    margin-top: 2.5rem;
    text-align: center;
    animation: fade-in 0.9s cubic-bezier(0.16, 1, 0.3, 1) 0.6s both;
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

/* Footer - New */
.page-footer {
    margin-top: 2rem;
    text-align: center;
    animation: fade-in 0.9s cubic-bezier(0.16, 1, 0.3, 1) 0.7s both;
}

.page-footer p {
    font-size: 0.75rem;
    color: rgb(100, 116, 139);
}

/* ===== RESPONSIVE ADJUSTMENTS ===== */
@media (max-width: 640px) {
    .login-card {
        border-radius: 2rem;
    }
    
    .form-panel {
        padding: 1.75rem;
    }
    
    .form-header h3 {
        font-size: 1.75rem;
    }
    
    .form-options {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .social-login {
        grid-template-columns: 1fr;
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

/* Focus visible states */
.input-field:focus-visible,
.checkbox-label:focus-visible,
.forgot-link:focus-visible,
.submit-btn:focus-visible,
.social-btn:focus-visible,
.register-link .link:focus-visible {
    outline: 2px solid rgb(59, 130, 246);
    outline-offset: 2px;
}

/* ===== PRINT STYLES ===== */
@media print {
    .bg-layer,
    .social-login,
    .divider,
    .register-link,
    .page-footer {
        display: none !important;
    }
    
    .login-container {
        background: white;
        color: black;
    }
}
</style>