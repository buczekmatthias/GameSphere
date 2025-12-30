<script setup lang="ts">
import CanInteract from '@/components/app/CanInteract.vue';
import Discussion from '@/components/app/Discussion.vue';
import Game from '@/components/app/Game.vue';
import MainContainer from '@/components/app/MainContainer.vue';
import PaginatedContent from '@/components/app/PaginatedContent.vue';
import Heading from '@/components/Heading.vue';
import { getPaginationData } from '@/composables/usePagination';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, Discussion as DiscussionType, Game as GameType, Genre, Pagination } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Gamepad2, MessageCircle, Star } from 'lucide-vue-next';

const props = defineProps<{
    genre: Genre & {
        games: Pagination & {
            data: GameType[];
        };
        discussions: Pagination & {
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
        <MainContainer class="mx-auto flex max-w-5xl flex-col gap-2">
            <div class="flex items-center justify-between gap-3">
                <Heading :title="genre.name" :description="`${genre.games.meta.total} game(s) | ${genre.discussions.meta.total} discussion(s)`" />
                <CanInteract>
                    <Link :href="route('genres.favorite', { genre: genre.slug })" method="post" :only="['genre']">
                        <Star class="size-6" :class="genre.is_user_favorite ? 'text-amber-300' : 'text-slate-200'" />
                    </Link>
                </CanInteract>
            </div>

            <div class="flex flex-col gap-4 border-y py-4">
                <div class="flex items-center gap-3">
                    <Gamepad2 class="mt-1 size-7" />
                    <p class="text-2xl font-semibold">Games</p>
                </div>
                <PaginatedContent :pagination="getPaginationData(genre.games)" page-name="games" pagination-position="bottom">
                    <div class="games-grid">
                        <Game class="w-full shrink-0" v-for="game in genre.games.data" :key="game.title" :game="game" />
                    </div>
                </PaginatedContent>
            </div>

            <div class="flex flex-col gap-4">
                <div class="flex items-center gap-3">
                    <MessageCircle class="mt-1 size-7" />
                    <p class="text-2xl font-semibold">Discussions</p>
                </div>
                <PaginatedContent :pagination="getPaginationData(genre.discussions)" page-name="discussions" pagination-position="bottom">
                    <div class="flex flex-col gap-2">
                        <Discussion v-for="discussion in genre.discussions.data" :key="discussion.title" :discussion="discussion" />
                    </div>
                </PaginatedContent>
            </div>
        </MainContainer>
    </AppLayout>
</template>
