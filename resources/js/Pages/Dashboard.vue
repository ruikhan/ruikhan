<script setup>
import ChatAssistant from '@/Components/ChatAssistant.vue';
import DashboardMap from '@/Components/DashboardMap.vue';
import IssueWizard from '@/Components/IssueWizard.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed, onMounted, onUnmounted, ref } from 'vue';

const user = usePage().props.auth.user;
const page = usePage();
const showWizard = ref(false);
const mounted = ref(false);
const currentTime = ref(new Date());

const isBusinessOwner = computed(() => {
    return user.role === 'business_owner' || user.has_business;
});

const flashSuccess = computed(() => page.props.flash?.success);
const showFlash = ref(false);

const time = ref('');
const date = ref('');
const greeting = ref('Good Morning');
const dayPeriod = ref('AM');
const notificationCount = ref();

const particles = ref([]);

const themeColor = computed(() => {
    const hour = currentTime.value.getHours();
    if (hour >= 5  && hour < 12) return { from: '#3b82f6', to: '#60a5fa', accent: '#06b6d4', name: 'morning',   glow: 'rgba(59,130,246,0.4)' };
    if (hour >= 12 && hour < 17) return { from: '#f59e0b', to: '#fbbf24', accent: '#fb923c', name: 'afternoon', glow: 'rgba(245,158,11,0.4)' };
    if (hour >= 17 && hour < 21) return { from: '#8b5cf6', to: '#a78bfa', accent: '#c084fc', name: 'evening',   glow: 'rgba(139,92,246,0.4)' };
    return                               { from: '#6366f1', to: '#818cf8', accent: '#4f46e5', name: 'night',     glow: 'rgba(99,102,241,0.4)' };
});

const updateTime = () => {
    const now = new Date();
    currentTime.value = now;
    const hours = now.getHours();
    const minutes = now.getMinutes();
    dayPeriod.value = hours >= 12 ? 'PM' : 'AM';
    const displayHours = hours % 12 || 12;
    time.value = `${displayHours.toString().padStart(2,'0')}:${minutes.toString().padStart(2,'0')}`;
    date.value = now.toLocaleDateString('en-US', { weekday: 'long', day: 'numeric', month: 'long' });
    if (hours < 12) greeting.value = 'Good Morning';
    else if (hours < 18) greeting.value = 'Good Afternoon';
    else greeting.value = 'Good Evening';
};

const quickStats = ref([
    { icon: '📄', label: 'Active',  value: '...', color: 'from-blue-500 to-blue-600',   loaded: false },
    { icon: '✅', label: 'Done',    value: '...', color: 'from-green-500 to-green-600',  loaded: false },
    { icon: '📊', label: 'Total',   value: '...', color: 'from-purple-500 to-purple-600',loaded: false },
]);

const loadStats = async () => {
    await new Promise(r => setTimeout(r, 500));
    quickStats.value[0].value = '12'; quickStats.value[0].loaded = true;
    await new Promise(r => setTimeout(r, 200));
    quickStats.value[1].value = '48'; quickStats.value[1].loaded = true;
    await new Promise(r => setTimeout(r, 200));
    quickStats.value[2].value = '60'; quickStats.value[2].loaded = true;
};

// 6 items → renders as a 2-column grid (3 rows)
const mobileApps = computed(() => [
    { icon: '⚡', name: 'Services',    link: route('services.landing'),                                               gradient: 'from-blue-500 to-indigo-600',  description: 'Permits & Docs' },
    { icon: '💳', name: 'Bills',       link: route('bills.create'),                                                   gradient: 'from-green-500 to-emerald-600', description: 'Pay Online' },
    { icon: '🩺', name: 'Health',      link: route('health.index'),                                                   gradient: 'from-red-500 to-rose-600',      description: 'Telemed' },
    { icon: '🏪', name: 'Market',      link: route('marketplace.index'),                                              gradient: 'from-purple-500 to-fuchsia-600',description: 'Shop Local' },
    { icon: '📊', name: isBusinessOwner.value ? 'Business' : 'Start Biz', link: isBusinessOwner.value ? route('business.dashboard') : route('business.register'), gradient: isBusinessOwner.value ? 'from-blue-500 to-indigo-600' : 'from-orange-500 to-amber-600', description: isBusinessOwner.value ? 'Dashboard' : 'Register' },
    { icon: '🚨', name: 'Report',      link: null, action: 'wizard',                                                  gradient: 'from-red-600 to-pink-700',      description: 'Get Help' },
]);

const desktopFeatures = computed(() => [
    { title: 'Smart Services', subtitle: 'Permits & Clearances', icon: '⚡', link: route('services.landing'), historyLink: route('services.index'), gradient: 'from-blue-500 to-indigo-600', size: 'large', description: 'Fast digital processing' },
    { title: 'Bills & Fees', subtitle: 'Secure Payments', icon: '💳', link: route('bills.create'), gradient: 'from-teal-500 to-emerald-600', size: 'medium', description: 'Pay online instantly' },
    { title: 'E-Health', subtitle: 'Telemed & Booking', icon: '🩺', link: route('health.index'), gradient: 'from-sky-500 to-cyan-600', size: 'medium', description: 'Healthcare at home' },
    { title: 'Issue Report', subtitle: 'Quick Response', icon: '🚨', link: null, action: 'wizard', gradient: 'from-red-500 to-pink-600', size: 'medium', description: 'Report problems' },
    { title: 'Marketplace', subtitle: 'Local Businesses', icon: '🌐', link: route('marketplace.index'), gradient: 'from-purple-500 to-fuchsia-600', size: 'large', description: 'Support local economy' },
    { title: isBusinessOwner.value ? 'My Business' : 'Business Hub', subtitle: isBusinessOwner.value ? 'Dashboard' : 'Register Now', icon: isBusinessOwner.value ? '📊' : '🏪', link: isBusinessOwner.value ? route('business.dashboard') : route('business.register'), gradient: isBusinessOwner.value ? 'from-blue-500 to-indigo-600' : 'from-orange-500 to-amber-600', size: 'medium', description: isBusinessOwner.value ? 'Manage business' : 'Start your journey' },
]);

