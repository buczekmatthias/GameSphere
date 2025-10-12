<script setup lang="ts">
import Table from '@/components/Admin/Table.vue';
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
import type { Pagination as PaginationType, Review } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Ellipsis, Eye, Trash } from 'lucide-vue-next';

interface ReviewsWithReportsCount extends Review {
    reports_count: number;
}

defineProps<{
    reviews: PaginationType & { data: ReviewsWithReportsCount[] };
}>();

const tableHeaders = [
    { label: 'Slug' },
    { label: 'Content', is_sortable: true, column: 'content' },
    { label: 'Game', is_sortable: true, column: 'game' },
    { label: 'User', is_sortable: true, column: 'user' },
    { label: 'Reports', is_sortable: true, column: 'reports' },
    { label: 'Created at', is_sortable: true, column: 'created_at' },
];

const reloadOnly: string[] = ['reviews'];
</script>

<template>
    <Head title="Reviews" />

    <AdminLayout>
        <div class="main-container flex flex-col gap-4">
            <Table :reload-only :headers="tableHeaders">
                <TableRow v-for="review in reviews.data" :key="review.slug">
                    <TableCell>{{ review.slug }}</TableCell>
                    <TableCell>{{ review.content }}</TableCell>
                    <TableCell>
                        <TextLink :href="route('games.show', { game: review.game.slug })">{{ review.game.title }}</TextLink>
                    </TableCell>
                    <TableCell>
                        <TextLink :href="route('user.profile', { user: review.user.username })">{{ review.user.name }}</TextLink>
                    </TableCell>
                    <TableCell>{{ review.reports_count }}</TableCell>
                    <TableCell>{{ review.created_at }}</TableCell>
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
                                    <Link :href="route('admin.reviews.show', { review: review.slug })" as="button" class="w-full cursor-pointer">
                                        <Eye class="size-4" />
                                        View
                                    </Link>
                                </DropdownMenuItem>
                                <DropdownMenuItem as-child>
                                    <Link :href="route('reviews.destroy', { review: review.slug })" method="delete" class="w-full cursor-pointer">
                                        <Trash class="size-4" />
                                        Delete
                                    </Link>
                                </DropdownMenuItem>
                            </DropdownMenuContent>
                        </DropdownMenu>
                    </TableCell>
                </TableRow>
            </Table>

            <Pagination :pagination="getPaginationData(reviews)" :reload-only />
        </div>
    </AdminLayout>
</template>
