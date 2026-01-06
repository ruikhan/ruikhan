<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';

const props = defineProps({
    notifications: Object,
    unread_count: Number
});

const markAsRead = async (notificationId) => {
    try {
        await fetch(`/api/notifications/${notificationId}/read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'Accept': 'application/json',
            }
        });
        
        router.reload({ only: ['notifications', 'unread_count'] });
    } catch (error) {
        console.error('Error marking as read:', error);
    }
};

const markAllAsRead = async () => {
    try {
        await fetch('/api/notifications/mark-all-read', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'Accept': 'application/json',
            }
        });
        
        router.reload({ only: ['notifications', 'unread_count'] });
    } catch (error) {
        console.error('Error marking all as read:', error);
    }
};

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

const handleNotificationClick = (notification) => {
    markAsRead(notification.id);
    if (notification.data?.url) {
        router.visit(notification.data.url);
    }
};
</script>

<template>
    <Head title="Notifications" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h1 class="text-3xl font-bold text-white">Notifications</h1>
                        <p class="text-gray-400 mt-1">
                            {{ unread_count }} unread notification{{ unread_count !== 1 ? 's' : '' }}
                        </p>
                    </div>
                    <button
                        v-if="unread_count > 0"
                        @click="markAllAsRead"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors"
                    >
                        Mark all as read
                    </button>
                </div>

                <!-- Notifications List -->
                <div class="space-y-3">
                    <!-- Empty State -->
                    <div v-if="notifications.data.length === 0" class="text-center py-16 bg-gray-800/50 rounded-2xl">
                        <div class="text-6xl mb-4">🔔</div>
                        <h3 class="text-xl font-semibold text-gray-300 mb-2">No notifications yet</h3>
                        <p class="text-gray-500">When you get notifications, they'll show up here</p>
                    </div>

                    <!-- Notification Items -->
                    <button
                        v-for="notification in notifications.data"
                        :key="notification.id"
                        @click="handleNotificationClick(notification)"
                        class="w-full text-left p-6 rounded-xl transition-all duration-200 flex items-start gap-4"
                        :class="[
                            notification.read_at 
                                ? 'bg-gray-800/30 hover:bg-gray-800/50' 
                                : 'bg-blue-600/10 hover:bg-blue-600/20 border border-blue-500/30'
                        ]"
                    >
                        <!-- Icon -->
                        <div class="flex-shrink-0">
                            <div class="w-14 h-14 rounded-xl flex items-center justify-center text-2xl bg-gradient-to-br from-blue-500/20 to-purple-500/20 border border-white/10">
                                {{ getNotificationIcon(notification.data?.type || 'info') }}
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2 mb-2">
                                <h3 class="text-lg font-semibold text-white">
                                    {{ notification.data?.title || 'Notification' }}
                                </h3>
                                <span v-if="!notification.read_at" class="flex-shrink-0 w-3 h-3 bg-blue-500 rounded-full"></span>
                            </div>
                            <p class="text-gray-300 mb-3">
                                {{ notification.data?.message || notification.data?.body }}
                            </p>
                            <div class="flex items-center gap-4 text-sm text-gray-500">
                                <span>{{ timeAgo(notification.created_at) }}</span>
                                <span v-if="!notification.read_at" class="text-blue-400 font-medium">Unread</span>
                            </div>
                        </div>
                    </button>
                </div>

                <!-- Pagination -->
                <div v-if="notifications.links.length > 3" class="mt-8 flex justify-center gap-2">
                    <template v-for="link in notifications.links" :key="link.label">
                        <button
                            v-if="link.url"
                            @click="router.visit(link.url)"
                            class="px-4 py-2 rounded-lg transition-colors"
                            :class="[
                                link.active 
                                    ? 'bg-blue-600 text-white' 
                                    : 'bg-gray-800 text-gray-300 hover:bg-gray-700'
                            ]"
                            v-html="link.label"
                        />
                    </template>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>