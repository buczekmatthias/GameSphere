<script setup lang="ts">
import { Gamepad2 } from 'lucide-vue-next';

defineOptions({
    inheritAttrs: false,
});

interface Props {
    size: number;
}

defineProps<Props>();
</script>

<template>
    <Gamepad2 :size="size" />
</template>
