<script setup>
import { router, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import { computed, onMounted, onUnmounted, ref } from 'vue';

const page = usePage();
const user = computed(() => page.props.auth.user);

// Notification state
const notifications  = ref([]);
const unreadCount    = ref(0);
const isOpen         = ref(false);
const showToast      = ref(false);
const currentToast   = ref(null);
const isConnected    = ref(false);
const loadingError   = ref(false);
const isLoading      = ref(false);

let userChannel          = null;
let broadcastChannel     = null;
let pollInterval         = null;
let connectionCheckTimeout = null;
let isSetupComplete      = ref(false);

onMounted(() => {
    loadNotifications();
    setTimeout(() => {
        if (!isSetupComplete.value) setupRealTimeListener();
    }, 500);
});

onUnmounted(() => {
    cleanupListeners();
});

// ── Data loading — uses axios so the CSRF 419 interceptor applies ─────────────

const loadNotifications = async () => {
    isLoading.value    = true;
    loadingError.value = false;

    try {
        const { data } = await axios.get('/api/notifications');
        notifications.value = data.notifications  ?? [];
        unreadCount.value   = data.unread_count   ?? 0;
        console.log('✅ Notifications loaded:', notifications.value.length);
    } catch (error) {
        // 419s are retried automatically by the axios interceptor in bootstrap.js.
        // Only flag a visible error for non-recoverable failures.
        if (error.response?.status !== 419) {
            console.error('❌ Failed to load notifications:', error.message);
            loadingError.value = true;
        }
    } finally {
        isLoading.value = false;
    }
};

// ── Mark as read — uses axios ─────────────────────────────────────────────────

const markAsRead = async (notificationId) => {
    try {
        await axios.post(`/api/notifications/${notificationId}/read`);
        const n = notifications.value.find(n => n.id === notificationId);
        if (n && !n.read_at) {
            n.read_at     = new Date().toISOString();
            unreadCount.value = Math.max(0, unreadCount.value - 1);
        }
    } catch (error) {
        if (error.response?.status !== 419) {
            console.error('❌ Mark as read error:', error.message);
        }
    }
};

const markAllAsRead = async () => {
    try {
        await axios.post('/api/notifications/mark-all-read');
        notifications.value.forEach(n => { n.read_at = new Date().toISOString(); });
        unreadCount.value = 0;
    } catch (error) {
        if (error.response?.status !== 419) {
            console.error('❌ Mark all as read error:', error.message);
        }
    }
};

// ── Real-time / polling ───────────────────────────────────────────────────────

const setupRealTimeListener = () => {
    if (isSetupComplete.value) return;

    if (typeof window.Echo === 'undefined' || !window.Echo) {
        // Echo not configured — fall back to silent polling (no console noise)
        setupPolling();
        isSetupComplete.value = true;
        return;
    }

    try {
        cleanupEchoChannels();

        const channelName = `App.Models.User.${user.value.id}`;

        userChannel = window.Echo.private(channelName)
            .notification((notification) => {
                handleNewNotification(notification);
                isConnected.value = true;
            })
            .error(() => {
                if (!pollInterval) setupPolling();
            });

        broadcastChannel = window.Echo.channel('notifications')
            .listen('.notification.sent', (e) => {
                if (e.userId === user.value.id || e.data?.broadcast === 'all') {
                    handleNewNotification(e);
                }
                isConnected.value = true;
            });

        // If no event arrives within 5s, assume Echo isn't working and poll instead
        connectionCheckTimeout = setTimeout(() => {
            if (!isConnected.value) setupPolling();
        }, 5000);

        isSetupComplete.value = true;
    } catch (error) {
        console.error('❌ Echo setup error:', error);
        setupPolling();
        isSetupComplete.value = true;
    }
};

const setupPolling = () => {
    if (pollInterval) return;
    // Poll every 30s (was 10s — reduce server load since we already load on mount)
    pollInterval = setInterval(loadNotifications, 30000);
};

const cleanupEchoChannels = () => {
    if (!window.Echo) return;
    try {
        if (userChannel)      { window.Echo.leave(`private-App.Models.User.${user.value.id}`); userChannel = null; }
        if (broadcastChannel) { window.Echo.leave('notifications'); broadcastChannel = null; }
    } catch { /* ignore cleanup errors */ }
};

const cleanupListeners = () => {
    clearTimeout(connectionCheckTimeout);
    clearInterval(pollInterval);
    connectionCheckTimeout = null;
    pollInterval           = null;
    cleanupEchoChannels();
    isConnected.value      = false;
    isSetupComplete.value  = false;
};

// ── New notification handler ──────────────────────────────────────────────────

const handleNewNotification = (notification) => {
    if (notifications.value.some(n => n.id === notification.id)) return;

    notifications.value.unshift(notification);
    unreadCount.value++;

    currentToast.value = notification;
    showToast.value    = true;

    if ('vibrate' in navigator) navigator.vibrate([100, 50, 100]);

    setTimeout(() => { showToast.value = false; }, 5000);
};

// ── Panel toggle ──────────────────────────────────────────────────────────────

const togglePanel = () => {
    isOpen.value = !isOpen.value;
    if (isOpen.value && !loadingError.value) loadNotifications();
};

// ── Notification navigation ───────────────────────────────────────────────────

const handleNotificationClick = (notification) => {
    markAsRead(notification.id);
    if (notification.data?.url) router.visit(notification.data.url);
    isOpen.value = false;
};

// ── Helpers ───────────────────────────────────────────────────────────────────

const getStatusColor = (status) => ({
    pending:          'text-amber-400   bg-amber-500/10   border-amber-500/20',
    processing:       'text-blue-400    bg-blue-500/10    border-blue-500/20',
    ready_for_pickup: 'text-emerald-400 bg-emerald-500/10 border-emerald-500/20',
    completed:        'text-slate-400   bg-slate-500/10   border-slate-500/20',
    rejected:         'text-red-400     bg-red-500/10     border-red-500/20',
}[status] ?? 'text-amber-400 bg-amber-500/10 border-amber-500/20');

const formatTimeAgo = (dateString) => {
    const s = Math.floor((Date.now() - new Date(dateString)) / 1000);
    if (s < 60)    return 'Just now';
    if (s < 3600)  return `${Math.floor(s / 60)}m ago`;
    if (s < 86400) return `${Math.floor(s / 3600)}h ago`;
    return `${Math.floor(s / 86400)}d ago`;
};
</script>

<template>
    <!-- Toast notification -->
    <Transition name="toast-slide">
        <div v-if="showToast && currentToast"
             class="fixed top-20 right-4 z-[200] max-w-sm bg-slate-900/95 backdrop-blur-xl border border-slate-700 rounded-2xl shadow-2xl p-4 flex gap-3 items-start">
            <div :class="['w-10 h-10 rounded-xl border flex items-center justify-center text-xl flex-shrink-0',
                          getStatusColor(currentToast.data?.status)]">
                {{ currentToast.data?.icon || '📄' }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-semibold text-sm text-white">{{ currentToast.data?.title }}</p>
                <p class="text-xs text-slate-400 mt-0.5 line-clamp-2">{{ currentToast.data?.message }}</p>
            </div>
            <button @click="showToast = false" class="text-slate-500 hover:text-white flex-shrink-0">✕</button>
        </div>
    </Transition>

    <!-- Bell button + dropdown -->
    <div class="relative">
        <button @click="togglePanel"
                class="relative p-2.5 rounded-xl bg-slate-800/50 hover:bg-slate-800 border border-slate-700/50 transition-all group"
                aria-label="Notifications">
            <svg class="w-5 h-5 text-slate-300 group-hover:text-blue-400 transition-colors"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
            <span v-if="unreadCount > 0"
                  class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 flex items-center justify-center bg-red-500 text-white text-[10px] font-bold rounded-full border-2 border-slate-950 animate-pulse">
                {{ unreadCount > 99 ? '99+' : unreadCount }}
            </span>
        </button>

        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0 scale-95 translate-y-1"
            enter-to-class="opacity-100 scale-100 translate-y-0"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100 scale-100 translate-y-0"
            leave-to-class="opacity-0 scale-95 translate-y-1"
        >
            <div v-if="isOpen"
                 class="absolute top-full right-0 mt-2 w-80 sm:w-96 bg-slate-900/98 backdrop-blur-xl border border-slate-700 rounded-2xl shadow-2xl overflow-hidden z-[100]">

                <!-- Header -->
                <div class="px-5 py-4 border-b border-slate-700/50 bg-gradient-to-r from-blue-950/50 to-purple-950/50 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-base text-white">Notifications</h3>
                        <p class="text-xs text-slate-400">{{ unreadCount }} unread</p>
                    </div>
                    <div class="flex gap-2 items-center">
                        <button v-if="unreadCount > 0" @click="markAllAsRead"
                                class="px-3 py-1 text-xs bg-blue-600/20 hover:bg-blue-600/30 border border-blue-500/30 rounded-lg text-blue-300 transition-colors font-medium">
                            Mark all read
                        </button>
                        <button @click="isOpen = false"
                                class="p-1 hover:bg-slate-800 rounded-lg text-slate-400 hover:text-white transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Body -->
                <div class="max-h-[420px] overflow-y-auto notification-scroll">
                    <!-- Loading -->
                    <div v-if="isLoading" class="py-10 text-center">
                        <div class="inline-block animate-spin rounded-full h-7 w-7 border-2 border-slate-600 border-t-blue-500"></div>
                    </div>

                    <!-- Error state with retry -->
                    <div v-else-if="loadingError" class="py-10 px-5 text-center">
                        <div class="text-3xl mb-2 opacity-40">⚠️</div>
                        <p class="text-slate-400 text-sm mb-3">Could not load notifications</p>
                        <button @click="loadNotifications"
                                class="px-4 py-1.5 text-xs bg-blue-600/20 border border-blue-500/30 rounded-lg text-blue-300 hover:bg-blue-600/30 transition-colors font-medium">
                            Try again
                        </button>
                    </div>

                    <!-- Empty -->
                    <div v-else-if="notifications.length === 0" class="py-12 text-center">
                        <div class="text-4xl mb-3 opacity-20">🔔</div>
                        <p class="text-slate-500 text-sm">No notifications yet</p>
                    </div>

                    <!-- Items -->
                    <div v-for="notification in notifications" :key="notification.id"
                         @click="handleNotificationClick(notification)"
                         :class="['relative px-4 py-3.5 border-b border-slate-800/50 hover:bg-slate-800/30 transition-colors cursor-pointer group',
                                  !notification.read_at ? 'bg-blue-950/15' : '']">
                        <div v-if="!notification.read_at"
                             class="absolute left-1.5 top-1/2 -translate-y-1/2 w-1.5 h-1.5 bg-blue-500 rounded-full"></div>
                        <div class="flex gap-3 ml-3">
                            <div :class="['flex-shrink-0 w-10 h-10 rounded-xl border flex items-center justify-center text-xl',
                                         getStatusColor(notification.data?.status)]">
                                {{ notification.data?.icon || '📄' }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-2 mb-0.5">
                                    <h4 class="font-semibold text-sm text-slate-200 truncate">{{ notification.data?.title }}</h4>
                                    <span class="text-[10px] text-slate-500 flex-shrink-0">{{ formatTimeAgo(notification.created_at) }}</span>
                                </div>
                                <p class="text-xs text-slate-400 line-clamp-2 mb-1.5">{{ notification.data?.message }}</p>
                                <div class="flex items-center gap-2">
                                    <span :class="['px-1.5 py-0.5 text-[10px] rounded-md border', getStatusColor(notification.data?.status)]">
                                        {{ notification.data?.status_label }}
                                    </span>
                                    <span class="text-[10px] text-slate-600 font-mono">#{{ notification.data?.tracking_code }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div v-if="notifications.length > 0"
                     class="px-5 py-3 border-t border-slate-700/50 bg-slate-900/50">
                    <button @click="router.visit('/notifications'); isOpen = false"
                            class="w-full text-center text-xs text-blue-400 hover:text-blue-300 transition-colors font-medium">
                        View all notifications →
                    </button>
                </div>
            </div>
        </Transition>
    </div>
</template>

<style scoped>
.notification-scroll::-webkit-scrollbar { width: 4px; }
.notification-scroll::-webkit-scrollbar-track { background: transparent; }
.notification-scroll::-webkit-scrollbar-thumb { background: rgba(71,85,105,0.4); border-radius: 4px; }
.notification-scroll::-webkit-scrollbar-thumb:hover { background: rgba(71,85,105,0.7); }
.line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }

.toast-slide-enter-active { animation: slide-in 0.3s cubic-bezier(0.34,1.56,0.64,1); }
.toast-slide-leave-active { animation: slide-out 0.2s ease; }
@keyframes slide-in  { from { opacity: 0; transform: translateX(100%); } to { opacity: 1; transform: translateX(0); } }
@keyframes slide-out { to   { opacity: 0; transform: translateX(100%); } }
</style>