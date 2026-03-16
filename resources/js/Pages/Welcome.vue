<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { onMounted, onUnmounted, ref } from 'vue';

const logoUrl = '/images/enotpili.png';

defineProps({
    canLogin:    Boolean,
    canRegister: Boolean,
});

// ── Intro State ───────────────────────────────────────────────────
const showIntro   = ref(true);
const introPhase  = ref('initial');
const currentFeature = ref(0);
const skipIntro   = ref(false);

// ── Scroll progress (low-freq reactive is fine) ───────────────────
const scrollProgress = ref(0);

// ── Precomputed shape positions (NO Math.random in template) ─────
const shapes = Array.from({ length: 12 }, (_, i) => ({
    left: `${((i * 37 + 13) % 97)}%`,
    top:  `${((i * 53 + 7)  % 93)}%`,
    delay: `${((i * 0.7) % 5).toFixed(1)}s`,
    duration: `${(8 + (i * 1.3) % 12).toFixed(1)}s`,
    type: (i % 3) + 1,
}));

const cinematicFeatures = [
    { icon: '📄', title: 'E-Documents',    subtitle: 'Instant Digital Services',
      description: 'Request barangay clearances and permits with QR verification',
      gradient: 'from-blue-500 via-blue-400 to-cyan-500',   particles: '💫' },
    { icon: '🚨', title: 'Rapid Response', subtitle: 'Emergency Services',
      description: 'Direct line to emergency with real-time geo-tagging',
      gradient: 'from-red-500 via-orange-500 to-red-400',   particles: '⚡' },
    { icon: '💳', title: 'Secure Payments', subtitle: 'Encrypted Transactions',
      description: 'Pay utilities and taxes through secure virtual gateway',
      gradient: 'from-emerald-500 via-teal-400 to-emerald-500', particles: '✨' },
    { icon: '🔒', title: 'Data Protection', subtitle: 'Military-Grade Security',
      description: 'Your information protected with advanced encryption',
      gradient: 'from-purple-500 via-indigo-400 to-purple-500', particles: '🛡️' },
];

// ── Direct DOM refs for cursor + orbs (bypass Vue reactivity) ────
const cursorEl  = ref(null);
const orbBlue   = ref(null);
const orbPurple = ref(null);
const orbCyan   = ref(null);
const orbPink   = ref(null);

let mouseRAF = null;
let scrollRAF = null;
let featureInterval = null;
const introTimeouts = [];

const handleMouseMove = (e) => {
    if (mouseRAF) return;
    mouseRAF = requestAnimationFrame(() => {
        // Cursor: direct DOM, zero Vue overhead
        if (cursorEl.value) {
            cursorEl.value.style.transform =
                `translate(${e.clientX}px, ${e.clientY}px)`;
        }
        // Orb parallax: CSS custom props on the wrapper, no Vue re-render
        const mx = e.clientX / window.innerWidth  - 0.5;
        const my = e.clientY / window.innerHeight - 0.5;
        if (orbBlue.value)   orbBlue.value.style.transform   = `translate(${mx * 60}px, ${my * 60}px)`;
        if (orbPurple.value) orbPurple.value.style.transform = `translate(${mx * -50}px, ${my * -50}px)`;
        if (orbCyan.value)   orbCyan.value.style.transform   = `translate(${mx * 40}px, ${my * -40}px)`;
        if (orbPink.value)   orbPink.value.style.transform   = `translate(${mx * -30}px, ${my * 30}px)`;
        mouseRAF = null;
    });
};

const handleScroll = () => {
    if (scrollRAF) return;
    scrollRAF = requestAnimationFrame(() => {
        const h = document.documentElement.scrollHeight - window.innerHeight;
        scrollProgress.value = h > 0 ? (window.scrollY / h) * 100 : 0;
        scrollRAF = null;
    });
};

const skipToMain = () => {
    skipIntro.value = true;
    introPhase.value = 'fadeout';
    setTimeout(() => { showIntro.value = false; }, 400);
};

const startCinematicSequence = () => {
    introTimeouts.push(setTimeout(() => {
        if (!skipIntro.value) introPhase.value = 'logo';
    }, 400));

    introTimeouts.push(setTimeout(() => {
        if (!skipIntro.value) introPhase.value = 'tagline';
    }, 2200));

    introTimeouts.push(setTimeout(() => {
        if (!skipIntro.value) {
            introPhase.value = 'features';
            featureInterval = setInterval(() => {
                if (skipIntro.value) { clearInterval(featureInterval); return; }
                currentFeature.value = (currentFeature.value + 1) % cinematicFeatures.length;
            }, 3000);
        }
    }, 5500));

    introTimeouts.push(setTimeout(() => {
        if (!skipIntro.value) {
            introPhase.value = 'fadeout';
            setTimeout(() => { showIntro.value = false; }, 800);
        }
    }, 17000));
};

onMounted(() => {
    window.addEventListener('mousemove', handleMouseMove, { passive: true });
    window.addEventListener('scroll',    handleScroll,    { passive: true });
    startCinematicSequence();
});

onUnmounted(() => {
    window.removeEventListener('mousemove', handleMouseMove);
    window.removeEventListener('scroll',    handleScroll);
    if (mouseRAF)  cancelAnimationFrame(mouseRAF);
    if (scrollRAF) cancelAnimationFrame(scrollRAF);
    if (featureInterval) clearInterval(featureInterval);
    introTimeouts.forEach(clearTimeout);
});
</script>

