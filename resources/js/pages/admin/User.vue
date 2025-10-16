<script setup lang="ts">
import Table from '@/components/Admin/Table.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
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
import { Separator } from '@/components/ui/separator';
import { TableCell, TableRow } from '@/components/ui/table';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';
import UserRole from '@/components/UserRole.vue';
import { getPaginationData } from '@/composables/usePagination';
import AdminLayout from '@/layouts/AdminLayout.vue';
import type { Pagination as PaginationType, User } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { CheckCircle, ChevronDown, ChevronUp, Ellipsis, Eye, Trash } from 'lucide-vue-next';
import { computed } from 'vue';

const props = defineProps<{
    users: PaginationType & { data: User[] };
    roles: string[];
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

const getRoleIndex = computed(() => (currentRole: string) => props.roles.indexOf(currentRole));

const getRolesAbove = computed(() => (currentRole: string) => {
    const ind = getRoleIndex.value(currentRole);

    const roles = [...props.roles].splice(0, ind);

    return roles;
});

const getRolesBelow = computed(() => (currentRole: string) => {
    const ind = getRoleIndex.value(currentRole);

    const roles = [...props.roles].splice(ind);
    roles.shift();

    return roles;
});
</script>

<template>
    <Head title="Users" />

    <AdminLayout>
        <div class="main-container flex flex-col gap-4">
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
            <Table :reload-only :headers="tableHeaders">
                <TableRow v-for="user in users.data" :key="user.username">
                    <TableCell>{{ user.name }}</TableCell>
                    <TableCell>{{ user.username }}</TableCell>
                    <TableCell>
                        <UserRole :role="user.role" class="text-center" />
                    </TableCell>
                    <TableCell>{{ user.email }}</TableCell>
                    <TableCell class="[&>*]:mx-auto">
                        <TooltipProvider v-if="user.email_verified_at">
                            <Tooltip>
                                <TooltipTrigger as-child>
                                    <CheckCircle class="size-4" />
                                </TooltipTrigger>
                                <TooltipContent class="max-w-[75vw]">
                                    <p>{{ user.email_verified_at }}</p>
                                </TooltipContent>
                            </Tooltip>
                        </TooltipProvider>
                    </TableCell>
                    <TableCell class="[&>*]:mx-auto">
                        <CheckCircle class="size-4" v-if="user.avatar" />
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
                                <DropdownMenuItem as-child>
                                    <Link
                                        :href="route('admin.users.destroy', { user: user.username })"
                                        as="button"
                                        method="delete"
                                        class="w-full cursor-pointer"
                                    >
                                        <Trash class="size-4" />
                                        Delete
                                    </Link>
                                </DropdownMenuItem>
                                <DropdownMenuSeparator v-if="getRolesAbove(user.role).length > 0" />
                                <!-- TODO: Conditional rendering basing on current role -->
                                <DropdownMenuItem as-child v-for="role in getRolesAbove(user.role)" :key="role">
                                    <Link
                                        :href="route('admin.users.role', { user: user.username, role: role })"
                                        as="button"
                                        method="patch"
                                        class="w-full cursor-pointer"
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
                                    >
                                        <ChevronDown class="size-4" />
                                        Demote to {{ role.replaceAll('_', ' ') }}
                                    </Link>
                                </DropdownMenuItem>
                                <!-- END TODO -->
                            </DropdownMenuContent>
                        </DropdownMenu>
                    </TableCell>
                </TableRow>
            </Table>

            <Pagination :pagination="getPaginationData(users)" :reload-only />
        </div>
    </AdminLayout>
</template>
