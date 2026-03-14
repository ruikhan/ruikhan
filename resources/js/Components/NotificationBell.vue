<script setup>
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import { onMounted, onUnmounted, ref } from 'vue';

const props = defineProps({
    user: { type: Object, required: true }
});

const notifications  = ref([]);
const unreadCount    = ref(0);
const isOpen         = ref(false);
const isLoading      = ref(false);
const fetchError     = ref(null);   // surface errors in the UI

// ── Lifecycle ─────────────────────────────────────────────────────────────────
onMounted(async () => {
    await fetchNotifications();
    setupEchoListener();
});

onUnmounted(() => {
    if (window.Echo) {
        window.Echo.leave(`App.Models.User.${props.user.id}`);
    }
});

// ── Data fetching ─────────────────────────────────────────────────────────────

/**
 * Fetch notifications via the web-route AJAX endpoint.
 * The global axios interceptor in bootstrap.js will auto-refresh a stale
 * CSRF token (419) and retry, so we don't need to handle that here.
 */
const fetchNotifications = async () => {
    try {
        isLoading.value  = true;
        fetchError.value = null;

        const { data } = await axios.get('/api/notifications');

        notifications.value = data.notifications  ?? [];
        unreadCount.value   = data.unread_count   ?? 0;
    } catch (error) {
        // Only show an error if the interceptor gave up (i.e. not a 419 retry)
        if (error.response?.status !== 419) {
            console.error('❌ Failed to fetch notifications:', error);
            fetchError.value = 'Could not load notifications.';
        }
    } finally {
        isLoading.value = false;
    }
};

// ── Real-time ─────────────────────────────────────────────────────────────────
const setupEchoListener = () => {
    if (!window.Echo) {
        console.log('📡 Real-time notifications: polling mode active (Echo not configured)');
        return;
    }

    window.Echo.private(`App.Models.User.${props.user.id}`)
        .notification(handleNewNotification);

    console.log('📡 Real-time notifications: Echo listening');
};

const handleNewNotification = (notification) => {
    notifications.value.unshift({
        id:         notification.id,
        type:       notification.type,
        data:       notification,
        read_at:    null,
        created_at: new Date().toISOString(),
    });
    unreadCount.value++;
    console.log('🔔', notification.title);
};

// ── Mark as read ──────────────────────────────────────────────────────────────
const markAsRead = async (notificationId) => {
    try {
        await axios.post(`/api/notifications/${notificationId}/read`);

        const n = notifications.value.find(n => n.id === notificationId);
        if (n && !n.read_at) {
            n.read_at     = new Date().toISOString();
            unreadCount.value = Math.max(0, unreadCount.value - 1);
        }
    } catch (error) {
        // 419 is handled and retried by the global interceptor — only log real failures
        if (error.response?.status !== 419) {
            console.error('❌ Mark as read error:', error.response?.data ?? error.message);
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
            console.error('❌ Mark all as read error:', error.response?.data ?? error.message);
        }
    }
};

// ── Navigation ────────────────────────────────────────────────────────────────
const handleNotificationClick = (notification) => {
    markAsRead(notification.id);

    if (notification.data?.url) {
        router.visit(notification.data.url);
    }

    isOpen.value = false;
};

// ── Presentational helpers ────────────────────────────────────────────────────
const getStatusColor = (status) => ({
    pending:          'text-amber-400   bg-amber-500/10   border-amber-500/20',
    processing:       'text-blue-400    bg-blue-500/10    border-blue-500/20',
    ready_for_pickup: 'text-emerald-400 bg-emerald-500/10 border-emerald-500/20',
    completed:        'text-slate-400   bg-slate-500/10   border-slate-500/20',
    rejected:         'text-red-400     bg-red-500/10     border-red-500/20',
}[status] ?? 'text-amber-400 bg-amber-500/10 border-amber-500/20');

const formatTimeAgo = (dateString) => {
    const seconds = Math.floor((Date.now() - new Date(dateString)) / 1000);
    if (seconds < 60)    return 'Just now';
    if (seconds < 3600)  return `${Math.floor(seconds / 60)}m ago`;
    if (seconds < 86400) return `${Math.floor(seconds / 3600)}h ago`;
    return `${Math.floor(seconds / 86400)}d ago`;
};
</script>