<template>
    <Head title="Welcome to E-PILI" />

    <!-- ── Cursor (direct DOM, no Vue style binding) ── -->
    <div ref="cursorEl" class="cursor-wrap" aria-hidden="true">
        <div class="cursor-dot"></div>
        <div class="cursor-ring"></div>
    </div>

    <!-- ── Cinematic Intro ───────────────────────────── -->
    <Transition name="intro-fade">
        <div v-if="showIntro" class="intro-overlay">

            <!-- Background -->
            <div class="intro-bg" aria-hidden="true">
                <div class="intro-mesh"></div>
                <div class="intro-shapes">
                    <div v-for="s in shapes" :key="s.left + s.top"
                         :class="`ishape ishape-${s.type}`"
                         :style="{ left: s.left, top: s.top, animationDelay: s.delay, animationDuration: s.duration }">
                    </div>
                </div>
                <svg class="intro-grid" aria-hidden="true">
                    <defs>
                        <pattern id="igrid" width="60" height="60" patternUnits="userSpaceOnUse">
                            <path d="M60 0L0 0 0 60" fill="none" stroke="rgba(59,130,246,0.2)" stroke-width="0.5"/>
                        </pattern>
                    </defs>
                    <rect width="100%" height="100%" fill="url(#igrid)"/>
                </svg>
            </div>

            <!-- Skip -->
            <button @click="skipToMain" class="skip-btn">
                <span>Skip</span>
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
                </svg>
            </button>

            <!-- Content -->
            <div class="intro-content">

                <!-- Phase: logo -->
                <Transition name="phase">
                    <div v-if="introPhase === 'initial' || introPhase === 'logo'" class="phase-logo">
                        <div class="logo-orb-wrap">
                            <div class="logo-ring logo-ring-1"></div>
                            <div class="logo-ring logo-ring-2"></div>
                            <div class="logo-ring logo-ring-3"></div>
                            <div class="logo-glow"></div>
                            <img :src="logoUrl" alt="E-PILI" class="logo-img" />
                        </div>
                        <Transition name="slide-up">
                            <h1 v-if="introPhase === 'logo'" class="logo-title">
                                <span v-for="(l, i) in 'E-PILI'.split('')" :key="i"
                                      class="logo-letter" :style="{ animationDelay: `${i * 0.08}s` }">{{ l }}</span>
                            </h1>
                        </Transition>
                    </div>
                </Transition>

                <!-- Phase: tagline -->
                <Transition name="phase">
                    <div v-if="introPhase === 'tagline'" class="phase-tagline">
                        <h2 class="tagline-top">The Future of</h2>
                        <h2 class="tagline-bottom">Digital Governance</h2>
                        <p class="tagline-sub">Transforming public service through innovation and technology</p>
                        <div class="tagline-bars">
                            <div v-for="i in 5" :key="i" class="tbar" :style="{ animationDelay: `${i * 0.08}s` }"></div>
                        </div>
                    </div>
                </Transition>

                <!-- Phase: features -->
                <Transition name="phase">
                    <div v-if="introPhase === 'features'" class="phase-features">
                        <TransitionGroup name="feat">
                            <div :key="currentFeature" class="feat-showcase">
                                <div class="feat-icon-wrap">
                                    <div class="feat-glow-bg" :class="`bg-gradient-to-r ${cinematicFeatures[currentFeature].gradient}`"></div>
                                    <span class="feat-icon">{{ cinematicFeatures[currentFeature].icon }}</span>
                                    <div class="feat-orbiters">
                                        <div v-for="i in 6" :key="i" class="orbiter"
                                             :style="{ '--angle': `${i * 60}deg`, animationDelay: `${i * 0.18}s` }">
                                            <span>{{ cinematicFeatures[currentFeature].particles }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="feat-card">
                                    <p :class="`feat-sub text-transparent bg-clip-text bg-gradient-to-r ${cinematicFeatures[currentFeature].gradient}`">
                                        {{ cinematicFeatures[currentFeature].subtitle }}
                                    </p>
                                    <h3 class="feat-title">{{ cinematicFeatures[currentFeature].title }}</h3>
                                    <p class="feat-desc">{{ cinematicFeatures[currentFeature].description }}</p>
                                </div>
                                <div class="feat-dots">
                                    <span v-for="(_, idx) in cinematicFeatures" :key="idx"
                                          :class="['fdot', idx === currentFeature && 'active',
                                                   idx === currentFeature && `bg-gradient-to-r ${cinematicFeatures[currentFeature].gradient}`]">
                                    </span>
                                </div>
                            </div>
                        </TransitionGroup>
                    </div>
                </Transition>

            </div>
        </div>
    </Transition>

    <!-- ── Main Portal ───────────────────────────────── -->
    <div class="portal">

        <!-- Background layers (all GPU-composited, no JS) -->
        <div class="portal-bg" aria-hidden="true">
            <div class="bg-mesh"></div>
            <div class="bg-orbs">
                <div ref="orbBlue"   class="orb orb-b"></div>
                <div ref="orbPurple" class="orb orb-p"></div>
                <div ref="orbCyan"   class="orb orb-c"></div>
                <div ref="orbPink"   class="orb orb-k"></div>
            </div>
            <div class="bg-hexgrid"></div>
        </div>

        <!-- Scroll bar -->
        <div class="scroll-bar-wrap" aria-hidden="true">
            <div class="scroll-bar-fill" :style="{ width: scrollProgress + '%' }"></div>
        </div>

        <!-- Nav -->
        <nav class="portal-nav">
            <div class="nav-inner">
                <Link :href="route('dashboard')" class="nav-logo-link">
                    <div class="nav-logo-img-wrap">
                        <img :src="logoUrl" alt="E-PILI" class="nav-logo-img" />
                    </div>
                    <div class="nav-logo-text">
                        <span class="nlt-name">Justine Villarosa</span>
                        <span class="nlt-role">Developer</span>
                    </div>
                </Link>
                <div v-if="canLogin" class="nav-actions">
                    <Link v-if="$page.props.auth.user" :href="route('dashboard')" class="btn-dash">
                        <span>Dashboard</span>
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </Link>
                    <template v-else>
                        <Link :href="route('login')"    class="btn-login">Sign In</Link>
                        <Link v-if="canRegister" :href="route('register')" class="btn-register">Get Started</Link>
                    </template>
                </div>
            </div>
        </nav>

        <!-- Hero -->
        <main class="hero">
            <div class="status-badge">
                <span class="status-dot"></span>
                <span class="status-ring"></span>
                <span class="status-text">System Online</span>
                <span class="status-beat">●</span>
            </div>

            <h1 class="hero-title">
                <span class="ht-line1">Governance</span>
                <span class="ht-line2">E-Portal System</span>
                <div class="ht-underline"></div>
            </h1>

            <p class="hero-desc">
                The future of public service is digital. Request documents, pay bills,
                and participate in governance through a secure, unified command center.
            </p>

            <div class="hero-cta">
                <Link :href="route('register')" class="cta-primary">
                    <span>Create Citizen Account</span>
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </Link>
                <Link :href="route('login')" class="cta-secondary">
                    <span>Access Portal</span>
                </Link>
            </div>

            <!-- Feature cards -->
            <div class="cards-grid">

                <div class="card" data-c="blue">
                    <div class="card-glow"></div>
                    <div class="card-icon">📄</div>
                    <h3 class="card-title">E-Documents</h3>
                    <p class="card-desc">Instant request processing for Barangay Clearances and Permits with digital QR verification.</p>
                    <span class="card-link">Learn More <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></span>
                </div>

                <div class="card" data-c="red">
                    <div class="card-glow"></div>
                    <div class="card-icon">🚨</div>
                    <h3 class="card-title">Rapid Response</h3>
                    <p class="card-desc">Direct line to emergency services. Report community incidents with geo-tagging.</p>
                    <span class="card-link">Learn More <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></span>
                </div>

                <div class="card" data-c="emerald">
                    <div class="card-glow"></div>
                    <div class="card-icon">💳</div>
                    <h3 class="card-title">Secure Payments</h3>
                    <p class="card-desc">Hassle-free payment for utilities and taxes through our encrypted virtual gateway.</p>
                    <span class="card-link">Learn More <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></span>
                </div>

                <div class="card" data-c="purple">
                    <div class="card-glow"></div>
                    <div class="card-icon">🎯</div>
                    <h3 class="card-title">Social Engagement</h3>
                    <p class="card-desc">Connect with your community, share ideas, and participate in local discussions.</p>
                    <Link href="/social" class="card-link">Join the Conversation <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></Link>
                </div>

            </div>
        </main>

        <!-- Footer -->
        <footer class="portal-footer">
            <div class="footer-inner">
                <span class="footer-copy">&copy; 2025 Provincial Government of Camarines Sur.</span>
                <div class="footer-links">
                    <a href="#" class="flink">Privacy</a>
                    <a href="#" class="flink">Terms</a>
                    <a href="#" class="flink">Support</a>
                </div>
            </div>
        </footer>
    </div>
