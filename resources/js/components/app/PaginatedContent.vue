<script setup lang="ts">
import Pagination from '@/components/app/Pagination.vue';
import { Pagination as PaginationType } from '@/types';

withDefaults(
    defineProps<{
        pagination: PaginationType;
        pageName?: string;
        reloadOnly?: string[];
        showCounter?: boolean;
        paginationPosition?: 'top' | 'bottom';
        containerClass?: string;
    }>(),
    {
        pageName: () => 'page',
        reloadOnly: () => [],
        showCounter: true,
        paginationPosition: () => 'top',
        containerClass: () => 'flex flex-col gap-4',
    },
);
</script>

<template>
    <div :class="containerClass">
        <Pagination
            :page-name="pageName"
            :pagination="pagination"
            :reload-only="reloadOnly"
            :show-counter="showCounter"
            :class="paginationPosition === 'top' ? 'order-first' : 'order-last'"
        />
        <slot />
    </div>
</template>
