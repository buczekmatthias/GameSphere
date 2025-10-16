<script setup lang="ts">
import Table from '@/components/Admin/Table.vue';
import Heading from '@/components/Heading.vue';
import Pagination from '@/components/Pagination.vue';
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
import type { Pagination as PaginationType, User as UserType } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Check, Ellipsis, User, X } from 'lucide-vue-next';

interface GameCreatorRequest {
    slug: string;
    user: UserType;
    created_at: string;
}

defineProps<{
    requests: PaginationType & { data: GameCreatorRequest[] };
}>();

const tableHeaders = [{ label: 'Slug' }, { label: 'Name' }, { label: 'Username' }, { label: 'Created at' }];

const reloadOnly: string[] = ['requests'];
</script>

<template>
    <Head title="Requests to become game creator" />

    <AdminLayout>
        <div class="main-container flex flex-col gap-4">
            <Heading title="Requests to become game creator" />
            <template v-if="requests.data.length > 0">
                <Table :reload-only :headers="tableHeaders">
                    <TableRow v-for="request in requests.data" :key="request.slug">
                        <TableCell>{{ request.slug }}</TableCell>
                        <TableCell>{{ request.user.name }}</TableCell>
                        <TableCell>{{ request.user.username }}</TableCell>
                        <TableCell>{{ request.created_at }}</TableCell>
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
                                            :href="route('user.profile', { user: request.user.username })"
                                            as="button"
                                            class="w-full cursor-pointer"
                                        >
                                            <User class="size-4" />
                                            View user profile
                                        </Link>
                                    </DropdownMenuItem>
                                    <DropdownMenuItem as-child>
                                        <Link
                                            :href="route('admin.game.creator.join.accept', { user: request.user.username })"
                                            as="button"
                                            method="post"
                                            class="w-full cursor-pointer"
                                        >
                                            <Check class="size-4" />
                                            Accept
                                        </Link>
                                    </DropdownMenuItem>
                                    <DropdownMenuItem as-child>
                                        <Link
                                            :href="route('admin.game.creator.join.reject', { user: request.user.username })"
                                            as="button"
                                            method="post"
                                            class="w-full cursor-pointer"
                                        >
                                            <X class="size-4" />
                                            Reject
                                        </Link>
                                    </DropdownMenuItem>
                                </DropdownMenuContent>
                            </DropdownMenu>
                        </TableCell>
                    </TableRow>
                </Table>

                <Pagination :pagination="getPaginationData(requests)" :reload-only v-if="requests.data.length > 0" />
            </template>
            <p v-else>Nothing to display</p>
        </div>
    </AdminLayout>
</template>
