<script setup lang="ts">
import { Separator } from '@/components/ui/separator';

defineProps<{
    title?: string;
    data: {
        name: string;
        color: string;
        value: any;
    }[];
}>();

const getColorClass = (color: string): string => `background-color: ${color};`;
</script>

<template>
    <div class="bg-card text-card-foreground flex h-full w-full flex-col gap-4 rounded-md border-2 border-slate-200/10 p-3 text-sm">
        <p>{{ title }}</p>
        <Separator orientation="horizontal" />
        <div class="flex min-w-[180px] flex-col gap-2">
            <div v-for="(item, key) in data" :key="key" class="flex justify-between">
                <div class="flex items-center">
                    <div class="mr-2 h-5 w-1" :style="getColorClass(item.color)"></div>
                    <span>{{ item.name }}</span>
                </div>
                <span class="ml-4 font-semibold">{{ item.value }}</span>
            </div>
        </div>
    </div>
</template>
