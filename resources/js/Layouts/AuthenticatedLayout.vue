<script setup>
import ChatAssistant from '@/Components/ChatAssistant.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NotificationCenter from '@/Components/NotificationCenter.vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed, onMounted, onUnmounted, ref, watch } from 'vue';

const showingNavigationDropdown = ref(false);
const showingMobileMenu = ref(false);
const page = usePage();
const user = computed(() => page.props.auth.user);
const logoUrl = '/images/enotpili.png'; 
const isLoading = ref(false);
const isMobileView = ref(false);
const isTabletView = ref(false);
const scrolled = ref(false);
const scrollProgress = ref(0);
const lastScrollTop = ref(0);
const isScrollingDown = ref(false);
const mouseX = ref(0);
const mouseY = ref(0);

// Check if user is a business owner
const isBusinessOwner = computed(() => {
    return user.value.role === 'business_owner' || user.value.has_business;
});

// Enhanced viewport detection
const updateViewport = () => {
    const width = window.innerWidth;
    isMobileView.value = width < 768;
    isTabletView.value = width >= 768 && width < 1024;
};

// Enhanced scroll detection with RAF
let scrollRAF = null;
const handleScroll = () => {
    if (scrollRAF) return;
    
    scrollRAF = requestAnimationFrame(() => {
        const currentScroll = window.scrollY;
        scrolled.value = currentScroll > 20;
        isScrollingDown.value = currentScroll > lastScrollTop.value && currentScroll > 100;
        lastScrollTop.value = currentScroll;
        
        const winScroll = document.documentElement.scrollTop;
        const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
        scrollProgress.value = height > 0 ? (winScroll / height) * 100 : 0;
        
        scrollRAF = null;
    });
};

// Mouse tracking with RAF
let mouseRAF = null;
const handleMouseMove = (e) => {
    if (mouseRAF) return;
    
    mouseRAF = requestAnimationFrame(() => {
        mouseX.value = e.clientX;
        mouseY.value = e.clientY;
        mouseRAF = null;
    });
};

// Debounced resize handler
let resizeTimeout;
const checkViewport = () => {
    clearTimeout(resizeTimeout);
    resizeTimeout = setTimeout(updateViewport, 100);
};

onMounted(() => {
    updateViewport();
    window.addEventListener('resize', checkViewport, { passive: true });
    window.addEventListener('scroll', handleScroll, { passive: true });
    window.addEventListener('mousemove', handleMouseMove, { passive: true });
    
    // Preload critical assets
    const link = document.createElement('link');
    link.rel = 'preload';
    link.as = 'image';
    link.href = logoUrl;
    document.head.appendChild(link);
});

onUnmounted(() => {
    window.removeEventListener('resize', checkViewport);
    window.removeEventListener('scroll', handleScroll);
    window.removeEventListener('mousemove', handleMouseMove);
    clearTimeout(resizeTimeout);
    if (scrollRAF) cancelAnimationFrame(scrollRAF);
    if (mouseRAF) cancelAnimationFrame(mouseRAF);
});

// Avatar gradient
const getAvatarGradient = (name) => {
    const gradients = [
        'from-blue-400 via-blue-500 to-blue-600', 
        'from-purple-400 via-purple-500 to-purple-600', 
        'from-pink-400 via-pink-500 to-pink-600',
        'from-orange-400 via-orange-500 to-orange-600',
        'from-green-400 via-green-500 to-green-600',
        'from-cyan-400 via-cyan-500 to-cyan-600',
        'from-rose-400 via-rose-500 to-rose-600',
        'from-indigo-400 via-indigo-500 to-indigo-600',
        'from-teal-400 via-teal-500 to-teal-600',
        'from-amber-400 via-amber-500 to-amber-600',
    ];
    const hash = (name?.split('').reduce((acc, char) => acc + char.charCodeAt(0), 0) || 0);
    return gradients[hash % gradients.length];
};

// Optimized loading transitions
let minLoadTime = 200;
let startTime = 0;

router.on('start', () => { 
    startTime = Date.now();
    isLoading.value = true;
    showingMobileMenu.value = false;
});

router.on('finish', () => { 
    const elapsed = Date.now() - startTime;
    const remaining = Math.max(0, minLoadTime - elapsed);
    
    setTimeout(() => {
        requestAnimationFrame(() => {
            isLoading.value = false;
        });
    }, remaining);
});

// Close mobile menu when clicking outside
watch(showingMobileMenu, (newVal) => {
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
            { name: 'Emergency', route: 'emergency.index', icon: '🚨', current: 'emergency.*', danger: true },
        ];
    }
});
</script>

