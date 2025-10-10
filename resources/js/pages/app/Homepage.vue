<script setup lang="ts">
import GameSkeleton from '@/components/fallbacks/GameSkeleton.vue';
import Game from '@/components/Game.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, Game as GameType } from '@/types';
import { Deferred, Head } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [];

defineProps<{
    games?: GameType[];
}>();
</script>

<template>
    <Head title="Home" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="main-container">
            <Deferred data="games">
                <template #fallback>
                    <GameSkeleton />
                </template>

                <div class="grid grid-cols-2 gap-4 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5">
                    <Game v-for="game in games" :key="game.title" :game />
                </div>
            </Deferred>
        </div>
    </AppLayout>
</template>
