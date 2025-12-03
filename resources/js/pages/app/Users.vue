<script setup lang="ts">
import UsersIndexSkeleton from '@/components/fallbacks/UsersIndexSkeleton.vue';
import MainContainer from '@/components/MainContainer.vue';
import Pagination from '@/components/Pagination.vue';
import RoleTabs from '@/components/Partials/User/RoleTabs.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import UserInfo from '@/components/UserInfo.vue';
import UserRole from '@/components/UserRole.vue';
import { getPaginationData } from '@/composables/usePagination';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, Pagination as PaginationType, SharedData, User, Ziggy } from '@/types';
import { Deferred, Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

interface SearchData {
    per_page?: string | number;
    contains?: string;
    role?: string;
}

export interface ZiggyWithGamesQuery extends Ziggy {
    query: SearchData;
}

defineProps<{
    users?: PaginationType & { data: User[] };
    roles: string[];
    users_count: number | string;
    per_page: string | number;
}>();

const ziggy = computed(() => usePage<SharedData>().props.ziggy as ZiggyWithGamesQuery);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Users',
        href: route(ziggy.value.current),
    },
];

const contains = ref<string>(ziggy.value.query.contains || '');
const role = ref<string>(ziggy.value.query.role || '');

const searchEntries = () => {
    const data: Partial<SearchData> = {};

    if (ziggy.value.query.per_page) data.per_page = ziggy.value.query.per_page;

    if (contains.value) data.contains = contains.value;

    if (role.value) data.role = role.value;

    router.get(route(ziggy.value.current), data, { only: reloadOnly });
};

const reloadOnly = ['users', 'ziggy'];
</script>

<template>
    <Head title="Users" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <MainContainer class="mx-auto flex max-w-3xl flex-col gap-4">
            <Deferred data="users">
                <template #fallback>
                    <UsersIndexSkeleton />
                </template>

                <template v-if="users!.data.length > 0">
                    <div class="grid grid-cols-[1fr_auto] gap-2">
                        <Input type="text" v-model="contains" placeholder="Search by name or username..." @keyup.enter="searchEntries" />
                        <Button type="submit" @click="searchEntries">Search</Button>
                    </div>
                    <RoleTabs :roles :ziggy :users_count :total="users!.meta.total" :reloadOnly />
                    <Link
                        :href="route('user.profile', { user: user.username })"
                        v-for="user in users!.data"
                        :key="user.username"
                        class="flex w-full items-center justify-between gap-4 rounded-md border px-2.5 py-3.5"
                    >
                        <UserInfo show-username :user="user" />
                        <UserRole :role="user.role" class="text-sm" :show-user-role-tag="false" />
                    </Link>
                    <Pagination class="mt-3" :customizable-per-page="true" :pagination="getPaginationData(users!)" :reload-only />
                </template>
                <template v-else>
                    <p class="text-lg">No users to display</p>
                </template>
            </Deferred>
        </MainContainer>
    </AppLayout>
</template>
