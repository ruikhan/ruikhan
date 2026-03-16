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
/* =====================================================================
   APPLE-PREMIUM DESIGN SYSTEM
   Inspired by macOS Sequoia · iOS 18 · Apple Intelligence UI
   Font: SF Pro Display stack · Accent: #0071e3
   ===================================================================== */

/* ── Design Tokens ──────────────────────────────────────────────────── */
:root {
    --apple-blue:        #0071e3;
    --apple-blue-hover:  #0077ed;
    --apple-blue-light:  rgba(0, 113, 227, 0.18);
    --apple-blue-glow:   rgba(0, 113, 227, 0.35);

    --surface-0:         #000000;
    --surface-1:         #0a0a0a;
    --surface-2:         #141414;
    --surface-3:         #1c1c1e;
    --surface-4:         #2c2c2e;

    --glass-bg:          rgba(20, 20, 22, 0.72);
    --glass-border:      rgba(255, 255, 255, 0.09);
    --glass-border-hover:rgba(255, 255, 255, 0.16);

    --text-primary:      #f5f5f7;
    --text-secondary:    rgba(245, 245, 247, 0.55);
    --text-tertiary:     rgba(245, 245, 247, 0.32);

    --radius-xs:  0.5rem;
    --radius-sm:  0.875rem;
    --radius-md:  1.25rem;
    --radius-lg:  1.75rem;
    --radius-xl:  2.5rem;

    --ease-spring: cubic-bezier(0.34, 1.56, 0.64, 1);
    --ease-out:    cubic-bezier(0.16, 1, 0.3, 1);
    --ease-std:    cubic-bezier(0.4, 0, 0.2, 1);
}

/* ── Reset helpers ──────────────────────────────────────────────────── */
* {
    backface-visibility: hidden;
    -webkit-backface-visibility: hidden;
}

/* ── Root container ─────────────────────────────────────────────────── */
.login-container {
    position: relative;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1.25rem;
    background: var(--surface-0);
    color: var(--text-primary);
    font-family: -apple-system, 'SF Pro Display', 'SF Pro Text',
                 'Helvetica Neue', sans-serif;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    overflow: hidden;
}

.login-container.initial-load {
    opacity: 0;
    animation: container-reveal 0.7s var(--ease-out) 0.05s forwards;
}

@keyframes container-reveal {
    from { opacity: 0; transform: scale(0.995); }
    to   { opacity: 1; transform: scale(1); }
}

/* ── Background ─────────────────────────────────────────────────────── */
.bg-layer {
    position: fixed;
    inset: 0;
    z-index: 1;
    overflow: hidden;
}

.bg-base {
    position: absolute;
    inset: 0;
    /* Soft charcoal-to-void gradient — Apple Dark mode depth */
    background:
        radial-gradient(ellipse 80% 60% at 20% 20%,  rgba(0, 60, 120, 0.22) 0%, transparent 60%),
        radial-gradient(ellipse 60% 50% at 80% 80%,  rgba(60, 0, 100, 0.14) 0%, transparent 55%),
        radial-gradient(ellipse 100% 100% at 50% 0%, rgba(0, 20, 50, 0.5)   0%, var(--surface-0) 70%);
}

/* Parallax orbs — subtle, not neon */
.bg-mesh { position: absolute; inset: 0; }

.mesh-orb {
    position: absolute;
    border-radius: 50%;
    filter: blur(120px);
    will-change: transform;
    animation: orb-breathe 40s ease-in-out infinite;
}

.mesh-1 {
    top: 5%; left: 10%;
    width: 55vw; height: 55vw;
    max-width: 700px; max-height: 700px;
    background: radial-gradient(circle, rgba(0, 90, 180, 0.18) 0%, transparent 65%);
}
.mesh-2 {
    bottom: 5%; right: 5%;
    width: 45vw; height: 45vw;
    max-width: 600px; max-height: 600px;
    background: radial-gradient(circle, rgba(80, 0, 160, 0.12) 0%, transparent 65%);
    animation-delay: 12s;
}
.mesh-3 {
    top: 40%; left: 45%;
    width: 35vw; height: 35vw;
    max-width: 500px; max-height: 500px;
    background: radial-gradient(circle, rgba(0, 120, 200, 0.1) 0%, transparent 65%);
    animation-delay: 22s;
}

@keyframes orb-breathe {
    0%, 100% { transform: translate(0, 0) scale(1); }
    33%       { transform: translate(30px, -25px) scale(1.06); }
    66%       { transform: translate(-20px, 20px) scale(0.95); }
}