<template>
    <div class="app-container">
        
        <!-- Optimized Scroll Progress -->
        <div class="scroll-progress-container">
            <div class="scroll-progress-bar" :style="{ width: scrollProgress + '%' }"></div>
        </div>

        <!-- Optimized Background -->
        <div class="bg-layer">
            <!-- Mobile Background -->
            <div class="mobile-bg">
                <div class="bg-gradient-base"></div>
                <div class="bg-grain"></div>
                <div 
                    class="bg-glow bg-glow-1" 
                    :style="{ transform: `translate(${mouseX * 0.015}px, ${mouseY * 0.015}px)` }"
                ></div>
                <div 
                    class="bg-glow bg-glow-2" 
                    :style="{ transform: `translate(${-mouseX * 0.01}px, ${-mouseY * 0.01}px)` }"
                ></div>
                <div 
                    class="bg-glow bg-glow-3" 
                    :style="{ transform: `translate(${mouseX * 0.008}px, ${mouseY * 0.008}px)` }"
                ></div>
            </div>

            <!-- Desktop Background -->
            <div class="desktop-bg">
                <div class="bg-gradient-base"></div>
                <div class="bg-grain"></div>
                <div 
                    class="bg-glow bg-glow-1" 
                    :style="{ transform: `translate(${mouseX * 0.02}px, ${mouseY * 0.02}px)` }"
                ></div>
                <div 
                    class="bg-glow bg-glow-2" 
                    :style="{ transform: `translate(${-mouseX * 0.015}px, ${-mouseY * 0.015}px)` }"
                ></div>
                <div 
                    class="bg-glow bg-glow-3" 
                    :style="{ transform: `translate(${mouseX * 0.01}px, ${mouseY * 0.01}px)` }"
                ></div>
                <div class="bg-mesh"></div>
            </div>
        </div>

        <!-- Optimized Desktop Navigation -->
        <nav 
            class="desktop-nav"
            :class="{ 
                'nav-hidden': isLoading,
                'nav-scrolled': scrolled,
                'nav-scrolling-down': isScrollingDown
            }"
        >
            <div class="nav-container">
                <div class="nav-glass"></div>
                <div class="nav-border"></div>
                
                <!-- Logo -->
                <Link :href="route('dashboard')" class="nav-logo">
                    <div class="logo-glow-wrapper">
                        <img :src="logoUrl" alt="E-Pili Logo" class="logo-img" />
                    </div>
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
                    </Link>

                    <template v-if="user.role === 'admin'">
                        <Link 
                            :href="route('admin.analytics')" 
                            :class="['nav-link', route().current('admin.analytics') ? 'active' : '']"
                        >
                            <span class="nav-link-icon">📊</span>
                            <span class="nav-link-text">Command</span>
                        </Link>
                        <Link 
                            :href="route('admin.documents.index')" 
                            :class="['nav-link', route().current('admin.documents.*') ? 'active' : '']"
                        >
                            <span class="nav-link-icon">✅</span>
                            <span class="nav-link-text">Approvals</span>
                        </Link>
                        <Link 
                            :href="route('marketplace.index')" 
                            :class="['nav-link', route().current('marketplace.*') ? 'active' : '']"
                        >
                            <span class="nav-link-icon">🏪</span>
                            <span class="nav-link-text">Marketplace</span>
                        </Link>
                    </template>

                    <template v-else>
                        <Link 
                            :href="route('profile.barangay.show')" 
                            :class="['nav-link', route().current('profile.barangay.*') ? 'active' : '']"
                        >
                            <span class="nav-link-icon">👤</span>
                            <span class="nav-link-text">Profile</span>
                        </Link>

                        <Link 
                            :href="route('marketplace.index')" 
                            :class="['nav-link', route().current('marketplace.*') ? 'active' : '']"
                        >
                            <span class="nav-link-icon">🏪</span>
                            <span class="nav-link-text">Market</span>
                        </Link>

                        <Link 
                            v-if="isBusinessOwner" 
                            :href="route('business.dashboard')" 
                            :class="['nav-link', route().current('business.dashboard') ? 'active' : '']"
                        >
                            <span class="nav-link-icon">📊</span>
                            <span class="nav-link-text">Business</span>
                        </Link>

                        <Link 
                            v-else
                            :href="route('business.register')" 
                            :class="['nav-link nav-link-cta', route().current('business.register') ? 'active' : '']"
                        >
                            <span class="nav-link-icon">🚀</span>
                            <span class="nav-link-text">Start Business</span>
                        </Link>

                        <Link 
                            :href="route('services.index')" 
                            :class="['nav-link', route().current('services.*') ? 'active' : '']"
                        >
                            <span class="nav-link-icon">📄</span>
                            <span class="nav-link-text">Docs</span>
                        </Link>
                        
                        <Link 
                            :href="route('market.index')" 
                            :class="['nav-link', route().current('market.*') ? 'active' : '']"
                        >
                            <span class="nav-link-icon">🌾</span>
                            <span class="nav-link-text">Prices</span>
                        </Link>
                        
                        <Link 
                            :href="route('emergency.index')" 
                            :class="['nav-link nav-link-danger', route().current('emergency.*') ? 'active active-danger' : '']"
                        >
                            <span class="nav-link-icon">🚨</span>
                            <span class="nav-link-text">Emergency</span>
                        </Link>
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
                                <div :class="`user-avatar ${getAvatarGradient(user.name)}`">
                                    <span>{{ user.name.charAt(0) }}</span>
                                </div>
                                <div class="user-status"></div>
                            </button>
                        </template>
                        <template #content>
                            <div class="dropdown-content">
                                <div class="dropdown-header">
                                    <div :class="`dropdown-avatar ${getAvatarGradient(user.name)}`">
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
                                    <div class="dropdown-section-title">Account</div>
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
                                    <div class="dropdown-section-title">Resources</div>
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
            <div class="mobile-nav-wrapper">
                <!-- Center Button -->
                <div class="mobile-center-btn-container">
                    <button @click="showingMobileMenu = !showingMobileMenu" class="mobile-center-btn">
                        <div class="center-btn-pulse" :class="{'opacity-0': showingMobileMenu}"></div>
                        <div class="center-btn-glow"></div>
                        <div class="center-btn-inner" :class="{'rotate-180 scale-110': showingMobileMenu}">
                            <span class="center-btn-icon">✨</span>
                        </div>
                    </button>
                </div>

                <!-- Nav Bar -->
                <div class="mobile-nav-bar">
                    <div class="mobile-nav-glass"></div>
                    
                    <div class="mobile-nav-items">
                        <Link 
                            v-for="item in mobileNavItems" 
                            :key="item.route"
                            :href="route(item.route)"
                            :class="['mobile-nav-item', {
                                'mobile-nav-item-active': route().current(item.current),
                                'mobile-nav-item-danger': item.danger
                            }]"
                        >
                            <span class="mobile-nav-icon">{{ item.icon }}</span>
                            <span class="mobile-nav-label">{{ item.name }}</span>
                        </Link>
                    </div>
                    
                    <div class="mobile-home-indicator"></div>
                </div>
            </div>
        </nav>

        <!-- Mobile More Menu -->
        <Transition name="mobile-menu">
            <div v-show="showingMobileMenu" class="mobile-menu">
                <!-- Profile Card -->
                <div class="mobile-profile">
                    <div class="mobile-profile-glass"></div>
                    
                    <div class="mobile-profile-content">
                        <div :class="`mobile-profile-avatar ${getAvatarGradient(user.name)}`">
                            <span>{{ user.name.charAt(0) }}</span>
                            <div class="mobile-profile-status"></div>
                        </div>
                        
                        <div class="mobile-profile-info">
                            <p class="mobile-profile-name">{{ user.name }}</p>
                            <p class="mobile-profile-email">{{ user.email }}</p>
                            <div class="mobile-profile-badges">
                                <span v-if="user.role === 'admin'" class="profile-badge profile-badge-admin">
                                    <span>👑</span>
                                    <span>Admin</span>
                                </span>
                                <span v-else class="profile-badge profile-badge-user">
                                    <span>👤</span>
                                    <span>{{ isBusinessOwner ? 'Business' : 'Citizen' }}</span>
                                </span>
                            </div>
                        </div>
                        
                        <NotificationCenter />
                    </div>
                    
                    <div class="mobile-profile-actions">
                        <Link :href="route('profile.edit')" class="profile-action-btn">
                            <span>⚙️</span>
                            <span>Settings</span>
                        </Link>
                        <Link :href="route('logout')" method="post" as="button" class="profile-action-btn profile-action-btn-danger">
                            <span>🚪</span>
                            <span>Logout</span>
                        </Link>
                    </div>
                </div>

                <!-- Menu Grid -->
                <div class="mobile-menu-grid">
                    <div class="mobile-menu-glass"></div>
                    
                    <div class="mobile-menu-header">
                        <h3 class="mobile-menu-title">Quick Actions</h3>
                    </div>
                    
                    <div class="mobile-menu-items">
                        <template v-if="user.role !== 'admin'">
                            <Link 
                                v-if="isBusinessOwner" 
                                :href="route('business.dashboard')" 
                                class="mobile-menu-card"
                            >
                                <span class="menu-card-icon">📊</span>
                                <div class="menu-card-content">
                                    <span class="menu-card-title">My Business</span>
                                    <span class="menu-card-desc">Dashboard</span>
                                </div>
                            </Link>
                            <Link 
                                v-else
                                :href="route('business.register')" 
                                class="mobile-menu-card menu-card-highlight"
                            >
                                <span class="menu-card-icon">🚀</span>
                                <div class="menu-card-content">
                                    <span class="menu-card-title">Start Business</span>
                                    <span class="menu-card-desc">Register Now</span>
                                </div>
                            </Link>
                            
                            <Link :href="route('services.index')" class="mobile-menu-card">
                                <span class="menu-card-icon">📄</span>
                                <div class="menu-card-content">
                                    <span class="menu-card-title">Documents</span>
                                    <span class="menu-card-desc">Track Requests</span>
                                </div>
                            </Link>
                            
                            <Link :href="route('market.index')" class="mobile-menu-card">
                                <span class="menu-card-icon">🌾</span>
                                <div class="menu-card-content">
                                    <span class="menu-card-title">Market Prices</span>
                                    <span class="menu-card-desc">Live Rates</span>
                                </div>
                            </Link>
                        </template>
                        
                        <Link :href="route('proposal')" class="mobile-menu-card">
                            <span class="menu-card-icon">📖</span>
                            <div class="menu-card-content">
                                <span class="menu-card-title">Proposal</span>
                                <span class="menu-card-desc">Documents</span>
                            </div>
                        </Link>
                        
                        <Link :href="route('simulation')" class="mobile-menu-card">
                            <span class="menu-card-icon">📚</span>
                            <div class="menu-card-content">
                                <span class="menu-card-title">Handbook</span>
                                <span class="menu-card-desc">Guide</span>
                            </div>
                        </Link>
                        
                        <Link :href="route('training')" class="mobile-menu-card">
                            <span class="menu-card-icon">🎓</span>
                            <div class="menu-card-content">
                                <span class="menu-card-title">Training</span>
                                <span class="menu-card-desc">Assessment</span>
                            </div>
                        </Link>
                    </div>
                </div>
            </div>
        </Transition>

        <!-- Backdrop -->
        <Transition name="backdrop">
            <div 
                v-show="showingMobileMenu" 
                @click="showingMobileMenu = false"
                class="mobile-backdrop"
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

        <!-- Loading Screen -->
        <Transition name="loading">
            <div v-if="isLoading" class="loading-screen">
                <div class="loading-glow"></div>
                <div class="loading-logo-wrapper">
                    <div class="loading-ring"></div>
                    <img :src="logoUrl" class="loading-logo" alt="Loading" />
                </div>
                <div class="loading-dots">
                    <div v-for="i in 3" :key="i" class="loading-dot" :style="{ animationDelay: `${(i - 1) * 0.15}s` }"></div>
                </div>
                <p class="loading-text">Loading...</p>
            </div>
        </Transition>
    </div>
