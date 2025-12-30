<script setup lang="ts">
defineProps<{
    hasValue: boolean;
}>();
</script>

<template>
    <template v-if="hasValue">
        <slot />
    </template>
    <template v-else>
        <slot name="fallback">
            <p>Nothing to show</p>
        </slot>
    </template>
</template>
