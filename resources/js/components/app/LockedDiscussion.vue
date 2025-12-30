<script setup lang="ts">
import HeadingSmall from '@/components/HeadingSmall.vue';

defineProps<{
    isLocked: boolean;
}>();
</script>

<template>
    <HeadingSmall title="Discussion locked" description="This discussion is locked. Only creator and staff can post comments." v-if="isLocked" />
    <slot v-else />
</template>
