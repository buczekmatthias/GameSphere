<script setup lang="ts">
import Skeleton from '@/components/ui/skeleton/Skeleton.vue';

withDefaults(
    defineProps<{
        hasQueries?: boolean;
    }>(),
    {
        hasQueries: false,
    },
);
</script>

<template>
    <div class="games-grid">
        <div class="col-span-full flex flex-col gap-2" v-if="hasQueries">
            <Skeleton class="h-8 w-2/5" />
            <Skeleton class="h-6 w-full" />
        </div>
        <div class="flex flex-col gap-2" v-for="i in new Array(20)" :key="i">
            <Skeleton class="h-80 w-full" />
            <Skeleton class="h-4 w-4/5" />
        </div>
    </div>
</template>
