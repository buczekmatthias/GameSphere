<script setup lang="ts">
import { userCanInteract } from '@/composables/useCanInteract';
</script>

<template>
    <template v-if="userCanInteract()">
        <slot />
    </template>
</template>
