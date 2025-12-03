import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

export function useTranslation() {
    const page = usePage();
    
    const translations = computed(() => page.props.translations || {});
    const currentLocale = computed(() => page.props.locale || 'en');
    const availableLocales = computed(() => page.props.availableLocales || ['en', 'hy', 'ge', 'ru']);
    
    /**
     * Translate a key
     * @param {string} key - Translation key
     * @param {object} params - Optional parameters for replacement
     * @returns {string}
     */
    const t = (key, params = {}) => {
        let translation = translations.value[key] || key;
        
        // Replace parameters if provided
        if (Object.keys(params).length > 0) {
            Object.keys(params).forEach(param => {
                translation = translation.replace(`:${param}`, params[param]);
            });
        }
        
        return translation;
    };
    
    /**
     * Get locale name for display
     * @param {string} locale - Locale code
     * @returns {string}
     */
    const getLocaleName = (locale) => {
        const names = {
            'en': 'English',
            'hy': 'Հայերեն',
            'ge': 'ქართული',
            'ru': 'Русский'
        };
        return names[locale] || locale;
    };
    
    return {
        t,
        translations,
        currentLocale,
        availableLocales,
        getLocaleName
    };
}

