<script setup>
import ChatAssistant from '@/Components/ChatAssistant.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NotificationCenter from '@/Components/NotificationCenter.vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed, onMounted, onUnmounted, ref, watch } from 'vue';

const showingNavigationDropdown = ref(false);
const showingMobileMenu = ref(false);
const showingSidebar = ref(false);
const page = usePage();
const user = computed(() => page.props.auth.user);
const logoUrl = '/images/enotpili.png'; 
const isLoading = ref(false);
const isMobileView = ref(false);
const scrolled = ref(false);
const scrollProgress = ref(0);
const mouseX = ref(0);
const mouseY = ref(0);

// Check if user is a business owner
const isBusinessOwner = computed(() => {
    return user.value.role === 'business_owner' || user.value.has_business;
});

// Dynamic time-based theme
const timeOfDay = ref('day');
const updateTimeOfDay = () => {
    const hour = new Date().getHours();
    if (hour >= 5 && hour < 12) timeOfDay.value = 'morning';
    else if (hour >= 12 && hour < 17) timeOfDay.value = 'afternoon';
    else if (hour >= 17 && hour < 21) timeOfDay.value = 'evening';
    else timeOfDay.value = 'night';
};

// Simplified viewport detection
const updateViewport = () => {
    isMobileView.value = window.innerWidth < 768;
};

// Optimized scroll handler with throttling
let scrollTimeout;
const handleScroll = () => {
    if (scrollTimeout) return;
    
    scrollTimeout = setTimeout(() => {
        const currentScroll = window.scrollY;
        scrolled.value = currentScroll > 20;
        
        const winScroll = document.documentElement.scrollTop;
        const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
        scrollProgress.value = height > 0 ? (winScroll / height) * 100 : 0;
        
        scrollTimeout = null;
    }, 100);
};

// Mouse move handler for parallax effects
const handleMouseMove = (e) => {
    if (!isMobileView.value) {
        mouseX.value = (e.clientX / window.innerWidth - 0.5) * 20;
        mouseY.value = (e.clientY / window.innerHeight - 0.5) * 20;
    }
};

// Debounced resize handler
let resizeTimeout;
const checkViewport = () => {
    clearTimeout(resizeTimeout);
    resizeTimeout = setTimeout(updateViewport, 150);
};

onMounted(() => {
    updateViewport();
    updateTimeOfDay();
    window.addEventListener('resize', checkViewport, { passive: true });
    window.addEventListener('scroll', handleScroll, { passive: true });
    window.addEventListener('mousemove', handleMouseMove, { passive: true });
    
    // Update time of day every minute
    const timeInterval = setInterval(updateTimeOfDay, 60000);
    
    return () => clearInterval(timeInterval);
});

onUnmounted(() => {
    window.removeEventListener('resize', checkViewport);
    window.removeEventListener('scroll', handleScroll);
    window.removeEventListener('mousemove', handleMouseMove);
    clearTimeout(resizeTimeout);
    clearTimeout(scrollTimeout);
});

// Avatar gradient
const getAvatarGradient = (name) => {
    const gradients = [
        'from-blue-400 to-blue-600', 
        'from-purple-400 to-purple-600', 
        'from-pink-400 to-pink-600',
        'from-orange-400 to-orange-600',
        'from-green-400 to-green-600',
    ];
    const hash = (name?.split('').reduce((acc, char) => acc + char.charCodeAt(0), 0) || 0);
    return gradients[hash % gradients.length];
};

// Optimized loading transitions
router.on('start', () => { 
    isLoading.value = true;
    showingMobileMenu.value = false;
    showingSidebar.value = false;
});

router.on('finish', () => { 
    setTimeout(() => isLoading.value = false, 150);
});

// Close menus when clicking outside
watch(showingMobileMenu, (newVal) => {
    document.body.style.overflow = newVal ? 'hidden' : '';
});

watch(showingSidebar, (newVal) => {
    document.body.style.overflow = newVal ? 'hidden' : '';
});

// Mobile navigation items
const mobileNavItems = computed(() => {
    if (user.value.role === 'admin') {
        return [
            { name: 'Dashboard', route: 'dashboard', icon: '🏠', current: 'dashboard' },
            { name: 'Command', route: 'admin.analytics', icon: '📊', current: 'admin.analytics' },
            { name: 'Approvals', route: 'admin.documents.index', icon: '✅', current: 'admin.documents.*' },
            { name: 'Market', route: 'marketplace.index', icon: '🏪', current: 'marketplace.*' },
        ];
    } else {
        return [
            { name: 'Home', route: 'dashboard', icon: '🏠', current: 'dashboard' },
            { name: 'Profile', route: 'profile.barangay.show', icon: '👤', current: 'profile.barangay.*' },
            { name: 'Market', route: 'marketplace.index', icon: '🏪', current: 'marketplace.*' },
            { name: 'More', action: 'sidebar', icon: '⚡', current: 'sidebar' },
        ];
    }
});

