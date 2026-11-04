<script setup lang="ts">
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';
import { ArrowDownRight, ArrowUpRight } from 'lucide-vue-next';

defineProps<{
    label: string;
    entry: { this_month: number; last_month: number; trend: number };
}>();
</script>

<template>
    <div class="dark:bg-card flex flex-col gap-2 rounded-md border p-2 dark:border-none">
        <p class="capitalize opacity-70">this month {{ label }}</p>
        <div class="mt-auto flex items-center justify-between gap-3">
            <p class="text-xl">{{ entry.this_month }}</p>
            <TooltipProvider>
                <Tooltip>
                    <TooltipTrigger as-child>
                        <div
                            class="flex items-center gap-1 rounded-md p-1.5"
                            :class="{
                                'bg-chart-2/10 text-chart-2': entry.trend > 0,
                                'bg-destructive/10 text-destructive': entry.trend < 0,
                                'bg-chart-3/10 text-chart-3': entry.trend === 0,
                            }"
                        >
                            <template v-if="entry.trend !== 0">
                                <template v-if="entry.trend > 0">
                                    <ArrowUpRight class="mt-1 size-4" />
                                </template>
                                <template v-else>
                                    <ArrowDownRight class="mt-1 size-4" />
                                </template>
                            </template>
                            <p>{{ Math.abs(entry.trend) }}%</p>
                        </div>
                    </TooltipTrigger>
                    <TooltipContent side="left">
                        <p>Last month: {{ entry.last_month }}</p>
                    </TooltipContent>
                </Tooltip>
            </TooltipProvider>
        </div>
    </div>
</template>
