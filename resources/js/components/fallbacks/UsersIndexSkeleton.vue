<script setup lang="ts">
import { Skeleton } from '@/components/ui/skeleton';
</script>

<template>
    <Skeleton class="h-10 w-full" />
    <Skeleton class="mx-auto h-16 w-full" v-for="i in 8" :key="i" />
    <div class="flex items-center justify-between gap-4">
        <Skeleton class="h-6 w-24" />
        <Skeleton class="h-9 w-44" />
    </div>
</template>
