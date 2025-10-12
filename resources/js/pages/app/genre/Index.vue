<script setup lang="ts">
import Genre from '@/components/Genre.vue';
import Pagination from '@/components/Pagination.vue';
import { Skeleton } from '@/components/ui/skeleton';
import { getPaginationData } from '@/composables/usePagination';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, Genre as GenreType, Pagination as PaginationType } from '@/types';
import { Deferred, Head } from '@inertiajs/vue3';

defineProps<{
    genres?: PaginationType & { data: GenreType[] };
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
        <div class="main-container flex flex-col gap-4">
            <Deferred data="genres">
                <template #fallback>
                    <Skeleton v-for="i in 15" :key="i" class="h-24 w-full" />
                </template>

                <Genre v-for="genre in genres!.data" :key="genre.name" :genre="genre" />
                <Pagination :pagination="getPaginationData(genres!)" />
            </Deferred>
        </div>
    </AppLayout>
</template>
