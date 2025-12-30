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

// Check if user is a business owner
const isBusinessOwner = computed(() => {
    return user.value.role === 'business_owner' || user.value.has_business;
});

// Enhanced viewport detection with more breakpoints
const updateViewport = () => {
    const width = window.innerWidth;
    isMobileView.value = width < 768;
    isTabletView.value = width >= 768 && width < 1024;
};

// Enhanced scroll detection
const handleScroll = () => {
    const currentScroll = window.scrollY;
    scrolled.value = currentScroll > 20;
    isScrollingDown.value = currentScroll > lastScrollTop.value && currentScroll > 100;
    lastScrollTop.value = currentScroll;
    
    // Calculate scroll progress
    const winScroll = document.documentElement.scrollTop;
    const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
    scrollProgress.value = height > 0 ? (winScroll / height) * 100 : 0;
};

// Debounced resize handler
let resizeTimeout;
const checkViewport = () => {
    clearTimeout(resizeTimeout);
    resizeTimeout = setTimeout(updateViewport, 150);
};

onMounted(() => {
    updateViewport();
    window.addEventListener('resize', checkViewport, { passive: true });
    window.addEventListener('scroll', handleScroll, { passive: true });
    
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
    clearTimeout(resizeTimeout);
});

// Avatar gradient with more variety
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

// Enhanced loading transitions
let minLoadTime = 300;
let startTime = 0;
let rafId = null;

router.on('start', () => { 
    startTime = Date.now();
    isLoading.value = true;
    showingMobileMenu.value = false;
});

router.on('finish', () => { 
    const elapsed = Date.now() - startTime;
    const remaining = Math.max(0, minLoadTime - elapsed);
    
    if (rafId) cancelAnimationFrame(rafId);
    
    setTimeout(() => {
        rafId = requestAnimationFrame(() => {
            isLoading.value = false;
        });
    }, remaining);
});

