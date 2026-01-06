<script setup>
import { router, usePage } from '@inertiajs/vue3';
import { computed, onMounted, onUnmounted, ref } from 'vue';

const page = usePage();
const user = computed(() => page.props.auth.user);

// Notification state
const notifications = ref([]);
const unreadCount = ref(0);
const isOpen = ref(false);
const showToast = ref(false);
const currentToast = ref(null);
const isConnected = ref(false);
const loadingError = ref(false);
const isLoading = ref(false);

// Store channel references
let userChannel = null;
let broadcastChannel = null;
let pollInterval = null;
let connectionCheckTimeout = null;
let isSetupComplete = ref(false);

onMounted(() => {
    loadNotifications();
    setTimeout(() => {
        if (!isSetupComplete.value) {
            setupRealTimeListener();
        }
    }, 500);
});

onUnmounted(() => {
    cleanupListeners();
});

// Helper to get CSRF token
const getCsrfToken = () => {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
};

// ✅ FIXED: Better fetch with proper headers
const loadNotifications = async () => {
    isLoading.value = true;
    loadingError.value = false;
    
    try {
        const response = await fetch('/api/notifications', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin'
        });

        if (!response.ok) {
            const errorText = await response.text();
            console.error('❌ Response error:', response.status, errorText);
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }

        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            console.error('❌ Invalid content type:', contentType);
            throw new Error('Server returned non-JSON response');
        }

        const data = await response.json();
        notifications.value = data.notifications || [];
        unreadCount.value = data.unread_count || 0;
        
        console.log('✅ Notifications loaded:', notifications.value.length);
        
    } catch (error) {
        console.error('❌ Failed to load notifications:', error.message);
        loadingError.value = true;
    } finally {
        isLoading.value = false;
    }
};

// Setup real-time listener
const setupRealTimeListener = () => {
    if (isSetupComplete.value) {
        console.log('⚠️ Setup already complete');
        return;
    }

    if (typeof window.Echo === 'undefined' || !window.Echo) {
        console.log('⚠️ Echo not available - using polling');
        setupFastPolling();
        isSetupComplete.value = true;
        return;
    }
    
    console.log('✅ Setting up real-time notifications for user:', user.value.id);
    
    try {
        cleanupEchoChannels();

        const userId = user.value.id;
        const channelName = `App.Models.User.${userId}`;
        
        console.log('📡 Subscribing to private channel:', channelName);
        
        userChannel = window.Echo.private(channelName)
            .notification((notification) => {
                console.log('🔔 Notification received:', notification);
                handleNewNotification(notification);
                isConnected.value = true;
            })
            .error((error) => {
                console.error('❌ User channel error:', error);
                if (!pollInterval) {
                    setupFastPolling();
                }
            });

        console.log('📡 Subscribing to public broadcast channel');
        
        broadcastChannel = window.Echo.channel('notifications')
            .listen('.notification.sent', (e) => {
                console.log('📢 Broadcast notification:', e);
                if (e.userId === user.value.id || e.data?.broadcast === 'all') {
                    handleNewNotification(e);
                }
                isConnected.value = true;
            })
            .error((error) => {
                console.error('❌ Broadcast error:', error);
            });
        
        connectionCheckTimeout = setTimeout(() => {
            if (!isConnected.value) {
                console.warn('⚠️ Connection timeout, starting polling');
                setupFastPolling();
            }
        }, 3000);
        
        isSetupComplete.value = true;
        console.log('✅ Listeners ready');
    } catch (error) {
        console.error('❌ Setup error:', error);
        setupFastPolling();
        isSetupComplete.value = true;
    }
};

// Polling fallback
const setupFastPolling = () => {
    if (pollInterval) return;
    console.log('⏱️ Starting polling (10s interval)');
    pollInterval = setInterval(loadNotifications, 10000);
};

// Cleanup
const cleanupEchoChannels = () => {
    if (!window.Echo) return;

    try {
        if (userChannel) {
            const channelName = `private-App.Models.User.${user.value.id}`;
            console.log('🧹 Leaving channel:', channelName);
            window.Echo.leave(channelName);
            userChannel = null;
        }

        if (broadcastChannel) {
            console.log('🧹 Leaving broadcast channel');
            window.Echo.leave('notifications');
            broadcastChannel = null;
        }
    } catch (error) {
        console.warn('Cleanup error:', error);
    }
};

