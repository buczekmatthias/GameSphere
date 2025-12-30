<script setup lang="ts">
import LazyAvatar from '@/components/app/LazyAvatar.vue';
import { Game } from '@/types';
import { Link } from '@inertiajs/vue3';
import { CircleCheckBig, Coins, Gamepad2, Heart, ListCheck, LucideIcon, Star } from 'lucide-vue-next';
import { capitalize } from 'vue';

defineProps<{
    game: Game;
}>();

const listTypeIcon: { [key: string]: LucideIcon } = {
    wishlist: ListCheck,
    owned: Coins,
    playing: Gamepad2,
    completed: CircleCheckBig,
    favorite: Heart,
};
</script>

<template>
    <Link :href="route('games.show', { game: game.slug })" class="group flex flex-col" as="button">
        <div class="relative">
            <LazyAvatar
                :src="game.thumbnail"
                :alt="game.title"
                class="group-hover:border-primary h-80 w-full border-2 border-solid border-transparent duration-300"
            />
            <div class="absolute top-2 left-2 flex flex-wrap gap-1.5">
                <div class="game-floating-label" v-if="game.list" @click.prevent>
                    <component :is="listTypeIcon[game.list]" class="size-4" />
                    <p>{{ capitalize(game.list).replace('_', ' ') }}</p>
                </div>
                <div class="game-floating-label">
                    <Star class="mt-0.5 size-5" />
                    <p>{{ game.score }}</p>
                </div>
            </div>
        </div>

        <p class="self-start truncate">{{ game.title }}</p>
    </Link>
</template>
