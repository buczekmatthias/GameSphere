<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { LoaderCircle } from 'lucide-vue-next';

defineProps<{
    isProcessing: boolean;
    isDirty: boolean;
    label: string;
}>();
</script>

<template>
    <Button type="submit" class="w-full" :disabled="isProcessing || !isDirty">
        <LoaderCircle v-if="isProcessing" class="h-4 w-4 animate-spin" />
        {{ label }}
    </Button>
</template>
