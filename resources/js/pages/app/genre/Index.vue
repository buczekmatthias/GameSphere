<script setup lang="ts">
import Genre from '@/components/app/Genre.vue';
import MainContainer from '@/components/app/MainContainer.vue';
import PaginatedContent from '@/components/app/PaginatedContent.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Skeleton } from '@/components/ui/skeleton';
import { getPaginationData } from '@/composables/usePagination';
import { useZiggy } from '@/composables/useZiggy';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, Genre as GenreType, Pagination, Ziggy } from '@/types';
import { Deferred, Head, useForm } from '@inertiajs/vue3';
import { ComputedRef } from 'vue';

interface ZiggyWithGamesQuery extends Ziggy {
    query: {
        name?: string;
    };
}

defineProps<{
    genres?: Pagination & { data: GenreType[] };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Genres',
        href: route('genres.index'),
    },
];

const ziggy: ComputedRef<ZiggyWithGamesQuery> = useZiggy();

const searchForm = useForm({
    name: ziggy.value.query.name || '',
});
</script>

<template>
    <Head title="Genres" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <MainContainer class="mx-auto flex max-w-2xl flex-col gap-4">
            <Deferred data="genres">
                <template #fallback>
                    <div class="flex gap-2">
                        <Skeleton class="h-9 w-[calc(100%-5rem)]"></Skeleton>
                        <Skeleton class="h-9 w-20"></Skeleton>
                    </div>
                    <Skeleton v-for="i in 15" :key="i" class="h-24 w-full" />
                </template>

                <form @submit.prevent="searchForm.get(route(ziggy.current), { only: ['genres', 'ziggy'] })" class="grid grid-cols-[1fr_auto] gap-2">
                    <Input v-model="searchForm.name" placeholder="Search by name..." />
                    <Button>Search</Button>
                </form>
                <PaginatedContent :pagination="getPaginationData(genres!)" pagination-position="bottom">
                    <Genre v-for="genre in genres!.data" :key="genre.name" :genre="genre" />
                </PaginatedContent>
            </Deferred>
        </MainContainer>
    </AppLayout>
</template>
