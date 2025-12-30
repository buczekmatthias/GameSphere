<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { LoaderCircle } from 'lucide-vue-next';
import { computed } from 'vue';

const props = defineProps<{
    isProcessing: boolean;
    disabled?: boolean;
    label: string;
    variant?: 'outline' | 'default' | 'destructive' | 'secondary' | 'ghost' | 'link';
}>();

const getVariant = computed(() => {
    if (props.variant) return props.variant;

    return props.isProcessing || props.disabled ? 'outline' : 'default';
});
</script>

<template>
    <Button type="submit" class="w-full" :disabled="isProcessing || disabled" :variant="getVariant">
        <LoaderCircle v-if="isProcessing" class="h-4 w-4 animate-spin" />
        {{ label }}
    </Button>
</template>
