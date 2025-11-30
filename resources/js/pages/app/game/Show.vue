<script setup lang="ts">
import CanInteract from '@/components/CanInteract.vue';
import ClientOnly from '@/components/ClientOnly.vue';
import ContentWithFallback from '@/components/ContentWithFallback.vue';
import Discussion from '@/components/Discussion.vue';
import DiscussionSkeleton from '@/components/fallbacks/DiscussionSkeleton.vue';
import ReviewSkeleton from '@/components/fallbacks/ReviewSkeleton.vue';
import GameActions from '@/components/GameActions.vue';
import NewDiscussionForm from '@/components/NewDiscussionForm.vue';
import NewReviewForm from '@/components/NewReviewForm.vue';
import PaginatedContent from '@/components/PaginatedContent.vue';
import GameDetails from '@/components/Partials/Game/Show/GameDetails.vue';
import Review from '@/components/Review.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Skeleton } from '@/components/ui/skeleton';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { canInteract } from '@/composables/useCanInteract';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, Discussion as DiscussionType, Game, Pagination, Permissions, Review as ReviewType, Ziggy } from '@/types';
import { Deferred, Head, usePage, WhenVisible } from '@inertiajs/vue3';
import { useMediaQuery } from '@vueuse/core';
import { LucideIcon, Rss, Star } from 'lucide-vue-next';
import { computed } from 'vue';

const props = withDefaults(
    defineProps<{
        game: Game;
        userLists?: { [key: string]: boolean };
        reviews?: Pagination & { data: ReviewType[] };
        discussions?: Pagination & { data: DiscussionType[] };
        permissions: Permissions;
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

    <!-- TODO: Break into components -->
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="main-container flex flex-col gap-4">
            <div
                class="grid gap-4 lg:grid-rows-[auto_auto]"
                :class="canInteract() ? 'ml:grid-cols-[auto_1fr_auto] grid-cols-[1fr_auto]' : 'grid-cols-1 md:grid-cols-[auto_1fr]'"
            >
                <Avatar class="h-96 w-80 overflow-hidden rounded-lg object-cover lg:row-span-2">
                    <AvatarImage :src="game.thumbnail" :alt="game.title" />
                    <AvatarFallback class="rounded-lg text-black dark:text-white" />
                </Avatar>
                <CanInteract>
                    <Deferred data="userLists">
                        <template #fallback>
                            <div class="flex flex-col gap-3">
                                <Skeleton class="h-9 w-12" />
                                <Skeleton class="h-9 w-12" />
                            </div>
                        </template>

                        <GameActions :game :lists="userLists" :permissions />
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
                            <NewReviewForm :slug="game.slug" />
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
                            <NewDiscussionForm :slug="game.slug" type="game" />
                        </div>

                        <ContentWithFallback :has-value="discussions!.data.length > 0">
                            <PaginatedContent page-name="discussions_page" :pagination="discussions!">
                                <Discussion v-for="discussion in discussions!.data" :key="discussion.slug" :discussion />
                            </PaginatedContent>
                        </ContentWithFallback>
                    </WhenVisible>
                </TabsContent>
            </Tabs>
        </div>
    </AppLayout>
</template>
