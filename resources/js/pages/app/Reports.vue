<script setup lang="ts">
import MainContainer from '@/components/app/MainContainer.vue';
import Heading from '@/components/Heading.vue';
import TextLink from '@/components/TextLink.vue';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, Report } from '@/types';
import { Head } from '@inertiajs/vue3';
import { capitalize } from 'vue';

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
        <MainContainer>
            <Heading title="My reports" />
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead class="w-24">#</TableHead>
                        <TableHead class="w-80">Id</TableHead>
                        <TableHead>Reason</TableHead>
                        <TableHead class="w-36">Entry type</TableHead>
                        <TableHead class="w-36">Status</TableHead>
                        <TableHead class="w-36">Created at</TableHead>
                        <TableHead class="w-6">Actions</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="(report, i) in reports" :key="report.slug">
                        <TableCell>{{ i + 1 }}</TableCell>
                        <TableCell>
                            <TextLink :href="report.reportable">{{ report.slug }}</TextLink>
                        </TableCell>
                        <TableCell>{{ report.reason }}</TableCell>
                        <TableCell>{{ capitalize(report.reportable_type) }}</TableCell>
                        <TableCell class="capitalize">{{ report.status }}</TableCell>
                        <TableCell>{{ report.created_at }}</TableCell>
                        <TableCell class="text-center">
                            <TextLink :href="report.reportable">View</TextLink>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </MainContainer>
    </AppLayout>
</template>
