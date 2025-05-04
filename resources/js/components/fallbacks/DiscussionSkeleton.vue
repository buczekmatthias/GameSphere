<script setup lang="ts">
import Skeleton from '@/components/ui/skeleton/Skeleton.vue';
</script>

<template>
    <div class="flex flex-col gap-4">
        <Skeleton class="h-24 w-full" v-for="i in new Array(8)" :key="i" />
    </div>
</template>
