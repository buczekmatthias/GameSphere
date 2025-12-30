<script setup lang="ts">
import Table from '@/components/admin/Table.vue';
import FormActionTap from '@/components/app/FormActionTap.vue';
import MainContainer from '@/components/app/MainContainer.vue';
import PaginatedContent from '@/components/app/PaginatedContent.vue';
import Preview from '@/components/app/Preview.vue';
import UserRole from '@/components/app/user/UserRole.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Separator } from '@/components/ui/separator';
import { TableCell, TableRow } from '@/components/ui/table';
import { getPaginationData } from '@/composables/usePagination';
import AdminLayout from '@/layouts/AdminLayout.vue';
import type { Pagination, User } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { ChevronDown, ChevronUp, Ellipsis, Eye, Trash } from 'lucide-vue-next';

const props = defineProps<{
    users: Pagination & { data: User[] };
    roles: string[];
    roles_user_can_manage: string[];
    game_creator_requests_count: number;
}>();

const tableHeaders = [
    { label: 'Name', is_sortable: true, column: 'name' },
    { label: 'Username', is_sortable: true, column: 'username' },
    { label: 'Role', is_sortable: true, column: 'role' },
    { label: 'E-mail', is_sortable: true, column: 'email' },
    { label: 'Verified', is_sortable: true, column: 'verified' },
    { label: 'Avatar', is_sortable: true, column: 'avatar' },
    { label: 'Created at', is_sortable: true, column: 'created_at' },
];

const reloadOnly: string[] = ['users', 'game_creator_requests_count'];

const getRoleIndex = (currentRole: string) => props.roles.indexOf(currentRole);

const getRolesAbove = (currentRole: string) => {
    const ind = getRoleIndex(currentRole);

    const roles = [...props.roles].splice(0, ind);

    return roles.filter((r) => props.roles_user_can_manage.includes(r));
};

const getRolesBelow = (currentRole: string) => {
    const ind = getRoleIndex(currentRole);

    const roles = [...props.roles].splice(ind);
    roles.shift();

    return roles.filter((r) => props.roles_user_can_manage.includes(r));
};

const canCurrentUserManageUser = (user: User) => props.roles_user_can_manage.includes(user.role);
</script>

<template>
    <Head title="Users" />

    <AdminLayout>
        <MainContainer class="flex flex-col gap-4">
            <div class="flex items-start space-y-1 max-md:flex-col md:items-center md:justify-between">
                <HeadingSmall
                    title="Game creator join requests"
                    :description="`There are currently ${game_creator_requests_count} request(s) to become game creator`"
                />

                <Button as-child class="max-md:mt-4">
                    <Link :href="route('admin.game.creator.join')">View requests</Link>
                </Button>
            </div>
            <Separator />
            <PaginatedContent pagination-position="bottom" :pagination="getPaginationData(users)" :reload-only>
                <Table :reload-only :headers="tableHeaders">
                    <TableRow v-for="user in users.data" :key="user.username">
                        <TableCell>{{ user.name }}</TableCell>
                        <TableCell>{{ user.username }}</TableCell>
                        <TableCell>
                            <UserRole :role="user.role" class="text-center" />
                        </TableCell>
                        <TableCell>{{ user.email }}</TableCell>
                        <TableCell>
                            <template v-if="user.email_verified_at">
                                {{ user.email_verified_at }}
                            </template>
                        </TableCell>
                        <TableCell class="text-center">
                            <Preview :item="{ filename: `${user.username}'s avatar`, path: user.avatar, type: 'image' }" v-if="user.avatar">
                                <FormActionTap>View</FormActionTap>
                            </Preview>
                        </TableCell>
                        <TableCell>{{ user.created_at }}</TableCell>
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
                                        <Link :href="route('user.profile', { user: user.username })" as="button" class="w-full cursor-pointer">
                                            <Eye class="size-4" />
                                            View profile
                                        </Link>
                                    </DropdownMenuItem>
                                    <template v-if="canCurrentUserManageUser(user)">
                                        <DropdownMenuItem as-child>
                                            <Link
                                                :href="route('admin.users.destroy', { user: user.username })"
                                                as="button"
                                                method="delete"
                                                class="w-full cursor-pointer"
                                                preserve-scroll
                                            >
                                                <Trash class="size-4" />
                                                Delete
                                            </Link>
                                        </DropdownMenuItem>
                                        <DropdownMenuItem as-child>
                                            <Link
                                                :href="route('admin.users.destroy', { user: user.username, with_relations: true })"
                                                as="button"
                                                method="delete"
                                                class="w-full cursor-pointer"
                                                preserve-scroll
                                            >
                                                <Trash class="size-4" />
                                                Delete with relations
                                            </Link>
                                        </DropdownMenuItem>
                                        <DropdownMenuSeparator v-if="getRolesAbove(user.role).length > 0" />
                                        <DropdownMenuItem as-child v-for="role in getRolesAbove(user.role)" :key="role">
                                            <Link
                                                :href="route('admin.users.role', { user: user.username, role: role })"
                                                as="button"
                                                method="patch"
                                                class="w-full cursor-pointer"
                                                preserve-scroll
                                            >
                                                <ChevronUp class="size-4" />
                                                Promote to {{ role.replaceAll('_', ' ') }}
                                            </Link>
                                        </DropdownMenuItem>
                                        <DropdownMenuSeparator v-if="getRolesBelow(user.role).length > 0" />
                                        <DropdownMenuItem as-child v-for="role in getRolesBelow(user.role)" :key="role">
                                            <Link
                                                :href="route('admin.users.role', { user: user.username, role: role })"
                                                as="button"
                                                method="patch"
                                                class="w-full cursor-pointer"
                                                preserve-scroll
                                            >
                                                <ChevronDown class="size-4" />
                                                Demote to {{ role.replaceAll('_', ' ') }}
                                            </Link>
                                        </DropdownMenuItem>
                                    </template>
                                </DropdownMenuContent>
                            </DropdownMenu>
                        </TableCell>
                    </TableRow>
                </Table>
            </PaginatedContent>
        </MainContainer>
    </AdminLayout>
</template>
