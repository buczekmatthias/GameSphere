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
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';
import { getPaginationData } from '@/composables/usePagination';
import AdminLayout from '@/layouts/AdminLayout.vue';
import type { Game, Pagination as PaginationType } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Ellipsis, Eye, Pen, Trash } from 'lucide-vue-next';

defineProps<{
    games: PaginationType & { data: Game[] };
}>();

const tableHeaders = [
    { label: 'Slug' },
    { label: 'Title', is_sortable: true, column: 'title' },
    { label: 'Description' },
    { label: 'Media count', is_sortable: true, column: 'media' },
    { label: 'Genre', is_sortable: true, column: 'genre' },
    { label: 'Creator', is_sortable: true, column: 'creator' },
    { label: 'Released at', is_sortable: true, column: 'released_at' },
    { label: 'Created at', is_sortable: true, column: 'created_at' },
];

const reloadOnly: string[] = ['games'];
</script>

<template>
    <Head title="Games" />

    <AdminLayout>
        <div class="main-container flex flex-col gap-4">
            <Table :reload-only :headers="tableHeaders">
                <TableRow v-for="game in games.data" :key="game.slug">
                    <TableCell>{{ game.slug }}</TableCell>
                    <TableCell>{{ game.title }}</TableCell>
                    <TableCell>
                        <TooltipProvider>
                            <Tooltip>
                                <TooltipTrigger as-child>
                                    <p>{{ game.short_description }}</p>
                                </TooltipTrigger>
                                <TooltipContent class="max-w-[75vw]">
                                    <p>{{ game.description }}</p>
                                </TooltipContent>
                            </Tooltip>
                        </TooltipProvider>
                    </TableCell>
                    <TableCell class="text-center">{{ game.media_count }}</TableCell>
                    <TableCell>
                        <TextLink :href="route('genres.show', { genre: game.genre.slug })">{{ game.genre.name }}</TextLink>
                    </TableCell>
                    <TableCell>
                        <TextLink :href="route('user.profile', { user: game.creator.username })">{{ game.creator.name }}</TextLink>
                    </TableCell>
                    <TableCell>{{ game.released_at }}</TableCell>
                    <TableCell>{{ game.created_at }}</TableCell>
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
                                    <Link :href="route('games.show', { game: game.slug })" as="button" class="w-full cursor-pointer">
                                        <Eye class="size-4" />
                                        View
                                    </Link>
                                </DropdownMenuItem>
                                <DropdownMenuItem as-child>
                                    <Link :href="route('games.edit', { game: game.slug })" as="button" class="w-full cursor-pointer">
                                        <Pen class="size-4" />
                                        Edit
                                    </Link>
                                </DropdownMenuItem>
                                <DropdownMenuItem as-child>
                                    <Link :href="route('games.destroy', { game: game.slug })" method="delete" class="w-full cursor-pointer">
                                        <Trash class="size-4" />
                                        Delete
                                    </Link>
                                </DropdownMenuItem>
                            </DropdownMenuContent>
                        </DropdownMenu>
                    </TableCell>
                </TableRow>
            </Table>

            <Pagination :pagination="getPaginationData(games)" :reload-only />
        </div>
    </AdminLayout>
</template>
