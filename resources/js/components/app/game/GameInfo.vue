<script setup lang="ts">
import MediaPreview from '@/components/MediaPreview.vue';
import TextLink from '@/components/TextLink.vue';
import { Game } from '@/types';
import { Blocks, Calendar, Star, User } from 'lucide-vue-next';

defineProps<{
    game: Game;
}>();
</script>

<template>
    <div class="max-ml:col-span-full ml:col-start-2 ml:row-end-1 flex flex-col gap-4">
        <h1 class="text-3xl">{{ game.title }}</h1>
        <div class="flex divide-x">
            <div class="flex items-center gap-2.5 pr-3">
                <Star class="mt-0.5 size-5" />
                <p>{{ game.score }}</p>
            </div>
            <p class="pl-3">{{ game.reviews_count }} review(s)</p>
        </div>
        <p class="text-sm leading-[165%]">{{ game.description }}</p>
        <div class="flex flex-wrap gap-4 lg:gap-6">
            <div class="flex items-center gap-2">
                <Blocks class="mt-0.5 size-4" />
                <TextLink :href="route('genres.show', { genre: game.genre.slug })">{{ game.genre.name }}</TextLink>
            </div>
            <div class="flex items-center gap-2">
                <Calendar class="mt-0.5 size-4" />
                <TextLink :href="route('games.index', { released_after: game.released_at, released_before: game.released_at })">
                    {{ game.released_at }}
                </TextLink>
            </div>
            <div class="flex items-center gap-2 max-lg:col-span-full">
                <User class="mt-0.5 size-4" />
                <TextLink :href="route('user.profile', { user: game.creator.username })">{{ game.creator.name }}</TextLink>
            </div>
        </div>

        <MediaPreview :media="game.media" v-if="game.media.length > 0" />
    </div>
</template>
