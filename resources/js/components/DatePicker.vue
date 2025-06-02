<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Calendar } from '@/components/ui/calendar';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { cn } from '@/lib/utils';
import { DateFormatter } from '@internationalized/date';
import { CalendarIcon } from 'lucide-vue-next';

const df = new DateFormatter('en-UK', { dateStyle: 'long' });

const model = defineModel<any>();

const getDisplay = () => {
    if (!model.value) return 'Pick a date';

    if (typeof model.value === 'string') return model.value;

    return df.format(model.value.toDate());
};
</script>

<template>
    <Popover>
        <PopoverTrigger as-child>
            <Button variant="outline" :class="cn('w-[280px] justify-start text-left font-normal', !model && 'text-muted-foreground')">
                <CalendarIcon class="mr-2 h-4 w-4" />
                {{ getDisplay() }}
            </Button>
        </PopoverTrigger>
        <PopoverContent class="w-auto p-0">
            <Calendar v-model="model" initial-focus />
        </PopoverContent>
    </Popover>
</template>