</template>

<style scoped>
/* Performance Optimizations */
* {
    backface-visibility: hidden;
    -webkit-backface-visibility: hidden;
}

.app-container {
    min-height: 100vh;
    background: #000;
    color: #f1f5f9;
    font-family: -apple-system, BlinkMacSystemFont, 'SF Pro Display', 'Segoe UI', sans-serif;
    -webkit-font-smoothing: antialiased;
    position: relative;
    overflow-x: hidden;
}

/* Scroll Progress - Optimized */
.scroll-progress-container {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: rgba(0, 0, 0, 0.3);
    z-index: 201;
    pointer-events: none;
    will-change: transform;
}

.scroll-progress-bar {
    height: 100%;
    background: linear-gradient(to right, #3b82f6, #a855f7, #ec4899);
    box-shadow: 0 0 20px rgba(59, 130, 246, 0.6);
    transition: width 0.1s ease-out;
    will-change: width;
}

/* Optimized Backgrounds */
.bg-layer {
    position: fixed;
    inset: 0;
    z-index: 1;
    overflow: hidden;
}

.mobile-bg,
.desktop-bg {
    position: absolute;
    inset: 0;
    will-change: transform;
}

.mobile-bg {
    display: block;
}

.desktop-bg {
    display: none;
}

@media (min-width: 1024px) {
    .mobile-bg {
        display: none;
    }
    .desktop-bg {
        display: block;
    }
}

.bg-gradient-base {
    position: absolute;
    inset: 0;
    background: radial-gradient(ellipse at top, #0f0f0f 0%, #050505 50%, #000 100%);
}

.bg-grain {
    position: absolute;
    inset: 0;
    opacity: 0.03;
    mix-blend-mode: overlay;
    background-image: url('data:image/svg+xml,%3Csvg viewBox="0 0 400 400" xmlns="http://www.w3.org/2000/svg"%3E%3Cfilter id="n"%3E%3CfeTurbulence type="fractalNoise" baseFrequency="3.5" numOctaves="4"/%3E%3C/filter%3E%3Crect width="100%25" height="100%25" filter="url(%23n)"/%3E%3C/svg%3E');
}

.bg-glow {
    position: absolute;
    border-radius: 50%;
    filter: blur(120px);
    animation: float-glow 30s ease-in-out infinite;
    will-change: transform;
}

.bg-glow-1 {
    top: 10%;
    left: 20%;
    width: 400px;
    height: 400px;
    background: radial-gradient(circle, rgba(59, 130, 246, 0.15), transparent 70%);
}

.bg-glow-2 {
    bottom: 10%;
    right: 20%;
    width: 450px;
    height: 450px;
    background: radial-gradient(circle, rgba(236, 72, 153, 0.12), transparent 70%);
    animation-delay: 5s;
}

.bg-glow-3 {
    top: 50%;
    left: 50%;
    width: 380px;
    height: 380px;
    background: radial-gradient(circle, rgba(168, 85, 247, 0.1), transparent 70%);
    animation-delay: 10s;
}

.bg-mesh {
    position: absolute;
    inset: 0;
    background: 
        linear-gradient(125deg, transparent 0%, rgba(59, 130, 246, 0.03) 40%, transparent 70%),
        linear-gradient(215deg, transparent 30%, rgba(236, 72, 153, 0.03) 60%, transparent 90%);
    animation: mesh-shift 40s ease-in-out infinite;
}

/* Desktop Navigation - Optimized */
.desktop-nav {
    position: fixed;
    top: 1.5rem;
    left: 0;
    right: 0;
    z-index: 40;
    display: none;
    justify-content: center;
    padding: 0 1rem;
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.3s;
    will-change: transform, opacity;
}

@media (min-width: 1024px) {
    .desktop-nav {
        display: flex;
    }
}

.desktop-nav.nav-hidden {
    transform: translateY(-100px);
    opacity: 0;
}

.desktop-nav.nav-scrolled {
    top: 0.75rem;
}

.desktop-nav.nav-scrolling-down {
    transform: translateY(-100px);
    opacity: 0;
}

.nav-container {
    position: relative;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.5rem 1rem;
    border-radius: 9999px;
    background: rgba(18, 18, 20, 0.8);
    border: 1px solid rgba(255, 255, 255, 0.12);
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.9);
    transition: all 0.3s;
    will-change: transform;
}

.nav-container:hover {
    transform: translateY(-2px);
    box-shadow: 0 24px 72px rgba(0, 0, 0, 0.95);
}

.nav-glass {
    position: absolute;
    inset: 0;
    border-radius: 9999px;
    backdrop-filter: blur(60px) saturate(200%);
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.08), rgba(255, 255, 255, 0.02));
}