/* Precision dot grid — Apple-esque subtle geometry */
.bg-grid {
    position: absolute;
    inset: 0;
    background-image:
        radial-gradient(circle, rgba(255,255,255,0.07) 1px, transparent 1px);
    background-size: 36px 36px;
    mask-image: radial-gradient(ellipse 70% 60% at 50% 50%,
                                black 0%, transparent 100%);
    animation: grid-breathe 18s ease-in-out infinite;
}

@keyframes grid-breathe {
    0%, 100% { opacity: 0.25; }
    50%       { opacity: 0.45; }
}

/* Film-grain noise — gives depth, Apple uses this on blur surfaces */
.bg-noise {
    position: absolute;
    inset: 0;
    background-image: url('data:image/svg+xml,%3Csvg viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"%3E%3Cfilter id="n"%3E%3CfeTurbulence type="fractalNoise" baseFrequency="0.75" numOctaves="4" stitchTiles="stitch"/%3E%3C/filter%3E%3Crect width="100%25" height="100%25" filter="url(%23n)" opacity="0.035"/%3E%3C/svg%3E');
    opacity: 0.06;
    mix-blend-mode: screen;
    pointer-events: none;
}

/* Particles — very faint, like dust motes */
.bg-particles { position: absolute; inset: 0; overflow: hidden; }

.particle {
    position: absolute;
    width: 1.5px;
    height: 1.5px;
    background: rgba(120, 180, 255, 0.3);
    border-radius: 50%;
    animation: particle-drift linear infinite;
    box-shadow: 0 0 4px rgba(120, 180, 255, 0.25);
}

@keyframes particle-drift {
    0%   { bottom: -5%;  opacity: 0; }
    8%   { opacity: 0.6; }
    92%  { opacity: 0.4; }
    100% { bottom: 108%; opacity: 0; }
}

/* Waves — very low opacity, just a shimmer */
.bg-waves {
    position: absolute;
    top: 0; left: 0;
    width: 100%; height: 60vh;
    opacity: 0.18;
    overflow: hidden;
}

.wave { position: absolute; width: 200%; height: 100%; }
.wave-1 { animation: wave-slide 40s linear infinite; }
.wave-2 { animation: wave-slide 50s linear infinite reverse; opacity: 0.6; }

@keyframes wave-slide {
    from { transform: translateX(0); }
    to   { transform: translateX(-50%); }
}

/* ── Content wrapper ────────────────────────────────────────────────── */
.content-wrapper {
    position: relative;
    z-index: 10;
    width: 100%;
    max-width: 1080px;
}

/* ── Login card — Apple "sheet" language ────────────────────────────── */
.login-card {
    position: relative;
    display: grid;
    grid-template-columns: 1fr;
    background: var(--glass-bg);
    border: 1px solid var(--glass-border);
    border-radius: var(--radius-xl);
    overflow: hidden;
    /* Signature Apple layered shadow */
    box-shadow:
        0 0   0 1px rgba(255,255,255,0.04) inset,
        0 1px 0 0   rgba(255,255,255,0.08) inset,
        0 24px 48px -12px rgba(0,0,0,0.8),
        0 48px 96px -24px rgba(0,0,0,0.6),
        0  0  120px  0    rgba(0, 70, 160, 0.12);
    backdrop-filter: blur(60px) saturate(160%);
    -webkit-backdrop-filter: blur(60px) saturate(160%);
    animation: sheet-up 0.9s var(--ease-out) forwards;
}

@media (min-width: 768px) {
    .login-card { grid-template-columns: 5fr 7fr; }
}

@keyframes sheet-up {
    from { opacity: 0; transform: translateY(28px) scale(0.97); }
    to   { opacity: 1; transform: translateY(0)   scale(1); }
}

/* Ambient top-edge highlight */
.card-glass {
    position: absolute;
    inset: 0;
    pointer-events: none;
    background: linear-gradient(180deg,
        rgba(255,255,255,0.055) 0%,
        rgba(255,255,255,0.01)  18%,
        transparent             100%);
    border-radius: inherit;
}

/* Halo glow around card edges */
.card-border-glow {
    position: absolute;
    inset: -1px;
    border-radius: var(--radius-xl);
    background: linear-gradient(135deg,
        rgba(0, 113, 227, 0.25),
        rgba(100, 60, 220, 0.12),
        rgba(0, 113, 227, 0.15));
    filter: blur(12px);
    opacity: 0;
    z-index: -1;
    animation: halo-pulse 5s ease-in-out infinite;
}

@keyframes halo-pulse {
    0%, 100% { opacity: 0.2; }
    50%       { opacity: 0.5; }
}

