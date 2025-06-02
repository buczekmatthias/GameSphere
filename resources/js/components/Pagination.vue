<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
    Combobox,
    ComboboxAnchor,
    ComboboxEmpty,
    ComboboxGroup,
    ComboboxInput,
    ComboboxItem,
    ComboboxItemIndicator,
    ComboboxList,
    ComboboxTrigger,
} from '@/components/ui/combobox';
import { Select, SelectContent, SelectGroup, SelectItem, SelectLabel, SelectTrigger, SelectValue } from '@/components/ui/select';
import { cn } from '@/lib/utils';
import { Pagination, Ziggy } from '@/types';
import { router, usePage } from '@inertiajs/vue3';
import { Check, ChevronsUpDown, Search } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';

const props = withDefaults(
    defineProps<{
        customizablePerPage?: boolean;
        pagination: Pagination;
        pageName?: string;
        prefPerPage?: number[];
    }>(),
    {
        customizablePerPage: false,
        pageName: () => 'page',
        prefPerPage: () => [10, 30, 50, 100],
    },
);

const isNumber = (e: any) => {
    const allowed = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

    if (!allowed.includes(e.key)) {
        e.preventDefault();
    }
};

const currentPage = ref<number>(props.pagination.meta.current_page);

const pages = computed(() => Array.from(Array(props.pagination.meta.last_page).keys(), (_, j) => j + 1));

const query = computed(() => (usePage().props.ziggy as Ziggy & { query: { per_page: string } }).query);

const preferredPerPage = ref<number>(props.pagination.meta.per_page);

watch(currentPage, () => router.get('', { ...query.value, [props.pageName]: currentPage.value }, { preserveScroll: true }));
watch(preferredPerPage, () => router.get('', { ...query.value, per_page: preferredPerPage.value, [props.pageName]: 1 }, { preserveScroll: true }));
</script>

<template>
    <div class="col-span-full flex items-center gap-2">
        <p class="mr-auto">{{ pagination.meta.from }} - {{ pagination.meta.to }} of {{ pagination.meta.total }}</p>
        <Select v-if="customizablePerPage" v-model="preferredPerPage" required>
            <SelectTrigger class="cursor-pointer">
                <SelectValue placeholder="Select items per page" />
            </SelectTrigger>
            <SelectContent>
                <SelectGroup>
                    <SelectLabel>Items per page</SelectLabel>
                    <SelectItem :value="item" v-for="item in prefPerPage" :key="item" class="cursor-pointer">
                        {{ item }}
                    </SelectItem>
                </SelectGroup>
            </SelectContent>
        </Select>
        <Combobox v-model="currentPage" by="label" v-if="pages.length > 1">
            <ComboboxAnchor as-child class="w-28">
                <ComboboxTrigger as-child>
                    <Button variant="outline" class="justify-between">
                        {{ currentPage ?? 'Select page' }}

                        <ChevronsUpDown class="ml-2 h-4 w-4 shrink-0 opacity-50" />
                    </Button>
                </ComboboxTrigger>
            </ComboboxAnchor>

            <ComboboxList>
                <div class="relative w-full max-w-sm items-center">
                    <ComboboxInput
                        @keypress="isNumber"
                        class="h-10 rounded-none border-0 border-b pl-9 focus-visible:ring-0"
                        placeholder="Select page..."
                    />
                    <span class="absolute inset-y-0 start-0 flex items-center justify-center px-3">
                        <Search class="text-muted-foreground size-4" />
                    </span>
                </div>

                <ComboboxEmpty> No page found. </ComboboxEmpty>

                <ComboboxGroup>
                    <ComboboxItem v-for="page in pages" :key="page" :value="page" class="cursor-pointer">
                        {{ page }}
                        <ComboboxItemIndicator v-if="page === currentPage">
                            <Check :class="cn('ml-auto h-4 w-4')" />
                        </ComboboxItemIndicator>
                    </ComboboxItem>
                </ComboboxGroup>
            </ComboboxList>
        </Combobox>
    </div>
</template>
