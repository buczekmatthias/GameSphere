<script setup lang="ts">
import Discussion from '@/components/Discussion.vue';
import Game from '@/components/Game.vue';
import Pagination from '@/components/Pagination.vue';
import { getPaginationData } from '@/composables/usePagination';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, Discussion as DiscussionType, Game as GameType, Genre, Pagination as PaginationType } from '@/types';
import { Head } from '@inertiajs/vue3';

const props = defineProps<{
    genre: Genre & {
        games: PaginationType & {
            data: GameType[];
        };
        discussions: PaginationType & {
            data: DiscussionType[];
        };
    };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Genres',
        href: route('genres.index'),
    },
    {
        title: props.genre.shortName,
        href: route('genres.show', { genre: props.genre.name }),
    },
];
</script>

<template>
    <Head :title="genre.name" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="main-container flex flex-col gap-10">
            <div class="flex flex-col gap-4">
                <p class="text-3xl font-semibold">Games</p>
                <div class="mb-4 flex flex-col gap-2">
                    <Game class="w-full shrink-0" v-for="game in genre.games.data" :key="game.title" :game="game" />
                </div>
                <Pagination :pagination="getPaginationData(genre.games)" page-name="games" />
            </div>

            <div class="flex flex-col gap-4">
                <p class="text-3xl font-semibold">Discussions</p>
                <div class="flex flex-col gap-2">
                    <Discussion v-for="discussion in genre.discussions.data" :key="discussion.title" :discussion="discussion" />
                </div>
                <Pagination :pagination="getPaginationData(genre.discussions)" page-name="discussions" />
            </div>
        </div>
    </AppLayout>
</template>
