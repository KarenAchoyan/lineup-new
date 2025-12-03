<template>
    <AppLayout>
        <div class="min-h-screen bg-[#232222] pt-[160px] pb-12">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <slot />
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
</script>