// Sidebar menu items (non-admin)
const sidebarItems = [
    { icon: '🌾', name: 'Market Prices', link: route('market.index'), gradient: 'from-cyan-500 to-teal-600' },
    { icon: '📍', name: 'Places', link: route('establishments.index'), gradient: 'from-purple-500 to-fuchsia-600' },
    { icon: '💼', name: 'Jobs', link: route('jobs.index'), gradient: 'from-yellow-500 to-orange-600' },
    { icon: '♻️', name: 'Green Guard', link: route('environment.index'), gradient: 'from-emerald-500 to-green-600' },
    { icon: '🤝', name: 'Social Aid', link: route('social.index'), gradient: 'from-violet-500 to-purple-600' },
    { icon: '🗳️', name: 'Polls', link: route('polls.index'), gradient: 'from-amber-500 to-orange-600' },
];

const handleNavClick = (item) => {
    if (item.action === 'sidebar') showingSidebar.value = true;
};
</script>

<template>
    <div class="app-container" :data-theme="timeOfDay">
        
        <!-- Animated Background -->
        <div class="bg-layer">
            <div class="bg-gradient"></div>
            <div class="bg-orbs">
                <div class="orb orb-1"></div>
                <div class="orb orb-2"></div>
                <div class="orb orb-3"></div>
            </div>
            <div class="bg-grain"></div>
            <div class="bg-mesh"></div>
        </div>

        <!-- Enhanced Scroll Progress -->
        <div class="scroll-progress-wrapper">
            <div class="scroll-progress-bar" :style="{ width: scrollProgress + '%' }"></div>
            <div class="scroll-progress-glow" :style="{ width: scrollProgress + '%' }"></div>
        </div>

        <!-- 🔔 MOBILE TOP BAR WITH NOTIFICATIONS -->
        <div class="mobile-top-bar" :class="{ 'mobile-top-bar-hidden': isLoading }">
            <div class="mobile-top-bar-content">
                <!-- Logo -->
                <Link :href="route('dashboard')" class="mobile-logo">
                    <img :src="logoUrl" alt="Logo" class="mobile-logo-img" />
                </Link>

                <!-- Mobile Notification Bell -->
                <div class="mobile-notification-wrapper">
                    <NotificationCenter />
                </div>
            </div>
        </div>

        <!-- Desktop Navigation -->
        <nav 
            class="desktop-nav"
            :class="{ 'nav-hidden': isLoading, 'nav-scrolled': scrolled }"
            :style="{ transform: `translate(${mouseX * 0.5}px, ${mouseY * 0.5}px)` }"
        >
            <div class="nav-container">
                
                <!-- Logo -->
                <Link :href="route('dashboard')" class="nav-logo">
                    <div class="logo-ring"></div>
                    <img :src="logoUrl" alt="E-Pili Logo" class="logo-img" />
                </Link>

                <div class="nav-divider"></div>

                <!-- Navigation Links -->
                <div class="nav-links">
                    <Link 
                        :href="route('dashboard')" 
                        :class="['nav-link', route().current('dashboard') ? 'active' : '']"
                    >
                        <span class="nav-link-icon">🏠</span>
                        <span class="nav-link-text">Dashboard</span>
                        <span class="nav-link-indicator"></span>
                    </Link>

                    <template v-if="user.role === 'admin'">
                        <Link 
                            :href="route('admin.analytics')" 
                            :class="['nav-link', route().current('admin.analytics') ? 'active' : '']"
                        >
                            <span class="nav-link-icon">📊</span>
                            <span class="nav-link-text">Command</span>
                            <span class="nav-link-indicator"></span>
                        </Link>
                        <Link 
                            :href="route('admin.documents.index')" 
                            :class="['nav-link', route().current('admin.documents.*') ? 'active' : '']"
                        >
                            <span class="nav-link-icon">✅</span>
                            <span class="nav-link-text">Approvals</span>
                            <span class="nav-link-indicator"></span>
                        </Link>
                        <Link 
                            :href="route('marketplace.index')" 
                            :class="['nav-link', route().current('marketplace.*') ? 'active' : '']"
                        >
                            <span class="nav-link-icon">🏪</span>
                            <span class="nav-link-text">Marketplace</span>
                            <span class="nav-link-indicator"></span>
                        </Link>
                    </template>

                    <template v-else>
                        <Link 
                            :href="route('profile.barangay.show')" 
                            :class="['nav-link', route().current('profile.barangay.*') ? 'active' : '']"
                        >
                            <span class="nav-link-icon">👤</span>
                            <span class="nav-link-text">Profile</span>
                            <span class="nav-link-indicator"></span>
                        </Link>

                        <Link 
                            :href="route('marketplace.index')" 
                            :class="['nav-link', route().current('marketplace.*') ? 'active' : '']"
                        >
                            <span class="nav-link-icon">🏪</span>
                            <span class="nav-link-text">Market</span>
                            <span class="nav-link-indicator"></span>
                        </Link>

                        <Link 
                            v-if="isBusinessOwner" 
                            :href="route('business.dashboard')" 
                            :class="['nav-link', route().current('business.dashboard') ? 'active' : '']"
                        >
                            <span class="nav-link-icon">📊</span>
                            <span class="nav-link-text">Business</span>
                            <span class="nav-link-indicator"></span>
                        </Link>

                        <Link 
                            v-else
                            :href="route('business.register')" 
                            :class="['nav-link nav-link-cta', route().current('business.register') ? 'active' : '']"
                        >
                            <span class="nav-link-icon">🚀</span>
                            <span class="nav-link-text">Start Business</span>
                            <span class="nav-link-shine"></span>
                        </Link>

                        <Link 
                            :href="route('services.index')" 
                            :class="['nav-link', route().current('services.*') ? 'active' : '']"
                        >
                            <span class="nav-link-icon">📄</span>
                            <span class="nav-link-text">Docs</span>
                            <span class="nav-link-indicator"></span>
                        </Link>
                        
                        <Link 
                            :href="route('emergency.index')" 
                            :class="['nav-link nav-link-danger', route().current('emergency.*') ? 'active' : '']"
                        >
                            <span class="nav-link-icon pulse-icon">🚨</span>
                            <span class="nav-link-text">Emergency</span>
                            <span class="nav-link-indicator"></span>
                        </Link>

                        <button @click="showingSidebar = true" class="nav-link">
                            <span class="nav-link-icon">⚡</span>
                            <span class="nav-link-text">More</span>
                            <span class="nav-link-indicator"></span>
                        </button>
                    </template>
                </div>

                <!-- User Section -->
                <div class="nav-user">
                    <NotificationCenter />
                    <div class="user-divider"></div>
                    
                    <Dropdown align="right" width="56">
                        <template #trigger>
                            <button class="user-btn">
                                <div class="user-avatar-ring"></div>
                                <div :class="`user-avatar bg-gradient-to-br ${getAvatarGradient(user.name)}`">
                                    <span>{{ user.name.charAt(0) }}</span>
                                </div>
                            </button>
                        </template>
                        <template #content>
                            <div class="dropdown-content">
                                <div class="dropdown-header">
                                    <div :class="`dropdown-avatar bg-gradient-to-br ${getAvatarGradient(user.name)}`">
                                        <span>{{ user.name.charAt(0) }}</span>
                                    </div>
                                    <div class="dropdown-user-info">
                                        <p class="dropdown-name">{{ user.name }}</p>
                                        <p class="dropdown-email">{{ user.email }}</p>
                                        <span v-if="user.role === 'admin'" class="admin-badge">
                                            <span>👑</span>
                                            <span>Admin</span>
                                        </span>
                                    </div>
                                </div>

                                <div class="dropdown-section">
                                    <DropdownLink :href="route('profile.edit')" class="dropdown-link">
                                        <span>🔧</span>
                                        <span>Settings</span>
                                    </DropdownLink>
                                    <DropdownLink :href="route('profile.barangay.show')" class="dropdown-link">
                                        <span>👤</span>
                                        <span>Profile</span>
                                    </DropdownLink>
                                </div>

                                <div class="dropdown-divider"></div>

                                <div class="dropdown-section">
                                    <DropdownLink :href="route('proposal')" class="dropdown-link">
                                        <span>📖</span>
                                        <span>Proposal</span>
                                    </DropdownLink>
                                    <DropdownLink :href="route('simulation')" class="dropdown-link">
                                        <span>📘</span>
                                        <span>Handbook</span>
                                    </DropdownLink>
                                    <DropdownLink :href="route('training')" class="dropdown-link">
                                        <span>🎓</span>
                                        <span>Training</span>
                                    </DropdownLink>
                                </div>

                                <div class="dropdown-divider"></div>

                                <div class="dropdown-footer">
                                    <DropdownLink :href="route('logout')" method="post" as="button" class="dropdown-link dropdown-link-danger">
                                        <span>🚪</span>
                                        <span>Logout</span>
                                    </DropdownLink>
                                </div>
                            </div>
                        </template>
                    </Dropdown>
                </div>
            </div>
        </nav>

        <!-- Mobile Bottom Navigation -->
        <nav class="mobile-nav" :class="{ 'mobile-nav-hidden': isLoading }">
            <div class="mobile-nav-bar">
                <div class="mobile-nav-ripple"></div>
                <div class="mobile-nav-items">
                    <component
                        :is="item.route ? Link : 'button'"
                        v-for="item in mobileNavItems" 
                        :key="item.route || item.action"
                        :href="item.route ? route(item.route) : undefined"
                        @click="item.action ? handleNavClick(item) : null"
                        :class="['mobile-nav-item', {
                            'mobile-nav-item-active': item.current && route().current(item.current)
                        }]"
                    >
                        <div class="mobile-nav-icon-wrap">
                            <span class="mobile-nav-icon">{{ item.icon }}</span>
                            <div class="mobile-nav-glow"></div>
                        </div>
                        <span class="mobile-nav-label">{{ item.name }}</span>
                    </component>
                </div>
            </div>
        </nav>

        <!-- Enhanced Side Drawer -->
        <Transition name="sidebar">
            <div v-show="showingSidebar" class="sidebar-drawer">
                <div class="sidebar-header">
                    <div class="sidebar-title-wrap">
                        <h3 class="sidebar-title">More Services</h3>
                        <p class="sidebar-subtitle">Explore more features</p>
                    </div>
                    <button @click="showingSidebar = false" class="sidebar-close">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <div class="sidebar-content">
                    <Link 
                        v-for="(item, index) in sidebarItems" 
                        :key="item.name"
                        :href="item.link"
                        @click="showingSidebar = false"
                        class="sidebar-item"
                        :style="{ animationDelay: `${index * 50}ms` }"
                    >
                        <div :class="`sidebar-item-icon bg-gradient-to-br ${item.gradient}`">
                            <span>{{ item.icon }}</span>
                            <div class="sidebar-item-glow"></div>
                        </div>
                        <span class="sidebar-item-name">{{ item.name }}</span>
                        <svg class="sidebar-item-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </Link>
                </div>

                <div class="sidebar-footer">
                    <Link :href="route('profile.edit')" @click="showingSidebar = false" class="sidebar-footer-btn">
                        <span>⚙️</span>
                        <span>Settings</span>
                    </Link>
                    <Link :href="route('logout')" method="post" as="button" @click="showingSidebar = false" class="sidebar-footer-btn sidebar-footer-btn-danger">
                        <span>🚪</span>
                        <span>Logout</span>
                    </Link>
                </div>
            </div>
        </Transition>

        <!-- Backdrop for Sidebar -->
        <Transition name="backdrop">
            <div 
                v-show="showingSidebar" 
                @click="showingSidebar = false"
                class="sidebar-backdrop"
            ></div>
        </Transition>

        <!-- Main Content -->
        <main class="main-content" :class="{ 'main-loading': isLoading }">
            <slot />
        </main>

        <!-- Chat Assistant -->
        <div class="chat-wrapper">
            <ChatAssistant />
        </div>

        <!-- Enhanced Loading Screen -->
        <Transition name="loading">
            <div v-if="isLoading" class="loading-screen">
                <div class="loading-spinner">
                    <div class="spinner-ring"></div>
                    <div class="spinner-ring"></div>
                    <div class="spinner-ring"></div>
                </div>
                <p class="loading-text">Loading...</p>
            </div>
        </Transition>
    </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');

