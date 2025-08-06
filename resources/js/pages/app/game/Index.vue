<script setup lang="ts">
import DatePicker from '@/components/DatePicker.vue';
import Game from '@/components/Game.vue';
import Pagination from '@/components/Pagination.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { getPaginationData } from '@/composables/usePagination';
import { transformDate } from '@/composables/useTransformDatePicker';
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem, Game as GameType, Pagination as PaginationType, Ziggy } from '@/types';
import { Head, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Games',
        href: route('games.index'),
    },
];

defineProps<{
    games: PaginationType & { data: GameType[] };
}>();

const ziggy = computed(() => usePage().props.ziggy as Ziggy & { query: { per_page?: string; title?: string; released_at?: string } });

const title = ref('');
const released_at = ref('');

const searchEntires = () => {
    const data: { per_page?: string; title?: string; released_at?: string } = {};

    if (ziggy.value.query.per_page) data.per_page = ziggy.value.query.per_page;

    if (title.value) data.title = title.value;

    if (released_at.value) data.released_at = transformDate(released_at.value);

    router.get(route(ziggy.value.current), data);
};
</script>

<template>
    <Head title="Games" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="main-container grid grid-cols-2 gap-4 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5">
            <div class="col-span-full grid grid-cols-[3fr_1fr] gap-2">
                <Input type="text" v-model="title" class="col-span-full" placeholder="Game title" />
                <DatePicker v-model="released_at" />
                <Button type="submit" @click="searchEntires"> Search </Button>
            </div>
            <template v-if="ziggy.query.title || ziggy.query.released_at">
                <p class="col-span-full text-lg">
                    Results for:
                    <template v-if="ziggy.query.title">"{{ ziggy.query.title }}"</template>
                    <template v-if="ziggy.query.title && ziggy.query.released_at"> & </template>
                    <template v-if="ziggy.query.released_at">{{ ziggy.query.released_at }}</template>
                </p>
            </template>
            <template v-if="games.data.length > 0">
                <Game v-for="game in games.data" :key="game.title" :game />
                <Pagination :customizable-per-page="true" :pagination="getPaginationData(games)" />
            </template>
            <template v-else>
                <p>Nothing to display</p>
            </template>
        </div>
    </AppLayout>
</template>
