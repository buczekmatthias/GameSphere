<script setup lang="ts">
import Pagination from '@/components/Pagination.vue';
import { getPaginationData } from '@/composables/usePagination';
import { Pagination as PaginationType } from '@/types';

withDefaults(
    defineProps<{
        customizablePerPage?: boolean;
        pagination: PaginationType;
        pageName?: string;
        prefPerPage?: number[];
        reloadOnly?: string[];
        showCounter?: boolean;
        paginationPosition?: 'top' | 'bottom';
    }>(),
    {
        customizablePerPage: false,
        pageName: () => 'page',
        prefPerPage: () => [10, 30, 50, 100],
        reloadOnly: () => [],
        showCounter: true,
        paginationPosition: () => 'top',
    },
);
</script>

<template>
    <div class="flex flex-col gap-4">
        <Pagination
            :customizable-per-page="customizablePerPage"
            :pref-per-page="prefPerPage"
            :page-name="pageName"
            :pagination="getPaginationData(pagination!)"
            :reload-only="reloadOnly"
            :show-counter="showCounter"
            :class="paginationPosition === 'top' ? 'order-first' : 'order-last'"
        />
        <slot />
    </div>
</template>
