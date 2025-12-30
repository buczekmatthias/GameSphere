<script setup lang="ts">
import FormActionTap from '@/components/app/FormActionTap.vue';
import DatePicker from '@/components/app/game/DatePicker.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Skeleton } from '@/components/ui/skeleton';
import { transformDate } from '@/composables/useTransformDatePicker';
import { ZiggyWithGamesQuery } from '@/pages/app/game/Index.vue';
import { Deferred } from '@inertiajs/vue3';
import { DateValue, parseDate } from '@internationalized/date';
import { useMediaQuery } from '@vueuse/core';
import { ArrowUpDown } from 'lucide-vue-next';
import { capitalize, ref, watch } from 'vue';

export interface FilterData {
    released_after?: string | object;
    released_before?: string | object;
    genre?: string;
}

const isMediumLarge = useMediaQuery('(min-width: 848px)');

const emit = defineEmits(['updateSearchConditions']);

const props = defineProps<{
    ziggy: ZiggyWithGamesQuery;
    genres?: string[];
}>();

const data = ref<FilterData>({
    released_after: props.ziggy.query.released_after,
    released_before: props.ziggy.query.released_before,
    genre: props.ziggy.query.genre || '',
});

const handleClose = (isOpen: boolean) => {
    const d = { ...data.value };

    if (!isOpen) {
        if (d.released_after) {
            d.released_after = transformDate(d.released_after);
        }
        if (d.released_before) {
            d.released_before = transformDate(d.released_before);
        }

        emit('updateSearchConditions', d);
    }
};

const getMinValue = (date: any): undefined | DateValue => {
    if (!data.value.released_after) return undefined;

    if (typeof date === 'string') return parseDate(date);

    return date;
};

watch(
    () => data.value.released_after,
    () => {
        if (data.value.released_before) {
            const before = new Date(transformDate(data.value.released_before)).getTime();
            const after = new Date(transformDate(data.value.released_after)).getTime();
            if (before < after) {
                data.value.released_before = data.value.released_after;
            }
        }
    },
);
</script>

<template>
    <Popover @update:open="handleClose">
        <PopoverTrigger as-child>
            <Button variant="outline">
                <ArrowUpDown class="mt-0.5 size-4" />
                Filters
            </Button>
        </PopoverTrigger>
        <PopoverContent :align="isMediumLarge ? 'end' : 'start'" class="flex w-[calc(100vw-3rem)] max-w-[25rem] flex-col gap-4">
            <div class="flex flex-col gap-2">
                <p>Released after</p>
                <DatePicker v-model="data.released_after" trigger-button-class="w-full" />
                <FormActionTap @click="data.released_after = undefined" v-if="data.released_after">Remove</FormActionTap>
            </div>
            <div class="flex flex-col gap-2">
                <p>Released before</p>
                <DatePicker v-model="data.released_before" trigger-button-class="w-full" :min-value="getMinValue(data.released_after)" />
                <FormActionTap @click="data.released_before = undefined" v-if="data.released_before">Remove</FormActionTap>
            </div>
            <Deferred data="genres">
                <template #fallback>
                    <Skeleton class="h-6 w-1/2" />
                    <Skeleton class="h-9 w-full" />
                </template>

                <div class="flex flex-col gap-2">
                    <p>Genre</p>
                    <Select v-model="data.genre">
                        <SelectTrigger class="w-full">
                            <SelectValue placeholder="Select a genre" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem :value="null">No genre</SelectItem>
                            <SelectItem v-for="genre in genres" :key="genre" :value="genre">
                                {{ capitalize(genre) }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <FormActionTap @click="data.genre = ''" v-if="data.genre">Remove</FormActionTap>
                </div>
            </Deferred>
            <TextLink class="self-start" :href="route(ziggy.current)" v-if="Object.keys(ziggy.query).length > 0">Remove all filters</TextLink>
        </PopoverContent>
    </Popover>
</template>