const generateParticles = () => {
    particles.value = Array.from({ length: 30 }, (_, i) => ({
        id: i,
        x: Math.random() * 100, y: Math.random() * 100,
        size: Math.random() * 3 + 1,
        duration: Math.random() * 15 + 15,
        delay: Math.random() * 5,
        speedX: (Math.random() - 0.5) * 0.5,
        speedY: (Math.random() - 0.5) * 0.5,
    }));
};

let clockTimer;
onMounted(() => {
    updateTime();
    clockTimer = setInterval(updateTime, 1000);
    generateParticles();
    if (flashSuccess.value) { showFlash.value = true; setTimeout(() => showFlash.value = false, 4000); }
    setTimeout(() => mounted.value = true, 100);
    loadStats();
});
onUnmounted(() => { clearInterval(clockTimer); });

const handleAppClick = (app) => { if (app.action === 'wizard') showWizard.value = true; };
</script>

<template>
    <Head title="Dashboard" />
    <AuthenticatedLayout>
        
        <!-- Particles -->
        <div class="particles-container">
            <div v-for="p in particles" :key="p.id" class="particle-enhanced"
                :style="{ left:p.x+'%', top:p.y+'%', width:p.size+'px', height:p.size+'px', animationDuration:p.duration+'s', animationDelay:p.delay+'s', '--speed-x':p.speedX, '--speed-y':p.speedY }">
                <div class="particle-glow" :style="{ background: themeColor.glow }"></div>
            </div>
        </div>

        <!-- Mesh orbs -->
        <div class="mesh-background">
            <div class="mesh-orb mesh-orb-1" :style="{ background:`radial-gradient(circle,${themeColor.from}40,transparent)` }"></div>
            <div class="mesh-orb mesh-orb-2" :style="{ background:`radial-gradient(circle,${themeColor.to}30,transparent)` }"></div>
            <div class="mesh-orb mesh-orb-3" :style="{ background:`radial-gradient(circle,${themeColor.accent}20,transparent)` }"></div>
        </div>

        <!-- Toast -->
        <Transition name="toast">
            <div v-if="showFlash && flashSuccess" class="toast-ultra">
                <div class="toast-shine"></div>
                <div class="toast-icon-wrap-ultra">
                    <svg class="toast-icon" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="toast-content">
                    <p class="toast-title">Success!</p>
                    <p class="toast-text">{{ flashSuccess }}</p>
                </div>
                <button @click="showFlash = false" class="toast-close-ultra">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </Transition>

        <!-- ════════════════════════════════════════
             MOBILE LAYOUT  — banking-app style
        ════════════════════════════════════════ -->
        <div class="mobile-layout">

            <!-- 1. GREETING ROW -->
            <div class="m-greeting-row" :class="{ show: mounted }">
                <div class="m-greeting-text">
                    <p class="m-greeting-sub">{{ greeting }},</p>
                    <h2 class="m-greeting-name">{{ user.name.split(' ')[0] }} 👋</h2>
                </div>
                <Link :href="route('notifications.index')" class="m-notif-btn">
                    <span class="m-notif-icon">📬</span>
                    <span v-if="notificationCount > 0" class="m-notif-badge">{{ notificationCount }}</span>
                </Link>
            </div>

            <!-- 2. HERO CARD — replaces the credit card in banking app -->
            <div class="m-hero-card" :class="{ show: mounted }"
                :style="{ background:`linear-gradient(135deg, ${themeColor.from}, ${themeColor.to}, ${themeColor.accent})` }">
                <div class="m-hero-card-shine"></div>
                <div class="m-hero-card-dots"></div>
                <!-- Card chip-like logo area -->
                <div class="m-hero-top">
                    <div class="m-hero-chip">
                        <svg viewBox="0 0 32 32" fill="none" class="m-hero-chip-svg">
                            <rect x="2" y="8" width="28" height="16" rx="3" stroke="rgba(255,255,255,0.6)" stroke-width="1.5"/>
                            <line x1="2" y1="13" x2="30" y2="13" stroke="rgba(255,255,255,0.4)" stroke-width="1"/>
                            <line x1="2" y1="19" x2="30" y2="19" stroke="rgba(255,255,255,0.4)" stroke-width="1"/>
                            <line x1="11" y1="8" x2="11" y2="24" stroke="rgba(255,255,255,0.4)" stroke-width="1"/>
                            <line x1="21" y1="8" x2="21" y2="24" stroke="rgba(255,255,255,0.4)" stroke-width="1"/>
                        </svg>
                    </div>
                    <div class="m-hero-portal-label">CITIZEN PORTAL</div>
                </div>
                <!-- Time display -->
                <div class="m-hero-time-wrap">
                    <span class="m-hero-time">{{ time }}</span>
                    <span class="m-hero-period">{{ dayPeriod }}</span>
                </div>
                <div class="m-hero-date">{{ date }}</div>
                <!-- Stats row inside card -->
                <div class="m-hero-stats">
                    <div v-for="stat in quickStats" :key="stat.label" class="m-hero-stat">
                        <span class="m-hero-stat-val">{{ stat.value }}</span>
                        <span class="m-hero-stat-lbl">{{ stat.label }}</span>
                    </div>
                </div>
            </div>

            <!-- 3. SEARCH BAR -->
            <div class="m-search-wrap" :class="{ show: mounted }">
                <div class="m-search-inner">
                    <svg class="m-search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" placeholder="Search services, bills, documents…" class="m-search-input">
                </div>
            </div>

            <!-- 4. SECTION HEADER -->
            <div class="m-section-header" :class="{ show: mounted }">
                <h3 class="m-section-title">Quick Access</h3>
                <Link :href="route('services.landing')" class="m-section-link">See all</Link>
            </div>

            <!-- 5. 2-COLUMN APPS GRID — matches banking app menu layout -->
            <div class="m-apps-grid">
                <component
                    :is="app.link ? Link : 'button'"
                    v-for="(app, i) in mobileApps" :key="app.name"
                    :href="app.link"
                    @click="app.action ? handleAppClick(app) : null"
                    class="m-app-card" :class="{ show: mounted }"
                    :style="`animation-delay: ${i * 60}ms`"
                >
                    <div :class="`m-app-icon-wrap bg-gradient-to-br ${app.gradient}`">
                        <span class="m-app-icon">{{ app.icon }}</span>
                        <div class="m-app-icon-glow"></div>
                    </div>
                    <div class="m-app-info">
                        <span class="m-app-name">{{ app.name }}</span>
                        <span class="m-app-desc">{{ app.description }}</span>
                    </div>
                    <svg class="m-app-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                    <div class="m-app-card-shine"></div>
                </component>
            </div>

            <!-- 6. MAP SECTION -->
            <div class="m-section-header" :class="{ show: mounted }">
                <h3 class="m-section-title">Live Map</h3>
                <Link :href="route('establishments.index')" class="m-section-link">Explore</Link>
            </div>

            <div class="m-map-card" :class="{ show: mounted }">
                <div class="m-map-live-badge">
                    <span class="m-live-dot"></span>
                    <span>Live</span>
                </div>
                <div class="m-map-wrap">
                    <DashboardMap />
                </div>
            </div>

            <!-- 7. EMERGENCY FAB -->
            <Link :href="route('emergency.index')" class="m-emergency-fab">
                <div class="m-fab-inner">
                    <span>🚨</span>
                    <div class="m-fab-ring"></div>
                    <div class="m-fab-glow"></div>
                </div>
                <span class="m-fab-label">SOS</span>
            </Link>
        </div>

        <!-- DESKTOP LAYOUT (unchanged) -->
        <div class="desktop-layout">
            <div class="desktop-logo-header-ultra" :class="{ show: mounted }">
                <div class="desktop-logo-container-ultra">
                    <div class="desktop-logo-icon-ultra" :style="{ background:`linear-gradient(135deg,${themeColor.from},${themeColor.to})` }">
                        🏛️
                        <div class="desktop-logo-glow-ultra"></div>
                        <div class="desktop-logo-ring"></div>
                    </div>
                    <div class="desktop-logo-text-ultra">
                        <h1 class="desktop-logo-title-ultra">CityConnect</h1>
                        <p class="desktop-logo-subtitle-ultra">Smart City Management Portal</p>
                    </div>
                </div>
                <Link :href="route('emergency.index')" class="emergency-button-desktop-ultra">
                    <div class="emergency-bg-gradient"></div>
                    <span class="emergency-icon-ultra">🚨</span>
                    <span class="emergency-text-ultra">Emergency</span>
                    <div class="emergency-pulse-ultra"></div>
                </Link>
            </div>

            <header class="desktop-header-ultra" :class="{ show: mounted }">
                <div class="header-content-ultra">
                    <div class="greeting-wrap-ultra">
                        <h1 class="greeting-text-ultra">{{ greeting }}, <span class="greeting-name-ultra">{{ user.name }}</span>!</h1>
                        <p class="greeting-subtitle-ultra">{{ date }}</p>
                    </div>
                    <div class="clock-display-ultra">
                        <div class="clock-decoration-ultra" :style="{ background:`radial-gradient(ellipse,${themeColor.from}40,transparent)` }"></div>
                        <div class="clock-time-ultra">
                            <span class="clock-digits-ultra">{{ time }}</span>
                            <span class="clock-period-ultra">{{ dayPeriod }}</span>
                        </div>
                    </div>
                </div>
            </header>

            <div class="desktop-grid">
                <div class="desktop-main">
                    <div class="features-grid-ultra">
                        <component :is="feature.link ? Link : 'button'"
                            v-for="(feature, i) in desktopFeatures" :key="feature.title"
                            :href="feature.link"
                            @click="feature.action === 'wizard' ? showWizard = true : null"
                            :class="['feature-card-ultra', `bg-gradient-to-br ${feature.gradient}`,
                                { 'feature-large': feature.size==='large', 'feature-medium': feature.size==='medium', 'show': mounted }]"
                            :style="`animation-delay:${i*80}ms`">
                            <div class="feature-glass-overlay"></div>
                            <div class="feature-mesh-bg"></div>
                            <Link v-if="feature.historyLink" :href="feature.historyLink" @click.stop class="feature-history-ultra">
                                <div class="history-glow"></div>
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>History</span>
                            </Link>
                            <div class="feature-content-ultra">
                                <div class="feature-header-ultra">
                                    <div class="feature-icon-wrap-ultra">
                                        <span class="feature-icon-ultra">{{ feature.icon }}</span>
                                        <div class="feature-icon-glow-ultra"></div>
                                    </div>
                                    <div class="feature-text-ultra">
                                        <p class="feature-subtitle-ultra">{{ feature.subtitle }}</p>
                                        <h3 class="feature-title-ultra">{{ feature.title }}</h3>
                                    </div>
                                </div>
                                <p class="feature-description-ultra">{{ feature.description }}</p>
                            </div>
                            <div class="feature-shine-ultra"></div>
                        </component>
                    </div>
                </div>

                <aside class="desktop-sidebar">
                    <div class="sidebar-card-ultra stats-card-ultra" :class="{ show: mounted }">
                        <div class="sidebar-glass-bg"></div>
                        <div class="card-header-ultra">
                            <h3 class="card-title-ultra">Quick Stats</h3>
                            <div class="card-icon-ultra" :style="{ background:`linear-gradient(135deg,${themeColor.from},${themeColor.to})` }">
                                📊<div class="card-icon-glow"></div>
                            </div>
                        </div>
                        <div class="stats-list-ultra">
                            <div v-for="stat in quickStats" :key="stat.label" class="stat-item-ultra" :class="{ loaded: stat.loaded }">
                                <div :class="`stat-item-icon-ultra bg-gradient-to-br ${stat.color}`">
                                    {{ stat.icon }}<div class="stat-item-icon-glow"></div>
                                </div>
                                <div class="stat-item-info-ultra">
                                    <p class="stat-item-value-ultra">{{ stat.value }}</p>
                                    <p class="stat-item-label-ultra">{{ stat.label }}</p>
                                </div>
                                <div class="stat-item-trend-ultra">↗</div>
                            </div>
                        </div>
                    </div>

                    <div class="sidebar-map-ultra" :class="{ show: mounted }" style="animation-delay:150ms">
                        <div class="sidebar-map-glass"></div>
                        <div class="map-header-ultra">
                            <div class="map-badge-desk-ultra" :style="{ background:`linear-gradient(135deg,${themeColor.from}80,${themeColor.to}80)` }">
                                <span class="badge-dot-ultra"></span> 📍 Live Map
                            </div>
                            <Link :href="route('establishments.index')" class="map-expand-desk-ultra">
                                <div class="expand-glow"></div>
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                            </Link>
                        </div>
                        <div class="map-wrapper-ultra"><DashboardMap /></div>
                    </div>
                </aside>
            </div>
        </div>

        <IssueWizard v-if="showWizard" @close="showWizard = false" />
        <ChatAssistant />
    </AuthenticatedLayout>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');