// Close mobile menu when clicking outside
watch(showingMobileMenu, (newVal) => {
    if (newVal) {
        document.body.style.overflow = 'hidden';
    } else {
        document.body.style.overflow = '';
    }
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
        
        <!-- Premium Scroll Progress Indicator -->
        <div class="scroll-progress-container">
            <div class="scroll-progress-glow" :style="{ width: scrollProgress + '%' }"></div>
            <div class="scroll-progress-fill" :style="{ width: scrollProgress + '%' }"></div>
        </div>
        
        <!-- Enhanced Mobile Phone Frame -->
        <div class="mobile-phone-frame">
            <div class="notch-container">
                <div class="notch-gradient"></div>
                <div class="notch-glow"></div>
                <div class="notch-speaker"></div>
            </div>
            
            <div class="phone-frame">
                <div class="phone-frame-inner"></div>
                <div class="phone-frame-inner-2"></div>
                <div class="phone-frame-shine"></div>
                <div class="phone-frame-edge-glow"></div>
            </div>

            <div class="camera-lens">
                <div class="camera-lens-inner"></div>
                <div class="camera-lens-reflection"></div>
            </div>
        </div>

        <!-- Premium Mobile Background -->
        <div class="mobile-content-wrapper">
            <div class="mobile-bg-base"></div>
            
            <div class="mobile-ambient-container">
                <div class="mobile-ambient-grain"></div>
                <div class="mobile-particle mobile-particle-1"></div>
                <div class="mobile-particle mobile-particle-2"></div>
                <div class="mobile-particle mobile-particle-3"></div>
                <div class="mobile-glow mobile-glow-1"></div>
                <div class="mobile-glow mobile-glow-2"></div>
                <div class="mobile-glow mobile-glow-3"></div>
                <div class="mobile-radial-1"></div>
                <div class="mobile-radial-2"></div>
                <div class="mobile-mesh-gradient"></div>
            </div>
        </div>

        <!-- Premium Desktop Background -->
        <div class="desktop-bg-container">
            <div class="desktop-bg-base"></div>
            <div class="desktop-grain"></div>
            <div class="desktop-particle desktop-particle-1"></div>
            <div class="desktop-particle desktop-particle-2"></div>
            <div class="desktop-particle desktop-particle-3"></div>
            <div class="desktop-glow desktop-glow-1"></div>
            <div class="desktop-glow desktop-glow-2"></div>
            <div class="desktop-glow desktop-glow-3"></div>
            <div class="desktop-radial-1"></div>
            <div class="desktop-radial-2"></div>
            <div class="desktop-mesh-gradient"></div>
        </div>

        <!-- Premium Desktop Navigation -->
        <nav 
            class="desktop-nav"
            :class="{ 
                'nav-hidden': isLoading,
                'nav-scrolled': scrolled,
                'nav-scrolling-down': isScrollingDown
            }"
            role="navigation"
            aria-label="Main navigation"
        >
            <div class="nav-container group">
                <!-- Premium glass effects -->
                <div class="nav-glass-layer"></div>
                <div class="nav-glow"></div>
                <div class="nav-glow-hover"></div>
                <div class="nav-inner-glow"></div>
                <div class="nav-border-glow"></div>
                
                <!-- Logo with enhanced effects -->
                <Link 
                    :href="route('dashboard')" 
                    class="nav-logo group/logo"
                    aria-label="Go to dashboard"
                >
                    <div class="logo-wrapper">
                        <div class="logo-glow"></div>
                        <div class="logo-pulse"></div>
                        <img :src="logoUrl" alt="E-Pili Logo" class="logo-image" />
                        <div class="logo-shine"></div>
                    </div>
                </Link>

                <!-- Premium Divider -->
                <div class="nav-divider">
                    <div class="nav-divider-line"></div>
                    <div class="nav-divider-glow"></div>
                    <div class="nav-divider-pulse"></div>
                </div>

                <!-- Navigation Links -->
                <div class="nav-links" role="menubar">
                    <Link 
                        :href="route('dashboard')" 
                        :class="['nav-pill', route().current('dashboard') ? 'active' : '']"
                        role="menuitem"
                    >
                        <span class="nav-pill-icon">🏠</span>
                        <span class="nav-pill-text">Dashboard</span>
                        <div class="nav-pill-glow"></div>
                    </Link>

                    <template v-if="user.role === 'admin'">
                        <Link 
                            :href="route('admin.analytics')" 
                            :class="['nav-pill', route().current('admin.analytics') ? 'active-danger' : '']"
                            role="menuitem"
                        >
                            <span class="nav-pill-icon">📊</span>
                            <span class="nav-pill-text">Command</span>
                            <div class="nav-pill-glow"></div>
                        </Link>
                        <Link 
                            :href="route('admin.documents.index')" 
                            :class="['nav-pill', route().current('admin.documents.*') ? 'active' : '']"
                            role="menuitem"
                        >
                            <span class="nav-pill-icon">✅</span>
                            <span class="nav-pill-text">Approvals</span>
                            <div class="nav-pill-glow"></div>
                        </Link>
                        <Link 
                            :href="route('marketplace.index')" 
                            :class="['nav-pill', route().current('marketplace.*') ? 'active' : '']"
                            role="menuitem"
                        >
                            <span class="nav-pill-icon">🏪</span>
                            <span class="nav-pill-text">Marketplace</span>
                            <div class="nav-pill-glow"></div>
                        </Link>
                    </template>

                    <template v-else>
                        <Link 
                            :href="route('profile.barangay.show')" 
                            :class="['nav-pill nav-pill-profile', route().current('profile.barangay.*') ? 'active-profile' : '']"
                            role="menuitem"
                        >
                            <span class="nav-pill-icon">👤</span>
                            <span class="nav-pill-text">My Profile</span>
                            <div class="nav-pill-glow"></div>
                        </Link>

                        <Link 
                            :href="route('marketplace.index')" 
                            :class="['nav-pill', route().current('marketplace.*') ? 'active' : '']"
                            role="menuitem"
                        >
                            <span class="nav-pill-icon">🏪</span>
                            <span class="nav-pill-text">Marketplace</span>
                            <div class="nav-pill-glow"></div>
                        </Link>

                        <Link 
                            v-if="isBusinessOwner" 
                            :href="route('business.dashboard')" 
                            :class="['nav-pill nav-pill-special', route().current('business.dashboard') ? 'active-special' : '']"
                            role="menuitem"
                        >
                            <span class="nav-pill-icon">📊</span>
                            <span class="nav-pill-text">My Business</span>
                            <div class="nav-pill-glow"></div>
                        </Link>

                        <Link 
                            v-else
                            :href="route('business.register')" 
                            :class="['nav-pill nav-pill-cta', route().current('business.register') ? 'active' : '']"
                            role="menuitem"
                        >
                            <span class="nav-pill-icon">🚀</span>
                            <span class="nav-pill-text">Start Business</span>
                            <div class="nav-pill-glow"></div>
                        </Link>

                        <Link 
                            :href="route('services.index')" 
                            :class="['nav-pill', route().current('services.*') ? 'active' : '']"
                            role="menuitem"
                        >
                            <span class="nav-pill-icon">📄</span>
                            <span class="nav-pill-text">My Docs</span>
                            <div class="nav-pill-glow"></div>
                        </Link>
                        
                        <Link 
                            :href="route('market.index')" 
                            :class="['nav-pill', route().current('market.*') ? 'active' : '']"
                            role="menuitem"
                        >
                            <span class="nav-pill-icon">🌾</span>
                            <span class="nav-pill-text">Market</span>
                            <div class="nav-pill-glow"></div>
                        </Link>
                        
                        <Link 
                            :href="route('emergency.index')" 
                            :class="['nav-pill nav-pill-danger', route().current('emergency.*') ? 'active-danger' : '']"
                            role="menuitem"
                        >
                            <span class="nav-pill-icon">🚨</span>
                            <span class="nav-pill-text">Emergency</span>
                            <div class="nav-pill-glow"></div>
                        </Link>
                    </template>
                </div>

                <!-- Premium User Section -->
                <div class="nav-user-section">
                    <NotificationCenter />
                    <div class="nav-user-divider">
                        <div class="nav-user-divider-glow"></div>
                    </div>
                    
                    <!-- Enhanced User Dropdown -->
                    <Dropdown align="right" width="56">
                        <template #trigger>
                            <button class="user-avatar-btn group/avatar" aria-label="User menu">
                                <div class="user-avatar-ring"></div>
                                <div class="user-avatar-ring-pulse"></div>
                                <div :class="`user-avatar-gradient ${getAvatarGradient(user.name)}`"></div>
                                <div :class="`user-avatar-glow ${getAvatarGradient(user.name)}`"></div>
                                <div class="user-avatar-shine"></div>
                                <span class="user-avatar-letter">{{ user.name.charAt(0) }}</span>
                                <div class="user-avatar-status"></div>
                            </button>
                        </template>
                        <template #content>
                            <div class="dropdown-container">
                                <div class="dropdown-glass"></div>
                                <div class="dropdown-border-glow"></div>
                                
                                <div class="dropdown-header">
                                    <div class="dropdown-header-bg"></div>
                                    <div class="dropdown-avatar-wrapper">
                                        <div :class="`dropdown-avatar ${getAvatarGradient(user.name)}`">
                                            <span>{{ user.name.charAt(0) }}</span>
                                        </div>
                                        <div class="dropdown-status-dot"></div>
                                    </div>
                                    <div class="dropdown-user-info">
                                        <p class="dropdown-name">{{ user.name }}</p>
                                        <p class="dropdown-email">{{ user.email }}</p>
                                        <span v-if="user.role === 'admin'" class="admin-badge">
                                            <span class="admin-badge-icon">👑</span>
                                            <span>Administrator</span>
                                            <div class="admin-badge-glow"></div>
                                        </span>
                                    </div>
                                </div>

                                <div class="dropdown-section">
                                    <div class="dropdown-section-title">
                                        <span>⚙️</span>
                                        <span>Account</span>
                                    </div>
                                    <DropdownLink :href="route('profile.edit')" class="dropdown-link">
                                        <span class="dropdown-link-icon">🔧</span>
                                        <span>Settings</span>
                                        <div class="dropdown-link-glow"></div>
                                    </DropdownLink>
                                    <DropdownLink :href="route('profile.barangay.show')" class="dropdown-link">
                                        <span class="dropdown-link-icon">👤</span>
                                        <span>Barangay Profile</span>
                                        <div class="dropdown-link-glow"></div>
                                    </DropdownLink>
                                </div>

                                <div class="dropdown-divider">
                                    <div class="dropdown-divider-line"></div>
                                    <div class="dropdown-divider-glow"></div>
                                </div>

                                <div class="dropdown-section">
                                    <div class="dropdown-section-title">
                                        <span>📚</span>
                                        <span>Resources</span>
                                    </div>
                                    <DropdownLink :href="route('proposal')" class="dropdown-link">
                                        <span class="dropdown-link-icon">📖</span>
                                        <span>Barangay Proposal</span>
                                        <div class="dropdown-link-glow"></div>
                                    </DropdownLink>
                                    <DropdownLink :href="route('simulation')" class="dropdown-link">
                                        <span class="dropdown-link-icon">📘</span>
                                        <span>System Handbook</span>
                                        <div class="dropdown-link-glow"></div>
                                    </DropdownLink>
                                    <DropdownLink :href="route('training')" class="dropdown-link">
                                        <span class="dropdown-link-icon">🎓</span>
                                        <span>Training Assessment</span>
                                        <div class="dropdown-link-glow"></div>
                                    </DropdownLink>
                                </div>

                                <div class="dropdown-divider">
                                    <div class="dropdown-divider-line"></div>
                                    <div class="dropdown-divider-glow"></div>
                                </div>

                                <div class="dropdown-footer">
                                    <DropdownLink :href="route('logout')" method="post" as="button" class="dropdown-link dropdown-link-danger">
                                        <span class="dropdown-link-icon">🚪</span>
                                        <span>Log Out</span>
                                        <div class="dropdown-link-glow-danger"></div>
                                    </DropdownLink>
                                </div>
                            </div>
                        </template>
                    </Dropdown>
                </div>
            </div>
        </nav>

        <!-- Premium Mobile Status Bar -->
        <div class="mobile-status-bar">
            <div class="status-bar-gradient"></div>
        </div>

        <!-- Premium Mobile Bottom Navigation -->
        <nav 
            class="mobile-bottom-nav"
            :class="{ 'mobile-nav-hidden': isLoading }"
            role="navigation"
        >
            <div class="mobile-nav-wrapper">
                <!-- Floating Center Button with Premium Effects -->
                <div class="mobile-center-btn-wrapper">
                    <button
                        @click="showingMobileMenu = !showingMobileMenu"
                        class="mobile-center-btn"
                        aria-label="Toggle menu"
                    >
                        <div class="mobile-btn-pulse-1" :class="{'opacity-0': showingMobileMenu}"></div>
                        <div class="mobile-btn-pulse-2" :class="{'opacity-0': showingMobileMenu}"></div>
                        <div class="mobile-btn-shadow"></div>
                        <div class="mobile-btn-container" :class="{'mobile-btn-active': showingMobileMenu}">
                            <div class="mobile-btn-shine"></div>
                            <div class="mobile-btn-gradient"></div>
                            <div class="mobile-btn-border-glow"></div>
                            <span class="mobile-btn-icon" :class="{'mobile-btn-icon-active': showingMobileMenu}">
                                ✨
                            </span>
                        </div>
                        <div class="mobile-btn-ripple"></div>
                    </button>
                </div>

                <!-- Main Navigation Bar with Premium Effects -->
                <div class="mobile-nav-bar">
                    <div class="mobile-nav-glass"></div>
                    <div class="mobile-nav-highlight"></div>
                    <div class="mobile-nav-glow"></div>
                    
                    <!-- Navigation Items -->
                    <div class="mobile-nav-items">
                        <Link 
                            v-for="(item, index) in mobileNavItems" 
                            :key="item.route"
                            :href="route(item.route)"
                            class="mobile-nav-item group"
                            :class="{'mobile-nav-item-active': route().current(item.current)}"
                            :style="`animation-delay: ${index * 50}ms`"
                        >
                            <div class="mobile-nav-item-bg" :class="{
                                'mobile-nav-item-bg-active': route().current(item.current),
                                'mobile-nav-item-bg-danger': item.danger && route().current(item.current)
                            }"></div>
                            
                            <div class="mobile-nav-icon-wrapper">
                                <div v-if="route().current(item.current)" class="mobile-nav-icon-glow" :class="item.danger ? 'mobile-nav-icon-glow-danger' : 'mobile-nav-icon-glow-normal'">
                                    {{ item.icon }}
                                </div>
                                <div v-if="route().current(item.current)" class="mobile-nav-icon-glow-2" :class="item.danger ? 'mobile-nav-icon-glow-danger' : 'mobile-nav-icon-glow-normal'">
                                    {{ item.icon }}
                                </div>
                                <span class="mobile-nav-icon" :class="{'mobile-nav-icon-active': route().current(item.current)}">
                                    {{ item.icon }}
                                </span>
                            </div>
                            
                            <span class="mobile-nav-label" :class="{
                                'mobile-nav-label-active': route().current(item.current),
                                'mobile-nav-label-danger-inactive': item.danger && !route().current(item.current),
                                'mobile-nav-label-danger-active': item.danger && route().current(item.current)
                            }">
                                {{ item.name }}
                            </span>
                            
                            <div class="mobile-nav-dot" :class="{
                                'mobile-nav-dot-active': route().current(item.current),
                                'mobile-nav-dot-danger': item.danger,
                                'mobile-nav-dot-normal': !item.danger
                            }"></div>
                            
                            <div class="mobile-nav-ripple-effect"></div>
                        </Link>
                    </div>
                    
                    <!-- Home Indicator -->
                    <div class="mobile-home-indicator">
                        <div class="mobile-home-bar">
                            <div class="mobile-home-bar-glow"></div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Premium Mobile More Menu -->
        <Transition
            enter-active-class="transition-all duration-500 ease-out"
            enter-from-class="opacity-0 translate-y-12 scale-90"
            enter-to-class="opacity-100 translate-y-0 scale-100"
            leave-active-class="transition-all duration-300 ease-in"
            leave-from-class="opacity-100 translate-y-0 scale-100"
            leave-to-class="opacity-0 translate-y-12 scale-90"
        >
            <div v-show="showingMobileMenu" class="mobile-more-menu">
                <!-- User Profile Card -->
                <div class="mobile-profile-card">
                    <div class="mobile-profile-glass"></div>
                    <div class="mobile-profile-highlight"></div>
                    <div class="mobile-profile-glow"></div>
                    
                    <div class="mobile-profile-content">
                        <!-- Avatar -->
                        <div class="mobile-profile-avatar-wrapper group/avatar">
                            <div :class="`mobile-profile-avatar ${getAvatarGradient(user.name)}`">
                                <span class="mobile-profile-letter">{{ user.name.charAt(0) }}</span>
                            </div>
                            <div :class="`mobile-profile-avatar-glow ${getAvatarGradient(user.name)}`"></div>
                            <div class="mobile-profile-status">
                                <div class="mobile-profile-status-dot"></div>
                                <div class="mobile-profile-status-pulse"></div>
                            </div>
                        </div>
                        
                        <!-- User Info -->
                        <div class="mobile-profile-info">
                            <p class="mobile-profile-name">{{ user.name }}</p>
                            <p class="mobile-profile-email">{{ user.email }}</p>
                            <div class="mobile-profile-badges">
                                <span v-if="user.role === 'admin'" class="mobile-badge mobile-badge-admin">
                                    <span>👑</span>
                                    <span>Admin</span>
                                    <div class="mobile-badge-glow"></div>
                                </span>
                                <span v-else class="mobile-badge mobile-badge-user">
                                    <span>👤</span>
                                    <span>{{ isBusinessOwner ? 'Business' : 'Citizen' }}</span>
                                    <div class="mobile-badge-glow"></div>
                                </span>
                            </div>
                        </div>
                        
                        <NotificationCenter />
                    </div>
                    
                    <!-- Quick Actions -->
                    <div class="mobile-profile-actions">
                        <Link :href="route('profile.edit')" class="mobile-action-btn group/btn">
                            <div class="mobile-action-gradient"></div>
                            <div class="mobile-action-border-glow"></div>
                            <span class="mobile-action-icon">⚙️</span>
                            <span class="mobile-action-text">Settings</span>
                        </Link>
                        <Link :href="route('logout')" method="post" as="button" class="mobile-action-btn mobile-action-btn-danger group/btn">
                            <div class="mobile-action-gradient-danger"></div>
                            <div class="mobile-action-border-glow-danger"></div>
                            <span class="mobile-action-icon">🚪</span>
                            <span class="mobile-action-text">Logout</span>
                        </Link>
                    </div>
                </div>

                <!-- Menu Cards -->
                <div class="mobile-menu-cards">
                    <div class="mobile-menu-glass"></div>
                    <div class="mobile-menu-highlight"></div>
                    <div class="mobile-menu-glow"></div>
                    
                    <div class="mobile-menu-header">
                        <h3 class="mobile-menu-title">
                            <span class="mobile-menu-title-icon">⚡</span>
                            Quick Actions
                        </h3>
                        <div class="mobile-menu-dots">
                            <div class="mobile-menu-dot mobile-menu-dot-1"></div>
                            <div class="mobile-menu-dot mobile-menu-dot-2"></div>
                            <div class="mobile-menu-dot mobile-menu-dot-3"></div>
                        </div>
                    </div>
                    
                    <div class="mobile-menu-items">
                        <template v-if="user.role !== 'admin'">
                            <Link 
                                v-if="isBusinessOwner" 
                                :href="route('business.dashboard')" 
                                class="mobile-menu-item group"
                                :class="{'mobile-menu-item-active': route().current('business.dashboard')}"
                            >
                                <div class="mobile-menu-item-gradient"></div>
                                <div class="mobile-menu-item-border-glow"></div>
                                <span class="mobile-menu-item-icon">📊</span>
                                <div class="mobile-menu-item-content">
                                    <span class="mobile-menu-item-title">My Business</span>
                                    <span class="mobile-menu-item-desc">Dashboard & Analytics</span>
                                </div>
                            </Link>
                            <Link 
                                v-else
                                :href="route('business.register')" 
                                class="mobile-menu-item group"
                            >
                                <div class="mobile-menu-item-gradient-green"></div>
                                <div class="mobile-menu-item-border-glow-green"></div>
                                <span class="mobile-menu-item-icon">🚀</span>
                                <div class="mobile-menu-item-content">
                                    <span class="mobile-menu-item-title">Start Business</span>
                                    <span class="mobile-menu-item-desc">Register Your Business</span>
                                </div>
                            </Link>
                            
                            <Link 
                                :href="route('services.index')" 
                                class="mobile-menu-item group"
                                :class="{'mobile-menu-item-active': route().current('services.*')}"
                            >
                                <div class="mobile-menu-item-gradient"></div>
                                <div class="mobile-menu-item-border-glow"></div>
                                <span class="mobile-menu-item-icon">📄</span>
                                <div class="mobile-menu-item-content">
                                    <span class="mobile-menu-item-title">My Documents</span>
                                    <span class="mobile-menu-item-desc">Track Requests</span>
                                </div>
                            </Link>
                            
                            <Link 
                                :href="route('market.index')" 
                                class="mobile-menu-item group"
                                :class="{'mobile-menu-item-active': route().current('market.*')}"
                            >
                                <div class="mobile-menu-item-gradient-green"></div>
                                <div class="mobile-menu-item-border-glow-green"></div>
                                <span class="mobile-menu-item-icon">🌾</span>
                                <div class="mobile-menu-item-content">
                                    <span class="mobile-menu-item-title">Market Prices</span>
                                    <span class="mobile-menu-item-desc">Live Commodity Rates</span>
                                </div>
                            </Link>
                        </template>
                        
                        <Link :href="route('proposal')" class="mobile-menu-item group">
                            <div class="mobile-menu-item-gradient-amber"></div>
                            <div class="mobile-menu-item-border-glow-amber"></div>
                            <span class="mobile-menu-item-icon">📖</span>
                            <div class="mobile-menu-item-content">
                                <span class="mobile-menu-item-title">Barangay Proposal</span>
                                <span class="mobile-menu-item-desc">View Documents</span>
                            </div>
                        </Link>
                        
                        <Link :href="route('simulation')" class="mobile-menu-item group">
                            <div class="mobile-menu-item-gradient-indigo"></div>
                            <div class="mobile-menu-item-border-glow-indigo"></div>
                            <span class="mobile-menu-item-icon">📚</span>
                            <div class="mobile-menu-item-content">
                                <span class="mobile-menu-item-title">System Handbook</span>
                                <span class="mobile-menu-item-desc">User Guide</span>
                            </div>
                        </Link>
                        
                        <Link :href="route('training')" class="mobile-menu-item group">
                            <div class="mobile-menu-item-gradient-cyan"></div>
                            <div class="mobile-menu-item-border-glow-cyan"></div>
                            <span class="mobile-menu-item-icon">🎓</span>
                            <div class="mobile-menu-item-content">
                                <span class="mobile-menu-item-title">Training Assessment</span>
                                <span class="mobile-menu-item-desc">Test Your Knowledge</span>
                            </div>
                        </Link>
                    </div>
                </div>
            </div>
        </Transition>

        <!-- Premium Backdrop -->
        <Transition
            enter-active-class="transition-opacity duration-400"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-opacity duration-300"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div 
                v-show="showingMobileMenu" 
                @click="showingMobileMenu = false"
                class="mobile-backdrop"
            >
                <div class="backdrop-blur"></div>
                <div class="backdrop-gradient"></div>
            </div>
        </Transition>

        <!-- Main Content -->
        <main 
            class="main-content"
            :class="{ 'main-loading': isLoading }"
        >
            <slot />
        </main>

        <!-- Chat Assistant -->
        <div class="chat-wrapper">
             <ChatAssistant />
        </div>

        <!-- Premium Loading Screen -->
        <Transition name="loading-fade">
            <div v-if="isLoading" class="loading-screen">
                <div class="loading-bg-gradient"></div>
                <div class="loading-container">
                    <div class="loading-glow loading-glow-1"></div>
                    <div class="loading-glow loading-glow-2"></div>
                    <div class="loading-glow loading-glow-3"></div>
                    
                    <div class="loading-logo-wrapper">
                        <div class="loading-logo-ring"></div>
                        <div class="loading-logo-ring-pulse"></div>
                        <img :src="logoUrl" class="loading-logo" alt="Loading" />
                        <div class="loading-logo-shine"></div>
                    </div>

                    <div class="loading-dots">
                        <div v-for="i in 3" :key="i" class="loading-dot-wrapper">
                            <div class="loading-dot" :style="{ animationDelay: `${(i - 1) * 0.15}s` }"></div>
                            <div class="loading-dot-blur" :style="{ animationDelay: `${(i - 1) * 0.15}s` }"></div>
                        </div>
                    </div>
                    
                    <p class="loading-text">Loading your experience...</p>
                </div>
            </div>
        </Transition>
    </div>
