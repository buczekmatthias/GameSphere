<script setup lang="ts">
import CanInteract from '@/components/app/CanInteract.vue';
import GameActions from '@/components/app/game/GameActions.vue';
import GameInfo from '@/components/app/game/GameInfo.vue';
import LazyAvatar from '@/components/app/LazyAvatar.vue';
import { Skeleton } from '@/components/ui/skeleton';
import { userCanInteract } from '@/composables/useCanInteract';
import { Game } from '@/types';
import { Deferred } from '@inertiajs/vue3';

defineProps<{
    game: Game;
    userLists: { [key: string]: boolean };
    teleportId: string;
}>();
</script>

<template>
    <div
        class="grid gap-4 lg:grid-rows-[auto_auto]"
        :class="userCanInteract() ? 'ml:grid-cols-[auto_1fr_auto] grid-cols-[1fr_auto]' : 'grid-cols-1 md:grid-cols-[auto_1fr]'"
    >
        <LazyAvatar :src="game.thumbnail" :alt="game.title" class="h-96 w-80 lg:row-span-2" />
        <CanInteract>
            <Deferred data="userLists">
                <template #fallback>
                    <div class="flex flex-col gap-3">
                        <Skeleton class="h-9 w-12" />
                        <Skeleton class="h-9 w-12" />
                    </div>
                </template>

                <GameActions :game :lists="userLists" />
            </Deferred>
        </CanInteract>

        <GameInfo :game />
        <div :id="teleportId" class="self-end lg:col-start-2 lg:col-end-4 lg:row-end-2" v-if="game.is_released"></div>
    </div>
</template>
