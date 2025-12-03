<script setup lang="ts">
import { Label } from '@/components/ui/label';
import { cn } from '@/lib/utils';
import { capitalize, HTMLAttributes } from 'vue';

const props = defineProps<{
    label: string;
    id?: string;
    class?: HTMLAttributes['class'];
}>();
</script>

<template>
    <div :class="cn('flex flex-col gap-1.5', props.class)">
        <Label :for="id">{{ capitalize(label) }}</Label>
        <slot />
    </div>
</template>
