<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Calendar } from '@/components/ui/calendar';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { cn } from '@/lib/utils';
import { CalendarDate, parseDate } from '@internationalized/date';
import { CalendarIcon } from 'lucide-vue-next';
import { DateValue } from 'reka-ui';
import { onBeforeMount } from 'vue';

type PresetValue = string | CalendarDate;

defineProps<{
    triggerButtonClass?: string;
    minValue?: DateValue;
    preset?: (PresetValue | PresetValue[])[];
}>();

const model = defineModel<any>();

const updateModel = (date: PresetValue | PresetValue[]) => {
    if (typeof date === 'string') model.value = parseDate(date);
    else if (date instanceof CalendarDate) model.value = date;
    else model.value = date[0] instanceof CalendarDate ? date[0] : parseDate(date[0]);
};

const getDisplay = (date: PresetValue | PresetValue[]) => {
    return Array.isArray(date) ? date[1] : date;
};

onBeforeMount(() => {
    if (typeof model.value === 'string') {
        model.value = parseDate(model.value);
    }
});
</script>

<template>
    <Popover>
        <PopoverTrigger as-child>
            <Button
                variant="outline"
                :class="cn('w-[280px] justify-start py-[24.75px] text-left font-normal', !model && 'text-muted-foreground', triggerButtonClass)"
            >
                <CalendarIcon class="mr-2 h-4 w-4" />
                {{ model || 'Pick a date' }}
            </Button>
        </PopoverTrigger>
        <PopoverContent class="w-auto p-0">
            <div class="grid w-full grid-cols-2 gap-2 p-3 pb-0" v-if="preset">
                <Button @click="updateModel(val)" v-for="(val, i) in preset" :key="i">{{ getDisplay(val) }}</Button>
            </div>

            <Calendar v-model="model" initial-focus :min-value="minValue" />
        </PopoverContent>
    </Popover>
</template>
