<script setup lang="ts">
import FormActionTap from '@/components/app/FormActionTap.vue';
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
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import type { Report } from '@/types';
import { Link } from '@inertiajs/vue3';
import { Ban, CircleX, Ellipsis, Eye } from 'lucide-vue-next';
import { capitalize } from 'vue';

defineProps<{
    active_reports: Report[];
}>();
</script>

<template>
    <div class="bg-card col-span-full">
        <div class="border-b-card-foreground/20 flex items-center justify-between gap-4 border-b border-solid px-3 py-4">
            <p class="text-xl">Active reports</p>
            <Link :href="route('admin.reports.index')" v-if="active_reports.length > 0">
                <FormActionTap>View all</FormActionTap>
            </Link>
        </div>
        <template v-if="active_reports.length > 0">
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead class="w-80">Slug</TableHead>
                        <TableHead>Reason</TableHead>
                        <TableHead>User</TableHead>
                        <TableHead>Entry</TableHead>
                        <TableHead class="w-32">Created at</TableHead>
                        <TableHead class="w-16"></TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="report in active_reports" :key="report.slug">
                        <TableCell>{{ report.slug }}</TableCell>
                        <TableCell>{{ report.reason }}</TableCell>
                        <TableCell>
                            <TextLink :href="route('user.profile', { user: report.user.username })">{{ report.user.name }}</TextLink>
                        </TableCell>
                        <TableCell>{{ capitalize(report.reportable_type) }}</TableCell>
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
                                    <DropdownMenuItem as-child>
                                        <Link :href="report.reportable" as="button" class="w-full cursor-pointer">
                                            <Eye class="size-4" />
                                            View
                                        </Link>
                                    </DropdownMenuItem>
                                    <DropdownMenuSeparator />
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
                                </DropdownMenuContent>
                            </DropdownMenu>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </template>
        <template v-else>
            <p class="mx-3 my-4">No active reports</p>
        </template>
    </div>
</template>
