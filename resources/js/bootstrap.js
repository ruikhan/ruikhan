import _ from 'lodash';
window._ = _;

import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo supports Pusher,
 * Redis, and Socket.io out of the box.
 */

// ✅ FIXED: Only initialize Pusher if credentials are configured
const pusherKey = import.meta.env.VITE_PUSHER_APP_KEY;

if (pusherKey && pusherKey.trim() !== '' && pusherKey !== 'undefined') {
    import('laravel-echo').then(({ default: Echo }) => {
        import('pusher-js').then(({ default: Pusher }) => {
            window.Pusher = Pusher;

            window.Echo = new Echo({
                broadcaster: 'pusher',
                key: pusherKey,
                cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'mt1',
                wsHost: import.meta.env.VITE_PUSHER_HOST ?? window.location.hostname,
                wsPort: import.meta.env.VITE_PUSHER_PORT ?? 6001,
                wssPort: import.meta.env.VITE_PUSHER_PORT ?? 6001,
                forceTLS: false,
                encrypted: false,
                disableStats: true,
                enabledTransports: ['ws', 'wss'],
                authEndpoint: '/broadcasting/auth',
                auth: {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                    },
                },
            });

            // Debug logging
            window.Echo.connector.pusher.connection.bind('connected', () => {
                console.log('✅ WebSocket connected!');
            });

            window.Echo.connector.pusher.connection.bind('error', (err) => {
                console.error('❌ WebSocket error:', err);
            });
        });
    });
} else {
    console.log('ℹ️ Pusher broadcasting disabled - VITE_PUSHER_APP_KEY not configured');
}