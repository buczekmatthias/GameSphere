<script setup lang="ts">
import GameSkeleton from '@/components/fallbacks/GameSkeleton.vue';
import Game from '@/components/Game.vue';
import GamesListFilter, { FilterData } from '@/components/GamesListFilter.vue';
import Pagination from '@/components/Pagination.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Separator } from '@/components/ui/separator';
import { getPaginationData } from '@/composables/usePagination';
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem, Game as GameType, Pagination as PaginationType, SharedData, Ziggy } from '@/types';
import { Deferred, Head, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

interface SearchData {
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
    games?: PaginationType & { data: GameType[] };
    per_page: number | string;
    genres?: string[];
}>();

const ziggy = computed(() => usePage<SharedData>().props.ziggy as ZiggyWithGamesQuery);

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

const queriesCount = computed((): number => Object.keys(ziggy.value.query).filter((key) => key !== 'per_page').length);
</script>

<template>
    <Head title="Games" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="main-container grid grid-cols-2 gap-4 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5">
            <div class="ml:grid-cols-[1fr_auto_auto] col-span-full grid grid-cols-2 gap-2">
                <Input type="text" v-model="title" class="max-ml:col-span-full" placeholder="Game title" @keyup.enter="searchEntries" />
                <GamesListFilter @update-search-conditions="searchConditions = $event" :ziggy :genres />
                <Button type="submit" @click="searchEntries"> Search </Button>
            </div>
            <Deferred data="games">
                <template #fallback>
                    <GameSkeleton class="col-span-full" :has-queries="isQueried" />
                </template>

                <template v-if="isQueried">
                    <div class="col-span-full flex flex-col gap-2">
                        <p class="text-2xl">{{ games?.meta.total }} {{ games?.meta.total === 1 ? 'result' : 'results' }}</p>
                        <div class="text-muted-foreground flex flex-wrap items-center gap-3">
                            <p v-if="ziggy.query.title">Contains "{{ ziggy.query.title }}"</p>
                            <Separator class="max-h-7" orientation="vertical" v-if="ziggy.query.title && queriesCount > 1" />
                            <p v-if="ziggy.query.genre">Genre: "{{ ziggy.query.genre }}"</p>
                            <Separator
                                class="max-h-7"
                                orientation="vertical"
                                v-if="(ziggy.query.released_after || ziggy.query.released_before) && ziggy.query.genre"
                            />
                            <template v-if="ziggy.query.released_after || ziggy.query.released_before">
                                <p v-if="ziggy.query.released_after && !ziggy.query.released_before">After {{ ziggy.query.released_after }}</p>
                                <p v-else-if="!ziggy.query.released_after && ziggy.query.released_before">Before {{ ziggy.query.released_before }}</p>
                                <p v-else>Between {{ ziggy.query.released_after }} and {{ ziggy.query.released_before }}</p>
                            </template>
                        </div>
                    </div>
                </template>
                <template v-if="games!.data.length > 0">
                    <Game v-for="game in games!.data" :key="game.title" :game />
                    <Pagination :customizable-per-page="true" :pagination="getPaginationData(games!)" :reload-only />
                </template>
                <template v-else>
                    <p class="col-span-full border-t pt-4">Nothing to display</p>
                </template>
            </Deferred>
        </div>
        ``
    </AppLayout>
</template>