</template>

<style scoped>
/* ==================== CSS VARIABLES & BASE ==================== */
:root {
    --fluid-xs: clamp(0.65rem, 0.5rem + 0.75vw, 0.85rem);
    --fluid-sm: clamp(0.75rem, 0.6rem + 0.75vw, 0.95rem);
    --fluid-base: clamp(0.875rem, 0.7rem + 0.875vw, 1.125rem);
    --fluid-lg: clamp(1rem, 0.85rem + 0.75vw, 1.35rem);
    --fluid-xl: clamp(1.25rem, 1rem + 1.25vw, 1.85rem);
    --fluid-2xl: clamp(1.5rem, 1.2rem + 1.5vw, 2.5rem);
    
    --space-xs: clamp(0.25rem, 0.15rem + 0.5vw, 0.5rem);
    --space-sm: clamp(0.5rem, 0.3rem + 1vw, 1rem);
    --space-md: clamp(0.75rem, 0.5rem + 1.25vw, 1.5rem);
    --space-lg: clamp(1rem, 0.7rem + 1.5vw, 2rem);
    --space-xl: clamp(1.5rem, 1rem + 2.5vw, 3rem);
    --space-2xl: clamp(2rem, 1.5rem + 2.5vw, 4rem);
    
    --nav-height: clamp(3.5rem, 3rem + 2.5vw, 5rem);
    --mobile-nav-height: clamp(4rem, 3.5rem + 2.5vw, 5.5rem);
    
    /* Premium Color Palette */
    --premium-blue: rgba(59, 130, 246, 1);
    --premium-purple: rgba(168, 85, 247, 1);
    --premium-pink: rgba(236, 72, 153, 1);
    --premium-cyan: rgba(6, 182, 212, 1);
    --premium-amber: rgba(245, 158, 11, 1);
}

.app-container {
    min-height: 100vh;
    background: #000000;
    color: rgb(241, 245, 249);
    font-family: -apple-system, BlinkMacSystemFont, 'SF Pro Display', 'Segoe UI', sans-serif;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    position: relative;
    overflow-x: hidden;
}

/* ==================== PREMIUM SCROLL PROGRESS ==================== */
.scroll-progress-container {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: rgba(0, 0, 0, 0.3);
    z-index: 201;
    pointer-events: none;
    overflow: hidden;
}

.scroll-progress-glow {
    position: absolute;
    top: 0;
    left: 0;
    height: 100%;
    background: linear-gradient(to right, 
        rgba(59, 130, 246, 0.8), 
        rgba(168, 85, 247, 0.8), 
        rgba(236, 72, 153, 0.8));
    transition: width 0.1s ease-out;
    filter: blur(8px);
}

.scroll-progress-fill {
    position: absolute;
    top: 0;
    left: 0;
    height: 100%;
    background: linear-gradient(to right, 
        rgb(59, 130, 246), 
        rgb(168, 85, 247), 
        rgb(236, 72, 153));
    transition: width 0.1s ease-out;
    box-shadow: 0 0 20px rgba(59, 130, 246, 0.6),
                0 0 40px rgba(168, 85, 247, 0.4);
}

/* ==================== ENHANCED MOBILE PHONE FRAME ==================== */
.mobile-phone-frame {
    position: fixed;
    inset: 0;
    pointer-events: none;
    z-index: 200;
    overflow: hidden;
}

@media (min-width: 1024px) {
    .mobile-phone-frame {
        display: none;
    }
}

.notch-container {
    position: absolute;
    top: 0;
    left: 50%;
    transform: translateX(-50%);
    width: clamp(120px, 30vw, 160px);
    height: clamp(28px, 7vw, 36px);
    border-radius: 0 0 24px 24px;
    background: #000;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.9),
                0 0 60px rgba(0, 0, 0, 0.8) inset;
    overflow: hidden;
}

.notch-gradient {
    position: absolute;
    inset: 0;
    background: linear-gradient(to bottom, rgb(31, 41, 55), #000);
    border-radius: 0 0 24px 24px;
    opacity: 0.95;
}

.notch-glow {
    position: absolute;
    inset: 0;
    background: linear-gradient(to bottom, rgba(59, 130, 246, 0.1), transparent);
    border-radius: 0 0 24px 24px;
    animation: pulse-glow 4s ease-in-out infinite;
}

.notch-speaker {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: clamp(40px, 10vw, 60px);
    height: clamp(4px, 1vw, 6px);
    background: linear-gradient(to right, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.05), rgba(255, 255, 255, 0.1));
    border-radius: 999px;
}

.phone-frame {
    position: absolute;
    inset: 0;
    border-radius: clamp(40px, 10vw, 52px);
    border: 3px solid rgba(255, 255, 255, 0.1);
    box-shadow: 0 0 60px rgba(0, 0, 0, 0.8),
                inset 0 0 40px rgba(0, 0, 0, 0.5);
}

.phone-frame-inner {
    position: absolute;
    inset: 3px;
    border-radius: clamp(37px, 9.5vw, 49px);
    border: 1px solid rgba(255, 255, 255, 0.08);
}

.phone-frame-inner-2 {
    position: absolute;
    inset: 5px;
    border-radius: clamp(35px, 9vw, 47px);
    border: 1px solid rgba(255, 255, 255, 0.04);
}

.phone-frame-shine {
    position: absolute;
    top: 0;
    left: 25%;
    right: 25%;
    height: 2px;
    background: linear-gradient(to right, 
        transparent, 
        rgba(255, 255, 255, 0.15), 
        transparent);
    filter: blur(1px);
}

.phone-frame-edge-glow {
    position: absolute;
    inset: -2px;
    border-radius: clamp(40px, 10vw, 52px);
    background: linear-gradient(135deg, 
        rgba(59, 130, 246, 0.1), 
        rgba(168, 85, 247, 0.1), 
        rgba(236, 72, 153, 0.1));
    filter: blur(10px);
    opacity: 0.5;
    animation: border-glow-pulse 6s ease-in-out infinite;
}

.camera-lens {
    position: absolute;
    top: clamp(48px, 12vw, 56px);
    left: clamp(20px, 5vw, 28px);
    width: clamp(8px, 2vw, 12px);
    height: clamp(8px, 2vw, 12px);
    border-radius: 50%;
    background: linear-gradient(135deg, 
        rgba(37, 99, 235, 0.6), 
        rgba(168, 85, 247, 0.6));
    box-shadow: 0 0 8px rgba(59, 130, 246, 0.6),
                0 2px 4px rgba(0, 0, 0, 0.5),
                inset 0 1px 2px rgba(255, 255, 255, 0.3);
}

.camera-lens-inner {
    position: absolute;
    inset: 2px;
    border-radius: 50%;
    background: radial-gradient(circle at 30% 30%, rgba(147, 197, 253, 0.8), rgba(37, 99, 235, 1));
}

.camera-lens-reflection {
    position: absolute;
    top: 20%;
    left: 20%;
    width: 40%;
    height: 40%;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.6);
    filter: blur(1px);
}

/* ==================== PREMIUM BACKGROUNDS ==================== */
.mobile-content-wrapper,
.desktop-bg-container {
    position: fixed;
    inset: 0;
    overflow: hidden;
    z-index: 1;
}

.mobile-content-wrapper {
    inset: 3px;
    border-radius: clamp(37px, 9.5vw, 49px);
}

@media (min-width: 1024px) {
    .mobile-content-wrapper {
        display: none;
    }
}

@media (max-width: 1023px) {
    .desktop-bg-container {
        display: none;
    }
}

