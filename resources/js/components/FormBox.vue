<script setup lang="ts">
import { Label } from '@/components/ui/label';
import { capitalize } from 'vue';

defineProps<{
    label: string;
    id?: string;
}>();
</script>

<template>
    <div class="form-box">
        <Label :for="id">{{ capitalize(label) }}</Label>
        <slot />
    </div>
</template>
