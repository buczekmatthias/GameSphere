<script setup lang="ts">
import ContentWithFallback from '@/components/app/ContentWithFallback.vue';
import Game from '@/components/app/Game.vue';
import GamesListFilter, { FilterData } from '@/components/app/game/GamesListFilter.vue';
import SearchHeaderText from '@/components/app/game/SearchHeaderText.vue';
import MainContainer from '@/components/app/MainContainer.vue';
import PaginatedContent from '@/components/app/PaginatedContent.vue';
import GameSkeleton from '@/components/fallbacks/GameSkeleton.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { useZiggy } from '@/composables/useZiggy';
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem, Game as GameType, Pagination, Ziggy } from '@/types';
import { Deferred, Head, router } from '@inertiajs/vue3';
import { computed, ComputedRef, ref } from 'vue';

export interface SearchData {
    title?: string;
    released_after?: string;
    released_before?: string;
    genre?: string;
}

export interface ZiggyWithGamesQuery extends Ziggy {
    query: SearchData;
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Games',
        href: route('games.index'),
    },
];

defineProps<{
    games?: Pagination & { data: GameType[] };
    genres?: string[];
}>();

const ziggy: ComputedRef<ZiggyWithGamesQuery> = useZiggy();

const title = ref<string>(ziggy.value.query.title || '');
const searchConditions = ref<FilterData>({
    released_after: ziggy.value.query.released_after,
    released_before: ziggy.value.query.released_before,
    genre: ziggy.value.query.genre,
});

const searchEntries = () => {
    let data: Partial<SearchData> = {};

    if (title.value) data.title = title.value;

    data = {
        ...data,
        ...Object.entries(searchConditions.value)
            .filter(([, value]) => value !== null && value !== undefined && value !== '')
            .reduce((acc, [key, value]) => ({ ...acc, [key]: value }), {}),
    };

    router.get(route(ziggy.value.current), data, { only: reloadOnly });
};

const reloadOnly = ['games', 'ziggy'];

const isQueried = computed(
    (): boolean =>
        ziggy.value.query.title !== undefined ||
        ziggy.value.query.released_after !== undefined ||
        ziggy.value.query.released_before !== undefined ||
        ziggy.value.query.genre !== undefined,
);
</script>

<template>
    <Head title="Games" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <MainContainer class="mx-auto flex max-w-5xl flex-col gap-4">
            <div class="ml:grid-cols-[1fr_auto_auto] grid grid-cols-2 gap-2">
                <Input type="text" v-model="title" class="max-ml:col-span-full" placeholder="Game title" @keyup.enter="searchEntries" />
                <GamesListFilter @update-search-conditions="searchConditions = $event" :ziggy :genres />
                <Button type="submit" @click="searchEntries"> Search </Button>
            </div>
            <Deferred data="games">
                <template #fallback>
                    <GameSkeleton :has-queries="isQueried" />
                </template>

                <SearchHeaderText :total="games!.meta.total" :query="ziggy.query" v-if="isQueried" />
                <ContentWithFallback :has-value="games!.data.length > 0">
                    <PaginatedContent container-class="games-grid" :pagination="games!" :reload-only pagination-position="bottom">
                        <Game v-for="game in games!.data" :key="game.title" :game />
                    </PaginatedContent>
                </ContentWithFallback>
            </Deferred>
        </MainContainer>
    </AppLayout>
</template>
