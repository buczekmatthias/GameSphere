<script setup lang="ts">
import TextLink from '@/components/TextLink.vue';
import { canInteract } from '@/composables/useCanInteract';

withDefaults(
    defineProps<{
        title?: string;
        content?: string;
    }>(),
    {
        title: () => 'Authentication required',
        content: () => 'Interactions require an account.',
    },
);
</script>

<template>
    <template v-if="canInteract()">
        <slot />
    </template>
    <div class="border-primary/40 bg-primary/5 flex flex-col items-start gap-4 rounded-md border-4 border-solid p-4" v-else>
        <p class="text-xl font-semibold">{{ title }}</p>
        <p class="text-sm md:text-base">{{ content }}</p>
        <TextLink :href="route('security.login')">Login now!</TextLink>
    </div>
</template>