.nav-border {
    position: absolute;
    inset: -1px;
    border-radius: 9999px;
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.3), rgba(168, 85, 247, 0.2), rgba(236, 72, 153, 0.3));
    filter: blur(8px);
    opacity: 0;
    transition: opacity 0.3s;
    z-index: -1;
}

.nav-container:hover .nav-border {
    opacity: 1;
}

.nav-logo {
    display: flex;
    align-items: center;
    padding: 0 0.5rem;
    transition: transform 0.3s;
    will-change: transform;
}

.nav-logo:hover {
    transform: scale(1.05);
}

.logo-glow-wrapper {
    position: relative;
}

.logo-img {
    height: 4rem;
    width: auto;
    filter: drop-shadow(0 10px 30px rgba(59, 130, 246, 0.4));
    transition: filter 0.3s;
    will-change: transform;
}

.nav-logo:hover .logo-img {
    filter: drop-shadow(0 12px 40px rgba(59, 130, 246, 0.6));
}

.nav-divider {
    width: 2px;
    height: 2rem;
    background: linear-gradient(to bottom, transparent, rgba(255, 255, 255, 0.2), transparent);
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
    transition: all 0.3s;
    will-change: transform;
}

.nav-link:hover {
    color: white;
    background: rgba(255, 255, 255, 0.12);
    transform: translateY(-1px);
}

