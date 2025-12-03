<script setup>
defineProps(['value']);
</script>

<template>
    <label class="block font-medium text-sm text-gray-700 transition-colors duration-300" :class="$attrs.class">
        <span v-if="value">{{ value }}</span>
        <span v-else><slot /></span>
    </label>
</template>
