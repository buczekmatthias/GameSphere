<script setup lang="ts">
import TextLink from '@/components/TextLink.vue';
import { Card } from '@/components/ui/card';
import { Genre } from '@/types';
import { Gamepad2, MessageCircle } from 'lucide-vue-next';

defineProps<{
    genre: Genre;
}>();
</script>

<template>
    <Card class="flex flex-col gap-4">
        <p class="truncate text-2xl">{{ genre.name }}</p>
        <div class="flex gap-4">
            <div class="flex items-center gap-1 text-sm">
                <Gamepad2 class="h-5" />
                <p>{{ genre.games_count }} game(s)</p>
            </div>
            <div class="flex items-center gap-1 text-sm">
                <MessageCircle class="h-5" />
                <p>{{ genre.discussions_count }} discussion(s)</p>
            </div>
        </div>
        <TextLink :href="route('genres.show', { genre: genre.slug })" class="self-start">View genre</TextLink>
    </Card>
</template>
