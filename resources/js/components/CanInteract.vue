<script setup lang="ts">
import { canInteract } from '@/composables/useCanInteract';
</script>

<template>
    <template v-if="canInteract()">
        <slot />
    </template>
</template>