const cleanupListeners = () => {
    if (connectionCheckTimeout) {
        clearTimeout(connectionCheckTimeout);
        connectionCheckTimeout = null;
    }

    if (pollInterval) {
        clearInterval(pollInterval);
        pollInterval = null;
    }
    
    cleanupEchoChannels();
    isConnected.value = false;
    isSetupComplete.value = false;
};

// Handle new notification
const handleNewNotification = (notification) => {
    const exists = notifications.value.some(n => n.id === notification.id);
    if (exists) return;
    
    notifications.value.unshift(notification);
    unreadCount.value++;
    
    currentToast.value = notification;
    showToast.value = true;
    
    playNotificationSound();
    
    if ('vibrate' in navigator) {
        navigator.vibrate([100, 50, 100]);
    }
    
    setTimeout(() => {
        showToast.value = false;
    }, 5000);
};

// Play sound
const playNotificationSound = () => {
    // Notification sound disabled - file not found
    // To enable: Add notification.mp3 to public/sounds/ directory
    return;
};

// Toggle panel
const togglePanel = () => {
    isOpen.value = !isOpen.value;
    if (isOpen.value && !loadingError.value) {
        loadNotifications();
    }
};

// ✅ FIXED: Mark as read
const markAsRead = async (notificationId) => {
    try {
        const response = await fetch(`/api/notifications/${notificationId}/read`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': getCsrfToken()
            },
            credentials: 'same-origin'
        });

        if (!response.ok) {
            const errorText = await response.text();
            console.error('❌ Mark as read error:', errorText);
            throw new Error('Failed to mark as read');
        }
        
        const notification = notifications.value.find(n => n.id === notificationId);
        if (notification && !notification.read_at) {
            notification.read_at = new Date().toISOString();
            unreadCount.value = Math.max(0, unreadCount.value - 1);
        }
    } catch (error) {
        console.error('Failed to mark as read:', error);
    }
};

// ✅ FIXED: Mark all as read
const markAllAsRead = async () => {
    try {
        const response = await fetch('/api/notifications/mark-all-read', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': getCsrfToken()
            },
            credentials: 'same-origin'
        });

        if (!response.ok) {
            const errorText = await response.text();
            console.error('❌ Mark all as read error:', errorText);
            throw new Error('Failed to mark all as read');
        }
        
        notifications.value.forEach(n => n.read_at = new Date().toISOString());
        unreadCount.value = 0;
    } catch (error) {
        console.error('Failed to mark all as read:', error);
    }
};

// ✅ FIXED: Handle click with route check
const handleNotificationClick = (notification) => {
    markAsRead(notification.id);
    
    if (notification.data?.url) {
        router.visit(notification.data.url);
        isOpen.value = false;
    }
};

// ✅ FIXED: View all with error handling
const viewAllNotifications = () => {
    try {
        router.visit('/notifications');
        isOpen.value = false;
    } catch (error) {
        console.error('Navigation error:', error);
        // Fallback to window.location if router fails
        window.location.href = '/notifications';
    }
};

// Get icon
const getNotificationIcon = (type) => {
    const icons = {
        'message': '💬',
        'document': '📄',
        'approval': '✅',
        'rejection': '❌',
        'update': '🔔',
        'emergency': '🚨',
        'payment': '💰',
        'alert': '⚠️',
        'success': '✨',
        'info': 'ℹ️'
    };
    return icons[type] || '🔔';
};

// Time ago
const timeAgo = (date) => {
    const seconds = Math.floor((new Date() - new Date(date)) / 1000);
    
    const intervals = {
        year: 31536000,
        month: 2592000,
        week: 604800,
        day: 86400,
        hour: 3600,
        minute: 60
    };
    
    for (const [unit, secondsInUnit] of Object.entries(intervals)) {
        const interval = Math.floor(seconds / secondsInUnit);
        if (interval >= 1) {
            return `${interval} ${unit}${interval === 1 ? '' : 's'} ago`;
        }
    }
    
    return 'Just now';
};

const closeToast = () => {
    showToast.value = false;
};
</script>

