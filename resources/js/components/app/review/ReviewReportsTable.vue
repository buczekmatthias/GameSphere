<script setup lang="ts">
import PaginatedContent from '@/components/app/PaginatedContent.vue';
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
import { getPaginationData } from '@/composables/usePagination';
import { Review } from '@/types';
import { Link } from '@inertiajs/vue3';
import { Ban, Circle, CircleX, Ellipsis, Trash } from 'lucide-vue-next';

defineProps<{
    review: Review;
}>();

const reloadOnly: string[] = ['review'];
</script>

<template>
    <template v-if="review.reports.data.length > 0">
        <p class="text-3xl">Reports</p>
        <PaginatedContent :pagination="getPaginationData(review.reports)" :reload-only pagination-position="bottom">
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead class="w-80">Slug</TableHead>
                        <TableHead class="w-20">Status</TableHead>
                        <TableHead>User</TableHead>
                        <TableHead>Reason</TableHead>
                        <TableHead class="w-32">Created at</TableHead>
                        <TableHead class="w-16"></TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="report in review.reports.data" :key="report.slug">
                        <TableCell>{{ report.slug }}</TableCell>
                        <TableCell class="capitalize">{{ report.status }}</TableCell>
                        <TableCell>
                            <TextLink :href="route('user.profile', { user: review.user.username })">{{ review.user.name }}</TextLink>
                        </TableCell>
                        <TableCell>{{ report.reason }}</TableCell>
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
                </TableBody>
            </Table>
        </PaginatedContent>
    </template>
    <template v-else>
        <p>No reports to display</p>
    </template>
</template>