* { font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; -webkit-font-smoothing: antialiased; }

/* ── particles & mesh (unchanged) ── */
.particles-container { position:fixed; inset:0; z-index:2; pointer-events:none; overflow:hidden; }
.particle-enhanced { position:absolute; border-radius:50%; animation:float-particle-enhanced ease-in-out infinite; will-change:transform,opacity; }
.particle-glow { position:absolute; inset:-200%; border-radius:50%; filter:blur(20px); opacity:0.6; }
@keyframes float-particle-enhanced { 0%,100%{transform:translate(0,0) scale(1);opacity:0;}5%{opacity:0.8;}95%{opacity:0.8;}100%{transform:translate(calc(var(--speed-x)*100vw),calc(-120vh+var(--speed-y)*100vh)) scale(1.5);opacity:0;}}
.mesh-background { position:fixed; inset:0; z-index:1; pointer-events:none; overflow:hidden; }
.mesh-orb { position:absolute; border-radius:50%; filter:blur(100px); animation:mesh-float 20s ease-in-out infinite; }
.mesh-orb-1 { top:10%; left:10%; width:600px; height:600px; }
.mesh-orb-2 { bottom:10%; right:10%; width:700px; height:700px; animation-delay:5s; }
.mesh-orb-3 { top:50%; left:50%; width:500px; height:500px; animation-delay:10s; }
@keyframes mesh-float { 0%,100%{transform:translate(0,0)scale(1);}33%{transform:translate(50px,-50px)scale(1.1);}66%{transform:translate(-30px,40px)scale(0.9);} }

