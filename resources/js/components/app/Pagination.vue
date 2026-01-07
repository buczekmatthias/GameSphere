<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { useZiggy } from '@/composables/useZiggy';
import { Pagination, Ziggy } from '@/types';
import { Link, router } from '@inertiajs/vue3';
import { ChevronLeft, ChevronRight } from 'lucide-vue-next';
import { computed, ComputedRef, ref } from 'vue';

const props = withDefaults(
    defineProps<{
        pagination: Pagination;
        pageName?: string;
        reloadOnly?: string[];
        showCounter?: boolean;
    }>(),
    {
        pageName: () => 'page',
        reloadOnly: () => [],
        showCounter: true,
    },
);

const isNumber = (e: any) => {
    const allowed = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

    if (!allowed.includes(e.key)) {
        e.preventDefault();
    }

    if (currentPage.value > props.pagination.meta.last_page) {
        currentPage.value = props.pagination.meta.last_page;
    }
};

const ziggy: ComputedRef<Ziggy> = useZiggy();

const currentPage = ref<number>(props.pagination.meta.current_page);

const rangeString = computed(
    () => `${props.pagination.meta.from} - ${props.pagination.meta.to > props.pagination.meta.total ? props.pagination.meta.total : props.pagination.meta.to} of
            ${props.pagination.meta.total}`,
);

const changePage = () => {
    router.reload({ only: props.reloadOnly, data: { ...ziggy.value.query, [props.pageName]: currentPage.value } });
};
</script>

<template>
    <div class="col-span-full flex items-center gap-2" v-if="pagination.meta.total > 0">
        <p class="mr-auto" v-if="showCounter">{{ rangeString }}</p>
        <template v-if="pagination.meta.last_page > 1">
            <div>
                <Button variant="outline" as-child :disabled="!pagination.links.prev">
                    <Link :href="pagination.links.prev || ''" as="button">
                        <ChevronLeft />
                    </Link>
                </Button>
                <Button variant="outline" :disabled="!pagination.links.next">
                    <Link :href="pagination.links.next || ''" as="button">
                        <ChevronRight />
                    </Link>
                </Button>
            </div>
            <div class="grid max-w-24 grid-cols-[1fr_auto] items-center gap-1">
                <Input v-model="currentPage" @keyup="isNumber" @keyup.enter="changePage" />
                <p>/{{ pagination.meta.last_page }}</p>
            </div>
        </template>
    </div>
</template>