/* Single specular sweep */
.card-shine {
    position: absolute;
    top: 0; left: -120%;
    width: 80%; height: 100%;
    background: linear-gradient(105deg,
        transparent,
        rgba(255,255,255,0.028),
        transparent);
    pointer-events: none;
    animation: specular-sweep 14s ease-in-out infinite;
}

@keyframes specular-sweep {
    0%   { left: -120%; }
    40%  { left: 160%; }
    100% { left: 160%; }
}

/* ── Info panel (left) ──────────────────────────────────────────────── */
.info-panel {
    position: relative;
    display: none;
    padding: 3rem 2.5rem;
    overflow: hidden;
}

@media (min-width: 768px) {
    .info-panel { display: flex; flex-direction: column; justify-content: space-between; }
}

/* Thin separator */
.panel-bg {
    position: absolute;
    inset: 0;
    background: linear-gradient(160deg,
        rgba(255,255,255,0.03) 0%,
        rgba(255,255,255,0.01) 50%,
        transparent 100%);
    border-right: 1px solid rgba(255,255,255,0.06);
}

/* Subtle cross-hatch — very Apple micro-texture */
.panel-pattern {
    position: absolute;
    inset: 0;
    background-image:
        linear-gradient(rgba(255,255,255,0.018) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255,255,255,0.018) 1px, transparent 1px);
    background-size: 32px 32px;
    mask-image: radial-gradient(ellipse 80% 80% at 50% 50%,
                                black 30%, transparent 100%);
    animation: texture-drift 60s linear infinite;
}

@keyframes texture-drift {
    to { transform: translate(32px, 32px); }
}

/* Radial glow top-left */
.panel-glow {
    position: absolute;
    top: -30%; left: -20%;
    width: 160%; height: 100%;
    background: radial-gradient(circle at 30% 30%,
        rgba(0, 80, 180, 0.1) 0%, transparent 55%);
    filter: blur(60px);
    animation: glow-slow-spin 30s linear infinite;
}

@keyframes glow-slow-spin {
    from { transform: rotate(0deg); }
    to   { transform: rotate(360deg); }
}

.panel-content {
    position: relative;
    z-index: 10;
    display: flex;
    flex-direction: column;
    gap: 2.25rem;
}

/* ── Status badge ───────────────────────────────────────────────────── */
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.625rem;
    padding: 0.55rem 1rem 0.55rem 0.75rem;
    background: rgba(0, 113, 227, 0.1);
    border: 1px solid rgba(0, 113, 227, 0.22);
    border-radius: 9999px;
    width: fit-content;
    animation: badge-float 4s ease-in-out infinite;
    box-shadow: 0 4px 24px rgba(0, 113, 227, 0.15);
    backdrop-filter: blur(20px);
}

@keyframes badge-float {
    0%, 100% { transform: translateY(0); }
    50%       { transform: translateY(-6px); }
}

.status-pulse {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 0.875rem;
    height: 0.875rem;
}

.pulse-ring {
    position: absolute;
    inset: 0;
    border-radius: 50%;
    background: #34aadc;
    animation: ring-ping 2.8s ease-out infinite;
}

@keyframes ring-ping {
    0%        { transform: scale(0.9); opacity: 1; }
    80%, 100% { transform: scale(2.4); opacity: 0; }
}

.pulse-dot {
    position: relative;
    width: 6px; height: 6px;
    border-radius: 50%;
    background: #34aadc;
    box-shadow: 0 0 10px #34aadc;
}

.status-text {
    font-size: 0.6875rem;
    font-weight: 600;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: #6eb9f7;
}

/* ── Panel title ────────────────────────────────────────────────────── */
.panel-title {
    animation: slide-left 0.8s var(--ease-out) both;
}

@keyframes slide-left {
    from { opacity: 0; transform: translateX(-24px); }
    to   { opacity: 1; transform: translateX(0); }
}

.panel-title h1 {
    font-size: 1.875rem;
    font-weight: 700;
    line-height: 1.18;
    letter-spacing: -0.03em;
    color: var(--text-primary);
    margin-bottom: 0.625rem;
}

/* Apple-style gradient text — cooler, less saturated */
.title-gradient {
    display: block;
    background: linear-gradient(135deg,
        #5eb5f7 0%,
        #7b8ff5 45%,
        #5eb5f7 100%);
    background-size: 200% auto;
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
    animation: gradient-cycle 8s linear infinite;
}

@keyframes gradient-cycle {
    to { background-position: 200% center; }
}

.panel-title p {
    font-size: 0.875rem;
    font-weight: 400;
    color: var(--text-secondary);
    letter-spacing: 0.01em;
}

/* ── Feature cards ──────────────────────────────────────────────────── */
.feature-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.feature-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem 1.125rem;
    background: rgba(255,255,255,0.025);
    border: 1px solid rgba(255,255,255,0.07);
    border-radius: var(--radius-md);
    backdrop-filter: blur(20px);
    transition:
        background  0.3s var(--ease-std),
        border-color 0.3s var(--ease-std),
        transform   0.35s var(--ease-spring);
    animation: item-rise 0.8s var(--ease-out) both;
    cursor: default;
}

