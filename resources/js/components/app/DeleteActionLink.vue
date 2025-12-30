<script setup lang="ts">
import { cn } from '@/lib/utils';
import { router } from '@inertiajs/vue3';
import { HTMLAttributes } from 'vue';

const props = defineProps<{
    class?: HTMLAttributes['class'];
    href: string;
}>();

const confirmBeforeAction = () => {
    if (confirm('Are you sure you want to delete this?')) {
        router.visit(props.href, {
            method: 'delete',
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <button :class="cn('text-destructive text-sm', props.class)" @click="confirmBeforeAction">
        <slot>Delete</slot>
    </button>
</template>