/* toast */
.toast-ultra { position:fixed; top:5.5rem; right:1rem; z-index:60; width:calc(100vw - 2rem); max-width:24rem; display:flex; align-items:center; gap:1rem; padding:1.25rem; border-radius:1.25rem; background:linear-gradient(135deg,rgba(16,185,129,0.95),rgba(5,150,105,0.95)); backdrop-filter:blur(30px); border:1px solid rgba(255,255,255,0.25); box-shadow:0 25px 50px rgba(0,0,0,0.5); overflow:hidden; }
@media (min-width:1024px) { .toast-ultra { top:6rem; right:2rem; } }
.toast-shine { position:absolute; inset:0; background:linear-gradient(135deg,rgba(255,255,255,0.1),transparent); }
.toast-icon-wrap-ultra { width:3rem; height:3rem; flex-shrink:0; display:flex; align-items:center; justify-content:center; border-radius:50%; background:rgba(255,255,255,0.25); }
.toast-icon { width:1.75rem; height:1.75rem; color:white; }
.toast-content { flex:1; min-width:0; }
.toast-title { font-size:1rem; font-weight:800; color:white; margin-bottom:0.25rem; }
.toast-text { font-size:0.875rem; color:rgba(255,255,255,0.95); }
.toast-close-ultra { width:2.25rem; height:2.25rem; flex-shrink:0; display:flex; align-items:center; justify-content:center; border-radius:50%; color:white; background:rgba(255,255,255,0.1); }
.toast-close-ultra svg { width:1.25rem; height:1.25rem; }
.toast-enter-active { animation:toast-in-ultra 0.5s cubic-bezier(0.34,1.56,0.64,1); }
.toast-leave-active { transition:all 0.4s ease; }
.toast-leave-to { opacity:0; transform:translateX(120%) translateY(-20px); }
@keyframes toast-in-ultra { from{opacity:0;transform:translateX(120%) translateY(-20px) scale(0.9);}to{opacity:1;transform:none;} }

/* ══════════════════════════════════════
   MOBILE LAYOUT — banking-app style
══════════════════════════════════════ */
.mobile-layout {
    display: block;
    width: 100%;
    max-width: 30rem;
    margin: 0 auto;
    padding: 0 1rem 7rem;
    position: relative;
    z-index: 10;
}
@media (min-width: 1024px) { .mobile-layout { display: none; } }

/* 1. Greeting row */
.m-greeting-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.5rem 0 1.25rem;
    opacity: 0;
    transform: translateY(-16px);
    transition: all 0.6s cubic-bezier(0.34,1.56,0.64,1);
}
.m-greeting-row.show { opacity:1; transform:none; }

.m-greeting-sub {
    font-size: 0.8125rem;
    font-weight: 600;
    color: rgba(255,255,255,0.5);
    text-transform: uppercase;
    letter-spacing: 0.06em;
    margin-bottom: 0.25rem;
}
.m-greeting-name {
    font-size: 1.625rem;
    font-weight: 800;
    color: white;
    letter-spacing: -0.02em;
    line-height: 1.15;
}

.m-notif-btn {
    position: relative;
    width: 2.75rem; height: 2.75rem;
    border-radius: 0.875rem;
    display: flex; align-items: center; justify-content: center;
    background: rgba(255,255,255,0.08);
    border: 1px solid rgba(255,255,255,0.12);
    backdrop-filter: blur(16px);
    transition: all 0.2s;
    flex-shrink: 0;
}
.m-notif-btn:active { transform: scale(0.9); }
.m-notif-icon { font-size: 1.375rem; }
.m-notif-badge {
    position: absolute; top: -0.375rem; right: -0.375rem;
    min-width: 1.25rem; height: 1.25rem;
    border-radius: 9999px; padding: 0 0.25rem;
    background: #ef4444;
    font-size: 0.6rem; font-weight: 900; color: white;
    display: flex; align-items: center; justify-content: center;
    border: 2px solid #0a0a0f;
}

