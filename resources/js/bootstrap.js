import _ from 'lodash';
window._ = _;

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.withCredentials = true;

// Helper function to get cookie value
const getCookie = (name) => {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
    return null;
};

// Axios interceptor to add CSRF token to every request
window.axios.interceptors.request.use(
    (config) => {
        // Try to get CSRF token from meta tag first
        const token = document.head.querySelector('meta[name="csrf-token"]');
        if (token && token.content) {
            config.headers['X-CSRF-TOKEN'] = token.content;
        } else {
            // Fallback: get from XSRF-TOKEN cookie
            const xsrfToken = getCookie('XSRF-TOKEN');
            if (xsrfToken) {
                config.headers['X-XSRF-TOKEN'] = decodeURIComponent(xsrfToken);
            }
        }
        return config;
    },
    (error) => {
        return Promise.reject(error);
    }
);

// Axios interceptor to handle 419 errors and refresh CSRF token
window.axios.interceptors.response.use(
    (response) => {
        // Update CSRF token from response headers if available
        const newToken = response.headers['x-csrf-token'];
        if (newToken) {
            const metaTag = document.head.querySelector('meta[name="csrf-token"]');
            if (metaTag) {
                metaTag.setAttribute('content', newToken);
            }
        }
        return response;
    },
    (error) => {
        if (error.response?.status === 419) {
            // Get new CSRF token from response
            const newToken = error.response.headers['x-csrf-token'] || 
                           error.response.data?.csrf_token;
            
            if (newToken) {
                // Update meta tag
                const metaTag = document.head.querySelector('meta[name="csrf-token"]');
                if (metaTag) {
                    metaTag.setAttribute('content', newToken);
                }
                
                // Retry the original request with new token
                if (error.config) {
                    error.config.headers['X-CSRF-TOKEN'] = newToken;
                    return window.axios.request(error.config);
                }
            } else {
                // If no token, reload page
                window.location.reload();
            }
        }
        return Promise.reject(error);
    }
);

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo';

// import Pusher from 'pusher-js';
// window.Pusher = Pusher;

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: import.meta.env.VITE_PUSHER_APP_KEY,
//     cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'mt1',
//     wsHost: import.meta.env.VITE_PUSHER_HOST ? import.meta.env.VITE_PUSHER_HOST : `ws-${import.meta.env.VITE_PUSHER_APP_CLUSTER}.pusher.com`,
//     wsPort: import.meta.env.VITE_PUSHER_PORT ?? 80,
//     wssPort: import.meta.env.VITE_PUSHER_PORT ?? 443,
//     forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? 'https') === 'https',
//     enabledTransports: ['ws', 'wss'],
// });
