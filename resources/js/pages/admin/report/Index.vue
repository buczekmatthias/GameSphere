<script setup lang="ts">
import Pagination from '@/components/Pagination.vue';
import TextLink from '@/components/TextLink.vue';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { getPaginationData } from '@/composables/usePagination';
import AdminLayout from '@/layouts/AdminLayout.vue';
import type { Pagination as PaginationType, Report } from '@/types';
import { Head } from '@inertiajs/vue3';

defineProps<{
    reports: PaginationType & { data: Report[] };
}>();
</script>

<template>
    <Head title="Dashboard" />

    <AdminLayout>
        <div class="main-container flex flex-col gap-4">
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead class="w-80">Slug</TableHead>
                        <TableHead>Reason</TableHead>
                        <TableHead class="w-32">Status</TableHead>
                        <TableHead class="w-32">Created at</TableHead>
                        <TableHead class="w-16"></TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="report in reports.data" :key="report.slug">
                        <TableCell>{{ report.slug }}</TableCell>
                        <TableCell>{{ report.reason }}</TableCell>
                        <TableCell class="capitalize">{{ report.status }}</TableCell>
                        <TableCell>{{ report.created_at }}</TableCell>
                        <TableCell>
                            <TextLink :href="route('admin.reports.show', { report: report.slug })">View report</TextLink>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>

            <Pagination :pagination="getPaginationData(reports)" :reload-only="['reports']" />
        </div>
    </AdminLayout>
</template>
