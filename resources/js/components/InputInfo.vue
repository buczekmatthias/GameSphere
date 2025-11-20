<script setup lang="ts">
defineProps<{
    message?: string;
}>();
</script>

<template>
    <p class="text-muted-foreground text-sm">
        {{ message }}
    </p>
</template>
