<script setup lang="ts">
import GameSkeleton from '@/components/fallbacks/GameSkeleton.vue';
import Game from '@/components/Game.vue';
import GamesListFilter, { FilterData } from '@/components/GamesListFilter.vue';
import MainContainer from '@/components/MainContainer.vue';
import PaginatedContent from '@/components/PaginatedContent.vue';
import SearchHeaderText from '@/components/Partials/Game/Index/SearchHeaderText.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { getPaginationData } from '@/composables/usePagination';
import { useZiggy } from '@/composables/useZiggy';
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem, Game as GameType, Pagination, Ziggy } from '@/types';
import { Deferred, Head, router } from '@inertiajs/vue3';
import { computed, ComputedRef, ref } from 'vue';

export interface SearchData {
    per_page?: string | number;
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
    per_page: number | string;
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

    if (ziggy.value.query.per_page) data.per_page = ziggy.value.query.per_page;

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
                <template v-if="games!.data.length > 0">
                    <PaginatedContent
                        container-class="games-grid"
                        :customizable-per-page="true"
                        :pagination="getPaginationData(games!)"
                        :reload-only
                        pagination-position="bottom"
                    >
                        <Game v-for="game in games!.data" :key="game.title" :game />
                    </PaginatedContent>
                </template>
                <template v-else>
                    <p class="col-span-full border-t pt-4">Nothing to display</p>
                </template>
            </Deferred>
        </MainContainer>
    </AppLayout>
</template>