</template>

<style scoped>
/* ── Reset ─────────────────────────────────────────────────────── */
/* NOTE: removed global backface-visibility:hidden — it forced
   composite layers on EVERY element and caused massive GPU overhead */

/* ── Custom Cursor ─────────────────────────────────────────────── */
.cursor-wrap {
    position: fixed;
    top: 0; left: 0;
    pointer-events: none;
    z-index: 99999;
    /* Use translate3d so it stays on its own GPU layer */
    will-change: transform;
    /* Default offsets so dot/ring are centered on the pointer */
}

.cursor-dot {
    position: absolute;
    width: 8px; height: 8px;
    background: white; border-radius: 50%;
    transform: translate(-50%, -50%);
}

.cursor-ring {
    position: absolute;
    width: 28px; height: 28px;
    border: 1.5px solid rgba(255,255,255,0.5); border-radius: 50%;
    transform: translate(-50%, -50%);
    /* Simple transition — much cheaper than Vue-bound style */
    transition: transform 0.12s ease-out, width 0.2s ease, height 0.2s ease;
    animation: c-pulse 2.5s ease-in-out infinite;
}

@keyframes c-pulse {
    0%, 100% { opacity: 0.7; }
    50%       { opacity: 0.35; }
}

/* ── Intro Overlay ─────────────────────────────────────────────── */
.intro-overlay {
    position: fixed; inset: 0; z-index: 9999;
    display: flex; align-items: center; justify-content: center;
    background: #000;
}

.intro-bg {
    position: absolute; inset: 0; overflow: hidden;
    /* contain paints to this layer */
    contain: strict;
}

.intro-mesh {
    position: absolute; inset: 0;
    background:
        radial-gradient(ellipse at 20% 30%, rgba(59,130,246,0.14) 0%, transparent 50%),
        radial-gradient(ellipse at 80% 70%, rgba(168,85,247,0.11) 0%, transparent 50%),
        radial-gradient(ellipse at 50% 50%, rgba(6,182,212,0.09) 0%, transparent 50%);
    /* slow drift — transform only, compositor-friendly */
    animation: mesh-drift 25s ease-in-out infinite;
    will-change: transform;
}
@keyframes mesh-drift {
    0%, 100% { transform: scale(1) translate(0,0); }
    50%       { transform: scale(1.06) translate(1.5%, -1.5%); }
}

.intro-shapes { position: absolute; inset: 0; opacity: 0.12; }

.ishape {
    position: absolute;
    animation: s-float 15s ease-in-out infinite;
    will-change: transform;
}
.ishape-1 { width: 80px; height: 80px; border: 1.5px solid rgba(59,130,246,0.5); border-radius: 16px; transform: rotate(45deg); }
.ishape-2 { width: 64px; height: 64px; border: 1.5px solid rgba(168,85,247,0.5); clip-path: polygon(50% 0%, 100% 50%, 50% 100%, 0% 50%); }
.ishape-3 { width: 48px; height: 48px; border: 1.5px solid rgba(6,182,212,0.5); border-radius: 50%; }

@keyframes s-float {
    0%, 100% { transform: translate(0,0) rotate(0deg); }
    33%       { transform: translate(40px,-60px) rotate(120deg); }
    66%       { transform: translate(-25px,45px) rotate(240deg); }
}

