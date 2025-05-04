<script setup lang="ts">
import { Discussion } from '@/types';

defineProps<{
    discussion: Discussion;
}>();
</script>

<template>
    <div>
        {{ discussion }}
    </div>
</template>
