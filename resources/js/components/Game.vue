<script setup lang="ts">
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Game } from '@/types';
import { Link } from '@inertiajs/vue3';

defineProps<{
    game: Game;
}>();
</script>

<template>
    <Link :href="route('games.show', { game: game.slug })" class="group flex flex-col gap-2">
        <Avatar class="group-hover:border-primary h-80 w-full overflow-hidden rounded-lg border-2 border-solid border-transparent duration-150">
            <AvatarImage :src="game.thumbnail" :alt="game.title" class="object-cover" />
            <AvatarFallback class="rounded-lg text-black dark:text-white" />
        </Avatar>

        <p class="truncate">{{ game.title }}</p>
    </Link>
</template>
