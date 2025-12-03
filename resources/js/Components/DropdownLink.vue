<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <Link
        class="block w-full px-4 py-2 text-left text-sm leading-5 text-gray-700 hover:bg-gradient-to-r hover:from-[#F15A2B]/10 hover:to-[#BF3206]/10 hover:text-[#F15A2B] focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out"
    >
        <slot />
    </Link>
</template>
