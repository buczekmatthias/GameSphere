<script setup lang="ts">
import TextLink from '@/components/TextLink.vue';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, Report } from '@/types';
import { Head } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Reports',
        href: route('user.reports'),
    },
];

defineProps<{
    reports: Report[];
}>();
</script>

<template>
    <Head title="My reports" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6 md:p-4">
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead class="w-24">#</TableHead>
                        <TableHead class="w-80">Id</TableHead>
                        <TableHead>Reason</TableHead>
                        <TableHead class="w-32">Item</TableHead>
                        <TableHead class="w-24">Status</TableHead>
                        <TableHead class="w-32">Created at</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="(report, i) in reports" :key="report.slug">
                        <TableCell>{{ i + 1 }}</TableCell>
                        <TableCell>{{ report.slug }}</TableCell>
                        <TableCell>{{ report.reason }}</TableCell>
                        <TableCell>
                            <TextLink :href="report.reportable as string">Show {{ report.reportable_type }}</TextLink>
                        </TableCell>
                        <TableCell class="capitalize">{{ report.status }}</TableCell>
                        <TableCell>{{ report.created_at }}</TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>
    </AppLayout>
</template>
