import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel';

// Update CSRF token in meta tag
const updateCsrfToken = (token) => {
    const metaTag = document.head.querySelector('meta[name="csrf-token"]');
    if (metaTag && token) {
        metaTag.setAttribute('content', token);
    }
};

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue, props.initialPage.props.ziggy)
            .mount(el);
    },
    progress: {
        color: '#F15A2B',
        showSpinner: false,
    },
});

// Update CSRF token on every Inertia page load
router.on('navigate', (event) => {
    if (event.detail.page?.props?.csrf_token) {
        updateCsrfToken(event.detail.page.props.csrf_token);
    }
});

// Handle Inertia errors
router.on('error', (errors) => {
    // Check if it's a 419 error
    if (errors.response?.status === 419) {
        const newToken = errors.response?.headers?.['x-csrf-token'] || 
                        errors.response?.data?.csrf_token;
        
        if (newToken) {
            updateCsrfToken(newToken);
            // Retry the request
            if (errors.retry) {
                errors.retry();
                return;
            }
        }
        
        // If retry is not available or no token, reload page
        window.location.reload();
    }
});
