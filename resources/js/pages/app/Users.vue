<script setup lang="ts">
import Pagination from '@/components/Pagination.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import UserInfo from '@/components/UserInfo.vue';
import UserRole from '@/components/UserRole.vue';
import { getPaginationData } from '@/composables/usePagination';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, Pagination as PaginationType, SharedData, User, Ziggy } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

interface SearchData {
    per_page?: string | number;
    contains?: string;
}

interface ZiggyWithGamesQuery extends Ziggy {
    query: SearchData;
}

defineProps<{
    users: PaginationType & { data: User[] };
    per_page: string | number;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Users',
        href: route('users.index'),
    },
];

const ziggy = computed(() => usePage<SharedData>().props.ziggy as ZiggyWithGamesQuery);

const contains = ref<string>(ziggy.value.query.contains || '');

const searchEntries = () => {
    const data: Partial<SearchData> = {};

    if (ziggy.value.query.per_page) data.per_page = ziggy.value.query.per_page;

    if (contains.value) data.contains = contains.value;

    router.get(route(ziggy.value.current), data);
};
</script>

<template>
    <Head title="Users" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="main-container flex flex-col gap-4">
            <div class="grid grid-cols-[1fr_auto] gap-2">
                <Input type="text" v-model="contains" placeholder="Search by name or username..." />
                <Button type="submit" @click="searchEntries">Search</Button>
            </div>
            <template v-if="users.data.length > 0">
                <Link
                    :href="route('user.profile', { user: user.username })"
                    v-for="user in users.data"
                    :key="user.username"
                    class="mx-auto flex w-full max-w-xl items-center justify-between gap-4 rounded-md border px-2.5 py-3.5"
                >
                    <UserInfo show-username :user="user" />
                    <UserRole :role="user.role" class="text-sm" :show-user-role-tag="false" />
                </Link>
                <Pagination :customizable-per-page="true" :pagination="getPaginationData(users)" />
            </template>
            <template v-else>
                <p>Nothing to display</p>
            </template>
        </div>
    </AppLayout>
</template>