<template>
    <div class="notification-center">
        <!-- Connection indicator -->
        <div v-if="loadingError" class="connection-indicator connection-error" title="Failed to load"></div>
        <div v-else-if="isConnected" class="connection-indicator connection-success" title="Real-time connected"></div>
        
        <!-- Bell Button -->
        <button
            @click="togglePanel"
            class="bell-button"
            :class="{ 'bell-active': isOpen }"
            aria-label="Notifications"
            :aria-expanded="isOpen"
        >
            <svg class="bell-icon" :class="{ 'bell-ring': unreadCount > 0 }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
            
            <span v-if="unreadCount > 0" class="unread-badge">
                {{ unreadCount > 99 ? '99+' : unreadCount }}
            </span>
        </button>

        <!-- Notification Panel -->
        <Transition name="panel-slide">
            <div
                v-show="isOpen"
                class="notification-panel"
                role="dialog"
                aria-label="Notifications panel"
            >
                <!-- Header -->
                <div class="panel-header">
                    <div class="panel-header-content">
                        <h3 class="panel-title">Notifications</h3>
                        <span v-if="unreadCount > 0" class="panel-unread-badge">
                            {{ unreadCount }} new
                        </span>
                    </div>
                    <button
                        v-if="unreadCount > 0"
                        @click="markAllAsRead"
                        class="mark-all-btn"
                    >
                        Mark all read
                    </button>
                </div>

                <!-- Loading State -->
                <div v-if="isLoading" class="loading-state">
                    <div class="loading-spinner"></div>
                    <p class="loading-text">Loading notifications...</p>
                </div>

                <!-- Error State -->
                <div v-else-if="loadingError" class="error-state">
                    <div class="error-icon">⚠️</div>
                    <p class="error-title">Failed to load notifications</p>
                    <p class="error-subtitle">Please check your connection</p>
                    <button @click="loadNotifications" class="retry-btn">
                        Retry
                    </button>
                </div>

                <!-- Notifications List -->
                <div v-else class="notifications-list">
                    <!-- Empty State -->
                    <div v-if="notifications.length === 0" class="empty-state">
                        <div class="empty-icon">🔔</div>
                        <p class="empty-title">No notifications yet</p>
                        <p class="empty-subtitle">We'll notify you when something arrives</p>
                    </div>

                    <!-- Notification Items -->
                    <button
                        v-for="notification in notifications"
                        :key="notification.id"
                        @click="handleNotificationClick(notification)"
                        class="notification-item"
                        :class="{ 'notification-unread': !notification.read_at }"
                    >
                        <!-- Icon -->
                        <div class="notification-icon-wrapper">
                            <div class="notification-icon">
                                {{ getNotificationIcon(notification.data?.type || 'info') }}
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="notification-content">
                            <div class="notification-header">
                                <h4 class="notification-title">
                                    {{ notification.data?.title || 'Notification' }}
                                </h4>
                                <span v-if="!notification.read_at" class="notification-unread-dot"></span>
                            </div>
                            <p class="notification-message">
                                {{ notification.data?.message || notification.data?.body }}
                            </p>
                            <span class="notification-time">
                                {{ timeAgo(notification.created_at) }}
                            </span>
                        </div>
                    </button>
                </div>

                <!-- ✅ FIXED: Footer with proper error handling -->
                <div v-if="!loadingError && !isLoading" class="panel-footer">
                    <button
                        @click="viewAllNotifications"
                        class="view-all-btn"
                    >
                        View all notifications →
                    </button>
                </div>
            </div>
        </Transition>

        <!-- Toast Notification -->
        <Transition name="toast-slide">
            <div
                v-if="showToast && currentToast"
                class="toast-notification"
                role="alert"
                aria-live="assertive"
            >
                <div class="toast-content">
                    <!-- Icon -->
                    <div class="toast-icon-wrapper">
                        <div class="toast-icon">
                            {{ getNotificationIcon(currentToast.data?.type || 'info') }}
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="toast-text">
                        <h4 class="toast-title">
                            {{ currentToast.data?.title || 'New Notification' }}
                        </h4>
                        <p class="toast-message">
                            {{ currentToast.data?.message || currentToast.data?.body }}
                        </p>
                    </div>

                    <!-- Close Button -->
                    <button @click="closeToast" class="toast-close" aria-label="Close">
                        <svg class="toast-close-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Progress bar -->
                <div class="toast-progress">
                    <div class="toast-progress-bar"></div>
                </div>
            </div>
        </Transition>

        <!-- Backdrop -->
        <Transition name="backdrop-fade">
            <div
                v-if="isOpen"
                @click="isOpen = false"
                class="notification-backdrop"
                aria-hidden="true"
            ></div>
        </Transition>
    </div>