<template>
    <div class="relative">
        <!-- Bell Button -->
        <button
            @click="isOpen = !isOpen"
            class="relative p-3 bg-slate-800/50 hover:bg-slate-800 border border-slate-700 rounded-xl transition-all shadow-lg hover:shadow-blue-500/20 group"
            aria-label="Notifications"
        >
            <svg class="w-6 h-6 text-slate-300 group-hover:text-blue-400 transition-colors"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>

            <!-- Unread badge -->
            <span v-if="unreadCount > 0"
                class="absolute -top-1 -right-1 flex items-center justify-center min-w-[22px] h-[22px] px-1.5 bg-gradient-to-r from-red-600 to-pink-600 text-white text-xs font-bold rounded-full border-2 border-slate-950 animate-pulse">
                {{ unreadCount > 99 ? '99+' : unreadCount }}
            </span>
        </button>

        <!-- Dropdown -->
        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95"
        >
            <div v-if="isOpen"
                 class="absolute top-full right-0 mt-2 w-96 bg-slate-900/95 backdrop-blur-xl border border-slate-700 rounded-2xl shadow-2xl overflow-hidden z-50">

                <!-- Header -->
                <div class="px-6 py-4 border-b border-slate-700/50 flex items-center justify-between bg-gradient-to-r from-blue-950/50 to-purple-950/50">
                    <div>
                        <h3 class="font-bold text-lg text-white">Notifications</h3>
                        <p class="text-xs text-slate-400">{{ unreadCount }} unread</p>
                    </div>
                    <div class="flex gap-2">
                        <button v-if="unreadCount > 0" @click="markAllAsRead"
                            class="px-3 py-1 text-xs bg-blue-600/20 hover:bg-blue-600/30 border border-blue-500/30 rounded-lg text-blue-300 hover:text-blue-200 transition-colors font-medium">
                            Mark all read
                        </button>
                        <button @click="isOpen = false"
                            class="p-1 hover:bg-slate-800 rounded-lg transition-colors text-slate-400 hover:text-white">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Body -->
                <div class="max-h-[500px] overflow-y-auto">

                    <!-- Loading -->
                    <div v-if="isLoading" class="py-12 text-center">
                        <div class="inline-block animate-spin rounded-full h-8 w-8 border-2 border-slate-600 border-t-blue-500"></div>
                    </div>

                    <!-- Fetch error -->
                    <div v-else-if="fetchError" class="py-10 px-6 text-center">
                        <div class="text-3xl mb-3 opacity-40">⚠️</div>
                        <p class="text-slate-400 text-sm mb-4">{{ fetchError }}</p>
                        <button @click="fetchNotifications"
                            class="px-4 py-2 text-xs bg-blue-600/20 hover:bg-blue-600/30 border border-blue-500/30 rounded-lg text-blue-300 font-medium transition-colors">
                            Try again
                        </button>
                    </div>

                    <!-- Empty -->
                    <div v-else-if="notifications.length === 0" class="py-12 text-center">
                        <svg class="w-12 h-12 mx-auto text-slate-700 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <p class="text-slate-500 text-sm">No notifications yet</p>
                    </div>

                    <!-- Items -->
                    <div v-for="notification in notifications" :key="notification.id"
                         @click="handleNotificationClick(notification)"
                         :class="['relative p-4 border-b border-slate-800/50 hover:bg-slate-800/30 transition-colors cursor-pointer group',
                                  !notification.read_at ? 'bg-blue-950/20' : '']">

                        <!-- Unread dot -->
                        <div v-if="!notification.read_at"
                             class="absolute left-2 top-1/2 -translate-y-1/2 w-2 h-2 bg-blue-500 rounded-full"></div>

                        <div class="flex gap-4 ml-4">
                            <div :class="['flex-shrink-0 w-12 h-12 rounded-xl border flex items-center justify-center text-2xl',
                                         getStatusColor(notification.data?.status)]">
                                {{ notification.data?.icon || '📄' }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-2 mb-1">
                                    <h4 class="font-semibold text-sm text-slate-200">{{ notification.data?.title }}</h4>
                                    <span class="text-xs text-slate-500 flex-shrink-0">{{ formatTimeAgo(notification.created_at) }}</span>
                                </div>
                                <p class="text-sm text-slate-400 mb-2 line-clamp-2">{{ notification.data?.message }}</p>
                                <div class="flex items-center gap-2">
                                    <span :class="['px-2 py-1 text-xs rounded-lg border', getStatusColor(notification.data?.status)]">
                                        {{ notification.data?.status_label }}
                                    </span>
                                    <span class="text-xs text-slate-500">#{{ notification.data?.tracking_code }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div v-if="notifications.length > 0"
                     class="px-6 py-3 border-t border-slate-700/50 bg-slate-900/50">
                    <button @click="router.visit('/notifications'); isOpen = false"
                        class="w-full text-center text-sm text-blue-400 hover:text-blue-300 transition-colors font-medium">
                        View all notifications →
                    </button>
                </div>
            </div>
        </Transition>
    </div>
</template>

<style scoped>
.overflow-y-auto::-webkit-scrollbar { width: 8px; }
.overflow-y-auto::-webkit-scrollbar-track { background: transparent; }
.overflow-y-auto::-webkit-scrollbar-thumb { background: rgba(71,85,105,0.5); border-radius: 4px; }
.overflow-y-auto::-webkit-scrollbar-thumb:hover { background: rgba(71,85,105,0.7); }
.line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
</style>