.intro-grid {
    position: absolute; inset: 0; width: 100%; height: 100%;
    opacity: 0.25;
    animation: grid-breathe 10s ease-in-out infinite;
}
@keyframes grid-breathe {
    0%, 100% { opacity: 0.2; }
    50%       { opacity: 0.4; }
}

/* Skip button */
.skip-btn {
    position: absolute; top: 1.75rem; right: 1.75rem; z-index: 10;
    display: flex; align-items: center; gap: 0.6rem;
    padding: 0.75rem 1.5rem;
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.12);
    border-radius: 9999px; color: white;
    font-size: 0.8125rem; font-weight: 700;
    letter-spacing: 0.08em; text-transform: uppercase;
    /* Single transition, no animation inside */
    transition: background 0.25s, border-color 0.25s, transform 0.25s;
    cursor: none;
}
.skip-btn:hover {
    background: rgba(255,255,255,0.1);
    border-color: rgba(96,165,250,0.5);
    transform: translateX(4px);
}

/* Intro content */
.intro-content {
    position: relative; z-index: 10;
    width: 100%; max-width: 1100px; padding: 0 2rem;
    display: flex; align-items: center; justify-content: center;
}

/* ── Logo Phase ────────────────────────────────────────────────── */
.phase-logo { text-align: center; }

.logo-orb-wrap {
    position: relative; display: inline-block; margin-bottom: 3rem;
}

.logo-ring {
    position: absolute; top: 50%; left: 50%;
    border-radius: 50%;
    border: 1.5px solid transparent;
    border-image: linear-gradient(135deg, rgba(59,130,246,0.6), rgba(168,85,247,0.4)) 1;
    will-change: transform;
}
.logo-ring-1 { width: 220px; height: 220px; transform: translate(-50%,-50%); animation: ring-spin 10s linear infinite; }
.logo-ring-2 { width: 270px; height: 270px; transform: translate(-50%,-50%); animation: ring-spin 14s linear infinite reverse; opacity: 0.5; }
.logo-ring-3 { width: 320px; height: 320px; transform: translate(-50%,-50%); animation: ring-spin 18s linear infinite; opacity: 0.3; }

@keyframes ring-spin {
    to { transform: translate(-50%,-50%) rotate(360deg); }
}

.logo-glow {
    position: absolute; top: 50%; left: 50%;
    width: 280px; height: 280px; transform: translate(-50%,-50%);
    background: radial-gradient(circle, rgba(59,130,246,0.35), transparent 70%);
    filter: blur(50px);
    animation: glow-pulse 4s ease-in-out infinite;
    will-change: opacity;
}
@keyframes glow-pulse {
    0%, 100% { opacity: 0.6; }
    50%       { opacity: 1; }
}

.logo-img {
    position: relative; height: 14rem; width: auto;
    filter: drop-shadow(0 20px 60px rgba(59,130,246,0.6));
    animation: logo-float 5s ease-in-out infinite;
    will-change: transform;
}
@keyframes logo-float {
    0%, 100% { transform: translateY(0); }
    50%       { transform: translateY(-20px); }
}

.logo-title {
    font-size: clamp(3.5rem, 9vw, 7rem); font-weight: 900;
    letter-spacing: 0.06em; color: white;
    text-shadow: 0 0 50px rgba(59,130,246,0.7), 0 0 100px rgba(168,85,247,0.3);
    display: flex; justify-content: center; gap: 0.08em;
}

.logo-letter {
    display: inline-block;
    animation: lb 0.7s cubic-bezier(0.175,0.885,0.32,1.275) both;
}
@keyframes lb {
    from { transform: translateY(60px) scale(0); opacity: 0; }
    to   { transform: translateY(0) scale(1); opacity: 1; }
}

/* ── Tagline Phase ─────────────────────────────────────────────── */
.phase-tagline { text-align: center; }

