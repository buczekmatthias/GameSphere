<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Calendar } from '@/components/ui/calendar';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { cn } from '@/lib/utils';
import { CalendarIcon } from 'lucide-vue-next';

defineProps<{
    triggerButtonClass?: string;
}>();

const model = defineModel<any>();
</script>

<template>
    <Popover>
        <PopoverTrigger as-child>
            <Button
                variant="outline"
                :class="cn('w-[280px] justify-start text-left font-normal', !model && 'text-muted-foreground', triggerButtonClass)"
            >
                <CalendarIcon class="mr-2 h-4 w-4" />
                {{ model || 'Pick a date' }}
            </Button>
        </PopoverTrigger>
        <PopoverContent class="w-auto p-0">
            <Calendar v-model="model" initial-focus />
        </PopoverContent>
    </Popover>
</template>
