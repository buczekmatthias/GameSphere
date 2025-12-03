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
import type { Discussion, Pagination as PaginationType } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Ellipsis, Eye, Trash } from 'lucide-vue-next';

interface DiscussionWithReportsCount extends Omit<Discussion, 'discussable'> {
    reports_count: number;
    discussable: string;
}

defineProps<{
    discussions: PaginationType & { data: DiscussionWithReportsCount[] };
}>();

const tableHeaders = [
    { label: 'Slug' },
    { label: 'Title', is_sortable: true, column: 'title' },
    { label: 'Author', is_sortable: true, column: 'author' },
    { label: 'Discussable' },
    { label: 'Comments', is_sortable: true, column: 'comments' },
    { label: 'Reports', is_sortable: true, column: 'reports' },
    { label: 'Created at', is_sortable: true, column: 'created_at' },
];

const reloadOnly: string[] = ['discussions'];
</script>

<template>
    <Head title="Discussions" />

    <AdminLayout>
        <MainContainer class="flex flex-col gap-4">
            <Table :reload-only :headers="tableHeaders">
                <TableRow v-for="discussion in discussions.data" :key="discussion.slug">
                    <TableCell>{{ discussion.slug }}</TableCell>
                    <TableCell>{{ discussion.title }}</TableCell>
                    <TableCell>
                        <TextLink :href="route('user.profile', { user: discussion.author.username })" v-if="discussion.author">
                            {{ discussion.author.name }}
                        </TextLink>
                        <p class="text-sm italic" v-else>Deleted user</p>
                    </TableCell>
                    <TableCell>
                        <TextLink :href="discussion.discussable">Show {{ discussion.discussable_type }}</TextLink>
                    </TableCell>
                    <TableCell class="text-center">{{ discussion.comments_count }}</TableCell>
                    <TableCell class="text-center">{{ discussion.reports_count }}</TableCell>
                    <TableCell>{{ discussion.created_at }}</TableCell>
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
                                    <Link
                                        :href="route('discussions.show', { discussion: discussion.slug })"
                                        as="button"
                                        class="w-full cursor-pointer"
                                    >
                                        <Eye class="size-4" />
                                        View
                                    </Link>
                                </DropdownMenuItem>
                                <DropdownMenuItem as-child>
                                    <Link
                                        :href="route('discussions.destroy', { discussion: discussion.slug, return_back: true })"
                                        method="delete"
                                        class="w-full cursor-pointer"
                                    >
                                        <Trash class="size-4" />
                                        Delete
                                    </Link>
                                </DropdownMenuItem>
                            </DropdownMenuContent>
                        </DropdownMenu>
                    </TableCell>
                </TableRow>
            </Table>

            <Pagination :pagination="getPaginationData(discussions)" :reload-only />
        </MainContainer>
    </AdminLayout>
</template>
