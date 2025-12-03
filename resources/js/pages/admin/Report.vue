<script setup lang="ts">
import Table from '@/components/Admin/Table.vue';
import MainContainer from '@/components/MainContainer.vue';
import Pagination from '@/components/Pagination.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { TableCell, TableRow } from '@/components/ui/table';
import { getPaginationData } from '@/composables/usePagination';
import AdminLayout from '@/layouts/AdminLayout.vue';
import type { Pagination as PaginationType, Report } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Ban, Circle, CircleX, Ellipsis, Trash } from 'lucide-vue-next';

defineProps<{
    reports: PaginationType & { data: Report[] };
}>();

const tableHeaders = [
    { label: 'Slug' },
    { label: 'Reason', is_sortable: true, column: 'reason' },
    { label: 'User', is_sortable: true, column: 'user' },
    { label: 'Entry' },
    { label: 'Status', is_sortable: true, column: 'status' },
    { label: 'Created at', is_sortable: true, column: 'created_at' },
];

const reloadOnly: string[] = ['reports'];
</script>

<template>
    <Head title="Dashboard" />

    <AdminLayout>
        <MainContainer class="flex flex-col gap-4">
            <Table :reload-only :headers="tableHeaders">
                <TableRow v-for="report in reports.data" :key="report.slug">
                    <TableCell>{{ report.slug }}</TableCell>
                    <TableCell>{{ report.reason }}</TableCell>
                    <TableCell>
                        <TextLink :href="route('user.profile', { user: report.user.username })">{{ report.user.name }}</TextLink>
                    </TableCell>
                    <TableCell>
                        <TextLink :href="report.reportable">Show {{ report.reportable_type }}</TextLink>
                    </TableCell>
                    <TableCell class="capitalize">{{ report.status }}</TableCell>
                    <TableCell>{{ report.created_at }}</TableCell>
                    <TableCell>
                        <DropdownMenu>
                            <DropdownMenuTrigger as-child>
                                <Button variant="outline">
                                    <Ellipsis class="size-4" />
                                </Button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent class="w-56">
                                <DropdownMenuLabel>Actions</DropdownMenuLabel>
                                <DropdownMenuSeparator />
                                <template v-if="report.status !== 'open'">
                                    <DropdownMenuItem as-child>
                                        <Link
                                            :href="route('admin.reports.update', { report: report.slug, status: 'open' })"
                                            method="patch"
                                            as="button"
                                            class="w-full cursor-pointer"
                                        >
                                            <Circle class="size-4" />
                                            Open
                                        </Link>
                                    </DropdownMenuItem>
                                    <DropdownMenuItem as-child>
                                        <Link
                                            :href="route('admin.reports.destroy', { report: report.slug })"
                                            method="delete"
                                            as="button"
                                            class="w-full cursor-pointer"
                                        >
                                            <Trash class="size-4" />
                                            Delete
                                        </Link>
                                    </DropdownMenuItem>
                                </template>
                                <template v-else>
                                    <DropdownMenuItem as-child>
                                        <Link
                                            :href="route('admin.reports.update', { report: report.slug, status: 'closed' })"
                                            method="patch"
                                            as="button"
                                            class="w-full cursor-pointer"
                                        >
                                            <CircleX class="size-4" />
                                            Close
                                        </Link>
                                    </DropdownMenuItem>
                                    <DropdownMenuItem as-child>
                                        <Link
                                            :href="route('admin.reports.update', { report: report.slug, status: 'rejected' })"
                                            method="patch"
                                            as="button"
                                            class="w-full cursor-pointer"
                                        >
                                            <Ban class="size-4" />
                                            Reject
                                        </Link>
                                    </DropdownMenuItem>
                                </template>
                            </DropdownMenuContent>
                        </DropdownMenu>
                    </TableCell>
                </TableRow>
            </Table>

            <Pagination :pagination="getPaginationData(reports)" :reload-only />
        </MainContainer>
    </AdminLayout>
</template>