.nav-link.active {
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.2), rgba(168, 85, 247, 0.15));
    color: white;
    box-shadow: 0 4px 20px rgba(59, 130, 246, 0.3);
}

.nav-link.active-danger {
    background: linear-gradient(135deg, rgba(239, 68, 68, 0.25), rgba(236, 72, 153, 0.25));
    color: rgb(252, 165, 165);
    box-shadow: 0 4px 20px rgba(239, 68, 68, 0.4);
}

.nav-link-cta {
    background: linear-gradient(135deg, rgba(34, 197, 94, 0.25), rgba(16, 185, 129, 0.25));
    color: rgb(134, 239, 172);
    box-shadow: 0 4px 20px rgba(34, 197, 94, 0.3);
}

.nav-link-icon {
    font-size: 1.125rem;
    transition: transform 0.3s;
}

.nav-link:hover .nav-link-icon {
    transform: scale(1.2);
}

.nav-user {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding-left: 0.75rem;
}

.user-divider {
    width: 2px;
    height: 2rem;
    background: linear-gradient(to bottom, transparent, rgba(255, 255, 255, 0.15), transparent);
}

.user-btn {
    position: relative;
    display: flex;
    height: 3rem;
    width: 3rem;
    align-items: center;
    justify-content: center;
    border-radius: 9999px;
    transition: transform 0.3s;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
    will-change: transform;
}

.user-btn:hover {
    transform: scale(1.1);
}

.user-avatar-ring {
    position: absolute;
    inset: -3px;
    border-radius: 9999px;
    border: 2px solid transparent;
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.5), rgba(168, 85, 247, 0.5)) border-box;
    -webkit-mask: linear-gradient(#fff 0 0) padding-box, linear-gradient(#fff 0 0);
    -webkit-mask-composite: xor;
    mask-composite: exclude;
    opacity: 0;
    transition: opacity 0.3s;
}

.user-btn:hover .user-avatar-ring {
    opacity: 1;
}

.user-avatar {
    position: absolute;
    inset: 0;
    border-radius: 9999px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    font-weight: 700;
    color: white;
}

.user-status {
    position: absolute;
    bottom: 0;
    right: 0;
    width: 0.75rem;
    height: 0.75rem;
    background: rgb(34, 197, 94);
    border-radius: 9999px;
    border: 2px solid rgba(18, 18, 20, 0.9);
    box-shadow: 0 0 8px rgba(34, 197, 94, 0.6);
    animation: pulse 2s infinite;
}

/* Dropdown - Simplified */
.dropdown-content {
    padding: 0.5rem;
    border-radius: 1.5rem;
    background: rgba(18, 18, 20, 0.98);
    border: 1px solid rgba(255, 255, 255, 0.15);
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.9);
    backdrop-filter: blur(60px);
    max-height: 80vh;
    overflow-y: auto;
}

.dropdown-header {
    padding: 1rem;
    display: flex;
    align-items: center;
    gap: 1rem;
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
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.5);
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
}

.admin-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
    font-weight: 700;
    border-radius: 9999px;
    background: linear-gradient(135deg, rgba(239, 68, 68, 0.25), rgba(236, 72, 153, 0.25));
    color: rgb(252, 165, 165);
    border: 1px solid rgba(239, 68, 68, 0.4);
    margin-top: 0.5rem;
}

.dropdown-section {
    padding: 0.5rem 0;
}

