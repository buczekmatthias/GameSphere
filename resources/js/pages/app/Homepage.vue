<script setup lang="ts">
import Game from '@/components/app/Game.vue';
import MainContainer from '@/components/app/MainContainer.vue';
import TabNavigation, { TabNavigationItem } from '@/components/app/TabNavigation.vue';
import GameSkeleton from '@/components/fallbacks/GameSkeleton.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, Game as GameType } from '@/types';
import { Deferred, Head } from '@inertiajs/vue3';
import { capitalize } from 'vue';

const props = defineProps<{
    games?: GameType[];
    activeType: string;
    types: TabNavigationItem[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: capitalize(props.activeType.replaceAll('_', ' ')),
        href: route('home', { type: props.activeType }),
    },
];
</script>

<template>
    <Head title="Homepage" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <MainContainer>
            <Deferred data="games">
                <template #fallback>
                    <GameSkeleton />
                </template>

                <div class="games-grid mx-auto max-w-5xl">
                    <TabNavigation :tabs="types" :active-tab="activeType" :reload-only="['activeType', 'games']" class="col-span-full" />
                    <Game v-for="game in games" :key="game.title" :game />
                </div>
            </Deferred>
        </MainContainer>
    </AppLayout>
</template>