@keyframes item-rise {
    from { opacity: 0; transform: translateY(18px) scale(0.97); }
    to   { opacity: 1; transform: translateY(0)   scale(1); }
}

.feature-item:hover {
    background: rgba(255,255,255,0.045);
    border-color: rgba(255,255,255,0.13);
    transform: translateX(4px);
}

/* Icon wrapper */
.feature-icon-wrapper { position: relative; flex-shrink: 0; }

.feature-icon {
    position: relative;
    width: 2.5rem; height: 2.5rem;
    border-radius: 0.875rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    transition: transform 0.35s var(--ease-spring);
    overflow: hidden;
}

.icon-glow {
    position: absolute;
    inset: -60%;
    border-radius: 50%;
    filter: blur(18px);
    opacity: 0.5;
    animation: icon-pulse 3s ease-in-out infinite;
}

@keyframes icon-pulse {
    0%, 100% { opacity: 0.35; }
    50%       { opacity: 0.6; }
}

.feature-item:hover .feature-icon { transform: scale(1.12) rotate(6deg); }

/* Icon color variants — desaturated Apple palette */
.feature-icon.blue    { background: rgba(0, 113, 227, 0.15); box-shadow: 0 6px 24px rgba(0, 113, 227, 0.18); }
.feature-icon.blue    .icon-glow { background: radial-gradient(circle, rgba(0,113,227,0.7), transparent); }
.feature-icon.emerald { background: rgba(48, 209, 88, 0.13);  box-shadow: 0 6px 24px rgba(48,209,88,0.15); }
.feature-icon.emerald .icon-glow { background: radial-gradient(circle, rgba(48,209,88,0.6), transparent); }
.feature-icon.purple  { background: rgba(191, 90, 242, 0.13); box-shadow: 0 6px 24px rgba(191,90,242,0.15); }
.feature-icon.purple  .icon-glow { background: radial-gradient(circle, rgba(191,90,242,0.6), transparent); }

/* Ring ripple */
.icon-ring {
    position: absolute;
    inset: -6px;
    border: 1.5px solid rgba(255,255,255,0.2);
    border-radius: 1rem;
    opacity: 0;
    animation: ring-expand 3s ease-out infinite;
}

@keyframes ring-expand {
    0%   { transform: scale(0.85); opacity: 0.5; }
    100% { transform: scale(1.5);  opacity: 0; }
}

.feature-text h3 {
    font-size: 0.9rem;
    font-weight: 600;
    letter-spacing: -0.01em;
    color: var(--text-primary);
    margin-bottom: 0.2rem;
}

.feature-text p {
    font-size: 0.75rem;
    color: var(--text-secondary);
    font-weight: 400;
}

/* ── Promo banner ───────────────────────────────────────────────────── */
.promo-banner {
    position: relative;
    padding: 1.5rem 1.625rem;
    border: 1px solid rgba(0, 113, 227, 0.25);
    border-radius: var(--radius-lg);
    overflow: hidden;
    animation: item-rise 0.8s var(--ease-out) 0.4s both;
    transition: transform 0.35s var(--ease-spring), box-shadow 0.35s;
    cursor: default;
}

.promo-banner:hover {
    transform: scale(1.025);
    box-shadow: 0 12px 40px rgba(0, 113, 227, 0.2);
}

.banner-bg {
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg,
        rgba(0, 113, 227, 0.14),
        rgba(80, 50, 200, 0.12));
    backdrop-filter: blur(20px);
}

.banner-shine {
    position: absolute;
    inset: 0;
    background: linear-gradient(105deg,
        transparent 20%,
        rgba(255,255,255,0.12) 50%,
        transparent 80%);
    transform: translateX(-100%);
    animation: banner-sweep 5s ease-in-out infinite;
}

@keyframes banner-sweep {
    0%   { transform: translateX(-100%); }
    40%  { transform: translateX(100%); }
    100% { transform: translateX(100%); }
}

.banner-sparkles { position: absolute; inset: 0; pointer-events: none; }

.sparkle {
    position: absolute;
    font-size: 0.875rem;
    animation: sparkle-pop 3s ease-in-out infinite;
}
.sparkle:nth-child(1) { top: 14%; right: 18%; animation-delay: 0s; }
.sparkle:nth-child(2) { bottom: 20%; right: 12%; animation-delay: 1s; }
.sparkle:nth-child(3) { top: 58%; left: 12%; animation-delay: 2s; }