.dropdown-section-title {
    padding: 0.5rem 1rem;
    font-size: 0.75rem;
    font-weight: 700;
    color: rgba(255, 255, 255, 0.5);
    text-transform: uppercase;
}

.dropdown-link {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 1rem;
    border-radius: 1rem;
    color: rgb(209, 213, 219);
    transition: all 0.2s;
    margin: 0 0.5rem;
}

.dropdown-link:hover {
    color: white;
    background: rgba(255, 255, 255, 0.10);
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
    margin: 0.5rem 1rem;
    background: linear-gradient(to right, transparent, rgba(255, 255, 255, 0.1), transparent);
}

.dropdown-footer {
    padding: 0.5rem 0;
}

/* Mobile Navigation - Optimized */
.mobile-nav {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    z-index: 40;
    display: block;
    transition: transform 0.3s;
    will-change: transform;
}

@media (min-width: 1024px) {
    .mobile-nav {
        display: none;
    }
}

.mobile-nav.mobile-nav-hidden {
    transform: translateY(100%);
}

.mobile-nav-wrapper {
    position: relative;
}

.mobile-center-btn-container {
    position: absolute;
    top: -2.5rem;
    left: 50%;
    transform: translateX(-50%);
    z-index: 10;
}

.mobile-center-btn {
    position: relative;
}

.center-btn-pulse {
    position: absolute;
    inset: 0;
    border-radius: 9999px;
    background: linear-gradient(135deg, rgb(168, 85, 247), rgb(236, 72, 153));
    animation: ping 2s cubic-bezier(0, 0, 0.2, 1) infinite;
    will-change: transform, opacity;
}

.center-btn-glow {
    position: absolute;
    inset: 0;
    border-radius: 9999px;
    filter: blur(1.5rem);
    background: linear-gradient(135deg, rgb(147, 51, 234), rgb(236, 72, 153));
    opacity: 0.5;
    will-change: opacity;
}

.center-btn-inner {
    width: 4.5rem;
    height: 4.5rem;
    border-radius: 9999px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, rgb(147, 51, 234), rgb(236, 72, 153));
    box-shadow: 0 10px 40px rgba(217, 70, 239, 0.6);
    transition: transform 0.3s;
    will-change: transform;
}

.center-btn-icon {
    font-size: 1.75rem;
    filter: drop-shadow(0 4px 12px rgba(0, 0, 0, 0.5));
}

.mobile-nav-bar {
    position: relative;
    background: rgba(18, 18, 20, 0.98);
    backdrop-filter: blur(60px);
    border-top: 1px solid rgba(255, 255, 255, 0.12);
    box-shadow: 0 -20px 60px rgba(0, 0, 0, 0.9);
    padding: 1rem 1rem 2rem;
}

.mobile-nav-glass {
    position: absolute;
    inset: 0;
    background: linear-gradient(to top, rgba(255, 255, 255, 0.08), rgba(255, 255, 255, 0.02));
}

.mobile-nav-items {
    position: relative;
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 0.5rem;
    max-width: 28rem;
    margin: 0 auto;
}

.mobile-nav-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.25rem;
    padding: 0.75rem 0.5rem;
    border-radius: 1rem;
    color: rgba(255, 255, 255, 0.5);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    will-change: transform;
}

.mobile-nav-item:active {
    transform: scale(0.95);
}

.mobile-nav-item-active {
    color: white;
    background: rgba(59, 130, 246, 0.15);
    box-shadow: 0 4px 20px rgba(59, 130, 246, 0.3);
}

.mobile-nav-item-danger {
    color: rgba(248, 113, 113, 0.6);
}

.mobile-nav-item-danger.mobile-nav-item-active {
    color: rgb(252, 165, 165);
    background: rgba(239, 68, 68, 0.15);
    box-shadow: 0 4px 20px rgba(239, 68, 68, 0.3);
}

.mobile-nav-icon {
    font-size: 1.5rem;
    transition: transform 0.3s;
}

.mobile-nav-item-active .mobile-nav-icon {
    transform: scale(1.2);
    animation: bounce-soft 0.6s ease-out;
}

.mobile-nav-label {
    font-size: 0.75rem;
    font-weight: 600;
    text-align: center;
}

.mobile-home-indicator {
    position: absolute;
    bottom: 0.5rem;
    left: 50%;
    transform: translateX(-50%);
    width: 8rem;
    height: 0.375rem;
    border-radius: 9999px;
    background: rgba(255, 255, 255, 0.2);
}

/* Mobile Menu Overlay - Enhanced */
.mobile-menu {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 6rem;
    z-index: 50;
    overflow-y: auto;
    padding: 1.5rem 1rem 2rem;
    pointer-events: auto;
}

.mobile-profile {
    position: relative;
    background: rgba(18, 18, 20, 0.95);
    border-radius: 2rem;
    border: 1px solid rgba(255, 255, 255, 0.12);
    padding: 1.5rem;
    margin-bottom: 1rem;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.9);
    backdrop-filter: blur(60px);
}

.mobile-profile-glass {
    position: absolute;
    inset: 0;
    border-radius: 2rem;
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.08), rgba(168, 85, 247, 0.05));
}

.mobile-profile-content {
    position: relative;
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
}

