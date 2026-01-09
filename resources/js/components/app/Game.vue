<script setup lang="ts">
import LazyAvatar from '@/components/app/LazyAvatar.vue';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';
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

const formatListName = (name: string): string => capitalize(name).replace('_', ' ');
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
                <div class="game-floating-label">
                    <Star class="mt-0.5 size-5" />
                    <p>{{ game.score || 'No reviews' }}</p>
                </div>
                <template v-if="game.lists">
                    <template v-if="game.lists.length > 1">
                        <TooltipProvider>
                            <Tooltip>
                                <TooltipTrigger as-child>
                                    <p class="game-floating-label" @click.prevent>{{ game.lists.length }} lists</p>
                                </TooltipTrigger>
                                <TooltipContent side="bottom">
                                    <ul class="text-foreground list-inside list-disc">
                                        <li class="py-0.5" v-for="list in game.lists" :key="list">
                                            {{ formatListName(list) }}
                                        </li>
                                    </ul>
                                </TooltipContent>
                            </Tooltip>
                        </TooltipProvider>
                    </template>
                    <template v-else>
                        <div class="game-floating-label" @click.prevent>
                            <component :is="listTypeIcon[game.lists[0]]" class="size-4" />
                            <p>{{ formatListName(game.lists[0]) }}</p>
                        </div>
                    </template>
                </template>
            </div>
        </div>

        <p class="max-w-full self-start truncate">{{ game.title }}</p>
    </Link>
</template>