@keyframes sparkle-pop {
    0%, 100% { opacity: 0.15; transform: scale(0.6) rotate(0deg); }
    50%       { opacity: 0.8;  transform: scale(1.2) rotate(180deg); }
}

.banner-content { position: relative; z-index: 10; }

.banner-content h3 {
    font-size: 0.9375rem;
    font-weight: 700;
    letter-spacing: -0.01em;
    color: var(--text-primary);
    margin-bottom: 0.3rem;
}

.banner-content p {
    font-size: 0.8rem;
    color: #6eb9f7;
    font-weight: 400;
}

/* ── Form panel (right) ─────────────────────────────────────────────── */
.form-panel {
    padding: 2rem 1.75rem;
    position: relative;
}

@media (min-width: 640px) { .form-panel { padding: 2.5rem 2.75rem; } }
@media (min-width: 768px) { .form-panel { padding: 3.25rem 3.5rem; } }

/* ── Mobile header ──────────────────────────────────────────────────── */
.mobile-header {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.875rem;
    margin-bottom: 2.25rem;
    animation: fade-scale 0.8s var(--ease-out);
}

@media (min-width: 768px) { .mobile-header { display: none; } }

@keyframes fade-scale {
    from { opacity: 0; transform: scale(0.92); }
    to   { opacity: 1; transform: scale(1); }
}

