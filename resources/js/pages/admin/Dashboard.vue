<script setup lang="ts">
import Trend from '@/components/Admin/Trend.vue';
import Pagination from '@/components/Pagination.vue';
import TextLink from '@/components/TextLink.vue';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { getPaginationData } from '@/composables/usePagination';
import AdminLayout from '@/layouts/AdminLayout.vue';
import type { Pagination as PaginationType, Report } from '@/types';
import { Head } from '@inertiajs/vue3';

interface Entries {
    games: { this_month: number; last_month: number };
    users: { this_month: number; last_month: number };
    reports: { this_month: number; last_month: number };
    discussions: { this_month: number; last_month: number };
}

defineProps<{
    new_entries: Entries;
    active_reports: PaginationType & { data: Report[] };
}>();
</script>

<template>
    <Head title="Dashboard" />

    <AdminLayout>
        <div class="main-container grid grid-cols-1 gap-4 lg:grid-cols-[2.5fr_1fr]">
            <p class="col-span-full text-2xl">Overview</p>
            <div class="grid grid-cols-1 grid-rows-4 gap-4 lg:col-start-2 lg:row-start-2">
                <Trend v-for="[label, entry] in Object.entries(new_entries)" :key="label" :label="label" :entry="entry" />
            </div>
            <div class="bg-card h-[30rem] rounded-md p-3">
                <p>Placeholder for chart when it's available with Tailwind v4</p>
            </div>
            <div class="bg-card col-span-full">
                <div class="border-b-card-foreground/20 flex items-center justify-between gap-4 border-b border-solid px-3 py-4">
                    <p class="text-xl">Active reports</p>
                    <Pagination :show-counter="false" :pagination="getPaginationData(active_reports)" :reload-only="['active_reports']" />
                </div>
                <template v-if="active_reports.data.length < 1">
                    <p class="mx-3 my-4">No active reports</p>
                </template>
                <template v-else>
                    <div class="m-1 flex flex-col gap-4">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead class="w-80">Slug</TableHead>
                                    <TableHead>Reason</TableHead>
                                    <TableHead class="w-32">Created at</TableHead>
                                    <TableHead class="w-16"></TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="report in active_reports.data" :key="report.slug">
                                    <TableCell>{{ report.slug }}</TableCell>
                                    <TableCell>{{ report.reason }}</TableCell>
                                    <TableCell>{{ report.created_at }}</TableCell>
                                    <TableCell>
                                        <TextLink :href="route('admin.reports.show', { report: report.slug })">View report</TextLink>
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </div>
                </template>
            </div>
        </div>
    </AdminLayout>
</template>
