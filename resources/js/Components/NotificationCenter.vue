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

// Store channel references for proper cleanup
let userChannel = null;
let broadcastChannel = null;
let pollInterval = null;
let connectionCheckTimeout = null;
let isSetupComplete = ref(false);

onMounted(() => {
    loadNotifications();
    // Delay real-time setup to avoid race conditions
    setTimeout(() => {
        if (!isSetupComplete.value) {
            setupRealTimeListener();
        }
    }, 500);
});

onUnmounted(() => {
    cleanupListeners();
});

// Fetch notifications from server
const loadNotifications = async () => {
    try {
        const response = await fetch('/api/notifications', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            credentials: 'same-origin'
        });

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }

        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            throw new Error('Server returned non-JSON response');
        }

        const data = await response.json();
        notifications.value = data.notifications || [];
        unreadCount.value = data.unread_count || 0;
        loadingError.value = false;
        
    } catch (error) {
        console.error('❌ Failed to load notifications:', error.message);
        loadingError.value = true;
    }
};

// Setup real-time notification listener with better error handling
const setupRealTimeListener = () => {
    // Prevent duplicate setup
    if (isSetupComplete.value) {
        console.log('⚠️ Setup already complete, skipping');
        return;
    }

    // Check if Echo is available
    if (typeof window.Echo === 'undefined' || !window.Echo) {
        console.log('⚠️ Echo not available - using polling fallback');
        setupFastPolling();
        isSetupComplete.value = true;
        return;
    }
    
    console.log('✅ Echo detected - Setting up real-time notifications');
    
    try {
        // Cleanup any existing channels first
        cleanupEchoChannels();

        // Setup private user channel
        const userId = user.value.id;
        userChannel = window.Echo.private(`App.Models.User.${userId}`)
            .notification((notification) => {
                console.log('🔔 Real-time notification received:', notification);
                handleNewNotification(notification);
                isConnected.value = true;
            })
            .error((error) => {
                console.error('❌ User channel error:', error);
                if (!pollInterval) {
                    setupFastPolling();
                }
            });

        // Setup broadcast channel
        broadcastChannel = window.Echo.channel('notifications')
            .listen('.notification.sent', (e) => {
                console.log('📢 Broadcast notification received:', e);
                if (e.userId === user.value.id || e.data?.broadcast === 'all') {
                    handleNewNotification(e);
                }
                isConnected.value = true;
            })
            .error((error) => {
                console.error('❌ Broadcast channel error:', error);
            });
        
        // Verify connection after delay
        connectionCheckTimeout = setTimeout(() => {
            if (!isConnected.value) {
                console.warn('⚠️ Echo connection not verified after 3s, starting polling');
                setupFastPolling();
            }
        }, 3000);
        
        isSetupComplete.value = true;
        console.log('✅ Real-time listeners setup complete');
    } catch (error) {
        console.error('❌ Error setting up Echo:', error);
        setupFastPolling();
        isSetupComplete.value = true;
    }
};

// Fast polling fallback
const setupFastPolling = () => {
    if (pollInterval) {
        console.log('⚠️ Polling already active');
        return;
    }
    
    console.log('⏱️ Starting fast polling (5s intervals)');
    pollInterval = setInterval(loadNotifications, 5000);
};

// Cleanup Echo channels only
const cleanupEchoChannels = () => {
    if (!window.Echo) return;

    try {
        if (userChannel) {
            const channelName = `private-App.Models.User.${user.value.id}`;
            console.log(`🔌 Leaving channel: ${channelName}`);
            window.Echo.leave(channelName);
            userChannel = null;
        }

        if (broadcastChannel) {
            console.log('🔌 Leaving channel: notifications');
            window.Echo.leave('notifications');
            broadcastChannel = null;
        }
    } catch (error) {
        console.warn('Error cleaning up channels:', error);
    }
};

// Complete cleanup on unmount
const cleanupListeners = () => {
    // Clear timeouts
    if (connectionCheckTimeout) {
        clearTimeout(connectionCheckTimeout);
        connectionCheckTimeout = null;
    }

    // Clear polling interval
    if (pollInterval) {
        clearInterval(pollInterval);
        pollInterval = null;
    }
    
    // Leave Echo channels
    cleanupEchoChannels();
    
    isConnected.value = false;
    isSetupComplete.value = false;
};

// Handle new incoming notification
const handleNewNotification = (notification) => {
    // Prevent duplicates
    const exists = notifications.value.some(n => n.id === notification.id);
    if (exists) {
        console.log('⚠️ Duplicate notification ignored');
        return;
    }
    
    // Add to notifications array
    notifications.value.unshift(notification);
    unreadCount.value++;
    
    // Show toast notification
    currentToast.value = notification;
    showToast.value = true;
    
    // Play notification sound
    playNotificationSound();
    
    // Vibrate on mobile
    if ('vibrate' in navigator) {
        navigator.vibrate([100, 50, 100]);
    }
    
    // Auto-hide toast
    setTimeout(() => {
        showToast.value = false;
    }, 5000);
    
    console.log('✅ Notification processed:', notification.data?.title);
};