.mobile-logo {
    position: relative;
    width: 4.25rem; height: 4.25rem;
    border-radius: 1.5rem;
    background: linear-gradient(145deg, #0a60c8, #1a3a8f);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.1rem;
    /* Apple app-icon shadow */
    box-shadow:
        0 12px 40px rgba(0, 90, 200, 0.45),
        0  2px  6px rgba(0,0,0,0.4),
        0  0   0 1px rgba(255,255,255,0.1) inset;
    cursor: pointer;
    transition: transform 0.4s var(--ease-spring);
    overflow: hidden;
}

.mobile-logo:hover { transform: scale(1.08) rotate(-4deg); }

.logo-rings { position: absolute; inset: -8px; }

.logo-ring {
    position: absolute;
    inset: 0;
    border: 1.5px solid rgba(255,255,255,0.2);
    border-radius: 1.75rem;
    animation: logo-ring-out 2.5s ease-out infinite;
}
.ring-2 { animation-delay: 0.5s; }

@keyframes logo-ring-out {
    0%   { transform: scale(1);   opacity: 0.5; }
    100% { transform: scale(1.6); opacity: 0; }
}

.logo-glow {
    position: absolute;
    inset: -30%;
    background: radial-gradient(circle, rgba(0, 113, 227, 0.55), transparent);
    filter: blur(25px);
    animation: logo-glow-breathe 2s ease-in-out infinite;
}

@keyframes logo-glow-breathe {
    0%, 100% { opacity: 0.5; }
    50%       { opacity: 0.9; }
}

.mobile-header h2 {
    font-size: 1.5rem;
    font-weight: 700;
    letter-spacing: -0.025em;
    color: var(--text-primary);
}

.mobile-subtitle {
    font-size: 0.8rem;
    color: var(--text-secondary);
    font-weight: 400;
    letter-spacing: 0.01em;
}

/* ── Form header ────────────────────────────────────────────────────── */
.form-header {
    margin-bottom: 2.25rem;
    text-align: center;
    animation: fade-up 0.8s var(--ease-out) 0.12s both;
}

@media (min-width: 768px) { .form-header { text-align: left; } }

.form-header h3 {
    font-size: 2rem;
    font-weight: 700;
    letter-spacing: -0.04em;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
    /* Apple headline weight */
    line-height: 1.12;
}

.form-header p {
    font-size: 0.9rem;
    color: var(--text-secondary);
    font-weight: 400;
    letter-spacing: 0.005em;
}

@keyframes fade-up {
    from { opacity: 0; transform: translateY(14px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* ── Status message ─────────────────────────────────────────────────── */
.status-message {
    position: relative;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem 1.125rem;
    margin-bottom: 1.75rem;
    border: 1px solid rgba(48, 209, 88, 0.35);
    border-radius: var(--radius-md);
    color: rgb(100, 220, 130);
    font-size: 0.875rem;
    font-weight: 500;
    overflow: hidden;
}

.status-message-bg {
    position: absolute;
    inset: 0;
    background: rgba(48, 209, 88, 0.08);
    backdrop-filter: blur(16px);
}

.status-icon {
    position: relative;
    z-index: 10;
    width: 1.375rem; height: 1.375rem;
    border-radius: 50%;
    background: rgb(48, 209, 88);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.8rem;
    flex-shrink: 0;
    box-shadow: 0 0 16px rgba(48, 209, 88, 0.5);
}

/* Transitions */
.status-enter-active { animation: drop-in  0.5s var(--ease-spring); }
.status-leave-active { animation: lift-out 0.35s var(--ease-std); }

@keyframes drop-in  {
    from { opacity: 0; transform: translateY(-12px) scale(0.97); }
    to   { opacity: 1; transform: translateY(0)     scale(1); }
}
@keyframes lift-out {
    to   { opacity: 0; transform: translateY(-12px) scale(0.97); }
}

/* ── Login form ─────────────────────────────────────────────────────── */
.login-form {
    display: flex;
    flex-direction: column;
    gap: 1.625rem;
    animation: fade-up 0.8s var(--ease-out) 0.25s both;
}

/* ── Input group ────────────────────────────────────────────────────── */
.input-group { display: flex; flex-direction: column; gap: 0.5rem; }

.input-label {
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: var(--text-tertiary);
    padding-left: 0.25rem;
}

.input-wrapper {
    position: relative;
    transition: transform 0.3s var(--ease-spring);
}

/* Frosted fill — Apple input style */
.input-bg {
    position: absolute;
    inset: 0;
    background: rgba(255,255,255,0.04);
    border-radius: var(--radius-sm);
    transition: background 0.3s;
}

.input-wrapper:hover .input-bg      { background: rgba(255,255,255,0.06); }
.input-wrapper.focused .input-bg    { background: rgba(0, 113, 227, 0.07); }

/* Focus radiance */
.input-glow {
    position: absolute;
    inset: -10px;
    border-radius: var(--radius-md);
    background: radial-gradient(ellipse, rgba(0,113,227,0.25), transparent 70%);
    filter: blur(14px);
    opacity: 0;
    transition: opacity 0.3s;
    pointer-events: none;
}

.input-wrapper.focused .input-glow { opacity: 1; }

.input-icon {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    font-size: 1.1rem;
    color: var(--text-tertiary);
    transition: all 0.3s var(--ease-spring);
    pointer-events: none;
    z-index: 10;
}

.input-wrapper.focused .input-icon {
    color: #6eb9f7;
    transform: translateY(-50%) scale(1.1) rotate(-8deg);
}

.input-field {
    position: relative;
    z-index: 5;
    width: 100%;
    padding: 1rem 1rem 1rem 3rem;
    background: transparent;
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: var(--radius-sm);
    color: var(--text-primary);
    font-family: inherit;
    font-size: 0.9375rem;
    font-weight: 400;
    letter-spacing: 0.005em;
    outline: none;
    transition: border-color 0.25s;
    caret-color: var(--apple-blue);
}

.input-field::placeholder { color: var(--text-tertiary); }
.input-field:hover         { border-color: rgba(255,255,255,0.14); }
.input-field:focus         { border-color: transparent; }

/* Animated border ring on focus */
.input-border {
    position: absolute;
    inset: -1px;
    border-radius: var(--radius-sm);
    background: linear-gradient(135deg,
        rgba(0,113,227,0.7),
        rgba(120,80,220,0.5),
        rgba(0,113,227,0.7));
    background-size: 200% 200%;
    opacity: 0;
    transition: opacity 0.25s;
    pointer-events: none;
    z-index: 1;
}

.input-wrapper.focused .input-border {
    opacity: 1;
    animation: border-flow 2.5s linear infinite;
}

@keyframes border-flow {
    0%   { background-position: 0%   50%; }
    50%  { background-position: 100% 50%; }
    100% { background-position: 0%   50%; }
}

.toggle-password {
    position: absolute;
    right: 0.875rem;
    top: 50%;
    transform: translateY(-50%);
    z-index: 10;
    font-size: 1.1rem;
    color: var(--text-tertiary);
    padding: 0.4rem;
    border-radius: var(--radius-xs);
    transition: all 0.3s var(--ease-spring);
    overflow: hidden;
}

.toggle-bg {
    position: absolute;
    inset: 0;
    background: rgba(255,255,255,0.06);
    border-radius: inherit;
    opacity: 0;
    transition: opacity 0.25s;
}

.toggle-password:hover .toggle-bg   { opacity: 1; }
.toggle-password:hover               { color: #6eb9f7; transform: translateY(-50%) scale(1.12); }
.toggle-password:active              { transform: translateY(-50%) scale(0.95); }

/* ── Input error ────────────────────────────────────────────────────── */
.input-error {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.625rem 0.875rem;
    background: rgba(255, 69, 58, 0.1);
    border: 1px solid rgba(255, 69, 58, 0.28);
    border-radius: var(--radius-xs);
    color: #ff6961;
    font-size: 0.8rem;
    font-weight: 500;
}

.error-enter-active { animation: shake-x 0.45s ease; }
.error-leave-active { animation: fade-out-up 0.3s ease; }

@keyframes shake-x {
    0%, 100% { transform: translateX(0); }
    15%, 55%  { transform: translateX(-5px); }
    35%, 75%  { transform: translateX(5px); }
}

@keyframes fade-out-up {
    to { opacity: 0; transform: translateY(-8px); }
}

/* ── Form options ───────────────────────────────────────────────────── */
.form-options {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 0.625rem;
    cursor: pointer;
    transition: transform 0.2s;
}

.checkbox-label:active { transform: scale(0.97); }

.checkbox-input { position: absolute; opacity: 0; pointer-events: none; }

.checkbox-box {
    position: relative;
    width: 1.125rem; height: 1.125rem;
    border: 1.5px solid rgba(255,255,255,0.25);
    border-radius: 0.375rem;
    background: transparent;
    transition: all 0.35s var(--ease-spring);
    overflow: hidden;
}

.checkbox-glow {
    position: absolute;
    inset: -60%;
    background: radial-gradient(circle, rgba(0,113,227,0.7), transparent);
    filter: blur(12px);
    opacity: 0;
    transition: opacity 0.3s;
}

.checkbox-input:checked + .checkbox-box .checkbox-glow { opacity: 1; }
.checkbox-input:checked + .checkbox-box {
    background: linear-gradient(135deg, var(--apple-blue), #4080f0);
    border-color: var(--apple-blue);
    transform: scale(1.08);
}
.checkbox-label:hover .checkbox-box { border-color: rgba(0,113,227,0.6); }

.checkbox-check {
    position: absolute;
    inset: 0;
    width: 1.125rem; height: 1.125rem;
    color: white;
    opacity: 0;
    transition: opacity 0.25s;
    pointer-events: none;
}

.checkbox-input:checked + .checkbox-box .checkbox-check {
    opacity: 1;
    animation: check-snap 0.35s var(--ease-spring);
}

@keyframes check-snap {
    0%   { transform: scale(0.3) rotate(-10deg); }
    60%  { transform: scale(1.2); }
    100% { transform: scale(1); }
}

.checkbox-text {
    font-size: 0.875rem;
    color: var(--text-secondary);
    font-weight: 400;
    transition: color 0.25s;
}
.checkbox-label:hover .checkbox-text { color: var(--text-primary); }

.forgot-link {
    font-size: 0.875rem;
    font-weight: 500;
    color: #5eb5f7;
    letter-spacing: 0.005em;
    transition: color 0.25s, transform 0.25s;
    white-space: nowrap;
}
.forgot-link:hover { color: #93d0fb; transform: translateX(1px); }

/* ── Submit button — Apple CTA pill ─────────────────────────────────── */
.submit-btn {
    position: relative;
    width: 100%;
    padding: 1.0625rem 1.5rem;
    border-radius: var(--radius-sm);
    overflow: hidden;
    transition:
        transform  0.35s var(--ease-spring),
        box-shadow 0.35s;
}

.submit-btn:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 12px 36px rgba(0,113,227,0.45);
}

.submit-btn:active:not(:disabled) { transform: translateY(0) scale(0.98); }
.submit-btn:disabled { opacity: 0.5; cursor: not-allowed; }

.btn-bg {
    position: absolute;
    inset: 0;
    /* Apple's blue — flat, confident, no rainbow */
    background: linear-gradient(155deg, #1a78e6 0%, #0058bf 100%);
    transition: transform 0.4s var(--ease-std);
}

.submit-btn:hover:not(:disabled) .btn-bg { transform: scale(1.05); }

/* Subtle ambient glow under button */
.btn-glow {
    position: absolute;
    inset: -30%;
    background: radial-gradient(circle, rgba(0,113,227,0.5), transparent 65%);
    filter: blur(30px);
    opacity: 0;
    transition: opacity 0.35s;
    pointer-events: none;
}
.submit-btn:hover:not(:disabled) .btn-glow { opacity: 1; }

/* Specular highlight */
.btn-shine {
    position: absolute;
    top: 0; left: 0;
    width: 100%; height: 50%;
    background: linear-gradient(180deg,
        rgba(255,255,255,0.14) 0%,
        transparent 100%);
    pointer-events: none;
}

.btn-content {
    position: relative;
    z-index: 10;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    color: #fff;
    font-size: 0.9375rem;
    font-weight: 600;
    letter-spacing: -0.01em;
}

.btn-loading {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.spinner {
    width: 1.125rem; height: 1.125rem;
    animation: spin 1s linear infinite;
}

@keyframes spin { to { transform: rotate(360deg); } }

.spinner-track { fill: none; stroke: currentColor; opacity: 0.22; }
.spinner-fill  { fill: none; stroke: currentColor; stroke-dasharray: 63; stroke-dashoffset: 47; }

.btn-arrow {
    width: 1.125rem; height: 1.125rem;
    transition: transform 0.35s var(--ease-spring);
    opacity: 0.9;
}
.submit-btn:hover:not(:disabled) .btn-arrow { transform: translateX(4px); }

/* ── Divider ────────────────────────────────────────────────────────── */
.divider {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin: 2.25rem 0 1.75rem;
}

.divider-line {
    flex: 1;
    height: 1px;
    background: linear-gradient(to right,
        transparent,
        rgba(255,255,255,0.1),
        transparent);
}

.divider-text {
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.12em;
    color: var(--text-tertiary);
    white-space: nowrap;
}

/* ── Social login ───────────────────────────────────────────────────── */
.social-login {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 0.875rem;
    animation: fade-up 0.8s var(--ease-out) 0.4s both;
}

.social-btn {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.625rem;
    padding: 0.875rem 1rem;
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: var(--radius-sm);
    color: var(--text-secondary);
    font-size: 0.875rem;
    font-weight: 500;
    letter-spacing: 0.01em;
    transition:
        background  0.3s,
        border-color 0.3s,
        transform   0.35s var(--ease-spring),
        color       0.3s;
    overflow: hidden;
}

.social-bg {
    position: absolute;
    inset: 0;
    background: rgba(255,255,255,0.025);
    transition: background 0.3s;
}
.social-btn:hover .social-bg { background: rgba(255,255,255,0.055); }

.social-shine {
    position: absolute;
    inset: 0;
    background: linear-gradient(105deg,
        transparent 30%,
        rgba(255,255,255,0.1) 50%,
        transparent 70%);
    transform: translateX(-100%);
    transition: transform 0.5s;
}
.social-btn:hover .social-shine { transform: translateX(100%); }

.social-btn:hover {
    border-color: rgba(255,255,255,0.14);
    transform: translateY(-2px);
    color: var(--text-primary);
}
.social-btn:active { transform: translateY(0) scale(0.97); }

.social-btn img {
    position: relative;
    z-index: 10;
    width: 1.25rem; height: 1.25rem;
    opacity: 0.85;
    transition: all 0.3s var(--ease-spring);
}
.social-btn:hover img { opacity: 1; transform: scale(1.12); }

/* ── Register link ──────────────────────────────────────────────────── */
.register-link {
    margin-top: 2.25rem;
    text-align: center;
    animation: fade-up 0.8s var(--ease-out) 0.5s both;
}

.register-link p {
    font-size: 0.875rem;
    color: var(--text-secondary);
    font-weight: 400;
}

.register-link .link {
    font-weight: 600;
    color: #5eb5f7;
    transition: color 0.25s;
}
.register-link .link:hover { color: #93d0fb; text-decoration: underline; }

/* ── Page footer ────────────────────────────────────────────────────── */
.page-footer {
    margin-top: 1.75rem;
    text-align: center;
    animation: fade-up 0.8s var(--ease-out) 0.6s both;
}

.page-footer p {
    font-size: 0.7rem;
    color: var(--text-tertiary);
    letter-spacing: 0.04em;
    font-weight: 400;
}

/* ── Responsive ─────────────────────────────────────────────────────── */
@media (max-width: 640px) {
    .login-card    { border-radius: var(--radius-lg); }
    .form-panel    { padding: 1.625rem; }
    .form-header h3 { font-size: 1.625rem; }

    .form-options {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.875rem;
    }

    .social-login { grid-template-columns: 1fr; }
}

/* ── Accessibility ──────────────────────────────────────────────────── */
@media (prefers-reduced-motion: reduce) {
    *, *::before, *::after {
        animation-duration:       0.01ms !important;
        animation-iteration-count: 1     !important;
        transition-duration:      0.01ms !important;
    }
}

.input-field:focus-visible,
.checkbox-label:focus-visible,
.forgot-link:focus-visible,
.submit-btn:focus-visible,
.social-btn:focus-visible,
.register-link .link:focus-visible {
    outline: 2px solid var(--apple-blue);
    outline-offset: 3px;
    border-radius: 4px;
}

/* ── Print ──────────────────────────────────────────────────────────── */
@media print {
    .bg-layer, .social-login, .divider, .register-link, .page-footer { display: none !important; }
    .login-container { background: white; color: black; }
}
</style>