.mobile-profile-avatar {
    position: relative;
    width: 4rem;
    height: 4rem;
    border-radius: 1.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    font-weight: 700;
    color: white;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
    flex-shrink: 0;
}

.mobile-profile-status {
    position: absolute;
    bottom: 0;
    right: 0;
    width: 1rem;
    height: 1rem;
    background: rgb(34, 197, 94);
    border-radius: 9999px;
    border: 3px solid rgba(18, 18, 20, 0.95);
    box-shadow: 0 0 12px rgba(34, 197, 94, 0.6);
    animation: pulse 2s infinite;
}

.mobile-profile-info {
    flex: 1;
    min-width: 0;
}

.mobile-profile-name {
    font-size: 1.125rem;
    font-weight: 700;
    color: white;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.mobile-profile-email {
    font-size: 0.875rem;
    color: rgb(156, 163, 175);
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.mobile-profile-badges {
    display: flex;
    gap: 0.5rem;
    margin-top: 0.5rem;
}

.profile-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.375rem 0.75rem;
    font-size: 0.75rem;
    font-weight: 700;
    border-radius: 9999px;
    border: 1px solid;
}

.profile-badge-admin {
    background: linear-gradient(135deg, rgba(239, 68, 68, 0.25), rgba(236, 72, 153, 0.25));
    color: rgb(252, 165, 165);
    border-color: rgba(239, 68, 68, 0.4);
}

.profile-badge-user {
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.25), rgba(168, 85, 247, 0.25));
    color: rgb(147, 197, 253);
    border-color: rgba(59, 130, 246, 0.4);
}

.mobile-profile-actions {
    position: relative;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.75rem;
}

.profile-action-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.875rem 1rem;
    border-radius: 1rem;
    font-size: 0.875rem;
    font-weight: 600;
    color: white;
    background: rgba(255, 255, 255, 0.10);
    border: 1px solid rgba(255, 255, 255, 0.15);
    transition: all 0.3s;
}

.profile-action-btn:active {
    transform: scale(0.97);
}

.profile-action-btn-danger {
    background: rgba(239, 68, 68, 0.15);
    border-color: rgba(239, 68, 68, 0.3);
    color: rgb(252, 165, 165);
}

.profile-action-btn-danger:hover {
    background: rgba(239, 68, 68, 0.25);
}

/* Mobile Menu Grid - Enhanced */
.mobile-menu-grid {
    position: relative;
    background: rgba(18, 18, 20, 0.95);
    border-radius: 2rem;
    border: 1px solid rgba(255, 255, 255, 0.12);
    padding: 1.5rem;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.9);
    backdrop-filter: blur(60px);
}

.mobile-menu-glass {
    position: absolute;
    inset: 0;
    border-radius: 2rem;
    background: linear-gradient(135deg, rgba(168, 85, 247, 0.05), rgba(236, 72, 153, 0.03));
}

.mobile-menu-header {
    position: relative;
    margin-bottom: 1rem;
}

.mobile-menu-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: white;
    background: linear-gradient(135deg, rgb(255, 255, 255), rgb(209, 213, 219));
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
}

.mobile-menu-items {
    position: relative;
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 0.75rem;
}

.mobile-menu-card {
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 0.75rem;
    padding: 1.25rem;
    border-radius: 1.5rem;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    overflow: hidden;
}

.mobile-menu-card::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), transparent);
    opacity: 0;
    transition: opacity 0.3s;
}

.mobile-menu-card:active {
    transform: scale(0.97);
}

.mobile-menu-card:active::before {
    opacity: 1;
}

.menu-card-highlight {
    background: linear-gradient(135deg, rgba(34, 197, 94, 0.15), rgba(16, 185, 129, 0.1));
    border-color: rgba(34, 197, 94, 0.3);
}

.menu-card-highlight::before {
    background: linear-gradient(135deg, rgba(34, 197, 94, 0.2), transparent);
}

.menu-card-icon {
    font-size: 2rem;
    filter: drop-shadow(0 4px 12px rgba(0, 0, 0, 0.3));
}

