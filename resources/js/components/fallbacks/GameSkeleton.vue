<script setup lang="ts">
import Skeleton from '@/components/ui/skeleton/Skeleton.vue';
</script>

<template>
    <div class="grid grid-cols-2 gap-4 p-6 md:p-4 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5">
        <div class="flex flex-col gap-2" v-for="i in new Array(20)" :key="i">
            <Skeleton class="h-80 w-full" />
            <Skeleton class="h-4 w-3/4" />
        </div>
    </div>
</template>
