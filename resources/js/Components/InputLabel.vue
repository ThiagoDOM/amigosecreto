<script setup>
defineProps({
    value: {
        type: String,
    },
});
</script>

<template>
    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider">
        <span v-if="value">{{ value }}</span>
        <span v-else><slot /></span>
    </label>
</template>