/* 2. Hero card */
.m-hero-card {
    position: relative;
    border-radius: 1.75rem;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    overflow: hidden;
    border: 1px solid rgba(255,255,255,0.2);
    box-shadow: 0 20px 60px rgba(0,0,0,0.5);
    opacity: 0;
    transform: translateY(24px) scale(0.97);
    transition: all 0.7s cubic-bezier(0.34,1.56,0.64,1);
    transition-delay: 0.08s;
    min-height: 11rem;
}
.m-hero-card.show { opacity:1; transform:none; }

.m-hero-card-shine {
    position: absolute; inset:0;
    background: linear-gradient(135deg, rgba(255,255,255,0.25) 0%, transparent 50%);
    pointer-events: none;
}
.m-hero-card-dots {
    position: absolute;
    top: -3rem; right: -3rem;
    width: 12rem; height: 12rem;
    border-radius: 50%;
    background: rgba(255,255,255,0.06);
    border: 1px solid rgba(255,255,255,0.1);
}
.m-hero-card-dots::after {
    content:'';
    position: absolute;
    inset: 1.5rem;
    border-radius: 50%;
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.08);
}

.m-hero-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1rem;
}
.m-hero-chip { width: 2.5rem; height: 2rem; }
.m-hero-chip-svg { width: 100%; height: 100%; }
.m-hero-portal-label {
    font-size: 0.625rem;
    font-weight: 800;
    letter-spacing: 0.15em;
    color: rgba(255,255,255,0.7);
    text-transform: uppercase;
}

.m-hero-time-wrap {
    display: flex;
    align-items: baseline;
    gap: 0.5rem;
    margin-bottom: 0.25rem;
}
.m-hero-time {
    font-size: 3rem;
    font-weight: 800;
    color: white;
    letter-spacing: -0.03em;
    line-height: 1;
    text-shadow: 0 4px 16px rgba(0,0,0,0.25);
}
.m-hero-period {
    font-size: 1.125rem;
    font-weight: 600;
    color: rgba(255,255,255,0.7);
}
.m-hero-date {
    font-size: 0.8125rem;
    font-weight: 600;
    color: rgba(255,255,255,0.6);
    margin-bottom: 1.25rem;
}

.m-hero-stats {
    display: flex;
    gap: 0;
    border-top: 1px solid rgba(255,255,255,0.2);
    padding-top: 1rem;
}
.m-hero-stat {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.25rem;
    position: relative;
}
.m-hero-stat + .m-hero-stat::before {
    content:'';
    position: absolute; left:0; top:10%; bottom:10%;
    width: 1px;
    background: rgba(255,255,255,0.2);
}
.m-hero-stat-val {
    font-size: 1.375rem;
    font-weight: 800;
    color: white;
    letter-spacing: -0.02em;
}
.m-hero-stat-lbl {
    font-size: 0.625rem;
    font-weight: 700;
    color: rgba(255,255,255,0.65);
    text-transform: uppercase;
    letter-spacing: 0.06em;
}

/* 3. Search bar */
.m-search-wrap {
    margin-bottom: 1.5rem;
    opacity: 0;
    transform: translateY(16px);
    transition: all 0.6s cubic-bezier(0.34,1.56,0.64,1);
    transition-delay: 0.15s;
}
.m-search-wrap.show { opacity:1; transform:none; }

.m-search-inner {
    display: flex;
    align-items: center;
    gap: 0.875rem;
    padding: 0.875rem 1.25rem;
    border-radius: 1.25rem;
    background: rgba(255,255,255,0.07);
    border: 1px solid rgba(255,255,255,0.1);
    backdrop-filter: blur(20px);
    transition: border-color 0.2s, box-shadow 0.2s;
}
.m-search-inner:focus-within {
    border-color: rgba(255,255,255,0.2);
    box-shadow: 0 0 0 3px rgba(255,255,255,0.05);
}
.m-search-icon { width: 1.25rem; height: 1.25rem; color: rgba(255,255,255,0.4); flex-shrink:0; }
.m-search-input { flex:1; background:transparent; border:none; outline:none; color:white; font-size:0.9375rem; font-weight:500; }
.m-search-input::placeholder { color: rgba(255,255,255,0.35); }

/* 4. Section header */
.m-section-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1rem;
    opacity: 0;
    transform: translateY(12px);
    transition: all 0.6s cubic-bezier(0.34,1.56,0.64,1);
    transition-delay: 0.2s;
}
.m-section-header.show { opacity:1; transform:none; }
.m-section-title {
    font-size: 1.0625rem;
    font-weight: 800;
    color: white;
    letter-spacing: -0.01em;
}
.m-section-link {
    font-size: 0.8125rem;
    font-weight: 700;
    color: rgba(255,255,255,0.5);
    transition: color 0.2s;
}
.m-section-link:hover { color: white; }

/* 5. 2-column apps grid */
.m-apps-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.875rem;
    margin-bottom: 1.75rem;
}

.m-app-card {
    position: relative;
    display: flex;
    align-items: center;
    gap: 0.875rem;
    padding: 1rem 0.875rem;
    border-radius: 1.25rem;
    background: rgba(255,255,255,0.06);
    border: 1px solid rgba(255,255,255,0.09);
    backdrop-filter: blur(20px);
    overflow: hidden;
    opacity: 0;
    transform: translateY(20px) scale(0.95);
    transition: all 0.55s cubic-bezier(0.34,1.56,0.64,1);
    cursor: pointer;
}
.m-app-card.show { opacity:1; transform:none; }
.m-app-card:active { transform: scale(0.94); }
.m-app-card:hover { background: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.16); }

.m-app-icon-wrap {
    position: relative;
    width: 2.75rem; height: 2.75rem;
    border-radius: 0.875rem;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    overflow: hidden;
}
.m-app-icon-glow {
    position: absolute; inset:-3px;
    border-radius: 0.875rem;
    background: inherit;
    filter: blur(10px);
    opacity: 0.65;
    z-index: -1;
}
.m-app-icon { font-size: 1.375rem; position: relative; z-index:1; }

