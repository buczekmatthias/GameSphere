<script setup lang="ts">
import MainContainer from '@/components/app/MainContainer.vue';
import PaginatedContent from '@/components/app/PaginatedContent.vue';
import Heading from '@/components/Heading.vue';
import TextLink from '@/components/TextLink.vue';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, Pagination, Report } from '@/types';
import { Head } from '@inertiajs/vue3';
import { capitalize } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'My reports',
        href: route('user.reports'),
    },
];

defineProps<{
    reports: Pagination & { data: Report[] };
}>();
</script>

<template>
    <Head title="My reports" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <MainContainer class="mx-auto flex max-w-5xl flex-col gap-4">
            <Heading title="My reports" />
            <PaginatedContent :pagination="reports" pagination-position="bottom">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead class="w-36">Entry type</TableHead>
                            <TableHead>Reason</TableHead>
                            <TableHead class="w-36">Status</TableHead>
                            <TableHead class="w-36">Created at</TableHead>
                            <TableHead class="w-6"></TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="report in reports.data" :key="report.slug">
                            <TableCell>{{ capitalize(report.reportable_type) }}</TableCell>
                            <TableCell>{{ report.reason }}</TableCell>
                            <TableCell class="capitalize">{{ report.status }}</TableCell>
                            <TableCell>{{ report.created_at }}</TableCell>
                            <TableCell class="text-center">
                                <TextLink :href="report.reportable">View</TextLink>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </PaginatedContent>
        </MainContainer>
    </AppLayout>
</template>
