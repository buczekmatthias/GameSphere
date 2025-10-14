<script setup lang="ts">
import Discussion from '@/components/Discussion.vue';
import Game from '@/components/Game.vue';
import Pagination from '@/components/Pagination.vue';
import { Button } from '@/components/ui/button';
import { canInteract } from '@/composables/useCanInteract';
import { getPaginationData } from '@/composables/usePagination';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, Discussion as DiscussionType, Game as GameType, Genre, Pagination as PaginationType } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Gamepad2, MessageCircle, Minus, Plus } from 'lucide-vue-next';

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
            <Button as-child :variant="genre.is_user_favorite ? 'destructive' : 'outline'" v-if="canInteract()">
                <Link :href="route('genres.favorite', { genre: genre.slug })" method="post" :only="['genre']">
                    <template v-if="genre.is_user_favorite">
                        <Minus class="mt-0.5 size-5" />
                        <span>Remove from favorites</span>
                    </template>
                    <template v-else>
                        <Plus class="mt-0.5 size-5" />
                        <span>Add to favorites</span>
                    </template>
                </Link>
            </Button>

            <div class="flex flex-col gap-4">
                <div class="flex items-center gap-2">
                    <Gamepad2 class="mt-1 size-7" />
                    <p class="text-2xl font-semibold">Games ({{ genre.games.data.length }})</p>
                </div>
                <div class="grid grid-cols-2 gap-4 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5">
                    <Game class="w-full shrink-0" v-for="game in genre.games.data" :key="game.title" :game="game" />
                </div>
                <Pagination :pagination="getPaginationData(genre.games)" page-name="games" />
            </div>

            <div class="flex flex-col gap-4">
                <div class="flex items-center gap-2">
                    <MessageCircle class="mt-1 size-7" />
                    <p class="text-2xl font-semibold">Discussions ({{ genre.discussions.data.length }})</p>
                </div>
                <div class="flex flex-col gap-2">
                    <Discussion v-for="discussion in genre.discussions.data" :key="discussion.title" :discussion="discussion" />
                </div>
                <Pagination :pagination="getPaginationData(genre.discussions)" page-name="discussions" />
            </div>
        </div>
    </AppLayout>
</template>
