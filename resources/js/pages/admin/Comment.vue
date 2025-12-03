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
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';
import { getPaginationData } from '@/composables/usePagination';
import AdminLayout from '@/layouts/AdminLayout.vue';
import type { DiscussionComment, Pagination as PaginationType } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Ellipsis, Trash } from 'lucide-vue-next';

interface CommentWithReportsCount extends Omit<DiscussionComment, 'media'> {
    reports_count: number;
    media: number;
    shortContent: string;
}

defineProps<{
    comments: PaginationType & { data: CommentWithReportsCount[] };
}>();

const tableHeaders = [
    { label: 'Slug' },
    { label: 'Content', is_sortable: true, column: 'content' },
    { label: 'User', is_sortable: true, column: 'user' },
    { label: 'Discussion' },
    { label: 'Media', is_sortable: true, column: 'media' },
    { label: 'Reports', is_sortable: true, column: 'reports' },
    { label: 'Created at', is_sortable: true, column: 'created_at' },
];

const reloadOnly: string[] = ['comments'];
</script>

<template>
    <Head title="Comments" />

    <AdminLayout>
        <MainContainer class="flex flex-col gap-4">
            <Table :reload-only :headers="tableHeaders">
                <TableRow v-for="comment in comments.data" :key="comment.slug">
                    <TableCell>{{ comment.slug }}</TableCell>
                    <TableCell>
                        <TooltipProvider>
                            <Tooltip>
                                <TooltipTrigger as-child>
                                    <p>{{ comment.shortContent }}</p>
                                </TooltipTrigger>
                                <TooltipContent class="max-w-[75vw]">
                                    <p>{{ comment.content }}</p>
                                </TooltipContent>
                            </Tooltip>
                        </TooltipProvider>
                    </TableCell>
                    <TableCell>
                        <TextLink :href="route('user.profile', { user: comment.user.username })" v-if="comment.user">
                            {{ comment.user.name }}
                        </TextLink>
                        <p class="text-sm italic" v-else>Deleted user</p>
                    </TableCell>
                    <TableCell>
                        <TextLink :href="route('discussions.show', { discussion: comment.discussion.slug })">{{ comment.discussion.title }}</TextLink>
                    </TableCell>
                    <TableCell class="text-center">{{ comment.media }}</TableCell>
                    <TableCell class="text-center">{{ comment.reports_count }}</TableCell>
                    <TableCell>{{ comment.created_at }}</TableCell>
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
                                        :href="route('comments.destroy', { comment: comment.slug, return_back: true })"
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

            <Pagination :pagination="getPaginationData(comments)" :reload-only />
        </MainContainer>
    </AdminLayout>
</template>