.menu-card-content {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.menu-card-title {
    font-size: 0.9375rem;
    font-weight: 700;
    color: white;
}

.menu-card-desc {
    font-size: 0.75rem;
    color: rgb(156, 163, 175);
}

.menu-card-highlight .menu-card-title {
    color: rgb(134, 239, 172);
}

.menu-card-highlight .menu-card-desc {
    color: rgb(110, 231, 183);
}

/* Mobile Backdrop */
.mobile-backdrop {
    position: fixed;
    inset: 0;
    z-index: 45;
    background: rgba(0, 0, 0, 0.8);
    backdrop-filter: blur(8px);
}

/* Main Content - Optimized */
.main-content {
    position: relative;
    z-index: 10;
    min-height: 100vh;
    padding-top: 2rem;
    padding-bottom: 8rem;
    transition: opacity 0.3s;
}

@media (min-width: 1024px) {
    .main-content {
        padding-top: 8rem;
        padding-bottom: 4rem;
    }
}

.main-content.main-loading {
    opacity: 0.7;
    pointer-events: none;
}

/* Chat Assistant Wrapper */
.chat-wrapper {
    position: relative;
    z-index: 30;
}

/* Loading Screen - Enhanced */
.loading-screen {
    position: fixed;
    inset: 0;
    z-index: 100;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: rgba(0, 0, 0, 0.95);
    backdrop-filter: blur(40px);
}

.loading-glow {
    position: absolute;
    inset: 0;
    background: radial-gradient(
        circle at center,
        rgba(59, 130, 246, 0.15),
        rgba(168, 85, 247, 0.1),
        transparent 70%
    );
    animation: pulse-glow 3s ease-in-out infinite;
}

.loading-logo-wrapper {
    position: relative;
    width: 8rem;
    height: 8rem;
    margin-bottom: 2rem;
}

.loading-ring {
    position: absolute;
    inset: -1rem;
    border: 3px solid transparent;
    border-top-color: rgb(59, 130, 246);
    border-right-color: rgb(168, 85, 247);
    border-radius: 9999px;
    animation: spin 2s linear infinite;
}

.loading-logo {
    width: 100%;
    height: 100%;
    object-fit: contain;
    filter: drop-shadow(0 20px 60px rgba(59, 130, 246, 0.6));
    animation: float 3s ease-in-out infinite;
}

.loading-dots {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 1rem;
}

.loading-dot {
    width: 0.75rem;
    height: 0.75rem;
    border-radius: 9999px;
    background: linear-gradient(135deg, rgb(59, 130, 246), rgb(168, 85, 247));
    animation: bounce-dot 1.2s ease-in-out infinite;
}

.loading-text {
    font-size: 1rem;
    font-weight: 600;
    color: rgba(255, 255, 255, 0.7);
    letter-spacing: 0.05em;
}

/* Animations */
@keyframes float-glow {
    0%, 100% { transform: translate(0, 0); }
    33% { transform: translate(30px, -30px); }
    66% { transform: translate(-30px, 30px); }
}

@keyframes mesh-shift {
    0%, 100% { transform: translate(0, 0); }
    50% { transform: translate(20px, 20px); }
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

@keyframes ping {
    0% { transform: scale(1); opacity: 1; }
    75%, 100% { transform: scale(2); opacity: 0; }
}

@keyframes bounce-soft {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.2); }
}

@keyframes pulse-glow {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

@keyframes float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}

@keyframes bounce-dot {
    0%, 80%, 100% { transform: scale(0); opacity: 0.5; }
    40% { transform: scale(1); opacity: 1; }
}

/* Transitions */
.mobile-menu-enter-active,
.mobile-menu-leave-active {
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.mobile-menu-enter-from {
    opacity: 0;
    transform: translateY(2rem);
}

.mobile-menu-leave-to {
    opacity: 0;
    transform: translateY(-2rem);
}

.backdrop-enter-active,
.backdrop-leave-active {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.backdrop-enter-from,
.backdrop-leave-to {
    opacity: 0;
}

.loading-enter-active {
    transition: all 0.2s ease-out;
}

.loading-leave-active {
    transition: all 0.3s ease-in;
}

.loading-enter-from {
    opacity: 0;
}

.loading-leave-to {
    opacity: 0;
    transform: scale(0.95);
}

/* Scrollbar Styling */
.dropdown-content::-webkit-scrollbar,
.mobile-menu::-webkit-scrollbar {
    width: 6px;
}

.dropdown-content::-webkit-scrollbar-track,
.mobile-menu::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 9999px;
}

.dropdown-content::-webkit-scrollbar-thumb,
.mobile-menu::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.2);
    border-radius: 9999px;
}

.dropdown-content::-webkit-scrollbar-thumb:hover,
.mobile-menu::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.3);
}

/* Responsive Enhancements */
@media (max-width: 374px) {
    .mobile-nav-items {
        gap: 0.25rem;
    }
    
    .mobile-nav-item {
        padding: 0.5rem 0.25rem;
    }
    
    .mobile-nav-icon {
        font-size: 1.25rem;
    }
    
    .mobile-nav-label {
        font-size: 0.625rem;
    }
}

@media (min-width: 640px) and (max-width: 1023px) {
    .mobile-menu-items {
        grid-template-columns: repeat(3, 1fr);
    }
    
    .mobile-nav-items {
        max-width: 36rem;
    }
}

/* Dark mode optimization */
@media (prefers-color-scheme: dark) {
    .app-container {
        color-scheme: dark;
    }
}

/* Reduce motion */
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
    .desktop-nav,
    .mobile-nav,
    .mobile-menu,
    .mobile-backdrop,
    .loading-screen,
    .chat-wrapper {
        display: none !important;
    }
    
    .main-content {
        padding: 0 !important;
    }
}

/* Safe area insets for notched devices */
@supports (padding: env(safe-area-inset-bottom)) {
    .mobile-nav-bar {
        padding-bottom: calc(2rem + env(safe-area-inset-bottom));
    }
    
    .mobile-menu {
        padding-top: calc(1.5rem);
        padding-bottom: calc(2rem + env(safe-area-inset-bottom));
    }
}

/* High contrast mode support */
@media (prefers-contrast: high) {
    .nav-container,
    .mobile-nav-bar,
    .mobile-profile,
    .mobile-menu-grid {
        border-width: 2px;
    }
    
    .nav-link.active,
    .mobile-nav-item-active {
        outline: 2px solid currentColor;
        outline-offset: 2px;
    }
}
</style>