</template>

<style scoped>
.notification-center { @apply relative; }
.connection-indicator { @apply absolute -top-1 -left-1 w-2 h-2 rounded-full; }
.connection-error { @apply bg-red-500 animate-pulse; }
.connection-success { @apply bg-green-500; }
.bell-button { @apply relative p-2 rounded-full transition-all duration-300 active:scale-90; background: rgba(31, 41, 55, 0.5); border: 1px solid rgba(255, 255, 255, 0.1); @apply focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500; }
.bell-button:hover { background: rgba(55, 65, 81, 0.5); }
.bell-active { background: rgba(55, 65, 81, 0.7); @apply border-white/20; }
.bell-icon { @apply w-5 h-5 sm:w-6 sm:h-6 text-gray-300 transition-all duration-300; }
.bell-button:hover .bell-icon { @apply text-white scale-110; }
.bell-ring { animation: bell-ring 1s ease-in-out infinite; }
@keyframes bell-ring { 0%, 100% { transform: rotate(0deg); } 10%, 30% { transform: rotate(-10deg); } 20%, 40% { transform: rotate(10deg); } }
.unread-badge { @apply absolute -top-1 -right-1 flex items-center justify-center min-w-[20px] h-5 px-1.5 rounded-full text-[10px] font-bold text-white shadow-lg animate-pulse; background: linear-gradient(135deg, rgba(239, 68, 68, 1), rgba(236, 72, 153, 1)); }
.notification-panel { @apply absolute right-0 top-full mt-3 w-[90vw] sm:w-96 max-h-[80vh] rounded-2xl shadow-2xl overflow-hidden z-50; background: rgba(26, 26, 26, 0.98); backdrop-filter: blur(40px) saturate(180%); border: 1px solid rgba(255, 255, 255, 0.10); }
.panel-header { @apply flex items-center justify-between px-4 py-3 border-b border-white/10; background: linear-gradient(to right, rgba(59, 130, 246, 0.1), rgba(147, 51, 234, 0.1)); }
.panel-header-content { @apply flex items-center gap-2; }
.panel-title { @apply text-base font-bold text-white; }
.panel-unread-badge { @apply px-2 py-0.5 rounded-full text-xs font-bold; background: rgba(59, 130, 246, 0.2); @apply text-blue-300; }
.mark-all-btn { @apply text-xs font-medium transition-colors; @apply text-blue-400 hover:text-blue-300; }
.loading-state { @apply flex flex-col items-center justify-center py-12 px-4; }
.loading-spinner { @apply w-10 h-10 rounded-full border-4 border-blue-500/20 border-t-blue-500 animate-spin mb-3; }
.loading-text { @apply text-sm text-gray-400 font-medium; }
.error-state { @apply flex flex-col items-center justify-center py-12 px-4; }
.error-icon { @apply text-4xl mb-2; }
.error-title { @apply text-sm text-red-400 font-medium mb-2; }
.error-subtitle { @apply text-xs text-gray-500 mb-3; }
.retry-btn { @apply px-4 py-2 rounded-lg text-xs font-medium transition-colors; background: rgba(59, 130, 246, 0.2); @apply text-blue-300; }
.retry-btn:hover { background: rgba(59, 130, 246, 0.3); }
.notifications-list { @apply overflow-y-auto max-h-[60vh]; }
.empty-state { @apply flex flex-col items-center justify-center py-12 px-4; }
.empty-icon { @apply w-16 h-16 rounded-full flex items-center justify-center text-3xl mb-3; background: rgba(31, 41, 55, 0.5); }
.empty-title { @apply text-gray-400 text-sm font-medium; }
.empty-subtitle { @apply text-gray-500 text-xs mt-1; }
.notification-item { @apply w-full text-left px-4 py-3 transition-colors border-b border-white/5 flex gap-3; @apply focus:outline-none focus-visible:bg-white/5; }
.notification-item:hover { background: rgba(255, 255, 255, 0.05); }
.notification-unread { background: rgba(59, 130, 246, 0.05); }
.notification-icon-wrapper { @apply flex-shrink-0; }
.notification-icon { @apply w-10 h-10 rounded-xl flex items-center justify-center text-xl border border-white/10; background: linear-gradient(135deg, rgba(59, 130, 246, 0.2), rgba(147, 51, 234, 0.2)); }
.notification-content { @apply flex-1 min-w-0; }
.notification-header { @apply flex items-start justify-between gap-2 mb-1; }
.notification-title { @apply text-sm font-semibold text-white truncate; }
.notification-unread-dot { @apply flex-shrink-0 w-2 h-2 bg-blue-500 rounded-full; }
.notification-message { @apply text-xs text-gray-400 mb-1; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.notification-time { @apply text-[10px] text-gray-500 font-medium; }
.panel-footer { @apply px-4 py-3 border-t border-white/10; background: rgba(0, 0, 0, 0.2); }
.view-all-btn { @apply w-full text-center text-sm font-medium transition-colors; @apply text-blue-400 hover:text-blue-300; }
.toast-notification { @apply fixed top-20 right-4 z-[60] w-[90vw] sm:w-96 rounded-2xl shadow-2xl overflow-hidden; background: linear-gradient(135deg, rgba(59, 130, 246, 1), rgba(147, 51, 234, 1)); border: 1px solid rgba(255, 255, 255, 0.2); }
.toast-content { @apply p-4 flex items-start gap-3; }
.toast-icon-wrapper { @apply flex-shrink-0; }
.toast-icon { @apply w-12 h-12 rounded-xl flex items-center justify-center text-2xl; background: rgba(255, 255, 255, 0.2); }
.toast-text { @apply flex-1 min-w-0; }
.toast-title { @apply text-sm font-bold text-white mb-1; }
.toast-message { @apply text-xs text-white/90; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.toast-close { @apply flex-shrink-0 p-1 rounded-lg transition-colors active:scale-90; }
.toast-close:hover { background: rgba(255, 255, 255, 0.2); }
.toast-close-icon { @apply w-5 h-5 text-white; }
.toast-progress { @apply h-1; background: rgba(255, 255, 255, 0.2); }
.toast-progress-bar { @apply h-full bg-white; animation: progress-shrink 5s linear forwards; }
@keyframes progress-shrink { from { width: 100%; } to { width: 0%; } }
.notification-backdrop { @apply lg:hidden fixed inset-0 z-40; background: rgba(0, 0, 0, 0.6); backdrop-filter: blur(8px); }
.panel-slide-enter-active { transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1); }
.panel-slide-leave-active { transition: all 0.2s cubic-bezier(0.4, 0, 1, 1); }
.panel-slide-enter-from { opacity: 0; transform: translateY(-10px) scale(0.95); }
.panel-slide-leave-to { opacity: 0; transform: translateY(-10px) scale(0.95); }
.toast-slide-enter-active { transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1); }
.toast-slide-leave-active { transition: all 0.2s cubic-bezier(0.4, 0, 1, 1); }
.toast-slide-enter-from { opacity: 0; transform: translateX(100%); }
.toast-slide-leave-to { opacity: 0; transform: translateX(100%); }
.backdrop-fade-enter-active, .backdrop-fade-leave-active { transition: opacity 0.3s ease; }
.backdrop-fade-enter-from, .backdrop-fade-leave-to { opacity: 0; }
.notifications-list::-webkit-scrollbar { width: 6px; }
.notifications-list::-webkit-scrollbar-track { background: rgba(0, 0, 0, 0.2); }
.notifications-list::-webkit-scrollbar-thumb { background: rgba(59, 130, 246, 0.4); border-radius: 10px; }
.notifications-list::-webkit-scrollbar-thumb:hover { background: rgba(59, 130, 246, 0.6); }
@media (prefers-reduced-motion: reduce) { *, *::before, *::after { animation-duration: 0.01ms !important; transition-duration: 0.01ms !important; } }
</style>