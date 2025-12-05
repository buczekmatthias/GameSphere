<script setup lang="ts">
import Genre from '@/components/Genre.vue';
import MainContainer from '@/components/MainContainer.vue';
import PaginatedContent from '@/components/PaginatedContent.vue';
import { Skeleton } from '@/components/ui/skeleton';
import { getPaginationData } from '@/composables/usePagination';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, Genre as GenreType, Pagination } from '@/types';
import { Deferred, Head } from '@inertiajs/vue3';

defineProps<{
    genres?: Pagination & { data: GenreType[] };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Genres',
        href: route('genres.index'),
    },
];
</script>

<template>
    <Head title="Genres" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <MainContainer class="mx-auto flex max-w-2xl flex-col gap-4">
            <Deferred data="genres">
                <template #fallback>
                    <Skeleton v-for="i in 15" :key="i" class="h-24 w-full" />
                </template>

                <PaginatedContent :pagination="getPaginationData(genres!)">
                    <Genre v-for="genre in genres!.data" :key="genre.name" :genre="genre" />
                </PaginatedContent>
            </Deferred>
        </MainContainer>
    </AppLayout>
</template>
