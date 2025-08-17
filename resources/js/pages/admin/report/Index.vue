<script setup lang="ts">
import Table from '@/components/Admin/Table.vue';
import Pagination from '@/components/Pagination.vue';
import TextLink from '@/components/TextLink.vue';
import { TableCell, TableRow } from '@/components/ui/table';
import { getPaginationData } from '@/composables/usePagination';
import AdminLayout from '@/layouts/AdminLayout.vue';
import type { Pagination as PaginationType, Report } from '@/types';
import { Head } from '@inertiajs/vue3';

defineProps<{
    reports: PaginationType & { data: Report[] };
}>();

const tableHeaders = [
    { label: 'Slug' },
    { label: 'Reason', is_sortable: true, column: 'reason' },
    { label: 'Status', is_sortable: true, column: 'status' },
    { label: 'Created at', is_sortable: true, column: 'created_at' },
];
</script>

<template>
    <Head title="Dashboard" />

    <AdminLayout>
        <div class="main-container flex flex-col gap-4">
            <Table :reload-only="['reports']" :headers="tableHeaders">
                <TableRow v-for="report in reports.data" :key="report.slug">
                    <TableCell>{{ report.slug }}</TableCell>
                    <TableCell>{{ report.reason }}</TableCell>
                    <TableCell class="capitalize">{{ report.status }}</TableCell>
                    <TableCell>{{ report.created_at }}</TableCell>
                    <TableCell>
                        <TextLink :href="route('admin.reports.show', { report: report.slug })">View report</TextLink>
                    </TableCell>
                </TableRow>
            </Table>

            <Pagination :pagination="getPaginationData(reports)" :reload-only="['reports']" />
        </div>
    </AdminLayout>
</template>
