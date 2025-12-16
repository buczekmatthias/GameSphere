<script setup lang="ts">
import CanInteract from '@/components/CanInteract.vue';
import ClientOnly from '@/components/ClientOnly.vue';
import ContentWithFallback from '@/components/ContentWithFallback.vue';
import Discussion from '@/components/Discussion.vue';
import DiscussionSkeleton from '@/components/fallbacks/DiscussionSkeleton.vue';
import ReviewSkeleton from '@/components/fallbacks/ReviewSkeleton.vue';
import FormActionTap from '@/components/FormActionTap.vue';
import GameActions from '@/components/GameActions.vue';
import LazyAvatar from '@/components/LazyAvatar.vue';
import MainContainer from '@/components/MainContainer.vue';
import PaginatedContent from '@/components/PaginatedContent.vue';
import GameDetails from '@/components/Partials/Game/Show/GameDetails.vue';
import Review from '@/components/Review.vue';
import { Skeleton } from '@/components/ui/skeleton';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { userCanInteract } from '@/composables/useCanInteract';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, Discussion as DiscussionType, Game, Pagination, Review as ReviewType, Ziggy } from '@/types';
import { Deferred, Head, Link, usePage, WhenVisible } from '@inertiajs/vue3';
import { useMediaQuery } from '@vueuse/core';
import { LucideIcon, Plus, Rss, Star } from 'lucide-vue-next';
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
    const query = (usePage().props.ziggy as Ziggy & { query: { discussions_page?: number } }).query;

    return query?.discussions_page ? 'discussions' : 'reviews';
});

const shouldTeleport = useMediaQuery('(min-width: 1024px)');
</script>

<template>
    <Head :title="game.title" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <MainContainer class="mx-auto flex max-w-5xl flex-col gap-4">
            <div
                class="grid gap-4 lg:grid-rows-[auto_auto]"
                :class="userCanInteract() ? 'ml:grid-cols-[auto_1fr_auto] grid-cols-[1fr_auto]' : 'grid-cols-1 md:grid-cols-[auto_1fr]'"
            >
                <LazyAvatar :src="game.thumbnail" :alt="game.title" class="h-96 w-80" />
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

                <GameDetails :game />
                <div id="tab_switch_teleport" class="self-end lg:col-start-2 lg:col-end-4 lg:row-end-2"></div>
            </div>
            <Tabs :default-value="tab">
                <ClientOnly>
                    <Teleport to="#tab_switch_teleport" :disabled="!shouldTeleport">
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
                    <WhenVisible data="reviews">
                        <template #fallback>
                            <ReviewSkeleton />
                        </template>

                        <div class="mb-4 flex w-full items-center justify-between gap-4 border-y py-3">
                            <p class="text-xl">Reviews</p>
                            <CanInteract>
                                <Link :href="route('reviews.create', { game: game.slug })">
                                    <FormActionTap> <Plus class="size-4" /> Create review </FormActionTap>
                                </Link>
                            </CanInteract>
                        </div>

                        <ContentWithFallback :has-value="reviews!.data.length > 0">
                            <PaginatedContent page-name="reviews_page" :pagination="reviews!">
                                <Review v-for="review in reviews!.data" :key="review.slug" :review />
                            </PaginatedContent>
                        </ContentWithFallback>
                    </WhenVisible>
                </TabsContent>

                <TabsContent value="discussions">
                    <WhenVisible data="reviews">
                        <template #fallback>
                            <DiscussionSkeleton />
                        </template>

                        <div class="mb-4 flex w-full items-center justify-between gap-4 border-y py-3">
                            <p class="text-xl">Discussions</p>
                            <CanInteract>
                                <Link :href="route('discussions.create', { type: 'game', slug: game.slug })">
                                    <FormActionTap> <Plus class="size-4" /> Create discussion </FormActionTap>
                                </Link>
                            </CanInteract>
                        </div>

                        <ContentWithFallback :has-value="discussions!.data.length > 0">
                            <PaginatedContent page-name="discussions_page" :pagination="discussions!">
                                <Discussion v-for="discussion in discussions!.data" :key="discussion.slug" :discussion />
                            </PaginatedContent>
                        </ContentWithFallback>
                    </WhenVisible>
                </TabsContent>
            </Tabs>
        </MainContainer>
    </AppLayout>
</template>