// Play notification sound
const playNotificationSound = () => {
    try {
        const audio = new Audio('/sounds/notification.mp3');
        audio.volume = 0.5;
        audio.play().catch(() => {
            console.log('🔇 Audio autoplay prevented');
        });
    } catch (e) {
        console.warn('Audio not available:', e);
    }
};

// Toggle notification panel
const togglePanel = () => {
    isOpen.value = !isOpen.value;
    if (isOpen.value && !loadingError.value) {
        loadNotifications();
    }
};

// Mark notification as read
const markAsRead = async (notificationId) => {
    try {
        const response = await fetch(`/api/notifications/${notificationId}/read`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            credentials: 'same-origin'
        });

        if (!response.ok) {
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

// Mark all as read
const markAllAsRead = async () => {
    try {
        const response = await fetch('/api/notifications/mark-all-read', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            credentials: 'same-origin'
        });

        if (!response.ok) {
            throw new Error('Failed to mark all as read');
        }
        
        notifications.value.forEach(n => n.read_at = new Date().toISOString());
        unreadCount.value = 0;
    } catch (error) {
        console.error('Failed to mark all as read:', error);
    }
};

// Handle notification click
const handleNotificationClick = (notification) => {
    markAsRead(notification.id);
    
    if (notification.data?.url) {
        router.visit(notification.data.url);
        isOpen.value = false;
    }
};

// Get notification icon
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

// Format time ago
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

// Close toast
const closeToast = () => {
    showToast.value = false;
};
</script>

<template>
    <div class="relative">
        <!-- Connection/Error indicator -->
        <div 
            v-if="loadingError" 
            class="absolute -top-1 -left-1 w-2 h-2 bg-red-500 rounded-full animate-pulse"
            title="Failed to load notifications"
        ></div>
        <div 
            v-else-if="isConnected" 
            class="absolute -top-1 -left-1 w-2 h-2 bg-green-500 rounded-full"
            title="Real-time connected"
        ></div>
        
        <!-- Notification Bell Button -->
        <button
            @click="togglePanel"
            class="relative p-2 rounded-full bg-gray-800/50 hover:bg-gray-700/50 border border-white/10 transition-all duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
            aria-label="Notifications"
            :aria-expanded="isOpen"
        >
            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
            
            <!-- Unread Badge -->
            <span 
                v-if="unreadCount > 0"
                class="absolute -top-1 -right-1 flex items-center justify-center min-w-[20px] h-5 px-1.5 bg-gradient-to-r from-red-500 to-pink-500 rounded-full text-[10px] font-bold text-white shadow-lg animate-pulse"
            >
                {{ unreadCount > 99 ? '99+' : unreadCount }}
            </span>
        </button>

        <!-- Notification Panel -->
        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="transform scale-95 opacity-0"
            enter-to-class="transform scale-100 opacity-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="transform scale-100 opacity-100"
            leave-to-class="transform scale-95 opacity-0"
        >
            <div
                v-show="isOpen"
                class="absolute right-0 top-full mt-3 w-[90vw] sm:w-96 max-h-[80vh] bg-[#1a1a1a]/98 backdrop-blur-2xl border border-white/10 rounded-2xl shadow-2xl overflow-hidden z-50"
                role="dialog"
                aria-label="Notifications panel"
            >
                <!-- Header -->
                <div class="flex items-center justify-between px-4 py-3 border-b border-white/10 bg-gradient-to-r from-blue-600/10 to-purple-600/10">
                    <div class="flex items-center gap-2">
                        <h3 class="text-base font-bold text-white">Notifications</h3>
                        <span v-if="unreadCount > 0" class="px-2 py-0.5 bg-blue-500/20 text-blue-300 text-xs font-bold rounded-full">
                            {{ unreadCount }} new
                        </span>
                    </div>
                    <button
                        v-if="unreadCount > 0"
                        @click="markAllAsRead"
                        class="text-xs text-blue-400 hover:text-blue-300 font-medium transition-colors"
                    >
                        Mark all read
                    </button>
                </div>

                <!-- Error Message -->
                <div v-if="loadingError" class="px-4 py-6 text-center">
                    <div class="text-4xl mb-2">⚠️</div>
                    <p class="text-sm text-red-400 font-medium mb-2">Failed to load notifications</p>
                    <p class="text-xs text-gray-500 mb-3">Check console for details</p>
                    <button 
                        @click="loadNotifications" 
                        class="px-4 py-2 bg-blue-500/20 hover:bg-blue-500/30 text-blue-300 rounded-lg text-xs font-medium transition-colors"
                    >
                        Retry
                    </button>
                </div>

                <!-- Notifications List -->
                <div v-else class="overflow-y-auto max-h-[60vh] custom-scrollbar">
                    <div v-if="notifications.length === 0" class="flex flex-col items-center justify-center py-12 px-4">
                        <div class="w-16 h-16 bg-gray-800/50 rounded-full flex items-center justify-center text-3xl mb-3">
                            🔔
                        </div>
                        <p class="text-gray-400 text-sm font-medium">No notifications yet</p>
                        <p class="text-gray-500 text-xs mt-1">We'll notify you when something arrives</p>
                    </div>

                    <button
                        v-for="notification in notifications"
                        :key="notification.id"
                        @click="handleNotificationClick(notification)"
                        class="w-full text-left px-4 py-3 hover:bg-white/5 transition-colors border-b border-white/5 focus:outline-none focus-visible:bg-white/5"
                        :class="{ 'bg-blue-500/5': !notification.read_at }"
                    >
                        <div class="flex gap-3">
                            <!-- Icon -->
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-500/20 to-purple-500/20 rounded-xl flex items-center justify-center text-xl border border-white/10">
                                    {{ getNotificationIcon(notification.data?.type || 'info') }}
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-2 mb-1">
                                    <h4 class="text-sm font-semibold text-white truncate">
                                        {{ notification.data?.title || 'Notification' }}
                                    </h4>
                                    <span v-if="!notification.read_at" class="flex-shrink-0 w-2 h-2 bg-blue-500 rounded-full"></span>
                                </div>
                                <p class="text-xs text-gray-400 line-clamp-2 mb-1">
                                    {{ notification.data?.message || notification.data?.body }}
                                </p>
                                <span class="text-[10px] text-gray-500 font-medium">
                                    {{ timeAgo(notification.created_at) }}
                                </span>
                            </div>
                        </div>
                    </button>
                </div>

                <!-- Footer -->
                <div v-if="!loadingError" class="px-4 py-3 border-t border-white/10 bg-black/20">
                    <button
                        @click="router.visit('/notifications'); isOpen = false;"
                        class="w-full text-center text-sm text-blue-400 hover:text-blue-300 font-medium transition-colors"
                    >
                        View all notifications →
                    </button>
                </div>
            </div>
        </Transition>

        <!-- Toast Notification -->
        <Transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="transform translate-x-full opacity-0"
            enter-to-class="transform translate-x-0 opacity-100"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="transform translate-x-0 opacity-100"
            leave-to-class="transform translate-x-full opacity-0"
        >
            <div
                v-if="showToast && currentToast"
                class="fixed top-20 right-4 z-[60] w-[90vw] sm:w-96 bg-gradient-to-r from-blue-600 to-purple-600 backdrop-blur-md border border-white/20 rounded-2xl shadow-2xl overflow-hidden"
                role="alert"
                aria-live="assertive"
            >
                <div class="p-4 flex items-start gap-3">
                    <!-- Icon -->
                    <div class="flex-shrink-0 w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center text-2xl">
                        {{ getNotificationIcon(currentToast.data?.type || 'info') }}
                    </div>

                    <!-- Content -->
                    <div class="flex-1 min-w-0">
                        <h4 class="text-sm font-bold text-white mb-1">
                            {{ currentToast.data?.title || 'New Notification' }}
                        </h4>
                        <p class="text-xs text-white/90 line-clamp-2">
                            {{ currentToast.data?.message || currentToast.data?.body }}
                        </p>
                    </div>

                    <!-- Close Button -->
                    <button
                        @click="closeToast"
                        class="flex-shrink-0 p-1 hover:bg-white/20 rounded-lg transition-colors"
                        aria-label="Close notification"
                    >
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Progress bar -->
                <div class="h-1 bg-white/20">
                    <div class="h-full bg-white animate-progress"></div>
                </div>
            </div>
        </Transition>

        <!-- Backdrop for mobile -->
        <Transition
            enter-active-class="transition-opacity duration-200"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-opacity duration-150"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="isOpen"
                @click="isOpen = false"
                class="fixed inset-0 bg-black/60 backdrop-blur-sm z-40 lg:hidden"
                aria-hidden="true"
            ></div>
        </Transition>
    </div>
</template>

<style scoped>
/* Custom Scrollbar */
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: rgba(0, 0, 0, 0.2);
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(59, 130, 246, 0.4);
    border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgba(59, 130, 246, 0.6);
}

/* Progress Animation */
@keyframes progress {
    from {
        width: 100%;
    }
    to {
        width: 0%;
    }
}

.animate-progress {
    animation: progress 5s linear forwards;
}

/* Line Clamp */
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>