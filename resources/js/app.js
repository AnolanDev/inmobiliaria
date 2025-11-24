import '../css/app.css';
import './bootstrap';

import { createInertiaApp, router } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import axios from 'axios';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

// Configure CSRF token for Inertia
window.axios = axios;
const token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
    // Also set for fetch requests
    window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
}

// Auto-refresh CSRF token every 60 minutes to prevent expiration
let csrfRefreshInterval = null;

function refreshCsrfToken() {
    axios.get('/refresh-csrf')
        .then(response => {
            if (response.data.token) {
                const metaTag = document.head.querySelector('meta[name="csrf-token"]');
                if (metaTag) {
                    metaTag.content = response.data.token;
                    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = response.data.token;

                    // Log refresh with session info
                    console.log('CSRF token refreshed successfully', {
                        refreshed_at: response.data.refreshed_at,
                        session_lifetime: `${response.data.session_lifetime} minutes`
                    });
                }
            }
        })
        .catch(error => {
            console.warn('Failed to refresh CSRF token:', error);
        });
}

// Start auto-refresh after page load
window.addEventListener('load', () => {
    // Refresh token every 60 minutes (3600000ms)
    // This is safe with a 720-minute (12 hour) session lifetime
    csrfRefreshInterval = setInterval(refreshCsrfToken, 3600000);
});

// Clean up interval on page unload
window.addEventListener('beforeunload', () => {
    if (csrfRefreshInterval) {
        clearInterval(csrfRefreshInterval);
    }
});

// Configure Inertia to handle CSRF token properly
router.on('before', (event) => {
    const csrfToken = document.head.querySelector('meta[name="csrf-token"]');
    if (csrfToken) {
        // Ensure the token is included in all requests
        event.detail.visit.headers = {
            ...event.detail.visit.headers,
            'X-CSRF-TOKEN': csrfToken.content,
        };
    }
});

// Handle 419 errors (CSRF token mismatch) gracefully
router.on('error', (event) => {
    const response = event.detail.response;
    if (response && response.status === 419) {
        event.preventDefault();

        // Log diagnostic information
        console.error('CSRF Token Mismatch (419)', {
            url: event.detail.visit?.url,
            method: event.detail.visit?.method,
            timestamp: new Date().toISOString(),
            currentToken: document.head.querySelector('meta[name="csrf-token"]')?.content,
            userAgent: navigator.userAgent,
            referrer: document.referrer
        });

        // Show user-friendly message
        if (confirm('Tu sesión ha expirado. ¿Deseas recargar la página para continuar?')) {
            window.location.reload();
        }
    }
});

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
