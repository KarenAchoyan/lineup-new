# Multi-Language Setup Guide

This project now supports 4 languages: English (en), Armenian (hy), Georgian (ge), and Russian (ru).

## Features

- ✅ Full translation system for all 4 languages
- ✅ Language switcher component
- ✅ Automatic locale detection and persistence
- ✅ Vue 3 composable for easy translation usage
- ✅ Laravel backend integration with Inertia.js

## Usage in Vue Components

### Basic Translation

```vue
<template>
    <div>
        <h1>{{ t('welcome_to_lineup') }}</h1>
        <p>{{ t('news') }}</p>
    </div>
</template>

<script setup>
import { useTranslation } from '../composables/useTranslation';

const { t } = useTranslation();
</script>
```

### Using the Language Switcher

```vue
<template>
    <div>
        <LanguageSwitcher />
    </div>
</template>

<script setup>
import LanguageSwitcher from '../Components/LanguageSwitcher.vue';
</script>
```

### Available Translation Keys

All translation keys from your original JSON files are available. Some examples:

- `welcome_to_lineup`
- `news`
- `events`
- `about_us`
- `archive`
- `sections`
- `support_us`
- `volunteering`
- `collaboration`
- `donation`
- `dance`
- `vocals`
- `theatre`
- `photo_video`
- `sign_in`
- `sign_up`
- `email`
- `password`
- ... and many more

See `lang/en/app.php` for the complete list.

## Backend Usage (Laravel)

### In PHP/Blade

```php
{{ __('app.welcome_to_lineup') }}
{{ trans('app.news') }}
```

### Setting Locale Programmatically

```php
App::setLocale('hy');
Session::put('locale', 'hy');
```

## How It Works

1. **Middleware**: `SetLocale` middleware detects and sets the locale from session or route
2. **Inertia Sharing**: Translations and locale are shared with all Inertia pages via `HandleInertiaRequests`
3. **Language Switcher**: The `LanguageSwitcher` component allows users to switch languages
4. **Persistence**: Selected language is stored in session and persists across requests

## File Structure

```
lang/
├── en/
│   └── app.php (English translations)
├── hy/
│   └── app.php (Armenian translations)
├── ge/
│   └── app.php (Georgian translations)
└── ru/
    └── app.php (Russian translations)

resources/js/
├── composables/
│   └── useTranslation.js (Vue composable)
└── Components/
    └── LanguageSwitcher.vue (Language switcher component)

app/Http/
├── Middleware/
│   └── SetLocale.php (Locale detection middleware)
└── Controllers/
    └── LocaleController.php (Locale switching controller)
```

## Adding New Translations

1. Add the key-value pair to all 4 language files in `lang/{locale}/app.php`
2. The translations will automatically be available in Vue components via the `t()` function

## Example Page

See `resources/js/Pages/ExampleTranslation.vue` for a complete example of using translations in a Vue component.

