<script setup lang="ts">
import ClientOnly from '@/components/app/ClientOnly.vue';
import Discussion from '@/components/app/Discussion.vue';
import GameDetails from '@/components/app/game/GameDetails.vue';
import GameTabContent from '@/components/app/game/GameTabContent.vue';
import MainContainer from '@/components/app/MainContainer.vue';
import Review from '@/components/app/Review.vue';
import DiscussionSkeleton from '@/components/fallbacks/DiscussionSkeleton.vue';
import Heading from '@/components/Heading.vue';
import { Separator } from '@/components/ui/separator';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { useZiggy } from '@/composables/useZiggy';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, Discussion as DiscussionType, Game, Pagination, Review as ReviewType, Ziggy } from '@/types';
import { Head, WhenVisible } from '@inertiajs/vue3';
import { useMediaQuery } from '@vueuse/core';
import { LucideIcon, Rss, Star } from 'lucide-vue-next';
import { computed } from 'vue';

const props = withDefaults(
    defineProps<{
        game: Game;
        userLists?: { [key: string]: boolean };
        reviews?: Pagination & { data: ReviewType[] };
        discussions?: Pagination & { data: DiscussionType[] };
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

const tab = computed(() => {
    const query = (useZiggy().value as Ziggy & { query: { discussions_page?: number } }).query;

    return query?.discussions_page ? 'discussions' : 'reviews';
});

const shouldTeleport = useMediaQuery('(min-width: 1024px)');

const teleportId = 'tab_switch_teleport';
</script>

<template>
    <Head :title="game.title" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <MainContainer class="mx-auto flex max-w-5xl flex-col gap-4">
            <GameDetails :teleport-id :game :user-lists />

            <Tabs :default-value="tab" v-if="game.is_released">
                <ClientOnly>
                    <Teleport :to="`#${teleportId}`" :disabled="!shouldTeleport">
                        <TabsList class="h-auto w-full">
                            <TabsTrigger
                                v-for="[name, icon] in Object.entries(tabs)"
                                :value="name"
                                :key="name"
                                class="py-2 capitalize"
                                :class="{ 'cursor-pointer': tab !== name }"
                            >
                                <component :is="icon" class="mr-1"></component>
                                {{ name }}
                            </TabsTrigger>
                        </TabsList>
                    </Teleport>
                </ClientOnly>
                <TabsContent value="reviews">
                    <GameTabContent :action-href="route('reviews.create', { game: game.slug })" data="review" :pagination="reviews">
                        <Review v-for="review in reviews!.data" :key="review.slug" :review />
                    </GameTabContent>
                </TabsContent>

                <TabsContent value="discussions">
                    <GameTabContent
                        :action-href="route('discussions.create', { type: 'game', slug: game.slug })"
                        data="discussion"
                        :pagination="discussions"
                    >
                        <Discussion v-for="discussion in discussions!.data" :key="discussion.slug" :discussion />
                    </GameTabContent>
                </TabsContent>
            </Tabs>
            <template v-else>
                <Separator />
                <Heading
                    title="Game unreleased"
                    description="No reviews can be posted nor discussions created until release date listed above."
                    class="mb-0!"
                />
                <WhenVisible data="reviews">
                    <template #fallback>
                        <DiscussionSkeleton />
                    </template>

                    <Discussion v-for="discussion in discussions!.data" :key="discussion.slug" :discussion />
                </WhenVisible>
            </template>
        </MainContainer>
    </AppLayout>
</template>
