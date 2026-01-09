<script setup lang="ts">
import ClientOnly from '@/components/app/ClientOnly.vue';
import Discussion from '@/components/app/Discussion.vue';
import GameDetails from '@/components/app/game/GameDetails.vue';
import GameTabContent from '@/components/app/game/GameTabContent.vue';
import MainContainer from '@/components/app/MainContainer.vue';
import Review from '@/components/app/Review.vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, Game } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { useMediaQuery } from '@vueuse/core';
import { LucideIcon, Rss, Star } from 'lucide-vue-next';

const props = withDefaults(
    defineProps<{
        game: Game;
        userLists?: { [key: string]: boolean };
        tab: 'reviews' | 'discussions';
    }>(),
    {
        userLists: () => ({}),
    },
);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Games',
        href: route('games.index'),
    },
    {
        title: props.game.shortTitle,
        href: route('games.show', { game: props.game.slug }),
    },
];

const tabs: { [key: string]: LucideIcon } = {
    reviews: Star,
    discussions: Rss,
};

const shouldTeleport = useMediaQuery('(min-width: 1024px)');

const teleportId = 'tab_switch_teleport';
</script>

<template>
    <Head :title="game.title" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <MainContainer class="mx-auto flex max-w-5xl flex-col gap-4">
            <GameDetails :teleport-id :game :user-lists />

            <template v-if="game.is_released">
                <ClientOnly>
                    <Teleport :to="`#${teleportId}`" :disabled="!shouldTeleport">
                        <div class="bg-muted text-muted-foreground grid h-auto w-full grid-cols-2 items-center justify-center rounded-lg p-[3px]">
                            <Button
                                v-for="[name, icon] in Object.entries(tabs)"
                                :variant="tab === name ? 'default' : 'ghost'"
                                :value="name"
                                :key="name"
                                class="py-2 capitalize"
                                :class="{
                                    'bg-primary/70 pointer-events-none': tab === name,
                                }"
                                as-child
                            >
                                <Link :href="route('games.show', { game: game.slug, tab: name })" preserve-scroll :only="['game', 'tab']" as="button">
                                    <component :is="icon" class="mr-1"></component>
                                    {{ name }}
                                </Link>
                            </Button>
                        </div>
                    </Teleport>
                </ClientOnly>
                <GameTabContent
                    v-if="'reviews' in game"
                    :action-href="route('reviews.create', { game: game.slug })"
                    data="review"
                    :pagination="game.reviews"
                >
                    <Review v-for="review in game.reviews.data" :key="review.slug" :review />
                </GameTabContent>
                <GameTabContent
                    :action-href="route('discussions.create', { type: 'game', slug: game.slug })"
                    data="discussion"
                    :pagination="game.discussions"
                    v-if="'discussions' in game"
                >
                    <Discussion v-for="discussion in game.discussions.data" :key="discussion.slug" :discussion />
                </GameTabContent>
            </template>
            <template v-else>
                <Separator />
                <Heading
                    title="Game unreleased"
                    description="No reviews can be posted nor discussions created until release date listed above."
                    class="mb-0!"
                />
                <Discussion v-for="discussion in game.discussions.data" :key="discussion.slug" :discussion />
            </template>
        </MainContainer>
    </AppLayout>
</template>