.m-app-info {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 0.2rem;
}
.m-app-name {
    font-size: 0.8125rem;
    font-weight: 800;
    color: white;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.m-app-desc {
    font-size: 0.6875rem;
    font-weight: 600;
    color: rgba(255,255,255,0.45);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.m-app-arrow {
    width: 1rem; height: 1rem;
    color: rgba(255,255,255,0.3);
    flex-shrink: 0;
    transition: transform 0.2s, color 0.2s;
}
.m-app-card:hover .m-app-arrow { color: rgba(255,255,255,0.7); transform: translateX(2px); }

.m-app-card-shine {
    position: absolute; inset:0;
    background: linear-gradient(135deg, rgba(255,255,255,0.06), transparent);
    pointer-events: none;
}

/* 6. Map */
.m-map-card {
    position: relative;
    border-radius: 1.5rem;
    overflow: hidden;
    border: 1px solid rgba(255,255,255,0.1);
    background: rgba(255,255,255,0.04);
    backdrop-filter: blur(20px);
    margin-bottom: 1.75rem;
    opacity: 0;
    transform: translateY(20px);
    transition: all 0.7s cubic-bezier(0.34,1.56,0.64,1);
    transition-delay: 0.3s;
}
.m-map-card.show { opacity:1; transform:none; }

.m-map-live-badge {
    position: absolute; top:1rem; left:1rem; z-index:10;
    display: flex; align-items: center; gap: 0.5rem;
    padding: 0.5rem 0.875rem;
    border-radius: 9999px;
    background: rgba(10,10,15,0.75);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255,255,255,0.15);
    font-size: 0.75rem; font-weight: 800; color: white;
}
.m-live-dot {
    width: 0.5rem; height: 0.5rem;
    border-radius: 50%;
    background: #10b981;
    box-shadow: 0 0 8px rgba(16,185,129,0.8);
    animation: live-pulse 2s ease-in-out infinite;
}
@keyframes live-pulse { 0%,100%{opacity:1;transform:scale(1);}50%{opacity:0.5;transform:scale(0.85);} }

.m-map-wrap { width:100%; height: 14rem; }

/* 7. Emergency FAB */
.m-emergency-fab {
    position: fixed;
    bottom: 5.5rem; right: 1rem;
    z-index: 35;
    display: flex; flex-direction: column; align-items: center; gap: 0.375rem;
}
.m-fab-inner {
    position: relative;
    width: 3.5rem; height: 3.5rem;
    border-radius: 9999px;
    display: flex; align-items: center; justify-content: center;
    background: linear-gradient(135deg, rgb(239,68,68), rgb(220,38,38));
    box-shadow: 0 12px 32px rgba(239,68,68,0.55);
    font-size: 1.5rem;
    border: 2px solid rgba(0,0,0,0.2);
    transition: transform 0.2s cubic-bezier(0.34,1.56,0.64,1);
}
.m-emergency-fab:active .m-fab-inner { transform: scale(0.9); }
.m-fab-ring {
    position: absolute; inset:-6px;
    border-radius: 9999px;
    border: 2px solid rgb(239,68,68);
    animation: fab-ring-spin 2.5s ease-in-out infinite;
    opacity: 0;
}
@keyframes fab-ring-spin { 0%{transform:scale(1);opacity:0.7;}100%{transform:scale(1.6);opacity:0;} }
.m-fab-glow {
    position: absolute; inset:-10px;
    border-radius: 9999px;
    background: radial-gradient(circle, rgb(239,68,68), transparent);
    filter: blur(16px); opacity: 0.6; z-index:-1;
}
.m-fab-label {
    font-size: 0.625rem; font-weight: 900;
    color: white; text-transform: uppercase; letter-spacing: 0.1em;
    text-shadow: 0 2px 6px rgba(0,0,0,0.6);
}

/* ══════════════════════════════════════
   DESKTOP LAYOUT (unchanged from original)
══════════════════════════════════════ */
.desktop-layout { display: none; }
@media (min-width: 1024px) { .desktop-layout { display:block; width:100%; max-width:80rem; margin:0 auto; padding:0 1.5rem 4rem; } }

.desktop-logo-header-ultra { display:flex; align-items:center; justify-content:space-between; margin-bottom:2.5rem; opacity:0; transform:translateY(-30px); transition:all 0.8s cubic-bezier(0.34,1.56,0.64,1); }
.desktop-logo-header-ultra.show { opacity:1; transform:none; }
.desktop-logo-container-ultra { display:flex; align-items:center; gap:1.25rem; }
.desktop-logo-icon-ultra { position:relative; width:4.5rem; height:4.5rem; font-size:2.25rem; border-radius:1.75rem; display:flex; align-items:center; justify-content:center; border:2px solid rgba(255,255,255,0.25); backdrop-filter:blur(20px); box-shadow:0 15px 50px rgba(0,0,0,0.4); }
.desktop-logo-glow-ultra { position:absolute; inset:-8px; border-radius:1.75rem; background:inherit; filter:blur(24px); opacity:0.7; z-index:-1; animation:logo-glow-desktop 3.5s ease-in-out infinite; }
@keyframes logo-glow-desktop { 0%,100%{transform:scale(1);opacity:0.7;}50%{transform:scale(1.15);opacity:1;} }
.desktop-logo-ring { position:absolute; inset:-12px; border:2px solid rgba(255,255,255,0.2); border-radius:2rem; animation:desktop-logo-ring-spin 10s linear infinite; }
@keyframes desktop-logo-ring-spin { to{transform:rotate(360deg);} }
.desktop-logo-text-ultra { display:flex; flex-direction:column; gap:0.375rem; }
.desktop-logo-title-ultra { font-size:2rem; font-weight:900; color:white; letter-spacing:-0.03em; }
.desktop-logo-subtitle-ultra { font-size:1rem; color:rgba(255,255,255,0.65); font-weight:700; }
.emergency-button-desktop-ultra { position:relative; display:flex; align-items:center; gap:1rem; padding:1rem 2rem; border-radius:9999px; background:linear-gradient(135deg,rgb(239,68,68),rgb(220,38,38)); border:3px solid rgba(0,0,0,0.2); box-shadow:0 15px 40px rgba(239,68,68,0.5); transition:all 0.3s cubic-bezier(0.34,1.56,0.64,1); overflow:hidden; }
.emergency-button-desktop-ultra:hover { transform:translateY(-3px); box-shadow:0 20px 50px rgba(239,68,68,0.6); }
.emergency-bg-gradient { position:absolute; inset:0; background:linear-gradient(135deg,rgba(255,255,255,0.2),transparent); opacity:0; transition:opacity 0.3s; }
.emergency-button-desktop-ultra:hover .emergency-bg-gradient { opacity:1; }
.emergency-icon-ultra { font-size:1.75rem; position:relative; z-index:1; animation:pulse 2.5s ease-in-out infinite; }
.emergency-text-ultra { font-size:1rem; font-weight:800; color:white; position:relative; z-index:1; text-transform:uppercase; letter-spacing:0.08em; }
.emergency-pulse-ultra { position:absolute; inset:0; background:radial-gradient(circle,rgba(255,255,255,0.4),transparent); animation:emergency-pulse-desktop 2.5s ease-in-out infinite; }
@keyframes emergency-pulse-desktop { 0%,100%{transform:scale(0.7);opacity:0;}50%{transform:scale(1.3);opacity:1;} }
@keyframes pulse { 0%,100%{transform:scale(1);}50%{transform:scale(1.1);} }

.desktop-header-ultra { margin-bottom:2.5rem; opacity:0; transform:translateY(30px); transition:all 0.8s cubic-bezier(0.34,1.56,0.64,1); transition-delay:0.1s; }
.desktop-header-ultra.show { opacity:1; transform:none; }
.header-content-ultra { display:flex; justify-content:space-between; align-items:flex-end; gap:2.5rem; }
.greeting-text-ultra { font-size:3.5rem; font-weight:900; color:white; line-height:1.1; letter-spacing:-0.03em; }
.greeting-name-ultra { background:linear-gradient(135deg,#60a5fa,#a78bfa,#06b6d4); background-size:200% auto; -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text; animation:greeting-gradient 6s ease-in-out infinite; }
@keyframes greeting-gradient { 0%,100%{background-position:0% 50%;}50%{background-position:100% 50%;} }
.greeting-subtitle-ultra { font-size:1.125rem; color:rgba(255,255,255,0.7); margin-top:0.625rem; font-weight:600; }
.clock-display-ultra { position:relative; text-align:right; }
.clock-decoration-ultra { position:absolute; inset:-1.5rem; border-radius:2rem; filter:blur(50px); opacity:0.4; z-index:-1; }
.clock-time-ultra { display:flex; align-items:baseline; gap:1rem; justify-content:flex-end; }
.clock-digits-ultra { font-size:4.5rem; font-weight:300; color:white; line-height:1; letter-spacing:-0.03em; }
.clock-period-ultra { font-size:1.75rem; font-weight:700; color:rgba(255,255,255,0.6); }
.greeting-wrap-ultra { flex:1; }

.desktop-grid { display:grid; grid-template-columns:1fr 24rem; gap:2.5rem; }
.desktop-main { min-width:0; }
.features-grid-ultra { display:grid; grid-template-columns:repeat(3,1fr); gap:1.25rem; }
.feature-card-ultra { position:relative; display:flex; flex-direction:column; justify-content:space-between; padding:2rem; border-radius:2rem; border:1px solid rgba(255,255,255,0.15); backdrop-filter:blur(30px) saturate(180%); opacity:0; transform:translateY(30px) scale(0.95); transition:all 0.6s cubic-bezier(0.34,1.56,0.64,1); overflow:hidden; cursor:pointer; box-shadow:0 20px 60px rgba(0,0,0,0.4); }
.feature-card-ultra.show { opacity:1; transform:none; }
.feature-card-ultra:hover { transform:translateY(-8px) scale(1.02); border-color:rgba(255,255,255,0.25); box-shadow:0 30px 80px rgba(0,0,0,0.5); }
.feature-large { grid-column:span 2; min-height:12rem; }
.feature-medium { min-height:10rem; }
.feature-glass-overlay { position:absolute; inset:0; background:linear-gradient(135deg,rgba(255,255,255,0.08),transparent); opacity:0; transition:opacity 0.3s; }
.feature-card-ultra:hover .feature-glass-overlay { opacity:1; }
.feature-mesh-bg { position:absolute; inset:0; background:radial-gradient(circle at top right,rgba(255,255,255,0.1),transparent); opacity:0; transition:opacity 0.3s; }
.feature-card-ultra:hover .feature-mesh-bg { opacity:1; }
.feature-history-ultra { position:absolute; top:1.25rem; right:1.25rem; z-index:10; display:flex; align-items:center; gap:0.5rem; padding:0.625rem 1rem; border-radius:9999px; border:1px solid rgba(255,255,255,0.25); background:rgba(0,0,0,0.4); backdrop-filter:blur(20px); font-size:0.8125rem; font-weight:800; color:white; transition:all 0.3s; overflow:hidden; }
.feature-history-ultra:hover { background:rgba(0,0,0,0.6); transform:scale(1.08); }
.history-glow { position:absolute; inset:-4px; background:radial-gradient(circle,rgba(255,255,255,0.3),transparent); filter:blur(12px); opacity:0; transition:opacity 0.3s; z-index:-1; }
.feature-history-ultra:hover .history-glow { opacity:1; }
.feature-history-ultra svg { width:1rem; height:1rem; }
.feature-content-ultra { display:flex; flex-direction:column; gap:1.25rem; height:100%; }
.feature-header-ultra { display:flex; flex-direction:column; gap:1rem; }
.feature-icon-wrap-ultra { position:relative; width:3.5rem; height:3.5rem; border-radius:1.25rem; display:flex; align-items:center; justify-content:center; background:rgba(255,255,255,0.2); backdrop-filter:blur(10px); }
.feature-icon-glow-ultra { position:absolute; inset:-6px; border-radius:1.25rem; background:rgba(255,255,255,0.3); filter:blur(16px); opacity:0.6; z-index:-1; }
.feature-icon-ultra { font-size:2rem; position:relative; z-index:1; }
.feature-text-ultra { display:flex; flex-direction:column; gap:0.375rem; }
.feature-subtitle-ultra { color:rgba(255,255,255,0.75); font-size:0.8125rem; font-weight:800; text-transform:uppercase; letter-spacing:0.08em; }
.feature-title-ultra { font-weight:900; color:white; font-size:1.75rem; line-height:1.2; letter-spacing:-0.03em; }
.feature-description-ultra { margin-top:auto; font-size:1rem; color:rgba(255,255,255,0.7); font-weight:500; line-height:1.6; }
.feature-shine-ultra { position:absolute; inset:0; background:linear-gradient(135deg,transparent,rgba(255,255,255,0.15),transparent); opacity:0; animation:shine-ultra 4s ease-in-out infinite; }
@keyframes shine-ultra { 0%,100%{transform:translateX(-100%) translateY(-100%);opacity:0;}50%{opacity:1;} }

.desktop-sidebar { display:flex; flex-direction:column; gap:2rem; }
.sidebar-card-ultra { border-radius:2rem; padding:2rem; border:1px solid rgba(255,255,255,0.15); background:rgba(28,28,30,0.7); backdrop-filter:blur(30px) saturate(180%); opacity:0; transform:translateY(30px); transition:all 0.8s cubic-bezier(0.34,1.56,0.64,1); position:relative; overflow:hidden; box-shadow:0 20px 60px rgba(0,0,0,0.4); }
.sidebar-card-ultra.show { opacity:1; transform:none; }
.sidebar-glass-bg { position:absolute; inset:0; background:linear-gradient(135deg,rgba(255,255,255,0.05),transparent); }
.card-header-ultra { display:flex; align-items:center; justify-content:space-between; margin-bottom:1.5rem; }
.card-title-ultra { font-size:1.5rem; font-weight:900; color:white; letter-spacing:-0.02em; }
.card-icon-ultra { position:relative; width:3rem; height:3rem; border-radius:1rem; display:flex; align-items:center; justify-content:center; font-size:1.5rem; }
.card-icon-glow { position:absolute; inset:-4px; border-radius:1rem; background:inherit; filter:blur(16px); opacity:0.6; z-index:-1; }
.stats-list-ultra { display:flex; flex-direction:column; gap:1.25rem; }
.stat-item-ultra { display:flex; align-items:center; gap:1.25rem; padding:1.25rem; border-radius:1.25rem; background:rgba(255,255,255,0.06); backdrop-filter:blur(10px); transition:all 0.3s; }
.stat-item-ultra:hover { background:rgba(255,255,255,0.1); transform:translateX(4px); }
.stat-item-icon-ultra { position:relative; width:3rem; height:3rem; border-radius:1rem; display:flex; align-items:center; justify-content:center; font-size:1.5rem; flex-shrink:0; }
.stat-item-icon-glow { position:absolute; inset:-3px; border-radius:1rem; background:inherit; filter:blur(12px); opacity:0.6; z-index:-1; }
.stat-item-info-ultra { flex:1; min-width:0; }
.stat-item-value-ultra { font-size:1.75rem; font-weight:900; color:white; line-height:1; margin-bottom:0.375rem; letter-spacing:-0.02em; }
.stat-item-label-ultra { font-size:0.8125rem; color:rgba(255,255,255,0.7); font-weight:700; }
.stat-item-trend-ultra { font-size:1.5rem; color:#10b981; flex-shrink:0; }
.stat-item-ultra.loaded .stat-item-value-ultra { animation:count-up 0.8s cubic-bezier(0.34,1.56,0.64,1); }
@keyframes count-up { from{transform:translateY(15px) scale(0.8);opacity:0;}to{transform:none;opacity:1;} }

.sidebar-map-ultra { position:relative; border-radius:2rem; padding:1rem; min-height:22rem; border:1px solid rgba(255,255,255,0.15); background:rgba(28,28,30,0.7); backdrop-filter:blur(30px) saturate(180%); opacity:0; transform:translateY(30px); transition:all 0.8s cubic-bezier(0.34,1.56,0.64,1); box-shadow:0 20px 60px rgba(0,0,0,0.4); }
.sidebar-map-ultra.show { opacity:1; transform:none; }
.sidebar-map-glass { position:absolute; inset:0; background:linear-gradient(135deg,rgba(255,255,255,0.05),transparent); border-radius:2rem; }
.map-header-ultra { position:absolute; top:2rem; left:2rem; right:2rem; z-index:10; display:flex; align-items:center; justify-content:space-between; gap:0.75rem; }
.map-badge-desk-ultra { display:flex; align-items:center; gap:0.625rem; padding:0.625rem 1.125rem; border-radius:9999px; font-size:0.8125rem; font-weight:800; color:white; border:1px solid rgba(255,255,255,0.25); backdrop-filter:blur(30px); }
.badge-dot-ultra { width:0.625rem; height:0.625rem; background:#10b981; border-radius:50%; box-shadow:0 0 12px rgba(16,185,129,0.8); animation:badge-pulse 2.5s ease-in-out infinite; }
@keyframes badge-pulse { 0%,100%{opacity:1;transform:scale(1);}50%{opacity:0.6;transform:scale(0.9);} }
.map-expand-desk-ultra { position:absolute; top:2rem; right:2rem; z-index:10; width:3rem; height:3rem; border-radius:1rem; display:flex; align-items:center; justify-content:center; background:rgba(0,0,0,0.6); backdrop-filter:blur(30px); border:1px solid rgba(255,255,255,0.25); color:white; transition:all 0.3s; }
.map-expand-desk-ultra:hover { transform:scale(1.08); }
.expand-glow { position:absolute; inset:-4px; background:radial-gradient(circle,rgba(255,255,255,0.3),transparent); filter:blur(12px); opacity:0; transition:opacity 0.3s; z-index:-1; }
.map-expand-desk-ultra:hover .expand-glow { opacity:1; }
.map-expand-desk-ultra svg { width:1.125rem; height:1.125rem; }
.map-wrapper-ultra { width:100%; height:100%; min-height:20rem; border-radius:1.5rem; overflow:hidden; }

@media (min-width:1024px) and (max-width:1439px) { .desktop-grid { grid-template-columns:1fr 22rem; gap:2rem; } .greeting-text-ultra { font-size:3rem; } .clock-digits-ultra { font-size:4rem; } }
@media (min-width:1440px) { .desktop-layout { max-width:90rem; } }
@media (prefers-reduced-motion:reduce) { *,*::before,*::after { animation-duration:0.01ms !important; transition-duration:0.01ms !important; } }
@media (hover:none) { .feature-card-ultra:active { transform:scale(0.97); } }
</style>