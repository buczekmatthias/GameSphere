<script setup lang="ts">
import GameSkeleton from '@/components/fallbacks/GameSkeleton.vue';
import Game from '@/components/Game.vue';
import MainContainer from '@/components/MainContainer.vue';
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
        <MainContainer>
            <Deferred data="games">
                <template #fallback>
                    <GameSkeleton />
                </template>

                <!-- TODO: Different filters -->
                <div class="games-grid mx-auto max-w-5xl">
                    <Game v-for="game in games" :key="game.title" :game />
                </div>
            </Deferred>
        </MainContainer>
    </AppLayout>
</template>
