<script setup lang="ts">
import TableLoader from '@/components/Admin/TableLoader.vue';
import { Table, TableBody, TableHeader, TableRow } from '@/components/ui/table';
import { router } from '@inertiajs/vue3';
import SortableTableHeader from './SortableTableHeader.vue';

const props = defineProps<{
    reloadOnly: string[];
    headers: { label: string; is_sortable?: boolean; column?: string }[];
}>();

const handleChoice = (order: 'asc' | 'desc', column: string) => router.reload({ only: props.reloadOnly, data: { column: column, order: order } });
</script>

<template>
    <TableLoader />
    <Table>
        <TableHeader>
            <TableRow>
                <template v-for="header in headers" :key="header.label">
                    <template v-if="header.is_sortable">
                        <SortableTableHeader
                            :label="header.label"
                            :with-sorting="header.is_sortable"
                            @order-choice="handleChoice($event, header.column!)"
                        />
                    </template>
                    <template v-else>
                        <SortableTableHeader :label="header.label" />
                    </template>
                </template>
                <SortableTableHeader label="" />
            </TableRow>
        </TableHeader>
        <TableBody>
            <slot />
        </TableBody>
    </Table>
</template>
