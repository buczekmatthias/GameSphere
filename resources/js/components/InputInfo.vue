<script setup lang="ts">
defineProps<{
    message?: string;
}>();
</script>

<template>
    <p class="text-sm text-slate-600 dark:text-slate-500">
        {{ message }}
    </p>
</template>
