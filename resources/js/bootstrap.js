import _ from 'lodash';
window._ = _;

import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// ── Axios XSRF config ─────────────────────────────────────────────────────────
// withCredentials: true → browser sends session + XSRF-TOKEN cookies on every request
// xsrfCookieName  → axios reads this cookie automatically
// xsrfHeaderName  → axios sends it under this header
// Laravel decrypts X-XSRF-TOKEN and compares to the session token.
//
// ⚠️  Do NOT manually set X-CSRF-TOKEN here.
//     Laravel checks X-CSRF-TOKEN BEFORE X-XSRF-TOKEN.
//     If X-CSRF-TOKEN is present and stale → 419, Laravel never checks the cookie.
window.axios.defaults.withCredentials = true;
window.axios.defaults.xsrfCookieName  = 'XSRF-TOKEN';
window.axios.defaults.xsrfHeaderName  = 'X-XSRF-TOKEN';

// ── CSRF refresh helper ───────────────────────────────────────────────────────
// Hits /sanctum/csrf-cookie which tells Laravel to (re-)set the XSRF-TOKEN cookie.
// After this call, axios's built-in xsrfCookieName logic will pick up the fresh
// cookie automatically on the very next request — no manual header writing needed.
let _csrfRefreshPromise = null;

const refreshCsrf = () => {
    if (_csrfRefreshPromise) return _csrfRefreshPromise;
    _csrfRefreshPromise = window.axios
        .get('/sanctum/csrf-cookie')
        .catch(err => console.error('❌ CSRF refresh failed:', err))
        .finally(() => { _csrfRefreshPromise = null; });
    return _csrfRefreshPromise;
};

window.__refreshCsrf = refreshCsrf; // expose for Login.vue, AuthenticatedLayout, etc.

// ── 419 Recovery interceptor ──────────────────────────────────────────────────
// On 419: refresh the CSRF cookie, strip any stale CSRF headers that were baked
// into the original request config (they would override axios defaults), then retry.
let _isRefreshing = false;
let _queue        = [];

const flushQueue = (err) => {
    _queue.forEach(({ resolve, reject, config }) =>
        err ? reject(err) : resolve(window.axios.request(config))
    );
    _queue = [];
};

window.axios.interceptors.response.use(
    res => res,
    async err => {
        const status   = err.response?.status;
        const original = err.config;

        if (status === 419 && !original._retry419) {
            original._retry419 = true;

            // ── Strip stale CSRF headers so fresh cookie defaults take over ──
            if (original.headers) {
                delete original.headers['X-CSRF-TOKEN'];
                delete original.headers['X-XSRF-TOKEN'];
                // Also clear from the request-level header objects axios merges
                ['common', 'post', 'put', 'patch', 'delete'].forEach(method => {
                    if (original.headers[method]) {
                        delete original.headers[method]['X-CSRF-TOKEN'];
                        delete original.headers[method]['X-XSRF-TOKEN'];
                    }
                });
            }

            if (_isRefreshing) {
                return new Promise((resolve, reject) =>
                    _queue.push({ resolve, reject, config: original })
                );
            }

            _isRefreshing = true;
            try {
                await refreshCsrf();
                flushQueue(null);
                return window.axios.request(original);
            } catch (refreshErr) {
                flushQueue(refreshErr);
                console.error('❌ CSRF refresh failed, reloading…', refreshErr);
                window.location.reload();
                return Promise.reject(refreshErr);
            } finally {
                _isRefreshing = false;
            }
        }

        if (status === 401 && !original._retry401) {
            original._retry401 = true;
            window.location.href = '/login';
            return Promise.reject(err);
        }

        return Promise.reject(err);
    }
);

// ── Pusher / Echo ─────────────────────────────────────────────────────────────
const pusherKey = import.meta.env.VITE_PUSHER_APP_KEY;
if (pusherKey && pusherKey.trim() !== '' && pusherKey !== 'undefined') {
    import('laravel-echo').then(({ default: Echo }) => {
        import('pusher-js').then(({ default: Pusher }) => {
            window.Pusher = Pusher;
            window.Echo = new Echo({
                broadcaster:       'pusher',
                key:               pusherKey,
                cluster:           import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'mt1',
                wsHost:            import.meta.env.VITE_PUSHER_HOST ?? window.location.hostname,
                wsPort:            import.meta.env.VITE_PUSHER_PORT ?? 6001,
                wssPort:           import.meta.env.VITE_PUSHER_PORT ?? 6001,
                forceTLS:          false,
                encrypted:         false,
                disableStats:      true,
                enabledTransports: ['ws', 'wss'],
                authEndpoint:      '/broadcasting/auth',
                auth: {
                    headers: {
                        // Broadcasting auth needs X-CSRF-TOKEN (server-side form check)
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                    },
                },
            });
            window.Echo.connector.pusher.connection.bind('connected', () => console.log('✅ WebSocket connected!'));
            window.Echo.connector.pusher.connection.bind('error',     err => console.error('❌ WebSocket error:', err));
        });
    });
}