.mobile-bg-base,
.desktop-bg-base {
    position: absolute;
    inset: 0;
    background: radial-gradient(ellipse at top, #0f0f0f 0%, #050505 50%, #000000 100%);
}

.mobile-ambient-container,
.desktop-bg-container > * {
    position: absolute;
    inset: 0;
    pointer-events: none;
}

.mobile-ambient-grain,
.desktop-grain {
    opacity: 0.03;
    mix-blend-mode: overlay;
    background-image: url('data:image/svg+xml,%3Csvg viewBox="0 0 400 400" xmlns="http://www.w3.org/2000/svg"%3E%3Cfilter id="noiseFilter"%3E%3CfeTurbulence type="fractalNoise" baseFrequency="3.5" numOctaves="4" stitchTiles="stitch"/%3E%3C/filter%3E%3Crect width="100%25" height="100%25" filter="url(%23noiseFilter)"/%3E%3C/svg%3E');
}

/* Premium Particle Effects */
.mobile-particle,
.desktop-particle {
    position: absolute;
    width: 2px;
    height: 2px;
    border-radius: 50%;
    background: rgba(59, 130, 246, 0.8);
    box-shadow: 0 0 4px rgba(59, 130, 246, 0.8),
                0 0 8px rgba(59, 130, 246, 0.4);
}

.mobile-particle-1,
.desktop-particle-1 {
    top: 20%;
    left: 30%;
    animation: float-particle-1 20s infinite;
}

.mobile-particle-2,
.desktop-particle-2 {
    top: 60%;
    right: 25%;
    background: rgba(168, 85, 247, 0.8);
    box-shadow: 0 0 4px rgba(168, 85, 247, 0.8),
                0 0 8px rgba(168, 85, 247, 0.4);
    animation: float-particle-2 25s infinite;
}

.mobile-particle-3,
.desktop-particle-3 {
    bottom: 30%;
    left: 20%;
    background: rgba(236, 72, 153, 0.8);
    box-shadow: 0 0 4px rgba(236, 72, 153, 0.8),
                0 0 8px rgba(236, 72, 153, 0.4);
    animation: float-particle-3 30s infinite;
}

/* Premium Glows */
.mobile-glow,
.desktop-glow {
    position: absolute;
    border-radius: 50%;
    animation-timing-function: cubic-bezier(0.4, 0, 0.6, 1);
}

.mobile-glow-1,
.desktop-glow-1 {
    top: 5%;
    left: 30%;
    width: clamp(200px, 60vw, 400px);
    height: clamp(200px, 60vw, 400px);
    background: radial-gradient(circle, 
        rgba(59, 130, 246, 0.15), 
        rgba(147, 51, 234, 0.10), 
        transparent 70%);
    filter: blur(clamp(70px, 22vw, 140px));
    animation: float-glow-1 25s infinite;
}

.mobile-glow-2,
.desktop-glow-2 {
    bottom: 10%;
    right: 25%;
    width: clamp(250px, 70vw, 450px);
    height: clamp(250px, 70vw, 450px);
    background: radial-gradient(circle, 
        rgba(236, 72, 153, 0.12), 
        rgba(251, 146, 60, 0.08), 
        transparent 70%);
    filter: blur(clamp(80px, 25vw, 160px));
    animation: float-glow-2 30s infinite;
}

.mobile-glow-3,
.desktop-glow-3 {
    top: 40%;
    left: 10%;
    width: clamp(220px, 65vw, 380px);
    height: clamp(220px, 65vw, 380px);
    background: radial-gradient(circle, 
        rgba(6, 182, 212, 0.10), 
        rgba(20, 184, 166, 0.07), 
        transparent 70%);
    filter: blur(clamp(60px, 20vw, 130px));
    animation: float-glow-3 35s infinite;
}

.mobile-radial-1,
.desktop-radial-1 {
    background: radial-gradient(ellipse at top left, 
        rgba(59, 130, 246, 0.08) 0%, 
        transparent 50%);
}

.mobile-radial-2,
.desktop-radial-2 {
    background: radial-gradient(ellipse at bottom right, 
        rgba(236, 72, 153, 0.06) 0%, 
        transparent 50%);
}

.mobile-mesh-gradient,
.desktop-mesh-gradient {
    background: 
        linear-gradient(125deg, transparent 0%, rgba(59, 130, 246, 0.03) 40%, transparent 70%),
        linear-gradient(215deg, transparent 30%, rgba(236, 72, 153, 0.03) 60%, transparent 90%);
    animation: mesh-shift 40s ease-in-out infinite;
}

/* ==================== PREMIUM DESKTOP NAVIGATION ==================== */
.desktop-nav {
    position: fixed;
    top: clamp(1rem, 1.5vw, 1.5rem);
    left: 0;
    right: 0;
    z-index: 40;
    display: none;
    justify-content: center;
    padding: 0 var(--space-md);
    transition: all 0.7s cubic-bezier(0.4, 0, 0.2, 1);
}

@media (min-width: 1024px) {
    .desktop-nav {
        display: flex;
    }
}

.desktop-nav.nav-hidden {
    transform: translateY(-8rem);
    opacity: 0;
}

.desktop-nav.nav-scrolled {
    top: clamp(0.5rem, 1vw, 0.75rem);
}

.desktop-nav.nav-scrolling-down {
    transform: translateY(-6rem);
    opacity: 0;
}

.nav-container {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: var(--space-md);
    padding: var(--space-xs) var(--space-sm);
    border-radius: 9999px;
    background: rgba(18, 18, 20, 0.75);
    border: 1px solid rgba(255, 255, 255, 0.12);
    box-shadow: 
        0 20px 60px rgba(0, 0, 0, 0.9),
        0 0 40px rgba(59, 130, 246, 0.05),
        inset 0 1px 1px rgba(255, 255, 255, 0.1);
    transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}

.nav-container::before {
    content: '';
    position: absolute;
    inset: 0;
    border-radius: 9999px;
    padding: 1px;
    background: linear-gradient(135deg, 
        rgba(59, 130, 246, 0.5), 
        rgba(168, 85, 247, 0.3), 
        rgba(236, 72, 153, 0.5));
    -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
    -webkit-mask-composite: xor;
    mask-composite: exclude;
    opacity: 0;
    transition: opacity 0.5s;
}

.nav-container:hover::before {
    opacity: 1;
}

.nav-container:hover {
    transform: scale(1.01) translateY(-1px);
    border-color: rgba(255, 255, 255, 0.15);
    box-shadow: 
        0 24px 72px rgba(0, 0, 0, 0.95),
        0 0 60px rgba(59, 130, 246, 0.1),
        inset 0 1px 1px rgba(255, 255, 255, 0.15);
}

.nav-glass-layer {
    position: absolute;
    inset: 0;
    border-radius: 9999px;
    backdrop-filter: blur(60px) saturate(200%);
    background: linear-gradient(135deg, 
        rgba(255, 255, 255, 0.08), 
        rgba(255, 255, 255, 0.02));
}

.nav-glow {
    position: absolute;
    inset: -3px;
    border-radius: 9999px;
    opacity: 0;
    filter: blur(25px);
    z-index: -10;
    transition: opacity 0.7s;
    background: linear-gradient(to right, 
        rgba(59, 130, 246, 0), 
        rgba(147, 51, 234, 0), 
        rgba(236, 72, 153, 0));
}

.nav-container:hover .nav-glow {
    opacity: 1;
    background: linear-gradient(to right, 
        rgba(59, 130, 246, 0.2), 
        rgba(147, 51, 234, 0.15), 
        rgba(236, 72, 153, 0.2));
}

.nav-glow-hover {
    position: absolute;
    inset: -3px;
    border-radius: 9999px;
    opacity: 0;
    filter: blur(30px);
    z-index: -10;
    transition: opacity 0.7s;
    background: linear-gradient(135deg, 
        rgba(59, 130, 246, 0.2), 
        rgba(168, 85, 247, 0.15), 
        rgba(236, 72, 153, 0.2));
    animation: glow-pulse 4s ease-in-out infinite;
}

.nav-inner-glow {
    position: absolute;
    inset: 1px;
    border-radius: 9999px;
    background: linear-gradient(to bottom, 
        rgba(255, 255, 255, 0.08), 
        rgba(255, 255, 255, 0.02), 
        transparent);
    pointer-events: none;
}

.nav-border-glow {
    position: absolute;
    inset: -1px;
    border-radius: 9999px;
    background: linear-gradient(135deg, 
        rgba(59, 130, 246, 0.3), 
        rgba(168, 85, 247, 0.2), 
        rgba(236, 72, 153, 0.3));
    filter: blur(8px);
    opacity: 0;
    transition: opacity 0.5s;
    z-index: -5;
}

.nav-container:hover .nav-border-glow {
    opacity: 1;
    animation: border-rotate 8s linear infinite;
}

/* Logo Section */
.nav-logo {
    padding-left: var(--space-md);
    padding-right: var(--space-sm);
    display: flex;
    align-items: center;
    gap: var(--space-sm);
    flex-shrink: 0;
    border-radius: 9999px;
    transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.nav-logo:hover {
    transform: scale(1.08);
}

.nav-logo:active {
    transform: scale(0.95);
}

.logo-wrapper {
    position: relative;
}

.logo-glow {
    position: absolute;
    inset: -8px;
    filter: blur(25px);
    opacity: 0;
    transition: opacity 0.5s;
    background: radial-gradient(circle, 
        rgba(59, 130, 246, 0.6), 
        rgba(168, 85, 247, 0.4), 
        transparent);
}

.nav-logo:hover .logo-glow {
    opacity: 0.8;
    animation: logo-pulse 2s ease-in-out infinite;
}

.logo-pulse {
    position: absolute;
    inset: -12px;
    border-radius: 50%;
    background: radial-gradient(circle, 
        rgba(59, 130, 246, 0.4), 
        transparent 70%);
    opacity: 0;
    animation: pulse-ring 3s ease-out infinite;
}

.logo-image {
    height: clamp(3rem, 4vw, 4.5rem);
    width: auto;
    filter: drop-shadow(0 10px 30px rgba(0, 0, 0, 0.8))
            drop-shadow(0 0 20px rgba(59, 130, 246, 0.3));
    position: relative;
    z-index: 10;
    transition: filter 0.3s;
}

.nav-logo:hover .logo-image {
    filter: drop-shadow(0 12px 40px rgba(0, 0, 0, 0.9))
            drop-shadow(0 0 30px rgba(59, 130, 246, 0.5));
}

.logo-shine {
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, 
        transparent 30%, 
        rgba(255, 255, 255, 0.3) 50%, 
        transparent 70%);
    transform: translateX(-100%);
    animation: logo-shine 5s ease-in-out infinite;
}

/* Premium Divider */
.nav-divider {
    position: relative;
    width: 2px;
    height: clamp(1.25rem, 1.75vw, 2rem);
}

.nav-divider-line {
    position: absolute;
    inset: 0;
    background: linear-gradient(to bottom, 
        transparent, 
        rgba(255, 255, 255, 0.2), 
        transparent);
}

.nav-divider-glow {
    position: absolute;
    inset: -4px;
    background: linear-gradient(to bottom, 
        transparent, 
        rgba(96, 165, 250, 0.4), 
        transparent);
    filter: blur(4px);
    animation: divider-pulse 3s ease-in-out infinite;
}

.nav-divider-pulse {
    position: absolute;
    inset: -6px;
    background: linear-gradient(to bottom, 
        transparent, 
        rgba(168, 85, 247, 0.3), 
        transparent);
    filter: blur(8px);
    opacity: 0;
    animation: pulse-soft 4s ease-in-out infinite;
}

/* Navigation Links */
.nav-links {
    display: flex;
    align-items: center;
    gap: var(--space-xs);
}

/* Premium Navigation Pills */
.nav-pill {
    position: relative;
    display: flex;
    align-items: center;
    gap: clamp(0.375rem, 0.75vw, 0.5rem);
    padding: var(--space-sm) var(--space-md);
    border-radius: 9999px;
    font-size: var(--fluid-xs);
    font-weight: 600;
    color: rgba(255, 255, 255, 0.6);
    white-space: nowrap;
    cursor: pointer;
    overflow: hidden;
    transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid transparent;
}

.nav-pill-icon {
    font-size: clamp(1rem, 1.25vw, 1.125rem);
    transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.nav-pill-text {
    position: relative;
    z-index: 1;
}

.nav-pill:hover {
    color: white;
    transform: translateY(-1px);
    border-color: rgba(255, 255, 255, 0.1);
}

.nav-pill:hover .nav-pill-icon {
    transform: scale(1.15) rotate(5deg);
}

.nav-pill:active {
    transform: scale(0.95);
}

.nav-pill::before {
    content: '';
    position: absolute;
    inset: 0;
    background: rgba(255, 255, 255, 0);
    border-radius: 9999px;
    transition: all 0.5s;
}

.nav-pill:hover::before {
    background: rgba(255, 255, 255, 0.12);
}

.nav-pill-glow {
    position: absolute;
    inset: -4px;
    border-radius: 9999px;
    opacity: 0;
    filter: blur(12px);
    transition: all 0.5s;
    background: linear-gradient(135deg, 
        rgba(59, 130, 246, 0), 
        rgba(168, 85, 247, 0), 
        rgba(236, 72, 153, 0));
    z-index: -1;
}

.nav-pill:hover .nav-pill-glow {
    opacity: 1;
    background: linear-gradient(135deg, 
        rgba(59, 130, 246, 0.3), 
        rgba(168, 85, 247, 0.2), 
        rgba(236, 72, 153, 0.3));
}

/* Active States */
.nav-pill.active {
    background: linear-gradient(135deg, 
        rgba(59, 130, 246, 0.2), 
        rgba(168, 85, 247, 0.15), 
        rgba(236, 72, 153, 0.2));
    color: white;
    border-color: rgba(255, 255, 255, 0.15);
    box-shadow: 
        0 4px 20px rgba(59, 130, 246, 0.3),
        inset 0 1px 1px rgba(255, 255, 255, 0.2);
}

.nav-pill.active .nav-pill-glow {
    opacity: 1;
    background: linear-gradient(135deg, 
        rgba(59, 130, 246, 0.4), 
        rgba(168, 85, 247, 0.3), 
        rgba(236, 72, 153, 0.4));
    animation: pill-glow-pulse 3s ease-in-out infinite;
}

/* Profile Pill */
.nav-pill-profile:hover {
    color: rgb(103, 232, 249);
}

.nav-pill-profile.active-profile {
    background: linear-gradient(135deg, 
        rgba(6, 182, 212, 0.25), 
        rgba(59, 130, 246, 0.25));
    color: rgb(165, 243, 252);
    border-color: rgba(6, 182, 212, 0.4);
    box-shadow: 0 4px 20px rgba(6, 182, 212, 0.4);
}

.nav-pill-profile.active-profile .nav-pill-glow {
    background: linear-gradient(135deg, 
        rgba(6, 182, 212, 0.5), 
        rgba(59, 130, 246, 0.4));
}

/* Danger Pill */
.nav-pill-danger:hover {
    color: rgb(252, 165, 165);
}

.nav-pill.active-danger {
    background: linear-gradient(135deg, 
        rgba(239, 68, 68, 0.25), 
        rgba(236, 72, 153, 0.25));
    color: rgb(252, 165, 165);
    border-color: rgba(239, 68, 68, 0.4);
    box-shadow: 0 4px 20px rgba(239, 68, 68, 0.4);
}

.nav-pill.active-danger .nav-pill-glow {
    background: linear-gradient(135deg, 
        rgba(239, 68, 68, 0.5), 
        rgba(236, 72, 153, 0.4));
}

/* Special Pill */
.nav-pill-special.active-special {
    background: linear-gradient(135deg, 
        rgba(59, 130, 246, 0.25), 
        rgba(147, 51, 234, 0.25));
    color: rgb(147, 197, 253);
    border-color: rgba(59, 130, 246, 0.4);
    box-shadow: 0 4px 20px rgba(59, 130, 246, 0.4);
}

/* CTA Pill */
.nav-pill-cta {
    background: linear-gradient(135deg, 
        rgba(34, 197, 94, 0.25), 
        rgba(16, 185, 129, 0.25));
    color: rgb(134, 239, 172);
    border-color: rgba(34, 197, 94, 0.4);
    box-shadow: 0 4px 20px rgba(34, 197, 94, 0.3);
}

.nav-pill-cta:hover {
    background: linear-gradient(135deg, 
        rgba(34, 197, 94, 0.35), 
        rgba(16, 185, 129, 0.35));
    transform: scale(1.05) translateY(-2px);
    box-shadow: 0 6px 30px rgba(34, 197, 94, 0.5);
}

/* User Section */
.nav-user-section {
    display: flex;
    align-items: center;
    gap: var(--space-sm);
    padding-left: var(--space-sm);
    padding-right: var(--space-xs);
}

.nav-user-divider {
    position: relative;
    width: 2px;
    height: clamp(1.25rem, 1.5vw, 1.75rem);
    background: linear-gradient(to bottom, 
        transparent, 
        rgba(255, 255, 255, 0.15), 
        transparent);
}

.nav-user-divider-glow {
    position: absolute;
    inset: -4px;
    background: linear-gradient(to bottom, 
        transparent, 
        rgba(96, 165, 250, 0.3), 
        transparent);
    filter: blur(4px);
    animation: divider-pulse 3s ease-in-out infinite;
}

/* Premium User Avatar Button */
.user-avatar-btn {
    position: relative;
    display: flex;
    height: clamp(2.75rem, 3vw, 3.25rem);
    width: clamp(2.75rem, 3vw, 3.25rem);
    align-items: center;
    justify-content: center;
    border-radius: 9999px;
    transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
    box-shadow: 
        0 4px 20px rgba(0, 0, 0, 0.5),
        0 0 0 2px rgba(255, 255, 255, 0.1);
}

.user-avatar-btn:hover {
    transform: scale(1.1);
    box-shadow: 
        0 8px 30px rgba(0, 0, 0, 0.7),
        0 0 0 2px rgba(255, 255, 255, 0.2),
        0 0 30px rgba(59, 130, 246, 0.3);
}

.user-avatar-btn:active {
    transform: scale(0.95);
}

.user-avatar-ring {
    position: absolute;
    inset: -3px;
    border-radius: 9999px;
    border: 2px solid transparent;
    background: linear-gradient(135deg, 
        rgba(59, 130, 246, 0.5), 
        rgba(168, 85, 247, 0.5), 
        rgba(236, 72, 153, 0.5)) border-box;
    -webkit-mask: linear-gradient(#fff 0 0) padding-box, linear-gradient(#fff 0 0);
    -webkit-mask-composite: xor;
    mask-composite: exclude;
    opacity: 0;
    transition: opacity 0.5s;
}

.user-avatar-btn:hover .user-avatar-ring {
    opacity: 1;
    animation: ring-rotate 8s linear infinite;
}

.user-avatar-ring-pulse {
    position: absolute;
    inset: -6px;
    border-radius: 9999px;
    background: radial-gradient(circle, 
        rgba(59, 130, 246, 0.4), 
        transparent 70%);
    opacity: 0;
}

.user-avatar-btn:hover .user-avatar-ring-pulse {
    animation: pulse-ring 2s ease-out infinite;
}

.user-avatar-gradient {
    position: absolute;
    inset: 0;
    border-radius: 9999px;
    opacity: 0.95;
    transition: all 0.5s;
}

.user-avatar-btn:hover .user-avatar-gradient {
    transform: scale(1.05);
    opacity: 1;
}

.user-avatar-glow {
    position: absolute;
    inset: -6px;
    border-radius: 9999px;
    opacity: 0;
    filter: blur(16px);
    transition: opacity 0.5s;
}

.user-avatar-btn:hover .user-avatar-glow {
    opacity: 0.7;
    animation: avatar-glow-pulse 3s ease-in-out infinite;
}

.user-avatar-shine {
    position: absolute;
    inset: 2px;
    border-radius: 9999px;
    background: linear-gradient(135deg, 
        rgba(255, 255, 255, 0.25) 0%, 
        transparent 50%);
}

.user-avatar-letter {
    position: relative;
    z-index: 10;
    font-size: var(--fluid-base);
    font-weight: 700;
    color: white;
    filter: drop-shadow(0 2px 8px rgba(0, 0, 0, 0.5));
}

.user-avatar-status {
    position: absolute;
    bottom: 0;
    right: 0;
    width: clamp(0.625rem, 0.85vw, 0.875rem);
    height: clamp(0.625rem, 0.85vw, 0.875rem);
    background: rgb(34, 197, 94);
    border-radius: 9999px;
    border: 2px solid rgba(18, 18, 20, 0.9);
    box-shadow: 0 0 8px rgba(34, 197, 94, 0.6);
    animation: status-pulse 2s ease-in-out infinite;
}

/* ==================== PREMIUM DROPDOWN ==================== */
.dropdown-container {
    position: relative;
    padding: var(--space-xs);
    border-radius: clamp(1.25rem, 1.75vw, 1.5rem);
    background: rgba(18, 18, 20, 0.98);
    border: 1px solid rgba(255, 255, 255, 0.15);
    box-shadow: 
        0 20px 60px rgba(0, 0, 0, 0.9),
        0 0 40px rgba(59, 130, 246, 0.1),
        inset 0 1px 1px rgba(255, 255, 255, 0.1);
    max-height: 80vh;
    overflow-y: auto;
    overflow-x: hidden;
}

/* Custom scrollbar for dropdown */
.dropdown-container::-webkit-scrollbar {
    width: 6px;
}

.dropdown-container::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 10px;
    margin: 8px 0;
}

.dropdown-container::-webkit-scrollbar-thumb {
    background: linear-gradient(180deg, 
        rgba(59, 130, 246, 0.5), 
        rgba(168, 85, 247, 0.5));
    border-radius: 10px;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.dropdown-container::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(180deg, 
        rgba(59, 130, 246, 0.7), 
        rgba(168, 85, 247, 0.7));
}

.dropdown-glass {
    position: absolute;
    inset: 0;
    border-radius: clamp(1.25rem, 1.75vw, 1.5rem);
    backdrop-filter: blur(60px) saturate(200%);
    background: linear-gradient(135deg, 
        rgba(255, 255, 255, 0.1), 
        rgba(255, 255, 255, 0.03));
}

.dropdown-border-glow {
    position: absolute;
    inset: -1px;
    border-radius: clamp(1.25rem, 1.75vw, 1.5rem);
    background: linear-gradient(135deg, 
        rgba(59, 130, 246, 0.3), 
        rgba(168, 85, 247, 0.2), 
        rgba(236, 72, 153, 0.3));
    filter: blur(8px);
    z-index: -1;
    animation: border-glow-pulse 6s ease-in-out infinite;
}

/* Dropdown Header */
.dropdown-header {
    position: relative;
    padding: var(--space-md);
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    display: flex;
    align-items: center;
    gap: var(--space-md);
    overflow: visible;
}

.dropdown-header-bg {
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, 
        rgba(59, 130, 246, 0.05), 
        rgba(168, 85, 247, 0.05), 
        rgba(236, 72, 153, 0.05));
    border-radius: clamp(1rem, 1.5vw, 1.25rem) clamp(1rem, 1.5vw, 1.25rem) 0 0;
}

.dropdown-avatar-wrapper {
    position: relative;
    flex-shrink: 0;
}

.dropdown-avatar {
    width: clamp(3rem, 3.5vw, 3.5rem);
    height: clamp(3rem, 3.5vw, 3.5rem);
    border-radius: 9999px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: var(--fluid-lg);
    font-weight: 700;
    color: white;
    box-shadow: 
        0 8px 24px rgba(0, 0, 0, 0.5),
        0 0 0 2px rgba(255, 255, 255, 0.1);
}

.dropdown-status-dot {
    position: absolute;
    bottom: 2px;
    right: 2px;
    width: clamp(0.75rem, 1vw, 0.875rem);
    height: clamp(0.75rem, 1vw, 0.875rem);
    background: rgb(34, 197, 94);
    border-radius: 9999px;
    border: 2px solid rgba(18, 18, 20, 0.9);
    box-shadow: 0 0 10px rgba(34, 197, 94, 0.6);
    animation: status-pulse 2s ease-in-out infinite;
}

.dropdown-user-info {
    flex: 1;
    min-width: 0;
}

.dropdown-name {
    font-size: var(--fluid-sm);
    font-weight: 700;
    color: white;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    margin-bottom: 0.25rem;
}

.dropdown-email {
    font-size: var(--fluid-xs);
    color: rgb(156, 163, 175);
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    margin-bottom: var(--space-sm);
}

.admin-badge {
    position: relative;
    display: inline-flex;
    align-items: center;
    gap: clamp(0.25rem, 0.5vw, 0.375rem);
    padding: clamp(0.25rem, 0.5vw, 0.375rem) clamp(0.5rem, 1vw, 0.75rem);
    font-size: var(--fluid-xs);
    font-weight: 700;
    border-radius: 9999px;
    background: linear-gradient(135deg, 
        rgba(239, 68, 68, 0.25), 
        rgba(236, 72, 153, 0.25));
    color: rgb(252, 165, 165);
    border: 1px solid rgba(239, 68, 68, 0.4);
    box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
    overflow: hidden;
}

.admin-badge-icon {
    font-size: clamp(0.875rem, 1vw, 1rem);
}

.admin-badge-glow {
    position: absolute;
    inset: -4px;
    border-radius: 9999px;
    background: linear-gradient(135deg, 
        rgba(239, 68, 68, 0.4), 
        rgba(236, 72, 153, 0.4));
    filter: blur(8px);
    z-index: -1;
    animation: badge-glow-pulse 3s ease-in-out infinite;
}

/* Dropdown Sections */
.dropdown-section {
    padding: var(--space-sm) 0;
}

.dropdown-section-title {
    display: flex;
    align-items: center;
    gap: var(--space-xs);
    padding: var(--space-xs) var(--space-md);
    font-size: var(--fluid-xs);
    font-weight: 700;
    color: rgba(255, 255, 255, 0.5);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: var(--space-xs);
}

.dropdown-link {
    position: relative;
    display: flex;
    align-items: center;
    gap: var(--space-sm);
    padding: var(--space-sm) var(--space-md);
    border-radius: clamp(0.875rem, 1.25vw, 1.125rem);
    color: rgb(209, 213, 219);
    transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    overflow: hidden;
    margin: 0 var(--space-xs);
}

.dropdown-link-icon {
    font-size: clamp(1rem, 1.25vw, 1.125rem);
    transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.dropdown-link:hover {
    color: white;
    background: rgba(255, 255, 255, 0.10);
    transform: translateX(2px);
}

.dropdown-link:hover .dropdown-link-icon {
    transform: scale(1.2) rotate(5deg);
}

.dropdown-link-glow {
    position: absolute;
    inset: -2px;
    border-radius: clamp(0.875rem, 1.25vw, 1.125rem);
    background: linear-gradient(135deg, 
        rgba(59, 130, 246, 0.2), 
        rgba(168, 85, 247, 0.15), 
        rgba(236, 72, 153, 0.2));
    opacity: 0;
    filter: blur(8px);
    transition: opacity 0.3s;
    z-index: -1;
}

.dropdown-link:hover .dropdown-link-glow {
    opacity: 1;
}

.dropdown-divider {
    position: relative;
    height: 1px;
    margin: var(--space-sm) var(--space-md);
}

.dropdown-divider-line {
    position: absolute;
    inset: 0;
    background: linear-gradient(to right, 
        transparent, 
        rgba(255, 255, 255, 0.1), 
        transparent);
}

.dropdown-divider-glow {
    position: absolute;
    inset: -2px;
    background: linear-gradient(to right, 
        transparent, 
        rgba(96, 165, 250, 0.2), 
        transparent);
    filter: blur(2px);
}

/* Dropdown Footer */
.dropdown-footer {
    padding: var(--space-xs) 0;
}

.dropdown-link-danger {
    color: rgb(248, 113, 113);
}

.dropdown-link-danger:hover {
    background: rgba(239, 68, 68, 0.15);
    color: rgb(252, 165, 165);
}

.dropdown-link-glow-danger {
    position: absolute;
    inset: -2px;
    border-radius: clamp(0.875rem, 1.25vw, 1.125rem);
    background: linear-gradient(135deg, 
        rgba(239, 68, 68, 0.3), 
        rgba(236, 72, 153, 0.3));
    opacity: 0;
    filter: blur(8px);
    transition: opacity 0.3s;
    z-index: -1;
}

.dropdown-link-danger:hover .dropdown-link-glow-danger {
    opacity: 1;
}

/* ==================== PREMIUM MOBILE NAVIGATION ==================== */
.mobile-status-bar {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 40;
    pointer-events: none;
    height: clamp(28px, 7vw, 36px);
    backdrop-filter: blur(12px);
    overflow: hidden;
}

.status-bar-gradient {
    position: absolute;
    inset: 0;
    background: linear-gradient(to bottom, 
        rgba(0, 0, 0, 0.8), 
        rgba(0, 0, 0, 0.4), 
        transparent);
}

@media (min-width: 1024px) {
    .mobile-status-bar {
        display: none;
    }
}

.mobile-bottom-nav {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    z-index: 40;
    transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
}

@media (min-width: 1024px) {
    .mobile-bottom-nav {
        display: none;
    }
}

.mobile-bottom-nav.mobile-nav-hidden {
    transform: translateY(100%);
    opacity: 0;
}

.mobile-nav-wrapper {
    position: relative;
}

/* Premium Floating Center Button */
.mobile-center-btn-wrapper {
    position: absolute;
    top: clamp(-2.25rem, -5.5vw, -2.75rem);
    left: 50%;
    transform: translateX(-50%);
    z-index: 10;
}

.mobile-center-btn {
    position: relative;
}

.mobile-btn-pulse-1,
.mobile-btn-pulse-2 {
    position: absolute;
    inset: 0;
    border-radius: 9999px;
    opacity: 0.25;
    background: linear-gradient(135deg, 
        rgb(168, 85, 247), 
        rgb(236, 72, 153));
}

.mobile-btn-pulse-1 {
    animation: ping-slow 2s cubic-bezier(0, 0, 0.2, 1) infinite;
}

.mobile-btn-pulse-2 {
    animation: ping-slower 2.5s cubic-bezier(0, 0, 0.2, 1) infinite;
}

.mobile-btn-shadow {
    position: absolute;
    inset: 0;
    border-radius: 9999px;
    filter: blur(clamp(1.25rem, 6vw, 2.5rem));
    opacity: 0.5;
    transition: opacity 0.5s;
    background: linear-gradient(135deg, 
        rgb(147, 51, 234), 
        rgb(236, 72, 153));
}

.mobile-center-btn:hover .mobile-btn-shadow {
    opacity: 0.8;
}

.mobile-btn-container {
    position: relative;
    width: clamp(3.75rem, 16vw, 5rem);
    height: clamp(3.75rem, 16vw, 5rem);
    border-radius: 9999px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
    background: linear-gradient(135deg, 
        rgb(147, 51, 234), 
        rgb(236, 72, 153));
    box-shadow: 
        0 10px 40px rgba(217, 70, 239, 0.6),
        0 0 0 2px rgba(255, 255, 255, 0.1);
}

.mobile-btn-container:hover {
    box-shadow: 
        0 15px 50px rgba(217, 70, 239, 0.7),
        0 0 0 2px rgba(255, 255, 255, 0.15);
}

.mobile-center-btn:active .mobile-btn-container {
    transform: scale(0.9);
}

.mobile-btn-active {
    transform: scale(1.12) rotate(180deg);
    box-shadow: 
        0 15px 50px rgba(217, 70, 239, 0.8),
        0 0 0 3px rgba(255, 255, 255, 0.2);
}

.mobile-btn-shine {
    position: absolute;
    inset: 2px;
    border-radius: 9999px;
    background: linear-gradient(to bottom, 
        rgba(255, 255, 255, 0.4), 
        rgba(255, 255, 255, 0.15), 
        transparent);
}

.mobile-btn-gradient {
    position: absolute;
    inset: 0;
    border-radius: 9999px;
    animation: spin-slow 8s linear infinite;
    background: linear-gradient(to right, 
        rgba(255, 255, 255, 0.2), 
        transparent, 
        transparent);
}

.mobile-btn-border-glow {
    position: absolute;
    inset: -2px;
    border-radius: 9999px;
    background: linear-gradient(135deg, 
        rgba(255, 255, 255, 0.4), 
        rgba(168, 85, 247, 0.4), 
        rgba(236, 72, 153, 0.4));
    filter: blur(6px);
    opacity: 0;
    transition: opacity 0.5s;
}

.mobile-btn-container:hover .mobile-btn-border-glow {
    opacity: 1;
}

.mobile-btn-icon {
    position: relative;
    font-size: clamp(1.375rem, 6.5vw, 2rem);
    filter: drop-shadow(0 4px 12px rgba(0, 0, 0, 0.5));
    transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
    z-index: 1;
}

.mobile-btn-icon-active {
    transform: rotate(45deg) scale(1.3);
}

.mobile-btn-ripple {
    position: absolute;
    inset: 0;
    border-radius: 9999px;
    background: rgba(255, 255, 255, 0.3);
    transform: scale(0);
    opacity: 0;
    transition: all 0.5s;
}

.mobile-center-btn:active .mobile-btn-ripple {
    transform: scale(1.5);
    opacity: 1;
}

/* Main Navigation Bar */
.mobile-nav-bar {
    position: relative;
    background: rgba(18, 18, 20, 0.98);
    backdrop-filter: blur(60px) saturate(180%);
    border-top: 1px solid rgba(255, 255, 255, 0.12);
    box-shadow: 
        0 -10px 60px rgba(0, 0, 0, 0.8),
        0 0 40px rgba(59, 130, 246, 0.05);
}

.mobile-nav-glass {
    position: absolute;
    inset: 0;
    background: linear-gradient(to bottom, 
        rgba(255, 255, 255, 0.08), 
        rgba(255, 255, 255, 0.02));
}

.mobile-nav-highlight {
    height: 1px;
    background: linear-gradient(to right, 
        transparent, 
        rgba(255, 255, 255, 0.15), 
        transparent);
}

.mobile-nav-glow {
    height: 1px;
    background: linear-gradient(to right, 
        transparent, 
        rgba(96, 165, 250, 0.3), 
        transparent);
    filter: blur(2px);
}

.mobile-nav-items {
    display: flex;
    align-items: flex-end;
    justify-content: space-around;
    padding: var(--space-sm) var(--space-md) var(--space-xs);
    padding-bottom: calc(var(--space-xs) + env(safe-area-inset-bottom));
}

/* Navigation Items */
.mobile-nav-item {
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: clamp(0.25rem, 1.5vw, 0.5rem);
    padding: clamp(0.5rem, 2.5vw, 0.75rem) clamp(0.5rem, 3vw, 1rem);
    min-width: clamp(60px, 18vw, 80px);
    border-radius: clamp(1rem, 4vw, 1.25rem);
    transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
    animation: scale-in 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
}

.mobile-nav-item:active {
    transform: scale(0.9);
}

.mobile-nav-item-bg {
    position: absolute;
    inset: 0;
    border-radius: clamp(1rem, 4vw, 1.25rem);
    transform: scale(0.9);
    opacity: 0;
    transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
    background: rgba(255, 255, 255, 0.05);
}

.mobile-nav-item-bg-active {
    transform: scale(1);
    opacity: 1;
    background: rgba(255, 255, 255, 0.12);
    box-shadow: 
        0 4px 20px rgba(0, 0, 0, 0.3),
        inset 0 1px 1px rgba(255, 255, 255, 0.2);
}

.mobile-nav-item-bg-danger {
    background: linear-gradient(135deg, 
        rgba(239, 68, 68, 0.25), 
        rgba(236, 72, 153, 0.25)) !important;
    box-shadow: 0 4px 20px rgba(239, 68, 68, 0.4) !important;
}

.mobile-nav-icon-wrapper {
    position: relative;
    margin-bottom: clamp(0.125rem, 0.5vw, 0.25rem);
}

.mobile-nav-icon-glow {
    position: absolute;
    inset: 0;
    filter: blur(clamp(0.75rem, 3vw, 1rem));
    opacity: 0.6;
    transform: scale(1.25);
    transition: all 0.5s;
}

.mobile-nav-icon-glow-normal {
    color: rgb(96, 165, 250);
}

.mobile-nav-icon-glow-danger {
    color: rgb(248, 113, 113);
}

.mobile-nav-icon-glow-2 {
    position: absolute;
    inset: 0;
    filter: blur(clamp(1rem, 4vw, 1.5rem));
    opacity: 0.4;
    transform: scale(1.5);
    transition: all 0.7s;
}

.mobile-nav-icon {
    position: relative;
    display: block;
    font-size: clamp(1.375rem, 7vw, 1.75rem);
    transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
    filter: drop-shadow(0 4px 12px rgba(0, 0, 0, 0.5)) grayscale(1);
    opacity: 0.6;
}

.mobile-nav-icon-active {
    transform: scale(1.15);
    opacity: 1;
    filter: grayscale(0) drop-shadow(0 4px 12px rgba(0, 0, 0, 0.5));
}

.mobile-nav-label {
    position: relative;
    font-size: clamp(0.625rem, 2.5vw, 0.75rem);
    font-weight: 700;
    letter-spacing: 0.025em;
    transition: all 0.5s;
    color: rgb(107, 114, 128);
}

.mobile-nav-label-active {
    color: white;
    transform: scale(1.05);
}

.mobile-nav-label-danger-inactive {
    color: rgba(248, 113, 113, 0.6);
}

.mobile-nav-label-danger-active {
    color: rgb(248, 113, 113);
    transform: scale(1.05);
}

.mobile-nav-dot {
    position: absolute;
    top: clamp(0.25rem, 1vw, 0.5rem);
    width: clamp(0.25rem, 1vw, 0.375rem);
    height: clamp(0.25rem, 1vw, 0.375rem);
    border-radius: 9999px;
    transition: all 0.5s;
    opacity: 0;
    transform: scale(0);
}

.mobile-nav-dot-active {
    opacity: 1;
    transform: scale(1);
}

.mobile-nav-dot-danger {
    background: rgb(248, 113, 113);
    box-shadow: 0 0 8px rgba(248, 113, 113, 0.6);
}

.mobile-nav-dot-normal {
    background: rgb(96, 165, 250);
    box-shadow: 0 0 8px rgba(96, 165, 250, 0.6);
}

.mobile-nav-ripple-effect {
    position: absolute;
    inset: 0;
    background: rgba(255, 255, 255, 0.05);
    border-radius: clamp(1rem, 4vw, 1.25rem);
    transform: scale(0);
    opacity: 0;
    transition: all 0.3s;
}

.mobile-nav-item:hover .mobile-nav-ripple-effect {
    transform: scale(1);
    opacity: 1;
}

/* Home Indicator */
.mobile-home-indicator {
    height: clamp(1rem, 5vw, 1.5rem);
    display: flex;
    align-items: center;
    justify-content: center;
    padding-top: var(--space-xs);
    padding-bottom: var(--space-xs);
    padding-bottom: calc(var(--space-xs) + env(safe-area-inset-bottom));
}

.mobile-home-bar {
    position: relative;
    width: clamp(6rem, 35vw, 9rem);
    height: clamp(0.3rem, 1.25vw, 0.5rem);
    background: rgba(255, 255, 255, 0.3);
    border-radius: 9999px;
    box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.3);
}

.mobile-home-bar-glow {
    position: absolute;
    inset: -2px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 9999px;
    filter: blur(4px);
    animation: pulse-soft 3s ease-in-out infinite;
}

/* ==================== PREMIUM MOBILE MORE MENU ==================== */
.mobile-more-menu {
    position: fixed;
    bottom: clamp(6.5rem, 25vw, 8rem);
    left: var(--space-md);
    right: var(--space-md);
    z-index: 50;
    max-height: calc(100vh - 10rem);
    overflow-y: auto;
}

@media (min-width: 1024px) {
    .mobile-more-menu {
        display: none;
    }
}

/* Custom scrollbar for mobile menu */
.mobile-more-menu::-webkit-scrollbar {
    width: 6px;
}

.mobile-more-menu::-webkit-scrollbar-track {
    background: transparent;
}

.mobile-more-menu::-webkit-scrollbar-thumb {
    background: linear-gradient(180deg, 
        rgba(59, 130, 246, 0.4), 
        rgba(168, 85, 247, 0.4));
    border-radius: 10px;
}

.mobile-profile-card {
    margin-bottom: var(--space-sm);
    border-radius: clamp(1.25rem, 5vw, 1.75rem);
    padding: var(--space-md);
    position: relative;
    overflow: hidden;
    background: rgba(18, 18, 20, 0.98);
    border: 1px solid rgba(255, 255, 255, 0.15);
    box-shadow: 
        0 20px 60px rgba(0, 0, 0, 0.9),
        0 0 40px rgba(59, 130, 246, 0.1);
}

.mobile-profile-glass {
    position: absolute;
    inset: 0;
    backdrop-filter: blur(60px) saturate(180%);
    background: linear-gradient(135deg, 
        rgba(255, 255, 255, 0.1), 
        rgba(255, 255, 255, 0.03));
    border-radius: clamp(1.25rem, 5vw, 1.75rem);
}

.mobile-profile-highlight {
    position: absolute;
    inset: 1px;
    background: linear-gradient(to bottom, 
        rgba(255, 255, 255, 0.1), 
        rgba(255, 255, 255, 0.02), 
        transparent);
    border-radius: clamp(1.25rem, 5vw, 1.75rem);
    pointer-events: none;
}

.mobile-profile-glow {
    position: absolute;
    inset: -3px;
    border-radius: clamp(1.25rem, 5vw, 1.75rem);
    filter: blur(20px);
    z-index: -10;
    background: linear-gradient(135deg, 
        rgba(59, 130, 246, 0.15), 
        rgba(147, 51, 234, 0.1), 
        rgba(236, 72, 153, 0.15));
    animation: profile-glow-pulse 6s ease-in-out infinite;
}

.mobile-profile-content {
    display: flex;
    align-items: center;
    gap: var(--space-md);
    margin-bottom: var(--space-sm);
    position: relative;
    z-index: 1;
}

.mobile-profile-avatar-wrapper {
    position: relative;
    flex-shrink: 0;
}

.mobile-profile-avatar {
    width: clamp(3.25rem, 14vw, 4.5rem);
    height: clamp(3.25rem, 14vw, 4.5rem);
    border-radius: 9999px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 
        0 8px 24px rgba(0, 0, 0, 0.5),
        0 0 0 2px rgba(255, 255, 255, 0.15);
    transition: transform 0.5s;
}

.mobile-profile-avatar-wrapper:hover .mobile-profile-avatar {
    transform: scale(1.1);
}

.mobile-profile-letter {
    font-size: clamp(1.25rem, 5.5vw, 1.75rem);
    font-weight: 700;
    color: white;
}

.mobile-profile-avatar-glow {
    position: absolute;
    inset: -6px;
    border-radius: 9999px;
    opacity: 0;
    filter: blur(16px);
    transition: opacity 0.5s;
}

.mobile-profile-avatar-wrapper:hover .mobile-profile-avatar-glow {
    opacity: 0.7;
}

.mobile-profile-status {
    position: absolute;
    bottom: clamp(-0.25rem, -1vw, -0.5rem);
    right: clamp(-0.25rem, -1vw, -0.5rem);
    width: clamp(1rem, 4.5vw, 1.5rem);
    height: clamp(1rem, 4.5vw, 1.5rem);
    background: rgb(34, 197, 94);
    border-radius: 9999px;
    border: 2px solid rgba(18, 18, 20, 0.9);
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 8px rgba(34, 197, 94, 0.6);
}

.mobile-profile-status-dot {
    width: clamp(0.375rem, 1.5vw, 0.5rem);
    height: clamp(0.375rem, 1.5vw, 0.5rem);
    background: white;
    border-radius: 9999px;
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

.mobile-profile-status-pulse {
    position: absolute;
    inset: -2px;
    border-radius: 9999px;
    background: rgba(34, 197, 94, 0.4);
    animation: pulse-ring 2s ease-out infinite;
}

.mobile-profile-info {
    flex: 1;
    min-width: 0;
}

.mobile-profile-name {
    font-size: var(--fluid-sm);
    font-weight: 700;
    color: white;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.mobile-profile-email {
    font-size: var(--fluid-xs);
    color: rgb(156, 163, 175);
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    margin-bottom: var(--space-xs);
}

.mobile-profile-badges {
    display: flex;
    gap: var(--space-sm);
}

.mobile-badge {
    position: relative;
    display: inline-flex;
    align-items: center;
    gap: clamp(0.25rem, 1vw, 0.375rem);
    padding: clamp(0.125rem, 0.5vw, 0.25rem) clamp(0.375rem, 1.5vw, 0.5rem);
    font-size: clamp(0.625rem, 2.5vw, 0.75rem);
    font-weight: 700;
    border-radius: 9999px;
    border: 1px solid;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    overflow: hidden;
}

.mobile-badge-admin {
    background: linear-gradient(135deg, 
        rgba(239, 68, 68, 0.25), 
        rgba(236, 72, 153, 0.25));
    color: rgb(252, 165, 165);
    border-color: rgba(239, 68, 68, 0.4);
}

.mobile-badge-user {
    background: linear-gradient(135deg, 
        rgba(59, 130, 246, 0.25), 
        rgba(6, 182, 212, 0.25));
    color: rgb(147, 197, 253);
    border-color: rgba(59, 130, 246, 0.4);
}

.mobile-badge-glow {
    position: absolute;
    inset: -4px;
    border-radius: 9999px;
    filter: blur(8px);
    z-index: -1;
    opacity: 0.5;
}

.mobile-badge-admin .mobile-badge-glow {
    background: linear-gradient(135deg, 
        rgba(239, 68, 68, 0.4), 
        rgba(236, 72, 153, 0.4));
}

.mobile-badge-user .mobile-badge-glow {
    background: linear-gradient(135deg, 
        rgba(59, 130, 246, 0.4), 
        rgba(6, 182, 212, 0.4));
}

.mobile-profile-actions {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: var(--space-sm);
    padding-top: var(--space-sm);
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    position: relative;
    z-index: 1;
}

.mobile-action-btn {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: var(--space-sm);
    padding: clamp(0.5rem, 2.5vw, 0.75rem) clamp(0.625rem, 3vw, 1rem);
    border-radius: clamp(0.75rem, 3vw, 1rem);
    transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    font-size: var(--fluid-xs);
    font-weight: 600;
    overflow: hidden;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.mobile-action-btn:active {
    transform: scale(0.95);
}

.mobile-action-btn:hover {
    background: rgba(255, 255, 255, 0.12);
    transform: translateY(-2px);
}

.mobile-action-gradient {
    position: absolute;
    inset: 0;
    transition: all 0.5s;
    background: linear-gradient(135deg, 
        rgba(59, 130, 246, 0), 
        rgba(147, 51, 234, 0));
}

.mobile-action-btn:hover .mobile-action-gradient {
    background: linear-gradient(135deg, 
        rgba(59, 130, 246, 0.15), 
        rgba(147, 51, 234, 0.15));
}

.mobile-action-border-glow {
    position: absolute;
    inset: -2px;
    border-radius: clamp(0.75rem, 3vw, 1rem);
    background: linear-gradient(135deg, 
        rgba(59, 130, 246, 0.3), 
        rgba(147, 51, 234, 0.3));
    opacity: 0;
    filter: blur(6px);
    transition: opacity 0.3s;
    z-index: -1;
}

.mobile-action-btn:hover .mobile-action-border-glow {
    opacity: 1;
}

.mobile-action-btn-danger {
    background: rgba(239, 68, 68, 0.12);
}

.mobile-action-btn-danger:hover {
    background: rgba(239, 68, 68, 0.2);
}

.mobile-action-gradient-danger {
    position: absolute;
    inset: 0;
    transition: all 0.5s;
    background: linear-gradient(135deg, 
        rgba(239, 68, 68, 0), 
        rgba(236, 72, 153, 0));
}

.mobile-action-btn-danger:hover .mobile-action-gradient-danger {
    background: linear-gradient(135deg, 
        rgba(239, 68, 68, 0.2), 
        rgba(236, 72, 153, 0.2));
}

.mobile-action-border-glow-danger {
    position: absolute;
    inset: -2px;
    border-radius: clamp(0.75rem, 3vw, 1rem);
    background: linear-gradient(135deg, 
        rgba(239, 68, 68, 0.4), 
        rgba(236, 72, 153, 0.4));
    opacity: 0;
    filter: blur(6px);
    transition: opacity 0.3s;
    z-index: -1;
}

.mobile-action-btn-danger:hover .mobile-action-border-glow-danger {
    opacity: 1;
}

.mobile-action-icon {
    position: relative;
    z-index: 10;
    font-size: clamp(1rem, 4vw, 1.25rem);
}

.mobile-action-text {
    position: relative;
    z-index: 10;
    color: rgb(209, 213, 219);
}

.mobile-action-btn:hover .mobile-action-text {
    color: white;
}

/* Menu Cards */
.mobile-menu-cards {
    border-radius: clamp(1.25rem, 5vw, 1.75rem);
    padding: var(--space-md);
    position: relative;
    overflow: hidden;
    background: rgba(18, 18, 20, 0.98);
    border: 1px solid rgba(255, 255, 255, 0.15);
    box-shadow: 
        0 20px 60px rgba(0, 0, 0, 0.9),
        0 0 40px rgba(59, 130, 246, 0.1);
}

.mobile-menu-glass {
    position: absolute;
    inset: 0;
    backdrop-filter: blur(60px) saturate(180%);
    background: linear-gradient(135deg, 
        rgba(255, 255, 255, 0.1), 
        rgba(255, 255, 255, 0.03));
    border-radius: clamp(1.25rem, 5vw, 1.75rem);
}

.mobile-menu-highlight {
    position: absolute;
    inset: 1px;
    background: linear-gradient(to bottom, 
        rgba(255, 255, 255, 0.1), 
        rgba(255, 255, 255, 0.02), 
        transparent);
    border-radius: clamp(1.25rem, 5vw, 1.75rem);
    pointer-events: none;
}

.mobile-menu-glow {
    position: absolute;
    inset: -3px;
    border-radius: clamp(1.25rem, 5vw, 1.75rem);
    filter: blur(20px);
    z-index: -10;
    background: linear-gradient(135deg, 
        rgba(147, 51, 234, 0.15), 
        rgba(236, 72, 153, 0.1), 
        rgba(236, 72, 153, 0.15));
    animation: menu-glow-pulse 6s ease-in-out infinite;
}

.mobile-menu-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: var(--space-sm);
    padding: 0 var(--space-sm);
    position: relative;
    z-index: 1;
}

.mobile-menu-title {
    display: flex;
    align-items: center;
    gap: var(--space-xs);
    font-size: var(--fluid-sm);
    font-weight: 700;
    color: rgba(255, 255, 255, 0.95);
    letter-spacing: 0.025em;
}

.mobile-menu-title-icon {
    font-size: clamp(1rem, 4vw, 1.25rem);
}

.mobile-menu-dots {
    display: flex;
    gap: clamp(0.25rem, 1.5vw, 0.375rem);
}

.mobile-menu-dot {
    width: clamp(0.25rem, 1.5vw, 0.375rem);
    height: clamp(0.25rem, 1.5vw, 0.375rem);
    border-radius: 9999px;
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

.mobile-menu-dot-1 {
    background: linear-gradient(135deg, 
        rgb(147, 51, 234), 
        rgb(236, 72, 153));
    box-shadow: 0 0 8px rgba(147, 51, 234, 0.6);
}

.mobile-menu-dot-2 {
    background: linear-gradient(135deg, 
        rgb(59, 130, 246), 
        rgb(6, 182, 212));
    box-shadow: 0 0 8px rgba(59, 130, 246, 0.6);
    animation-delay: 0.2s;
}

.mobile-menu-dot-3 {
    background: linear-gradient(135deg, 
        rgb(34, 197, 94), 
        rgb(16, 185, 129));
    box-shadow: 0 0 8px rgba(34, 197, 94, 0.6);
    animation-delay: 0.4s;
}

.mobile-menu-items {
    display: flex;
    flex-direction: column;
    gap: var(--space-sm);
    position: relative;
    z-index: 1;
}

.mobile-menu-item {
    position: relative;
    display: flex;
    align-items: center;
    gap: var(--space-sm);
    padding: clamp(0.625rem, 3vw, 1rem) clamp(0.75rem, 3.5vw, 1.25rem);
    border-radius: clamp(1rem, 4vw, 1.25rem);
    transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
    cursor: pointer;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
    overflow: hidden;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.mobile-menu-item:active {
    transform: scale(0.97);
}

.mobile-menu-item:hover {
    background: rgba(255, 255, 255, 0.12);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.5);
    transform: translateY(-2px);
}

.mobile-menu-item-active {
    background: linear-gradient(135deg, 
        rgba(59, 130, 246, 0.2), 
        rgba(147, 51, 234, 0.2));
    border-color: rgba(59, 130, 246, 0.4);
}

.mobile-menu-item-gradient,
.mobile-menu-item-gradient-green,
.mobile-menu-item-gradient-amber,
.mobile-menu-item-gradient-indigo,
.mobile-menu-item-gradient-cyan {
    position: absolute;
    inset: 0;
    transition: all 0.5s;
    opacity: 0;
}

.mobile-menu-item-gradient {
    background: linear-gradient(135deg, 
        rgba(59, 130, 246, 0), 
        rgba(147, 51, 234, 0));
}

.mobile-menu-item:hover .mobile-menu-item-gradient {
    background: linear-gradient(135deg, 
        rgba(59, 130, 246, 0.2), 
        rgba(147, 51, 234, 0.2));
    opacity: 1;
}

.mobile-menu-item-gradient-green {
    background: linear-gradient(135deg, 
        rgba(34, 197, 94, 0), 
        rgba(16, 185, 129, 0));
}

.mobile-menu-item:hover .mobile-menu-item-gradient-green {
    background: linear-gradient(135deg, 
        rgba(34, 197, 94, 0.2), 
        rgba(16, 185, 129, 0.2));
    opacity: 1;
}

.mobile-menu-item-gradient-amber {
    background: linear-gradient(135deg, 
        rgba(245, 158, 11, 0), 
        rgba(251, 146, 60, 0));
}

.mobile-menu-item:hover .mobile-menu-item-gradient-amber {
    background: linear-gradient(135deg, 
        rgba(245, 158, 11, 0.2), 
        rgba(251, 146, 60, 0.2));
    opacity: 1;
}

.mobile-menu-item-gradient-indigo {
    background: linear-gradient(135deg, 
        rgba(99, 102, 241, 0), 
        rgba(139, 92, 246, 0));
}

.mobile-menu-item:hover .mobile-menu-item-gradient-indigo {
    background: linear-gradient(135deg, 
        rgba(99, 102, 241, 0.2), 
        rgba(139, 92, 246, 0.2));
    opacity: 1;
}

.mobile-menu-item-gradient-cyan {
    background: linear-gradient(135deg, 
        rgba(6, 182, 212, 0), 
        rgba(59, 130, 246, 0));
}

.mobile-menu-item:hover .mobile-menu-item-gradient-cyan {
    background: linear-gradient(135deg, 
        rgba(6, 182, 212, 0.2), 
        rgba(59, 130, 246, 0.2));
    opacity: 1;
}

/* Border glows for menu items */
.mobile-menu-item-border-glow,
.mobile-menu-item-border-glow-green,
.mobile-menu-item-border-glow-amber,
.mobile-menu-item-border-glow-indigo,
.mobile-menu-item-border-glow-cyan {
    position: absolute;
    inset: -2px;
    border-radius: clamp(1rem, 4vw, 1.25rem);
    filter: blur(6px);
    opacity: 0;
    transition: opacity 0.3s;
    z-index: -1;
}

.mobile-menu-item-border-glow {
    background: linear-gradient(135deg, 
        rgba(59, 130, 246, 0.3), 
        rgba(147, 51, 234, 0.3));
}

.mobile-menu-item:hover .mobile-menu-item-border-glow {
    opacity: 1;
}

.mobile-menu-item-border-glow-green {
    background: linear-gradient(135deg, 
        rgba(34, 197, 94, 0.3), 
        rgba(16, 185, 129, 0.3));
}

.mobile-menu-item:hover .mobile-menu-item-border-glow-green {
    opacity: 1;
}

.mobile-menu-item-border-glow-amber {
    background: linear-gradient(135deg, 
        rgba(245, 158, 11, 0.3), 
        rgba(251, 146, 60, 0.3));
}

.mobile-menu-item:hover .mobile-menu-item-border-glow-amber {
    opacity: 1;
}

.mobile-menu-item-border-glow-indigo {
    background: linear-gradient(135deg, 
        rgba(99, 102, 241, 0.3), 
        rgba(139, 92, 246, 0.3));
}

.mobile-menu-item:hover .mobile-menu-item-border-glow-indigo {
    opacity: 1;
}

.mobile-menu-item-border-glow-cyan {
    background: linear-gradient(135deg, 
        rgba(6, 182, 212, 0.3), 
        rgba(59, 130, 246, 0.3));
}

.mobile-menu-item:hover .mobile-menu-item-border-glow-cyan {
    opacity: 1;
}

.mobile-menu-item-icon {
    font-size: clamp(1.25rem, 6vw, 1.75rem);
    filter: drop-shadow(0 4px 12px rgba(0, 0, 0, 0.5));
    position: relative;
    z-index: 10;
    transition: transform 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.mobile-menu-item:hover .mobile-menu-item-icon {
    transform: scale(1.25) rotate(12deg);
}

.mobile-menu-item-content {
    flex: 1;
    min-width: 0;
    text-align: left;
}

.mobile-menu-item-title {
    font-size: var(--fluid-sm);
    font-weight: 700;
    position: relative;
    z-index: 10;
    display: block;
    color: rgb(209, 213, 219);
    transition: color 0.3s;
}

.mobile-menu-item:hover .mobile-menu-item-title {
    color: white;
}

.mobile-menu-item-desc {
    font-size: var(--fluid-xs);
    color: rgb(156, 163, 175);
    position: relative;
    z-index: 10;
    display: block;
}

/* Backdrop */
.mobile-backdrop {
    position: fixed;
    inset: 0;
    z-index: 40;
    overflow: hidden;
}

@media (min-width: 1024px) {
    .mobile-backdrop {
        display: none;
    }
}

.backdrop-blur {
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, 0.85);
    backdrop-filter: blur(20px);
}

.backdrop-gradient {
    position: absolute;
    inset: 0;
    background: radial-gradient(ellipse at center, 
        rgba(59, 130, 246, 0.1), 
        rgba(0, 0, 0, 0.5));
    animation: backdrop-pulse 8s ease-in-out infinite;
}

/* ==================== MAIN CONTENT ==================== */
.main-content {
    position: relative;
    z-index: 10;
    padding-top: clamp(2.5rem, 5vw, 5rem);
    padding-bottom: clamp(6rem, 15vw, 8rem);
    padding-left: var(--space-md);
    padding-right: var(--space-md);
    transition: all 0.7s cubic-bezier(0.4, 0, 0.2, 1);
}

@media (min-width: 1024px) {
    .main-content {
        padding-top: clamp(6rem, 10vw, 8rem);
        padding-bottom: clamp(3rem, 5vw, 5rem);
    }
}

.main-loading {
    transform: scale(0.98);
    opacity: 0.3;
    filter: blur(4px);
    pointer-events: none;
}

/* Chat Wrapper */
.chat-wrapper {
    position: relative;
    z-index: 50;
}

/* ==================== PREMIUM LOADING SCREEN ==================== */
.loading-screen {
    position: fixed;
    inset: 0;
    z-index: 100;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: rgba(0, 0, 0, 0.99);
    backdrop-filter: blur(40px);
}

.loading-bg-gradient {
    position: absolute;
    inset: 0;
    background: radial-gradient(ellipse at center, 
        rgba(59, 130, 246, 0.1), 
        rgba(0, 0, 0, 0));
    animation: loading-gradient-pulse 4s ease-in-out infinite;
}

.loading-container {
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.loading-glow {
    position: absolute;
    border-radius: 50%;
    animation: pulse-smooth 4s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

.loading-glow-1 {
    width: clamp(12rem, 35vw, 16rem);
    height: clamp(12rem, 35vw, 16rem);
    background: radial-gradient(circle, 
        rgba(59, 130, 246, 0.3), 
        rgba(147, 51, 234, 0.2), 
        rgba(236, 72, 153, 0.15), 
        transparent);
}

.loading-glow-2 {
    width: clamp(10rem, 30vw, 14rem);
    height: clamp(10rem, 30vw, 14rem);
    background: radial-gradient(circle, 
        rgba(6, 182, 212, 0.25), 
        rgba(59, 130, 246, 0.15), 
        rgba(147, 51, 234, 0.1), 
        transparent);
    animation-delay: 0.5s;
}

.loading-glow-3 {
    width: clamp(8rem, 25vw, 12rem);
    height: clamp(8rem, 25vw, 12rem);
    background: radial-gradient(circle, 
        rgba(236, 72, 153, 0.2), 
        rgba(168, 85, 247, 0.15), 
        transparent);
    animation-delay: 1s;
}

.loading-logo-wrapper {
    position: relative;
    margin-bottom: clamp(2rem, 5vw, 3rem);
}

.loading-logo-ring {
    position: absolute;
    inset: -12px;
    border-radius: 50%;
    border: 2px solid transparent;
    background: linear-gradient(135deg, 
        rgba(59, 130, 246, 0.6), 
        rgba(168, 85, 247, 0.6), 
        rgba(236, 72, 153, 0.6)) border-box;
    -webkit-mask: linear-gradient(#fff 0 0) padding-box, linear-gradient(#fff 0 0);
    -webkit-mask-composite: xor;
    mask-composite: exclude;
    animation: ring-rotate 8s linear infinite;
}

.loading-logo-ring-pulse {
    position: absolute;
    inset: -16px;
    border-radius: 50%;
    background: radial-gradient(circle, 
        rgba(59, 130, 246, 0.4), 
        transparent 70%);
    animation: pulse-ring 2s ease-out infinite;
}

.loading-logo {
    height: clamp(5rem, 16vw, 8rem);
    width: auto;
    position: relative;
    animation: float-smooth 3s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    filter: drop-shadow(0 20px 40px rgba(59, 130, 246, 0.5))
            drop-shadow(0 0 60px rgba(168, 85, 247, 0.3));
    z-index: 1;
}

.loading-logo-shine {
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, 
        transparent 30%, 
        rgba(255, 255, 255, 0.4) 50%, 
        transparent 70%);
    transform: translateX(-100%);
    animation: logo-shine 3s ease-in-out infinite;
}

.loading-dots {
    margin-top: clamp(2rem, 5vw, 3rem);
    display: flex;
    gap: clamp(0.5rem, 1.5vw, 0.75rem);
}

.loading-dot-wrapper {
    position: relative;
}

.loading-dot {
    width: clamp(0.75rem, 2.5vw, 1rem);
    height: clamp(0.75rem, 2.5vw, 1rem);
    border-radius: 9999px;
    background: linear-gradient(to bottom right, 
        rgb(96, 165, 250), 
        rgb(59, 130, 246));
    animation: dot-bounce 1.6s cubic-bezier(0.34, 1.56, 0.64, 1) infinite;
    box-shadow: 0 0 20px rgba(59, 130, 246, 0.6);
}

.loading-dot-blur {
    position: absolute;
    inset: 0;
    width: clamp(0.75rem, 2.5vw, 1rem);
    height: clamp(0.75rem, 2.5vw, 1rem);
    background: rgb(96, 165, 250);
    border-radius: 9999px;
    filter: blur(10px);
    opacity: 0.5;
    animation: dot-bounce 1.6s cubic-bezier(0.34, 1.56, 0.64, 1) infinite;
}

.loading-text {
    margin-top: clamp(1.5rem, 4vw, 2rem);
    font-size: var(--fluid-sm);
    font-weight: 500;
    color: rgba(255, 255, 255, 0.7);
    letter-spacing: 0.05em;
    animation: fade-in-out 2s ease-in-out infinite;
}

/* Loading Fade Transition */
.loading-fade-enter-active,
.loading-fade-leave-active {
    transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}

.loading-fade-enter-from {
    opacity: 0;
    transform: scale(1.05);
}

.loading-fade-enter-to {
    opacity: 1;
    transform: scale(1);
}

.loading-fade-leave-from {
    opacity: 1;
    transform: scale(1);
}

.loading-fade-leave-to {
    opacity: 0;
    transform: scale(0.95);
}

/* ==================== ANIMATIONS ==================== */
@keyframes float-smooth {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-15px); }
}

@keyframes float-glow-1 {
    0%, 100% { transform: translate(0, 0) scale(1); }
    33% { transform: translate(-40px, 35px) scale(1.1); }
    66% { transform: translate(30px, -30px) scale(0.9); }
}

@keyframes float-glow-2 {
    0%, 100% { transform: translate(0, 0) scale(1); }
    33% { transform: translate(35px, -40px) scale(1.08); }
    66% { transform: translate(-30px, 30px) scale(0.92); }
}

@keyframes float-glow-3 {
    0%, 100% { transform: translate(0, 0) scale(1); }
    50% { transform: translate(-25px, 25px) scale(1.05); }
}

@keyframes float-particle-1 {
    0%, 100% { transform: translate(0, 0); opacity: 0.8; }
    25% { transform: translate(100px, -50px); opacity: 1; }
    50% { transform: translate(50px, 100px); opacity: 0.6; }
    75% { transform: translate(-80px, 20px); opacity: 1; }
}

@keyframes float-particle-2 {
    0%, 100% { transform: translate(0, 0); opacity: 0.7; }
    33% { transform: translate(-90px, 70px); opacity: 1; }
    66% { transform: translate(60px, -80px); opacity: 0.5; }
}

@keyframes float-particle-3 {
    0%, 100% { transform: translate(0, 0); opacity: 0.9; }
    40% { transform: translate(70px, 90px); opacity: 0.6; }
    80% { transform: translate(-100px, -60px); opacity: 1; }
}

@keyframes mesh-shift {
    0%, 100% { transform: translate(0, 0); }
    50% { transform: translate(20px, -20px); }
}

@keyframes ping-slow {
    75%, 100% {
        transform: scale(2);
        opacity: 0;
    }
}

@keyframes ping-slower {
    75%, 100% {
        transform: scale(2.2);
        opacity: 0;
    }
}

@keyframes spin-slow {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

@keyframes pulse-smooth {
    0%, 100% { 
        opacity: 0.15; 
        transform: scale(1);
    }
    50% { 
        opacity: 0.3; 
        transform: scale(1.08);
    }
}

@keyframes pulse-glow {
    0%, 100% { opacity: 0.3; }
    50% { opacity: 0.6; }
}

@keyframes pulse-soft {
    0%, 100% { opacity: 0.5; }
    50% { opacity: 1; }
}

@keyframes border-glow-pulse {
    0%, 100% { opacity: 0.5; }
    50% { opacity: 1; }
}

@keyframes border-rotate {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

@keyframes logo-pulse {
    0%, 100% { opacity: 0.6; transform: scale(1); }
    50% { opacity: 1; transform: scale(1.1); }
}

@keyframes logo-shine {
    0% { transform: translateX(-100%); }
    50%, 100% { transform: translateX(200%); }
}

@keyframes divider-pulse {
    0%, 100% { opacity: 0.5; }
    50% { opacity: 1; }
}

@keyframes pill-glow-pulse {
    0%, 100% { opacity: 0.8; }
    50% { opacity: 1; }
}

@keyframes ring-rotate {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

@keyframes pulse-ring {
    0% { opacity: 0.7; transform: scale(1); }
    50% { opacity: 0; transform: scale(1.5); }
    100% { opacity: 0; transform: scale(1.5); }
}

@keyframes avatar-glow-pulse {
    0%, 100% { opacity: 0.5; }
    50% { opacity: 1; }
}

@keyframes status-pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.6; }
}

@keyframes badge-glow-pulse {
    0%, 100% { opacity: 0.4; }
    50% { opacity: 0.8; }
}

@keyframes glow-pulse {
    0%, 100% { opacity: 0; }
    50% { opacity: 1; }
}

@keyframes profile-glow-pulse {
    0%, 100% { opacity: 0.8; }
    50% { opacity: 1; }
}

@keyframes menu-glow-pulse {
    0%, 100% { opacity: 0.7; }
    50% { opacity: 1; }
}

@keyframes backdrop-pulse {
    0%, 100% { opacity: 0.8; }
    50% { opacity: 1; }
}

@keyframes loading-gradient-pulse {
    0%, 100% { opacity: 0.5; }
    50% { opacity: 1; }
}

@keyframes dot-bounce {
    0%, 60%, 100% { 
        transform: translateY(0) scale(1); 
        opacity: 0.5; 
    }
    30% { 
        transform: translateY(-20px) scale(1.3); 
        opacity: 1; 
    }
}

@keyframes fade-in-out {
    0%, 100% { opacity: 0.5; }
    50% { opacity: 1; }
}

@keyframes scale-in {
    from {
        opacity: 0;
        transform: scale(0.9);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
}

/* ==================== SCROLLBAR STYLING ==================== */
::-webkit-scrollbar {
    width: clamp(8px, 1.5vw, 12px);
    height: clamp(8px, 1.5vw, 12px);
}

::-webkit-scrollbar-track {
    background: transparent;
}

::-webkit-scrollbar-thumb {
    background: linear-gradient(180deg, 
        rgba(59, 130, 246, 0.4), 
        rgba(168, 85, 247, 0.4));
    border-radius: 10px;
    border: 2px solid transparent;
    background-clip: padding-box;
    backdrop-filter: blur(10px);
}

::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(180deg, 
        rgba(59, 130, 246, 0.6), 
        rgba(168, 85, 247, 0.6));
    background-clip: padding-box;
}

/* Firefox Scrollbar */
* {
    scrollbar-width: thin;
    scrollbar-color: rgba(59, 130, 246, 0.4) transparent;
}

/* ==================== ACCESSIBILITY & PERFORMANCE ==================== */
@media (prefers-reduced-motion: reduce) {
    *,
    *::before,
    *::after {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
}

/* Touch Device Optimizations */
@media (hover: none) and (pointer: coarse) {
    button:active,
    a:active {
        transform: scale(0.95);
    }
    
    .nav-pill:active {
        transform: scale(0.92);
    }
    
    .mobile-nav-item:active {
        transform: scale(0.9);
    }
    
    .mobile-menu-item:active {
        transform: scale(0.97);
    }
}

/* Font Rendering */
* {
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    text-rendering: optimizeLegibility;
}

/* Focus Styles */
*:focus {
    outline: none;
}

*:focus-visible {
    outline: 2px solid rgb(59, 130, 246);
    outline-offset: 2px;
    border-radius: 0.5rem;
}

/* ==================== RESPONSIVE BREAKPOINTS ==================== */

/* Extra Small Devices (320px - 479px) */
@media (max-width: 479px) {
    :root {
        --space-xs: 0.25rem;
        --space-sm: 0.5rem;
        --space-md: 0.75rem;
        --space-lg: 1rem;
        --space-xl: 1.5rem;
    }
}

/* Landscape Mobile (Small Height) */
@media (max-height: 500px) and (orientation: landscape) {
    .main-content {
        padding-top: 1rem;
        padding-bottom: 4rem;
    }
    
    .mobile-more-menu {
        bottom: 5rem;
        max-height: calc(100vh - 7rem);
    }
    
    .loading-logo {
        height: 3rem;
    }
    
    .loading-dots {
        margin-top: 1rem;
    }
}

/* Print Styles */
@media print {
    .desktop-nav,
    .mobile-bottom-nav,
    .mobile-status-bar,
    .scroll-progress-container,
    .chat-wrapper,
    .loading-screen,
    .mobile-backdrop {
        display: none !important;
    }
    
    .main-content {
        padding: 0;
    }
    
    .app-container {
        background: white;
        color: black;
    }
}

/* High Contrast Mode */
@media (prefers-contrast: high) {
    .nav-container,
    .mobile-nav-bar,
    .dropdown-container,
    .mobile-profile-card,
    .mobile-menu-cards {
        border-width: 2px;
        border-color: white;
    }
    
    .nav-pill,
    .mobile-nav-item,
    .mobile-menu-item {
        border: 1px solid rgba(255, 255, 255, 0.5);
    }
}
</style>