<script setup lang="ts">
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';
import { Game } from '@/types';
import { Link } from '@inertiajs/vue3';
import { CalendarCheck, CircleCheckBig, Coins, Gamepad2, Heart, ListCheck } from 'lucide-vue-next';
import { capitalize } from 'vue';

defineProps<{
    game: Game;
}>();

const listTypeIcon: any = {
    wishlist: ListCheck,
    owned: Coins,
    playing: Gamepad2,
    completed: CircleCheckBig,
    favorite: Heart,
    upcoming_releases: CalendarCheck,
};
</script>

<template>
    <Link :href="route('games.show', { game: game.slug })" class="group flex flex-col" as="button">
        <div class="relative">
            <Avatar class="group-hover:border-primary h-80 w-full overflow-hidden rounded-lg border-2 border-solid border-transparent duration-150">
                <AvatarImage :src="game.thumbnail" :alt="game.title" class="object-cover" />
                <AvatarFallback class="rounded-lg text-black dark:text-white" />
            </Avatar>
            <TooltipProvider v-if="game.list">
                <Tooltip>
                    <TooltipTrigger as-child class="absolute top-2 right-2">
                        <Button>
                            <component :is="listTypeIcon[game.list]" class="size-[1.3rem]" />
                        </Button>
                    </TooltipTrigger>
                    <TooltipContent class="max-w-[75vw]">
                        <p>{{ capitalize(game.list).replace('_', ' ') }}</p>
                    </TooltipContent>
                </Tooltip>
            </TooltipProvider>
        </div>

        <p class="self-start truncate">{{ game.title }}</p>
    </Link>
</template>
