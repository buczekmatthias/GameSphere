<script setup lang="ts">
import Table from '@/components/Admin/Table.vue';
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
import type { User as UserType } from '@/types';
import { Link } from '@inertiajs/vue3';
import { Check, Ellipsis, User, X } from 'lucide-vue-next';

export interface GameCreatorRequest {
    slug: string;
    user: UserType;
    created_at: string;
}

defineProps<{
    requests: GameCreatorRequest[];
}>();

const tableHeaders = [{ label: 'Slug' }, { label: 'Name' }, { label: 'Username' }, { label: 'Created at' }];

const reloadOnly: string[] = ['requests'];
</script>

<template>
    <template v-if="requests.length > 0">
        <Table :reload-only :headers="tableHeaders">
            <TableRow v-for="request in requests" :key="request.slug">
                <TableCell>{{ request.slug }}</TableCell>
                <TableCell>{{ request.user.name }}</TableCell>
                <TableCell>
                    <TextLink :href="route('user.profile', { user: request.user.username })">{{ request.user.username }}</TextLink>
                </TableCell>
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
                                <Link :href="route('user.profile', { user: request.user.username })" as="button" class="w-full cursor-pointer">
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

        <slot />
    </template>
    <p v-else>Nothing to display</p>
</template>