* { 
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; 
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}

.app-container {
    min-height: 100vh;
    background: #000;
    color: #f1f5f9;
    position: relative;
    overflow-x: hidden;
}

/* Dynamic Theme Colors */
.app-container[data-theme="morning"] {
    --primary-color: #3b82f6;
    --secondary-color: #60a5fa;
}

.app-container[data-theme="afternoon"] {
    --primary-color: #f59e0b;
    --secondary-color: #fbbf24;
}

.app-container[data-theme="evening"] {
    --primary-color: #8b5cf6;
    --secondary-color: #a78bfa;
}

.app-container[data-theme="night"] {
    --primary-color: #6366f1;
    --secondary-color: #818cf8;
}

/* Enhanced Background */
.bg-layer {
    position: fixed;
    inset: 0;
    z-index: 1;
    overflow: hidden;
}

.bg-gradient {
    position: absolute;
    inset: 0;
    background: radial-gradient(ellipse at top, #0a0a0f 0%, #000 100%);
}

.bg-orbs {
    position: absolute;
    inset: 0;
    opacity: 0.3;
}

.orb {
    position: absolute;
    border-radius: 50%;
    filter: blur(80px);
    animation: float 20s ease-in-out infinite;
}

.orb-1 {
    width: 500px;
    height: 500px;
    background: radial-gradient(circle, var(--primary-color, #3b82f6) 0%, transparent 70%);
    top: -250px;
    right: -250px;
    animation-delay: 0s;
}

.orb-2 {
    width: 400px;
    height: 400px;
    background: radial-gradient(circle, var(--secondary-color, #a855f7) 0%, transparent 70%);
    bottom: -200px;
    left: -200px;
    animation-delay: 5s;
}

.orb-3 {
    width: 300px;
    height: 300px;
    background: radial-gradient(circle, #ec4899 0%, transparent 70%);
    top: 50%;
    left: 50%;
    animation-delay: 10s;
}

@keyframes float {
    0%, 100% {
        transform: translate(0, 0) scale(1);
    }
    33% {
        transform: translate(30px, -30px) scale(1.1);
    }
    66% {
        transform: translate(-20px, 20px) scale(0.9);
    }
}

.bg-grain {
    position: absolute;
    inset: 0;
    opacity: 0.03;
    background-image: url('data:image/svg+xml,%3Csvg viewBox="0 0 400 400" xmlns="http://www.w3.org/2000/svg"%3E%3Cfilter id="n"%3E%3CfeTurbulence type="fractalNoise" baseFrequency="3" numOctaves="3"/%3E%3C/filter%3E%3Crect width="100%25" height="100%25" filter="url(%23n)"/%3E%3C/svg%3E');
}

.bg-mesh {
    position: absolute;
    inset: 0;
    opacity: 0.05;
    background-image: 
        linear-gradient(rgba(255,255,255,0.05) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255,255,255,0.05) 1px, transparent 1px);
    background-size: 50px 50px;
    animation: mesh-move 20s linear infinite;
}

@keyframes mesh-move {
    0% {
        background-position: 0 0;
    }
    100% {
        background-position: 50px 50px;
    }
}

/* Enhanced Scroll Progress */
.scroll-progress-wrapper {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: rgba(0, 0, 0, 0.3);
    z-index: 201;
    overflow: hidden;
}

.scroll-progress-bar {
    height: 100%;
    background: linear-gradient(to right, var(--primary-color, #3b82f6), var(--secondary-color, #a855f7));
    transition: width 0.1s ease;
    position: relative;
}

.scroll-progress-glow {
    position: absolute;
    top: 0;
    left: 0;
    height: 100%;
    background: linear-gradient(to right, var(--primary-color, #3b82f6), var(--secondary-color, #a855f7));
    filter: blur(10px);
    opacity: 0.5;
    transition: width 0.1s ease;
}

/* 🔔 MOBILE TOP BAR */
.mobile-top-bar {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 40;
    display: block;
    background: rgba(18, 18, 20, 0.95);
    backdrop-filter: blur(30px);
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    padding: 0.75rem 1rem;
    padding-top: calc(0.75rem + env(safe-area-inset-top));
    transition: transform 0.3s ease;
}

@media (min-width: 1024px) {
    .mobile-top-bar {
        display: none;
    }
}

.mobile-top-bar.mobile-top-bar-hidden {
    transform: translateY(-100%);
}

.mobile-top-bar-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
    max-width: 28rem;
    margin: 0 auto;
}

.mobile-logo {
    display: flex;
    align-items: center;
}

.mobile-logo-img {
    height: 2rem;
    width: auto;
    filter: drop-shadow(0 4px 12px rgba(59, 130, 246, 0.4));
}

.mobile-notification-wrapper {
    display: flex;
    align-items: center;
}

/* Desktop Navigation */
.desktop-nav {
    position: fixed;
    top: 1rem;
    left: 0;
    right: 0;
    z-index: 40;
    display: none;
    justify-content: center;
    padding: 0 1rem;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    will-change: transform;
}

@media (min-width: 1024px) {
    .desktop-nav {
        display: flex;
    }
}

.desktop-nav.nav-hidden {
    transform: translateY(-120px);
    opacity: 0;
}

.desktop-nav.nav-scrolled {
    top: 0.5rem;
}

.nav-container {
    position: relative;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.5rem 1rem;
    border-radius: 9999px;
    background: rgba(18, 18, 20, 0.7);
    border: 1px solid rgba(255, 255, 255, 0.1);
    box-shadow: 
        0 20px 50px rgba(0, 0, 0, 0.8),
        inset 0 1px 0 rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(30px);
    transition: all 0.3s ease;
}

.nav-container:hover {
    background: rgba(18, 18, 20, 0.85);
    border-color: rgba(255, 255, 255, 0.15);
}

.nav-logo {
    position: relative;
    display: flex;
    align-items: center;
    padding: 0 0.5rem;
}

.logo-ring {
    position: absolute;
    inset: -4px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary-color, #3b82f6), var(--secondary-color, #a855f7));
    opacity: 0;
    animation: logo-pulse 3s ease-in-out infinite;
    filter: blur(8px);
}

@keyframes logo-pulse {
    0%, 100% {
        opacity: 0;
        transform: scale(0.8);
    }
    50% {
        opacity: 0.3;
        transform: scale(1);
    }
}

.logo-img {
    height: 3rem;
    width: auto;
    filter: drop-shadow(0 8px 20px rgba(59, 130, 246, 0.5));
    transition: all 0.3s ease;
    position: relative;
    z-index: 1;
}

.nav-logo:hover .logo-img {
    transform: scale(1.05) rotate(5deg);
}

.nav-divider {
    width: 1px;
    height: 2rem;
    background: rgba(255, 255, 255, 0.2);
}

.nav-links {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.nav-link {
    position: relative;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border-radius: 9999px;
    font-size: 0.875rem;
    font-weight: 600;
    color: rgba(255, 255, 255, 0.6);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    overflow: hidden;
}

.nav-link::before {
    content: '';
    position: absolute;
    inset: 0;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 9999px;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.nav-link:hover::before {
    opacity: 1;
}

.nav-link:hover {
    color: white;
    transform: translateY(-1px);
}

.nav-link.active {
    background: rgba(var(--primary-color-rgb, 59, 130, 246), 0.15);
    color: white;
}

.nav-link.active::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 20px;
    height: 2px;
    background: var(--primary-color, #3b82f6);
    border-radius: 2px;
}

.nav-link-indicator {
    position: absolute;
    bottom: 2px;
    left: 50%;
    transform: translateX(-50%);
    width: 0;
    height: 2px;
    background: var(--primary-color, #3b82f6);
    border-radius: 2px;
    transition: width 0.3s ease;
}

.nav-link.active .nav-link-indicator {
    width: 20px;
}

.nav-link-cta {
    background: linear-gradient(135deg, rgba(34, 197, 94, 0.2), rgba(22, 163, 74, 0.2));
    color: rgb(134, 239, 172);
    position: relative;
    overflow: hidden;
}

.nav-link-shine {
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    animation: shine 3s ease-in-out infinite;
}

@keyframes shine {
    to {
        left: 100%;
    }
}

.nav-link-danger {
    color: rgba(248, 113, 113, 0.8);
}

.nav-link-danger.active {
    background: rgba(239, 68, 68, 0.2);
    color: rgb(252, 165, 165);
}

.pulse-icon {
    animation: pulse 2s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.1);
    }
}

.nav-link-icon {
    font-size: 1rem;
    position: relative;
    z-index: 1;
}

.nav-link-text {
    position: relative;
    z-index: 1;
}

.nav-user {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding-left: 0.75rem;
}

.user-divider {
    width: 1px;
    height: 2rem;
    background: rgba(255, 255, 255, 0.2);
}

.user-btn {
    position: relative;
    display: flex;
    height: 2.5rem;
    width: 2.5rem;
    align-items: center;
    justify-content: center;
    border-radius: 9999px;
    transition: transform 0.2s;
}

.user-btn:hover {
    transform: scale(1.05);
}

.user-avatar-ring {
    position: absolute;
    inset: -2px;
    border-radius: 9999px;
    background: linear-gradient(135deg, var(--primary-color, #3b82f6), var(--secondary-color, #a855f7));
    opacity: 0;
    transition: opacity 0.3s ease;
}

.user-btn:hover .user-avatar-ring {
    opacity: 1;
    animation: rotate 3s linear infinite;
}

@keyframes rotate {
    to {
        transform: rotate(360deg);
    }
}

.user-avatar {
    width: 100%;
    height: 100%;
    border-radius: 9999px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    font-weight: 700;
    color: white;
    position: relative;
    z-index: 1;
}

/* Dropdown */
.dropdown-content {
    padding: 0.75rem;
    border-radius: 1.5rem;
    background: rgba(18, 18, 20, 0.95);
    border: 1px solid rgba(255, 255, 255, 0.1);
    box-shadow: 0 20px 50px rgba(0, 0, 0, 0.8);
    backdrop-filter: blur(20px);
    animation: dropdown-in 0.3s ease;
}

@keyframes dropdown-in {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.dropdown-header {
    padding: 0.75rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.dropdown-avatar {
    width: 3rem;
    height: 3rem;
    border-radius: 9999px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    font-weight: 700;
    color: white;
}

.dropdown-user-info {
    flex: 1;
    min-width: 0;
}

.dropdown-name {
    font-size: 0.875rem;
    font-weight: 700;
    color: white;
    overflow: hidden;
    text-overflow: ellipsis;
}

.dropdown-email {
    font-size: 0.75rem;
    color: rgb(156, 163, 175);
    overflow: hidden;
    text-overflow: ellipsis;
    margin-top: 0.125rem;
}

.admin-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.25rem 0.5rem;
    font-size: 0.625rem;
    font-weight: 700;
    border-radius: 9999px;
    background: rgba(239, 68, 68, 0.2);
    color: rgb(252, 165, 165);
    margin-top: 0.375rem;
}

.dropdown-section {
    padding: 0.5rem 0;
}

.dropdown-link {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.625rem 0.75rem;
    border-radius: 0.75rem;
    color: rgb(209, 213, 219);
    transition: all 0.2s;
    margin: 0 0.25rem;
    font-weight: 500;
}

.dropdown-link:hover {
    color: white;
    background: rgba(255, 255, 255, 0.1);
    transform: translateX(2px);
}

.dropdown-link-danger {
    color: rgb(248, 113, 113);
}

.dropdown-link-danger:hover {
    background: rgba(239, 68, 68, 0.15);
}

.dropdown-divider {
    height: 1px;
    margin: 0.5rem 0.75rem;
    background: rgba(255, 255, 255, 0.1);
}

.dropdown-footer {
    padding: 0.5rem 0;
}

/* Mobile Navigation */
.mobile-nav {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    z-index: 40;
    display: block;
    transition: transform 0.3s ease;
}

@media (min-width: 1024px) {
    .mobile-nav {
        display: none;
    }
}

.mobile-nav.mobile-nav-hidden {
    transform: translateY(100%);
}

.mobile-nav-bar {
    position: relative;
    background: rgba(18, 18, 20, 0.95);
    backdrop-filter: blur(30px);
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    box-shadow: 0 -10px 30px rgba(0, 0, 0, 0.8);
    padding: 0.75rem 0.5rem calc(0.75rem + env(safe-area-inset-bottom));
}

.mobile-nav-ripple {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 2px;
    background: linear-gradient(90deg, 
        transparent,
        var(--primary-color, #3b82f6),
        var(--secondary-color, #a855f7),
        transparent
    );
    animation: nav-ripple 3s ease-in-out infinite;
}

@keyframes nav-ripple {
    0%, 100% {
        transform: translateX(-100%);
        opacity: 0;
    }
    50% {
        opacity: 1;
    }
}

.mobile-nav-items {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 0.25rem;
    max-width: 28rem;
    margin: 0 auto;
}

.mobile-nav-item {
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.25rem;
    padding: 0.625rem 0.5rem;
    border-radius: 1rem;
    color: rgba(255, 255, 255, 0.5);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.mobile-nav-item:active {
    transform: scale(0.95);
}

.mobile-nav-item-active {
    color: white;
}

.mobile-nav-icon-wrap {
    position: relative;
    width: 2.5rem;
    height: 2.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

.mobile-nav-glow {
    position: absolute;
    inset: -4px;
    border-radius: 50%;
    background: radial-gradient(circle, var(--primary-color, #3b82f6), transparent);
    opacity: 0;
    transition: opacity 0.3s ease;
    filter: blur(8px);
}

.mobile-nav-item-active .mobile-nav-glow {
    opacity: 0.5;
    animation: glow-pulse 2s ease-in-out infinite;
}

@keyframes glow-pulse {
    0%, 100% {
        transform: scale(0.8);
        opacity: 0.3;
    }
    50% {
        transform: scale(1);
        opacity: 0.6;
    }
}

.mobile-nav-icon {
    font-size: 1.5rem;
    position: relative;
    z-index: 1;
}

.mobile-nav-label {
    font-size: 0.75rem;
    font-weight: 600;
    position: relative;
    z-index: 1;
}

/* Enhanced Sidebar */
.sidebar-drawer {
    position: fixed;
    top: 0;
    right: 0;
    bottom: 0;
    width: 85vw;
    max-width: 400px;
    z-index: 50;
    background: rgba(18, 18, 20, 0.98);
    backdrop-filter: blur(30px);
    border-left: 1px solid rgba(255, 255, 255, 0.1);
    box-shadow: -20px 0 50px rgba(0, 0, 0, 0.9);
    display: flex;
    flex-direction: column;
}

.sidebar-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1.5rem 1.25rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.sidebar-title-wrap {
    flex: 1;
}

.sidebar-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: white;
    margin-bottom: 0.25rem;
}

.sidebar-subtitle {
    font-size: 0.75rem;
    color: rgba(255, 255, 255, 0.5);
}

.sidebar-close {
    width: 2.5rem;
    height: 2.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 0.75rem;
    color: white;
    background: rgba(255, 255, 255, 0.05);
    transition: all 0.2s;
}

.sidebar-close:hover {
    background: rgba(255, 255, 255, 0.1);
    transform: rotate(90deg);
}

.sidebar-content {
    flex: 1;
    overflow-y: auto;
    padding: 1rem;
}

.sidebar-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    border-radius: 1rem;
    background: rgba(255, 255, 255, 0.05);
    margin-bottom: 0.75rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    opacity: 0;
    animation: slide-in 0.4s ease forwards;
}

@keyframes slide-in {
    from {
        opacity: 0;
        transform: translateX(20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.sidebar-item:hover {
    background: rgba(255, 255, 255, 0.1);
    transform: translateX(-4px);
}

.sidebar-item-icon {
    position: relative;
    width: 3rem;
    height: 3rem;
    border-radius: 0.875rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    flex-shrink: 0;
}

.sidebar-item-glow {
    position: absolute;
    inset: -2px;
    border-radius: 0.875rem;
    background: inherit;
    filter: blur(8px);
    opacity: 0.5;
    z-index: -1;
}

.sidebar-item-name {
    flex: 1;
    font-size: 0.9375rem;
    font-weight: 600;
    color: white;
}

.sidebar-item-arrow {
    width: 1.25rem;
    height: 1.25rem;
    color: rgba(255, 255, 255, 0.4);
    transition: all 0.2s;
}

.sidebar-item:hover .sidebar-item-arrow {
    color: white;
    transform: translateX(4px);
}

.sidebar-footer {
    padding: 1rem;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 0.75rem;
}

.sidebar-footer-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    padding: 1rem;
    border-radius: 1rem;
    background: rgba(255, 255, 255, 0.05);
    color: white;
    font-size: 0.875rem;
    font-weight: 600;
    transition: all 0.2s;
}

.sidebar-footer-btn:hover {
    background: rgba(255, 255, 255, 0.1);
    transform: translateY(-2px);
}

.sidebar-footer-btn-danger {
    background: rgba(239, 68, 68, 0.15);
    color: rgb(252, 165, 165);
}

.sidebar-footer-btn-danger:hover {
    background: rgba(239, 68, 68, 0.25);
}

/* Sidebar Transitions */
.sidebar-enter-active,
.sidebar-leave-active {
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.sidebar-enter-from {
    transform: translateX(100%);
}

.sidebar-leave-to {
    transform: translateX(100%);
}

/* Backdrop */
.sidebar-backdrop {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.7);
    backdrop-filter: blur(4px);
    z-index: 49;
}

.backdrop-enter-active,
.backdrop-leave-active {
    transition: opacity 0.3s ease;
}

.backdrop-enter-from,
.backdrop-leave-to {
    opacity: 0;
}

/* Main Content */
.main-content {
    position: relative;
    z-index: 10;
    min-height: 100vh;
    padding-top: 5rem;
    padding-bottom: 6rem;
    transition: filter 0.3s ease;
}

@media (min-width: 1024px) {
    .main-content {
        padding-top: 6rem;
        padding-bottom: 2rem;
    }
}

.main-loading {
    filter: blur(4px);
    pointer-events: none;
}

/* Chat Wrapper */
.chat-wrapper {
    position: fixed;
    bottom: 6rem;
    right: 1rem;
    z-index: 30;
}

@media (min-width: 1024px) {
    .chat-wrapper {
        bottom: 1.5rem;
        right: 1.5rem;
    }
}

/* Enhanced Loading Screen */
.loading-screen {
    position: fixed;
    inset: 0;
    z-index: 100;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: rgba(0, 0, 0, 0.95);
    backdrop-filter: blur(20px);
}

.loading-spinner {
    position: relative;
    width: 80px;
    height: 80px;
}

.spinner-ring {
    position: absolute;
    inset: 0;
    border-radius: 50%;
    border: 3px solid transparent;
    animation: spinner-rotate 1.5s cubic-bezier(0.5, 0, 0.5, 1) infinite;
}

.spinner-ring:nth-child(1) {
    border-top-color: var(--primary-color, #3b82f6);
    animation-delay: -0.45s;
}

.spinner-ring:nth-child(2) {
    border-right-color: var(--secondary-color, #a855f7);
    animation-delay: -0.3s;
}

.spinner-ring:nth-child(3) {
    border-bottom-color: #ec4899;
    animation-delay: -0.15s;
}

@keyframes spinner-rotate {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(360deg);
    }
}

.loading-text {
    margin-top: 1.5rem;
    font-size: 1rem;
    font-weight: 600;
    color: rgba(255, 255, 255, 0.7);
    letter-spacing: 0.05em;
}

.loading-enter-active {
    transition: opacity 0.2s ease;
}

.loading-leave-active {
    transition: opacity 0.3s ease;
}

.loading-enter-from,
.loading-leave-to {
    opacity: 0;
}

/* Scrollbar Styling */
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: rgba(0, 0, 0, 0.3);
}

::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.2);
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.3);
}

/* Mobile Responsive Adjustments */
@media (max-width: 768px) {
    .nav-link-text {
        display: none;
    }
    
    .nav-link {
        padding: 0.5rem;
    }
}

/* Accessibility */
@media (prefers-reduced-motion: reduce) {
    *,
    *::before,
    *::after {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
}

/* High Contrast Mode */
@media (prefers-contrast: high) {
    .nav-container,
    .mobile-nav-bar,
    .sidebar-drawer {
        border-width: 2px;
    }
    
    .nav-link:hover,
    .mobile-nav-item-active {
        outline: 2px solid currentColor;
    }
}

/* Dark Mode Adjustments (if needed) */
@media (prefers-color-scheme: light) {
    /* Add light mode overrides here if needed */
}
</style>