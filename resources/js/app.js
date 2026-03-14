import '../css/app.css';
import './bootstrap';

import { createInertiaApp, router } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue, Ziggy)
            .mount(el);
    },
    progress: { color: '#4f46e5', showSpinner: true },
});

// ════════════════════════════════════════════════════════════════════════════
//  INJECT X-XSRF-TOKEN BEFORE EVERY INERTIA REQUEST
//
//  ⚠️  DO NOT inject X-CSRF-TOKEN here.
//      Laravel checks X-CSRF-TOKEN first. If it's present and stale
//      (meta tag was set at page-load time and never refreshed), Laravel
//      returns 419 immediately — it never even looks at X-XSRF-TOKEN.
//
//  Only inject X-XSRF-TOKEN (encrypted cookie). Laravel decrypts it fresh
//  on every request and compares to the session token.
// ════════════════════════════════════════════════════════════════════════════

const getXsrfCookie = () => {
    try {
        const row = document.cookie.split('; ').find(r => r.startsWith('XSRF-TOKEN='));
        return row ? decodeURIComponent(row.split('=')[1]) : null;
    } catch { return null; }
};

router.on('before', (event) => {
    const xsrf = getXsrfCookie();
    if (xsrf) {
        event.detail.visit.headers ??= {};
        event.detail.visit.headers['X-XSRF-TOKEN'] = xsrf;
        // Explicitly do NOT set X-CSRF-TOKEN
        delete event.detail.visit.headers['X-CSRF-TOKEN'];
    }
});

// ════════════════════════════════════════════════════════════════════════════
//  HANDLE 419 via `invalid` event (non-Inertia response from server)
//
//  Inertia fires `invalid` when the server returns HTML instead of JSON,
//  e.g. the "419 | PAGE EXPIRED" error page.
//
//  For GET  → safe to replay after refreshing cookie
//  For POST/mutations → refresh cookie silently; the caller retries
// ════════════════════════════════════════════════════════════════════════════

router.on('invalid', async (event) => {
    if (event.detail.response?.status !== 419) return;
    event.preventDefault();

    await (window.__refreshCsrf?.() ?? Promise.resolve());

    const method = (event.detail.visit?.method ?? 'get').toLowerCase();
    if (method === 'get' && event.detail.visit?.url) {
        router.visit(event.detail.visit.url.toString(), {
            preserveState: false, preserveScroll: false, replace: true,
        });
    }
    // POST/mutations: token is fresh — caller handles retry
});

// ════════════════════════════════════════════════════════════════════════════
//  CSRF KEEP-ALIVE
// ════════════════════════════════════════════════════════════════════════════

const keepAlive = setInterval(() => window.__refreshCsrf?.(), 5 * 60 * 1000);

let lastVisible = Date.now();
document.addEventListener('visibilitychange', async () => {
    if (document.hidden) { lastVisible = Date.now(); return; }
    if (Date.now() - lastVisible > 2 * 60 * 1000) await window.__refreshCsrf?.();
});

window.addEventListener('beforeunload', () => clearInterval(keepAlive));

// ════════════════════════════════════════════════════════════════════════════
//  DEV LOGGING
// ════════════════════════════════════════════════════════════════════════════
if (import.meta.env.DEV) {
    router.on('start',   e => console.log('🚀', e.detail.visit.url?.pathname));
    router.on('success', e => console.log('✅', e.detail.page.url));
}