.tagline-top {
    display: block;
    font-size: clamp(2.5rem, 6vw, 5rem); font-weight: 900; color: white;
    animation: ft 0.9s cubic-bezier(0.16,1,0.3,1) both;
}
.tagline-bottom {
    display: block; margin-top: 0.5rem;
    font-size: clamp(2.5rem, 6vw, 5rem); font-weight: 900;
    background: linear-gradient(135deg, #60a5fa 0%, #a78bfa 50%, #06b6d4 100%);
    background-size: 250% 250%;
    background-clip: text; -webkit-background-clip: text; -webkit-text-fill-color: transparent;
    animation: ft 0.9s cubic-bezier(0.16,1,0.3,1) 0.15s both, grad-wave 8s ease-in-out infinite;
    filter: drop-shadow(0 8px 30px rgba(96,165,250,0.5));
}
@keyframes ft {
    from { opacity: 0; transform: translateY(30px); }
    to   { opacity: 1; transform: translateY(0); }
}
@keyframes grad-wave {
    0%, 100% { background-position: 0% 50%; }
    50%       { background-position: 100% 50%; }
}

.tagline-sub {
    margin-top: 2rem;
    font-size: clamp(1.1rem, 2vw, 1.5rem); color: rgb(203,213,225); font-weight: 300;
    animation: ft 1s ease-out 0.4s both;
}

.tagline-bars {
    display: flex; gap: 0.5rem; justify-content: center; margin-top: 2.5rem;
}
.tbar {
    width: 1.5rem; height: 4px;
    background: linear-gradient(to right, #60a5fa, #a78bfa);
    border-radius: 9999px;
    animation: tbar-pulse 1.4s ease-in-out infinite;
}
@keyframes tbar-pulse {
    0%, 100% { transform: scaleX(1); opacity: 0.5; }
    50%       { transform: scaleX(1.6); opacity: 1; }
}

/* ── Features Phase ────────────────────────────────────────────── */
.phase-features {
    width: 100%; display: flex; justify-content: center;
}

.feat-showcase {
    display: flex; flex-direction: column; align-items: center; gap: 2.5rem;
}

.feat-icon-wrap {
    position: relative; width: 14rem; height: 14rem;
    display: flex; align-items: center; justify-content: center;
}

.feat-glow-bg {
    position: absolute; inset: -3rem; border-radius: 50%;
    opacity: 0.25; filter: blur(80px);
    animation: gpulse 3.5s ease-in-out infinite;
    will-change: opacity;
}
@keyframes gpulse {
    0%, 100% { opacity: 0.25; }
    50%       { opacity: 0.5; }
}

.feat-icon {
    position: relative; font-size: 7rem;
    filter: drop-shadow(0 15px 40px rgba(59,130,246,0.6));
    animation: icon-bob 3.5s ease-in-out infinite;
    will-change: transform;
}
@keyframes icon-bob {
    0%, 100% { transform: translateY(0) rotate(0deg); }
    50%       { transform: translateY(-12px) rotate(3deg); }
}

.feat-orbiters { position: absolute; inset: 0; }

.orbiter {
    position: absolute; top: 50%; left: 50%;
    width: 100%; height: 100%;
    animation: orbit 5s linear infinite;
    will-change: transform;
}
@keyframes orbit {
    from { transform: translate(-50%,-50%) rotate(var(--angle)) translateX(100px) rotate(calc(-1 * var(--angle))); }
    to   { transform: translate(-50%,-50%) rotate(calc(var(--angle) + 360deg)) translateX(100px) rotate(calc(-1 * (var(--angle) + 360deg))); }
}
.orbiter span { font-size: 1.5rem; }

.feat-card {
    max-width: 52rem; padding: 2rem 2.5rem;
    background: rgba(255,255,255,0.03);
    /* Removed backdrop-filter on animated element — saves GPU */
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 1.75rem;
    box-shadow: 0 16px 50px rgba(0,0,0,0.35), inset 0 1px 0 rgba(255,255,255,0.08);
    text-align: center;
}

.feat-sub {
    font-size: 0.8rem; font-weight: 800; text-transform: uppercase;
    letter-spacing: 0.22em; margin-bottom: 1.2rem;
}
.feat-title {
    font-size: clamp(2.5rem, 5vw, 4.5rem); font-weight: 900; color: white; margin-bottom: 1rem;
}
.feat-desc {
    font-size: clamp(1.1rem, 2vw, 1.5rem); color: rgb(203,213,225); font-weight: 300; line-height: 1.7;
}

.feat-dots {
    display: flex; gap: 0.75rem; justify-content: center; padding-top: 1rem;
}
.fdot {
    width: 0.625rem; height: 0.625rem; border-radius: 9999px;
    background: rgba(255,255,255,0.2);
    transition: width 0.5s cubic-bezier(0.34,1.56,0.64,1), box-shadow 0.5s;
}
.fdot.active {
    width: 3.5rem;
    box-shadow: 0 0 20px currentColor;
}

/* ── Intro Transitions ─────────────────────────────────────────── */
.intro-fade-enter-active, .intro-fade-leave-active { transition: opacity 0.8s ease; }
.intro-fade-enter-from, .intro-fade-leave-to { opacity: 0; }

.phase-enter-active, .phase-leave-active { transition: all 0.9s cubic-bezier(0.16,1,0.3,1); }
.phase-enter-from { opacity: 0; transform: scale(0.92) translateY(30px); }
.phase-leave-to   { opacity: 0; transform: scale(1.06) translateY(-30px); }

.slide-up-enter-active { transition: all 1s cubic-bezier(0.16,1,0.3,1) 0.4s; }
.slide-up-enter-from   { opacity: 0; transform: translateY(50px); }

.feat-enter-active, .feat-leave-active { transition: all 0.8s cubic-bezier(0.16,1,0.3,1); }
.feat-enter-from { opacity: 0; transform: translateY(50px) scale(0.95); }
.feat-leave-to   { opacity: 0; transform: translateY(-50px) scale(1.05); }

/* ══════════════════════════════════════════════════════════════════
   MAIN PORTAL
══════════════════════════════════════════════════════════════════ */
.portal {
    position: relative; min-height: 100vh;
    background: #000; color: #f8fafc;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    -webkit-font-smoothing: antialiased;
    overflow-x: hidden;
    cursor: none;
}

/* ── Background ────────────────────────────────────────────────── */
.portal-bg {
    position: fixed; inset: 0; z-index: 1;
    /* contain to avoid triggering layout/paint in the main document */
    contain: strict;
    pointer-events: none;
}

.bg-mesh {
    position: absolute; inset: 0;
    background:
        radial-gradient(ellipse at 25% 20%, rgba(59,130,246,0.14) 0%, transparent 40%),
        radial-gradient(ellipse at 75% 80%, rgba(168,85,247,0.11) 0%, transparent 40%),
        radial-gradient(ellipse at 50% 50%, rgba(6,182,212,0.09) 0%, transparent 40%);
    animation: mesh-drift 28s ease-in-out infinite;
    will-change: transform;
}
/* reuse same mesh-drift keyframe defined above */

.bg-orbs { position: absolute; inset: 0; }

/* Orbs: position is set via JS but movement is done with CSS will-change */
.orb {
    position: absolute; border-radius: 50%;
    /* Reduced blur — was 150px, now 100px — huge GPU saving */
    filter: blur(100px);
    will-change: transform;
    /* CSS drift animation still runs, JS parallax layered on top */
    animation: orb-drift 35s ease-in-out infinite;
}
.orb-b { top: 15%; left: 20%; width: 600px; height: 600px; background: radial-gradient(circle, rgba(59,130,246,0.2), transparent 70%); }
.orb-p { bottom: 15%; right: 20%; width: 700px; height: 700px; background: radial-gradient(circle, rgba(168,85,247,0.15), transparent 70%); animation-delay: 6s; }
.orb-c { top: 50%; left: 50%; width: 500px; height: 500px; background: radial-gradient(circle, rgba(6,182,212,0.12), transparent 70%); animation-delay: 12s; }
.orb-k { top: 65%; left: 75%; width: 450px; height: 450px; background: radial-gradient(circle, rgba(236,72,153,0.1), transparent 70%); animation-delay: 18s; }

@keyframes orb-drift {
    0%, 100% { transform: translate(0,0) scale(1); }
    25%       { transform: translate(80px,-80px) scale(1.08); }
    50%       { transform: translate(-60px,60px) scale(0.92); }
    75%       { transform: translate(50px,-50px) scale(1.04); }
}

.bg-hexgrid {
    position: absolute; inset: 0;
    background-image:
        linear-gradient(30deg, rgba(59,130,246,0.035) 12%, transparent 12.5%, transparent 87%, rgba(59,130,246,0.035) 87.5%),
        linear-gradient(150deg, rgba(59,130,246,0.035) 12%, transparent 12.5%, transparent 87%, rgba(59,130,246,0.035) 87.5%);
    background-size: 80px 140px; background-position: 0 0, 40px 70px;
    mask-image: radial-gradient(ellipse at center, black 30%, transparent 70%);
    /* Removed animation — grid-flow was painting a huge area every frame */
    opacity: 0.6;
}

/* ── Scroll bar ────────────────────────────────────────────────── */
.scroll-bar-wrap {
    position: fixed; top: 0; left: 0; right: 0;
    height: 3px; z-index: 100;
    background: rgba(0,0,0,0.2);
}
.scroll-bar-fill {
    height: 100%;
    background: linear-gradient(to right, #3b82f6, #8b5cf6, #ec4899, #06b6d4);
    box-shadow: 0 0 20px rgba(59,130,246,0.6);
    /* Only transition width — much cheaper than animating background-position */
    transition: width 0.1s linear;
}

/* ── Navigation ────────────────────────────────────────────────── */
.portal-nav {
    position: fixed; top: 0; left: 0; right: 0; z-index: 50; padding: 1.25rem 1.5rem;
    animation: nav-in 0.7s cubic-bezier(0.16,1,0.3,1) both;
}
@keyframes nav-in {
    from { transform: translateY(-100%); opacity: 0; }
    to   { transform: translateY(0); opacity: 1; }
}

.nav-inner {
    position: relative; max-width: 1280px; margin: 0 auto;
    display: flex; justify-content: space-between; align-items: center;
    padding: 1rem 1.75rem; border-radius: 9999px;
    background: rgba(10,10,15,0.55);
    border: 1px solid rgba(255,255,255,0.1);
    /* Reduced blur value */
    backdrop-filter: blur(30px) saturate(160%);
    box-shadow: 0 20px 60px rgba(0,0,0,0.5), inset 0 1px 0 rgba(255,255,255,0.08);
    transition: border-color 0.3s, box-shadow 0.3s;
}
.nav-inner:hover {
    border-color: rgba(255,255,255,0.18);
    box-shadow: 0 25px 80px rgba(0,0,0,0.7), inset 0 1px 0 rgba(255,255,255,0.12);
}

.nav-logo-link {
    display: flex; align-items: center; gap: 1rem;
    transition: transform 0.3s cubic-bezier(0.34,1.56,0.64,1);
}
.nav-logo-link:hover { transform: scale(1.06); }

.nav-logo-img-wrap { width: 4.5rem; height: 4.5rem; }
.nav-logo-img {
    width: 100%; height: 100%; object-fit: contain;
    filter: drop-shadow(0 10px 30px rgba(59,130,246,0.45));
    transition: filter 0.3s;
}
.nav-logo-link:hover .nav-logo-img { filter: drop-shadow(0 15px 45px rgba(59,130,246,0.7)); }

.nav-logo-text { display: none; }
@media (min-width: 640px) {
    .nav-logo-text { display: flex; flex-direction: column; gap: 2px; }
}
.nlt-name { font-size: 0.9375rem; font-weight: 800; color: white; letter-spacing: 0.01em; }
.nlt-role { font-size: 0.6875rem; color: #60a5fa; text-transform: uppercase; letter-spacing: 0.14em; font-weight: 700; }

.btn-bpo {
    padding: 0.625rem 1.25rem; border-radius: 9999px;
    background: linear-gradient(135deg, #8b5cf6, #ec4899);
    color: white; font-size: 0.8125rem; font-weight: 700; letter-spacing: 0.02em;
    box-shadow: 0 8px 25px rgba(139,92,246,0.35);
    transition: transform 0.25s, box-shadow 0.25s;
}
.btn-bpo:hover { transform: translateY(-2px); box-shadow: 0 12px 40px rgba(139,92,246,0.55); }

.nav-actions { display: flex; align-items: center; gap: 0.875rem; }

.btn-dash, .btn-login, .btn-register {
    display: flex; align-items: center; gap: 0.6rem;
    padding: 0.625rem 1.5rem; border-radius: 9999px;
    font-size: 0.8125rem; font-weight: 700; letter-spacing: 0.02em;
    transition: transform 0.25s, box-shadow 0.25s, background 0.25s;
}
.btn-dash {
    background: linear-gradient(135deg, #3b82f6, #8b5cf6); color: white;
    box-shadow: 0 8px 30px rgba(59,130,246,0.35);
}
.btn-dash:hover { transform: translateY(-2px); box-shadow: 0 12px 45px rgba(59,130,246,0.55); }

.btn-login {
    color: rgb(226,232,240); background: rgba(255,255,255,0.06);
    border: 1px solid rgba(255,255,255,0.1);
}
.btn-login:hover { background: rgba(255,255,255,0.12); transform: translateY(-2px); border-color: rgba(255,255,255,0.2); }

.btn-register {
    background: white; color: #0f0f1e;
    box-shadow: 0 8px 30px rgba(255,255,255,0.2);
}
.btn-register:hover { transform: translateY(-2px); box-shadow: 0 12px 45px rgba(255,255,255,0.35); }

/* ── Hero ──────────────────────────────────────────────────────── */
.hero {
    position: relative; z-index: 10;
    min-height: 100vh; display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    padding: 9rem 1.5rem 5rem; text-align: center;
}

.status-badge {
    position: relative; display: inline-flex; align-items: center; gap: 0.875rem;
    padding: 0.875rem 1.75rem; margin-bottom: 2.5rem;
    background: rgba(59,130,246,0.08);
    border: 1px solid rgba(59,130,246,0.25); border-radius: 9999px;
    /* Reduced blur */
    backdrop-filter: blur(20px);
    box-shadow: 0 12px 40px rgba(59,130,246,0.2), inset 0 1px 0 rgba(255,255,255,0.08);
    animation: badge-float 4.5s ease-in-out infinite;
    will-change: transform;
}
@keyframes badge-float {
    0%, 100% { transform: translateY(0); }
    50%       { transform: translateY(-10px); }
}

.status-dot {
    position: relative; width: 0.625rem; height: 0.625rem; border-radius: 50%;
    background: #60a5fa; display: inline-block;
    box-shadow: 0 0 15px rgba(96,165,250,0.8);
}
.status-ring {
    position: absolute; left: 1.75rem; width: 0.625rem; height: 0.625rem; border-radius: 50%;
    background: #60a5fa; opacity: 0;
    animation: status-ping 2.5s ease-out infinite;
}
@keyframes status-ping {
    0%   { transform: scale(1); opacity: 0.8; }
    70%  { transform: scale(2.8); opacity: 0; }
    100% { opacity: 0; }
}
.status-text {
    font-size: 0.8rem; font-weight: 800; text-transform: uppercase;
    letter-spacing: 0.18em; color: #93c5fd;
}
.status-beat {
    color: #22c55e; font-size: 0.875rem;
    animation: beat 1.6s ease-in-out infinite;
}
@keyframes beat {
    0%, 100% { transform: scale(1); }
    50%       { transform: scale(1.35); }
}

.hero-title {
    display: flex; flex-direction: column; gap: 0.75rem; margin-bottom: 2rem;
}
.ht-line1, .ht-line2 {
    font-size: clamp(3rem, 9vw, 7.5rem); font-weight: 900;
    letter-spacing: -0.04em; line-height: 1;
    animation: word-in 0.9s cubic-bezier(0.16,1,0.3,1) backwards;
    will-change: transform, opacity;
}
.ht-line1 {
    color: white; text-shadow: 0 8px 30px rgba(0,0,0,0.4);
    animation-delay: 0.15s;
}
.ht-line2 {
    background: linear-gradient(135deg, #60a5fa 0%, #a78bfa 50%, #06b6d4 100%);
    background-size: 200% 200%; background-clip: text; -webkit-background-clip: text; -webkit-text-fill-color: transparent;
    animation-delay: 0.3s, 0s;
    /* Layered animation: word-in + gradient-shift */
    animation: word-in 0.9s cubic-bezier(0.16,1,0.3,1) 0.3s backwards, grad-wave 8s ease-in-out 1.5s infinite;
    filter: drop-shadow(0 10px 40px rgba(96,165,250,0.5));
}
@keyframes word-in {
    from { opacity: 0; transform: translateY(35px) scale(0.96); }
    to   { opacity: 1; transform: translateY(0) scale(1); }
}

.ht-underline {
    width: 10rem; height: 4px; margin: 1rem auto 0;
    background: linear-gradient(to right, transparent, #60a5fa, #a78bfa, #06b6d4, transparent);
    border-radius: 9999px; box-shadow: 0 0 25px rgba(96,165,250,0.5);
    animation: ul-pulse 4s ease-in-out infinite;
}
@keyframes ul-pulse {
    0%, 100% { width: 10rem; opacity: 0.8; }
    50%       { width: 14rem; opacity: 1; }
}

.hero-desc {
    max-width: 48rem;
    font-size: clamp(1rem, 2vw, 1.375rem); color: rgb(203,213,225);
    font-weight: 300; line-height: 1.8; margin-bottom: 3.5rem;
    animation: word-in 0.9s cubic-bezier(0.16,1,0.3,1) 0.5s backwards;
}

.hero-cta {
    display: flex; flex-wrap: wrap; gap: 1.25rem;
    justify-content: center; margin-bottom: 5rem;
    animation: word-in 0.9s cubic-bezier(0.16,1,0.3,1) 0.65s backwards;
}

.cta-primary, .cta-secondary {
    display: flex; align-items: center; gap: 0.875rem;
    padding: 1.125rem 2.25rem; border-radius: 9999px;
    font-size: 1.0625rem; font-weight: 700; letter-spacing: 0.02em;
    transition: transform 0.25s cubic-bezier(0.34,1.56,0.64,1), box-shadow 0.25s;
    overflow: hidden;
}
.cta-primary {
    background: linear-gradient(135deg, #3b82f6, #8b5cf6); color: white;
    box-shadow: 0 20px 60px rgba(59,130,246,0.45), inset 0 1px 0 rgba(255,255,255,0.15);
}
.cta-primary:hover {
    transform: translateY(-4px) scale(1.02);
    box-shadow: 0 25px 80px rgba(59,130,246,0.65), inset 0 1px 0 rgba(255,255,255,0.2);
}
.cta-primary svg { transition: transform 0.25s cubic-bezier(0.34,1.56,0.64,1); }
.cta-primary:hover svg { transform: translateX(5px); }

.cta-secondary {
    background: rgba(255,255,255,0.06); color: white;
    border: 1px solid rgba(255,255,255,0.12);
    backdrop-filter: blur(16px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.2), inset 0 1px 0 rgba(255,255,255,0.08);
}
.cta-secondary:hover {
    transform: translateY(-4px);
    background: rgba(255,255,255,0.1);
    border-color: rgba(255,255,255,0.22);
    box-shadow: 0 20px 60px rgba(0,0,0,0.3), inset 0 1px 0 rgba(255,255,255,0.12);
}

/* ── Feature Cards ─────────────────────────────────────────────── */
.cards-grid {
    display: grid; grid-template-columns: 1fr; gap: 1.75rem;
    max-width: 1280px; width: 100%;
    animation: word-in 0.9s cubic-bezier(0.16,1,0.3,1) 0.9s backwards;
}
@media (min-width: 768px)  { .cards-grid { grid-template-columns: repeat(2, 1fr); } }
@media (min-width: 1024px) { .cards-grid { grid-template-columns: repeat(4, 1fr); } }

.card {
    position: relative; padding: 2.25rem; border-radius: 2rem;
    background: rgba(255,255,255,0.04);
    border: 1px solid rgba(255,255,255,0.09);
    /* Reduced blur — was 30px */
    backdrop-filter: blur(16px) saturate(150%);
    box-shadow: 0 16px 50px rgba(0,0,0,0.2), inset 0 1px 0 rgba(255,255,255,0.08);
    /* Use translate only — avoids layout/paint on hover */
    transition: transform 0.35s cubic-bezier(0.34,1.56,0.64,1), box-shadow 0.35s, border-color 0.35s;
    overflow: hidden;
}
.card:hover {
    transform: translateY(-10px);
    border-color: rgba(255,255,255,0.18);
    box-shadow: 0 28px 80px rgba(0,0,0,0.35), inset 0 1px 0 rgba(255,255,255,0.12);
}

/* Glow per card color — opacity animates, not filter */
.card-glow {
    position: absolute; inset: 0; border-radius: 2rem;
    opacity: 0; transition: opacity 0.35s;
}
.card[data-c="blue"]    .card-glow { background: radial-gradient(circle at 50% 0%, rgba(59,130,246,0.22), transparent 65%); }
.card[data-c="red"]     .card-glow { background: radial-gradient(circle at 50% 0%, rgba(239,68,68,0.22), transparent 65%); }
.card[data-c="emerald"] .card-glow { background: radial-gradient(circle at 50% 0%, rgba(16,185,129,0.22), transparent 65%); }
.card[data-c="purple"]  .card-glow { background: radial-gradient(circle at 50% 0%, rgba(168,85,247,0.22), transparent 65%); }
.card:hover .card-glow { opacity: 1; }

.card-icon {
    font-size: 2.75rem; margin-bottom: 1.5rem; display: block;
    transition: transform 0.35s cubic-bezier(0.34,1.56,0.64,1);
}
.card:hover .card-icon { transform: translateY(-8px) scale(1.1); }

.card-title {
    font-size: 1.5rem; font-weight: 800; color: white; margin-bottom: 0.875rem;
    transition: color 0.3s;
}
.card[data-c="blue"]:hover    .card-title { color: #60a5fa; }
.card[data-c="red"]:hover     .card-title { color: #f87171; }
.card[data-c="emerald"]:hover .card-title { color: #34d399; }
.card[data-c="purple"]:hover  .card-title { color: #a78bfa; }

.card-desc {
    font-size: 0.9375rem; color: rgb(203,213,225); line-height: 1.65; margin-bottom: 1.75rem;
}

.card-link {
    display: inline-flex; align-items: center; gap: 0.5rem;
    font-size: 0.8125rem; font-weight: 700;
    opacity: 0; transform: translateY(6px);
    transition: opacity 0.3s, transform 0.3s;
}
.card:hover .card-link { opacity: 1; transform: translateY(0); }
.card[data-c="blue"]    .card-link { color: #60a5fa; }
.card[data-c="red"]     .card-link { color: #f87171; }
.card[data-c="emerald"] .card-link { color: #34d399; }
.card[data-c="purple"]  .card-link { color: #a78bfa; }

/* ── Footer ────────────────────────────────────────────────────── */
.portal-footer {
    position: relative; z-index: 10; padding: 3.5rem 1.5rem;
    border-top: 1px solid rgba(255,255,255,0.07);
    background: rgba(0,0,0,0.45);
    backdrop-filter: blur(20px);
}

.footer-inner {
    max-width: 1200px; margin: 0 auto;
    display: flex; flex-direction: column; gap: 1.5rem; align-items: center;
}
@media (min-width: 768px) {
    .footer-inner { flex-direction: row; justify-content: space-between; }
}

.footer-copy { font-size: 0.875rem; color: rgb(148,163,184); }

.footer-links { display: flex; gap: 2rem; }

.flink {
    font-size: 0.875rem; font-weight: 700; color: rgb(203,213,225);
    transition: color 0.2s;
    position: relative;
}
.flink::after {
    content: ''; position: absolute; bottom: -3px; left: 0; width: 0; height: 2px;
    background: linear-gradient(to right, #60a5fa, #a78bfa);
    transition: width 0.25s cubic-bezier(0.4,0,0.2,1);
}
.flink:hover { color: white; }
.flink:hover::after { width: 100%; }

/* ── Responsive ────────────────────────────────────────────────── */
@media (max-width: 640px) {
    .skip-btn { top: 1rem; right: 1rem; padding: 0.625rem 1rem; font-size: 0.75rem; }
    .nav-inner { padding: 0.875rem 1.25rem; }
    .nav-logo-img-wrap { width: 3.5rem; height: 3.5rem; }
    .btn-bpo { display: none; }
    .btn-dash, .btn-login, .btn-register { padding: 0.5rem 1rem; font-size: 0.75rem; }
    .hero { padding: 8rem 1rem 3rem; }
    .hero-cta { flex-direction: column; width: 100%; }
    .cta-primary, .cta-secondary { width: 100%; justify-content: center; padding: 1rem 1.5rem; }
}

/* ── Reduced motion ─────────────────────────────────────────────── */
@media (prefers-reduced-motion: reduce) {
    *, *::before, *::after {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
}
</style>